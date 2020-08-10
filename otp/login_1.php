<?php
session_start();
if (isset($_SESSION['session_id'])){
    header("Location:index.php");
}
include "php/koneksi.php";
include "php/func.php";
if($_POST){
       $token = getRequestToken();
       if(!empty($token)){
           $otp_sent = sendOTP($token,$_POST['phone']);
           if($otp_sent){
               $_SESSION['token_api'] = $token;
               $_SESSION['phone'] = $_POST['phone'];
               header("Location: verify.php");
           }
           print_r($_SESSION);           
       }        
        //bypass
        // $_SESSION['session_id'] = "9b0e8fe25ae969500fc7e186466541425b566526";
        // $_SESSION['level'] = 100;
        // setcookie("md_sess_id_v6", $_SESSION['session_id'], time()+(86400*30), "/robo/v6/cpanel");
        // $_SESSION['username'] = "bypass";
        // $_SESSION['permission_api'] = "superuser";
        // header("Location: index.php");
}


//LOGIN GOOGLE
include_once("login/config.php");
include_once("login/includes/functions.php");

if(isset($_REQUEST['code'])){
    $gClient->authenticate();
    $_SESSION['token'] = $gClient->getAccessToken();
    header('Location: ' . filter_var($redirectUrl, FILTER_SANITIZE_URL));
}

if (isset($_SESSION['token'])) {
    $gClient->setAccessToken($_SESSION['token']);
}

if ($gClient->getAccessToken()) {
    $_SESSION['token_api'] = getRequestToken();
    $userProfile = $google_oauthV2->userinfo->get();
    //DB Insert
    $gUser = new Users();
    $gUser->checkUser('google',$userProfile['id'],$userProfile['given_name'],$userProfile['family_name'],$userProfile['email'],$userProfile['gender'],$userProfile['locale'],$userProfile['link'],$userProfile['picture']);
    $_SESSION['google_data'] = $userProfile; // Storing Google User Data in Session
    $data = $userProfile;
    if(!empty($data['given_name'])){
           $first_name = $data['given_name'];
          }
          if(!empty($data['family_name'])){
           $last_name = $data['family_name'];
          }
          if(!empty($data['email'])){
           $email = $data['email'];
          }
          if(!empty($data['picture'])){
           $avatar = $data['picture'];
          }
          $session_id = validateWithGoogle($_SESSION['token_api'], $email, $first_name, $last_name, $avatar);
    header("location: login/account.php");
    $_SESSION['token'] = $gClient->getAccessToken();
} else {
    $authUrl = $gClient->createAuthUrl();
}

if(isset($authUrl)) {
//	echo '<a href="'.$authUrl.'"><img src="login/images/glogin.png" alt=""/></a>';
    $login_button = '<a href="'.$authUrl.'"><img src="login/images/glogin.png" alt="" width="100%"/></a>';
} else {
//    echo '<a href="login/logout.php?logout">Logout</a>';
    $login_button = '<a href="login/logout.php?logout">Logout</a>';
}

?>


<!DOCTYPE html>
<html lang="en-us" id="extr-page">
<head>
    <meta charset="utf-8">
    <title> Inflixer Robot</title>
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

    <!-- #CSS Links -->
    <!-- Basic Styles -->
    <link rel="stylesheet" type="text/css" media="screen" href="css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" media="screen" href="css/font-awesome.min.css">

    <!-- SmartAdmin Styles : Caution! DO NOT change the order -->
    <link rel="stylesheet" type="text/css" media="screen" href="css/smartadmin-production-plugins.min.css">
    <link rel="stylesheet" type="text/css" media="screen" href="css/smartadmin-production.min.css">
    <link rel="stylesheet" type="text/css" media="screen" href="css/smartadmin-skins.min.css">

    <!-- SmartAdmin RTL Support -->
    <link rel="stylesheet" type="text/css" media="screen" href="css/smartadmin-rtl.min.css">

    <!-- We recommend you use "your_style.css" to override SmartAdmin
         specific styles this will also ensure you retrain your customization with each SmartAdmin update.
    <link rel="stylesheet" type="text/css" media="screen" href="css/your_style.css"> -->

    <!-- Demo purpose only: goes with demo.js, you can delete this css when designing your own WebApp -->
    <link rel="stylesheet" type="text/css" media="screen" href="css/demo.min.css">

    <!-- #FAVICONS -->
    <link rel="shortcut icon" href="img/favicon/favicon.ico" type="image/x-icon">
    <link rel="icon" href="img/favicon/favicon.ico" type="image/x-icon">

    <!-- #GOOGLE FONT -->
    <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Open+Sans:400italic,700italic,300,400,700">

    <!-- #APP SCREEN / ICONS -->
    <!-- Specifying a Webpage Icon for Web Clip
         Ref: https://developer.apple.com/library/ios/documentation/AppleApplications/Reference/SafariWebContent/ConfiguringWebApplications/ConfiguringWebApplications.html -->
    <link rel="apple-touch-icon" href="img/splash/sptouch-icon-iphone.png">
    <link rel="apple-touch-icon" sizes="76x76" href="img/splash/touch-icon-ipad.png">
    <link rel="apple-touch-icon" sizes="120x120" href="img/splash/touch-icon-iphone-retina.png">
    <link rel="apple-touch-icon" sizes="152x152" href="img/splash/touch-icon-ipad-retina.png">

    <!-- iOS web-app metas : hides Safari UI Components and Changes Status Bar Appearance -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">

    <!-- Startup image for web apps -->
    <link rel="apple-touch-startup-image" href="img/splash/ipad-landscape.png" media="screen and (min-device-width: 481px) and (max-device-width: 1024px) and (orientation:landscape)">
    <link rel="apple-touch-startup-image" href="img/splash/ipad-portrait.png" media="screen and (min-device-width: 481px) and (max-device-width: 1024px) and (orientation:portrait)">
    <link rel="apple-touch-startup-image" href="img/splash/iphone.png" media="screen and (max-device-width: 320px)">

