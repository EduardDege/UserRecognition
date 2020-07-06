<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 *  Contributors: MooveAgency
 *  Plugin Name: User Activity Tracking and Log
 *  Plugin URI: http://www.mooveagency.com
 *  Description: This plugin gives you the ability to track user activity on your website.
 *  Version: 2.0.7
 *  Author: Moove Agency
 *  Author URI: http://www.mooveagency.com
 *  License: GPLv2
 *  Text Domain: user-activity-tracking-and-log
 */

define( 'MOOVE_UAT_VERSION', '2.0.7' );

if ( ! defined( 'MOOVE_SHOP_URL' ) ) :
  define( 'MOOVE_SHOP_URL', 'https://shop.mooveagency.com' );
endif;

register_activation_hook( __FILE__, 'moove_activity_activate' );
register_deactivation_hook( __FILE__, 'moove_activity_deactivate' );

/**
 * Set options page for the plugin
 */
function moove_set_options_values() {
	$settings   = get_option( 'moove_post_act' );
	$post_types = get_post_types( array( 'public' => true ) );
	unset( $post_types['attachment'] );
	if ( ! $settings ) :
		foreach ( $post_types as $post_type ) :
			if ( 1 !== $settings[ $post_type ] || ! isset( $settings[ $post_type ] ) ) :
				$settings[ $post_type ] = 0;
				update_option( 'moove_post_act', $settings );
			endif;
			if ( 1 !== $settings[ $post_type . '_transient' ] || ! isset( $settings[ $post_type . '_transient' ] ) ) :
				$settings[ $post_type . '_transient' ] = 7;
				update_option( 'moove_post_act', $settings );
			endif;
		endforeach;
	endif;
}

/**
 * Functions on plugin activation, create relevant pages and defaults for settings page.
 */
function moove_activity_activate() {
	moove_set_options_values();
}


/**
 * Function on plugin deactivation. It removes the pages created before.
 */
function moove_activity_deactivate() {
}

/**
 * Star rating on the plugin listing page
 */
if ( ! function_exists('moove_uat_add_plugin_meta_links') ) {
function moove_uat_add_plugin_meta_links($meta_fields, $file) {
  if ( plugin_basename(__FILE__) == $file ) :
    $plugin_url = "https://wordpress.org/support/plugin/user-activity-tracking-and-log/reviews/?rate=5#new-post";
    $meta_fields[] = "<a href='" . esc_url($plugin_url) ."' target='_blank' title='" . esc_html__('Rate', 'user-activity-tracking-and-log') . "'>
          <i class='moove-uat-star-rating'>"
      . "<svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg>"
      . "<svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg>"
      . "<svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg>"
      . "<svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg>"
      . "<svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg>"
      . "</i></a>";      
     
  endif;
  return $meta_fields;
  }
}
add_filter('plugin_row_meta' , 'moove_uat_add_plugin_meta_links', 10, 2);


require_once dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'moove-view.php';
require_once dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'moove-content.php';
require_once dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'moove-options.php';
require_once dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'moove-controller.php';
require_once dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'moove-actions.php';
require_once dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'moove-shortcodes.php';
require_once dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'moove-functions.php';

