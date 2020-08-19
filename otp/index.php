<?php
session_start();
if (!isset($_SESSION['username'])){
//	echo  "asdkhkjsa";
//	die();
	// echo $_SESSION['key'];
	header("Location:login.php");
	//header("Location:http://devel.motion.co.id/crawling_bioskopkeren/login.php");
}
?>
<!DOCTYPE html>
<html lang="en-us">	
	<head>
		<meta charset="utf-8">
		<title> SmartAdmin (AJAX)</title>
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

		<!-- DEV links : turn this on when you like to develop directly -->
		<!--<link rel="stylesheet" type="text/css" media="screen" href="../Source_UNMINIFIED_CSS/smartadmin-production.css">-->
		<!--<link rel="stylesheet" type="text/css" media="screen" href="../Source_UNMINIFIED_CSS/smartadmin-skins.css">-->

		<link rel="stylesheet" type="text/css" media="screen" href="css/jquery-confirm.min.css">

		<link rel="stylesheet" type="text/css" media="screen" href="css/movie.css">
		<!-- We recommend you use "your_style.css" to override SmartAdmin
		     specific styles this will also ensure you retrain your customization with each SmartAdmin update.
		<link rel="stylesheet" type="text/css" media="screen" href="css/your_style.css"> -->
		
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
		<style type="text/css">
			.modal-dialog {

				width: 100%;


			}
			mark{
				background-color: yellow;
				color: black;
			}

			.btn-act{
				width: 100%;
				margin-bottom: 5px;
			}
			.modal-content {

				/* 80% of window height */

				height: 60%;


			}
			.ucwords { text-transform: capitalize; }
			.modal-header {


				padding:16px 16px;
				align-content: center;
			}
			.search-res {
				max-width: 350px; display: none; max-height: 500px;overflow-y: scroll
			}
			.search-res-title {
				margin-top: 0;
				margin-bottom: 0;
			}

		</style>
	</head>

	<body class="smart-style-0">

		<!-- #HEADER -->
		<header id="header">
			<div id="logo-group">

				<!-- PLACE YOUR LOGO HERE -->
				<span id="logo"> <img src="img/logo.png" alt="SmartAdmin"> </span>
				<!-- END LOGO PLACEHOLDER -->
				
			</div>
			
			
			<!-- #TOGGLE LAYOUT BUTTONS -->
			<!-- pulled right: nav area -->
			<div class="pull-right">
				
				<!-- collapse menu button -->
				<div id="hide-menu" class="btn-header pull-right">
					<span> <a href="javascript:void(0);" data-action="toggleMenu" title="Collapse Menu"><i class="fa fa-reorder"></i></a> </span>
				</div>
				<!-- end collapse menu -->

				<!-- logout button -->
				<div id="logout" class="btn-header transparent pull-right">
					<span> <a href="login.html" title="Sign Out" data-action="userLogout" data-logout-msg="You can improve your security further after logging out by closing this opened browser"><i class="fa fa-sign-out"></i></a> </span>
				</div>
				<!-- end logout button -->
				

				<!-- fullscreen button -->
				<div id="fullscreen" class="btn-header transparent pull-right">
					<span> <a href="javascript:void(0);" data-action="launchFullscreen" title="Full Screen"><i class="fa fa-arrows-alt"></i></a> </span>
				</div>
				<!-- end fullscreen button -->
				<div class="header-search pull-right">
					<span role="status" aria-live="polite" class="ui-helper-hidden-accessible"></span>
					<input id="search-fld" type="text" style="width: 350px;" name="param" placeholder="Cari Film/Seri" class="ui-autocomplete-input" autocomplete="off">
					<button type="submit">
						<i class="fa fa-search"></i>
					</button>
					<ul class="list-group search-res" id="search-res" style="">
						
					</ul>
				</div>
			</div>
			<!-- end pulled right: nav area -->

		</header>
		<!-- END HEADER -->

		<!-- #NAVIGATION -->
		<!-- Left panel : Navigation area -->
		<!-- Note: This width of the aside area can be adjusted through LESS/SASS variables -->
		<aside id="left-panel">

			<!-- User info -->
			<div class="login-info">
				<span> <!-- User image size is adjusted inside CSS, it should stay as is --> 
					
					<a href="javascript:void(0);" id="show-shortcut" data-action="toggleShortcut">
						<img src="img/avatars/male.png" alt="me" class="online" /> 
						<span>
							<?= $_SESSION['username']?>
						</span>
					</a> 
					
				</span>
			</div>
			<!-- end user info -->

			<!-- NAVIGATION : This navigation is also responsive

			To make this navigation dynamic please make sure to link the node
			(the reference to the nav > ul) after page load. Or the navigation
			will not initialize.
			-->
			<nav>
				<!-- 
				NOTE: Notice the gaps after each icon usage <i></i>..
				Please note that these links work a bit different than
				traditional href="" links. See documentation for details.
				-->
				<?php include "menu.php";?>
			</nav>
			

			<span class="minifyme" data-action="minifyMenu"> <i class="fa fa-arrow-circle-left hit"></i> </span>

		</aside>
		<!-- END NAVIGATION -->
		
		<!-- #MAIN PANEL -->
		<div id="main" role="main">

			<!-- RIBBON -->
			<div id="ribbon">

				<span class="ribbon-button-alignment"> 
					<span id="refresh" class="btn btn-ribbon" data-action="resetWidgets" data-title="refresh" rel="tooltip" data-placement="bottom" data-original-title="<i class='text-warning fa fa-warning'></i> Warning! This will reset all your widget settings." data-html="true" data-reset-msg="Would you like to RESET all your saved widgets and clear LocalStorage?"><i class="fa fa-refresh"></i></span> 
				</span>

				<!-- breadcrumb -->
				<ol class="breadcrumb">
					<!-- This is auto generated -->
				</ol>
				<!-- end breadcrumb -->

				<!-- You can also add more buttons to the
				ribbon for further usability

				Example below:

				<span class="ribbon-button-alignment pull-right" style="margin-right:25px">
					<a href="#" id="search" class="btn btn-ribbon hidden-xs" data-title="search"><i class="fa fa-grid"></i> Change Grid</a>
					<span id="add" class="btn btn-ribbon hidden-xs" data-title="add"><i class="fa fa-plus"></i> Add</span>
					<button id="search" class="btn btn-ribbon" data-title="search"><i class="fa fa-search"></i> <span class="hidden-mobile">Search</span></button>
				</span> -->

			</div>
			<!-- END RIBBON -->
			
			<!-- #MAIN CONTENT -->
			<div id="content">

			</div>
			
			<!-- END #MAIN CONTENT -->
		</div>
		<!-- END #MAIN PANEL -->





		<!--================================================== -->

		<!-- PACE LOADER - turn this on if you want ajax loading to show (caution: uses lots of memory on iDevices)
		<script data-pace-options='{ "restartOnRequestAfter": true }' src="js/plugin/pace/pace.min.js"></script>-->


	
		
		<!-- #PLUGINS -->
		<!-- Link to Google CDN's jQuery + jQueryUI; fall back to local -->
		<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
		<script>
			if (!window.jQuery) {
				document.write('<script src="js/libs/jquery-2.1.1.min.js"><\/script>');
			}
		</script>

		<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
		<script>
			if (!window.jQuery.ui) {
				document.write('<script src="js/libs/jquery-ui-1.10.3.min.js"><\/script>');
			}
		</script>
		<script src="js/func.js"></script>
		<script src="js/funcDramaqu.js"></script>
        <script src="js/funcUniversal.js"></script>
		<!-- IMPORTANT: APP CONFIG -->
		<script src="js/app.config.js"></script>

		<!-- BOOTSTRAP JS -->
		<script src="js/bootstrap/bootstrap.min.js"></script>

		<!-- Jquery confirm -->
		<script src="js/jquery-confirm.min.js"></script>

		<!-- JARVIS WIDGETS -->
		<script src="js/smartwidgets/jarvis.widget.min.js"></script>

		<!-- JQUERY VALIDATE -->
		<script src="js/plugin/jquery-validate/jquery.validate.min.js"></script>
		<script src="js/plugin/jquery-nestable/jquery.nestable.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/mark.js/8.11.1/jquery.mark.min.js"></script>
		<!--[if IE 8]>
			<h1>Your browser is out of date, please update your browser by going to www.microsoft.com/download</h1>
		<![endif]-->


		<!-- MAIN APP JS FILE -->
		<script src="js/app.min.js"></script>

		<script>
			$("#search-fld").focus(function () {
				$("#search-res").show();
			});
			$("#search-res").hover(function(){
				$("#search-res").show();
			},function(){
				if($("#search-fld").is(":focus"))$("#search-res").show();
				else $("#search-res").hide();
			});
			$("#search-fld").on("focusout",function () {
				if($("#search-res:hover").length < 0)
				$("#search-res").hide();
			});

			$("#search-fld").keyup(function () {
				var q = $(this).val();
				var html = "";

				$.get("http://dev.motion.co.id/robo/v4/cpanel/php/typeahead.php?q="+q,function( data ) {
					for(k in data){
						var title = data[k].rawtitle;
						var movurl = data[k].moviepageurl;
						var type = data[k].type;
						var source = data[k].source;
						var poster = data[k].poster;
						var domain = data[k].domain;
						var icon = "fa-film";
						var iconcolor = "label-success";
						var ticon = data[k].matched ? "fa-chain" : "fa-chain-broken";
						var ticoncolor = data[k].matched  ? "label-success" : "label-danger";
						if(type=='seri'){
							icon = "fa-tv";
							iconcolor = " label-info";
						}
						html += `
<li class="list-group-item" style="height: 75px">
	<div class="col-sm-2" style="padding: 0;margin: 0">
		<a href="${movurl}" target="_blank">
			<img height="50" src="${poster}" alt="Da Vinci's Demons">
		</a>
	</div>
	<div class="col-sm-10">
		<div>
			<a target="" href="${movurl}" class=""><span class="search-res-title">${title}</span></a>
		</div>
		
		<span class="label ${iconcolor} btn-act"><i class="fa ${icon}"></i> </span>
		&nbsp;<span class="label ${ticoncolor}"><i class="fa ${ticon}"></i> </span> -
		<img src="http://www.google.com/s2/favicons?domain=${domain}"> ${source}
	</div>
</li>
`;
					}

					$("#search-res").html(html);
					$("#search-res").show();
				});


			})
		</script>

	</body>

</html>