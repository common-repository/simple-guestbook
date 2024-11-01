=== Simple Guestbook ===
Contributors: dichternebel
Donate link: https://ko-fi.com/dichternebel
Tags: guestbook, comments, paging, navigation
Requires at least: 5.2
Requires PHP: 5.6.20
Tested up to: 6.6
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A simple guestbook plugin based on WordPress page comments.

== Description ==

This plugin is based on the comments feature from WordPress and creates a paged output that can be displayed in a WordPress **page** by simply putting the shortcode `[simple-guestbook]` as its content.

Since the plugin just uses existing core functionality it should respect all WP settings and integrate seemless into most of the themes out there.

You can tweak some basic settings in the options section of the plugin like:

* sort order
* entries per page
* avatar size
* custom avatar
* reply functionality for editors
* JavaScript based validation for the WP comment form

== Manual Installation ==

1. Download `simple-guestbook[version].zip` and unzip to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Change settings in the 'Options' menu as needed or leave them default

== Usage ==

Just place the shortcode `[simple-guestbook]` in an (empty) WordPress page. If you like to have some small content on that page, please make sure to put the shortcode at the very end of the page.

== Frequently Asked Questions ==

= I can not add any comment to the guestbook page so what is wrong? =

Please check the settings of your page where you put in the shortcode and make sure that the option "Allow Comments" is enabled. 

== Screenshots ==

1. The admin page of Simple Guestbook

== Changelog ==

= 1.0.0 =
* Inital version.

== Upgrade Notice ==

= 1.0.0 =
It's free! Go! Get it!

== Arbitrary section ==

Since this plugin uses WP comments I highly recommend that you protect yourself against spam by using e.g. at least one of these plugins:

* [hCaptcha](https://wordpress.org/plugins/hcaptcha-for-forms-and-more/)
* [Antispam Bee](https://wordpress.org/plugins/antispam-bee/)
* [Honeypot Toolkit](https://wordpress.org/plugins/honeypot-toolkit/)

This plugin was tested with hCaptcha and the included JavaScript Validation functionality for the comment form comes with an integration for hCaptcha already.

Enjoy!

--

Banner image by [Pexels](https://pixabay.com/users/pexels-2286921/?utm_source=link-attribution&utm_medium=referral&utm_campaign=image&utm_content=1866992) from [Pixabay](https://pixabay.com//?utm_source=link-attribution&utm_medium=referral&utm_campaign=image&utm_content=1866992)
