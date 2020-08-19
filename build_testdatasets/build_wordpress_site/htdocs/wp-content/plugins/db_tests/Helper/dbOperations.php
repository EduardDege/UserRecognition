<?php

function createNewTable($wpdb, $table) {
	//echo $table;
	$table_name = $wpdb->prefix . "user_recognition";
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
		duration text NOT NULL,
		loginstatus text NOT NULL,
		PRIMARY KEY  (id)
	)";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );

}

function createSessionTable($wpdb){
	$table_name = $wpdb->prefix . "session";
	$sql = "CREATE TABLE $table_name (
	  id mediumint(9) NOT NULL AUTO_INCREMENT,
		session_id text NOT NULL,
		user_id mediumint(9) NOT NULL,
		ip_address text NOT NULL,
		login_attempt int DEFAULT 0,
		attempt_date DATETIME,
		countrycode text NOT NULL,
		state text NOT NULL,
	  PRIMARY KEY  (id)
	)";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
}

function insertToSessionTable($wpdb, $session_id, $countrycode, $state){

$table_name = $wpdb->prefix . "session";

$wpdb->insert(
	$table_name,
	array(
		'session_id' =>  $session_id,
		'user_ID' => get_current_user_id(),
		'ip_address' => $_SERVER['REMOTE_ADDR'],
		'countrycode' => $countrycode,
		'state' => $state,
	)
);
}

function insertToDB($wpdb, $table, $device, $user_id) {

	$table_name = $wpdb->prefix . $table;
	$push_array = array_merge($device, array("user_id" => $user_id));

	//print_r($push_array);
	$wpdb->insert(
		$table_name,
		$push_array
	);
}
 ?>
