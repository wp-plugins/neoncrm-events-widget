=== NeonCRM Events Widget ===
Contributors: colinpizarek
Donate link: http://www.z2systems.com/
Tags: neon, neoncrm, crm, events, nonprofit
Requires at least: 4.0
Tested up to: 4.3
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Displays a feed of upcoming events retrieved from NeonCRM.

== Description ==

Display a list of upcoming events in a widget, retrieved from your NeonCRM system. Limit your list of events to a specific campaign
or category.

Requires PHP 5.2 and cURL.

== Installation ==

1. Obtain a NeonCRM account. [Learn about NeonCRM](http://www.z2systems.com/ "NeonCRM by Z2 Systems, Inc.")
2. Ensure the API is enabled for your NeonCRM system. This guide can help you determine whether the API is enabled for your system:
[API Keys](http://help.neoncrm.com/api/keys "API Keys")
3. Generate an API key to be used with this plugin. The guide in the previous step demonstrates this.
4. Extract all files in the 'neon-events' plugin to the '/wp-content/plugins/' directory,
5. Activate the plugin through the 'Plugins' menu in WordPress.
6. Navigate to the Widgets configuration to add the NeonCRM Events Widget.
7. Enter a valid API key and organization ID into the widget configuration.

== Frequently Asked Questions ==

= What is NeonCRM? =

NeonCRM is a web-based fundraising and membership system that provides nonprofit organizations with all the tools 
required to increase donations and memberships while automating common processes and streamlining staff's day-to-day tasks.  

= Where do I get an API Key/ Org ID? =

This guide explains how to generate an API key: [API Keys](http://help.neoncrm.com/api/keys "API Keys")

== Screenshots ==

1. Display a list of upcoming events in a widget
2. Configure each widget's settings

== Changelog ==

= 0.1 =
* Original release

= 0.11 =
* Fixed an issue that caused multiple cached widget instances to overwrite each other.

= 0.12 =
* Fixed an issue that prevented the cache from being cleared when a widget is updated.

= 0.13 =
* Updated the references to NeonCRM's field names. This maintains compatibility after NeonCRM's API upgrade on 1/17/2015. The plugin may not work properly without this update.
* Added two new filter options to hide events that are not web-published or have web registration disabled.
* Refactored the logic for the number of events to display, due to the new filters

= 0.14 =
* Fixed an issue that prevented Category filtering from working

= 0.15 =
* Removed PHP4-style class constructors

== Upgrade Notice ==

= 0.1 =
* Original release

= 0.11 =
* Fixes a bug related to multiple widget instances

= 0.12 =
* Fixes a bug related to saving widget instances

= 0.13 =
* Update to stay compatible with changes made to NeonCRM. The plugin may not work properly without this update.
* Adds new filters that correspond with NeonCRM's web publish settings

= 0.14 =
* Fixes an bug that prevents Category filtering from working

= 0.15 =
* This update ensures compatibility with WordPress 4.3. The plugin may not work properly without this update.