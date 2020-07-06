<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 * Moove_Activity_Database_Model File Doc Comment
 *
 * @category  Moove_Activity_Database_Model
 * @package   moove-activity-tracking
 * @author    Gaspar Nemes
 */

/**
 * Moove_Activity_Database_Model Class Doc Comment
 *
 * @category Class
 * @package  Moove_Activity_Database_Model
 * @author   Gaspar Nemes
 */
class Moove_Activity_Database_Model {

	static $primary_key = 'id';

	function __construct() {
		global $wpdb;
		if ( $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->prefix}moove_activity_log'" ) != $wpdb->prefix . 'moove_activity_log' ) {
			$wpdb->query(
				"CREATE TABLE {$wpdb->prefix}moove_activity_log(
        id integer not null auto_increment,
        post_id integer not null,
        user_id integer DEFAULT NULL,
        status TINYTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
        user_ip TINYTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
        city TINYTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
        post_type TINYTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
        referer TINYTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
        campaign_id TINYTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
        month_year TINYTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
        display_name TINYTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
        visit_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
        );"
			);
			update_option( 'moove_importer_has_database', false );
		}
	}

	private static function _table() {
		global $wpdb;
		$tablename = 'moove_activity_log';
		return $wpdb->prefix . $tablename;
	}

	private static function _fetch_sql( $value ) {
		global $wpdb;
		$table_name = self::_table();
		$key        = static::$primary_key;
		return $wpdb->prepare( "SELECT * FROM `$table_name` WHERE `$key` = %s", $value );
	}

	private static function _fetch_log_sql( $key, $value ) {
		global $wpdb;

		$table_name = self::_table();
		$where      = '';
		if ( $key && $value ) :
			return $wpdb->prepare( "SELECT * FROM `$table_name` WHERE `$key` = %s", $value );
		else :
			return $wpdb->prepare( "SELECT * FROM `$table_name` WHERE %s = %s", '1', '1' );
		endif;

	}

	private static function _fetch_search_sql( $where ) {
		global $wpdb;
		$table_name = self::_table();

		$_where     = '';
		if ( is_array( $where ) && ! empty( $where ) ) :
			$relation = '';
			if ( isset( $where['relation'] ) ) :
				$relation = $where['relation'];
			endif;
			$count = 0;
			foreach ( $where as $key => $value ) :
				$count++;
				if ( $key !== 'relation' ) :
					$_where .= $count === 1 ? '' : ' ' . $relation . ' ';
					$_key    = $value['key'];
					$_val    = $value['value'];
					if ( isset( $value['operator'] ) && $value['operator'] === 'IN' ) :
						$_where .= " `$_key` " . $value['operator'] . " ( $_val ) ";
					else :
						$_where .= " `$_key` = '$_val' ";
					endif;
				endif;
			endforeach;
			// return $wpdb->prepare( "SELECT * FROM `$table_name`", null );
			return $wpdb->prepare( "SELECT * FROM `$table_name` WHERE $_where AND %s = %s", '1', '1' );
		else :
			return $wpdb->prepare( "SELECT * FROM `$table_name` WHERE %s = %s", '1', '1' );
		endif;
	}

	private static function _fetch_old_logs_sql( $post_id, $end_date ) {
		global $wpdb;
		$table_name = self::_table();
		return $wpdb->prepare( "DELETE FROM `$table_name` WHERE `post_id` = %s AND `visit_date` <= %s", $post_id, $end_date );
	}

	private static function _fetch_users_sql() {
		global $wpdb;
		$table_name = self::_table();
		return $wpdb->prepare( "SELECT DISTINCT display_name, user_id FROM `$table_name` WHERE %s = %s ORDER BY display_name ASC", '1', '1'  );
	}

	static function get( $value ) {
		global $wpdb;
		return $wpdb->get_row( self::_fetch_sql( $value ) );
	}

	static function get_search_results( $where ) {
		global $wpdb;
		return $wpdb->get_results( self::_fetch_search_sql( $where ) );
	}

	static function get_log( $key, $value ) {
		global $wpdb;
		return $wpdb->get_results( self::_fetch_log_sql( $key, $value ) );
	}

	static function get_usernames() {
		global $wpdb;
		return $wpdb->get_results( self::_fetch_users_sql() );
	}

	static function remove_old_logs( $post_id, $end_date ) {
		global $wpdb;
		return $wpdb->get_results( self::_fetch_old_logs_sql( $post_id, $end_date ) );
	}

	static function delete_log( $key, $value ) {
		global $wpdb;
		$where      = $key . '=' . $value;
		$table_name = self::_table();
		return $wpdb->query( $wpdb->prepare( "DELETE FROM `$table_name` WHERE `$key` = %s", $value ) );
	}

	static function insert( $data ) {
		global $wpdb;
		$wpdb->insert( self::_table(), $data );
	}

	static function update( $data, $where ) {
		global $wpdb;
		$wpdb->update( self::_table(), $data, $where );
	}

	static function delete( $value ) {
		global $wpdb;
		$key        = static::$primary_key;
		$table_name = self::_table();
		return $wpdb->query( $wpdb->prepare( "DELETE FROM `$table_name` WHERE `$key` = %s", $value ) );
	}

	static function insert_id() {
		global $wpdb;
		return $wpdb->insert_id;
	}

	static function time_to_date( $time ) {
		return gmdate( 'Y-m-d H:i:s', $time );
	}

	static function now() {
		return self::time_to_date( time() );
	}

	static function date_to_time( $date ) {
		return strtotime( $date . ' GMT' );
	}

}
new Moove_Activity_Database_Model();
