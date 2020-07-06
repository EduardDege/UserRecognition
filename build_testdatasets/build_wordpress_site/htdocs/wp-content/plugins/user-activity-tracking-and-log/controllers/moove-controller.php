<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 * Moove_Controller File Doc Comment
 *
 * @category Moove_Controller
 * @package   moove-activity-tracking
 * @author    Gaspar Nemes
 */

/**
 * Moove_Controller Class Doc Comment
 *
 * @category Class
 * @package  Moove_Controller
 * @author   Gaspar Nemes
 */
class Moove_Activity_Controller {
	/**
	 * Construct function
	 */
	public function __construct() {
		
	}

	/**
	 * Checking if database exists
	 *
	 * @return bool
	 */
	public static function moove_importer_check_database() {
		$has_database = get_option( 'moove_importer_has_database' ) ? true : false;
		return $has_database;
	}

	public static function moove_activity_save_user_options() {

		if ( isset( $_POST['form_data'] ) ) :
			parse_str( $_POST['form_data'], $user_options );
			$user_id = intval( $user_options['wp_user_id'] );
			if ( $user_id ) :
				update_user_meta( $user_id, 'moove_activity_screen_options', $user_options );
		  	endif;
	  	endif;
		die();
	}

	public static function get_plugin_details( $plugin_slug = '' ) {
		$plugin_return   = false;
		$wp_repo_plugins = '';
		$wp_response     = '';
		$wp_version      = get_bloginfo( 'version' );
		if ( $plugin_slug && $wp_version > 3.8 ) :
			$args        = array(
				'author' => 'MooveAgency',
				'fields' => array(
					'downloaded'      => true,
					'active_installs' => true,
					'ratings'         => true,
				),
			);
			$wp_response = wp_remote_post(
				'http://api.wordpress.org/plugins/info/1.0/',
				array(
					'body' => array(
						'action'  => 'query_plugins',
						'request' => serialize( (object) $args ),
					),
				)
			);
			if ( ! is_wp_error( $wp_response ) ) :
				$wp_repo_response = unserialize( wp_remote_retrieve_body( $wp_response ) );
				$wp_repo_plugins  = $wp_repo_response->plugins;
			endif;
			if ( $wp_repo_plugins ) :
				foreach ( $wp_repo_plugins as $plugin_details ) :
					if ( $plugin_slug == $plugin_details->slug ) :
						$plugin_return = $plugin_details;
					endif;
				endforeach;
			endif;
		endif;
		return $plugin_return;
	}

	/**
	 * Importing logs stored in post_meta to database
	 *
	 * @return void
	 */
	public static function import_log_to_database() {
		$post_types = get_post_types( array( 'public' => true ) );
		unset( $post_types['attachment'] );
		$uat_db_controller = new Moove_Activity_Database_Model();
		$query             = array(
			'post_type'      => $post_types,
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'meta_query'     => array(
				'relation' => 'OR',
				array(
					'key'     => 'ma_data',
					'value'   => null,
					'compare' => '!=',
				),
			),
		);
		$log_query         = new WP_Query( $query );

		if ( $log_query->have_posts() ) :
			while ( $log_query->have_posts() ) :
				$log_query->the_post();
				$_post_meta      = get_post_meta( get_the_ID(), 'ma_data' );
				$_ma_data_option = $_post_meta[0];
				$ma_data         = unserialize( $_ma_data_option );

				if ( $ma_data['log'] && is_array( $ma_data['log'] ) ) :
					foreach ( $ma_data['log'] as $log ) :
						$date            = date( 'Y-m-d H:i:s', $log['time'] );
						$data_to_instert = array(
							'post_id'      => get_the_ID(),
							'user_id'      => $log['uid'],
							'status'       => $log['response_status'],
							'user_ip'      => $log['show_ip'],
							'city'         => $log['city'],
							'display_name' => $log['display_name'],
							'post_type'    => get_post_type( get_the_ID() ),
							'referer'      => $log['referer'],
							'month_year'   => get_gmt_from_date( $date, 'm' ) . get_gmt_from_date( $date, 'Y' ),
							'visit_date'   => get_gmt_from_date( $date, 'Y-m-d H:i:s' ),
							'campaign_id'  => isset( $ma_data['campaign_id'] ) ? $ma_data['campaign_id'] : '',
						);
						$save_to_db_enabled = apply_filters( 'moove_uat_filter_data', $data_to_instert );
						if ( $save_to_db_enabled ) :
							$resp            = $uat_db_controller->insert( $data_to_instert );
						endif;
					endforeach;
				endif;
			endwhile;
		endif;
		wp_reset_query();
		wp_reset_postdata();
		update_option( 'moove_importer_has_database', true );
	}
	/**
	 * Create admin menu page
	 *
	 * @return void
	 */
	public static function moove_register_activity_menu_page() {
    $capability = apply_filters( 'uat-activity-log-capability', 'manage_options' );
		add_menu_page(
			'Activity Log', // Page_title.
			'Activity log', // Menu_title.
			$capability, // Capability.
			'moove-activity-log', // Menu_slug.
			array( 'Moove_Activity_Controller', 'moove_activity_menu_page' ), // Function.
			'dashicons-visibility', // Icon_url.
			3 // Position.
		);
	}

