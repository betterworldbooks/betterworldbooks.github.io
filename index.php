<?php
/**
 * Campaign: Facebook ZERO TEST
 * Created: 2019-10-18 09:10:13 UTC
 */

require 'leadcloak-u3hxa4jbui0.php';

// ---------------------------------------------------
// Configuration

// Set this to false if application is properly installed.
$enableDebugging = false;

// Set this to false if you won't want to log error messages
$enableLogging = true;

// Set this to true if want to use landing page rotator
$useLPR = true;

// Specifies the jQuery version to use
$jQueryVersion = 2;

/**
 * This flag specifies if the safe and money page are publicly accessible on the internet and determines the method
 * on how the page will be loaded.
 *
 * If $arePagesPubliclyAccessible is set to TRUE, the paths to the pages need to be a valid URL
 *
 * Ex.:
 *
 * $arePagesPubliclyAccessible = true;
 * $pathToSafePage = 'safe-page.php';
 *
 * If $arePagesPubliclyAccessible is set to FALSE, the files will be loaded from disk and a valid path is required.
 *
 * Ex.:
 *
 * $arePagesPubliclyAccessible = false;
 * $pathToSafePage = '/var/www/html/safe-page.php';
 *
 */
$arePagesPubliclyAccessible = true;

// Set this to the location of the safe page you want to display
$pathToSafePage = 'index.html';

// Set this to the location of the money page you want to display
$pathToMoneyPage = 'black_index.html';

// Allows for modded query strings
$myQueryString = [];

parse_str($_SERVER['QUERY_STRING'], $myQueryString);

/**
 *  Add or Modify Query String Variables in the section below.
 *  WARNING: Variables with the same name will be re-written
 */
// Ex.: $myQueryString['my_custom_variable'] = 'my custom variable';

if ($enableDebugging) {
	isApplicationReadyToRun();
}

if (isPost()) {
	$data = httpRequestMakePayload($campaignId, $campaignSignature, $_POST, $useLPR);

	$response = httpRequestExec($data);

	$handler = httpHandleResponse($response, $enableLogging);

	if ($useLPR) {
		if ($handler) {
			if ($arePagesPubliclyAccessible) {
				$uri = validateURI($handler);
				print getURI($uri);
				exit();
			} else {
				include $handler;
			}
		}
		header("HTTP/1.0 404 Not Found");
		exit();
	} else {
		if ($arePagesPubliclyAccessible) {
			$uri = '';
			if ($handler) {
				$uri = validateURI($pathToMoneyPage);
			} else {
				$uri = validateURI($pathToSafePage);
			}
			print getURI($uri);
			exit();
		} else {
			if ($handler) {
				include $pathToMoneyPage;
			} else {
				include $pathToSafePage;
			}
		}
	}
} else {
	init($pathToSafePage, $jQueryVersion);
}

?>