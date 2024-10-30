<?php
/*
 Plugin Name: LH Places
 Plugin URI: https://lhero.org/plugins/lh-plaves/
 Description: Adds a places custom post type
 Author: Peter Shaw
 Author URI: https://shawfactor.com
 Version: 1.02
 License: GPL v3 (http://www.gnu.org/licenses/gpl.html)
*/

class LH_Places_plugin {

var $opt_name = 'lh_places-options';
var $hidden_field_name = 'lh_places-submit_hidden';
var $posttype = 'lh-place';
var $namespace = 'lh_places';

var $filename;
var $options;




private function get_connection_type_by_id($id){

global $wpdb;

$sql = "SELECT p2p_type FROM ".$wpdb->prefix."p2p WHERE p2p_id = '" .$id. "'";

$type = $wpdb->get_var($sql);

return $type;


}


private function get_connection_from_by_id($id){

global $wpdb;

$sql = "SELECT p2p_from FROM ".$wpdb->prefix."p2p WHERE p2p_id = '" .$id. "'";

$from = $wpdb->get_var($sql);

return $from;

}

private function curpageurl() {
	$pageURL = 'http';

	if ((isset($_SERVER["HTTPS"])) && ($_SERVER["HTTPS"] == "on")){
		$pageURL .= "s";
}

	$pageURL .= "://";

	if (($_SERVER["SERVER_PORT"] != "80") and ($_SERVER["SERVER_PORT"] != "443")){
		$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];

	} else {
		$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];

}

	return $pageURL;
}


private function register_places_post_type() {

$labels = array(
    'name' => 'Place',
      'singular_name' => 'Place',
      'menu_name' => 'Places',
      'add_new' => 'Add New',
      'add_new_item' => 'Add New Place',
      'edit' => 'Edit place',
      'edit_item' => 'Edit Place',
      'new_item' => 'New Place',
      'view' => 'View Place',
      'view_item' => 'View Place',
      'search_items' => 'Search Places',
      'not_found' => 'No places Found',
      'not_found_in_trash' => 'No Places Found in Trash',
      'parent' => 'Parent Place',);

    register_post_type($this->posttype, array(
        'label' => 'Places',
	'menu_icon'  => 'dashicons-location',
        'description' => '',
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'hierarchical' => true,
        'rewrite' => array('slug' => 'place'),
        'query_var' => true,
        'supports' =>  array( 'title', 'editor', 'author', 'thumbnail','page-attributes'),
	'has_archive' => "places",
        'labels' => $labels,
        )
    );



}


public function geo_options(){
global $post;

$custom = get_post_custom($post->ID);


if (!$custom["geo_latitude"][0]){

$custom["geo_latitude"][0] = "0";

}

if (!$custom["geo_longitude"][0]){

$custom["geo_longitude"][0] = "0";

}


?>


<div id="map_canvas" style="width: 100%; height: 300px"></div>
<div id="geo_options">
<label>Located near this latitude</label>
<br/>
<input name="geo_latitude" id="geo_latitude" value="<?php echo $custom["geo_latitude"][0]; ?>" />
<br/>
<label>Located near this longitude</label>
<br/>
<input name="geo_longitude" id="geo_longitude" value="<?php echo $custom["geo_longitude"][0]; ?>" />
<br/>
<label for="geo_address"><?php _e( "Human-Readable Address (Optional)", 'lh-geo-coordinate' ); ?></label>
<br />
<input type="text" name="geo_address" id="geo_address" value="<?php echo $custom["geo_address"][0]; ?>" size="70" />
<br/>
<button type="button" onclick="getLocation();return false;">Retrieve Location</button
</div>

<div id="panel">
      <div id="panel-content">
        <div id="panel-title">Simple GeoJSON Editor</div>
        <hr/>
        <div id="geojson-controls">
          <button onclick="document.getElementById('geojson-input').select();">Select All</button>
          <a id="download-link" href="data:;base64," download="geojson.json"><button>Download</button></a>
        </div>
        <textarea id="geojson-input"  name="lh_places-place-geojson"
            placeholder="Drag and drop GeoJSON onto the map or paste it here to begin editing."><?php echo get_post_meta($post->ID, $this->namespace."-place-geojson", true ); ?></textarea>
      </div>
    </div>

    <div id="map-container">
      <div id="map-holder"></div>

      <div id="drop-container">
        <div id="drop-silhouette"></div>
      </div>
    </div>


<script type="text/javascript">

function getLocation(){
  		if (navigator.geolocation)
			{
		      navigator.geolocation.getCurrentPosition(showPosition);
	   }
  		else{alert("Geolocation is not supported by this browser.");}
  }
function showPosition(position){
	document.getElementById("geo_latitude").value = position.coords.latitude;
     	document.getElementById("geo_longitude").value = position.coords.longitude;
document.getElementById("geo_address").value = json.address.road + ',' + json.address.city;

  }


function initialize() {
var myLatlng = new google.maps.LatLng(<?php echo $custom["geo_latitude"][0]; ?>, <?php echo $custom["geo_longitude"][0]; ?>);
    var myOptions = {
      zoom: 12,
      center: myLatlng,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    }
    var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);

start_marker = new google.maps.Marker({
			    			                    position: myLatlng,
title: 'Drag Me',
map: map,
draggable: true
            });

google.maps.event.addListener(start_marker, "dragend", function() {

start_marker.point = start_marker.getPosition();

document.getElementById('geo_latitude').value = start_marker.point.lat();

document.getElementById('geo_longitude').value = start_marker.point.lng();


});


}

