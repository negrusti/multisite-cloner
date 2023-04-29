<?php
/**
 * Plugin Name: Multisite Cloner
 * Description: A minimal WordPress plugin that provides a single WP-CLI command.
 * Version: 1.0
 * Author: Gregory Morozov
 * Author URI: https://github.com/negrusti
 */

// Check if WP-CLI is active
if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
    return;
}

/**
 * My WP-CLI Command class
 */
class WP_CLI_Clone_Command {

    /**
     * A simple WP-CLI command
     *
     * @param array $args       Positional arguments.
     * @param array $assoc_args Associative arguments.
     */
    public function __invoke( $args, $assoc_args ) {
        
        if ( count( $args ) !== 2 || ! ctype_digit( $args[0] ) || ! ctype_digit( $args[1] ) ) {
            WP_CLI::error( 'Please provide exactly two integer arguments.' );
            return;
        }        

        $source_prefix = $wpdb->prefix . "_" . $source_site_id . "_";
        $target_prefix = $wpdb->prefix . "_" . $target_site_id . "_";

        $source_site_details = get_blog_details($source_site_id);
        $target_site_details = get_blog_details($target_site_id);

        if(!$source_site_details || !$target_site_details) {
            WP_CLI::error("Site does not exist");
        }

        $sql = $wpdb->prepare("SHOW TABLES LIKE %s", $source_prefix);
        $source_tables = $wpdb->get_results($sql, ARRAY_N);

        if (!empty($source_tables)) {
            foreach($source_tables as $source_table) {
                $destination_table = str_replace($source_prefix, $target_prefix, $source_table[0]);
                WP_CLI::log("Source table: " . $source_table[0] . " Destination table: " . $destination_table);


                //$wpdb->query( "DROP TABLE $destination_table" );
                //$wpdb->query( "CREATE TABLE $destination_table LIKE $source_table[0]" );
                //$wpdb->query( "INSERT $destination_table SELECT * FROM $source_table[0]" );

            }
        } else {
            WP_CLI::error("No tables found");
        }

        //WP_CLI::run_command(['search-replace', $source_site_details->siteurl, $target_site_details->siteurl, $target_prefix . "*"]);
        WP_CLI::success( 'Clone completed!' );
    }
}

WP_CLI::add_command( 'clone', 'WP_CLI_Clone_Command' );
