<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Moove_Activity_Shortcodes File Doc Comment
 *
 * @category Moove_Activity_Shortcodes
 * @package   moove-activity-tracking
 * @author    Gaspar Nemes
 */

/**
 * Moove_Activity_Shortcodes Class Doc Comment
 *
 * @category Class
 * @package  Moove_Activity_Shortcodes
 * @author   Gaspar Nemes
 */
class Moove_Activity_Shortcodes {
	/**
	 * Construct function
	 */
	public function __construct() {
		$this->moove_activity_register_shortcodes();
	}
	/**
	 * Register shortcodes
	 *
	 * @return void
	 */
	public function moove_activity_register_shortcodes() {
		add_shortcode( 'show_ip', array( &$this, 'moove_get_the_user_ip' ) );
	}

	/**
	 * User IP address
	 *
	 * @return string IP Address
	 */
	public function moove_get_the_user_ip() {
		if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) :
			// Check ip from share internet.
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) :
			// To check ip is pass from proxy.
			if ( is_array( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) :
				$ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
				$ip = isset( $ip[0] ) ? $ip[0] : $_SERVER['REMOTE_ADDR'];
			else :
				$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			endif;
		else :
			$ip = $_SERVER['REMOTE_ADDR'];
		endif;
		return apply_filters( 'moove_activity_tracking_ip_filter', $ip );
	}

	public function get_location_details( $ip = false ) {
		$response = false;
		if ( $ip ) :
			$transient_key 	= 'uat_locdata_' . md5( $ip ); 
			$details 				= get_transient( $transient_key );
			if ( ! $details ) :
				try {
					$details = @file_get_contents( "https://ipinfo.io/{$ip}/json" );
					if ( $details && json_decode( $details ) ) :
						set_transient( $transient_key, $details, 30 * DAY_IN_SECONDS );
					else :
						$details = false;
					endif;
				} catch (Exception $e) {
					$details = false;
				}
			else :
				$details = json_decode( $details );
			endif;
		endif;
		return $details;
	}
}
new Moove_Activity_Shortcodes();
