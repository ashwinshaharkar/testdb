Module: Custom Google Comparison Shopping Service

Description
===========
Manage products from Google Comparison Shopping Service API

Requirements
============
Install "google/apiclient": "^2.0" library using composer

i.e.
1. Update composer.json file
"require": {
	"google/apiclient": "^2.0"
},

2. Run composer update

Installation and usages
=======================

1.	Install ‘Custom Google Comparison Shopping Service’ module as usual
2.	It will create ‘Google Products’ content type automatically 
3.	Go to the module configuration page i.e. admin/config/gcss/gcss-settings to configure merchant and oauth2 authentication configurations settings
4.	Fetch products from : /admin/gcss/fetch-products

Reference link
===============

https://support.google.com/merchants/answer/7524491?hl=en-GB
https://developers.google.com/shopping-content/v2/quickstart
https://developers.google.com/adwords/api/docs/guides/authentication#webapp