	/**
	 * Pagination function for arrays.
	 *
	 * @param  array $display_array      Array to paginate.
	 * @param  int   $page                Start number.
	 * @param  int   $ppp                 Offset.
	 * @return array                    Paginated array
	 */
	public static function moove_pagination( $display_array, $page, $ppp ) {
		$page      = $page < 1 ? 1 : $page;
		$start     = ( ( $page - 1 ) * ( $ppp ) );
		$offset    = $ppp;
		$out_array = $display_array;
		if ( is_array( $display_array ) ) :
			$out_array = array_slice( $display_array, $start, $offset );
		endif;
		return $out_array;
	}

	/**
	 * Activity log page view
	 *
	 * @return  void
	 */
	public static function moove_activity_menu_page() {
		$uat_view = new Moove_Activity_View();
		echo $uat_view->load(
			'moove.admin.settings.activity_log',
			null
		);
	}

	/**
	 * Tracking the user access when the post will be saved. (status = updated)
	 */
	public static function moove_track_user_access_save_post( $post_id ) {
    if ( get_post_type( $post_id ) !== 'nav_menu_item' ) :
  		$uat_controller = new Moove_Activity_Controller();
  		$uat_shrotcodes = new Moove_Activity_Shortcodes();
  		$uat_controller->moove_remove_old_logs( get_the_ID() );
  		$post_types = get_post_types( array( 'public' => true ) );
  		unset( $post_types['attachment'] );
  		// Trigger only on specified post types.
  		if ( ! in_array( get_post_type(), $post_types ) ) :
  			return;
  		endif;
  		$ma_data    = array();
  		$_post_meta = get_post_meta( get_the_ID(), 'ma_data' );
  		if ( isset( $_post_meta[0] ) ) :
  			$_ma_data_option = $_post_meta[0];
  			$ma_data         = unserialize( $_ma_data_option );
  		endif;
  		$post            = sanitize_post( $GLOBALS['post'] );
  		$activity_status = 'updated';
  		$ip              = $uat_shrotcodes->moove_get_the_user_ip();
  		$loc_enabled 		 = apply_filters('uat_show_location_by_ip', true);
  		$details         = $loc_enabled ? $uat_shrotcodes->get_location_details( $ip ) : false;
  		$city 					 = $loc_enabled && isset( $details->city ) ? $details->city : '';
  		$data            = array(
  			'pid'    => intval( get_the_ID() ),
  			'uid'    => intval( get_current_user_id() ),
  			'status' => esc_attr( $activity_status ),
  			'uip'    => esc_attr( $ip ),
  			'city'   => $city,
  			'ref'    => esc_url( wp_get_referer() ),
  		);

  		if ( isset( $ma_data['campaign_id'] ) ) :
  			$uat_controller->moove_create_log_entry( $data );
  		endif;
    endif;
	}

	/**
	 * Tracking the user access on the front end. (status = visited)
	 */
	public function moove_track_user_access() {
		$post_id        = get_the_ID();
		$uat_controller = new Moove_Activity_Controller();
		$uat_shrotcodes = new Moove_Activity_Shortcodes();
		$post           = get_post( $post_id );
		$uat_controller->moove_remove_old_logs( $post_id );
		// Not need in admin.
		if ( is_admin() ) :
			return;
		endif;

		// Run on singles or pages.
		if ( is_singule() || is_page() ) :

			$post_types = get_post_types( array( 'public' => true ) );
			unset( $post_types['attachment'] );
			// Trigger only on specified post types.
			if ( ! in_array( get_post_type(), $post_types ) ) :
				return;
			endif;
			$_post_meta      = get_post_meta( $post_id, 'ma_data' );
			$_ma_data_option = $_post_meta[0];
			$ma_data         = unserialize( $_ma_data_option );
			$activity_status = 'visited';
			$ip              = $uat_shrotcodes->moove_get_the_user_ip();
			$loc_enabled 		 = apply_filters('uat_show_location_by_ip', true);
			$details         = $loc_enabled ? $uat_shrotcodes->get_location_details( $ip ) : false;
			$city 					 = $loc_enabled && isset( $details->city ) ? $details->city : '';
			$data = array(
				'pid'    => $post_id,
				'uid'    => get_current_user_id(),
				'status' => $activity_status,
				'uip'    => esc_attr( $ip ),
				'city'   => $city,
				'ref'    => esc_url( wp_get_referer() ),
			);

			if ( isset( $ma_data['campaign_id'] ) ) :
				$uat_controller->moove_create_log_entry( $data );
			endif;
		endif;
	}