initialize();

</script>

<!--end place options-->   




<?php



}


public function setup_post_types() {


$this->register_places_post_type();
 

}


public function register_p2p_connection_types() {

$from_posttypes = array('post','page');


$from_posttypes = apply_filters( 'lh_places_from_posttypes', $from_posttypes);

 
  p2p_register_connection_type( array(
	'title' => 'Located at',
	'cardinality' => 'many-to-one',
        'name' => $this->namespace.'-object_location',
        'from' => $from_posttypes,
        'to' => $this->posttype,
'admin_column' => 'from',
  'admin_box' => array(
    'show' => 'from',
    'context' => 'side'
  )
    ) );


}


public function on_activate($network_wide) {

    if ( is_multisite() && $network_wide ) { 

        global $wpdb;

        foreach ($wpdb->get_col("SELECT blog_id FROM $wpdb->blogs") as $blog_id) {
            switch_to_blog($blog_id);
wp_clear_scheduled_hook( 'lh_places_flush' ); 
wp_schedule_single_event(time(), 'lh_places_flush');
            restore_current_blog();
        } 

    } else {

flush_rewrite_rules();

}




}


public function add_meta_boxes($post_type, $post) {

if ($post_type == $this->posttype){


add_meta_box("geo_details", "Geo Options", array($this,"geo_options"), $post_type, "normal", "low");



}

}


public function update_post_geo_coordinate(){
global $post;

if ($_POST["geo_latitude"]){

update_post_meta($post->ID, "geo_latitude", $_POST["geo_latitude"]);
update_post_meta($post->ID, "geo_longitude", $_POST["geo_longitude"]);






}

if ($_POST["lh_places-place-geojson"]){


update_post_meta($post->ID, "lh_places-place-geojson", $_POST["lh_places-place-geojson"]);




}

}

function add_geometa_head() {

global $post;

if ( is_singular() ) {

$custom_fields = get_post_custom();

if (isset($custom_fields['geo_latitude'])){

echo "\n<!-- begin LH Places coordinate output -->\n";

echo "<meta name=\"ICBM\" content=\"".$custom_fields['geo_latitude'][0].", ".$custom_fields[geo_longitude][0]."\" />\n";

echo "<meta name=\"DC.title\" content=\"".$post->post_title." | ";

bloginfo( 'name' );

echo "\" />\n";

echo "<!-- end LH LH Places coordinate output -->\n";

}
}
}

function add_georss_namespace() {

echo "xmlns:georss=\"http://www.georss.org/georss\" ";

}

function add_georss_node() {

$custom_fields = get_post_custom();

if (isset($custom_fields['geo_latitude'])){

echo "<georss:point>".$custom_fields[geo_latitude][0]." ".$custom_fields[geo_longitude][0]."</georss:point>\n";

}

}

