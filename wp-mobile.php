<?php

// WordPress Mobile Edition
//
// Copyright (c) 2002-2006 Alex King
// http://alexking.org/projects/wordpress
//
// Released under the GPL license
// http://www.opensource.org/licenses/gpl-license.php
//
// **********************************************************************
// This program is distributed in the hope that it will be useful, but
// WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
// *****************************************************************

/*
Plugin Name: WordPress Mobile Edition
Plugin URI: http://alexking.org/projects/wordpress
Description: Show a mobile view of the post/page if the visitor is on a known mobile device. Questions on configuration, etc.? Make sure to read the README.
Author: Alex King
Author URI: http://alexking.org
Version: 2.1dev
*/ 

$_SERVER['REQUEST_URI'] = ( isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $_SERVER['SCRIPT_NAME'] . (( isset($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '')));

function akm_check_mobile() {
	if (!isset($_SERVER["HTTP_USER_AGENT"]) || (isset($_COOKIE['akm_mobile']) && $_COOKIE['akm_mobile'] == 'false')) {
		return false;
	}
	if (akm_mobile_exclude()) {
		return false;
	}
	if (isset($_COOKIE['akm_mobile']) && $_COOKIE['akm_mobile'] == 'true') {
		return true;
	}
	$whitelist = array(
		'Stand Alone/QNws'
	);
	foreach ($whitelist as $browser) {
		if (strstr($_SERVER["HTTP_USER_AGENT"], $browser)) {
			return false;
		}
	}
	$small_browsers = array(
		'2.0 MMP'
		,'240x320'
		,'AvantGo'
		,'BlackBerry'
		,'Blazer'
		,'Cellphone'
		,'Danger'
		,'DoCoMo'
		,'Elaine/3.0'
		,'EudoraWeb'
		,'hiptop'
		,'KYOCERA/WX310K'
		,'MIDP-2.0'
		,'MMEF20'
		,'MOT-V'
		,'NetFront'
		,'Newt'
		,'Nintendo Wii'
		,'Nitro' // Nintendo DS
		,'Nokia'
		,'Opera Mini'
		,'Palm'
		,'Playstation Portable'
		,'portalmmm'
		,'Proxinet'
		,'ProxiNet'
		,'SHARP-TQ-GX10'
		,'Small'
		,'SonyEricsson'
		,'Symbian OS'
		,'SymbianOS'
		,'TS21i-10'
		,'UP.Browser'
		,'UP.Link'
		,'Windows CE'
		,'WinWAP'
	);

	foreach ($small_browsers as $browser) {
		if (strstr($_SERVER["HTTP_USER_AGENT"], $browser)) {
			return true;
		}
	}
	return false;
}

function akm_mobile_exclude() {
	$exclude = false;
	$pages_to_exclude = array(
		'wp-admin'
		,'wp-comments-post.php'
		,'wp-mail.php'
		,'wp-login.php'
	);
	foreach ($pages_to_exclude as $exclude) {
		if (strstr(strtolower($_SERVER['REQUEST_URI']), $exclude)) {
			$exclude = true;
		}
	}
	return $exclude;
}

function akm_template($theme) {
	return apply_filters('akm_template', 'wp-mobile');
}

function akm_mobile_available($content) {
	return $content.'<p><a href="'.get_bloginfo('wpurl').'/wp-admin/options-general.php?ak_action=accept_mobile">Return to the Mobile Edition</a>.</p>';
}

if (!function_exists('ak_recent_posts')) {
// this is based almost entirely on:
/*
Plugin Name: Recent Posts
Plugin URI: http://mtdewvirus.com/code/wordpress-plugins/
Description: Returns a list of the most recent posts.
Version: 1.07
Author: Nick Momrik
Author URI: http://mtdewvirus.com/
*/
	function ak_recent_posts($count = 5, $before = '<li>', $after = '</li>', $hide_pass_post = true, $skip_posts = 0, $show_excerpts = false, $where = '', $join = '', $groupby = '') {
		global $wpdb;
		$time_difference = get_settings('gmt_offset');
		$now = gmdate("Y-m-d H:i:s",time());
	
		$join = apply_filters('posts_join', $join);
		$where = apply_filters('posts_where', $where);
		$groupby = apply_filters('posts_groupby', $groupby);
		if (!empty($groupby)) { $groupby = ' GROUP BY '.$groupby; }
	
		$request = "SELECT ID, post_title, post_excerpt FROM $wpdb->posts $join WHERE post_status = 'publish' AND post_type != 'page' ";
		if ($hide_pass_post) $request .= "AND post_password ='' ";
		$request .= "AND post_date_gmt < '$now' $where $groupby ORDER BY post_date DESC LIMIT $skip_posts, $count";
		$posts = $wpdb->get_results($request);
		$output = '';
		if ($posts) {
			foreach ($posts as $post) {
				$post_title = stripslashes($post->post_title);
				$permalink = get_permalink($post->ID);
				$output .= $before . '<a href="' . $permalink . '" rel="bookmark" title="Permanent Link: ' . htmlspecialchars($post_title, ENT_COMPAT) . '">' . htmlspecialchars($post_title) . '</a>';
				if($show_excerpts) {
					$post_excerpt = stripslashes($post->post_excerpt);
					$output.= '<br />' . $post_excerpt;
				}
				$output .= $after;
			}
		} else {
			$output .= $before . "None found" . $after;
		}
		echo $output;
	}
}

if (isset($_GET['ak_action'])) {
	$url = parse_url(get_bloginfo('home'));
	$domain = $url['host'];
	if (!empty($url['path'])) {
		$path = $url['path'];
	}
	else {
		$path = '/';
	}
	$redirect = false;
	switch ($_GET['ak_action']) {
		case 'reject_mobile':
			setcookie(
				'akm_mobile'
				, 'false'
				, time() + 300000
				, $path
				, $domain
			);
			$redirect = true;
			break;
		case 'force_mobile':
		case 'accept_mobile':
			setcookie(
				'akm_mobile'
				, 'true'
				, time() + 300000
				, $path
				, $domain
			);
			$redirect = true;
			break;
	}
	if ($redirect) {
		if (!empty($_SERVER['HTTP_REFERER'])) {
			$go = $_SERVER['HTTP_REFERER'];
		}
		else {
			$go = get_bloginfo('home');
		}
		header('Location: '.$go);
		die();
	}
}

if (akm_check_mobile()) {
	add_action('template', 'akm_template');
	add_action('option_template', 'akm_template');
	add_action('option_stylesheet', 'akm_template');
}

if (isset($_COOKIE['akm_mobile']) && $_COOKIE['akm_mobile'] == 'false') {
	add_action('the_content', 'akm_mobile_available');
}

?>