<?php
include "./include/db.php";

// connect to db
//pg_connect($db_host, $db_user, $db_pass) or die(pg_last_error());
//pg_select_db($db_name) or die(pg_last_error());
pg_connect("host=" .$db_host ." port=5432 dbname=" .$db_name ." user=" .$db_user ." password=" .$db_pass);

// if map is in Startup Genome mode, check for new data
if($sg_enabled) {
  require_once("include/http.php");
  include_once("startupgenome_get.php");
}

// input parsing
function parseInput($value) {
  $value = htmlspecialchars($value, ENT_QUOTES);
  $value = str_replace("\r", "", $value);
  $value = str_replace("\n", "", $value);
  return $value;
}



?>