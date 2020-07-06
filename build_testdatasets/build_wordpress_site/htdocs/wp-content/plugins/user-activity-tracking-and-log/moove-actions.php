<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Moove_Activity_Actions File Doc Comment
 *
 * @category  Moove_Activity_Actions
 * @package   moove-activity-tracking
 * @author    Gaspar Nemes
 */

/**
 * Moove_Activity_Actions Class Doc Comment
 *
 * @category Class
 * @package  Moove_Activity_Actions
 * @author   Gaspar Nemes
 */
class Moove_Activity_Actions {
	/**
	 * Global cariable used in localization
	 *
	 * @var array
	 */
	var $activity_loc_data;
	/**
	 * Construct
	 */
	function __construct() {
		$this->moove_register_scripts();

		add_action( 'wp_ajax_moove_activity_track_pageview', array( 'Moove_Activity_Controller', 'moove_track_user_access_ajax' ) );
		add_action( 'wp_ajax_nopriv_moove_activity_track_pageview', array( 'Moove_Activity_Controller', 'moove_track_user_access_ajax' ) );
		add_action( 'moove-activity-tab-content', array( &$this, 'moove_activity_tab_content' ), 999, 1 );
		add_action( 'moove-activity-tab-extensions', array( &$this, 'moove_activity_tab_extensions' ), 5, 1 );
		add_action( 'moove-activity-filters', array( &$this, 'moove_activity_filters' ), 5, 2 );
		add_action( 'moove-activity-top-filters', array( &$this, 'moove_activity_top_filters' ) );
		add_action( 'moove_activity_check_extensions', array( &$this, 'moove_activity_check_extensions' ), 10, 2 );
		add_action( 'moove-activity_premium_section_ads', array( &$this, 'moove_activity_premium_section_ads' ) );
		add_action( 'moove_uat_filter_plugin_settings', array( &$this, 'moove_uat_filter_plugin_settings' ), 10, 1 );
		// Custom meta box for protection.
		add_action( 'add_meta_boxes', array( 'Moove_Activity_Content', 'moove_activity_meta_boxes' ) );
		add_action( 'save_post', array( 'Moove_Activity_Content', 'moove_save_post' ) );
		add_action('moove_activity_check_tab_content', array( &$this, 'moove_activity_check_tab_content' ), 10, 2);

    add_action( 'uat_licence_action_button', array( 'Moove_Activity_Content', 'uat_licence_action_button' ), 10, 2 );
    add_action( 'uat_get_alertbox', array( 'Moove_Activity_Content', 'uat_get_alertbox' ), 10, 3 );
    add_action( 'uat_licence_input_field', array( 'Moove_Activity_Content', 'uat_licence_input_field' ), 10, 2 );
    add_action( 'uat_premium_update_alert', array( 'Moove_Activity_Content', 'uat_premium_update_alert' ) );


    $uat_default_content  = new Moove_Activity_Content();
    $option_key           = $uat_default_content->moove_uat_get_key_name();
    $uat_key              = function_exists( 'get_site_option' ) ? get_site_option( $option_key ) : get_option( $option_key );

    if ( $uat_key && ! isset( $uat_key['deactivation'] ) ) :
      do_action( 'uat_plugin_loaded' );
    endif;


    add_action( 'admin_menu', array( 'Moove_Activity_Controller', 'moove_register_activity_menu_page' ) );
		add_action( 'save_post', array( 'Moove_Activity_Controller', 'moove_track_user_access_save_post' ) );
		add_action( 'wp_ajax_moove_activity_save_user_options', array( 'Moove_Activity_Controller', 'moove_activity_save_user_options' ) );
	}

	function moove_uat_filter_plugin_settings( $global_settings ) {
		$show_disabled = apply_filters( 'uat_show_disabled_cpt', true );
		if ( $show_disabled ) :
			$post_types       = get_post_types( array( 'public' => true ) );
			unset( $post_types['attachment'] );
			foreach ( $post_types as $post_type ) :
				if ( isset( $global_settings[$post_type] ) ) :
					$global_settings[$post_type] = '1';
				endif;
			endforeach;
		endif;
		return $global_settings;
	}

	function moove_activity_top_filters() {
		echo '';
	}

