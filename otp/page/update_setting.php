<?php
session_start();
require_once "../php/const.php";
?>
<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <h1 class="page-title txt-color-blueDark">
            <i class="fa fa-pencil-square-o fa-fw "></i>
            Update Setting
                            <span>
                            </span>
        </h1>
    </div>
    <div class="col-xs-12 col-sm-5 col-md-5 col-lg-8">

    </div>
</div>

<!-- widget grid -->
<section id="widget-grid" class="">
    <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <!-- Widget ID (each widget will need unique ID)-->
        <div class="jarviswidget jarviswidget-color-blueDark" id="wid-id-latest" data-widget-editbutton="false" data-widget-deletebutton="false">
            <header>
                <span class="widget-icon"> <i class="fa fa-table"></i> </span>
                <h2 id="titleCategory">Update Setting</h2>
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
                    <div id="videoAdd-body" class="form-horizontal">
                        <fieldset>
                            <legend>Update Setting</legend>
                            <div class="col-md-9 col-sm-10">
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Api Key</label>
                                    <div class="col-md-10">
                                        <input type="text" name="api_key" id="api_key" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label" for="select-1">Nama Setting</label>
                                    <div class="col-md-10">
                                        <select class="form-control" id="label">

                                        </select>

                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Script</label>
                                    <div class="col-md-10">
                                        <input type="file" name="script" id="script" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Base64</label>
                                    <div class="col-md-10">
                                        <textarea name="base64script" id="base64script" readonly class="form-control" cols="30" rows="4"></textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Active for</label>
                                    <div class="col-md-10">
                                        <input type="checkbox" id="wa" name="wa"><label for="wa"> Whatsapp </label> <input type="checkbox" id="sms" name="sms"><label for="sms"> SMS </label>
                                    </div>
                                </div>
                                <div class="form-actions">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <button class="btn btn-default" type="submit" onclick="clearForm()">
                                                Cancel
                                            </button>
                                            <button class="btn btn-primary" type="submit" onclick="uploadScript()">
                                                <i class="fa fa-save"></i>
                                                Submit
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-2">
                                <img src="" class="img-responsive" alt="" id="poster" width="300">
                            </div>
                            
                        </fieldset>
                    </div>
                </div>
            </div>
            <!-- end widget content -->
        </div>
        <!-- end widget div -->
    </article>
    <!-- end article -->
    <!-- row -->
</section>
<!-- end widget grid -->

<!-- widget grid -->
<section id="widget-grid" class="">
    <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <!-- Widget ID (each widget will need unique ID)-->
        <div class="jarviswidget jarviswidget-color-blueDark" id="wid-id-cur" data-widget-editbutton="false" data-widget-deletebutton="false">
            <header>
                <span class="widget-icon"> <i class="fa fa-table"></i> </span>
                <h2 id="titleCategory">Script</h2>
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
                    <div id="videoAdd-body" class="form-horizontal">
                        <fieldset>
                            <div class="col-md-9 col-sm-10">

                                <div class="form-group">
                                    <textarea name="setting" id="setting" readonly class="form-control" cols="30" rows="50"></textarea>
                                </div>

                            </div>
                            <div class="col-md-3 col-sm-2">
                                <img src="" class="img-responsive" alt="" id="poster" width="300">
                            </div>

                        </fieldset>
                    </div>
                </div>
            </div>
            <!-- end widget content -->
        </div>
        <!-- end widget div -->
    </article>
    <!-- end article -->
    <!-- row -->
</section>
<!-- end widget grid -->

<script type="text/javascript">
    var api_key = "<?= $_SESSION['key']?>",
        session_id = "<?= $_SESSION['session_id']?>",
        apibaseurl = "<?= INF_URL?>",
        parser=[];
        $("#api_key").val(api_key);
    function getLabel(){
        var url = `${apibaseurl}smsbot_v2/get_setting.php?api_key=${api_key}`;
        $.ajax({
            type: "GET",
            url: url
        }).done(function(data) {
            var res = JSON.parse(data),
                labels = "",
                arr = res;

            if(typeof arr === 'undefined')  alertFail("script tidak boleh kosong!");
            else{
                console.log(arr);
                for(var k in arr){
                    labels += `<option>${arr[k].name}</option>`;
                }
                $("#label").html(labels);
                // $("#wa").prop("checked", true);
            }

        });
    }
    function handleFileSelect(evt) {
        var files = evt.target.files; // FileList object
        console.log("blable");
        // use the 1st file from the list
        f = files[0];

        var reader = new FileReader();

        // Closure to capture the file information.
        reader.onload = (function(theFile) {
            return function(e) {
                var b64 = btoa( e.target.result);
                jQuery( '#base64script' ).val(b64);
            };
        })(f);

        // Read in the image file as a data URL.
        reader.readAsText(f);
    }

    document.getElementById('script').addEventListener('change', handleFileSelect, false);
    getLabel();
    $("#label").change(function () {
        getsetting($(this).val());
    })
    $("#filename").change(function () {
        $("#setting").val(atob(parser[$(this).val()]));
    })

    function getsetting(label){
        label = btoa(label);
        var url = `${apibaseurl}smsbot_v2/get_setting_with_name.php?api_key=${api_key}&name_sett=${label}`;
        console.log(url);
        $.ajax({
            type: "GET",
            url: url
        }).done(function(data) {
            var ress = JSON.parse(data);
            console.log(ress);
            $("#setting").val(ress.script);
            if(ress.wa == 1){
                $("#wa").prop("checked", true);
            }else{
                $("#wa").prop("checked", false);
            }
            console.log(ress.sms);
            if(ress.sms == 1){
                $("#sms").prop("checked", true);
            }else{
                $("#sms").prop("checked", false);
            }
        });
    }

    function clearForm(){
        $("#label").val("");
        $("#script").val("");
        $("#base64script").val("");
        $("#wa").val("");
        $("#sms").val("");
    }
    
    function uploadScript() {
        var api_key = $("#api_key").val(),
            name = $("#label").val(),
            script = $("#base64script").val(),
            wa = document.getElementById("wa"),
            sms = document.getElementById("sms");

            if (wa.checked == true){
                wa = "1";
            }else{
                wa = "0";
            }
            if (sms.checked == true){
                sms = "1";
            }else{
                sms = "0";
            }

        console.log($("#script").val());
        if($("#script") == ""){
            alertFail("script tidak boleh kosong!");
            return;
        }
        if(name == "" || name == null){
            alertFail("filename tidak boleh kosong!");
            return;
        }
        var url =  `${apibaseurl}smsbot_v2/update_setting.php?api_key=${api_key}&nama=${name}&script=${script}&wa=${wa}&sms=${sms}`;

        $.confirm({
            title: 'Confirm',
            icon: 'fa fa-exclamation-triangle',
            type: 'orange',
            content: "Pastikan data yang diinput sudah benar!",
            buttons: {
                Ya: function () {
                    $.ajax({
                        type: "GET",
                        url: url
                    }).done(function(data) {
                        console.log(data);
                        var res = data;
                        console.log(res["m"]);
                        if(res["m"] = "Ok"){
                            alertSuccess('Setting berhasil diupload');
                            clearForm();
                        }else{
                            alertFail('error' + JSON.stringify(data));
                        }
                    });
                },
                cancel: function () {
                    //close
                },
            }
        });
       

    }
 
    
</script>