<?php
/**
 * Plugin Name: Get DB data
 * Plugin URI: http://www.mywebsite.com/my-first-plugin
 * Description: The very first plugin that I have ever created.
 * Version: 1.0
 * Author: Your Name
 * Author URI: http://www.mywebsite.com
 */
 function debug_to_console($data){
   $output = $data;
    if (is_array($output))
        $output = implode(',', $output);

    echo "<script>console.log('Debug Objects: " . $output . "' );</script>";
 }

 function exportDB(){
   $servername = "localhost:8801";
   $username = "wordpress";
   $password = "wordpress";
   $dbname = "wordpress";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT id, user_nicename, user_email FROM wp_users";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
    debug_to_console("id: " . $row["id"]. " - Name: " . $row["user_nicename"]. " " . $row["user_email"]. "<br>");
  }
} else {
  echo "0 results";
}
$conn->close();
 }

 add_action("init", "exportDB")
