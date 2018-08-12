Module: Nearby Places Search

Project Page
============
https://www.drupal.org/sandbox/ashwinshaharkar/2729005

Description
===========
Nearby Places Search: This module integrates with the Google Places and GMap.

Shows a list of places of a certain type and configurable from the back-end 
settings within the specified radius for specified location.

Requirements
============
Libraries API (>8.x-3.x-dev or any stable version)

Installation
============
1. Install and configure 'Libraries API' (>8.x-3.x-dev or any stable version).

2. Copy the 'nearby_places_search' module directory in to your Drupal 
'modules' directory and install it as usual.

3. Download the places marker images library from 
https://github.com/ashwinshaharkar/nearbyplace.markers.library

4. Unzip the file and rename the folder to "nearby_places_search.markers" 
(pay attention to the case of the letters)

5. Create a directory called 'libraries' under root folder of your Drupal 
installation.
Ex: /libraries

6. Ensure you have a valid path similar to this one for all files
Ex: /libraries/nearby_places_search.markers

That's it!

Usage
=====
1. In path (admin/config/nearby_places_search/settings) configure the search 
settings for places. 

2. You can change the type of place, the center point and the radius from the 
center point in the configuration page.

3. In path (admin/structure/block) find and configure the 'Nearby Place Search' 
block as per requirement.
e.g. Block title : Nearby Place Search, etc

Note: As this block covers most of the page space so we cannot assign this block
into smaller regions e.g Siderbar first or Siderbar second region. 
We must assign it into main contain or similar regions.

Configuration Settings
======================
1. Location Types - Location Type is restricted to select only one value.
See https://developers.google.com/places/documentation/supported_types 
for supported types. 
Default values set as: Atm|Bank|Bus Station|Hospital|Park|Restaurant|School

2. Google API Authentication Method - 'API Key' or 'Google Maps API for Work'
As per method selection add 'Google Maps API Key' (if API key) and  
'Google Maps API for Work: Client ID' or 
'Google Maps API for Work: Private/Signing Key' (if API for Work1).

3. Location - The latitude/longitude around which to retrieve the information 
of place. This must be specified as latitude,longitude.
Default value set to 18.5204, 73.8567 (Pune, Maharashtra, India).

4. Radius - Defines the distance (in meters) within which to return place 
results. The maximum allowed radius is 50000 meters. Default set to 1000 meter.

Details
=======
For the meaning of the several configuration parameters please check README.md

Reference link
===============
https://developers.google.com/places/web-service/search#PlaceSearchRequests
