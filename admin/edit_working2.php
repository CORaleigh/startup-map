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
 $title = str_replace( "'", "\\'", str_replace( "\\", "\\\\", $_POST['title'] ) );
  $type = $_POST['type'];
  $address = str_replace( "'", "\\'", str_replace( "\\", "\\\\", $_POST['address'] ) );
  $uri = $_POST['uri'];
  
  pg_query("UPDATE places SET title='$title', type='$type', address='$address', uri='$uri' WHERE id='$place_id' LIMIT 1") or die(pg_last_error());
  
  // geocode
  //$hide_geocode_output = true;
  //include "../geocode.php";
  
  header("Location: index.php?view=$view&search=$search&p=$p");
  exit;
}
?>



<? echo $admin_head; ?>

<form id="admin" class="form-horizontal" action="edit.php" method="post">
  <h1>
    Edit Place
  </h1>
  <fieldset>
    <div class="control-group">
      <label class="control-label" for="">Title</label>
      <div class="controls">
        <input type="text" class="input input-xlarge" name="title" value="<?=$place[title]?>" id="">
      </div>
    </div>
    <div class="control-group">
      <label class="control-label" for="">Type</label>
      <div class="controls">
        <select class="input input-xlarge" name="type">

              Array('#e418ac', 'Professional Services'),
              Array('#bb25e2','Tech'),
              Array('#3d57de', 'Life Science'), 
              Array('#49a8dd', 'Industrial'),
              Array('#54dbcb', 'Creative'),
              Array('#60d991', 'Cultural and Educational'),
              Array('#73d76b', 'Food and Retail'),
              Array('#abd576', 'Innovation Spaces'),
              Array('#d4d181', 'Institutional and Non-Profit'),
              Array('#d49779', 'Other')

          <option<? if($place[type] == "Professional Services") {?> selected="selected"<? } ?>>Professional Services</option>
          <option<? if($place[type] == "Tech") {?> selected="selected"<? } ?>>Tech</option>
          <option<? if($place[type] == "Life Science") {?> selected="selected"<? } ?>>Life Science</option>
          <option<? if($place[type] == "Industrial") {?> selected="selected"<? } ?>>Industrial</option>
          <option<? if($place[type] == "Creative") {?> selected="selected"<? } ?>>Creative</option>
          <option<? if($place[type] == "Cultural and Educational") {?> selected="selected"<? } ?>>Cultural and Educational</option>
          <option<? if($place[type] == "Food and Retail") {?> selected="selected"<? } ?>>Food and Retail</option>
          <option<? if($place[type] == "Innovation Spaces") {?> selected="selected"<? } ?>>Innovation Spaces</option>
          <option<? if($place[type] == "Institutional and Non-Profit") {?> selected="selected"<? } ?>>Institutional and Non-Profit</option>
          <option<? if($place[type] == "Other") {?> selected="selected"<? } ?>>Other</option>
        </select>
      </div>
    </div>
    <div class="control-group">
      <label class="control-label" for="">Address</label>
      <div class="controls">
        <input type="text" class="input input-xlarge" name="address" value="<?=$place[address]?>" id="">
      </div>
    </div>
    <div class="control-group">
      <label class="control-label" for="">URL</label>
      <div class="controls">
        <input type="text" class="input input-xlarge" name="uri" value="<?=$place[uri]?>" id="">
      </div>
    </div>
    <div class="control-group">
      <label class="control-label" for="">Description</label>
      <div class="controls">
        <textarea class="input input-xlarge" name="description"><?=$place[description]?></textarea>
      </div>
    </div>
    <div class="control-group">
      <label class="control-label" for="">Submitter Name</label>
      <div class="controls">
        <input type="text" class="input input-xlarge" name="owner_name" value="<?=$place[owner_name]?>" id="">
      </div>
    </div>
    <div class="control-group">
      <label class="control-label" for="">Submitter Email</label>
      <div class="controls">
        <input type="text" class="input input-xlarge" name="owner_email" value="<?=$place[owner_email]?>" id="">
      </div>
    </div>
    <div class="control-group">
      <label class="control-label" for="">Number of Employees</label>
      <div class="controls">
        <input type="text" class="input input-large" name="numemployees" value="<?=$place[numemployees]?>" id="">
      </div>
    </div>
    <div class="control-group">
      <label class="control-label" for="">Location</label>
      <div class="controls">
        <input type="hidden" name="lat" id="mylat" value="<?=$place[lat]?>"/>
        <input type="hidden" name="lng" id="mylng" value="<?=$place[lng]?>"/>
        <div id="map" style="width:80%;height:300px;">
        </div>
        <script type="text/javascript">
          var map = new google.maps.Map( document.getElementById('map'), {
            zoom: 17,
            center: new google.maps.LatLng( <?=$place[lat]?>, <?=$place[lng]?> ),
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            streetViewControl: false,
            mapTypeControl: false
          });
          var marker = new google.maps.Marker({
            position: new google.maps.LatLng( <?=$place[lat]?>, <?=$place[lng]?> ),
            map: map,
            draggable: true
          });
          google.maps.event.addListener(marker, 'dragend', function(e){
            document.getElementById('mylat').value = e.latLng.lat().toFixed(6);
            document.getElementById('mylng').value = e.latLng.lng().toFixed(6);
          });
        </script>
      </div>
    </div>    
    <div class="form-actions">
      <button type="submit" class="btn btn-primary">Save Changes</button>
      <input type="hidden" name="task" value="doedit" />
      <input type="hidden" name="place_id" value="<?=$place[id]?>" />
      <input type="hidden" name="view" value="<?=$view?>" />
      <input type="hidden" name="search" value="<?=$search?>" />
      <input type="hidden" name="p" value="<?=$p?>" />
      <a href="index.php" class="btn" style="float: right;">Cancel</a>
    </div>
  </fieldset>
</form>



<? echo $admin_foot; ?>