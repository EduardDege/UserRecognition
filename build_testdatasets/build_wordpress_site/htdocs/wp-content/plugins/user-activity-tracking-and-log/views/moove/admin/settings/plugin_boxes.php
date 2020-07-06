<?php 
$activity_controller 	= new Moove_Activity_Controller();
$plugin_details 		= $activity_controller->get_plugin_details( 'user-activity-tracking-and-log' );
?>
<div class="moove-uat-plugins-info-boxes">

	<?php ob_start(); ?>
	<div class="m-plugin-box m-plugin-box-highlighted">
		<div class="box-header">
			<h4>Premium Add-On</h4>
		</div>
		<!--  .box-header -->
		<div class="box-content">
			<ul class="plugin-features">
				<li>Extend log up to 4 years</li>
				<li>Export logs to CSV</li>
				<li>Filter activity by user</li>
				<li>Anonymise IP addresses (GDPR)</li>
				<li>Set timezone, screen options</li>
				<li>Option to track logged in users only</li>
				<li>Exclude users from tracking by role</li>
			</ul>
			<hr />
			<strong>Buy Now for only <span>£49</span></strong>
			<a href="https://www.mooveagency.com/wordpress-plugins/user-activity-tracking-and-log/" target="_blank" class="plugin-buy-now-btn">Buy Now</a>
		</div>
		<!--  .box-content -->
	</div>
	<!--  .m-plugin-box -->
	<?php echo $premium_box = apply_filters( 'uat_premium_section', ob_get_clean() ); ?>

	<?php $support_class = $premium_box ? '' : 'm-plugin-box-highlighted'; ?>

	<div class="m-plugin-box m-plugin-box-support <?php echo $support_class; ?>">
		<div class="box-header">
			<h4>Need Support or New Feature?</h4>
		</div>
		<!--  .box-header -->
		<div class="box-content">
			<?php 
				$forum_link = apply_filters( 'uat_forum_section_link', 'https://support.mooveagency.com/forum/user-activity-tracking-and-log/' );
			?>
      <div class="uat-faq-forum-content">
        <p><span class="uat-chevron-left">&#8250;</span> Check the <a href="<?php echo admin_url( 'options-general.php?page=moove-activity&tab=documentation' ); ?>">Documentation section</a> to find out more.</p>
        <p><span class="uat-chevron-left">&#8250;</span> Create a new support ticket or request new features in our <a href="<?php echo $forum_link; ?>" target="_blank">Support Forum</a></p>
      </div>
      <!--  .uat-faq-forum-content -->
      <span class="uat-review-container" >
        <a href="<?php echo $forum_link; ?>#new-post" target="_blank" class="uat-review-bnt ">Create a new support ticket</a>
      </span>
		</div>
		<!--  .box-content -->
	</div>
	<!--  .m-plugin-box -->
	
	<div class="m-plugin-box">
		<div class="box-header">
			<h4>Help to improve this plugin!</h4>
		</div>
		<!--  .box-header -->
		<div class="box-content">
			<p>Enjoyed this plugin? <br />You can help by <a href="https://wordpress.org/support/plugin/user-activity-tracking-and-log/reviews/?rate=5#new-post" class="uat_admin_link" target="_blank">rating this plugin on wordpress.org.</a></p>
			<hr />
			<?php if ( $plugin_details ) : ?>
				<div class="plugin-stats">
					<div class="plugin-downloads">
						Downloads: <strong><?php echo number_format( $plugin_details->downloaded, 0, '', ','); ?></strong>
					</div>
					<!--  .plugin-downloads -->
					<div class="plugin-active-installs">
						Active installations: <strong><?php echo number_format( $plugin_details->active_installs, 0, '', ','); ?>+</strong>
					</div>
					<!--  .plugin-downloads -->
					<div class="plugin-rating">
						<?php 
						$rating_val = $plugin_details->rating * 5 / 100;
						if ( $rating_val > 0 ) :
							$args = array(
								'rating' 	=> $rating_val,
								'number' 	=> $plugin_details->num_ratings,
								'echo'		=> false
							);
							$rating = wp_star_rating( $args );
						endif;
						?>
						<?php if ( $rating ) : ?>
							<?php echo $rating; ?>
						<?php endif; ?>
					</div>
					<!--  .plugin-rating -->
				</div>
				<!--  .plugin-stats -->
			<?php endif; ?>
		</div>
		<!--  .box-content -->
	</div>
	<!--  .m-plugin-box -->

</div>
<!--  .moove-plugins-info-boxes -->