</head>

<body class="animated fadeInDown">

<div id="main" role="main">

    <!-- MAIN CONTENT -->
    <div id="content" class="container">

        <div class="row">

            <div class="col-xs-12 col-sm-12 col-md-4 col-md-offset-4 col-lg-4 col-lg-offset-4">
                <div class="well no-padding" style="top: auto">
                    <form action="<?php echo $_SERVER['PHP_SELF']?>" method="post" id="login-form" class="smart-form client-form">
                        <header>
                            Login Inflixer-Robot
                        </header>

                        <fieldset>

<!--                            <section>
                                <label class="label">Username</label>
                                <label class="input"> <i class="icon-append fa fa-user"></i>
                                    <input type="text" name="username">
                                    <b class="tooltip tooltip-top-right"><i class="fa fa-user txt-color-teal"></i> Please enter email address/username</b></label>
                            </section>

                            <section>
                                <label class="label">Password</label>
                                <label class="input"> <i class="icon-append fa fa-lock"></i>
                                    <input type="password" name="password">
                                    <b class="tooltip tooltip-top-right"><i class="fa fa-lock txt-color-teal"></i> Enter your password</b> </label>
                                <div class="note">
                                </div>
                            </section>-->
                            
                            <section>
                                <label class="label">Phone</label>
                                <label class="input"> <i class="icon-append fa fa-phone"></i>
                                    <input type="text" name="phone" id="phone" >
                                    <b class="tooltip tooltip-top-right"><i class="fa fa-user txt-color-teal"></i> Please enter your phone number</b></label>
                            </section>
                              
                        </fieldset>
                        <footer>
                            <button type="submit" name="send_otp" value="send_otp" class="btn btn-default">
                                Send OTP
                            </button>
                        </footer>
                    </form>

                </div>
                
                <div class="alert alert-info">
                    <button class="close" data-dismiss="alert">
                        ×
                    </button>
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <?php
                            if(isset($_GET['e']) && $_GET['e']=='1'){
                                echo 'Bad Credentials';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-4 col-md-offset-4 col-lg-4 col-lg-offset-4">
                <?php //echo $login_button?>
            </div>
        </div>
    </div>

</div>

<!--================================================== -->

<!-- PACE LOADER - turn this on if you want ajax loading to show (caution: uses lots of memory on iDevices)-->
<!--<script src="js/plugin/pace/pace.min.js"></script>-->

<!-- Link to Google CDN's jQuery + jQueryUI; fall back to local -->
<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script> if (!window.jQuery) { document.write('<script src="js/libs/jquery-2.1.1.min.js"><\/script>');} </script>

<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
<script> if (!window.jQuery.ui) { document.write('<script src="js/libs/jquery-ui-1.10.3.min.js"><\/script>');} </script>

<!-- IMPORTANT: APP CONFIG -->
<script src="js/app.config.js"></script>

<!-- JS TOUCH : include this plugin for mobile drag / drop touch events
<script src="js/plugin/jquery-touch/jquery.ui.touch-punch.min.js"></script> -->

<!-- BOOTSTRAP JS -->
<script src="js/bootstrap/bootstrap.min.js"></script>

<!-- JQUERY VALIDATE -->
<script src="js/plugin/jquery-validate/jquery.validate.min.js"></script>

<!--[if IE 8]>

<h1>Your browser is out of date, please update your browser by going to www.microsoft.com/download</h1>

<![endif]-->

<!-- MAIN APP JS FILE -->
<script src="js/app.min.js"></script>

<script type="text/javascript">
    runAllForms();

    function updateForm(){
        $("#phone").attr("readonly",true);
        $("#otp").attr("readonly",false);
//        $("#vtype").val("");
    }

    $(function() {
        // Validation
        $("#login-form").validate({
            // Rules for form validation
            rules : {
//                email : {
//                    required : true,
//                    email : true
//                },
//                password : {
//                    required : true,
//                    minlength : 3,
//                    maxlength : 20
//                },
                phone : {
                    required : true,
                    minlength : 10,
                    maxlength : 15,
                }
            },

            // Messages for form validation
            messages : {
//                email : {
//                    required : 'Please enter your email address',
//                    email : 'Please enter a VALID email address'
//                },
//                password : {
//                    required : 'Please enter your password'
//                },
                phone : {
                    required : 'Please enter your phone number then click SEND OTP'
                }
            },

            // Do not change code below
            errorPlacement : function(error, element) {
                error.insertAfter(element.parent());
            }
        });
    });
</script>

</body>
</html>
