<?php
session_start();
include_once("src/Google_Client.php");
include_once("src/contrib/Google_Oauth2Service.php");
######### edit details ##########
$clientId = '383650067274-lrukneluh41lv6unefav3rhgfs5gc2tg.apps.googleusercontent.com'; //Google CLIENT ID
$clientSecret = 'WJwtSCZ35Bna2ENjCaKL-O3F'; //Google CLIENT SECRET
$redirectUrl = 'http://localhost/otp/login/index.php';  //return url (url to script)
$homeUrl = 'http://localhost/otp/login/index.php';  //return to home
//local
//$redirectUrl = 'http://localhost/login-with-google-using-php/index.php';  //return url (url to script)
//$homeUrl = 'http://localhost/login-with-google-using-php/index.php';  //return to home

##################################

$gClient = new Google_Client();
$gClient->setApplicationName('Login to robo v5');
$gClient->setClientId($clientId);
$gClient->setClientSecret($clientSecret);
$gClient->setRedirectUri($redirectUrl);

$google_oauthV2 = new Google_Oauth2Service($gClient);
?>