=== User Activity Tracking and Log ===
Contributors: MooveAgency
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=VV64TVD23Z32A
Stable tag: trunk
Tags: activity tracking, activity log, tracking, user tracking
Requires at least: 4.3
Tested up to: 5.4
License: GPLv2

This plugin gives you the ability to track user activity on your website

== Description ==

This plugin gives you the ability to track user activity on your website.

### Data that will be logged:

* Date/time
* User name
* Activity (visited/updated)
* Client IP
* Client Location (by IP Address)
* Referrer url

### Features 

* Simple & Intuitive
* Enable / disable for custom post types
* Keep logs up to 2 weeks 
* Intuitive Search
* Custom log order in the Activity View
* **[Premium]** Keep logs up to 4 years 
* **[Premium]** Anonymise IP addresses (GDPR)
* **[Premium]** Export logs to CSV
* **[Premium]** Filter activity by user
* **[Premium]** Set timezone, screen options
* **[Premium]** Option to track logged in users only
* **[Premium]** Exclude users from tracking by role

> Note: some features are part of the Premium Add-on. You can [get Activity Premium Add-on here](https://www.mooveagency.com/wordpress-plugins/)!

### Demo Video

You can view a demo of the plugin here: 

[vimeo https://vimeo.com/305493827]

[User Activity Tracking and Log by Moove Agency](https://vimeo.com/305493827)

#### Custom Log Order

* By clicking to the table header, you'll be able to order the log values. You can order the table by Date, Title, Post Type, User, Activity, Client IP, Client Location, Referrer.

#### Screen Options [Premium]

This is a customized version of the default WordPress screen settings layout. You can activate the screen settings by clicking the button at the top right corner of the "Activity Log" page.
Using these options, you'll be able to:

* disable / enable the columns in the log view
* change the pagination value
* use a custom "Date / Time" as log date

### Additional Features

* This plugin works seamlessly with our membership plugin that encourages users to register on your website giving you the ability to see who has visited your site and which pages they viewed.
* Download the Membership plugin here: https://wordpress.org/plugins/post-protection-and-registration-wall/

== Installation ==
1. Upload the plugin files to the plugins directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the \'Plugins\' screen in WordPress.
3. Logs are stored in the "Activity log" item on the left hand side in the menu, below the Dashboard menu item.
4. Use the Settings screen to configure the plugin.

== Screenshots ==

1. User Activity Tracking and Log - Preview of global settings
2. User Activity Tracking and Log - Changing log interval
3. User Activity Tracking and Log - Activity Screen & Timezone options [Premium]
4. User Activity Tracking and Log - GDPR Settings [Premium]
5. User Activity Tracking and Log - Tracking Settings [Premium]
6. User Activity Tracking and Log - Activity Logs - All ( * the screenshot contains Premium features as well )
7. User Activity Tracking and Log - Activity Logs - Post Type
8. User Activity Tracking and Log - Activity Logs - Detail Page

== Changelog ==
= 2.0.7 =
* Extended individual activity meta box
* Bugfixes

= 2.0.6 =
* Activity Log - Query updated

= 2.0.5 =
* Bugfixes

= 2.0.4 =
* Added hook to disable location tracking
* Improved location tracking
* Caching improvements

= 2.0.3 =
* Added hook to filter results

= 2.0.2 =
* Class static methods fixed

= 2.0.1 =
* Fixed php error in moove-content class

= 2.0.0 =
* Licence manager implemented
* Improved admin layout
* Bugfixes

= 1.5.1 = 
* Filter bug fixed
* Post type filters extended
* Added hook to hide disabled post type - add_action( 'uat_show_disabled_cpt', 'return__false' );
* Bugfixes

= 1.5.0 = 
* Fixed filter conflicts
* Bugfixes

= 1.4.1 = 
* Updated filters & database controller

= 1.4.0 = 
* Ability to order table content in post type view

= 1.3.2 = 
* Fixed duplicate records issue

= 1.3.1 = 
* Fixed PHP ip checker

= 1.3.0 = 
* Updated plugin premium box
* Bugfixes

= 1.2.9 = 
* Updated plugin admin tabs

= 1.2.8 = 
* Updated plugin premium box

= 1.2.7 = 
* Updated plugin premium box

= 1.2.6 = 
* Fixed missing CSS class to hide items
* Fixed clear all logs feature

= 1.2.5 = 
* Php warning fixed on save_post

= 1.2.4 = 
* Fixed single analytics tracking
* Fixed single analytics log list

= 1.2.3 = 
* Fixed select2 loading issue

= 1.2.2 = 
* Added select2 to filters
* User dropdown ordered by Display Name

= 1.2.1 = 
* Database Search Fixed

= 1.2.0. =
* Bugfixing
* Cleared Activity Layout

= 1.1.2. =
* Fixed warning on Activity Screen

= 1.1.1. =
* Fixed setting button link
* PHP compatibility issues resolved
* Translation slug updated, plugin prepared for localization 
* Bugfixes

= 1.1.0. =
* Adding Czech translation

= 1.0.9. =
* Added donations message

= 1.0.8. =
* Fixed PHP v5.x errors

= 1.0.7. =
* Fixed PHP warnings

= 1.0.6. =
* Added Screen Options, custom table order features

= 1.0.5. =
* Fixed log export download function

= 1.0.4. =
* Fixed array shortcode syntax error

= 1.0.3. =
* Fixed menu page icon

= 1.0.2. =
* Validated, sanitised and escaped inputs

= 1.0.1. =
* Code modified to follow WP standards

= 1.0.0. =
* Initial release of the plugin.
