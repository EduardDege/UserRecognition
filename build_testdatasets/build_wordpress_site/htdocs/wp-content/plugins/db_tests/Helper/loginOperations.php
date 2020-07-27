<?php

include "/getDeviceInfo.php";

  function checkUser($username){
    global $wpdb;
    $results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}users");
    foreach($results as $row){
      if($row->user_login == $username){
        pushFailedAttempt($wpdb, $row->ID);
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
    $push_array = array_merge(getDevice(), array("user_id" => get_current_user_id(), "login_date" => date('Y-m-d H:i:s'), "loginstatus" => true));
    $wpdb->insert("{$wpdb->prefix}user_recognition", $push_array);
  }

  function getLogout(){
    global$wpdb;
    $user_id = get_current_user_id();
    $login_date = $wpdb->get_var("SELECT login_date FROM {$wpdb->prefix}user_recognition WHERE user_id = $user_id AND loginstatus = 1");
    $wpdb->update("{$wpdb->prefix}user_recognition", array("duration" => strtotime(date('Y-m-d H:i:s')) - strtotime($login_date), "logout_date" => date('Y-m-d H:i:s'), "loginstatus" => 0), array("user_id"=>$user_id, "loginstatus"=>1));
    //$wpdb->update("{$wpdb->prefix}user_recognition", array("duration"=> strtotime(date('Y-m-d H:i:s')) - strtotime($login_date), "logout_date" => date("Y-m-d H:i:s"), "loginstatus" => 0), array("user_id"=>$id));
  }


  function debug_to_console($data) {
    $output = $data;
    if (is_array($output))
        $output = implode(',', $output);
    echo "<script>console.log('Debug Objects: " . $output . "' );</script>";
}

 ?>
