<?php
/**
 * Plugin Name: An Official TraceMyIP Tracker
 * Plugin URI: https://www.tracemyip.org
 * Description: Website visitor IP activity tracking, IP analytics, IP changes tracker and visitor IP address blocking via IP address contacts manager. Tag visitors IPs, track, contol and manage individual pages, links and protect contact forms. GDPR compliant. For visitor tracker setup instructions, see <a href="admin.php?page=tmip_lnk_wp_settings"><b>plugin settings</b></a>.
 * Version: 2.30
 * Author: TraceMyIP.org
 * Author URI: https://www.TraceMyIP.org
 * Text Domain: tracemyip-visitor-analytics-ip-tracking-control
 * License: GPLv2 (or later)
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/


### SET CONSTANTS ############################################
header('X-XSS-Protection:0');
require_once('languages/en.php');
define("tmip_plugin_dir_name", 			'tracemyip-visitor-analytics-ip-tracking-control', false);
define("tmip_enable_meta_rating", 		2); // Show rate section. 1-post selected rating, 2-show transitional screen
define("tmip_codes_usage_rate_thresh",	5); // Number of tracker pageloads required to trigger rating panel
define("tmip_html_to_js_format_realti", 1); // 1- Enable real time HTML to JavaScript code conversion
define("tmip_html_to_js_format_onsubm", 1); // 1- Enable reformatting html code to JavaScript code on submit if new code or if realtime
											// html>JS have occured at least once
define("tmip_codes_usage_stats_data",	1); // 1- Enable codes loading status, 2- Enable code usage process counts

### ADD PRE REQ ############################################
require_once('includes/functions.php');
tmip_static_urls();
tmip_plugins_dirpath(__FILE__);
tmip_get_url_vars();

$script_filename=trim($_SERVER['SCRIPT_FILENAME']);
// register_activation_hook( __FILE__, 'tmip_func_on_activation' );

### ADD OPTIONS ############################################
add_option(tmip_visit_tracker_val, 		tmip_visit_tracker_default);
add_option(tmip_page_tracker_val, 		tmip_page_tracker_default);

### ADD PLUGIN ACTION LINKS ############################################
add_filter('plugin_action_links_'.plugin_basename (__FILE__), 'tmip_plugin_action_links');


### ADD PLUGIN ROW LINKS ############################################
if (tmip_enable_meta_rating) add_filter( 'plugin_row_meta', 'tmip_plugin_row_add_rating', 10, 2 );

// Determine how plugin is loaded
$WP_admin_pages=NULL;
if (stristr($script_filename,'admin.php')) {
	$WP_admin_pages='admin';
} elseif (stristr($script_filename,'plugins.php')) {
	$WP_admin_pages='plugins';
} elseif (stristr($script_filename,'options-general.php')) {
	$WP_admin_pages='options-general';
}
if ($WP_admin_pages) tmip_load_js();

### ADD USER ACCESS ############################################
add_action('admin_menu', 	'tmip_access_reports');
add_action('admin_menu' , 	'add_tmip_option_page');
add_action('admin_menu', 	'tmip_admin_menu');
add_action('wp_head', 		'tmip_addToTags');


### FUNCTIONS ############################################

// Reset settings
//tmip_reset_plugin_settings();

// Add Page Tracker to header
add_action('wp_head','tmip_insert_page_tracker');

// Add Visitor Tracker to header or footer
$tmip_visit_tracker_position=get_option(tmip_position_val);
if ($tmip_visit_tracker_position=="header") {
	add_action('wp_head','tmip_insert_visitor_tracker');
} else {
	add_action('wp_footer','tmip_insert_visitor_tracker');
}

?>