<?php

require 'vendor/cosenary/instagram/src/Instagram.php';
use MetzWeb\Instagram\Instagram;

$opts = get_option( 'rhd_instagrabby_settings' );

// initialize class
$instagram = new Instagram(array(
	'apiKey'      => $opts['rhd_instagrabby_client_id'],
	'apiSecret'   => $opts['rhd_instagrabby_client_secret'],
	'apiCallback' => 'http://dev.roundhouse-designs.com/damasklove/wp-content/mu-plugins/rhd-instagrabby/success.php'
));

// receive OAuth code parameter
$code = $_GET['code'];

// check whether the user has granted access
if (isset($code)) {
	// receive OAuth token object
	$data = $instagram->getOAuthToken($code);
	$username = $username = $data->user->username;

	// store user access token
	$instagram->setAccessToken($data);

	// now you have access to all authenticated user methods
	$result = $instagram->getUserMedia();
} else {
	// check whether an error occurred
	if (isset($_GET['error'])) {
		echo 'An error occurred: ' . $_GET['error_description'];
	}
}