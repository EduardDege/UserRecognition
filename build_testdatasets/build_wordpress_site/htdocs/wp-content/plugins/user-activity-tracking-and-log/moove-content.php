<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Moove_Activity_Content Class Doc Comment
 *
 * @category Class
 * @package  Moove_Controller
 * @author   Gaspar Nemes
 */
class Moove_Activity_Content {
	/**
	 * Construct
	 */
	public function __construct() {

	}

	/**
	 * Checks the log status when the post being saved.
	 *
	 * @param int    $post_id  The post's id if the function is called from another controller.
	 * @param string $action Can be enabled or delete.
	 */
	public static function moove_save_post( $post_id, $action = false ) {

		if ( isset( $post_id ) ) :
			$pid = $post_id;
		else :
			$pid = intval( $_POST['post_ID'] );
		endif;

		if ( ! $pid ) {
			$pid = '';
		}

		// We are deleting campaign.
		if ( isset( $_POST['ma-delete-campaign'] ) ) :
			$campaign_id_sanitized = sanitize_key( wp_unslash( $_POST['ma-delete-campaign'] ) );
		endif;

		if ( ( isset( $campaign_id_sanitized ) && intval( $campaign_id_sanitized ) === 1 ) ) :
			delete_post_meta( $pid, 'ma_data' );
		$uat_db_controller = new Moove_Activity_Database_Model();
		$end_date          = date( 'Y-m-d H:i:s' );
		$uat_db_controller->remove_old_logs( $pid, $end_date );
			return; // Break the function.
		endif;
		$trigger_campaign = false;
		// We don't need to create any campaign.
		if ( isset( $_POST['ma-trigger-campaign'] ) ) :
			$trigger_campaign = sanitize_key( wp_unslash( $_POST['ma-trigger-campaign'] ) );
			if ( ! isset( $trigger_campaign ) ) :
				if ( $action !== 'enable' ) :
					return;
				endif;
			endif;
		endif;

		// Get data for this post.
		$_post_meta      	= get_post_meta( $pid, 'ma_data' );
		$_post_meta 		= isset( $_post_meta[0] ) ? $_post_meta : array( 0 => '' );
		if ( isset( $_post_meta[0] ) ) :
			$_ma_data_option = $_post_meta[0];
			$ma_data         = unserialize( $_ma_data_option );
			// If we have the campaign ID set already, don't do anything.
			if ( isset( $ma_data['campaign_id'] ) && $ma_data['campaign_id'] !== '' ) :
				return;
			endif;

			// We can go ahead and create campaign.
			$campaign_id            = current_time( 'timestamp' ) . $post_id;
			$ma_data['campaign_id'] = $campaign_id;

			$post_type = get_post_type( $post_id );
			$settings  = get_option( 'moove_post_act' );

			if ( isset(  $settings[ $post_type ] ) && intval( $settings[ $post_type ] ) !== 0 ) :
				update_post_meta( $pid, 'ma_data', serialize( $ma_data ) );
		endif;

		if ( intval( $trigger_campaign ) === 1 ) :
			update_post_meta( $pid, 'ma_data', serialize( $ma_data ) );
		endif;
	endif;
}

	/**
	 * Adding META-BOX for protection
	 */
	public static function moove_activity_meta_boxes() {
		$post_types = get_post_types( array( 'public' => true ) );
		$plugin_settings = apply_filters( 'moove_uat_filter_plugin_settings', get_option('moove_post_act') );
		unset( $post_types['attachment'] );
		foreach ( $post_types as $post_type ) :
			if ( isset( $plugin_settings[$post_type] ) && intval( $plugin_settings[$post_type] ) === 1 ) :
				add_meta_box(
					'ma-main-meta-box',
					__( 'Moove Activity', 'user-activity-tracking-and-log' ),
					array( 'Moove_Activity_Content', 'moove_main_meta_box_callback' ),
					$post_type,
					'normal',
					'default'
				);
		endif;
	endforeach;
}

