<?php
include '../../../../wp-load.php';
function createNewTable($wpdb, $table) {

	$table_name = $wpdb->prefix . $table;
	$sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		user_id mediumint(9),
		browser text NOT NULL,
		browser_version text NOT NULL,
		IP text NOT NULL,
		user_agent text NOT NULL,
		platform text NOT NULL,
		login_attempt int DEFAULT 0,
		login_date DATETIME,
		logout_date DATETIME,
		duration(ms) text NOT NULL,
		loginstatus text NOT NULL,
		PRIMARY KEY  (id)
	)";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );

}

function insertToDB($wpdb, $table, $device, $user_id) {

	$table_name = $wpdb->prefix . $table;
	$push_array = array_merge($device, array("user_id" => $user_id));

	print_r($push_array);
	$wpdb->insert(
		$table_name,
		$push_array
	);
}
 ?>
