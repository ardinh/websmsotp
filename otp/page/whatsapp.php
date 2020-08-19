<?php
session_start();
require_once "../php/const.php";
include "../phpqrcode/qrlib.php";

$tempdir = "temp/"; 
if (!file_exists($tempdir))
    mkdir($tempdir);

$isi_teks = "https://api.whatsapp.com/send?phone=628888214421&text=req%20otp";
//direktori dan nama logo
$logopath = '../mydrakor.png';
//namafile setelah jadi qrcode
$namafile = "qrcode.png";
//kualitas dan ukuran qrcode
$quality = 'H'; 
$ukuran = 5; 
$padding = 0;

QRCode::png($isi_teks,$tempdir.$namafile,QR_ECLEVEL_H,$ukuran,$padding);
$filepath = $tempdir.$namafile;
$QR = imagecreatefrompng($filepath);

$logo = imagecreatefromstring(file_get_contents($logopath));
$QR_width = imagesx($QR);
$QR_height = imagesy($QR);

$logo_width = imagesx($logo);
$logo_height = imagesy($logo);

//besar logo
$logo_qr_width = $QR_width/2.5;
$scale = $logo_width/$logo_qr_width;
$logo_qr_height = $logo_height/$scale;

//posisi logo
imagecopyresampled($QR, $logo, $QR_width/3.3, $QR_height/2.5, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height);

imagepng($QR,$filepath);
?>
<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <h1 class="page-title txt-color-blueDark">
            <i class="fa fa-pencil-square-o fa-fw "></i>
            Whatsapp
            <span>
            </span>
        </h1>
    </div>
    <div class="col-xs-12 col-sm-5 col-md-5 col-lg-8">

    </div>
</div>

<!-- NEW WIDGET START -->
<article class="col-sm-12 col-md-12 col-lg-6">
    <!-- Widget ID (each widget will need unique ID)-->
    <div class="jarviswidget" id="wid-id-5" data-widget-colorbutton="false" data-widget-editbutton="false" data-widget-fullscreenbutton="false" data-widget-custombutton="false" data-widget-sortable="false">

        <header>
            <h2>Test Send whatsapp</h2>
        </header>
        <div>

            <!-- widget edit box -->
            <div class="jarviswidget-editbox">
                <!-- This area used as dropdown edit box -->
            </div>
            <!-- end widget edit box -->

            <!-- widget content -->
            <div class="widget-body" id="video">
                <div>
                    <legend>Scan the QR code, or <a href="https://web.whatsapp.com/send?phone=628888214421&text=req%20otp">click this link</a>, and hit send on the pre-filled message. to test send whatsapp</legend>
                </div>
                <div align="center">
                    <img src="page/temp/<?php echo $namafile; ?>">
                </div>
                <div align="center">
                    <legend>or</legend>
                </div>
                <div>
                    <legend>Send a WhatsApp message to <b>08888214421</b>  with the passphrase <b>req otp</b></legend>
                </div>
            </div>
            <!-- end widget content -->  
        </div>
        <!-- end widget div -->
    </div>
    <!-- end widget -->
</article>