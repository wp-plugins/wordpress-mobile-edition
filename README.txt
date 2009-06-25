=== WordPress Mobile Edition ===
Tags: mobile, pda, wireless, cellphone, phone, iphone, touch, webkit, android, blackberry, carrington
Contributors: alexkingorg
Minimum version: 2.3
Tested with: 2.8
Stable tag: 3.1

WordPress Mobile Edition is a plugin that shows an interface designed for a mobile device when visitors come to your site on a mobile device.

Mobile browsers are automatically detected, the list of mobile browsers can be customized on the settings page. 

== Installation ==

1. Drop the wp-mobile.php file in your wp-content/plugins directory
2. Drop the carrington-mobile-(version #) directory in your wp-content/themes directory
3. Click the 'Activate' link for WordPress Mobile Edition on your Plugins page (in the WordPress admin interface)


== Frequently Asked Questions ==

= Is this compatible with the WP plugin auto-upgrade feature? =

No. The mobile theme needs to be moved to your themes directory and the WP plugin auto-upgrade does not do this. For this reason, please follow the installation instructions here.


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

Yes, this is included as an experimental feature in version 2.1. The link can be added to your theme by using the akm_mobile_link() template tag:

`<?php in (function_exists('cfmobi_mobile_link') { cfmobi_mobile_link(); } ?>`

Note that this does not work if you have WP Cache enabled.


= Why are recent posts shown on every page? =

This is a feature of the plugin to allow easy access to recent content.


= How do I customize the display of the mobile interface? =

The contents of the wp-mobile folder are a standard WordPress theme. Any changes you make there will affect the display of the mobile interface.


== Examples ==

You can see the mobile theme in action here: http://mobile.carringtontheme.com


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


== Changelog ==

= 3.1 =

- Added changelog
- Added filter to enable other plugins to participate in mobile checking
- Added hook to enable other plugins to add to the settings page
