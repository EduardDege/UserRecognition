<?php
/**
 * Plugin Name: Some test
 * Plugin URI: http://www.mywebsite.com/my-first-plugin
 * Description: The very first plugin that I have ever created.
 * Version: 1.0
 * Author: Your Name
 * Author URI: http://www.mywebsite.com
 */

 add_action('admin_menu', 'test_plugin_setup_menu');

global $wpdb;
$table_name = $wpdb->prefix.'users';

$DBP_results = $wpdb->get_results("SELECT * FROM $wpdb->users");

function test_plugin_setup_menu(){
        add_menu_page( 'Test Plugin Page', 'Test Plugin', 'manage_options', 'test-plugin', 'test_init' );
}

function test_init(){
        echo "<div>
        <div >HEADER SETTING</div>
        <table >
          <tr>
            <th>ID</th>
            <th>LOGO</th>
            <th>LOGO Image</th>
          </tr>";
         echo $DBP_results[0];

}

?>