	function moove_activity_premium_section_ads() {

		if ( class_exists( 'Moove_Activity_Addon_View' ) ) :
			$add_on_view  = new Moove_Activity_Addon_View();
			$slug         = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : false;
			$view_content = $slug ? $add_on_view->load( 'moove.admin.settings.' . $slug, array() ) : false;

			if ( ! $view_content && $slug && $slug !== 'help' ) :
				?>
				<div class="uat-locked-section">
					<span>
					<i class="dashicons dashicons-lock"></i>
					<h4>This feature is not supported in this version of the Premium Add-on.</h4>
					
					<p><strong><a href="<?php echo admin_url( 'options-general.php?page=moove-activity&tab=licence' ); ?>" class="uat_admin_link">Activate your licence</a> to download the latest version of the Premium Add-on.</strong></p>
				
					<p class="uat_license_info">Don’t have a valid licence key yet? <br><a href="<?php echo MOOVE_SHOP_URL; ?>/my-account" target="_blank" class="uat_admin_link">Login to your account</a> to generate the key or <a href="https://www.mooveagency.com/wordpress-plugins/user-activity-tracking-and-log" class="uat_admin_link" target="_blank">buy a new licence here</a>.</p>
					<br />

					<a href="https://www.mooveagency.com/wordpress-plugins/user-activity-tracking-and-log" target="_blank" class="plugin-buy-now-btn">Buy Now</a>
					</span>

				</div>
				<!--  .uat-locked-section -->
				<?php
			endif;
		else :
			?>
			<div class="muat-locked-section">
				<span>
					<i class="dashicons dashicons-lock"></i>
					<h4>This feature is part of the Premium Add-on</h4>
					<?php
					$uat_default_content = new Moove_Activity_Content();
					$option_key           = $uat_default_content->moove_uat_get_key_name();
					$uat_key             = function_exists( 'get_site_option' ) ? get_site_option( $option_key ) : get_option( $option_key );
					?>
					<?php if ( isset( $uat_key['deactivation'] ) || $uat_key['activation'] ) : ?>
					<p><strong><a href="<?php echo admin_url( 'options-general.php?page=moove-activity&tab=licence' ); ?>" class="uat_admin_link">Activate your licence</a> or <a href="https://www.mooveagency.com/wordpress-plugins/user-activity-tracking-and-log" class="uat_admin_link" target="_blank">buy a new licence here</a></strong>.</p>
					<?php else : ?>
					<p><strong>Do you have a licence key? <br />Insert your license key to the "<a href="<?php echo admin_url( 'admin.php' ); ?>?page=moove-uat&amp;tab=licence" class="uat_admin_link">Licence Manager</a>" and activate it.</strong></p>

					<?php endif; ?>
					<br />

					<a href="https://www.mooveagency.com/wordpress-plugins/user-activity-tracking-and-log" target="_blank" class="plugin-buy-now-btn">Buy Now</a>
				</span>

			</div>
			<!--  .uat-locked-section -->
			<?php
		endif;
	}

	function moove_activity_check_extensions( $content, $slug ) {
		$return = $content;
		if ( class_exists( 'Moove_Activity_Addon_View' ) ) :
			$add_on_view  = new Moove_Activity_Addon_View();
			$view_content = $add_on_view->load( 'moove.admin.settings.' . $slug, array() );
			if ( trim( $view_content ) ) :
				$return = '';
			endif;
		endif;
		return $return;
	}

	function moove_activity_check_tab_content( $content, $slug ) {
		$_return = $content;
		if ( class_exists( 'Moove_Activity_Addon_View' ) ) :
			$add_on_view  = new Moove_Activity_Addon_View();
			$view_content = $add_on_view->load( 'moove.admin.settings.' . $slug, array() );
			if ( $view_content ) :
				$_return = '';
			endif;
		endif;
		return $_return;
	}

	function moove_activity_tab_content( $data, $active_tab = '' ) {
		$uat_view = new Moove_Activity_View();
		if( $data['tab'] == 'post_type_activity' ) : ?>
			<form action="options.php" method="post" class="moove-activity-form">
				<?php
				settings_fields( 'moove_post_activity' );
				do_settings_sections( 'moove-activity' );
				submit_button();
				?>
			</form>        
			<?php 
		else :
			$content = $uat_view->load( 'moove.admin.settings.'.$data['tab'], true );
			echo apply_filters( 'moove_activity_check_tab_content', $content, $data['tab'] ); 
		endif;
	}

