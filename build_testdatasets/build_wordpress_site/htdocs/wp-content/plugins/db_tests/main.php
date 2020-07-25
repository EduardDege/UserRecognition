<?php
/**
 * Plugin Name: TESTs
 * Plugin URI: http://www.mywebsite.com/my-first-plugin
 * Description: The very first plugin that I have ever created.
 * Version: 1.0
 * Author: Your Name
 * Author URI: http://www.mywebsite.com
 */
define("ABS_PATH", dirname(__FILE__));

include (ABS_PATH . "/Helper/getDeviceInfo.php");
include (ABS_PATH . "/Helper/dbOperations.php");
include (ABS_PATH . "/Helper/loginAttempts.php");

 add_action('admin_menu', 'test_plugin_setup_menu');
 add_action('wp_login_failed', 'checkUser');

function test_plugin_setup_menu(){
        add_menu_page( 'Test Plugin Page', 'Test Plugin', 'manage_options', 'test-plugin', 'test_init' );
}

function test_init(){
        //echo 'User IP - '.$_SERVER['REMOTE_ADDR'];
        global $wpdb;
        $table = "user_recognition";
        $results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}users");
        $user_data = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}fa_user_logins");
        $user_id = get_current_user_id();


        echo get_current_user_id();

        createNewTable($wpdb, $table);
        //insertToDB($wpdb, $table, getDevice(), $user_id);

}

?>
