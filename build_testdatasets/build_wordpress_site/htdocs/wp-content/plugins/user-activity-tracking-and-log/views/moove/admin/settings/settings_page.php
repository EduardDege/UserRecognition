<div class="wrap moove-activity-plugin-wrap" id="uat-settings-cnt">
	<h1><?php _e('Global content activity tracking', 'user-activity-tracking-and-log'); ?></h1>
	<?php
	if (isset($_GET['tab'])) {
		$active_tab = $_GET['tab'];
	} else {
		$active_tab = "post_type_activity";
	} // end if
	?>
	<br />
	<div class="uat-tab-section-cnt">
		<?php do_action('uat_premium_update_alert'); ?>
		<h2 class="nav-tab-wrapper">          
			<a href="<?php echo admin_url( '/options-general.php?page=moove-activity&tab=post_type_activity' ); ?>" class="nav-tab <?php echo $active_tab == 'post_type_activity' ? 'nav-tab-active' : ''; ?>">
				<?php _e('Post type activity tracking','user-activity-tracking-and-log'); ?>
			</a>
			<a href="<?php echo admin_url( '/options-general.php?page=moove-activity&tab=licence' ); ?>" class="nav-tab <?php echo $active_tab == 'licence' ? 'nav-tab-active' : ''; ?>">
				<?php _e('Licence Manager','user-activity-tracking-and-log'); ?>
			</a>
			<a href="?page=moove-activity&tab=documentation" class="nav-tab <?php echo $active_tab == 'documentation' ? 'nav-tab-active' : ''; ?>">
				<?php _e('Documentation', 'user-activity-tracking-and-log'); ?>
			</a>

			<?php do_action('moove-activity-tab-extensions', $active_tab); ?>
			
		</h2>
		<div class="moove-form-container <?php echo $active_tab; ?>">
			<a href="https://www.mooveagency.com" target="blank" title="WordPress agency"><span class="moove-logo"></span></a>

			<?php
			$content = array(
				'tab' => $active_tab,
				'data' => $data
			);
			do_action('moove-activity-tab-content', $content, $active_tab);
			?>

		</div>
		<!-- moove-form-container -->

	</div>
	<!--  .uat-tab-section-cnt -->

	<?php 
	$view_cnt = new Moove_Activity_View();
	echo $view_cnt->load( 'moove.admin.settings.plugin_boxes', array() );
	?>
</div>
<!-- wrap -->