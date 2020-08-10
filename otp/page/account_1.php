<?php
require_once '../php/session.php';
include "../php/func.php";
checkAccount();
//if(!isset($_SESSION['google_data'])):header("Location:../login.php");endif;
//print_r($_SESSION);
?>
<!-- row -->
<div class="row">

    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

        <div class="row">
            <div class="col-sm-12">
                <div class="text-center error-box">
                    <h1 class="error-text tada animated"><i class="fa fa-times-circle text-danger error-icon-shadow"></i> Permission Denied</h1>
                    <h2 class="font-xl"><strong>Sorry..</strong></h2>
                    <br />
                    <p class="lead semi-bold">
                        <strong>This account doesn't have permission to access MyDrakor cPanel. </strong><br><br>
<!--                        <small>
                            We are working hard to correct this issue. Please wait a few moments and try your search again. <br> In the meantime, check out whats new on SmartAdmin:
                        </small>-->
                    </p>
                    <ul class="error-search text-left font-md">
                        <li><a href="http://dev.motion.co.id/robo/v5/cpanel/index.php"><small>Go to My Dashboard <i class="fa fa-arrow-right"></i></small></a></li>
                        <!--<li><small>Google ID : <?= $_SESSION['google_data']['id'] ?> </small></a></li>-->
                        <li><small>Name : <?= $_SESSION['google_data']['name'] ?> </small></a></li>
                        <li><small>Email : <?= $_SESSION['google_data']['email'] ?> </small></a></li>
<!--                        <li><small>Gender : <?= $_SESSION['google_data']['gender'] ?> </small></a></li>
                        <li><small>Locale : <?= $_SESSION['google_data']['locale'] ?> </small></a></li>
                        <li><small>Session ID : <?= $_SESSION['session_id'] ?> </small></a></li>-->
                        <li><small>Permission : <?= $_SESSION['permission_api'] ?> </small></a></li>                        
                    </ul>
                </div>	<?php //echo print_r($_SESSION);?>
            </div>	
        </div>	
    </div>		
</div>
<?php
//if($_SESSION['permission_api'] == "superuser"){
//        header("location: ../index.php");
//    }
?>
<!-- end row -->

<script type="text/javascript">

    pageSetUp();

    var pagefunction = function () {
        $("#search-error").focus();
    };

    pagefunction();

</script>
