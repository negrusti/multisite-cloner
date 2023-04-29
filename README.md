# multisite-cloner

This plugin allows cloning of the sites in a Multisite WordPress installation via WP-CLI command.
*Disclaimer - use at your own risk! Backup your database first and make sure you are selecting the correct target site ID - the target site contents will be completely overwritten without any requests for confirmation.*

## Prerequisites

WP-CLI installed, SSH access

## Usage

1. Install and activate the plugin. There is no GUI
1. Create a fresh site via usual WordPress means
2. With WP-CLI list the sites of the multisite: `wp site list`, note the IDs of the source site and the new site that you have created
3. Run the clone command: `wp clone <source site ID> <target site ID>`

## Support

Paid support is available: wordpress@fastserver.io