public function lh_places_geolocate_output($attributes, $content = null){

extract( shortcode_atts( array(
		'url' => ''
	), $attributes ) );

ob_start();

 ob_start();

echo '
<form name="lh_places-geolocate_form" id="lh_places-geolocate_form" action="'.$url.'" method="get" accept-charset="utf-8"
>
';

?>

<input name="lh_places-geolocate_form-latitude" id="lh_places-geolocate_form-latitude" value="" type="hidden" />

<input name="lh_places-geolocate_form-longitude" id="lh_places-geolocate_form-longitude" value="" type="hidden" />

<?php

echo '<p>
<input type="submit" id="lh_places-geolocate_form-submit" name="lh_places-geolocate_form-submit" value="Search"/>
</p>
</form>
';



wp_enqueue_script('lh_places-script', plugins_url( '/scripts/lh-places.js' , __FILE__ ), array(), '1.00', true  );


$content = ob_get_clean();


return $content;

}

public function register_shortcodes(){

add_shortcode('lh_places_geolocate', array($this,"lh_places_geolocate_output"));
add_shortcode('lh_places_geomap', array($this,"lh_places_geomap_output"));



}

public function lh_places_geomap_output($attributes, $content = null){

global $content_width;

if ( ! isset( $content_width ) ) {
	$content_width = 600;
}


    ob_start();


 
    // define attributes and their defaults
    extract( shortcode_atts( array (
        'type' => 'post',
        'order' => 'date',
        'orderby' => 'title',
        'posts' => -1,
        'category' => '',
'post_parent' => '',
'p' => '',
    ), $attributes ) );
 
  // define query parameters based on attributes

$myoptions = array( 
'post_type'  => 'lh-place', 
 'posts_per_page' => "10",
	'orderby' => 'title',
	'order'   => 'ASC',
    'meta_query' => array(
            'relation' => 'OR',
    array(
        'key'   => 'geo_latitude',
    ),
    array(
        'key'   => 'geo_longitude',
    ),
    array(
        'key'   => 'geo_address',
    )
)

);  



if ($post_parent){ $myoptions['post_parent'] = $post_parent; }

if ($p){ $myoptions['p'] = $p; }

$recentPosts = new WP_Query();
$recentPosts->query($myoptions);

//print_r($recentPosts);

$latitude = 0;

$longitude = 0;

$count = $recentPosts->post_count;
if ($count > 1){ echo '<ol id="lh_places_geomap_location_list">'; } else {echo '<ul id="lh_places_geomap_location_list">'; } 

$markers = "";

$i = 1;
?>

<?php while ($recentPosts->have_posts()) : $recentPosts->the_post(); ?>

<li><a href="<?php the_permalink() ?>" rel="bookmark"><?php the_title(); ?></a> - 
<span  class="geo"><?php echo get_post_meta( get_the_ID(), 'geo_address', true ); ?>

<span class="latitude">
<span class="value-title" title="<?php $geo_latitude = get_post_meta( get_the_ID(), 'geo_latitude', true ); echo $geo_latitude; $latitude = $latitude + $geo_latitude; ?>"> </span>
</span>
<span class="longitude">
<span class="value-title" title="<?php $geo_longitude = get_post_meta( get_the_ID(), 'geo_longitude', true ); echo $geo_longitude; $longitude = $longitude + $geo_longitude; ?>"> </span>
</span>
</span>
</li>
<?php 

$add = 'markers=color:red%7Clabel:'.$i.'%7C';

$add .= get_post_meta( get_the_ID(), 'geo_latitude', true ).','.get_post_meta( get_the_ID(), 'geo_longitude', true );


$markers .= '&'.$add;

$i++;

?>


<?php endwhile; ?>
<?php
if ($count > 1){ echo '</ol>'; } else {echo '</ul>'; } 

$latitude = $latitude / $count;

$longitude = $longitude / $count;

$myvariable = ob_get_clean();

$add = '<div id="lh_places_geomap_div" style="width:100%;height:400px;" data-center_latitude="'.$latitude.'" data-center_longitude="'.$longitude.'" data-icon_directory="'.plugins_url( 'assets/icons/', __FILE__ ) .'">
<img src="https://maps.googleapis.com/maps/api/staticmap?center='.$latitude.','.$longitude.'&size='.$content_width.'x400&maptype=roadmap'.$markers;


$add .= '" width="600px" height="400px" />
</div> <button id="lh_geo_shortcodes-locate">Locate Me</button>';

$myvariable = $add.$myvariable;

wp_reset_postdata(); 

return $myvariable;

}