	/**
	 * Meta box callback
	 */
	public static function moove_main_meta_box_callback() {
		$post_id  			= get_the_ID();
		$ma_data  			= array();
		$uat_view 			= new Moove_Activity_View();
		$uat_db_controller  = new Moove_Activity_Database_Model();
		$global_setup 		= array();
		if ( $post_id ) :
			if ( isset( $post_id ) ) :
				$_post_meta = get_post_meta( $post_id, 'ma_data' );
				if ( isset( $_post_meta[0] ) ) :
					$_ma_data_option = $_post_meta[0];
					$ma_data         = unserialize( $_ma_data_option );
				endif;
				if ( isset( $ma_data['campaign_id'] ) ) :
					$activity = $uat_db_controller->get_log( 'post_id', $post_id );
					if ( $activity && is_array( $activity ) ) :					
						foreach ( $activity as $log ) :
							$data = array(
								'post_id'         	=>  $log->post_id,
								'time'            	=>  $log->visit_date,
								'uid'             	=>  $log->user_id,
								'display_name'    	=>  $log->display_name,
								'show_ip'      			=>  $log->user_ip,
								'response_status' 	=>  $log->status,
								'referer'         	=>  $log->referer,
								'city'            	=>  $log->city
							);
							$data = apply_filters( 'uat_filter_data_entry', $data );
							$ma_data['log'][] = $data;

						endforeach;
					endif;
				endif;

				$post_type    = get_post_type( $post_id );
				$settings     = get_option( 'moove_post_act' );
				$global_setup = isset( $settings[ $post_type ] ) ? $settings[ $post_type ] : array();

			else :
				$ma_data = array();
			endif;
		else :
			$ma_data = array();
		endif;

		echo $uat_view->load(
			'moove.admin.activity_metabox',
			array(
				'activity'     => $ma_data,
				'global_setup' => $global_setup,
			)
		);

	}

	public static function get_license_token() {
		$license_token = function_exists('network_site_url') ? network_site_url('/') : home_url('/');
		return $license_token;
	}

	public static function moove_uat_get_key_name() {
		return 'moove_uat_plugin_key';
	}

	public static function uat_licence_action_button( $response, $uat_key ) {
		$type = isset( $response['type'] ) ? $response['type'] : false;
		if ( $type === 'expired' || $type === 'activated' || $type === 'max_activation_reached' ) : ?>
			<button type="submit" name="uat_activate_license" class="button button-primary button-inverse">
				<?php _e('Activate','user-activity-tracking-and-log'); ?>
			</button>
			<?php
		elseif ( $type === 'invalid' ) :
			?>
			<button type="submit" name="uat_activate_license" class="button button-primary button-inverse">
				<?php _e('Activate','user-activity-tracking-and-log'); ?>
			</button>
			<?php
		else :
			?>
			<button type="submit" name="uat_activate_license" class="button button-primary button-inverse">
				<?php _e('Activate','user-activity-tracking-and-log'); ?>
			</button>
			<br /><br />
			<hr />
			<h4 style="margin-bottom: 0;"><?php _e('Buy licence','user-activity-tracking-and-log'); ?></h4>
			<p>
				<?php 
				$store_link = __('You can buy licences from our [store_link]online store[/store_link].','user-activity-tracking-and-log');
				$store_link = str_replace('[store_link]', '<a href="https://www.mooveagency.com/wordpress-plugins/user-activity-tracking-and-log/" target="_blank" class="uat_admin_link">', $store_link );
				$store_link = str_replace('[/store_link]', '</a>.', $store_link );
				echo $store_link;
				?>
			</p>
			<p>
				<a href="https://www.mooveagency.com/wordpress-plugins/user-activity-tracking-and-log/" target="_blank" class="button button-primary">Buy Now</a>
			</p>
			<br />
			<hr />

			<?php
		endif;
	}

