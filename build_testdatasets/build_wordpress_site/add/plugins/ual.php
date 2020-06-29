<?php

//Logging User Activity
add_action( 'phpmailer_init', 'microtime_float' );
function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((int)$usec + (int)$sec);
}

$filename = $config->urls->template."activity/".$user.".txt";

if (file_exists($filename)) {

   $handle = fopen($filename, 'a') or die('Cannot open file:  '.$filename); //implicitly creates file
   $new_data = "\nPage Name: {$page->url} \nTime Stamp: ".date('d/M/Y h:i:s A')."\nUser Agent: ".$_SERVER['HTTP_USER_AGENT']."\nIP Address: ".$_SERVER['REMOTE_ADDR']."\n============================";
   fwrite($handle, $new_data.PHP_EOL);

} else {

    $log_file = "activity/".$user.".txt";
    $handle = fopen($log_file, 'w') or die('Cannot open file:  '.$log_file); //implicitly creates file
    $data = "UserName = {$user->u_fullname}\nPage Name: {$page->url} \nTime Stamp: ".date('d/M/Y h:i:s A')."\nUser Agent: ".$_SERVER['HTTP_USER_AGENT']."\nIP Address: ".$_SERVER['REMOTE_ADDR']."\n============================";
    fwrite($handle, $data);
}

fclose($handle);

//End of Logging Code

$(document).ready(function() {
	$(document).click(function(e){
      log_click(window.location.href.toString().split(window.location.host)[1], e.pageX, e.pageY);
   });
	var canvas = document.getElementsByTagName('canvas')[0];
	canvas.style.display = "none";   
});

function log_click(page, x, y){ // log clicks for heatmap
	$.post("/log_click.php", {
		page: page, 
		x_coord : x,
		y_coord: y
	}, function(data){
		if (data == 1){ 
			console.log("Click logged: " + x + ", " + y);

	} else{
      console.log("Error - click not logged " + x + ", " + y);
   }
  }) 
}
?>