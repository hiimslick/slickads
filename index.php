<?php
/*
Plugin Name: Slick Ads
Plugin URI: http://nickmarcelo.xyz/slickads
Description: Manage your advertising with no efforts! It's so slick, you can have unlimited ads!
Version: 1.0.1
Author: Nick Marcelo
Author URI: http://nickmarcelo.xyz
*/
define('SLICKADS_PLUGIN_PATH', WP_PLUGIN_DIR . '/slickads/');
global $slickads, $page_handle, $plugin_url, $shortname, $table_name;
$page_handle = "slick-ads";
$plugin_url = get_bloginfo('url') . '/wp-content/plugins/SlickAds';
$shortname = "slickads";
$table_name = 'slickads_tbl';

// call our class file
require('classes/SlickAds.php');
$slickads = new SlickAds();

// run code to create table when plugin is activated
register_activation_hook(__FILE__, array( $slickads, 'slickAds_activate' ) );
register_deactivation_hook(__FILE__, array( $slickads, 'slickAds_deactivate') );
?>
