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
//$myrows = $wpdb->get_results( "SELECT id FROM wp_users" );
//$table_name = $wpdb->prefix.'users';

//$DBP_results = $wpdb->get_results("SELECT * FROM $wpdb->users");

function test_plugin_setup_menu(){
        add_menu_page( 'Test Plugin Page', 'Test Plugin', 'manage_options', 'test-plugin', 'test_init' );
}

function test_init(){
        //echo 'User IP - '.$_SERVER['REMOTE_ADDR'];
        global $wpdb;
        $results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}users");
        $user_data = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}fa_user_logins");
        echo "<table border='1'>
            <th>USER-ID</th>
            <th>Nickname</th>";
            foreach($results as $rows){
              echo "<tr>
              <td>$rows->ID</td>
              <td>$rows->user_nicename</td>
              </tr>";
            }
            echo "</table>";

        echo "<table border='1'>
            <th>USER-ID</th>
            <th>Letzer Login</th>
            <th>IP</th>
            <th>Browser</th>
            <th>Browser Version</th>
            <th>OS</th>
            <th>User Agent</th>";
            foreach($user_data as $rows){
              echo "<tr>
              <td>$rows->user_id</td>
              <td>$rows->time_last_seen</td>
              <td>$rows->ip_address</td>
              <td>$rows->browser</td>
              <td>$rows->browser_version</td>
              <td>$rows->operating_system</td>
              <td>$rows->user_agent</td>
              </tr>";
            }
            echo "</table>";
            //print_r($user_data);
/*        echo "<div>
        <div >HEADER SETTING</div>
        <table >
          <tr>
            <th>ID</th>
            <th>NICENAME</th>
            <th>EMAIL</th>
          </tr>
          <?php
          foreach($results as $row){
          <tr>
            <td>$row->ID</td>
            <td>$row->user_nicename</td>
            <td>$row->user_email</td>
          </tr>
        }
          ?>
          ";

          echo $results[0]->ID;
         //print_r($results);
        // echo $results[0];
         //echo $myrows;
         //echo $_SERVER['HTTP_USER_AGENT'];
         if(!$results){
           echo "im empty";
         } else {
           //print_r($results);
         }
         foreach($results as $row){
           echo $row->user_nicename;
         }
*/
}

?>
