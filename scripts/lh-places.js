function lh_places_run_geolocate(){
if (navigator.geolocation) {
  navigator.geolocation.getCurrentPosition(function (position){
document.getElementById("lh_places-geolocate_form-latitude").value = position.coords.latitude;
document.getElementById("lh_places-geolocate_form-longitude").value = position.coords.longitude;
document.getElementById("lh_places-geolocate_form").submit();
  })

      return false;
} else {
        alert("Geolocation not supported");
        return false;
}
    }




document.getElementById("lh_places-geolocate_form").onsubmit= function(){ return lh_places_run_geolocate();}