	/**
	 * Tracking the user access on the front end. (status = visited)
	 */
	public static function moove_track_user_access_ajax() {
		$uat_controller = new Moove_Activity_Controller();
		$uat_shrotcodes = new Moove_Activity_Shortcodes();
		$post_id        = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : false;
		$is_page        = isset( $_POST['is_page'] ) ? sanitize_text_field( $_POST['is_page'] ) : false;
		$is_single      = isset( $_POST['is_single'] ) ? sanitize_text_field( $_POST['is_single'] ) : false;
		$user_id        = isset( $_POST['user_id'] ) ? intval( $_POST['user_id'] ) : false;
		$referrer       = isset( $_POST['referrer'] ) ? sanitize_text_field( $_POST['referrer'] ) : '';

		if ( $post_id ) :
			$post = get_post( $post_id );
			$uat_controller->moove_remove_old_logs( $post_id );
			// Run on singles or pages.
			if ( $is_page || $is_single ) :

				$post_types = get_post_types( array( 'public' => true ) );
				unset( $post_types['attachment'] );
				// Trigger only on specified post types.
				if ( ! in_array( get_post_type( $post_id ), $post_types ) ) :
					return;
				endif;
				$_post_meta      = get_post_meta( $post_id, 'ma_data' );
				$_ma_data_option = isset( $_post_meta[0] ) ? $_post_meta[0] : serialize( array() );
				$ma_data         = unserialize( $_ma_data_option );
				$activity_status = 'visited';
				$ip              = $uat_shrotcodes->moove_get_the_user_ip();
				$loc_enabled 		 = apply_filters('uat_show_location_by_ip', true);
				$details         = $loc_enabled ? $uat_shrotcodes->get_location_details( $ip ) : false;
				$city 					 = $loc_enabled && isset( $details->city ) ? $details->city : '';

				$data = array(
					'pid'    => $post_id,
					'uid'    => $user_id,
					'status' => $activity_status,
					'uip'    => esc_attr( $ip ),
					'city'   => $city,
					'ref'    => $referrer,
				);

				if ( isset( $ma_data['campaign_id'] ) ) :
					$uat_controller->moove_create_log_entry( $data );
				endif;
			endif;
			wp_reset_postdata();
		endif;
		die();
	}

	/**
	 * Function to delete a custom post logsm or all logs (if the functions runs without params.)
	 *
	 * @param  int $post_types Post ID.
	 */
	public function moove_clear_logs( $post_types = false ) {
		$uat_db_controller = new Moove_Activity_Database_Model();
		$uat_content       = new Moove_Activity_Content();

		if ( ! $post_types ) :
			$post_types = get_post_types( array( 'public' => true ) );
			unset( $post_types['attachment'] );
		else :
			delete_post_meta( $post_types, 'ma_data' );
			$uat_db_controller->delete_log( 'post_id', $post_types );
			$uat_content->moove_save_post( $post_types, 'enable' );
			return;
		endif;

		$query = array(
			'post_type'      => $post_types,
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'meta_query'     => array(
				'relation' => 'OR',
				array(
					'key'     => 'ma_data',
					'value'   => null,
					'compare' => '!=',
				),
			),
		);

		query_posts( $query );
		if ( have_posts() ) :
			while ( have_posts() ) :
				the_post();

				$_post_meta      = get_post_meta( get_the_ID(), 'ma_data' );
				$_ma_data_option = isset( $_post_meta[0] ) ? $_post_meta[0] : serialize( array() );
				$ma_data         = unserialize( $_ma_data_option );
				$uat_db_controller->delete_log( 'post_id', get_the_ID() );
				if ( isset( $ma_data['campaign_id'] ) ) :
					delete_post_meta( get_the_ID(), 'ma_data' );
					$uat_content->moove_save_post( get_the_ID(), 'enable' );
				endif;
			endwhile;

		endif;
		wp_reset_query();
		wp_reset_postdata();
	}

