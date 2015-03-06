<?php
include_once "header.php";

// This is used to submit new jobs for review.
// Jobs won't appear on the map until they are approved.
//$owner_name = parseInput($_POST['owner_name'];
//$owner_email = parseInput($_POST['owner_email'];
$byid = parseInput($_POST['id']) * 1;
$hirelink = parseInput($_POST['hirelink']);
$hiredate = parseInput($_POST['hiredate']) * 1;
// insert into db, wait for approval
//if(empty($owner_name) || empty($owner_email) || empty($hirelink) || empty($hiredate)) {
if(empty($hirelink) || empty($hiredate)) {
  echo "Please provide required information\n";
  exit;

} else {
	//print_r("'$hiredate'");
    //$update = pg_query("UPDATE places SET hiring=1,hirelink='$hirelink',hiredate='$hiredate' WHERE id=$byid") or die(pg_last_error());
    $update = pg_query("UPDATE places SET hiring=1,hirelink='$hirelink',hiredate='$hiredate' WHERE id=$byid") or die(pg_last_error());
    // remove old hirings
    $passed = time() * 1000;
    $update = pg_query("UPDATE places SET hiring=0,hirelink='',hiredate='' WHERE hiredate<'$passed'") or die(pg_last_error());
    //$message = "this job has been submitted";
    echo "success";
    exit;
}

?>
