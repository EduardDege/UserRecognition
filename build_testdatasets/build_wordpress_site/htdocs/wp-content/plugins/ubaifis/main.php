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
include (ABS_PATH . "/Helper/ip_range.php");

/*function wpb_confirm_leaving_js() {

     wp_enqueue_script( 'Confirm Leaving', plugins_url( 'Helper/javascript/confirm-leaving.js', __FILE__ ), array('jquery'), '1.0.0', true );
}
add_action('wp_enqueue_scripts', 'wpb_confirm_leaving_js');*/
add_filter( 'authenticate', 'custom_authenticate_username_password', 30, 3);
function custom_authenticate_username_password( $user, $username, $password )
{
    if ( is_a($user, 'WP_User') ) { return $user; }

    if ( empty($username) || empty($password) )
    {
        $error = new WP_Error();
        $user  = new WP_Error('authentication_failed', __('<strong>ERROR</strong>: Invalid username or incorrect password.'));

        return $error;
    }
}

add_action("init", "set_cookie");
add_action("init", "start_session");
add_action('admin_menu', 'test_plugin_setup_menu');
add_action('wp_login_failed', 'checkUser');
add_action('wp_login', "addUserToSessionSuccLogin");
add_action('wp_login', 'saveLastLogin');
add_action('wp_login', 'saveLoginToUserLoginData');
add_action('wp_head', 'onTabClosed');
add_action('clear_auth_cookie', "saveLogoutToUserLoginData");
add_action('wp_logout', "getLogout");

// create custom plugin settings menu
add_action('admin_menu', 'ubaifis_create_menu');
// hook trace user
add_action("template_redirect", "track_user");
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
    // add_action( 'admin_init', 'register_ubaifis_settings' );
}
function track_user(){
    global $wpdb;
    $table = "user_recognition";
    $user_id = get_current_user_id();

    createNewTable($wpdb, $table);
    createSessionTable($wpdb);
    insertToDB($wpdb, $table, getDevice(), $user_id);
    show_cookie();
}

function ubaifis_settings_page(){
  global $wpdb;
  $table = "user_recognition";
  $user_id = get_current_user_id();

  createNewTable($wpdb, $table);
  createSessionTable($wpdb);
  insertToDB($wpdb, $table, getDevice(), $user_id);
  show_cookie();
  createSessionDataTable($wpdb);
  createUserLoginDataTable($wpdb);

  #$ip = $_SE%RVER['REMOTE_ADDR'];
  #echo $ip;
  #echo ip_info("108.171.134.41", "Country");
?>
<div class="wrap">
    <h1>UBAifis</h1>
</div>
<form method="post" action="options.php">
    <?php settings_fields( 'ubaifis' ); ?>
    <?php do_settings_sections( 'ubaifis' ); ?>

    <table class="form-table">
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