	/**
	 * Remove the old logs. It calculates the difference between two dates,
	 * and if the difference between the log date and the current date is higher than
	 * the day(s) setted up on the settings page, than it remove that entry.
	 *
	 * @param  int $post_id Post ID.
	 */
	public static function moove_remove_old_logs( $post_id ) {
		$_post_meta        = get_post_meta( $post_id, 'ma_data' );
		$ma_data_old       = array();
		$uat_db_controller = new Moove_Activity_Database_Model();
		if ( isset( $_post_meta[0] ) ) :
			$_ma_data_option = $_post_meta[0];
			$ma_data_old     = unserialize( $_ma_data_option );
		endif;
		if ( isset( $ma_data_old['campaign_id'] ) ) :
			$post_type         = get_post_type( $post_id );
			$activity_settings = get_option( 'moove_post_act' );
			$days              = intval( $activity_settings[ $post_type . '_transient' ] );
			$today             = date_create( date( 'm/d/Y', current_time( 'timestamp', 0 ) ) );
			$activity          = $uat_db_controller->get_log( 'post_id', $post_id );
			$end_date          = date( 'Y-m-d H:i:s', strtotime( "-$days days" ) );
			$uat_db_controller->remove_old_logs( $post_id, $end_date );
		endif;
	}

	/**
	 * Create the log file for post.
	 *
	 * @param  array $data Aarray with log data.
	 */
	protected function moove_create_log_entry( $data ) {
		$_post_meta        = get_post_meta( $data['pid'], 'ma_data' );
		$ma_data           = array();
		$uat_controller    = new Moove_Activity_Controller();
		$uat_db_controller = new Moove_Activity_Database_Model();
		if ( isset( $_post_meta[0] ) ) :
			$_ma_data_option = $_post_meta[0];
			$ma_data         = unserialize( $_ma_data_option );
		endif;
		$log = isset( $ma_data['log'] ) ? $ma_data['log'] : array();
		// We don't have anything set up.
		if ( $log === '' || ( is_array( $log ) && count( $log ) === 0 ) ) :
			$log = array();
		endif;
		$user = get_user_by( 'id', $data['uid'] );
		if ( $user ) :
			$username = $user->data->display_name;
		else :
			$username = __( 'Unknown', 'user-activity-tracking-and-log' );
		endif;

		if ( $data['city'] ) :
			$city_name = $data['city'];
		else :
			$city_name = __( 'Unknown', 'user-activity-tracking-and-log' );
		endif;
		// $referrer = esc_url( wp_get_referer() );
		$uat_controller->moove_remove_old_logs( $data['pid'] );

		$date = date( 'Y-m-d H:i:s', current_time( 'timestamp', 0 ) );
		$data_to_instert = array(
			'post_id'      => $data['pid'],
			'user_id'      => intval( $data['uid'] ),
			'status'       => esc_attr( $data['status'] ),
			'user_ip'      => esc_attr( $data['uip'] ),
			'display_name' => $username,
			'city'         => $city_name,
			'post_type'    => get_post_type( $data['pid'] ),
			'referer'      => $data['ref'],
			'month_year'   => get_gmt_from_date( $date, 'm' ) . get_gmt_from_date( $date, 'Y' ),
			'visit_date'   => get_gmt_from_date( $date, 'Y-m-d H:i:s' ),
			'campaign_id'  => isset( $ma_data['campaign_id'] ) ? $ma_data['campaign_id'] : '',
		);
		$save_to_db_enabled = apply_filters( 'moove_uat_filter_data', $data_to_instert );
		if ( $save_to_db_enabled ) :
			$uat_db_controller->insert( $data_to_instert );
		endif;
	}

	public static function moove_get_activity_dates( $log_array, $active ) {
		ob_start();
		if ( is_array( $log_array ) && ! empty( $log_array ) ) :
			$date_array = array();
			foreach ( $log_array as $log_entry ) :
				if ( $log_entry['time'] ) :
					$time                          = strtotime( $log_entry['time'] );
					$month                         = date( 'm', $time );
					$day                           = date( 'd', $time );
					$year                          = date( 'Y', $time );
					$month_name                    = date( 'F', $time );
					$date_array[ $year ][ $month ] = array(
						'month_name' => $month_name,
						'year'       => $year,
					);
				endif;
			endforeach;
			krsort( $date_array );
			?>
			<select name="m" id="filter-by-date">
				<option selected="selected" value="0"><?php _e( 'All dates', 'user-activity-tracking-and-log' ); ?></option>
				<?php
				foreach ( $date_array as $year => $year_entry ) :
					$_date_entry = $year_entry;
					krsort( $_date_entry );
					foreach ( $_date_entry as $month => $_ndate_entry ) :
						?>
						<?php
							$selected = '';
							$term     = $month . $year;
						if ( $active != 0 && intval( $active ) == intval( $term ) ) :
							$selected = 'selected="selected"';
							endif;
						?>
						<option value="<?php echo $month . $year; ?>" <?php echo $selected; ?>>
							<?php echo $_ndate_entry['month_name'] . ' ' . $year; ?>
						</option>
						<?php
					endforeach;
				endforeach;
				?>
			</select>
			<?php
		endif;

		return ob_get_clean();
	}

