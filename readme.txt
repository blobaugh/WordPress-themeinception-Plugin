=== Themeinception === 
Contributors: blobaugh
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=otto%40ottodestruct%2ecom
Tags: theme, creation, new
Requires at least: 3.0
Tested up to: 3.4
Stable Tag: trunk
License: GPLv2
License URI: http://www.opensource.org/licenses/GPL-2.0

A plugin that lets you quickly and easily create new themes.

== Description ==

A plugin that lets you quickly and easily create new themes.

Install, activate, and then go to the Appearance->Create a New Theme menu to create and activate a new blank theme, live, on your site.


== Installation ==

1. Upload the files to the `/wp-content/plugins/themeinception/` directory or install through WordPress directly.
1. Activate the "Themeinception" plugin through the 'Plugins' menu in WordPress
1. Try the "Create a New Theme" option in the Appearance menu.

== Frequently Asked Questions ==

= Is this safe? =

Nope, not in the slightest. You have been warned.

That said, it's perfectly "safe", given a certain definition of the word "safe". The "create a new theme" screen itself is secure, and inaccessible to anybody who lacks the capability to "edit_plugins" to begin with. Themeinception also uses the proper WP_Filesystem methods to create the theme files, so there's no worries about incorrect file ownership on shared hosting. You may have to give it FTP credentials on some hosts for it to be able to create the theme, that's the WP_Filesystem at work, making sure the files are correctly "owned".

All this plugin really does is make it quick and easy to create a new one-off theme and take you directly to editing it. Cowboy-coding at its finest.

= For the love of god, man, why? =

The pluginception plugin by Otto42 inspired me

= I want this plugin to do something other than what it does. = 

Then modify the plugin however you like to do whatever you want. Don't bother me with it. This plugin does what I want it to do.

== Changelog ==

= 1.1 =
* Fix problems with quotes being slash-escaped (magic quotes).

= 1.0 = 
* First version.
