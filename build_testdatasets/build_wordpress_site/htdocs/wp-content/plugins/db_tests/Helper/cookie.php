<?php

define("ABS_PATH", dirname(__FILE__));

include (ABS_PATH . "/php_class/time_online.class.php");

function show_cookie(){

//  $test = new time_online;

  $lastvisit = $_COOKIE['wpb_visit_time'];

  $string .= 'You last visited our website '. $lastvisit .'. Check out whats new';

  //echo $string;
  //echo time() - strtotime($_COOKIE['wpb_visit_time']);
  //echo date("d-m-Y", time() - strtotime($_COOKIE['wpb_visit_time']));

}

function set_cookie() {
$visit_time = date('F j, Y g:i a');

if(isset($_COOKIE['wpb_visit_time'])) {

  cookie_exist($visit_time);

} else {

  create_cookie($visit_time);

}
}
//some placeholder from a tutorial
function cookie_exist($visit_time){
  $lastvisit = $_COOKIE['wpb_visit_time'];

  $string .= 'You last visited our website '. $lastvisit .'. Check out whats new';

  return $string;
}

function create_cookie($visit_time){
  setcookie('wpb_visit_time',  $visit_time, 0);
}

function calculateTimeSpend($time){

}

 ?>
