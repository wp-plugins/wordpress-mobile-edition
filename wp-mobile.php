<?php
/*
Plugin Name: WordPress Mobile Edition 
Plugin URI: http://crowdfavorite.com/wordpress/plugins/wordpress-mobile-edition/
Description: Show your mobile visitors a site presentation designed just for them. Rich experience for iPhone, Android, etc. and clean simple formatting for less capable mobile browsers. Cache-friendly with a Carrington-based theme, and progressive enhancement for advanced mobile browsers.  
Version: 3.2
Author: Crowd Favorite
Author URI: http://crowdfavorite.com
*/

// Copyright (c) 2007-2010 
//   Crowd Favorite, Ltd. - http://crowdfavorite.com
//   Alex King - http://alexking.org
// All rights reserved.
//
// Released under the GPL license
// http://www.opensource.org/licenses/gpl-license.php
//
// This is an add-on for WordPress - http://wordpress.org
//
// **********************************************************************
// This program is distributed in the hope that it will be useful, but
// WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
// **********************************************************************

// ini_set('display_errors', '1'); ini_set('error_reporting', E_ALL);
define('CFMOBI_VERSION', '3.2');

load_plugin_textdomain('wordpress-mobile-edition');

define('CF_MOBILE_THEME', 'carrington-mobile.1.1');

if (is_file(trailingslashit(WP_PLUGIN_DIR).'wp-mobile.php')) {
	define('CFMOBI_FILE', trailingslashit(WP_PLUGIN_DIR).'wp-mobile.php');
	define('CFMOBI_DIR_URL', trailingslashit(WP_PLUGIN_URL));
	define('CF_ADMIN_DIR', '/cf-admin/'); 
}
else if (is_file(trailingslashit(WP_PLUGIN_DIR).'wordpress-mobile-edition/wp-mobile.php')) {
	define('CFMOBI_FILE', trailingslashit(WP_PLUGIN_DIR).'wordpress-mobile-edition/wp-mobile.php');
	define('CFMOBI_DIR_URL', trailingslashit(WP_PLUGIN_URL).'wordpress-mobile-edition/');
	define('CF_ADMIN_DIR', 'wordpress-mobile-edition/cf-admin/'); 
}

require_once('cf-admin/cf-admin.php');

function cfmobi_activate() {
	if (cfmobi_is_multisite() && cfmobi_is_network_activation()) {
		cfmobi_network_activate();
	}
	else {
		cfmobi_activate_single();
	}
}
register_activation_hook(CFMOBI_FILE, 'cfmobi_activate');

function cfmobi_activate_single() {
	cfmobi_update_settings();
}

function cfmobi_network_activate() {
	CF_Admin::activate_for_network('cfmobi_activate_single');
}

function cfmobi_default_browsers($type = 'mobile') {
	$mobile = array(
		'2.0 MMP',
		'240x320',
		'400X240',
		'AvantGo',
		'BlackBerry',
		'Blazer',
		'Cellphone',
		'Danger',
		'DoCoMo',
		'Elaine/3.0',
		'EudoraWeb',
		'Googlebot-Mobile',
		'hiptop',
		'IEMobile',
		'KYOCERA/WX310K',
		'LG/U990',
		'MIDP-2.',
		'MMEF20',
		'MOT-V',
		'NetFront',
		'Newt',
		'Nintendo Wii',
		'Nitro', // Nintendo DS
		'Nokia',
		'Opera Mini',
		'Palm',
		'PlayStation Portable',
		'portalmmm',
		'Proxinet',
		'ProxiNet',
		'SHARP-TQ-GX10',
		'SHG-i900',
		'Small',
		'SonyEricsson',
		'Symbian OS',
		'SymbianOS',
		'TS21i-10',
		'UP.Browser',
		'UP.Link',
		'webOS', // Palm Pre, etc.
		'Windows CE',
		'WinWAP',
		'YahooSeeker/M1A1-R2D2',
	);
	$touch = array(
		'iPhone',
		'iPod',
		'Android',
		'BlackBerry9530',
		'LG-TU915 Obigo', // LG touch browser
		'LGE VX',
		'webOS', // Palm Pre, etc.
		'Nokia5800',
	);
	switch ($type) {
		case 'mobile':
		case 'touch':
			return $$type;
	}
}

function cfmobi_setup_theme() {
	if (!is_admin() && cfmobi_check_mobile()) {
		add_filter('theme_root', 'cfmobi_set_theme_root');
		add_filter('theme_root_uri', 'cfmobi_set_theme_root_uri');
		add_filter('template', 'cfmobi_set_template');
		add_filter('stylesheet', 'cfmobi_set_stylesheet');
	}
}
add_action('setup_theme', 'cfmobi_setup_theme');