public function run_geo_search( $query ){



if (isset( $_GET['lh_places-geolocate_form-latitude']) and isset($_GET['lh_places-geolocate_form-longitude']) and ($query->is_main_query())){

//print_r($query);

global $wpdb;

if ($query->query['post_type']){


$type = $query->query['post_type'];

} else {

$type = 'post';
}

$sqlorig = "SELECT 
objects.ID as ObjectsID,
objects.post_title as ObjectsTitle,
  places.ID as placeID,
  places.post_title as placeTitle,
  pm2.meta_value AS latitude,
  pm3.meta_value AS longitude,
  (
    6371 * ACOS(
      COS(RADIANS(".$_GET['lh_places-geolocate_form-latitude'].")) * COS(
        RADIANS(
          CASE
            WHEN pm2.meta_value = '' 
            THEN 0 
            WHEN pm2.meta_value IS NULL 
            THEN 0 
            ELSE pm2.meta_value 
          END
        )
      ) * COS(
        RADIANS(
          CASE
            WHEN pm3.meta_value = '' 
            THEN 0 
            WHEN pm3.meta_value IS NULL 
            THEN 0 
            ELSE pm3.meta_value 
          END
        ) - RADIANS(".$_GET['lh_places-geolocate_form-longitude'].")
      ) + SIN(RADIANS(".$_GET['lh_places-geolocate_form-latitude'].")) * SIN(
        RADIANS(
          CASE
            WHEN pm2.meta_value = '' 
            THEN 0 
            WHEN pm2.meta_value IS NULL 
            THEN 0 
            ELSE pm2.meta_value 
          END
        )
      )
    )
  ) AS distance 
FROM
  ".$wpdb->posts." as objects
INNER JOIN ".$wpdb->prefix."p2p as p2p on objects.ID = p2p.p2p_from INNER JOIN ".$wpdb->posts." as places on p2p.p2p_to = places.ID
  LEFT JOIN ".$wpdb->postmeta." AS pm2 
    ON (
      places.ID = pm2.post_id 
      AND pm2.meta_key = 'geo_latitude'
    ) 
  LEFT JOIN ".$wpdb->postmeta." AS pm3 
    ON (
      places.ID = pm3.post_id 
      AND pm3.meta_key = 'geo_longitude'
    ) 
WHERE objects.post_type = '".$type."' and p2p.p2p_type = 'lh_places-object_location' and places.post_type = 'lh-place' and places.post_status = 'publish' 
HAVING distance < 25 
ORDER BY distance 
LIMIT 0, 20 ";

$sql = "SELECT 
objects.ID as ObjectsID,
objects.post_title as ObjectsTitle,
  places.ID as placeID,
  places.post_title as placeTitle,
  pm2.meta_value AS latitude,
  pm3.meta_value AS longitude,
  (
    6371 * ACOS(
      COS(RADIANS(".$_GET['lh_places-geolocate_form-latitude'].")) * COS(
        RADIANS( pm2.meta_value )
      ) * COS(
        RADIANS( pm3.meta_value ) - RADIANS(".$_GET['lh_places-geolocate_form-longitude'].")
      ) + SIN(RADIANS(".$_GET['lh_places-geolocate_form-latitude'].")) * SIN(
        RADIANS( pm2.meta_value )
      )
    )
  ) AS distance 
FROM
  ".$wpdb->posts." as objects
INNER JOIN ".$wpdb->prefix."p2p as p2p on objects.ID = p2p.p2p_from INNER JOIN ".$wpdb->posts." as places on p2p.p2p_to = places.ID
  LEFT JOIN ".$wpdb->postmeta." AS pm2 
    ON (
      places.ID = pm2.post_id 
      AND pm2.meta_key = 'geo_latitude'
    ) 
  LEFT JOIN ".$wpdb->postmeta." AS pm3 
    ON (
      places.ID = pm3.post_id 
      AND pm3.meta_key = 'geo_longitude'
    ) 
WHERE objects.post_type = '".$type."' and p2p.p2p_type = 'lh_places-object_location' and places.post_type = 'lh-place' and places.post_status = 'publish' 
HAVING distance < 25 
ORDER BY distance 
LIMIT 0, 20 ";

//echo $sql;

$results = $wpdb->get_results($sql);

//print_r($results);

 foreach ($results as $result) {

$ids[] = $result->ObjectsID;
}

//print_r($ids);

$query->set('post__in', $ids);

return $query;

}


}