	public static function uat_licence_input_field( $response, $uat_key ) {
		$type = isset( $response['type'] ) ? $response['type'] : false;
		if ( $type === 'expired' ) :
			// LICENSE EXPIRED
			?>
			<tr>
				<th scope="row" style="padding: 0 0 10px 0;">
					<hr />
					<h4 style="margin-bottom: 0;"><?php _e('Renew your licence','user-activity-tracking-and-log'); ?></h4>
					<p><?php _e('Your licence has expired. You will not receive the latest updates and features unless you renew your licence.','user-activity-tracking-and-log'); ?></p>
					<a href="<?php echo MOOVE_SHOP_URL; ?>?renew=<?php echo $response['key']; ?>" class="button button-primary">Renew Licence</a>
					<br /><br />
					<hr />

					<h4 style="margin-bottom: 0;"><?php _e('Enter new licence key','user-activity-tracking-and-log'); ?></h4>
				</th>
			</tr>
			
			<tr>
				<td style="padding: 0;">
					<input name="moove_uat_license_key" required min="35" type="text" id="moove_uat_license_key" value="" class="regular-text">
				</td>
			</tr>
			<?php
		elseif ( $type === 'activated' || $type === 'max_activation_reached' ) :
			// LICENSE ACTIVATED
			?>
			<tr>
				<th scope="row" style="padding: 0 0 10px 0;">
					<hr />
					<h4 style="margin-bottom: 0;"><?php _e('Buy more licences','user-activity-tracking-and-log'); ?></h4>
					<p>
						<?php 
						$store_link = __('You can buy more licences from our [store_link]online store[/store_link].','user-activity-tracking-and-log');
						$store_link = str_replace('[store_link]', '<a href="https://www.mooveagency.com/wordpress-plugins/user-activity-tracking-and-log/" target="_blank" class="uat_admin_link">', $store_link );
						$store_link = str_replace('[/store_link]', '</a>.', $store_link );
						echo $store_link;
						?>
					</p>
					<p>
						<a href="https://www.mooveagency.com/wordpress-plugins/user-activity-tracking-and-log/" target="_blank" class="button button-primary">Buy Now</a>
					</p>
					<br />
					<hr />

					<h4 style="margin-bottom: 0;"><?php _e('Enter new licence key','user-activity-tracking-and-log'); ?></h4>
				</th>
			</tr>
			
			<tr>
				<td style="padding: 0;">
					<input name="moove_uat_license_key" required min="35" type="text" id="moove_uat_license_key" value="" class="regular-text">
				</td>
			</tr>
			<?php
		elseif ( $type === 'invalid' ) :
			?>
			<tr>
				<th scope="row" style="padding: 0 0 10px 0;">
					<hr />
					<h4 style="margin-bottom: 0;"><?php _e('Buy licence','user-activity-tracking-and-log'); ?></h4>
					<p>
						<?php 
						$store_link = __('You can buy licences from our [store_link]online store[/store_link].','user-activity-tracking-and-log');
						$store_link = str_replace('[store_link]', '<a href="https://www.mooveagency.com/wordpress-plugins/user-activity-tracking-and-log/" target="_blank" class="uat_admin_link">', $store_link );
						$store_link = str_replace('[/store_link]', '</a>.', $store_link );
						echo $store_link;
						?>
					</p>
					<p>
						<a href="https://www.mooveagency.com/wordpress-plugins/user-activity-tracking-and-log/" target="_blank" class="button button-primary">Buy Now</a>
					</p>
					<br />
					<hr />
				</th>
			</tr>
			<tr>
				<th scope="row" style="padding: 0 0 10px 0;">
					<label><?php _e('Enter your licence key:','user-activity-tracking-and-log'); ?></label>
				</th>
			</tr>
			
			<tr>
				<td style="padding: 0;">
					<input name="moove_uat_license_key" required min="35" type="text" id="moove_uat_license_key" value="" class="regular-text">
				</td>
			</tr>
			<?php
		else :
			?>
			
			<tr>
				<th scope="row" style="padding: 0 0 10px 0;">
					<label><?php _e('Enter licence key:','user-activity-tracking-and-log'); ?></label>
				</th>
			</tr>
			
			<tr>
				<td style="padding: 0;">
					<input name="moove_uat_license_key" required min="35" type="text" id="moove_uat_license_key" value="" class="regular-text">
				</td>
			</tr>


			<?php
		endif;
	}

