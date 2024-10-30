=== LH Places ===
Contributors: shawfactor
Donate link: https://lhero.org/plugins/lh-places/
Tags: geo, location, geolocation, geotag, radius, latitude, longitude, distance, miles, km, geolocate, range, plugin, url, GET
Requires at least: 3.0
Tested up to: 4.7
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

LH Places is a complete solution geo enabling your wordpress website, it does so in a standards compliant way and does not create additional database tables.

== Description ==
LH Places creates a cpt called a place, places have geographical information (eg latitude and longitude) associated with them. Posts, pages, and others CPTS (eg events), can then be connected to a place and are searchable by location.

It  also includes a shortcode that can be used to find posts or pages that are connected to a nearby location, as well as a shortcode to add maps of various places to any post object.


== Installation ==

1. Upload the `lh-places` folder to the `/wp-content/plugins/` directory
1. Install the WordPress Posts 2 Posts plugin
1. Activate both plugins through the 'Plugins' menu in WordPress


== Frequently Asked Questions ==

= What could you use this for? =
This plugin is part of the LocalHero project for member driven WordPress organisations but as it is modular could be used for other things. Use your imagination

= How do I use the geolocation shortcode? =

Place the short code [lh_places_geolocate] anywhere in the your post_content.m The shortcode has one optional parameter call url. This is the url that the browser will request, when making its geolocated search. By default this will be the blog url but if you wish to search a custom post type then use the url of the custom post type archive.

= How do I include maps of places on the front end? =

Place the shortcode [lh_places_geomap] into your post_content. The shortcode takes two options 'p' which is the post id of the place you wish to display or 'post_parent' which is the post id of the parent place of a set of places you may wish to display. Please don't use both.


= What post types can be linked to places? =

by default posts and pages can have a place attached (se the post edit screen). To add (or remove) an additional post type you can use the lh_places_from_posttypes filter which takes and returns the eligible post types. See plugin source.


== Changelog ==


**1.00 May 30, 2016*  
Initial release.


**1.01 May 31, 2016*  
Added Filter on post types

**1.02 March 30, 2017*  
Use isset