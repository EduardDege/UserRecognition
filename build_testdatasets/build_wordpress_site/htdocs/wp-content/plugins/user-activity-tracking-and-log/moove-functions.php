<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Moove_Functions File Doc Comment
 *
 * @category Moove_Functions
 * @package   moove-activity-tracking
 * @author    Gaspar Nemes
 */

/**
 * Returns activity by referrer URL.
 *
 * @param  mixt $url URL.
 * @return  string
 */
function moove_activity_get_referrer_link_by_url( $url ) {
	$post_id = url_to_postid( $url );
	$return  = '';
	if ( $post_id ) : ?>
		<a href="<?php the_permalink( $post_id ); ?>" title="<?php echo get_the_title( $post_id ); ?>" target="_blank"><?php echo get_the_title( $post_id ); ?></a>
	<?php else : ?>
		<?php
		if ( strpos( $url, 'wp-admin/post' ) ) :
			$query_str = wp_parse_url( $url, PHP_URL_QUERY );
			parse_str( $query_str, $query_params );
			if ( isset( $query_params['post'] ) && intval( $query_params['post'] ) ) :
				$page_name = 'Edit Post';
				$edit_url  = get_edit_post_link( intval( $query_params['post'] ) );
			else :
				$page_name = $url;
				$edit_url  = $url;
			endif;
			?>
			<a href="<?php echo esc_url( $edit_url ); ?>" title="<?php echo esc_html( $page_name ); ?>" target="_blank"><?php echo esc_html( $page_name ); ?></a>
		<?php else : ?>
			<a href="<?php echo esc_url( $url ); ?>" target="_blank" title="<?php echo esc_url( $url ); ?>"><?php echo esc_url( $url ); ?></a>
		<?php endif; ?>

	<?php
	endif;
	return $return;
}

function moove_activity_get_timezone_dropdown( $selected ) {
	$list = moove_activity_timezone_list( $selected );

	?>
	<select name="timezone">
		<?php foreach ( $list as $t ) : ?>
			  <option value="<?php echo esc_html( $t['zone'] ); ?>" <?php echo $selected === $t['zone'] ? 'selected="selected"' : ''; ?>>
				<?php echo esc_html( $t['zone'] ); ?>
			  </option>
		<?php endforeach; ?>
	</select>
	<?php
}

function moove_activity_timezone_converter( $date_time, $offset ) {

	$timestamp = strtotime( $date_time );

	$date           = new \DateTime();
	$datetimeFormat = 'Y-m-d H:i:s';

	$date->setTimestamp( $timestamp );
	$date->format( $datetimeFormat );
	$date_with_timezone = $date;

	$sign = $offset < 0 ? '-' : '+';

	return $dateWithTimezone;
}

function moove_activity_timezone_list() {
	$zones_array = array();
	$timestamp   = time();
	foreach ( timezone_identifiers_list() as $key => $zone ) {
		date_default_timezone_set( $zone );
		$zones_array[ $key ]['zone'] = $zone;
	}
	return $zones_array;
}

function moove_activity_get_plugin_dir() {
	return plugins_url( basename( dirname( __FILE__ ) ) );
}

function moove_activity_convert_date( $selected, $date, $options ) {
	if ( $selected === 'a' ) :
		$date = new DateTime( $date );
		$date->setTimezone( new DateTimeZone( 'UTC' ) );
		return $date->format( 'Y-m-d H:i:s' );

	elseif ( $selected === 'b' ) :
		$tz   = moove_activity_get_blog_timezone();
		$date = new DateTime( $date );
		$date->setTimezone( new DateTimeZone( $tz ) );
		return $date->format( 'Y-m-d H:i:s' );

	elseif ( $selected === 'c' ) :
		$tz   = isset( $options['timezone'] ) ? $options['timezone'] : 'UTC';
		$date = new DateTime( $date );
		$date->setTimezone( new DateTimeZone( $tz ) );
		return $date->format( 'Y-m-d H:i:s' );
	endif;
}

/**
 *  Returns the blog timezone
 *
 * Gets timezone settings from the db. If a timezone identifier is used just turns
 * it into a DateTimeZone. If an offset is used, it tries to find a suitable timezone.
 * If all else fails it uses UTC.
 *
 * @return DateTimeZone The blog timezone
 */
function moove_activity_get_blog_timezone() {

	$tzstring = get_option( 'timezone_string' );
	$offset   = get_option( 'gmt_offset' );

	/** Manual offset...
	 *
	* @see http://us.php.net/manual/en/timezones.others.php
	* @see https://bugs.php.net/bug.php?id=45543
	* @see https://bugs.php.net/bug.php?id=45528
	* IANA timezone database that provides PHP's timezone support uses POSIX (i.e. reversed) style signs.
	*/

	if ( empty( $tzstring ) && 0 != $offset && floor( $offset ) == $offset ) {
		$offset_st = $offset > 0 ? "-$offset" : '+' . absint( $offset );
		$tzstring  = 'Etc/GMT' . $offset_st;
	}

	// Issue with the timezone selected, set to 'UTC'.
	if ( empty( $tzstring ) ) {
		$tzstring = 'UTC';
	}
	return $tzstring;
}


function uat_remove_screen_options_from_activity_screen( $boolval, $screen ) { 
	if( isset( $_GET['page'] ) && ( $_GET['page'] === 'moove-activity-log' || $_GET['page'] === 'moove-activity' ) ) {
		return false;
	}
	return $boolval; 
}
add_filter('screen_options_show_screen', 'uat_remove_screen_options_from_activity_screen', 10, 2);