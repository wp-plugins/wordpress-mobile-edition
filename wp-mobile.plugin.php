<?php

// WordPress Mobile Edition
// version 1.7, 2005-01-12
//
// Copyright (c) 2002-2005 Alex King
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
Description: Redirect mobile devices to a mobile friendly interface. Version 1.7, compatible with WP 1.2.
Author: Alex King
Author URI: http://www.alexking.org/
*/ 

$small_browsers = array('Elaine/3.0'
					   ,'Palm'
					   ,'EudoraWeb'
					   ,'Blazer'
					   ,'AvantGo'
					   ,'Windows CE'
					   ,'Cellphone'
					   ,'Small'
					   ,'MMEF20'
					   ,'Danger'
					   ,'hiptop'
					   ,'Proxinet'
					   ,'Newt'
					   ,'PalmOS'
					   ,'NetFront'
					   ,'SHARP-TQ-GX10'
					   ,'SonyEricsson'
					   ,'SymbianOS'
					   ,'UP.Browser'
					   ,'TS21i-10'
					   ,'BlackBerry'
					   ,'portalmmm'
					   );

foreach ($small_browsers as $browser) {
	if (strstr($_SERVER["HTTP_USER_AGENT"], $browser) && !strstr($_SERVER['REQUEST_URI'], 'wp-mobile.php')) {
		$URL = get_settings('siteurl').'/wp-mobile.php?';
		if (isset($p)) {
			$URL .= 'p='.$p.'&';
		}
		if (isset($m)) {
			$URL .= 'm='.$m.'&';
		}
		if (isset($cat)) {
			$URL .= 'cat='.$cat.'&';
		}
		header("Location: $URL");
		die();
	}
}

?>
