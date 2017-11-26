<?php

$file = file("ip.txt"); // edit if you need to your ips file

////////////////////////////////////////////////
///// Dont edit any thing under this line. /////
/////   By Cold z3ro, www.hackteach.org	   /////
/////     SipOrg 0.7 linux edition		   /////
/////       Free Limited Edition 		   /////
/////  facebook.com/groups/hackteach.org   /////
////////////////////////////////////////////////

$date= @date("Y-m-d");
$brutedir = "date_".$date;
if(!file_exists($brutedir)) { mkdir($brutedir); }
ini_set('memory_limit', 1024 * 1024 * 1024);
ini_set("max_execution_time", "on");
if(isExtensionLoaded('curl') != true)
{
   die("CURL extension is not available on your web server");
}

file_put_contents('router_pages'.$date.'.htm', '<div> sipOrg v0.7 SIP HTTP/HTTPS IP REPORTER || By Cold z3ro</div><br>', FILE_APPEND);
foreach ($file as $ipkey => $line) 
{
	if ( preg_match('/([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3})\:?([0-9]{1,5})?/', $line, $match) ) 
	{
		$host = explode(':', $match[1]);
		if(!empty($host[0]))
		{
			file_put_contents($brutedir.'/ipCLEan_'.$date.'.txt', $host[0]."\n" , FILE_APPEND);
			echo $host[0] ." | ". sIP_Check($host[0], $brutedir) ."\n";
		}
	}
}
echo "\n\nDONE!. TOTAL $ipkey HOSTS EXECUTED\n\n";

function sIP_Check( $url, $brutedir, $timeout = 10 )
{
	global $date;
    $url = str_replace( "&amp;", "&", urldecode(trim($url)) );
	$cookie = getcwd()."CURLCOOKIE";
	$ch = curl_init();
	curl_setopt( $ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20041001 Firefox/0.10.1" );
	curl_setopt( $ch, CURLOPT_URL, $url );
	curl_setopt( $ch, CURLOPT_COOKIEJAR, $cookie );
	curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
	curl_setopt( $ch, CURLOPT_ENCODING, "" );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	curl_setopt( $ch, CURLOPT_AUTOREFERER, true );
	curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, $timeout );
	curl_setopt( $ch, CURLOPT_TIMEOUT, $timeout );
	curl_setopt( $ch, CURLOPT_MAXREDIRS, 3 );
	$content = curl_exec( $ch );
	$auth  	= curl_getinfo( $ch, CURLINFO_HTTPAUTH_AVAIL);
	$response = curl_getinfo( $ch );
	curl_close ( $ch );
	
if($response['http_code']=="0")
{
	$ports = trim(shodon($url));
	if(!empty($ports))
	{
		file_put_contents('router_pages'.$date.'.htm', $url." Err102 | OPEN PORTS: ". $ports."<br>", FILE_APPEND);
		return " Err102 | OPEN PORTS: ". $ports;
	}else{
		return " Err102 | OPEN PORTS: NONE";
	}
}else{

	if ($response['http_code'] == 301 || $response['http_code'] == 302)
	{
		ini_set("user_agent", "Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20041001 Firefox/0.10.1");
		$headers = @get_headers($response['url']);

		$location = "";
		foreach( $headers as $value )
		{
			if ( substr( strtolower($value), 0, 9 ) == "location:" ||  substr( strtolower($value), 0, 9 ) == "Location:")
            return sIP_Check(trim(substr($value, 9, strlen($value))), $brutedir);
		}
	}
	
	if (preg_match("/window\.location\.replace\('(.*)'\)/i", $content, $value) || preg_match("/window\.location\=\"(.*)\"/i", $content, $value) || preg_match("/Refresh\" content\=\"0; url\=(.*)\"/i", $content, $value))
	{
		return sIP_Check( $value[1] , $brutedir);
	}else{
			$title = trim(getBetween($content,'<title>','</title>'));
			if(empty($title))
			{
				$title = trim(getBetween($content,'<TITLE>','</TITLE>'));
			}
			if(!empty($title))
			{
				$msg = $title;
				file_put_contents('router_pages'.$date.'.htm', '<a href="http://'.$url.'" target="_blank">'.$url.'</a>      ' . $title .'<br>', FILE_APPEND);
				return $msg;
			}else{
				
				if($response['http_code']!="0")
				{
					$msg = $title. " ALIVE BUT NOT KNOWN";
					file_put_contents('router_pages'.$date.'.htm', '<a href="http://'.$url.'" target="_blank">'.$url.'</a>      ' . $msg .'<br>', FILE_APPEND);
					return $msg;
				}
			}
		}
	}
}

function isExtensionLoaded($extension_name)
{
    return extension_loaded($extension_name);
}
function shodon($host, $timeout = 2 )
{
	$ch = curl_init();
	curl_setopt( $ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20041001 Firefox/0.10.1" );
	curl_setopt( $ch, CURLOPT_URL, 'https://www.shodan.io/host/'.$host );
	curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, $timeout );
	curl_setopt( $ch, CURLOPT_TIMEOUT, $timeout );
	$content = curl_exec( $ch );
	return getBetween($content, 'Ports open:', '"');
}
function getBetween($content, $start, $end)
{
	$r = explode($start, $content);
	if (isset($r[1]))
	{
		$r = explode($end, $r[1]);
		return $r[0];
	}
	return '';
}
?>