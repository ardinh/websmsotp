<?php
session_start();
require_once "../php/const.php";
define("SMS_BASEURL", "http://dev.motion.co.id/smsbot/");
define("SMS_KUOTA", SMS_BASEURL . "smskuota.php");
define("CHECK_KUOTA", SMS_BASEURL . "checkKuota.php");
define("UPDATE_DVC", SMS_BASEURL . "update_dvc.php");
?>
<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <h1 class="page-title txt-color-blueDark">
            <i class="fa fa-pencil-square-o fa-fw "></i>
            SMS OTP
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
            <h2>Cek Kuota </h2>
        </header>

        <!-- widget div-->
        <div>

            <!-- widget edit box -->
            <div class="jarviswidget-editbox">
                <!-- This area used as dropdown edit box -->

            </div>
            <!-- end widget edit box -->

            <!-- widget content -->
            <div class="widget-body">

                <div class="tabs-left">
                    <ul class="nav nav-tabs tabs-left" id="demo-pill-nav">

                    </ul>
                    <div class="tab-content" id="tab-content">
                        
                    </div>
                    <div class="col-md-12 pull-right">
                        <!--<div class="btn-group btn-group-justified">--><br>
                        <button class="btn btn-info" onclick="smsKuotaOut()"><i class="fa fa-arrow-right"></i> Kirim Request</button><br><br>
                        <button class="btn btn-success" onclick="checkKuota()"><i class="fa fa-refresh"></i> Refresh Data</button><br><br>
                        <!--</div>-->
                    </div>
                </div>
            </div>
            <!-- end widget content -->  
        </div>
        <!-- end widget div -->
    </div>
    <!-- end widget -->
</article>
<!-- NEW WIDGET START -->
<article class="col-sm-12 col-md-12 col-lg-6">
    <!-- Widget ID (each widget will need unique ID)-->
    <div class="jarviswidget" id="wid-id-6" data-widget-colorbutton="false" data-widget-editbutton="false" data-widget-fullscreenbutton="false" data-widget-custombutton="false" data-widget-sortable="false">

        <header>
            <h2>Cek Kuota </h2>
        </header>

        <!-- widget div-->
        <div>

            <!-- widget edit box -->
            <div class="jarviswidget-editbox">
                <!-- This area used as dropdown edit box -->

            </div>
            <!-- end widget edit box -->

            <!-- widget content -->
            <div class="widget-body">

                <form class="form-horizontal">

                    <fieldset>
                        <legend>Operator Tujuan</legend>
                        <div class="form-group">
                            <div class="col-md-10">
                                <input type="checkbox" id="xl" name="xl"><label for="xl"> XL </label> <input type="checkbox" id="tsel" name="tsel"><label for="tsel"> Telkomsel </label>
                                <input type="checkbox" id="tri" name="tri"><label for="tri"> TRI </label> <input type="checkbox" id="ind" name="ind"><label for="ind"> Indosat </label>
                                <input type="checkbox" id="smartfren" name="smartfren"><label for="smartfren"> Smartfren </label>
                            </div>
                        </div>

                    </fieldset>
                    <div class="form-actions">
                        <div class="row">
                            <div class="col-md-12">
                                <button class="btn btn-default" type="submit" onclick="clearNomor()">
                                    Cancel
                                </button>
                                <button class="btn btn-primary" type="submit"  onclick="updateNomor()">
                                    <i class="fa fa-save"></i>
                                    Submit
                                </button>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
            <!-- end widget content -->  
        </div>
        <!-- end widget div -->
    </div>
    <!-- end widget -->
</article>
<script type="text/javascript">
    var smskuota = "<?= SMS_KUOTA ?>",
            checkkuota = "<?= CHECK_KUOTA ?>",
            updatedvc = "<?= UPDATE_DVC ?>",
            jml_device = 2;
    
    for (var i = 1; i <= jml_device; i++) {
        var clas = ``;
        if (i == 1) {
            clas = `active`;
        }
        if(i==jml_device){
            $('#demo-pill-nav').append(`<li class="${clas}">
                            <a href="#tab-r${i}" data-toggle="tab">API No Malaysia</a>
                        </li>`);
        }else{
            $('#demo-pill-nav').append(`<li class="${clas}">
                            <a href="#tab-r${i}" data-toggle="tab"> Device ${i} </a>
                        </li>`);
        }        
        $('#tab-content').append(`<div class="tab-pane ${clas}" id="tab-r${i}">
                        </div>`);
    }
    $("#tab-r1").html(`<div id="spinner"><i class="fa fa-spinner fa-spin fa-3x"></i></div>`);
    
    function smsKuotaOut() {
        var i;
        for (i = 1; i <= jml_device; i++) {
            var post = {
                "c": 6,
                "d": i
            }
            $.ajax({
                url: smskuota,
                type: "POST",
                data: JSON.stringify(post),
            }).done(function (data) {
                console.log(data);
            }).fail(function (xhr) {
                console.log(xhr);
            });
        }
        alertSuccess("Silahkan tunggu beberapa menit, kemudian klik <b>refresh data</b>");
    }

    checkKuota();
    function checkKuota() {
        for (var i = 1; i <= jml_device; i++) {
            $("#tab-r"+i).html(`<div id="spinner"><i class="fa fa-spinner fa-spin fa-3x"></i></div>`);
        }        
        $.get(checkkuota).done(function (data) {
//            alertSuccess("Data telah diupdate");
            var obj = JSON.parse(data);
//            console.log(data);
            $.each(obj, function (index, value) {
                var html = "";
                if((index+1)==jml_device){
                    html += "<h3>API No Malaysia </h3> " +
                     ("<p>Terakhir dicek: " + value[3] + "</p>") +
                     ("<p>Saldo: <b>" + value[2] + "</b></p>");
                }else{
                    html += "<h3>Device " + value[0] + "</h3> " +
                     ("<p>Terakhir dicek: " + value[3] + "</p>") +
                     ("<p>Nomor: <b>" + value[4] + "</b></p>") +
                     ("<p>Jumlah kuota: <b>" + value[2] + "</b></p>");
                }                 
                html += ("<p>" + value[1] + "</p>");
                $("#tab-r" + (index + 1)).html(html);
            });
//            $("#tab-r"+(device_id)).html(html);
        }).fail(function () {
            $("#tab-r1").html("failed");
        });
    }

    function updateNomor() {
        var d = $("#nomorkuota").val();
        console.log(d);
        var post = {
            "c": 10,
            "d": d
        }
        $.ajax({
            url: updatedvc,
            type: "POST",
            data: JSON.stringify(post),
        }).done(function (data) {
            console.log(data);
            alertSuccess(data);
        }).fail(function (xhr) {
            console.log(xhr);
            alertFail("Failed");
        });
    }

    function clearNomor() {
        $("#nomorkuota").val("");
    }
</script>