public function flush_rules(){

flush_rewrite_rules();
wp_clear_scheduled_hook( 'lh_places_flush' ); 

}


// Set up Scripts For Tabs 
public function add_google_api_script_to_head(){
    wp_register_script( 'google-maps-api', 'https://maps.googleapis.com/maps/api/js?sensor=false' );
    // For either a plugin or a theme, you can then enqueue the script:
    wp_enqueue_script( 'google-maps-api' );
wp_enqueue_script('lh_places_geomap_init_script', plugins_url( '/scripts/lh-places-map-init.js' , __FILE__ ), array(), '1.00', true  );
}


public function maybe_add_scripts_to_head() { 
   global $wp_query; 
   if ( is_singular() ) { 
      $post = $wp_query->get_queried_object(); 
      if ( has_shortcode( $post->post_content, 'lh_places_geomap') ) { 
      // Adding the google api JUST when needed
      add_action( 'wp_enqueue_scripts', array($this,"add_google_api_script_to_head"));
      } 
   } 
}



function add_admin_scripts( $hook ) {

    global $post;

    if ( $hook == 'post-new.php' || $hook == 'post.php' ) {
        if ( 'lh-place' === $post->post_type ) {     
    wp_register_style( 'lh_places-admin_css', plugins_url( '/assets/admin-stye.css' , __FILE__ ), false, '1.0.2' );
    wp_enqueue_style( 'lh_places-admin_css' );
            wp_enqueue_script(  'lh_places-admin_google_maps', 'https://maps.googleapis.com/maps/api/js?v=3.exp' );
	    wp_enqueue_script(  'lh_places-admin_geojson_editor', 'https://google-developers.appspot.com/maps/documentation/utils/geojson/editor.js' );
	}
    }
}



public function __construct() {

$this->options = get_option($this->opt_name);
$this->filename = plugin_basename( __FILE__ );

//Register custom post type 
add_action('init', array($this,"setup_post_types"));
add_action('add_meta_boxes', array($this,"add_meta_boxes"),10,2);
add_action( 'save_post', array($this,"update_post_geo_coordinate"));
add_action( 'p2p_init', array($this,"register_p2p_connection_types"));
add_action('wp_head', array($this,"add_geometa_head"));
add_action( 'rdf_ns', array($this,"add_georss_namespace"), 1);
add_action( 'rss2_ns', array($this,"add_georss_namespace"), 1);
add_action( 'rdf_item', array($this,"add_georss_node"), 1);
add_action( 'rss2_item', array($this,"add_georss_node"), 1);

add_action( 'init', array($this,"register_shortcodes"));
add_action( 'pre_get_posts', array($this,"run_geo_search"), 10, 1 );
add_action('lh_places_flush', array($this,"flush_rules"));

add_action('template_redirect', array($this,"maybe_add_scripts_to_head"));
add_action('admin_enqueue_scripts', array($this,"add_admin_scripts"),10,1);


}

}





$lh_places_instance = new LH_Places_plugin();

register_activation_hook(__FILE__, array($lh_places_instance, 'on_activate') , 10, 1);


function lh_places_deactivate() {
	flush_rewrite_rules();
}

register_deactivation_hook( __FILE__, 'lh_places_deactivate' );










?>