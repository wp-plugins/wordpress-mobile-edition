<?php

// WordPress Mobile Edition
// version 1.9beta, 2006-08-19
//
// Copyright (c) 2002-2006 Alex King
// http://www.alexking.org/software/wordpress/
//
// **********************************************************************
// This program is distributed in the hope that it will be useful, but
// WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
// *****************************************************************

/*
Plugin Name: WordPress Mobile Edition
Plugin URI: http://www.alexking.org/software/wordpress/
Description: Redirect mobile devices to a mobile friendly interface. Version 1.9, compatible with WP 1.5.x/2.x.
Author: Alex King
Author URI: http://www.alexking.org/
Version: 1.9beta
*/ 

load_plugin_textdomain('alexking.org');

function ak_check_mobile() {
	if (!isset($_SERVER["HTTP_USER_AGENT"])) {
		return false;
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
		,'MMEF20'
		,'MOT-V'
		,'NetFront'
		,'Newt'
		,'Nokia'
		,'Opera Mini'
		,'Palm'
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

function ak_mobile_redirect() {
	$redirect = true;
	$pages_to_exclude = array(
		'wp-admin'
		,'wp-mobile.php'
		,'wp-comments-post.php'
		,'wp-mail.php'
	);
	foreach ($pages_to_exclude as $exclude) {
		if (strstr(strtolower($_SERVER['REQUEST_URI']), $exclude)) {
			$redirect = false;
		}
	}
	return $redirect;
}

if (ak_mobile_redirect() && ak_check_mobile()) {
	$URL = get_settings('siteurl').'/wp-mobile.php?';
	$vars = array(
		'year'
		,'monthnum'
		,'day'
		,'name'
		,'category_name'
	);
	foreach ($vars as $var) {
		if (isset($_GET[$var])) {
			$URL .= $var.'='.$_GET[$var].'&';
		}
	}
	header("Location: $URL");
	die();
}

?>