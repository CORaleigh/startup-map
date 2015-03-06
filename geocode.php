<?php
include_once "header.php";

// Run this script after new markers have been added to the DB.
// It will look for any markers that are missing latlong values
// and automatically geocode them.

// google maps vars
define("MAPS_HOST", "maps.googleapis.com");

// geocode all markers
geocode("places");
geocode("events");




// geocode function
function geocode($table) {

  global $hide_geocode_output;
  // get places that don't have latlong values
  $result = pg_query("SELECT * FROM $table WHERE lat=0 OR lng=0") or die(pg_last_error());

  // geocode and save them back to the db
  $delay = 0;
  $base_url = "http://" . MAPS_HOST . "/maps/api/geocode/xml";

  // Iterate through the rows, geocoding each address
  while ($row = @pg_fetch_assoc($result)) {
    $geocode_pending = true;
    //echo $row[address];
    while ($geocode_pending) {

      $address = $row["address"];
      $id = $row["id"];
      
      $request_url = $base_url . "?address=" . urlencode($address) . "&sensor=false";
      
      $xml = simplexml_load_file($request_url) or die("url not loading");
      
      $status = $xml->status;
      if ($status == "OK") {
        // Successful geocode
        $geocode_pending = false;
        $coordinates = $xml->result->geometry->location;
        // Format: Longitude, Latitude, Altitude
        $lat = $coordinates->lat;
        $lng = $coordinates->lng;

/*        $query = sprintf("UPDATE $table " .
              " SET lat = '%s', lng = '%s' " .
              " WHERE id = '%s' LIMIT 1;",*/
        $query = sprintf("UPDATE $table " .
              " SET lat = '%s', lng = '%s' " .
              " WHERE id = '%s';",              
              pg_escape_string($lat),
              pg_escape_string($lng),
              pg_escape_string($id));
        $update_result = pg_query($query);

        if (!$update_result) {
          die("Invalid query: " . pg_last_error());
        }
      } else if (strcmp($status, "620") == 0) {
        // sent geocodes too fast
        $delay += 100000;
      } else {
        // failure to geocode
        $geocode_pending = false;
        //echo "Address " . $address . " failed to geocoded. ";
        //echo "Received status " . $status . " \n";
      }
      usleep($delay);
    }
  }

  // finish
  if(@$hide_geocode_output != true) {
    echo pg_num_rows($result)." $table geocoded<br />";
  }

}

?>
