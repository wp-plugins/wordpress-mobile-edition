=== WordPress Mobile Edition ===
Tags: mobile, pda, wireless, cellphone, phone, iphone, touch, webkit, android, blackberry, carrington
Contributors: alexkingorg, crowdfavorite
Minimum version: 2.8
Tested with: 3.0.1
Stable tag: 3.2

WordPress Mobile Edition is a plugin that shows an interface designed for a mobile device when visitors come to your site on a mobile device.

Mobile browsers are automatically detected, the list of mobile browsers can be customized on the settings page. 


== Installation ==

1. Drop the wp-mobile.php file in your wp-content/plugins directory
2. Drop the carrington-mobile.(version #) directory in your wp-content/themes directory
3. Click the 'Activate' link for WordPress Mobile Edition on your Plugins page (in the WordPress admin interface)


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

Yes, this is included as an experimental feature in version 2.1. The link can be added to your theme by using the cfmobi_mobile_link() template tag:

`<?php if (function_exists('cfmobi_mobile_link') { cfmobi_mobile_link(); } ?>`

If you would prefer you own link text:

`<?php if (function_exists('cfmobi_mobile_link') { cfmobi_mobile_link('This is the link text'); } ?>`

To add a link to any page or post use the shortcode: [cfmobile-link] or [cfmobile-link linktext="This is the link text"] If the user is already using a mobile browser listed in the settings and not currently on the mobile theme, the plugin will automatically produce a return link after the content.

Note that neither of these work if you have WP Cache enabled.

= Why are recent posts shown on every page? =

This is a feature of the plugin to allow easy access to recent content.

= How do I customize the display of the mobile interface? =

The contents of the carrington-mobile folder are a standard WordPress theme. Any changes you make there will affect the display of the mobile interface.


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

= 3.2 =

- Updated to carrington-mobile.1.1
- Updated changelog for previous versions
- Added additional security
- Support for non traditional directory structures
- Created a working-html directory
- Added the option of custom link text for clfmobi_mobile_link()
- Included options for default Exit and Return mobile version text. 
- Added shortcode for cfmobile_link [cfmobi-link]
- No longer need to move the theme file into the theme directory

= 3.1 =

- Added changelog
- Added filter to enable other plugins to participate in mobile checking
- Added hook to enable other plugins to add to the settings page


= 3.0.5 =

- Updated Carrington mobile theme
- Added default support for webOS


= 3.0.4 =

- Updated README FAQ


= 3.0.3 =

- Support for Carrington mobile theme versioning


= 3.0.2 =

- Default support for google and yahoo mobile crawlers
- Check to see where the plugin file is located  /plugins or /plugins/wordpress-mobile-edition


= 3.0.1 =

- Readme update


= 3.0 =

- Compatibility with WP Super cache
- Restructuring of how the plugin works
- Added a theme based on Carrington mobile to display the mobile version
- Created an options page to add custom user agents


= 2.1 =

- Added custom request handler
- Security update
- Added ability for insertion of 'see mobile version' link into theme
- Added akm_template filter


= 2.0 =

- Added wp-mobile theme; how/what is displayed in the mobile version
- Restructured plugin


= 1.8 = 

- Localization support
- Comment support for already logged in users
- Added check_mobile and mobile_redirect functions


= 1.7 =

- WordPress 1.2 support