	function moove_activity_tab_extensions( $active_tab ) {
		$tab_data = array(
			array(
				'name' => __( 'Activity Screen Options', 'gdpr-cookie-compliance-addon' ),
				'slug' => 'activity_screen_settings',
			),
			array(
				'name' => __( 'Tracking Settings', 'gdpr-cookie-compliance-addon' ),
				'slug' => 'tracking_settings',
			),
			array(
				'name' => __( 'GDPR Settings', 'gdpr-cookie-compliance-addon' ),
				'slug' => 'gdpr_activity',
			),
		);
		foreach ( $tab_data as $tab ) :
			ob_start();
			?>
			<a href="<?php echo admin_url( '/options-general.php?page=moove-activity&tab='.$tab['slug'] ); ?>" class="nav-tab nav-cc-premium nav-tab-disabled <?php echo $active_tab == $tab['slug'] ? 'nav-tab-active' : ''; ?>">
				<?php echo $tab['name']; ?>
			</a>
			<?php
			$content = ob_get_clean();
			echo apply_filters( 'moove_activity_check_extensions', $content, $tab['slug'] );
		endforeach;
	}

	/**
	 * Register Front-end / Back-end scripts
	 *
	 * @return void
	 */
	function moove_register_scripts() {
		if ( is_admin() ) :
			add_action( 'admin_enqueue_scripts', array( &$this, 'moove_activity_admin_scripts' ) );
		else :
			add_action( 'wp_enqueue_scripts', array( &$this, 'moove_frontend_activity_scripts' ) );
		endif;
	}

	function moove_activity_filters( $filters, $content ) {
		echo $filters;
	}

	/**
	 * Register global variables to head, AJAX, Form validation messages
	 *
	 * @param  string $ascript The registered script handle you are attaching the data for.
	 * @return void
	 */
	public function moove_localize_script( $ascript ) {
		$activity_loc_data = array(
			'activityoptions'		=> 	get_option( 'moove_activity-options' ),
			'referer'				=> 	wp_get_referer(),
			'ajaxurl'				=>	admin_url( 'admin-ajax.php' ),
			'post_id'				=>	get_the_ID(),
			'is_page'				=>	is_page(),
			'is_single'				=>	is_single(),
			'current_user'			=>	get_current_user_id(),
			'referrer'				=>	esc_url( wp_get_referer() )
		);
		$this->activity_loc_data = apply_filters( 'moove_uat_extend_loc_data', $activity_loc_data );

		wp_localize_script( $ascript, 'moove_frontend_activity_scripts', $this->activity_loc_data );
	}

	/**
	 * Registe FRONT-END Javascripts and Styles
	 *
	 * @return void
	 */
	public function moove_frontend_activity_scripts() {
		wp_enqueue_script( 'moove_activity_frontend', plugins_url( basename( dirname( __FILE__ ) ) ) . '/assets/js/moove_activity_frontend.js', array( 'jquery' ), MOOVE_UAT_VERSION, true );
		// wp_enqueue_style( 'moove_activity_frontend', plugins_url( basename( dirname( __FILE__ ) ) ) . '/assets/css/moove_activity_frontend.css' );
		$this->moove_localize_script( 'moove_activity_frontend' );
	}
	/**
	 * Registe BACK-END Javascripts and Styles
	 *
	 * @return void
	 */
	public function moove_activity_admin_scripts() {
		wp_enqueue_script( 'moove_activity_backend', plugins_url( basename( dirname( __FILE__ ) ) ) . '/assets/js/moove_activity_backend.js', array( 'jquery' ), MOOVE_UAT_VERSION, true );
		wp_enqueue_style( 'moove_activity_backend', plugins_url( basename( dirname( __FILE__ ) ) ) . '/assets/css/moove_activity_backend.css', '', MOOVE_UAT_VERSION );
	}
}
$moove_activity_actions_provider = new Moove_Activity_Actions();

