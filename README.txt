=== WordPress Mobile Edition ===
Contributors: alexkingorg, crowdfavorite
Donate link: http://crowdfavorite.com/donate/
Tags: mobile, pda, wireless, cellphone, phone, iphone, touch, webkit, android, blackberry, carrington
Minimum version: 3.0
Tested with: 3.0.1
Stable tag: 3.2

WordPress Mobile Edition is a plugin that shows an interface designed for a mobile device when visitors come to your site on a mobile device.

== Description ==

Links : 

- [Wordpress Mobile Edition plugin in action](http://mobile.carringtontheme.com).
- [Carrington Mobile theme page](http://www.carringtontheme.com).
- [Wordpress.org Wordpress Mobile Edition forum topics](http://wordpress.org/tags/wordpress-mobile-edition?forum_id=10).
- [Wordpress Mobile Edition plugin page at Crowd Favorite](http://crowdfavorite.com/wordpress/plugins/wordpress-mobile-edition/).
- [Wordpress Mobile Edition Plugin forums at Crowd Favorite](http://crowdfavorite.com/forums/forum/wordpress-mobile-edition).
- [WordPress Help Center Wordpress Mobile Edition support](http://wphelpcenter.com/plugins/wordpress-mobile-edition/).


Wordpress mobile edition will display your site using Carrington Mobile theme if the visitor is on a browser listed on the settings page.

Whether your visitors are using a mobile phone such as a Blackberry or Nokia, or a smartphone like an Android or iPhone device with touch capabilities, WordPress Mobile Edition will detect and display a different stylesheet for these two categories. This way, users with touch screens have nice big tap zones, and users with pointers or scroll wheels have shorter scroll areas.

== Installation ==

1. Drop the wordpress-mobile-edition directory in your wp-content/plugins directory
2. Click the 'Activate' link for WordPress Mobile Edition on your Plugins page (in the WordPress admin interface)

== Frequently Asked Questions ==

= Is this compatible with the WP plugin auto-upgrade feature? =

Yes, as of version 3.2 you can use the auto-upgrade feature and no longer have to move any folders.

= Is this compatible with WP (Super) Cache? =

Yes, it is compatible with WP Super Cache 0.9 (using WP Cache mode).

= Does this create a mobile admin interface too? =

No, it does not.

= Does this serve a mobile interface to mobile web search crawlers? =

Yes, to Google and Yahoo mobile search crawlers. You can add any others by adding their user agents in the plugin's Settings page.

= Does this support iPhones and other "touch" browsers? =

Yes, as of version 3.0. There is a customized interface for advanced mobile browsers and special styling to make things "finger-sized" for touch browsers.

= My mobile device isn't automatically detected, what do I do? =

Visit the settings page and use the link there to identify your mobile browser's User Agent.

Then add that to the list of mobile browsers in your settings.

= Does this conflict with other iPhone theme plugins? =

Not fundamentally. If you remove the iPhone from the list of detected browsers, then the other iPhone theme should work as normal.

= Does this support pages too? =

Yes, it does.

= Can I create a link that forces someone to see the mobile version? =

There is a widget that will do this for you in your sidebar.

The link can also be added to your theme by using the `cfmobi_mobile_link()` template tag:

`<?php if (function_exists('cfmobi_mobile_link') { cfmobi_mobile_link(); } ?>`

If you would prefer you own link text:

`<?php if (function_exists('cfmobi_mobile_link') { cfmobi_mobile_link('This is the link text'); } ?>`

To add a link to any page or post use the shortcode: [cfmobile-link] or [cfmobile-link linktext="This is the link text"] If the user is already using a mobile browser listed in the settings and not currently on the mobile theme, the plugin will automatically produce a return link after the content.

Note that neither of these work if you have WP Cache enabled.

= Why are recent posts shown on every page? =

This is a feature of the plugin to allow easy access to recent content.

= How do I customize the display of the mobile interface? =

The contents of the carrington-mobile folder are a standard WordPress theme. Any changes you make there will affect the display of the mobile interface.

== Screenshots ==

1. The mobile theme in action
2. Up close of the Mobile theme in action

== Changelog ==

= 3.2 =
- New : Widget
- New : Option for link text
- New : Support for non traditional directory structures
- New : Added shortcode for cfmobile_link [cfmobi-link]
- New : CF_Admin integration and option serialization
- Changed : Updated to carrington-mobile.1.1
- Changed : No longer need to move the theme file into the theme directory
- Bugfix : Added additional security

= 3.1 =
- New : Changelog
- New : Filter to enable other plugins to participate in mobile checking
- New : Hook to enable other plugins to add to the settings page

= 3.0.5 =
- Changed : Updated to latest Carrington mobile theme
- Changed : Added default support for webOS

= 3.0.4 =
- Changed : Updated README FAQ

= 3.0.3 =
- New : Support for Carrington mobile theme versioning

= 3.0.2 =
- New : Default support for google and yahoo mobile crawlers
- New : Check to see where the plugin file is located  /plugins or /plugins/wordpress-mobile-edition

= 3.0.1 =
- Changed : README update

= 3.0 =
- New : Compatibility with WP Super cache
- New : Added a theme based on Carrington mobile to display the mobile version
- New : Created an options page to add custom user agents
- Changed : Restructuring of how the plugin works

= 2.1 =
- New : Custom request handler
- New : Added ability for insertion of 'see mobile version' link into theme
- New : Added akm_template filter
- Bugfix : Security update

= 2.0 =
- New : Added wp-mobile theme; how/what is displayed in the mobile version
- Changed : Restructured plugin

= 1.8 = 
- New : Localization support
- New : Comment support for already logged in users
- New : Added check_mobile and mobile_redirect functions

= 1.7 =
- New : WordPress 1.2 support

= 1.0 =
- New : The first version

== API ==

There is a filter `cfmobi_check_mobile` that allows you to affect if a mobile browser is detected.

`function your_mobile_check_function($mobile_status) {

	// do your logic, set $mobile_status to true/false as needed
	
	return $mobile_status;
}
add_filter('cfmobi_check_mobile', 'your_mobile_check_function');`


There is an action `cfmobi_settings_form` that allows you to add to the settings page for this plugin. Handling form posts and other activities from anything you add to this form should be done in your plugin.

`function your_settings_form() {
	// create your form here - don't forget to catch the form submission as well
}
add_action('cfmobi_settings_form', 'your_settings_form');`


