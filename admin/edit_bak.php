<?php
include "header.php";


if(isset($_GET['place_id'])) {
  $place_id = htmlspecialchars($_GET['place_id']); 
} else if(isset($_POST['place_id'])) {
  $place_id = htmlspecialchars($_POST['place_id']);
} else {
  exit; 
}

// get place info

$place_query = pg_query("SELECT * FROM places WHERE id='$place_id' LIMIT 1");
if(pg_num_rows($place_query) != 1) { exit; }
$place = pg_fetch_assoc($place_query);

// do place edit if requested
if($task == "doedit") {
  echo $place["address"];
  $title = str_replace( "'", "\\'", str_replace( "\\", "\\\\", $_POST['title'] ) );
  $type = $_POST['type'];
  $address = str_replace( "'", "\\'", str_replace( "\\", "\\\\", $_POST['address'] ) );
  $uri = $_POST['uri'];
  //$description = str_replace( "'", "\\'", str_replace( "\\", "\\\\", $_POST['description'] ) );
  //$owner_name = str_replace( "'", "\\'", str_replace( "\\", "\\\\", $_POST['owner_name'] ) );
  //$owner_email = $_POST['owner_email'];
  //$lat = (float) $_POST['lat'];
  //$lng = (float) $_POST['lng'];
  
  pg_query("UPDATE places SET title='$title', type='$type', address='$address', uri='$uri', lat='$lat', lng='$lng', description='$description', owner_name='$owner_name', owner_email='$owner_email' WHERE id='$place_id' LIMIT 1") or die(pg_last_error());
  
  // geocode
  //$hide_geocode_output = true;
  //include "../geocode.php";
  
  header("Location: index.php?view $view&search $search&p $p");
  exit;
}

?>



<?php echo $admin_head;?>

<form id="admin" class="form-horizontal" action="edit.php" method="post">
  <h1>
    Edit Place
  </h1>
  <fieldset>
    <div class="control-group">
      <label class="control-label" for="">Title</label>
      <div class="controls">
        <input type="text" class="input input-xlarge" name="title" value="<?php echo $place[title];?>" id="">
      </div>
    </div>
    <div class="control-group">
      <label class="control-label" for="">Type</label>
      <div class="controls">
        <select class="input input-xlarge" name="type">
          <option<?php if($place[type] == "Technology") {?> selected="selected"<?php } ?>>Technology</option>
          <option<?php if($place[type] == "Design-Media") {?> selected="selected"<?php } ?>>Design-Media</option>
          <option<?php if($place[type] == "Life Science") {?> selected="selected"<?php } ?>>Life Science</option>
          <option<?php if($place[type] == "Consumer Products") {?> selected="selected"<?php } ?>>Consumer Products</option>
          <option<?php if($place[type] == "Misc") {?> selected="selected"<?php } ?>>Misc</option>
        </select>
      </div>
    </div>
    <div class="control-group">
      <label class="control-label" for="">Address</label>
      <div class="controls">
        <input type="text" class="input input-xlarge" name="address" value="<?php echo $place[address]?>" id="">
      </div>
    </div>
    <div class="control-group">
      <label class="control-label" for="">URL</label>
      <div class="controls">
        <input type="text" class="input input-xlarge" name="uri" value="<?php echo $place[uri]?>" id="">
      </div>
    </div>
    <div class="control-group">
      <label class="control-label" for="">Description</label>
      <div class="controls">
        <textarea class="input input-xlarge" name="description"><?php echo $place[description]?></textarea>
      </div>
    </div>
    <div class="control-group">
      <label class="control-label" for="">Submitter Name</label>
      <div class="controls">
        <input type="text" class="input input-xlarge" name="owner_name" value="<?php echo $place[owner_name]?>" id="">
      </div>
    </div>
    <div class="control-group">
      <label class="control-label" for="">Submitter Email</label>
      <div class="controls">
        <input type="text" class="input input-xlarge" name="owner_email" value="<?php echo $place[owner_email]?>" id="">
      </div>
    </div>
    <div class="control-group">
      <label class="control-label" for="">Location</label>
      <div class="controls">
        <input type="hidden" name="lat" id="mylat" value="<?php $place[lat]?>"/>
        <input type="hidden" name="lng" id="mylng" value="<?php $place[lng]?>"/>
        <div id="map" style="width:80%;height:300px;">
        </div>
        <!--<script type="text/javascript">
          var map = new google.maps.Map( document.getElementById('map'), {
            zoom: 17,
            center: new google.maps.LatLng( <?php $place[lat]?>, <?php $place[lng]?> ),
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            streetViewControl: false,
            mapTypeControl: false
          });
          var marker = new google.maps.Marker({
            position: new google.maps.LatLng( <?php $place[lat]?>, <?php $place[lng]?> ),
            map: map,
            draggable: true
          });
          google.maps.event.addListener(marker, 'dragend', function(e){
            document.getElementById('mylat').value = e.latLng.lat().toFixed(6);
            document.getElementById('mylng').value = e.latLng.lng().toFixed(6);
          });
        </script>-->
      </div>
    </div>    
    <div class="form-actions">
      <button type="submit" class="btn btn-primary">Save Changes</button>
      <input type="hidden" name="task" value="doedit" />
      <input type="hidden" name="place_id" value="<?php $place[id]?>" />
      <input type="hidden" name="view" value="<?php $view?>" />
      <input type="hidden" name="search" value="<?php $search?>" />
      <input type="hidden" name="p" value="<?php $p?>" />
      <a href="index.php" class="btn" style="float: right;">Cancel</a>
    </div>
  </fieldset>
</form>



<?php echo $admin_foot; ?>
