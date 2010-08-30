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

load_plugin_textdomain('cf-mobile');

define('CF_MOBILE_THEME', 'carrington-mobile.1.1');
define('CF_TEST_DIR', 'wordpress-mobile-edition'); //Used in local testing, comment out on production

if (is_file(trailingslashit(WP_PLUGIN_DIR).'wp-mobile.php')) {
	define('CFMOBI_FILE', trailingslashit(WP_PLUGIN_DIR).'wp-mobile.php');
	define('CFMOBI_DIR_URL', trailingslashit(WP_PLUGIN_URL));
}
else if (is_file(trailingslashit(WP_PLUGIN_DIR).'wordpress-mobile-edition/wp-mobile.php')) {
	define('CFMOBI_FILE', trailingslashit(WP_PLUGIN_DIR).'wordpress-mobile-edition/wp-mobile.php');
	define('CFMOBI_DIR_URL', trailingslashit(WP_PLUGIN_URL).'wordpress-mobile-edition/');
}

require_once(trailingslashit(dirname(CFMOBI_FILE)) . 'admin-ui/cf-admin-ui.php');
register_activation_hook(CFMOBI_FILE, 'cfmobi_activate');

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

function cfmobi_activate() {
	if (cfmobi_is_multisite_and_network_activation()) {
		cfmobi_activate_for_network();
	}
	else {
		cfmobi_activate_single();
	}
}
function cfmobi_activate_single() {			
	add_option('cfmobi_mobile_browsers', implode("\n", cfmobi_default_browsers('mobile')));
	add_option('cfmobi_touch_browsers', implode("\n", cfmobi_default_browsers('touch')));
	add_option('cfmobi_return_link_text', __('Return to the Mobile Edition', 'cf-mobile' ));
	add_option('cfmobi_exit_link_text', __('Exit the Mobile Edition', 'cf-mobile'));
	add_option('cfmobi_exit_link_subtext', __('(view the standard browser version)', 'cf-mobile'));
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
	echo '<p><a href="?cf_action=reject_mobile">'.esc_html(get_option('cfmobi_exit_link_text')).'</a> <span class="small">'.esc_html(get_option('cfmobi_exit_link_subtext')).'</span>.</p>';
}

function cfmobi_mobile_available($content) {
	// Checking for WP Cache
	if (!defined('WPCACHEHOME')) {
		$content .= '<p><a href="?cf_action=show_mobile">'.esc_html(get_option('cfmobi_return_link_text')).'</a>.</p>';
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
			$link_text = __('View the Mobile Edition', 'cf-mobile');
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
					echo sprintf(__('Browser: %s', 'cf-mobile'), strip_tags($_SERVER['HTTP_USER_AGENT']));
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
				if (!check_admin_referer('cfmobi', 'cfmobi-settings-nonce')) {
					die();
				}
				cfmobi_save_settings();
				wp_redirect(trailingslashit(get_bloginfo('wpurl')).'wp-admin/options-general.php?page=wp-mobile.php&updated=true');
				die();
				break;
		}
	}
}
add_action('init', 'cfmobi_request_handler');

function cfmobi_clean_url()
{
	$url = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
	// Cleaner ways to do this, but not too many to check here
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
	if (is_admin() && $_GET['page'] == basename(__FILE__)) {
		CF_Admin_UI::cf_load_js();
		CF_Admin_UI::cf_load_css();
	}	
}
add_action('admin_init', 'cfmobi_admin_init');

function cfmobi_admin_head() {
	if (is_admin() && $_GET['page'] == basename(__FILE__)) {
		echo '<link rel="stylesheet" type="text/css" href="'.trailingslashit(get_bloginfo('url')).'?cf_action=cfmobi_admin_css" />';
	}
}
add_action('admin_head', 'cfmobi_admin_head');

$cfmobi_settings = array(
	'cfmobi_mobile_browsers' => array(
		'type' => 'textarea',
		'label' => __('Mobile Browsers', 'cf-mobile').' <a href="#" id="cfmobi_mobile_reset">'.__('Reset to Default', 'cf-mobile').'</a>',
		'default' => cfmobi_default_browsers('mobile'),
		'help' => '',
	),
	'cfmobi_touch_browsers' => array(
		'type' => 'textarea',
		'label' => __('Touch Browsers', 'cf-mobile').' <a href="#" id="cfmobi_touch_reset">'.__('Reset to Default', 'cf-mobile').'</a>',
		'default' => __('Return to the Mobile Edition', 'cf_mobi' ),
		'help' => '(iPhone, Android G1, BlackBerry Storm, etc.)',
	),
	'cfmobi_return_link_text' => array(
		'type' => 'text',
		'label' => __('Return to mobile link-text', 'cf-mobile'),
		'default' => __('Return to the Mobile Edition', 'cf-mobile'),
		'help' => '',
	),
	'cfmobi_exit_link_text' => array(
		'type' => 'text',
		'label' => __('Exit mobile version link-text', 'cf-mobile'),
		'default' => __('Exit the Mobile Edition', 'cf-mobile'),
		'help' => '',
	),
	'cfmobi_exit_link_subtext' => array(
		'type' => 'text',
		'label' => __('	Exit mobile version link description', 'cf-mobile'),
		'default' => __('(view the standard browser version)', 'cf-mobile'),
		'help' => '',
	),
);

