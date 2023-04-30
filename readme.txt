=== Multisite Cloner ===
Contributors: negrusti
Tags: multisite, clone
Requires at least: 4.7
Tested up to: 6.1.1
Stable tag: 1.0
Requires PHP: 7.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

This plugin allows cloning of the sites in a Multisite WordPress installation via WP-CLI command.

== Description ==

**Disclaimer: use at your own risk! Backup your database first and make sure you are selecting the correct target site ID - the target site contents will be completely overwritten without any requests for confirmation.**

## Prerequisites

WP-CLI installed, SSH access

## Usage

1. Install and activate the plugin. There is no GUI
1. Create a fresh site via usual WordPress means
2. With WP-CLI list the sites of the multisite: `wp site list`, note the IDs of the source site and the new site that you have created
3. Run the clone command: `wp site clone <source site ID> <target site ID>`

## Support

Paid support is available: wordpress@fastserver.io

== Changelog ==

= 1.0 =
* Initial release.

