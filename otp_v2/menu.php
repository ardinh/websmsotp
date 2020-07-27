<?php
$sms = '<li class="top-menu-invisible ">
		<a href="page/sms.php"><i class="fa fa-lg fa-fw fa-phone txt-color-blue"></i> <span class="menu-item-parent">SMS OTP</span></a>
		<ul style="display: block;">
		    <li>
		    	<a href="page/setting.php"><i class="fa fa-film"></i> Content Setting</a>
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
</ul>