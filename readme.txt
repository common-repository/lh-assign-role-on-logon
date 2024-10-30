=== LH Assign Role On Logon ===
Contributors: shawfactor
Donate link: https://lhero.org/portfolio/lh-assign-role-on-logon/
Tags: multisite, buddypress, users, roles, multiuser, login
Requires at least: 3.0
Tested up to: 4.4
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Automatically add users to each site in your WordPress network on initial login.

== Description ==

Are you running a WordPress network? You no longer need to manually add new users to each of your sites.

With this plugin, users are assigned a default role on a new site when they log in for the first time (they authenticate against the network username and password). You set the default role for each site and this plugin applies it. If the user already has a higher role assigned they keep their role on logon

You can assign different roles for each site or keep a site private by assigning no role.

== Installation ==

1. Upload the entire `/lh-assign-role-on-login/` folder to the `/wp-content/plugins/` directory.
1. Activate the plugin.
1. Navigate to the **User Management** section of the *Network Admin > Settings > Network Settings* page. At `example.com` this page would be found at `http://example.com/wp-admin/network/settings.php`.
1. Set default roles for each of your sites.

== Frequently Asked Questions ==

= Does the plugin require a multisite installation? =

Yes, WordPress takes care of the default role on non-multisite installations.

= Where do I find the settings section =

The **User Management** section is near the bottom of the the *Network Admin > Settings > Network Settings* page. 

For `example.com` this page would be found at `http://example.com/wp-admin/network/settings.php`.

= Why aren't all my sites listed on the options page? =

Any sites archived or deleted will not appear. All others sites will appear. 

If you do not see your sites, be sure you are looking at the *Network Settings* Page found at `/wp-admin/network/settings.php` not a single site settings, found at `/wp-admin/settings.php`.

= Does this plugin assign the default role to existing users? =

No, a role is only assigned to new users or if the user who is logging in has no role on the site currently or if the default role is higher than the users currently assigned role

= Will these roles be allocated to new users registering with a new site? =

Yes, users registering with a site will receive the existing default roles. 

The new site will not have a default role until it is manually set. Once set, all existing users will receive that role for the new site.


== Changelog ==

**1.00 July 13, 2015**  
Initial release.

== Changelog ==
**1.01 December 2015**  
Initial release.

== Changelog ==
**1.04 June 25 2016**  
Code Improvment.


