<?php
$sms = '<li class="top-menu-invisible ">
		<a href="page/dashboard.php"><i class="fa fa-lg fa-fw fa-phone txt-color-blue"></i> <span class="menu-item-parent">OTP</span></a>
		<ul style="display: block;">
		    <li>
		    	<a href="page/setting.php"><i class="fa fa-film"></i>Add Content Setting</a>
		    </li>
            <li>
                <a href="page/update_setting.php"><i class="fa fa-film"></i>Update Content Setting</a>
            </li>
		</ul>
	</li>';
?>


<ul>
    <?php
        switch ($_SESSION['level']){
            case 0:
                echo $sms;
                break;
            case 100:
                echo $sms;
                break;
        }
    ?> 

    <li class="top-menu-invisible">
        <a href="login/logout.php?logout" target="_top"><i class="fa fa-lg fa-fw fa-sign-out txt-color-blue"></i> <span class="menu-item-parent">Log Out </span></a>
    </li>   
</ul>