function lh_geo_shortcode_map_initialize() {



if (document.getElementById("lh_places_geomap_div")){

lat  = document.getElementById("lh_places_geomap_div").getAttribute("data-center_latitude");

lng  = document.getElementById("lh_places_geomap_div").getAttribute("data-center_longitude");

dir  = document.getElementById("lh_places_geomap_div").getAttribute("data-icon_directory");

	map = new google.maps.Map(document.getElementById('lh_places_geomap_div'), { 
		zoom: 15, 
		center: new google.maps.LatLng(lat, lng), 
		mapTypeId: google.maps.MapTypeId.ROADMAP 
	});


var bounds = new google.maps.LatLngBounds();


if (document.getElementById("lh_places_geomap_location_list")){

var list = document.getElementById("lh_places_geomap_location_list").getElementsByTagName("li");

i=0;

while(i < list.length){
span =  list[i].getElementsByTagName("span");
string = (span[0].innerText || span[0].textContent);

num = i + 1;

url = dir + 'marker' + num + '.png';


if (list.length > 1){

   var marker = new google.maps.Marker({
        position: new google.maps.LatLng (span[2].getAttribute("title"), span[4].getAttribute("title")),
        map: map,
 icon: {
                  url: url,
                  size: new google.maps.Size(20,34)
              },
        title: string.trim(),
    });


} else {

  var marker = new google.maps.Marker({
        position: new google.maps.LatLng (span[2].getAttribute("title"), span[4].getAttribute("title")),
        map: map,
        title: string.trim(),
    });


}





bounds.extend(marker.getPosition());



i++;
}

if (list.length > 1){

map.setCenter(bounds.getCenter());

map.fitBounds(bounds);

}

}




}
}

google.maps.event.addDomListener(window, 'resize', lh_geo_shortcode_map_initialize);
google.maps.event.addDomListener(window, 'load', lh_geo_shortcode_map_initialize)

lh_geo_shortcode_map_initialize();

function success(position) {

var coords = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);

if (typeof marker === 'undefined' || marker === null) {
marker = new google.maps.Marker({
      position: coords,
      map: map,
      title:"You are here!"
  });

} else {

marker.setPosition(coords);


}


}


   function readPage(){
if (navigator.geolocation) {
  navigator.geolocation.getCurrentPosition(success);
} else {
  error('Geo Location is not supported');
}
    }

    document.getElementById('lh_geo_shortcodes-locate').onclick=readPage;




