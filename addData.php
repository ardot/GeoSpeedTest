<?php
  session_start();

  $db = mysql_connect("localhost","root", "6PJ3k3gr!");

  if (!$db){
    echo "Could not connect to database" . mysql_error();
    exit();
  }

  $db_name = "speedtest";
  if (!mysql_select_db($db_name, $db)){
    die ("Could not select database") . mysql_error();
  }

  if (!isset($_GET['lat']) ||
      !isset($_GET['long']) ||
      !isset($_GET['up']) ||
      !isset($_GET['down']) ||
      !isset($_GET['time'])) {
    echo "Invalid call to this function";
    exit();
  }

  $lat = $_GET['lat'];
  $long = $_GET['long'];
  $up = $_GET['up'];
  $down = $_GET['down'];
  $time = $_GET['time'];

  $query = "INSERT INTO `speedtest`.`Results` (
        `lat`,
        `long`,
        `time`,
        `up`,
        `down`,
        `id`
      ) VALUES (
        '" .mysql_real_escape_string($lat). "',
        '" .mysql_real_escape_string($long). "',
        '" .mysql_real_escape_string($time). "',
        '" .mysql_real_escape_string($up). "',
        '" .mysql_real_escape_string($down). "',
        NULL
      );";

  print("<h1> $query </h1>");
  mysql_query($query);

?>
