<?php
/*
Plugin Name: Link Market's LDMS
Plugin URI: http://www.linkmarket.com/customer_center/index.php?action=show&cat=16
Description: The Link Market's LDMS (Link Directory Management Service) plug-in will manage all the links that have been traded through LinkMarket.com websites. 
Version: The Plugin's Version Number, e.g.: 1.0.2
Author: Link Market Team
Author URI: http://www.linkmarket.com/
License: A "Slug" license name e.g. GPL2
*/
?>
<?php
/*  Copyright 2010  Link Market Team  (email : support@linkmarket.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
?>
<?php

function GetLDMS($text)
{
	
	$ldmstag = '[linkmarketldms]';
	
	if(strpos($text, $ldmstag) !== false)
	{
		$mykey_values = get_post_custom_values('ldmskey');
		
		if(trim($mykey_values[0]) == "")
		{
			$ldms_html_code ="<hr /><b>Warning: The LDMS Key is not valid!</b><hr />";
		}
		
		$url = "http://api.linkmarket.com/mng_dir/get_links.php?user_id=".trim($mykey_values[0])."&cid=".$_GET['cid']."&start=".$_GET['start']."";
		
		$ldms_html_code .= GetLDMSHtmlCode($url);	
		
		$text = str_replace($ldmstag,$ldms_html_code, $text);
	}
	
	return $text;
}

function GetLDMSHtmlCode($url)
{
	$buffer = ""; 
	$urlArr = parse_url($url);
	if($urlArr[query])
	{
		$urlArr[query] = "?".$urlArr[query];
	}
	
	$fp = fsockopen($urlArr[host], 80, $errno, $errstr, 30);
	if (!$fp)
	{
		echo "$errstr ($errno)<br />";
	}
	else
	{
		$out = "GET /".substr($urlArr[path], 1).$urlArr[query]." HTTP/1.0\r\n";
		$out .= "Host: ".$urlArr[host]."\r\n";
		$out .= "Connection: Close\r\n\r\n";
		fwrite($fp, $out);
		while (!feof($fp))
		{
			$buffer .= fgets($fp, 128);
		} 
		fclose($fp);
	}
	
	$buffer = strstr($buffer,"\r\n\r\n");
	
	return $buffer;
}

add_filter('the_content','GetLDMS');

?>