	public static function uat_get_alertbox( $type, $response, $uat_key ) {
		if ( $type === 'error' ) :
			$messages = isset( $response['message'] ) && is_array( $response['message'] ) ? implode( '</p><p>', $response['message'] ) : '';
			if ( $response['type'] === 'inactive' ) :
				$uat_default_content  = new Moove_Activity_Content();
		    $option_key           = $uat_default_content->moove_uat_get_key_name();
		    $uat_key              = function_exists( 'get_site_option' ) ? get_site_option( $option_key ) : get_option( $option_key );
				if ( function_exists( 'update_site_option' ) ) :
					update_site_option(
						$option_key,
						array(
							'key'          => $response['key'],
							'deactivation' => strtotime( 'now' ),
						)
					);
				else :
					update_option(
						$option_key,
						array(
							'key'          => $response['key'],
							'deactivation' => strtotime( 'now' ),
						)
					);
				endif;
				$uat_key = function_exists( 'get_site_option' ) ? get_site_option( $option_key ) : get_option( $option_key );
			endif;
			?>
			<div class="uat-admin-alert uat-admin-alert-error">
				<div class="uat-alert-content">        
					<p>License key: <strong><?php echo isset( $response['key'] ) ? $response['key'] : ( isset( $uat_key['key'] ) ? $uat_key['key'] : $uat_key ) ; ?></strong></p>
					<p><?php echo $messages; ?></p>
				</div>
				<span class="dashicons dashicons-dismiss"></span>
			</div>
			<!--  .uat-admin-alert uat-admin-alert-success -->
			<?php
		else :
			$messages       = isset( $response['message'] ) && is_array( $response['message'] ) ? implode( '</p><p>', $response['message'] ) : '';
			?>
			<div class="uat-admin-alert uat-admin-alert-success">    
				<div class="uat-alert-content">         
					<p>License key: <strong><?php echo isset( $response['key'] ) ? $response['key'] : ( isset( $uat_key['key'] ) ? $uat_key['key'] : $uat_key ) ; ?></strong></p>
					<p><?php echo $messages; ?></p>
				</div>
				<span class="dashicons dashicons-yes-alt"></span>
			</div>
			<!--  .uat-admin-alert uat-admin-alert-success -->
			<?php
		endif;
		do_action('uat_plugin_updater_notice');
	}

	public static function uat_premium_update_alert() {

		$plugins 						= get_site_transient( 'update_plugins' );
		$lm                 = new Moove_UAT_License_Manager();
		$plugin_slug        = $lm->get_add_on_plugin_slug();

		if ( isset( $plugins->response[$plugin_slug] ) && is_plugin_active( $plugin_slug ) ) :
			$version = $plugins->response[$plugin_slug]->new_version;

			$current_user         = wp_get_current_user();
			$user_id              = isset( $current_user->ID ) ? $current_user->ID : 0;

			if ( isset( $plugins->response[$plugin_slug]->package ) && ! $plugins->response[$plugin_slug]->package ) :

				$uat_default_content  = new Moove_Activity_Content();
				$option_key           = $uat_default_content->moove_uat_get_key_name();
				$uat_key              = function_exists( 'get_site_option' ) ? get_site_option( $option_key ) : get_option( $option_key );
				$license_key          = isset( $uat_key['key'] ) ? sanitize_text_field( $uat_key['key'] ) : false;
				$renew_link           = MOOVE_SHOP_URL . '?renew='.$license_key;
				$license_manager      = admin_url('options-general.php') . '?page=moove-activity&amp;tab=licence';
				$purchase_link        = 'https://www.mooveagency.com/wordpress-plugins/user-activity-tracking-and-log/';
				$notice_text          = '';
				if ( $license_key && isset( $uat_key['activation'] ) ) :
						// Expired.
					$notice_text = 'Update is not available until you <a href="'.$renew_link.'" target="_blank">renew your licence</a>. You can also update your licence key in the <a href="'.$license_manager.'">Licence Manager</a>.';
				elseif ( $license_key && isset( $uat_key['deactivation'] ) ) :
						// Deactivated.
					$notice_text = 'Update is not available until you <a href="'.$purchase_link.'" target="_blank">purchase a licence</a>. You can also update your licence key in the <a href="'.$license_manager.'">Licence Manager</a>.';
				elseif ( ! $license_key ) :
						// No license key installed.
					$notice_text = 'Update is not available until you <a href="'.$purchase_link.'" target="_blank">purchase a licence</a>. You can also update your licence key in the <a href="'.$license_manager.'">Licence Manager</a>.';
				endif;  
				?>
				<div class="uat-cookie-alert uat-cookie-update-alert" style="display: inline-block;">
					<h4><?php _e('There is a new version of User Activity Tracking and Log - Premium Add-On.','user-activity-tracking-and-log'); ?></h4>
					<p><?php echo $notice_text; ?></p>
				</div>
				<!--  .uat-cookie-alert -->
				<?php      
				endif;

		endif;
	}
}
$moove_activity_content_provider = new Moove_Activity_Content();