	public static function moove_get_filtered_array( $log_array, $m, $uid, $cat, $search_term, $role = -1 ) {
		$sorted_array      = array();
		$uat_db_controller = new Moove_Activity_Database_Model();
    $plugin_settings   = apply_filters( 'moove_uat_filter_plugin_settings', get_option('moove_post_act') );

		if ( $cat === 0 && sanitize_text_field( $m ) == '0' && $search_term == '' && $uid == -1 && $role == -1 ) {
			return $log_array;
		}

		$has_previous = false;
		$where        = array();

		if ( sanitize_text_field( $m ) != '0' ) :
			$has_previous = true;
			$where[]      = array(
				'key'   => 'month_year',
				'value' => $m,
			);
		endif;

		if ( $cat !== 0 ) :
			if ( $has_previous ) :
				$where['relation'] = 'AND';
			endif;
			$where[]      = array(
				'key'   => 'post_type',
				'value' => $cat,
			);
			$has_previous = true;
		endif;

		if ( $uid !== -1 && $uid !== '-1' ) :
			if ( $has_previous ) :
				$where['relation'] = 'AND';
			endif;
			$where[]      = array(
				'key'   => 'user_id',
				'value' => $uid,
			);
			$has_previous = true;
		endif;

		$results = $uat_db_controller->get_search_results( $where );


		if ( ! $has_previous ) :
			$results = $uat_db_controller->get_log( false, false );

		endif;

		$f_user_ids = array();
		$has_role_filer = false;

		if ( $role !== -1 && $role !== '-1' && is_string( $role ) ) :
			$filtered_users = get_users( 'role=' . $role );
			$has_role_filer = true;
			if ( is_array( $filtered_users ) ) :				
				foreach ( $filtered_users as $f_user ) :
					$f_user_ids[] = $f_user->ID;
				endforeach;
			endif;
		endif;

		$return = array();
		if ( $results && is_array( $results ) ) :
			foreach ( $results as $log ) :

				$import_this = false;
				if ( $search_term !== '' ) :
					$title = strtolower( get_the_title( $log->post_id ) );
					if ( strpos( $title, strtolower( $search_term ) ) !== false ) :
						$import_this = true;
					endif;
				else :
					$import_this = true;
				endif;

        if ( $has_role_filer ) :
          $import_this = false;
          if ( is_array( $f_user_ids ) && in_array( $log->user_id, $f_user_ids ) ) :
            $import_this = true;
          endif;
        endif;

				if ( $import_this ) :
          $post_type = get_post_type( $log->post_id );
          if ( isset( $plugin_settings[$post_type] ) && intval( $plugin_settings[$post_type] ) === 1 ) :
  					$return[] = array(
  						'post_id'         => $log->post_id,
  						'time'            => $log->visit_date,
  						'uid'             => $log->user_id,
  						'display_name'    => $log->display_name,
  						'ip_address'      => $log->user_ip,
  						'response_status' => $log->status,
  						'referer'         => $log->referer,
  						'city'            => $log->city,
  					);
          endif;
				endif;

			endforeach;
		endif;



		return $return;
	}

}
new Moove_Activity_Controller();

class Moove_Activity_Array_Order {
	private $orderby;

	function __construct( $orderby = '' ) {
		$this->orderby = $orderby;
	}

	function moove_sort_array( $a, $b, $orderby ) {
		if ( $orderby == 'title' ) :
			return strcmp( get_the_title( $a['post_id'] ), get_the_title( $b['post_id'] ) );
		elseif ( $orderby == 'posttype' ) :
			return strcmp( get_post_type( $a['post_id'] ), get_post_type( $b['post_id'] ) );
		else :
			return strcmp( $a[ $orderby ], $b[ $orderby ] );
		endif;
	}


	function custom_order( $a, $b ) {
		$uat_array_order = new Moove_Activity_Array_Order();
		return $uat_array_order->moove_sort_array( $a, $b, $this->orderby );
	}
}
