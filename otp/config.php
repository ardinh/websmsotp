<?php

//config.php

//Include Google Client Library for PHP autoload file
require_once 'vendor/autoload.php';

//Make object of Google API Client for call Google API
$google_client = new Google_Client();

//Set the OAuth 2.0 Client ID
$google_client->setClientId('345246769035-ic0e23eesujh19m5qf1rimjjg0lanl4a.apps.googleusercontent.com');

//Set the OAuth 2.0 Client Secret key
$google_client->setClientSecret('8w0NYIdpM9MAjVZA3pUrN5rW');

//Set the OAuth 2.0 Redirect URI
//$google_client->setRedirectUri('http://dev.motion.co.id/robo/v5/cpanel/login.php');
//$google_client->setRedirectUri('http://localhost/v5/cpanel/index2.php');
$google_client->setRedirectUri('http://localhost/otp/index.php');

//
$google_client->addScope('email');

$google_client->addScope('profile');

//start session on web page
session_start();

?>