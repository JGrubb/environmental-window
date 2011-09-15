=== Random Text ===
Contributors: pantsonhead
Donate link: http://www.amazon.co.uk/gp/registry/1IX1RH4LFSY4W
Tags: widget, plugin, sidebar, random, text, quotes
Requires at least: 2.8
Tested up to: 3.1.2
Stable tag: trunk

Store and display random/rotated text by category in sidebar widget or templates.

== Description ==

RandomText is a handy WordPress plugin that allows you to save, edit and delete categorized text, and inject random/rotated text by category into the sidebar (via widget) or page body (via template tags). The sidebar widget allows you to set an optional Title, and text header and footer. You could say RandomText picks up where the Text widget left off. Whether you want to display random/rotated trivia, quotes, helpful hints, featured articles, or snippets of html, you can do it all easily with RandomText.

== Installation ==

IMPORTANT: This plugin uses the WP_Widget class introduced in WordPress v2.8 and will not work with earlier versions of WordPress.

1. Upload `randomtext.php` and `randomtext_admin.php` to the `/wp-content/plugins/` directory of your WordPress installation
2. Activate the plugin through the 'Plugins' menu in WordPress
3. The Random Text widget can now be configured and used from the Appearance -> Widgets menu
4. Text entries can be managed via from the Settings -> Random Text menu

Note: During installation, Random Text creates a new database table to store the entries by category  - you should see two test records after installation by clicking on the Settings -> Random Text menu.

== Screenshots ==

1. Sidebar widget options
2. Text management page

== Frequently Asked Questions ==

= Can I use shortcodes? =

Yes, you can use [randomtext] or [randomtext category="funny"] or even [randomtext category="funny" random="1"].

= What about template tags? = 

You can use something like this, where 'category' is the group you wish to select items from.
< ?php randomtext('category'); ?>


== Changelog ==

= v0.2.8 2011-06-15 =

* Fixed pagination issues on admin page.

= v0.2.7 2011-06-05 =

* Fixed numerous issues highlighted by debugging mode.

= v0.2.6 2010-08-05 =

* Fixed shortcode recursion

= v0.2.5 2010-08-04 =

* Added support for shortcodes in text field
* Fixed Admin unicode truncation issue

= v0.2.4 2009-10-26 =

* Added shortcode support

= v0.2.3 2009-09-22 =

* Added Bulk Insert option
* Improved handling of "No Category" items

= v0.2.2 2009-08-23 =

* Added record id check before timestamp update 

= v0.2.1 2009-08-22 =

* Added database table check/error to admin page

= v0.2 2009-08-22 =

* Added random/rotation option
* Added screenshots

= v0.1.4 2009-08-19 =

* Fixed admin path bug

= v0.1.3 2009-08-18 =

* Fixed Pre-text/Post-text bug

= v0.1.2 2009-08-16 =

* Fixed editing issues
* Minor correction to readme.txt

= v0.1.1 2009-08-14 =

* Minor corrections to readme.txt

= v0.1 2009-08-11 =

* Initial release
