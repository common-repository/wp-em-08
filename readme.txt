=== Plugin Name ===
Contributors: kubi23
Donate link: http://www.svenkubiak.de/wordpress-em-2008/#spenden
Tested up to: 2.5.1
Stable tag: 1.16
Requires at least: 2.1
Tags: euro 2008, em, plugin, sidebar, soccer, austria, switzerland, euro2008, european championship, 2008, euro 08, em08

Displays results and games of the UEFO EURO 2008.

== Description ==

From June 7th to June 29th the EURO 2008 will take place in Switzerland and Austria. Use this Plugin to display live results and game informations. The Plugin grabs its informations from the web-service at [OpenLigaDB](http://www.openligadb.de). 

Note: As of version 1.16 development is discontinued!

= Available languages =

* German
* English
* Spanish
* French
* Italian 
* Portuguese
* Russian

== Installation ==

1. Unzip
2. Upload complete folder to wp-content/plugins
3. Activate plugin 

If you want to display the next match, put the following code in your sidebar:

`<?php if (class_exists('EM08')){EM08::nextMatch();}?>`

If you want to display live results, put the following code in your sidebar:

`<?php if (class_exists('EM08')){EM08::currentMatch(1);}?>`

You may want to change the given ID for currentMatch depending on what round you want to display.

* First Round  = 1
* Quarter-finals = 2
* Semi-finals = 3
* Finals = 4

If the Live-Result is displayed in red, the game is still running. If the the Live-Result is displayed in black, the game is over. Live-Results will be displayed 180 minutes from kick-off.

== Frequently Asked Questions ==

= Why does the plugin require PHP 5 and SOAP Extension? =

PHP 5 has a build in SOAP Client which enables easy access to webservices.

= I have PHP 5 but get the error "Parse error: syntax error, unexpected {" =

This means, that your Webhoster is not running PHP 5 as default. If you have to run PHP-Scripts using .php5 instead of .php your blog is not running on PHP 5. What *might* help is adding an additional mapping to your WordPress .htaccess file. 

Open your .htaccess file located in you WordPress-Root and add the following line at the end:

`AddType application/x-httpd-php5 .php`


If this does not work, try the PHP 4 Version of this Plugin (see Description).

= Is it possible to display more than one "Next Game" ? =

No. The WebService returns only one.

Currently every 5 Minutes.

= How is the TImes and Dates displayed ? =

Times and Dates based on your server. If you live in GMT+5, then the gametime is displayed in GMT+5 based on your server time.

= Can i change the apperance for Next Game and Live-Results ? =

Yes and no. If you want to changes the apperance you have to modify the code itself.

= How frequently are the Live-results updated ? =

Currently every 5 Minutes.

== Screenshots ==

1. Next Match
2. Live Results

== Version History ==

* Version 1.16
	* Required Update to display Finals Live-Results
* Version 1.15
	* Required Update to display Semi-Finals Live-Results
* Version 1.14
	* Required Update to display Quarter-Finals Live-Results
* Version 1.13
	* Bugfix to avoid timeout when webservice is not available
* Version 1.12
	* Time is now based on WordPress gmt-offset
	* Removed debug functions
* Version 1.11
	* Updated language files
* Version 1.10
	* Fixed error when displaying team names
	* Updated language files
* Version 1.9
	* Fixed bug when handling timezones
	* Fixed bug when displaying Live-Results
	* Fixed encoding bug
* Version 1.8
	* Addes support for different timezones
* Version 1.7
	* Increased time for displaying Live-Results
* Version 1.6
	* Fixed Bug when accessing SOAP-Service
* Version 1.5
	* Fixed SOAP-Bug
    * Added russian and portugese translation
* Version 1.4
	* Added timeout for SOAP-Client
	* Added deactivation function
    * Minor code changes
    * Added french and italian translation
* Version 1.3
    * Added language support 
    * Added english and spanish translation
* Version 1.2
    * Checking if SOAP extension is available
* Version 1.1
    * Added admin notice when checking PHP Version
* Version 1.0
    * Initial version