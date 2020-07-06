<h2><?php _e( 'Licence Manager', 'import-uat-feed' ); ?></h2>
<hr />
<?php 
$uat_default_content  = new Moove_Activity_Content();
$option_key           = $uat_default_content->moove_uat_get_key_name();
$uat_key              = function_exists( 'get_site_option' ) ? get_site_option( $option_key ) : get_option( $option_key );
?>
<form action="<?php echo admin_url( '/options-general.php?page=moove-activity&tab=licence' ) ?>" method="post" id="moove_uat_license_settings" data-key="<?php echo $uat_key && isset( $uat_key['key'] ) && isset( $uat_key['activation'] ) ? $uat_key['key'] : ''; ?>">
	<table class="form-table">
		<tbody>
			<tr>
				<td colspan="2" class="uat_license_log_alert" style="padding: 0;">
					<?php
					$is_valid_license = false;
					if ( isset( $_POST['moove_uat_license_key'] ) && isset( $_POST['uat_activate_license'] ) ) :
						$license_key  = sanitize_text_field( $_POST['moove_uat_license_key'] );
					if ( $license_key ) :
						$license_manager    = new Moove_UAT_License_Manager();
						$is_valid_license   = $license_manager->get_premium_add_on( $license_key, 'activate' );

						if ( $is_valid_license && isset( $is_valid_license['valid'] ) && $is_valid_license['valid'] === true ) : 
							if ( function_exists( 'update_site_option' ) ) :
								update_site_option( 
									$option_key, 
									array( 
										'key'         => $is_valid_license['key'],
										'activation'  => $is_valid_license['data']['today']
									)
								);
							else :
								update_option( 
									$option_key, 
									array( 
										'key'         => $is_valid_license['key'],
										'activation'  => $is_valid_license['data']['today']
									)
								);
							endif;
									// VALID
							$uat_key       = function_exists( 'get_site_option' ) ? get_site_option( $option_key ) : get_option( $option_key );
							$messages       = isset( $is_valid_license['message'] ) && is_array( $is_valid_license['message'] ) ? implode( '<br>', $is_valid_license['message'] ) : '';
							do_action( 'uat_get_alertbox', 'success', $is_valid_license, $license_key ); 
						else :
									// INVALID
							do_action( 'uat_get_alertbox', 'error', $is_valid_license, $license_key );
						endif;
					endif;
				elseif ( isset( $_POST['moove_uat_license_key'] ) && isset( $_POST['uat_deactivate_license'] ) ) :
					$license_key  = sanitize_text_field( $_POST['moove_uat_license_key'] );
				if ( $license_key ) :
					$license_manager    = new Moove_UAT_License_Manager();
					$is_valid_license   = $license_manager->premium_deactivate( $license_key );
					if ( function_exists( 'update_site_option' ) ) :
						update_site_option( 
							$option_key, 
							array(
								'key'           => $license_key,
								'deactivation'  => strtotime('now')
							)
						);
					else :
						update_option( 
							$option_key, 
							array(
								'key'           => $license_key,
								'deactivation'=> strtotime('now')
							)
						);
					endif;
					$uat_key       = function_exists( 'get_site_option' ) ? get_site_option( $option_key ) : get_option( $option_key );

					if ( $is_valid_license && isset( $is_valid_license['valid'] ) && $is_valid_license['valid'] === true ) :
									// VALID
						do_action( 'uat_get_alertbox', 'success', $is_valid_license, $license_key ); 
					else :
									// INVALID
						do_action( 'uat_get_alertbox', 'error', $is_valid_license, $license_key );
					endif;
				endif;
			elseif ( $uat_key && isset( $uat_key['key'] ) && isset( $uat_key['activation'] ) ) :
				$license_manager    = new Moove_UAT_License_Manager();
			$is_valid_license   = $license_manager->get_premium_add_on( $uat_key['key'], 'check' );
			$uat_key           = function_exists( 'get_site_option' ) ? get_site_option( $option_key ) : get_option( $option_key );
			if ( $is_valid_license && isset( $is_valid_license['valid'] ) && $is_valid_license['valid'] === true ) :
				// VALID
				do_action( 'uat_get_alertbox', 'success', $is_valid_license, $uat_key ); 
			else :
				// INVALID
				do_action( 'uat_get_alertbox', 'error', $is_valid_license, $uat_key );
			endif;
		endif;  
		?>
	</td>
</tr>
<?php do_action( 'uat_licence_input_field', $is_valid_license, $uat_key ); ?>

</tbody>
</table>

<br />
<?php do_action( 'uat_licence_action_button', $is_valid_license, $uat_key ); ?>
<br />
<?php do_action('uat_cc_general_buttons_settings'); ?>
</form>
<div class="uat-admin-popup uat-admin-popup-deactivate" style="display: none;">
	<span class="uat-popup-overlay"></span>
	<div class="uat-popup-content">
		<div class="uat-popup-content-header">
			<a href="#" class="uat-popup-close"><span class="dashicons dashicons-no-alt"></span></a>
		</div>
		<!--  .uat-popup-content-header -->
		<div class="uat-popup-content-content">
			<h4><strong>Please confirm that you would like to de-activate this licence. </strong></h4><p><strong>This action will remove all of the premium features from your website.</strong></p>
			<button class="button button-primary button-deactivate-confirm">
				<?php _e('Deactivate Licence','import-uat-feed'); ?>
			</button>
		</div>
		<!--  .uat-popup-content-content -->    
	</div>
	<!--  .uat-popup-content -->
</div>
<!--  .uat-admin-popup -->