function cfmobi_init() {
	// Loads the global values for the carrington theme to use when deciding to include touch css or not
	cfmobi_get_mobile_browsers();
	cfmobi_get_touch_browsers();
	
	if (isset($_COOKIE['cf_mobile']) && $_COOKIE['cf_mobile'] == 'false') {
		add_action('the_content', 'cfmobi_mobile_available');
	}
}
add_action('init', 'cfmobi_init');

function cfmobi_set_theme_root() {
	return dirname(CFMOBI_FILE);
}

function cfmobi_set_theme_root_uri() {
	return CFMOBI_DIR_URL;	
}

function cfmobi_set_template() {
	return CF_MOBILE_THEME;
}

function cfmobi_set_stylesheet() {
	return CF_MOBILE_THEME;
}

function cfmobi_check_mobile() {
	$mobile = null;
	if (!isset($_SERVER["HTTP_USER_AGENT"]) || (isset($_COOKIE['cf_mobile']) && $_COOKIE['cf_mobile'] == 'false')) {
		$mobile = false;
	}
	else if (isset($_COOKIE['cf_mobile']) && $_COOKIE['cf_mobile'] == 'true') {
		$mobile = true;
	}
	else if (!isset($mobile) && cfmobi_agent_is_mobile()) {
		$mobile = true;
	}
	else {
		$mobile = false;
	}
	return apply_filters('cfmobi_check_mobile', $mobile);
}

function cfmobi_agent_is_mobile() {
	$browsers = cfmobi_get_merged_browsers();
	if (count($browsers)) {
		foreach ($browsers as $browser) {
			if (!empty($browser) && strpos($_SERVER["HTTP_USER_AGENT"], trim($browser)) !== false) {
				return true;
			}
		}
	}
	return false;
}

function cfmobi_mobile_exit() {	
	echo '<p><a href="?cf_action=reject_mobile">'.esc_html(cfmobi_get_setting('cfmobi_exit_link_text')).'</a> <span class="small">'.esc_html(cfmobi_get_setting('cfmobi_exit_link_subtext')).'</span>.</p>';
}

function cfmobi_mobile_available($content) {
	// Checking for WP Cache
	if (!defined('WPCACHEHOME')) {
		$content .= '<p><a href="?cf_action=show_mobile">'.esc_html(cfmobi_get_setting('cfmobi_return_link_text')).'</a>.</p>';
	}
	return $content;
}

function cfmobi_shortcode_link($atts) {
	extract(shortcode_atts(array(
		'linktext' => '',
	), $atts));

	cfmobi_mobile_link($linktext);
}
add_shortcode('cfmobi-link', 'cfmobi_shortcode_link');

function cfmobi_mobile_link($link_text = null) {
	if (!defined('WPCACHEHOME') && get_template() != CF_MOBILE_THEME) {
		if (empty($link_text)) {
			$link_text = __('View the Mobile Edition', 'wordpress-mobile-edition');
		}
		echo '<a href="?cf_action=show_mobile">'.$link_text.'</a>';
	}
}

function cfmobi_request_handler() {
	if (!empty($_GET['cf_action'])) {
		$url = parse_url(get_bloginfo('home'));
		$domain = $url['host'];
		if (!empty($url['path'])) {
			$path = $url['path'];
		}
		else {
			$path = '/';
		}
		$redirect = false;
		switch ($_GET['cf_action']) {
			case 'cfmobi_admin_js':
				cfmobi_admin_js();
				break;
			case 'reject_mobile':
				setcookie(
					'cf_mobile',
					'false',
					time() + 300000,
					'/',//$path,  //  use   '/',            for local cookie support 
					false,false//$domain //  use   false, false    for local cookie support 
				);
				$redirect = true;
				break;
			case 'show_mobile':
				setcookie(
					'cf_mobile',
					'true',
					time() + 300000,
					'/',//$path,  //  use   '/',            for local cookie support 
					false,false//$domain //  use   false, false    for local cookie support
				);
				$redirect = true;
				break;
			case 'cfmobi_who':
				if (current_user_can('manage_options')) {
					header("Content-type: text/plain");
					echo sprintf(__('Browser: %s', 'wordpress-mobile-edition'), strip_tags($_SERVER['HTTP_USER_AGENT']));
					die();
				}
				break;
		}
		if ($redirect) {
			if (!empty($_SERVER['SERVER_NAME']) && !empty($_SERVER['REQUEST_URI'])) {
				$go = cfmobi_clean_url();
			}
			else {
				$go = get_bloginfo('home');
			}
			header('Location: '.$go);
			die();
		}
	}
	if (!empty($_POST['cf_action'])) {
		switch ($_POST['cf_action']) {
			case 'cfmobi_update_settings':
				if (!check_admin_referer('cfmobi', 'cfmobi_settings_nonce')) {
					die();
				}
				cfmobi_update_settings();
				wp_redirect(admin_url('options-general.php?page=wp-mobile.php&updated=true'));
				die();
				break;
		}
	}
}
add_action('init', 'cfmobi_request_handler');

