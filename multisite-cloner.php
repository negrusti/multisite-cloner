<?php
/**
 * Plugin Name: Multisite Cloner
 * Description: WP-CLI only plugin for cloning sites within multisite WordPress
 * Version: 1.0
 * Author: Gregory Morozov
 * Author URI: https://github.com/negrusti
 */

// Check if WP-CLI is active
if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
    return;
}

class WP_CLI_Clone_Command {

    /**
    * Clone one site to another.
    *
    * ## OPTIONS
    *
    * <source_ID>
    * : Source site ID
    *
    * <target_ID>
    * : Target site ID
    */
    
    public function __invoke( $args, $assoc_args ) {
        
        if ( ! is_multisite() ) {
            WP_CLI::error( 'This is not a multisite installation.' );
        }
        
        if ( count( $args ) !== 2 || ! ctype_digit( $args[0] ) || ! ctype_digit( $args[1] ) ) {
            WP_CLI::error( 'Please provide two integer arguments.' );
        }        

        global $wpdb;
        
        $source_prefix = $wpdb->prefix . $args[0] . "_";
        $target_prefix = $wpdb->prefix . $args[1] . "_";

        $source_site_details = get_blog_details($args[0]);
        $target_site_details = get_blog_details($args[1]);

        if(!$source_site_details || !$target_site_details) {
            WP_CLI::error("Site does not exist");
        }

        WP_CLI::log("Cloning tables: " . $source_site_details->siteurl . " => " . $target_site_details->siteurl);
        
        $sql = $wpdb->prepare("SHOW TABLES LIKE %s", $source_prefix . "%");
        $source_tables = $wpdb->get_results($sql, ARRAY_N);

        if (!empty($source_tables)) {
            foreach($source_tables as $source_table) {
                $destination_table = str_replace($source_prefix, $target_prefix, $source_table[0]);
                WP_CLI::log("Source table: " . $source_table[0] . " => Destination table: " . $destination_table);

                $wpdb->query( "DROP TABLE IF EXISTS $destination_table" );
                $wpdb->query( "CREATE TABLE $destination_table LIKE $source_table[0]" );
                $wpdb->query( "INSERT INTO $destination_table SELECT * FROM $source_table[0]" );

            }
        } else {
            WP_CLI::error("No tables found");
        }

        // Fix user roles option name
        $wpdb->query("UPDATE " . $target_prefix . "options SET option_name = '" . $target_prefix . "user_roles' WHERE option_name = '" . $source_prefix . "user_roles'");
        
        WP_CLI::log("Replacing URLs in the target site tables: " . $source_site_details->siteurl . " => " . $target_site_details->siteurl);
        WP_CLI::runcommand("search-replace $source_site_details->siteurl $target_site_details->siteurl $target_prefix* --network");
        
        $upload_data = wp_get_upload_dir();
        WP_CLI::log("Copying site files");
        self::recurseCopy($upload_data['basedir'] . "/sites/" . $args[0], $upload_data['basedir'] . "/sites/" . $args[1]);
        
        WP_CLI::runcommand("cache flush");        
        WP_CLI::success("Clone completed!");
    }
    
    function recurseCopy($src, $dst) {
        $dir = opendir($src);
        @mkdir($dst);

        while (($file = readdir($dir)) !== false) {
            if ($file != '.' && $file != '..') {
                if (is_dir($src . '/' . $file)) {
                    self::recurseCopy($src . '/' . $file, $dst . '/' . $file);
                } else {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }

        closedir($dir);
    }
}

WP_CLI::add_command( 'site clone', 'WP_CLI_Clone_Command' );
