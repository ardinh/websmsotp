<?php
session_start();
require_once "../php/const.php";
?>
<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <h1 class="page-title txt-color-blueDark">
            <i class="fa fa-pencil-square-o fa-fw "></i>
            Update Parser
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
                <h2 id="titleCategory">Update Parser</h2>
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
                                            <button class="btn btn-primary" type="submit" onclick="uploadParser()">
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
    var api_key = "<?= INF_KEY?>",
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
                arr = res.setting;

            if(typeof arr === 'undefined')  alertFail("script tidak boleh kosong!");
            else{
                for(var k in arr){
                    labels += `<option>${arr[k].name}</option>`;
                    wa += `<option>${arr[k].wa}</option>`;
                    sms += `<option>${arr[k].sms}</option>`;
                }
                $("#label").html(labels);
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
            var setting = data;
            console.log(setting);
            $("#setting").val(setting);

        });
    }

    function clearForm(){
        $("#title").val("");
        $("#poster").val("");
        $("#titles_id").val("");
        $("#media_type").val("");
        $("#release_date").val("");
        $("#season").val("");
        $("#episode").val("");
        $("#url").val("");
        $("#sumber").val("");
        $("#label").val("");
        $("#vtype").val("");
    }
    
    function uploadParser() {
        var label = $("#label").val(),
            parser = $("#filename").val(),
            script = $("#base64script").val(),
            url =  `${apibaseurl}video_label/parser/change?api_key=${api_key}&session_id=${session_id}`;
        console.log($("#script").val());
        if($("#script") == ""){
            alertFail("script tidak boleh kosong!");
            return;
        }
        if(parser == "" || parser == null){
            alertFail("filename tidak boleh kosong!");
            return;
        }
        
        var post = {
            code : 14,
            data : {
                name : label,
                parser : parser,
                script : script
            }
        };
//        console.log(post);
        console.log(url);
        $.confirm({
            title: 'Confirm',
            icon: 'fa fa-exclamation-triangle',
            type: 'orange',
            content: "Pastikan parser yang diinput sudah benar!",
            buttons: {
                Ya: function () {
                    console.log(post);
                    $.ajax({
                        type: "POST",
                        url: "php/inflixer/api.php",
                        contentType: "application/json; charset=utf-8",
                        data: JSON.stringify(post),
                        dataType: "json",
                    }).done(function(data) {
                        console.log(data);
                        var res = data;
                        if(res.bytes_written>0){
                            alertSuccess(res.msg||'parser berhasil diupload '+ JSON.stringify(data));
                        }else{
                            alertFail('error' + JSON.stringify(data));
                        }
                        clearForm();
                    });
                },
                cancel: function () {
                    //close
                },
            }
        });
       

    }
 
    
</script>