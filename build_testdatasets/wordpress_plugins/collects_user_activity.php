<?php
//Logging User Activity

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
?>
