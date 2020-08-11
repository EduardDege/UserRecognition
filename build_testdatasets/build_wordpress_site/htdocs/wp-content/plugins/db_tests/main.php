<?php

/*
Plugin Name: User Behavior Analysics if(is)
Plugin URI: https://www.internet-sicherheit.de/
Description: This Plugin combine Machine Learning & User and Entity Behavioral Analytics to detect new threats inside a organization
Author: Armel Wonga & Eduard Dege
Version: 1.0.0
Author URI:
License: GPL2
*/

define("ABS_PATH", dirname(__FILE__));

include (ABS_PATH . "/Helper/getDeviceInfo.php");
include (ABS_PATH . "/Helper/dbOperations.php");
include (ABS_PATH . "/Helper/loginOperations.php");
include (ABS_PATH . "/Helper/cookie.php");


add_action("init", "set_cookie");
add_action("init", "start_session");
add_action('admin_menu', 'test_plugin_setup_menu');
add_action('wp_login_failed', 'checkUser');
add_action('wp_login', "addUserToSessionSuccLogin");
add_action('wp_login', 'saveLastLogin');

add_action('wp_logout', "getLogout");
// create custom plugin settings menu
add_action('admin_menu', 'ubaifis_create_menu');

/**function test_plugin_setup_menu(){
        add_menu_page( 'Test Plugin Page', 'Test Plugin', 'manage_options', 'test-plugin', 'test_init' );
}*/

/**function test_init(){
        global $wpdb;
        $table = "user_recognition";
        $user_id = get_current_user_id();

        createNewTable($wpdb, $table);
        insertToDB($wpdb, $table, getDevice(), $user_id);
        show_cookie();
}*/

function ubaifis_create_menu() {
// define admin page in Back-End
    //create new top-level menu
    add_menu_page('UBAifis Einstellungen', 'UBAifis', 'administrator',
        __FILE__, 'ubaifis_settings_page' , "dashicons-performance" , 65 );

    //https://developer.wordpress.org/resource/dashicons/#search

    //call register settings function
    add_action( 'admin_init', 'register_ubaifis_settings' );
}

function ubaifis_settings_page(){
  global $wpdb;
  $table = "user_recognition";
  $user_id = get_current_user_id();

  createNewTable($wpdb, $table);
  createSessionTable($wpdb);
  insertToDB($wpdb, $table, getDevice(), $user_id);
  show_cookie();
?>
<div class="wrap">
    <h1>UBAifis</h1>
</div>
<form method="post" action="options.php">
    <?php settings_fields( 'ubaifis' ); ?>
    <?php do_settings_sections( 'ubaifis' ); ?>
    <table class="form-table">
        <tr valign="top">
            <th scope="row">An oder aus?</th>
            <td><input type="checkbox" name="onoff" value="1"  <?php echo ( get_option("onoff") == 1 ) ? "checked" : ""; ?> /></t>
        </tr>

        <tr valign="top">
            <th scope="row">Inhalt der Datenbanktabelle</th>
        </tr>

        <?php
      //  echo $_SERVER['HTTP_USER_AGENT'];
        /**function getUserIpAddr(){
            if(!empty($_SERVER['HTTP_CLIENT_IP'])){
                //ip from share internet
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            }elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
                //ip pass from proxy
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            }else{
                $ip = $_SERVER['REMOTE_ADDR'];
            }
            return $ip;
        }*/

        //echo 'User Real IP - '.getUserIpAddr();
        $results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}user_recognition", OBJECT);

        echo "<tr>";
        foreach($results[0] AS $key=>$value){
            echo "<th>$key</th>";
        }
        echo "</tr>";

        foreach($results AS $result){
            echo "<tr>";
            foreach($result AS $key=>$value){
                echo "<td>$value</td>";
            }
            echo "</tr>";
        }
        ?>
    </table>
</form>
<?php
}


?>
