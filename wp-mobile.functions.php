<?php

// WordPress Mobile Edition
// version 1.3, 2004-01-14
//
// Copyright (c) 2002-2004 Alex King
// http://www.alexking.org/software/wordpress/
//
// **********************************************************************
// This program is distributed in the hope that it will be useful, but
// WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
// *****************************************************************


// paste this function into your my-hacks.php


function ak_redirect_to_mobile() {
	$small_browsers = array("Elaine/3.0"
						   ,"Palm"
						   ,"EudoraWeb"
						   ,"Blazer"
						   ,"AvantGo"
						   ,"Windows CE"
						   ,"Cellphone"
						   ,"Small"
						   ,"MMEF20"
						   ,"Danger"
						   ,"hiptop"
						   ,"Proxinet"
						   ,"Newt"
						   ,"PalmOS"
						   ,"NetFront"
						   ,"SHARP-TQ-GX10"
						   ,"SonyEricsson"
						   ,"SymbianOS"
						   ."UP.Browser"
						   );
	foreach ($small_browsers as $browser) {
		if (strstr($_SERVER["HTTP_USER_AGENT"], $browser)) {
			global $siteurl;
			$URL = $siteurl.'/wp-mobile.php?';
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
}

?>