function cfmobi_clean_url() {
	$url = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
	// Slicker ways to do this, but not too many to check here
	$url = str_replace('?cf_action=show_mobile&', '?', $url);
	$url = str_replace('?cf_action=reject_mobile&', '?', $url);
	$url = str_replace('?cf_action=show_mobile', '', $url);
	$url = str_replace('?cf_action=reject_mobile', '', $url);
	$url = str_replace('&cf_action=show_mobile', '', $url);
	$url = str_replace('&cf_action=reject_mobile', '', $url);

	return esc_url($url);
}

function cfmobi_admin_js() {
	header('Content-type: text/javascript');
	$mobile = str_replace(array("'","\r", "\n"), array("\'", '', ''), implode('\\n', cfmobi_default_browsers('mobile')));
	$touch = str_replace(array("'","\r", "\n"), array("\'", '', ''), implode('\\n', cfmobi_default_browsers('touch')));
?>
jQuery(function($) {
	$('#cfmobi_mobile_reset').click(function() {
		$('#cfmobi_mobile_browsers').val('<?php echo $mobile; ?>');
		return false;
	});
	$('#cfmobi_touch_reset').click(function() {
		$('#cfmobi_touch_browsers').val('<?php echo $touch; ?>');
		return false;
	});
});
<?php
	die();
}

function cfmobi_admin_init() {
	if ($_GET['page'] == basename(__FILE__)) {
		CF_Admin::load_js();
		CF_Admin::load_css();
		wp_enqueue_script('cfmobi_js', admin_url('?cf_action=cfmobi_admin_js'), array('jquery'));
	}	
}
add_action('admin_init', 'cfmobi_admin_init');

global $cfmobi_settings;
$cfmobi_settings = array(
	'cfmobi_mobile_browsers' => array(
		'type' => 'textarea',
		'label' => __('Mobile Browsers', 'wordpress-mobile-edition').' <a href="#" id="cfmobi_mobile_reset">'.__('Reset to Default', 'wordpress-mobile-edition').'</a>',
		'default' => implode("\n", cfmobi_default_browsers('mobile')),
		'help' => '',
		'div_class' => 'cf-elm-width-300',
	),
	'cfmobi_touch_browsers' => array(
		'type' => 'textarea',
		'label' => __('Touch Browsers', 'wordpress-mobile-edition').' <a href="#" id="cfmobi_touch_reset">'.__('Reset to Default', 'wordpress-mobile-edition').'</a>',
		'default' => implode("\n", cfmobi_default_browsers('touch')),
		'help' => '(iPhone, Android G1, BlackBerry Storm, etc.)',
		'help_class' => 'cf-elm-align-bottom',
		'div_class' => 'cf-elm-width-300',
	),
	'cfmobi_return_link_text' => array(
		'type' => 'text',
		'label' => __('Return to mobile link-text', 'wordpress-mobile-edition'),
		'default' => __('Return to the Mobile Edition', 'wordpress-mobile-edition'),
		'help' => '',
		'div_class' => 'cf-elm-width-300',
	),
	'cfmobi_exit_link_text' => array(
		'type' => 'text',
		'label' => __('Exit mobile version link-text', 'wordpress-mobile-edition'),
		'default' => __('Exit the Mobile Edition', 'wordpress-mobile-edition'),
		'help' => '',
		'div_class' => 'cf-elm-width-300',
	),
	'cfmobi_exit_link_subtext' => array(
		'type' => 'text',
		'label' => __('	Exit mobile version link description', 'wordpress-mobile-edition'),
		'default' => __('(view the standard browser version)', 'wordpress-mobile-edition'),
		'help' => '',
		'div_class' => 'cf-elm-width-300',		
	),
);

function cfmobi_admin_menu() {
	add_options_page(
		__('WordPress Mobile Edition', 'wordpress-mobile-edition'), 
		__('Mobile', 'wordpress-mobile-edition'), 
		'manage_options', 
		basename(CFMOBI_FILE), 
		'cfmobi_settings_form'
	);
}
add_action('admin_menu', 'cfmobi_admin_menu');

