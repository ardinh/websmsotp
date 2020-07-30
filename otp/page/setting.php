<?php
session_start();
require_once "../php/const.php";
?>
<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <h1 class="page-title txt-color-blueDark">
            <i class="fa fa-pencil-square-o fa-fw "></i>
            Add Setting
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
                            <legend>Add Setting</legend>
                            <div class="col-md-9 col-sm-10">
                                <div class="form-group">
                                    <label class="col-md-2 control-label" for="select-1">API Key</label>
                                    <div class="col-md-10">
                                        <input type="text" name="api_key" id="api_key" class="form-control">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label" for="select-1">Setting Name</label>
                                    <div class="col-md-10">
                                        <input type="text" name="n_sett" id="name_sett" class="form-control">
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
                <h2 id="titleCategory">Script Example</h2>
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
        getsetting();
    function getsetting(){
        var url = `${apibaseurl}smsbot_v2/getsetting.php?api_key=${api_key}`;
        $.ajax({
            type: "GET",
            url: url
        }).done(function(data) {
            var res = data.video_label,
                Setting = "";
                setting = data;
                // $("#setting").val(setting);

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
                jQuery( '#base64script' ).val( b64 );
            };
        })(f);

        // Read in the image file as a data URL.
        reader.readAsText(f);
    }

    document.getElementById('script').addEventListener('change', handleFileSelect, false);
    // getLabel();
    $("#label").change(function () {
        getParser($(this).val());
    })
    $("#filename").change(function () {
        $("#currentParser").val(atob(parser[$(this).val()]));
    })

    function getParser(label) {
        var url =  `${apibaseurl}video_label/parser?name=${label}&api_key=${api_key}&session_id=${session_id}`;
        $.ajax({
            type: "GET",
            url: url
        }).done(function(data) {
            var res = data.parser,
                labels = "";
            parser = res;
            for(var k in res){
                parser[res[k].name] = res[k].script;
                labels += `<option>${res[k].name}</option>`;
            }
            $("#filename").html(labels);
            $("#currentParser").val(atob(res[0].script));

        });
    }

    function clearForm(){
        $("#name_sett").val("");
        $("#script").val("");
        $("#base64script").val("");
        $("#wa").val("");
        $("#sms").val("");
    }
    
    function uploadScript() {
        var api_key = $("#api_key").val(),
            name = $("#name_sett").val(),
            script = $("#base64script").val(),
            url =  `${apibaseurl}smsbot_v2/add_setting.php`,
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
        
        var post = {
            c : "11",
            u : api_key,
            ns : name,
            scr : script,
            wa : wa,
            sms : sms
        };
       console.log(post);
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
                        url: url,
                        contentType: "application/json; charset=utf-8",
                        data: JSON.stringify(post),
                        dataType: "json",
                    }).done(function(data) {
                        console.log(data);
                        var res = data;
                        console.log(res["m"]);
                        if(res["m"] = "Ok"){
                            alertSuccess('Setting berhasil diupload ');
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