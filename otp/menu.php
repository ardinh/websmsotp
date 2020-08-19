<?php
$sms = '<li class="top-menu-invisible ">
		<a href="page/setting.php"><i class="fa fa-lg fa-fw fa-phone txt-color-blue"></i> <span class="menu-item-parent">OTP</span></a>
		<ul style="display: block;">
		    <li>
		    	<a href="page/setting.php"><i class="fa fa-lg fa-fw fa-phone"></i>Add Content Setting</a>
		    </li>
            <li>
                <a href="page/update_setting.php"><i class="fa fa-lg fa-fw fa-phone"></i>Update Content Setting</a>
            </li>
		</ul>
	</li>';
$wa =   '<li>
                <a href="page/sms.php"><i class="fa fa-lg fa-fw fa-phone"></i>Whatsapp</a>
        </li>';
?>


<ul>
    <?php
        switch ($_SESSION['level']){
            case 0:
                echo $wa;
                break;
            case 10:
                echo $wa;
                echo $sms;
                break;
            case 100:
                echo $wa;
                echo $sms;
                break;
        }
    ?> 

    <li class="top-menu-invisible">
        <a href="login/logout.php?logout" target="_top"><i class="fa fa-lg fa-fw fa-sign-out txt-color-blue"></i> <span class="menu-item-parent">Log Out </span></a>
    </li>   
</ul>