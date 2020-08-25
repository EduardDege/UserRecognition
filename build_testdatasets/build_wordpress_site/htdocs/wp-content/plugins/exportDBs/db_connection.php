<?php

function OpenCon()
{
    $dbhost = "localhost";
    $dbuser = "root";
    $dbpass = "1EDFgCoNXBKXrv0Ebe0G";
    $db = "tmudb";

    $conn = new mysqli($dbhost, $dbuser, $dbpass,$db) or die("Connect failed: %s\n". $conn -> error);

    return $conn;
}
function CloseCon($conn)
{
    $conn -> close();
}
?>