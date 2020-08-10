<?php
include_once("config.php");
if(array_key_exists('logout',$_GET))
{
    $_SESSION = array(); // Unset all $_SESSION variables
    setcookie("md_sess_id", "", time() - 3600,"/otp");
    setcookie("md_sess_id_v6", "", time() - 3600,"/otp"); //Clear the Session Cookie
    $gClient->revokeToken();
    session_destroy();  //Destroy the session data

//	unset($_SESSION['token']);
//	unset($_SESSION['google_data']); //Google session data unset
//	$gClient->revokeToken();
//	session_destroy();
	header("Location:../login.php");
}
?>