function cfmobi_setting($option) {
	$value = get_option($option);
	if (empty($value)) {
		global $cfmobi_settings;
		$value = $cfmobi_settings[$option]['default'];
	}
	return $value;
}

function cfmobi_admin_menu() {
	if (current_user_can('manage_options')) {
		add_options_page(
			__('WordPress Mobile Edition', 'cf-mobile'), 
			__('Mobile', 'cf-mobile'), 
			10, 
			basename(CFMOBI_FILE), 
			'cfmobi_settings_form'
		);
	}
}
add_action('admin_menu', 'cfmobi_admin_menu');

function cfmobi_plugin_action_links($links, $file) {
	$plugin_file = basename(__FILE__);
	if (basename($file) == $plugin_file) {
		$settings_link = '<a href="options-general.php?page='.$plugin_file.'">'.__('Settings', 'cf-mobile').'</a>';
		array_unshift($links, $settings_link);
	}
	return $links;
}
add_filter('plugin_action_links', 'cfmobi_plugin_action_links', 10, 2);


function cfmobi_settings_form() {
	global $cfmobi_settings;
	
 	print('<div id="cf" class="wrap">
			<h2>'.__('WordPress Mobile Edition Settings', 'cf-mobile').'</h2>');
	CF_Admin_UI::cf_settings_form($cfmobi_settings, 'cfmobi', 'cf-mobile');
	do_action('cfmobi_settings_form');
	CF_Admin_UI::cf_callouts();
	print('</div>');
	
}

function cfmobi_save_settings() {
	if (!current_user_can('manage_options')) {
		return;
	}
	global $cfmobi_settings;
	foreach ($cfmobi_settings as $key => $option) {
		$value = '';
		switch ($option['type']) {
			case 'int':
				$value = intval($_POST[$key]);
				break;
			case 'select':
				$test = stripslashes($_POST[$key]);
				if (isset($option['options'][$test])) {
					$value = $test;
				}
				break;
			case 'string':
			case 'textarea':
			default:
				$value = stripslashes($_POST[$key]);
				break;
		}
		update_option($key, $value);
	}
}

class CFmobi_Widget extends WP_Widget {
	function CFmobi_Widget() {
        parent::WP_Widget(false, $name = 'WordPress Mobile Edition');	
	}

	function widget($args, $instance) {		
		extract( $args );
		$link_text = $instance['link_text'];
		cfmobi_mobile_link($link_text);	
	}

	function update($new_instance, $old_instance) {				
		$instance = $old_instance;
		$instance['link_text'] = strip_tags($new_instance['link_text']);
		return $instance;
	}

	function form($instance) {				
 		$link_text = esc_html($instance['link_text']);
		print('
			<p>'.__('Display a link that forces a user to see the mobile version' . 'cf-mobile') . '</p>
 			<p><label for="' . $this->get_field_id('link_text') . '">' . __('Link Text', 'cf-mobile') . '<input class="widefat" id="'. $this->get_field_id('link_text') . '" name="' . $this->get_field_name('link_text') . '" type="text" value="' . $link_text .'" /></label></p>
        ');
	}
}
add_action('widgets_init', create_function('', 'return register_widget("CFmobi_Widget");'));

function cfmobi_get_merged_browsers() {
	return array_merge(cfmobi_get_mobile_browsers(), cfmobi_get_touch_browsers());
}

function cfmobi_get_mobile_browsers() {
	global $cfmobi_mobile_browsers;
	$mobile = explode("\n", trim(get_option('cfmobi_mobile_browsers')));
	$cfmobi_mobile_browsers = apply_filters('cfmobi_mobile_browsers', $mobile);
	return $cfmobi_mobile_browsers;
}

function cfmobi_get_touch_browsers() {
	global $cfmobi_touch_browsers;
	$touch = explode("\n", trim(get_option('cfmobi_touch_browsers')));
	$cfmobi_touch_browsers = apply_filters('cfmobi_touch_browsers', $touch);
	return $cfmobi_touch_browsers;
}

// Multisite support/utility functions
function cfmobi_get_site_blogs() {
	global $wpdb;
	return $wpdb->get_col("
		SELECT blog_id
		FROM $wpdb->blogs
		WHERE site_id = '{$wpdb->siteid}'
		AND deleted = 0
	");	
}

function cfmobi_is_multisite_and_network_activation() {
	return (function_exists('is_multisite') && is_multisite() &&
			isset($_GET['networkwide']) && ($_GET['networkwide'] == 1));
}

function cfmobi_activate_for_network() {
	$blogs = cfmobi_get_site_blogs();
	foreach ($blogs as $blog_id) {
		switch_to_blog($blog_id);
		cfmobi_activate_single();
		restore_current_blog();
	}
	return;
}

function cfmobi_new_blog($blog_id) {
	if (is_plugin_active_for_network(plugin_basename(CFMOBI_FILE))) {
		switch_to_blog($blog_id);
		cfmobi_activate_single();
		restore_current_blog();
	}	
}
add_action( 'wpmu_new_blog', 'cfmobi_new_blog');

?>