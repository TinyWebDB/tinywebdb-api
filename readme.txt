===  TinyWebDB API  ===
Contributors: chen420
Plugin URI: http://appinventor.in/side/tinywebdb-api/
Author URI: http://digilib.net/
Donate link: http://appinventor.in/donate/
Tags: appinventor, tinywebdb, api
Requires at least: 3.4
Tested up to: 3.4.2
Stable tag: 0.2.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

a AppInventor TinyWebDB API plugin, use you WordPress as a TinyWebDB web service.


== Description ==

AppInventor is a easy way to creating an Android app from web browser.
TinyWebDB API is a AppInventor TinyWebDB API plugin, use you WordPress as a TinyWebDB web service.

TinyWebDB Protocol:
    Action        URL                      Post Parameters  Response 
    Get Value     {ServiceURL}/getvalue    tag              JSON: ["VALUE","{tag}", {value}] 
    Store A Value {ServiceURL}/storeavalue tag,value        JSON: ["STORED", "{tag}", {value}] 

Roadmap:
    TinyWebDB API 0.1.0 implemented Get Value Action.
    TinyWebDB API 0.2.0 will implement Store A Value Action.
	TinyWebDB API 0.3.0 will implement Authentication.
	TinyWebDB API 1.0.0 Full release.

Visit Plugin URI for detail.

== Installation ==

1. FTP the entire tinywebdb-api directory to your Wordpress blog's plugins folder (/wp-content/plugins/).
2. Activate the plugin on the "Plugins" tab of the administration panel.
3. Check test URL on admin menu to make sure API work properly.
4. Refer to Plugin URI to get sample Android test app which make by App Inventor, to create your own app.

for how to use AppInventor to inventor your Android app with this plugin , visit Plugin URI for detail please.


== Upgrade Notice ==
1. Deactivate plugin
2. Upload updated files
3. Reactivate plugin

Upgrade notes:
*  You may use the autmoated plugin updater in WordPress 2.5+ with this plugin, but make sure you read the upgrade notes of the latest version after upgrading.

= 1.0 =


== Frequently Asked Questions ==
= A question that someone might have =

An answer to that question.


== Screenshots ==
1. The TinyWebDB API management page in the WordPress Admin.


== Known Issues ==


== Changelog ==

= 0.1.0 =
Start TinyWebDB API plugin.

= 0.1.3 =
First alpha release which implemented Get Value Action.

= 0.2.0 =
Release which implemented Get Value Action with API Key.
Add test URL on admin menu.

= 1.0.0 =

