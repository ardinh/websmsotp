<?php
session_start();
include "../php/func.php";
checkAccount();
//if(!isset($_SESSION['google_data'])):header("Location:../login.php");endif;
//print_r($_SESSION);
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Login with Google Account</title>
<style type="text/css">
h2
{
font-family:Arial, Helvetica, sans-serif;
color:#999999;
text-align: center;
}
.wrapper{width:600px; margin-left:auto;margin-right:auto;}
.welcome_txt{
	margin: 20px;
	background-color: #EBEBEB;
	padding: 10px;
	border: #D6D6D6 solid 1px;
	-moz-border-radius:5px;
	-webkit-border-radius:5px;
	border-radius:5px;
        text-align: right;
}
.google_box{
	margin: 20px;
	background-color: #FFF0DD;
	padding: 10px;
	border: #F7CFCF solid 1px;
	-moz-border-radius:5px;
	-webkit-border-radius:5px;
	border-radius:5px;
}
.google_box .image{ text-align:center;}
</style>
</head>
<body>
<div class="wrapper">
    <h2>Sorry, this account doesn't have permission to access MyDrakor cPanel</h2>
    <?php
    echo '<div class="welcome_txt">Welcome <b>'.$_SESSION['google_data']['given_name'].' | <a href="logout.php?logout">Logout</a></b></div>';
    echo '<div class="google_box">';
    echo '<p class="image"><img src="'.$_SESSION['google_data']['picture'].'" alt="" width="220" height="220"/></p>';
    echo '<p><b>Google ID : </b>' . $_SESSION['google_data']['id'].'</p>';
    echo '<p><b>Name : </b>' . $_SESSION['google_data']['name'].'</p>';
    echo '<p><b>Email : </b>' . $_SESSION['google_data']['email'].'</p>';
    echo '<p><b>Locale : </b>' . $_SESSION['google_data']['locale'].'</p>';
    echo '<p><b>Session ID : </b>' . $_SESSION['session_id'].'</p>';
    echo '<p><b>Permission : </b>' . $_SESSION['permission_api'].'</p>';
    echo '<p><b>GO TO  <a href="http://localhost/otp/index.php">DASHBOARD</a></b></p>';
    echo '</div>';
    if($_SESSION['permission_api'] == "superuser"){
        header("location: ../index.php");
    }//else{
        //header("location: ../login.php");
    //}
    
    ?>
</div>
</body>
</html>