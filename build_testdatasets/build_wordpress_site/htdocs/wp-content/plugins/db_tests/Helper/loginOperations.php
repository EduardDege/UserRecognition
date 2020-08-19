<?php

include "/getDeviceInfo.php";
//include $_SERVER['DOCUMENT_ROOT']."/getDeviceInfo.php";
include "/ip_range.php";
include "/dbOperations.php";

  function checkUser($username){
    global $wpdb;
    $results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}users");
    foreach($results as $row){
      if($row->user_login == $username){
        pushFailedAttempt($wpdb, $row->ID);
        addUserToSessionFailedLogin($wpdb, $row->ID);
      }
    }
  }

  function pushFailedAttempt($wpdb, $id){
    $login_attempt = $wpdb->get_var("SELECT login_attempt FROM {$wpdb->prefix}user_recognition WHERE user_id=$id");
    //$wpdb->update("wp_user_recognition", array("lo"));
    $wpdb->update("{$wpdb->prefix}user_recognition", array("login_attempt"=>(int)$login_attempt + 1), array("user_id"=>$id));
  //  echo gettype((int)$login_attempt);
  //  debug_to_console($login_attempt);
  }

  function saveLastLogin(){
    global $wpdb;
    $push_array = array_merge(getDevice(), array("user_id" => get_current_user_id(),
        "login_date" => date('Y-m-d H:i:s'), "loginstatus" => true));
    $wpdb->insert("{$wpdb->prefix}user_recognition", $push_array);

  }

  function getLogout(){
    global $wpdb;
    $user_id = get_current_user_id();
    $login_date = $wpdb->get_var("SELECT login_date FROM {$wpdb->prefix}user_recognition WHERE user_id = $user_id AND loginstatus = 1");
    $wpdb->update("{$wpdb->prefix}user_recognition",
        array("duration" => strtotime(date('Y-m-d H:i:s')) - strtotime($login_date),
            "logout_date" => date('Y-m-d H:i:s'), "loginstatus" => 0),
        array("user_id"=>$user_id, "loginstatus"=>1));
    //$wpdb->update("{$wpdb->prefix}user_recognition", array("duration"=> strtotime(date('Y-m-d H:i:s')) - strtotime($login_date), "logout_date" => date("Y-m-d H:i:s"), "loginstatus" => 0), array("user_id"=>$id));
    end_session();
  }

  //Tracking Sessions

  function start_session($id){
    global $wpdb;
    if(!session_id()){
      session_start();
      insertToSessionTable($wpdb, session_id(), ip_info($_SERVER['REMOTE_ADDR'],"countrycode"),
          ip_info($_SERVER['REMOTE_ADDR'], "state"));
    }
  }

  function addUserToSessionFailedLogin($wpdb, $id){
    $session_id = session_id();
    $user_id = get_current_user_id();
    $login_attempt = $wpdb->get_var("SELECT MAX(login_attempt) FROM {$wpdb->prefix}session WHERE user_id=$id AND attempt_date=(SELECT Max(attempt_date) FROM {$wpdb->prefix}session WHERE user_id=$id)");
    //$wpdb->update("{$wpdb->prefix}session", array("login_attempt"=>(int)$login_attempt + 1, "user_id" => $id), array("session_id"=>$session_id));
    $wpdb->insert("{$wpdb->prefix}session", array("session_id" => $session_id, "user_id" => $user_id,
     "login_attempt"=>(int)$login_attempt + 1, "attempt_date" => date('Y-m-d H:i:s'),
        "ip_address"=>$_SERVER["REMOTE_ADDR"], "countrycode" => ip_info($_SERVER['REMOTE_ADDR'],"countrycode"),
        "state"=>ip_info($_SERVER['REMOTE_ADDR'], "state")));
  }

  function addUserToSessionSuccLogin($username){
    global $wpdb;
    $results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}users");
    foreach($results as $row){
      if($row->user_login == $username){
        $user_id = $row->ID;
      }
    }
    $session_id = session_id();
    $push_array = array("user_id" => $user_id, "session_id" => $session_id, "login_attempt" => 0,
        "attempt_date" => date('Y-m-d H:i:s'),"ip_address"=>$_SERVER["REMOTE_ADDR"],
        "countrycode" => ip_info($_SERVER['REMOTE_ADDR'],"countrycode"),
        "state"=>ip_info($_SERVER['REMOTE_ADDR'], "state"));
    $wpdb->insert("{$wpdb->prefix}session", $push_array);
  }

  function end_session(){
    session_regenerate_id(true);
    session_destroy();

  }

  function debug_to_console($data) {
    $output = $data;
    if (is_array($output))
        $output = implode(',', $output);
    echo "<script>console.log('Debug Objects: " . $output . "' );</script>";
}
 ?>