function cfmobi_plugin_action_links($links, $file) {
	return CF_Admin::plugin_action_links($links, $file, CFMOBI_FILE, 'wordpress-mobile-edition');
}
add_filter('plugin_action_links', 'cfmobi_plugin_action_links', 10, 2);

function cfmobi_settings_form() {
	global $cfmobi_settings;
	echo('
<div id="cf" class="wrap">
	<div id="cf-header"> 
	');
	CF_Admin::admin_header(__('WordPress Mobile Edition Settings', 'wordpress-mobile-edition'), 'Wordpress Mobile Edition', CFMOBI_VERSION, 'wordpress-mobile-edition');
	echo('
	</div>
	<p>'.__('Browsers that have a <a href="http://en.wikipedia.org/wiki/User_agent">User Agent</a> matching a key below will be shown the mobile version of your site instead of the normal theme.', 'wordpress-mobile-edition').' <a href="'.admin_url('options.php?cf_action=cfmobi_who').'">Check your user agent</a></p>
	');
	CF_Admin::settings_form($cfmobi_settings, 'cfmobi', 'wordpress-mobile-edition');
	do_action('cfmobi_settings_form');
	CF_Admin::callouts('wordpress-mobile-edition');
	
	echo '</div>';	
}

function cfmobi_update_settings() {
	if (!is_admin() || !current_user_can('manage_options')) {
		return;
	}
	global $cfmobi_settings;
	CF_Admin::update_settings($cfmobi_settings, 'cfmobi');
}

function cfmobi_get_setting($setting) {
	return CF_Admin::get_setting($setting, 'cfmobi');
}

class CFmobi_Widget extends WP_Widget {
	function CFmobi_Widget() {
        parent::WP_Widget(false, $name = 'WordPress Mobile Edition');	
	}

	function widget($args, $instance) {		
		extract( $args );
		$link_text = $instance['link_text'];
		$title = esc_html($instance['title']);
		
		if (!defined('WPCACHEHOME')) {
		
			echo $before_widget;
		
			if (!empty($title)) {
				echo $before_title.$title.$after_title;
			}
			cfmobi_mobile_link($link_text);	
		
			echo $after_widget;
		}
	}

	function update($new_instance, $old_instance) {				
		$instance = $old_instance;
		$instance['link_text'] = strip_tags($new_instance['link_text']);
		$instance['title'] = strip_tags($new_instance['title']);
		return $instance;
	}

	function form($instance) {				
 		$link_text = esc_html($instance['link_text']);
		$title = esc_html($instance['title']);
 		
		echo('
 			<p>'.__('Display a link that forces a user to see the mobile version (Does not display using WP Super Cache)','wordpress-mobile-edition').'</p>
			<p><label for="'.$this->get_field_id('title').'">'.__('Title', 'wordpress-mobile-edition').'<input class="widefat" id="'. $this->get_field_id('title').'" name="'.$this->get_field_name('title').'" type="text" value="'.$title.'" /></label></p>
		
 			<p><label for="'.$this->get_field_id('link_text').'">'.__('Link Text', 'wordpress-mobile-edition').'<input class="widefat" id="'. $this->get_field_id('link_text').'" name="'.$this->get_field_name('link_text').'" type="text" value="'.$link_text.'" /></label></p>
        ');
	}
}
add_action('widgets_init', create_function('', 'return register_widget("CFmobi_Widget");'));

function cfmobi_get_merged_browsers() {
	return array_merge(cfmobi_get_mobile_browsers(), cfmobi_get_touch_browsers());
}

function cfmobi_get_mobile_browsers() {
	global $cfmobi_mobile_browsers;
	$mobile = explode("\n", trim(cfmobi_get_setting('cfmobi_mobile_browsers')));
	$cfmobi_mobile_browsers = apply_filters('cfmobi_mobile_browsers', $mobile);
	return $cfmobi_mobile_browsers;
}

function cfmobi_get_touch_browsers() {
	global $cfmobi_touch_browsers;
	$touch = explode("\n", trim(cfmobi_get_setting('cfmobi_touch_browsers')));
	$cfmobi_touch_browsers = apply_filters('cfmobi_touch_browsers', $touch);
	return $cfmobi_touch_browsers;
}

//multisite

function cfmobi_is_multisite() {
	return CF_Admin::is_multisite();
}

function cfmobi_is_network_activation() {
	return CF_Admin::is_network_activation();
}

function cfmobi_activate_plugin_for_new_blog() {
	CF_Admin::activate_plugin_for_new_blog('cfmobi_activate_single');
}
add_action('new_blog', 'cfmobi_activate_plugin_for_new_blog');
?>