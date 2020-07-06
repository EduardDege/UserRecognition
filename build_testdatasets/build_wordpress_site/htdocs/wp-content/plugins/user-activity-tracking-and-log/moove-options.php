<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Moove_Activity_Options File Doc Comment
 *
 * @category Moove_Activity_Options
 * @package   moove-activity-tracking
 * @author    Gaspar Nemes
 */

/**
 * Moove_Activity_Options Class Doc Comment
 *
 * @category Class
 * @package  Moove_Activity_Options
 * @author   Gaspar Nemes
 */
class Moove_Activity_Options {
	/**
	 * Global options
	 *
	 * @var array
	 */
	private $options;
	/**
	 * Construct
	 */
	function __construct() {
    add_action( 'uat-log-settings-capability', array( &$this, 'uat_custom_log_settings_capability' ), 10, 1 );
    add_action( 'uat-activity-log-capability', array( &$this, 'uat_custom_activity_log_capability' ), 10, 1 );
		add_action( 'admin_menu', array( $this, 'moove_activity_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'moove_activity_page_init' ) );
		add_action( 'update_option_moove_post_act', array( $this, 'moove_activity_check_settings' ), 10, 2 );
		add_action( 'plugins_loaded', array( $this, 'load_languages' ) );
	}
  /**
  * Custom capability type for Activity log settings (Settings -> Activity log)
  */
  function uat_custom_log_settings_capability( $capability ) {
    return $capability;
  }

  /**
  * Custom capability type for Activity log table (CMS -> Activity log)
  */
  function uat_custom_activity_log_capability( $capability ) {
    return $capability;
  }
  

	function load_languages() {
		load_plugin_textdomain( 'user-activity-tracking-and-log', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}


	/**
	 * Callback function after settings page saved. If there is any changes,
	 * it change the selected post type posts by the settings page value.
	 *
	 * @param  mixt $old_value Old value.
	 * @param  mixt $new_value New value.
	 * @return  void
	 */
	function moove_activity_check_settings( $old_value, $new_value ) {
		$activity_settings 	= get_option( 'moove_post_act' );
		$post_types        	= get_post_types( array( 'public' => true ) );
		$uat_content 		= new Moove_Activity_Content();
		unset( $post_types['attachment'] );
		foreach ( $post_types as $post_type => $value ) {
			if ( $activity_settings[ $post_type ] === '1' || $activity_settings[ $post_type ] === 1 ) :
				$args = array(
					'post_type'      => $post_type,
					'posts_per_page' => -1,
					'post_status'    => 'publish',
				);
				query_posts( $args );
				if ( have_posts() ) :
					while ( have_posts() ) :
						the_post();
						global $post;						
						$uat_content->moove_save_post( $post->ID, 'enable' );
					endwhile;

				endif;
				wp_reset_postdata();
				wp_reset_query();
			else :
				$args = array(
					'post_type'      => $post_type,
					'posts_per_page' => -1,
					'post_status'    => 'publish',
				);
				query_posts( $args );
				if ( have_posts() ) :
					while ( have_posts() ) :
						the_post();
						global $post;
						delete_post_meta( $post->ID, 'ma_data' );
					endwhile;
				endif;
				wp_reset_postdata();
				wp_reset_query();
			endif;
		}
	}

	/**
	 * Moove activity log page added to settings
	 *
	 * @return  void
	 */
	function moove_activity_admin_menu() {
    $capability = apply_filters( 'uat-log-settings-capability', 'manage_options' );
		add_options_page(
			'Activity tracking',
			__( 'Activity log', 'user-activity-tracking-and-log' ),
			$capability,
			'moove-activity',
			array( &$this, 'moove_activity_settings_page' )
		);
	}
	/**
	 * Settings page registration
	 *
	 * @return void
	 */
	function moove_activity_settings_page() {
		$uat_view      = new Moove_Activity_View();
		$this->options = get_option( 'moove_post_act' );
		echo $uat_view->load( 'moove.admin.settings.settings_page', array() );
	}

	/**
	 * Return the posts count per post_type.
	 *
	 * @param  string $post_type Post type name.
	 */
	function moove_get_logs_count( $post_type ) {
		$posts = array();
		return count( $posts );
	}
	/**
	 * Register settings page
	 *
	 * @return void
	 */
	function moove_activity_page_init() {
		$uat_options = new Moove_Activity_Options();
		register_setting(
			'moove_post_activity', // Option group.
			'moove_post_act' // Option name.
		);
		add_settings_section(
			'post_type_act', // ID.
			__( 'Post-type activity tracking', 'user-activity-tracking-and-log' ), // Title.
			array( &$this, 'moove_activity_print_section_info' ), // Callback.
			'moove-activity' // Page.
		);
		$post_types = get_post_types( array( 'public' => true ) );
		unset( $post_types['attachment'] );
		foreach ( $post_types as &$post_type ) :
			add_settings_field(
				$post_type,
				ucfirst( str_replace( '_', ' ', preg_replace( '/_cpt$/', '', $post_type ) ) ),
				array( &$this, 'moove_activity_setting_callback' ),
				'moove-activity',
				'post_type_act',
				array(
					'post_type'  => $post_type,
					'post_count' => $uat_options->moove_get_logs_count( $post_type ),
				)
			);
		endforeach;
	}
	/**
	 * Print settings page secion info
	 *
	 * @return string Message
	 */
	function moove_activity_print_section_info() {
		return _e( 'This page provides a facility to set activity log options globally for post types.', 'user-activity-tracking-and-log' );
	}
	/**
	 * Settings callback function
	 *
	 * @param  array $args Data array to view.
	 * @return void
	 */
	function moove_activity_setting_callback( $args ) {
		$uat_view = new Moove_Activity_View();
		echo $uat_view->load(
			'moove.admin.settings.post_type',
			array(
				'post_type'  => esc_attr( $args['post_type'] ),
				'post_count' => intval( $args['post_count'] ),
				'options'    => $this->options,
			)
		);
	}
}
$moove_activity_options = new Moove_Activity_Options();
