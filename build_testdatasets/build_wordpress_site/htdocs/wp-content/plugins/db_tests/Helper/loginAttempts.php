<?php

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
    echo gettype((int)$login_attempt);
  //  debug_to_console($login_attempt);
  }

  function debug_to_console($data) {
    $output = $data;
    if (is_array($output))
        $output = implode(',', $output);
    echo "<script>console.log('Debug Objects: " . $output . "' );</script>";
}

 ?>
