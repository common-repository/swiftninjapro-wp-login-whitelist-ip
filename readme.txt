=== WP-Login and WP-Admin Whitelist ===
Contributors: swiftninjapro
Tags: security, whitelist, wp-login, login, wp-admin, admin, ip, browser
Requires at least: 3.0.1
Tested up to: 5.5
Stable tag: 5.5
Requires PHP: 5.2.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Donate link: https://buymeacoffee.swiftninjapro.com

A Plugin That only allows whitelisted IP's, or optionally whitelisted browsers, to access wp-login, or optionally wp-admin.
This plugin also does Not effect front-end login plugins.

== Description ==

A Plugin That only allows whitelisted IP's, or optionally whitelisted browsers, to access wp-login.
This plugin does Not effect front-end login plugins.
If an IP is not whitelisted, the wp-login page will be killed and replaced with a message saying "your IP/Browser is not whitelisted", or optionally redirect the user to 404 page instead.

A better way to hide wp-login. You can add a list of admin IP's to this plugin, where you want to allow usage of wp-login. 
Even if you have other users that login, its better to use another plugin for a more secure front end login, and this plugin will only allow a specific list of IP's to access the wp-login page. 
You can also (optionally) have this plugin attempt to redirect anyone to 404 page, if they try and access wp-login without the right IP. 
You can also choose to disable the 404 redirect, and instead tell users there IP is not whitelisted, and that they should contact the admin if this is in error. 
The plugin does Not block wp-admin, so once logged in, you can still edit your site on the go. 
The plugin also has an option to whitelist your favorite common browsers to wp-login. This means you can keep users from accessing the wp-login page, simply because there using Internet Explore, and not what you chose to allow. 
There is another option (which may return false positives), that attempts to check if the source of an IP is commonly used by a proxy server, and can block proxy IP's to try and reduce spoofing. 

== Installation ==

1. Upload plugin to the /wp-content/plugins
2. Important: Make sure you have cpanel or filezilla working and connected to your website, so if you forget to add your IP and get locked out of wp-login, you can disable the plugin
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Add your IP to the list of whitelisted IP's in Swiftninjapro wp-login Whitelist IP settings (the admin settings page will tell you your Current IP)
5. Check the setting "Plugin Enabled" To Enable wp-login Whitelist IP And Click Save

== Frequently Asked Questions ==

= What do I do if I forgot to add my IP to the whitelist and can Not access wp-login =
To reactivate the plugin: (requires version 1.10 or later)
 1. on your site, go to: /admin-login-whitelist-recovery
 2. enter the page password "gHxXeVwvuz6Cez3" (You will still need to verify disabling the plugin through an admin email)
 3. follow the directions on that page
After that, the plugins "Enabled" setting will be disabled, and the plugin will not run, and settings will still be accessible.
Note: The email must be an admin users email. The email will also be rejected if the account is less than a week old.

If that fails, you can do 1 of 2 things:
 1. Contact your host, and ask them to disable the plugin manually
 2. Use FTP, Filezilla, or cpanel, then (click "File Manager", if using cpanel) navigate to public_html/wp-content/plugins, then find the folder "swiftninjapro-wp-login-whitelist-ip" and rename it to "swiftninjapro-wp-login-whitelist-ip-off" to disable the plugin.

= Does this work on wp-login even if I change the name with other plugin =
yes, this plugin uses wordpress functions wp_login_url(), wp_registration_url(), etc. to get an accurate url based on what your website uses.

= Does this block plugins from logging in users through front-end =
no. only the backend wp-login is blocked, you can still use a front end login for your users if you want to.
note: if your front end login plugin uses wp-login, you will need to enable a setting in this plugin, for your front end login plugin to work.

= what happens to a user if there IP is not whitelisted =
The plugin will kill the wp-login url for non-whitelisted IP's, and (optional by the admin) display a message saying there IP is not whitelisted, or redirect them to the 404 page. 
The plugins displayed message will also suggest a blocked user contact the admin if the block was in error. 

== Screenshots ==
1. the Swiftninjapro wp-login Whitelist IP settings

== Changelog ==

= 1.10 =
Moved recovery page to front end (password protected)

= 1.9 =
Added recovery page in case ip not whitelisted (requires admin)

= 1.8.1 =
Updated Function to detect browser
Usage of new Function can be enabled in the plugin settings

= 1.8 =
Added option to add wp-admin whitelist

= 1.7.2 =
Modified block message shown when 404 redirect option is disabled
 - no longer mentions existence of a plugin
 - now provides debug suggestions (like changing wifi network or using a different browser)
Settings page now grabs admin url from a wordpress function to provide better compatibility with plugins that may change the admin url

= 1.7 =
Improved compatibility with plugins that use wp-login redirects
This new compatibility can be enabled in settings and is optional

= 1.6 =
Browser Detection Now Includes Microsoft Edge
Microsoft Edge no longer returns as Google-Chrome

= 1.5.3 =
Added option to block common proxy server IP's

= 1.5.2 =
bug fix
fixed small php error where multi-line ip list was getting phrased weird and invisible encoding for extra lines (added by default from a common php function) was causing a difference between the userIP and the ip whitelist
ip list now recognizes multiple ip's in a list

= 1.5 =
plugin now (optionally) kills the page and displays a message instead of sending a non-whitelisted user to a 404 page

= 1.4 =
changed the settings layout
updated option names
organized naming system
reorganized plugin layout

= 1.3.2 =
bug fix

= 1.3 =
Added Option To Whitelist Browser

= 1.2 =
put settings under a tab which will be shared with other plugins by SwiftNinjaPro
this will help reduce the space taken up in admin menu if you use multiple plugins by SwiftNinjaPro :D

= 1.1.1 =
bug fix

= 1.0 =
First Version

== Upgrade Notice ==

= 1.10 =
Moved recovery page to front end (password protected)

= 1.9 =
Added recovery page in case ip not whitelisted (requires admin)

= 1.8.1 =
Updated Function to detect browser
Usage of new Function can be enabled in the plugin settings

= 1.8 =
Added option to add wp-admin whitelist

= 1.7.2 =
Modified block message shown when 404 redirect option is disabled
 - no longer mentions existence of a plugin
 - now provides debug suggestions (like changing wifi network or using a different browser)
Settings page now grabs admin url from a wordpress function to provide better compatibility with plugins that may change the admin url

= 1.7 =
Improved compatibility with plugins that use wp-login redirects
This new compatibility can be enabled in settings and is optional

= 1.6 =
Browser Detection Now Includes Microsoft Edge
Microsoft Edge no longer returns as Google-Chrome

= 1.5.3 =
Added option to block common proxy server IP's

= 1.5.2 =
bug fix
fixed small php error where multi-line ip list was getting phrased weird and invisible encoding for extra lines (added by default from a common php function) was causing a difference between the userIP and the ip whitelist
ip list now recognizes multiple ip's in a list

= 1.5 =
plugin now kills the page and displays a message instead of sending a non-whitelisted user to a 404 page

= 1.4 =
changed the settings layout
updated option names
organized naming system
reorganized plugin layout

= 1.3.2 =
bug fix

= 1.3 =
Added Option To Whitelist Browser

= 1.2 =
put settings under a tab which will be shared with other plugins by SwiftNinjaPro
this will help reduce the space taken up in admin menu if you use multiple plugins by SwiftNinjaPro :D

= 1.1.1 =
bug fix

= 1.0 =
First Version
