<?php
require_once '../php/session.php';
require_once "../php/const.php";
include '../../htmldom/simple_html_dom.php';
include '../php/koneksi.php';
require_once "../php/func.php";

$theader = array(
    "Source Title",
    "Source Poster",
    "Poster INF",
    "Normal Title",
    "Title Inflixer",
    "ID Inflixer",
    "Type",
    "Kondisi",
    "Created",
    "Updated",
    "Aksi"
);
$thead = "";
foreach ($theader as $h) {
    $thead .= "<th>$h</th>";
}

$theader = array(
    "Source Title",
    "Source Poster",
    "ID Inflixer",
    "Season",
    "Episode",
    "Kondisi Seri",
    "Kondisi",
    "Created",
    "Updated",
    "Aksi"
);
$theadeps = "";
foreach ($theader as $h) {
    $theadeps .= "<th>$h</th>";
}

$theaderanime = array(
    "Title Gdrive",
    "Poster Gdrive",
    "Poster INF",
    "Normal Title",
    "Title Inflixer",
    "ID Inflixer",
    "Status",
    "Kondisi",
    "Created",
    "Updated",
    "Aksi"
);
$theadanime = "";
foreach ($theaderanime as $h) {
    $theadanime .= "<th>$h</th>";
}

$theader = array(
    "Title Gdrive",
    "Poster Gdrive",
    "ID Inflixer",
    "Season",
    "Episode",
    "Kondisi Seri",
    "Kondisi",
    "Created",
    "Updated",
    "Aksi"
);
$theadepsanime = "";
foreach ($theader as $h) {
    $theadepsanime .= "<th>$h</th>";
}

$totalCrawledM = totalDataPerSource("film");
$totalCrawledS = totalDataPerSource("seri");

$totalCrawled = array_merge($totalCrawledM, $totalCrawledS);

$totalMatchedM = totalDataPerSource("film", 1);
$totalMatchedS = totalDataPerSource("seri", 1);

$totalUnMatchedM = totalDataPerSource("film", 2);
$totalUnMatchedS = totalDataPerSource("seri", 2);

$level = isset($_SESSION['level']) ? $_SESSION['level'] : null;
$pagetitle = "Dashboard";
$imgbaseurl = 'https://api.inflixer.com/img/w185';
$data = latestMovie();
$latestepisode = latestEpisode();

$configs = getConfig();
$tbody = "";

$newbody = "";
foreach ($data as $k => $v) {
    $id = $v['id'];
    $type = $v['type'];
    $inflixerid = $v['inflixerid'];
    $rawtitle = $v['rawtitle'];
    $movieurl = $v['moviepageurl'];
    $poster = $v['poster'];
    $poster2 = strpos($v['poster_inflixer'], 'https') !== false ? $v['poster_inflixer'] : $imgbaseurl . $v['poster_inflixer'];
    $type = $v['type'];
    $normtitle = $v['normaltitle'];
    $tahun = $v['year'];
    $director = $v['director'];
    $kondisi = $v['kondisi'];
    $source = $v['source'];
    $trxid = $v['trx_id'];
    $kondisil = "";
    $aksi = "";
    if (is_numeric($inflixerid)) {
        $linkinflixer = INF_URL . $type . "/" . $inflixerid;
        $linkinflixer = "<a href='$linkinflixer' target='_blank'>$inflixerid</a>";
    } else {
        $linkinflixer = str_replace("|", "\n", $inflixerid);
    }
    $titlehref = "<a href='$movieurl' target='_blank'>$rawtitle</a>";

    if ($type == 'film') {
        $typel = '<span class="label label-info"><i class="fa fa-film"></i> film</span>';
    } elseif ($type == 'seri') {
        $typel = '<span class="label label-success"><i class="fa fa-desktop"></i> seri</span>';
    } elseif ($type == 'koleksi') {
        $typel = '<span class="label label-default"><i class="fa fa-cubes"></i> koleksi</span>';
    }

    $td1 = " <div class='moviefilm'>
                    <img src='$poster' width='100px'>
                </div>";
    $posterinf = " <div class='moviefilm'>
                    <img src='$poster2' width='100px'>
                </div>";
//    if(strpos(strtolower($normtitle),"season")!==false and $type=='film'){
//        $aksi = "<button type='button' class='btn btn-danger btn-md' onclick='changeType($id)'>Change Type</button> &nbsp;";
//    }
//    if(strpos($kondisi,"Siap")!==false and $type=='film'){
//        $aksi .= "<button type='button' class='btn btn-success btn-md' onclick='fetchmovieUniversal(\"$source\",$id)'>Add Video</button> &nbsp;";
//    }
////    $aksi .= "<button type='button' class='btn btn-info btn-md btnmodalnya' data-toggle='modal' data-target='#myModal' data-tahun='$tahun' data-director='$director' data-link='$linkvideo' data-normaltitle='$normtitle' data-title='$rawtitle' data-alamat='$movieurl' data-poster='$poster' data-subtitle='$linksubtitle' data-list='' data-id='$id'>Detail</button>";
//    $aksi .= "<button type='button' class='btn btn-info btn-md btnmodalnyaa' data-toggle='modal' data-target='#myModal' 
//                onclick='detailMovieUniversal(\"$source\",$id,\"$rawtitle\",\"$normtitle\",\"$poster\",\"$movieurl\",\"$tahun\",\"$director\",\"$type\")' >Detail</button>";
//    
    if (strpos($kondisi, "Siap") !== false) {
        if ($type == 'film' or $type == 'koleksi') {
        $aksi .= "<button type='button' class='btn btn-success btn-md btn-act' onclick='fetchmovieUniversal(\"$source\",$id)'><i class='fa fa-arrow-right'></i> Add Video</button> ";
        }
    } elseif (strpos($kondisi, "Fetch Done") !== false) {
        if ($type == 'film' or $type == 'koleksi') {
            $aksi .= "<button type='button' class='btn btn-success btn-md btn-act' onclick='fetchmovieUniversal(\"$source\",$id)'><i class='fa fa-arrow-right'></i> Add Video</button> ";
        }  
    } elseif (strpos($kondisi, "Process") !== false) {
        $aksi .= "<button type='button' class='btn btn-success btn-md btn-act' onclick='fetchstatusUniversal(\"$source\",$id,\"$trxid\",\"$type\")'><i class='fa fa-arrow-right'></i> Cek Statusss</button> ";
    } elseif (strpos($kondisi, "scrape/fetch") !== false) {
        $aksi .= "<button type='button' class='btn btn-success btn-md btn-act' onclick='fetchstatusUniversal(\"$source\",$id,\"$trxid\",\"$type\")'><i class='fa fa-arrow-right'></i> Cek Statuss</button> ";
    }
    $aksi .= "<button type='button' class='btn btn-info btn-md btn-act' data-toggle='modal' data-target='#myModal'
                onclick='detailMovieUniversal(\"$source\",$id,\"$rawtitle\",\"$normtitle\",\"$poster\",\"$movieurl\",\"$tahun\",\"$director\",\"$type\")' ><i class='fa fa-info-circle'></i> Detail</button> &nbsp; ";
    
    
    $label = $kondisi;
    if ($level != 100 || empty($label)) $label = "Belum Diinisialisasi";
    if (strpos($kondisi, "Done") !== false) {
        $kondisil = '<span class="label label-success"><i class="fa fa-thumbs-up"></i> ' . str_replace("Fetch Done", "Sudah diupdate", $kondisi) . '</span>';
    } elseif (strpos($kondisi, "Process") !== false) {
        $kondisil = '<span class="label label-success"><i class="fa fa-spinner"></i> On Process</span>';
    } elseif (strpos($kondisi, "Siap") !== false) {
        $kondisil = '<span class="label label-info"><i class="fa fa-thumbs-up"></i> ' . str_replace("Siap Fetch", "Siap diupdate", $kondisi) . '</span>';
    } else {
        $kondisil = '<span class="label label-warning"><i class="fa fa-exclamation-circle"></i> ' . $label . '</span>';
    }
    $source = "<img src=\"http://www.google.com/s2/favicons?domain=" . parse_url($movieurl)['host'] . "\">&nbsp;$source";
    $newbody .= "<div class=\"movie-card-container\"> 
                                <div class=\"movie-card-header\">
                                    <div class=\"movie-card-poster\">
                                        <label></label>
                                        <img class=\"movie-poster\" src=\"$poster\">
                                    </div>
                                    <div class=\"movie-card-poster\">
                                        <label></label>
                                        <img class=\"movie-poster\" src=\"$poster2\">   
                                    </div>
                                </div>
                                <div class=\"movie-card-body\">
                                    <strong>{$titlehref}</strong>
                                    <table width='100%' class='table'>
                                    <tr><td width='65px' valign='top'>TitleINF</td><td><strong>{$v['inflixertitle']}</strong></td></tr>
                                    <tr><td>InflixerID</td><td><strong>{$linkinflixer}</strong></td></tr>
                                    <tr><td>Sumber</td><td>$source</td></tr>
                                    <tr><td>Type</td><td>$typel</td></tr>
                                    <tr><td>Kondisi</td><td>$kondisil</td></tr>
</table>
                                </div>                                                  
                                    
                                    <div class='movie-card-footer'>
                                    <div class='kondisil'></div>
                                    
                                    <div class='card-btn'>
                                    $aksi
</div>
                                    
                                    
</div>
                            </div>";
    $tbody .= "
        <tr>
            <td>{$titlehref}</td>
            <td>{$td1}</td>
            <td>{$posterinf}</td>
            <td>{$normtitle}</td>
            <td>{$v['inflixertitle']}</td>
            <td>{$linkinflixer}</td>
            <td>{$type}</td>
            <td>{$kondisil}</td>
            <td>{$v['created']}</td>
            <td>{$v['updated']}</td>
            <td>{$aksi}</td>
        </tr>   
    ";
}

$tbodyeps = "";
$dontreplace = array("ongoingdrakor","drakorsubind");
foreach ($latestepisode as $k => $v) {
    $id = $v['title_id'];
    $inflixerid = $v['inflixerid'];
    $rawtitle = $v['rawtitle'];
    $movieurl = $v['moviepageurl'];
    $epsurl = $v['episodepageurl'];
    $poster = $v['poster'];
    $normtitle = $v['normaltitle'];
    $tahun = $v['year'];
    $director = $v['director'];
    $season = $v['season'];
    $source = $v['source'];
    if(!in_array($source,$dontreplace)){
        $movieurl = replaceDomain($movieurl,getBaseUrl($source));
    }
    $eps = $v['episode'];
    $kondisi = $v['kondisi'];
    $kondisil = "";
    if (strpos($kondisi, "Done") !== false) {
        $kondisil = '<span class="label label-success"><i class="fa fa-thumbs-up"></i> ' . str_replace("Fetch Done", "Sudah diupdate", $kondisi) . '</span>';
    } elseif (strpos($kondisi, "Siap") !== false) {
        $kondisil = '<span class="label label-info"><i class="fa fa-thumbs-up"></i> ' . str_replace("Siap Fetch", "Siap diupdate", $kondisi) . '</span>';
    } elseif (strpos($kondisi, "Api") !== false) {
    $kondisil = '<span class="label label-warning"><i class="fa fa-exclamation-circle"></i> ' . $kondisi . '</span>';
    } elseif (strpos($kondisi, "On") !== false) {
    $kondisil = '<span class="label label-success"><i class="fa fa-spinner"></i> ' . $kondisi . '</span>';
    } else {
        $kondisil = '<span class="label label-warning"><i class="fa fa-exclamation-circle"></i> ' . $label . '</span>';
}
    
    if (is_numeric($inflixerid)) {
        $linkinflixer = INF_URL . "seri/" . $inflixerid;
        $linkinflixer = "<a href='$linkinflixer' target='_blank'>$inflixerid</a>";
    } else {
        $linkinflixer = str_replace("|", "\n", $inflixerid);
    }
    $aksi = "";
    $titlehref = "<a href='$movieurl' target='_blank'>$rawtitle</a>";
    $epshref = "<a href='$epsurl' target='_blank'>{$eps}</a>";
    $td1 = " <div class='moviefilm'>
                    <img src='$poster' width='100px'>
                </div>";
    
    if (strpos($v['kondisiseri'], 'Siap Fetch') !== false and strpos($kondisi, 'Done') === false and strpos($kondisi, 'On') === false)
    $aksi .= "<button type='button' class='btn btn-success btn-md' 
                onclick='fetchepisodeUniversal(\"$source\",$id,$season,$eps)' >Fetch</button>";

    $tbodyeps .= "
        <tr>
            <td>{$titlehref}</td>
            <td>{$td1}</td>
            <td>{$linkinflixer}</td>
            <td>{$v['season']}</td>
            <td>{$epshref}</td>
            <td>{$v['kondisiseri']}</td>
            <td>{$kondisil}</td>
            <td>{$v['created']}</td>
            <td>{$v['updated']}</td>
            <td>$aksi</td>
        </tr>   
    ";
}

$checklist = array();

$tbodyconf = "";
foreach ($configs as $k => $v) {
    $key = $v['conf_key'];
    $id = $v['id'];
    $status = "Ok";
    $statusIcon = "fa-check";
    $statusClass = "success";
    $value = $v['conf_value'];
    $note = "";
    $labelStat = '<span class="label label-success"><i class="fa fa-check"></i> OK</span>';
    $label = $v['label'];

    if (strpos($key, "baseurl") !== false and strpos($value, 'http') !== false) {
        $statusClass = "info";
        $statusIcon = "fa-spinner fa-spin";
        $status = "checking";
        $checklist[$id] = $value;
    }else{
        continue;
    }

    $aksi = "<button onclick='editConf(\"$key\",\"$label\")' class='btn btn-primary' id='btnSubmitConf_$key' value='0'>Edit</button></td>";
    $namebox = strtok($label, ' ');
    $tbodyconf .= "
        <tr>
            <td><a href = '$value' target=blank class='$namebox'>{$label}</a></td>
            <td><input type='text' class='form-control' id='txtConf_$key' value='$value' readonly></td>
            <td id='status_$id'><span class='label label-$statusClass'><i class='fa $statusIcon'></i> $status</span>$note</td>
            <td>{$v['updated']}</td>
            <td>{$aksi}</td>
        </tr>   
    ";

}

$videoSourceOrder = array();//getVideoSourceOrder();
//print_r($videoSourceOrder);

$sortList = "";

foreach ($videoSourceOrder as $k => $v) {
    $checked = "";
//    print_r($v);
    if ($v->visible == 1)
        $checked = "checked";
    $sortList .= '<li class="ui-state-default" id="' . $v->name . '"><h3 class="btn btn-info">' . $v->name . '<span class="onoffswitch">
                                                                        <input type="checkbox" ' . $checked . ' name="start_interval" class="onoffswitch-checkbox" id="visible_' . $v->name . '">
                                                                        <label class="onoffswitch-label" for="visible_' . $v->name . '">
                                                                    <span class="onoffswitch-inner" data-swchon-text="visible" data-swchoff-text="off"></span>
                                                                    <span class="onoffswitch-switch"></span>
                                                                </label>
															</span></h3></li>';
}

?>
<style>
    #sortable-container {
        width: 250px
    }

    #sortable-container #spinner {
        text-align: center
    }

    #sortable {
        list-style-type: none;
        margin: 0;
        padding: 0;
        width: 250px
    }

    #sortable li {
        margin: 3px 3px 3px 0;
        border: none;
        cursor: grab
    }

    #sortable h3 {
        margin: 0;
        width: 100%;
        text-align: left
    }

    #sortable .onoffswitch {
        margin: 0;
        float: right
    }

    .movie-cards-container {
        margin: auto;
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
    }

    .movie-card-container {
        padding: 5px;
        margin: 5px;
        max-width: 250px;
        border: 1px solid rgba(0, 0, 0, .125);
        background: #fbfbfb;
        position: relative;
    }

    .movie-card-header {
        margin: auto;
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        padding-bottom: 10px;
    }

    .movie-card-poster {
        display: grid;
        padding: 2px;
    }

    .movie-poster {
        width: 115px;
        height: 150px;
        object-fit: cover;
    }

    .card-btn {
        bottom: 5px;
        right: 5px;
        position: absolute;
    }

    .movie-card-body {
        /*top: 160px;*/
        /*bottom: 0;*/

        /*position: absolute;*/
    }

    .movie-card-footer {
        padding-top: 25px;
        /*bottom: 5px;*/
        /*position: absolute;*/
    }

    .kondisil {

    }

    .movie-card-body table td {
        border-top: none;
    }
</style>
<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <h1 class="page-title txt-color-blueDark">
            <i class="fa fa-pencil-square-o fa-fw "></i>
            <?= $pagetitle ?>
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

        <!-- start row -->

        <div class="row">
            <h2 class="row-seperator-header"><i class="fa fa-info-circle"></i> Info</h2>

            <div class="col-sm-12">

                <!-- well -->
                <div class="well">
                    <!-- row -->
                    <div class="row">
                        <!-- col -->
                        <div class="col-sm-12"></p>
                            <!-- row -->
                            <div class="row">

                                <div class="col-md-6">
                                    <p>Informasi jumlah data</p>

                                    <?php
                                    $navtabs = "";
                                    $tabContent = "";
                                    $idx = 0;
                                    $logContent = "";
                                    foreach ($totalCrawled as $k => $v) {
                                        $class = "";
                                        $idx++;
                                        if ($idx == 1)
                                            $class = "active in";
                                        $tabtitle = strtoupper($k);
                                        $tabname = $k;
                                        $configkey = $k."_label";
//                                        print_r($configs);
                                        if($pos = array_search($configkey,array_column($configs,"conf_key"))){
                                            $tabname = $configs[$pos]['conf_value'];
                                        }
                                        $tabname=getLabel($k);
                                        $total1 = $totalCrawledM[$k] + $totalCrawledS[$k];
                                        $total2 = $totalMatchedM[$k] + $totalMatchedS[$k];
                                        $total3 = $totalUnMatchedM[$k] + $totalUnMatchedS[$k];
                                        $navtabs .= "<li class='$class'><a data-toggle='tab' href='#$k' class='$tabname'>$tabname</a></li>";
                                        $tabContent .= "<div id=\"$k\" class=\"tab-pane fade $class\">
                                            <table class=\"table table-bordered \">
                                                <thead>
                                                <tr>
                                                    <th style=\"width:50%\">{$tabtitle}</th>
                                                    <th style=\"width:15%\">Movie</th>
                                                    <th style=\"width:15%\">Seri</th>
                                                    <th style=\"width:15%\">Total</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr>
                                                    <td>Judul yang telah dicrawling</td>
                                                    <td><span class=\"badge bg-color-blue\">{$totalCrawledM[$k]}</span></td>
                                                    <td><span class=\"badge bg-color-blue\">{$totalCrawledS[$k]}</span></td>
                                                    <td><span class=\"badge bg-color-blue\">{$total1}</span></td>
                                                </tr>
                                                <tr>
                                                    <td>Berhasil dicocokan</td>
                                                    <td><span class=\"badge bg-color-green\">{$totalMatchedM[$k]}</span></td>
                                                    <td><span class=\"badge bg-color-green\">{$totalMatchedS[$k]}</span></td>
                                                    <td><span class=\"badge bg-color-green\">{$total2}</span></td>
                                                </tr>
                                                <tr>
                                                    <td>Belum dicocokan</td>
                                                    <td><span class=\"badge bg-color-red\">{$totalUnMatchedM[$k]}</span></td>
                                                    <td><span class=\"badge bg-color-red\">{$totalUnMatchedS[$k]}</span></td>
                                                    <td><span class=\"badge bg-color-red\">{$total3}</span></td>
                                                </tr>

                                                </tbody>
                                            </table>
                                        </div>";

                                        //UNTUK LOG BIAR GA HARDCODE MULU
                                        $lastrun = lastRun("api/log/$k.txt");
                                        $logContent.="
                                         <tr>
                                            <td class='$tabname'>$tabname</td>
                                            
                                            <td>
                                                <span class=\"label label-success\">$lastrun</span>
                                            </td>
                                            <td>
                                                <button onclick=\"slog('api/log/$k.txt')\" class=\"btn btn-xs btn-default\">
                                                    log
                                                </button>
                                            </td>
                                        </tr>";
                                    }

                                    ?>
                                    <ul class="nav nav-tabs">
                                        <?= $navtabs ?>
                                    </ul>
                                    <div class="tab-content">
                                        <?= $tabContent ?>
                                        </div>

                                    <p>Urutan Link Film di MyDrakor</p>
                                    <div id="sortable-container">
                                        <div id="spinner"><i class="fa fa-spinner fa-spin fa-3x"></i></div>
                                        <div id="sortable-body" style="display: none">
                                            <ul id="sortable"></ul>
                                            <button class="btn btn-primary btn-block btnSaveList">Simpan Perubahan Urutan</button>
                                        </div>
                                       
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <p>Daftar script yang berjalan saat ini</p>
                                    <table class="table table-bordered">
                                        <thead>
                                        <tr>
                                            <th>Crawler</th>
                                            
                                            <th style="width:40%">Last Execute</th>
                                            <th style="width:30%">Log</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?= $logContent?>
                                        <tr>
                                            <th>Script lain</th>
                                            
                                            <th style="width:40%">Last Execute</th>
                                            <th style="width:30%">Log</th>
                                        </tr>
                                        <tr>
                                            <td>Cron All (search/scrape hingga video/add)</td>
                                            
                                            <td>
                                                <span class="label label-success"><?= lastRun("universal/cronlog_all.txt") ?></span>
                                            </td>
                                            <td>
                                                <button onclick="slog('universal/cronlog_all.txt')"
                                                        class="btn btn-xs btn-default">log
                                                </button>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>

                                    <p>Konfigurasi Baseurl</p>
                                    <table class="table table-bordered">
                                        <thead>
                                        <tr>
                                            <th style="width:20%">Source</th>
                                            <th style="width: 100%">Baseurl</th>
                                            <th style="width: 50%">Status</th>
                                            <th style="width:50%">Updated</th>
                                            <th style="width:50%">Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?= $tbodyconf ?>
                                        </tbody>
                                    </table>


                                </div>

                            </div>
                            <!-- end row -->
                        </div>
                        <!-- end col -->
                    </div>
                    <!-- end row -->
                </div>
                <!-- end well -->

            </div>

        </div>

        <!-- end row -->

        <!-- Widget ID (each widget will need unique ID)-->
        <div class="jarviswidget jarviswidget-color-blueDark" id="wid-id-latest" data-widget-editbutton="false"
             data-widget-deletebutton="false">
            <header>
                <span class="widget-icon"> <i class="fa fa-table"></i> </span>
                <h2>Latest Movie/Seri (1 days ago) </h2>

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
                    <div class="table-responsive" style="max-height: 500px">
                        <table id="latest" class="table table-striped table-bordered table-hover" width="100%">
                            <thead>
                            <tr>
                                <?php echo $thead ?>
                            </tr>
                            </thead>
                            <tbody>
                            <?= $tbody ?>
                            </tbody>
                        </table>
                    </div>
                    <!-- end table-responsive  -->
                </div>
            </div>
            <!-- end widget content -->
        </div>
        <!-- end widget div -->


        <!-- Widget ID (each widget will need unique ID)-->
        <div class="jarviswidget jarviswidget-color-blueDark" id="wid-id-latest-eps" data-widget-editbutton="false"
             data-widget-deletebutton="false">
            <header>
                <span class="widget-icon"> <i class="fa fa-table"></i> </span>
                <h2>Latest Episode (1 days ago) </h2>
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
                    <div class="table-responsive" style="max-height: 500px">
                        <table id="latest" class="table table-striped table-bordered table-hover" width="100%">
                            <thead>
                            <tr>
                                <?= $theadeps ?>
                            </tr>
                            </thead>
                            <tbody>
                            <?= $tbodyeps ?>
                            </tbody>
                        </table>
                    </div>
                    <!-- end table-responsive  -->
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
<!-- Modal -->
<div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog modal-sm">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Data Film</h4>
            </div>
            <div class="modal-body">

                <div class="col-sm-6" style="overflow-x: scroll">
                    <h3 id="judulfilmbioskopkeren"></h3>
                    <table class="table table-responsive">
                        <tr>
                        <td>Judul Film :</td>
                            <td id="judulfilm"></td>
                        </tr>
                        <tr>
                        <td>Tahun :</td>
                            <td id="tahunFilm"></td>
                        </tr>
                        <tr>
                        <td>Director :</td>
                            <td id="director"></td>
                        </tr>
                        <tr>
                        <td>Poster Film :</td>
                            <td id="Posterfilm"></td>
                        </tr>
                        <tr>
                            <td id="video" colspan=2></td>
                        </tr>
                        <tr>
                            <td id="season" colspan=2></td>
                        </tr>
                        <tr>
                            <td id="episode" colspan=2></td>
                        </tr>
                    </table>
                </div>

                <div class="col-sm-6">
                    <h3 style="display: none">Untuk Manual Input:</h3>
                    <input type="hidden" class="input-sm" id="table-id" name="table-id">
                    <table class="table table-responsive hidden">
                        <tr>
                            <td>Link Inflixer :</td>
                            <td><input type="text" class="input-sm" id="link-inflixer" name="link-inflixer"></td>
                            <td>
                                <button type="submit" class="btn btn-success " onclick="submitlinkUniversal('fa')"
                                        id="btnsubmitlink">Submit
                                </button>
                            </td>
                        </tr>
                    </table>


                    <h3>Hasil Pencarian</h3>
                    <input type="text" name="query" id="query" class="form-control">
                    <table width="100%">
                        <tr>
                            <td>
                                <div id="list-info" style="display: none">
                                    <div id="list-info-total"></div>
                                    <div id="btn-search-container" style='float: right'>
                                        <button id="btn-prev" class='btn btn-primary btn-xs' onclick='searchTitleUniversal('fa')'><i class='fa fa-arrow-left'></i>Prev</button>
                                        <button id="btn-next"class='btn btn-primary btn-xs' onclick='searchTitleUniversal('fa')'> Next <i class='fa fa-arrow-right'></i></button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr><td><div id="list" style="max-height: 75vh;overflow-y: scroll"></div></td></tr>
                    </table>
                    
                    
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>
<!-- Modal -->
<div class="modal fade " id="modal_label_domain" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    &times;
                </button>
                <h4 class="modal-title" id="labeldomainfor">Label Domain for </h4>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-md-6 col-md-offset-3" id="labeldomain">
                        <div id="spinner"><i class="fa fa-spinner fa-spin fa-3x"></i></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">
                    Close
                </button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<script type="text/javascript">
    $(function () {
        $("#sortable").sortable({
            change: function (event, ui) {
                // console.log(ui);
            },
            update: function (event, ui) {
                var data = $("#sortable").sortable('serialize', {key: "sort"});
                var index = ui.item.index();
            console.log(index);
                // POST to server using $.post or $.ajax
                // $.ajax({
                //     data: data,
                //     type: 'POST',
                //     url: '/your/url/here'
                // });
            }
        });
        $("#sortable").disableSelection();
    });
    $(".btnSaveList").click(function () {
        var newlist = $("#sortable").sortable("toArray");
        $.post(baseurl + "php/updateVideoList.php", {list: JSON.stringify(newlist)})
            .done(function (data) {
                alertSuccess("urutan video berhasil diupdate");
            })
    });
    $('#sortable').on('click', 'input[name="start_interval"]', function () {
        console.log($(this).attr("id"));
        var el = this;
        var name = $(this).attr("id").replace("visible_", "");
        var post = {
            name: name,
            order: $(this).data("order") + 1,
            visible: this.checked
        };
        if (this.checked) {
            $.post(baseurl + "php/updateVideoList.php", {data: JSON.stringify(post)})
                .done(function (data) {
                    //console.log(JSON.stringify(post));
                    alertSuccess(data);
                    //update color di Informasi jumlah data
                    var substring = name.split(' ', 1)[0];
                    $('.'+substring).css("color","black");
                })
        } else {
            $.confirm({
                title: 'Confirm',
                icon: 'fa fa-exclamation-triangle',
                type: 'orange',
                content: `Yakin akan menonaktifkan ${name}?`,
                buttons: {
                    Ya: function () {
                        $.post(baseurl + "php/updateVideoList.php", {data: JSON.stringify(post)})
                            .done(function (data) {
                                alertSuccess(data);
                                //update color di Informasi jumlah data
                                var substring = name.split(' ', 1)[0];
                                $('.'+substring).css("color","red");
                            })
                    },
                    cancel: function () {
                        $(el).prop('checked', true);
                    },
                }
            });
        }
        
       
    });
    $('#sortable').on('click', 'input[name="pnotif_cb"]', function () {
//        console.log($(this).attr("id"));
        var el = this;
        var name = $(this).attr("id").replace("pnotif_", "");  
        var act = this.checked;
        if (this.checked) {
            $.post(baseurl + "php/pushNotif.php", {name:name, act:'1' })
                .done(function (data) {
                    alertSuccess(data);
                })
        } else {
            $.confirm({
                title: 'Confirm',
                icon: 'fa fa-exclamation-triangle',
                type: 'orange',
                content: `Yakin akan menonaktifkan push notifikasi dari ${name}?`,
                buttons: {
                    Ya: function () {
                        $.post(baseurl + "php/pushNotif.php", {name:name, act:'0'})
                            .done(function (data) {
                                alertSuccess(data);
                            })
                    },
                    cancel: function () {
                        $(el).prop('checked', true);
                    },
                }
            });
        }
   
       
    });
   
    //$("#myModal").modal();
    // DO NOT REMOVE : GLOBAL FUNCTIONS!
    var level = '<?= $level?>';
    pageSetUp();
    $("#query").keyup(function (e) {
        if (e.key == " " || e.key == "Enter") {
            var q = $(this).val();
            searchTitleUniversal('<?= $source?>', $('#table-id').val(), mtype, 1, q);
        }
    });
//    datapost = <?//= json_encode($datapost)?>// ;
    var checklist = JSON.parse('<?= json_encode($checklist)?>');
//    console.log(checklist);
    for (var key in checklist) {
//        console.log(checklist[url]);
//        console.log(key);
        checkUrl(checklist[key] + "sda", key);
    }
    
    function checkUrl(url, key) {
//        fetch("https://dramaqu.fun/",{mode:"no-cors"})
//            .then(function(response) {
//                console.log(response.status); // returns 200
//                console.log(response);
//                console.log(response.statusText);
//            });
        $.ajax({
            url: baseurl + "php/checkUrl.php",
            type: "POST",
            data: {url: url},
            async: true
        }).done(function (data) {
            var label = "<span class='label label-danger'><i class='fa fa-exclamation-circle'></i> " + data + "</span>";
            if (data.indexOf("200")) {
                label = "<span class='label label-success'><i class='fa fa-check'></i> " + data + "</span>";
            } else {

            }
            $("#status_" + key).html(label);
        }).fail(function (xhr) {
            var label = "<span class='label label-danger'><i class='fa fa-exclamation-circle'></i> " + xhr.statusText + "</span>";
            $("#status_" + key).html(label);
//            console.log(xhr);
        });
    }
    $.get("http://dev.motion.co.id/robo/v6/cpanel/php/getVideoSourceList.php").done(function (d) {
        console.log(d);
        var pn = "";
        $.get(baseurl + "php/pushNotif.php").done(function (p) {
//            console.log(p);
            pn = p;
            var content = "";
//            console.log(pn);
            for (var k in d.video_label) {
                data = d.video_label[k];
    //            var source = getSource(data.name);
                var checked = data.visible ? "checked" : "";
                var checkedpn = pn.includes(data.name) ? "checked" : "";
//                console.log(pn.includes(data.name));
                content += `<li class="ui-state-default" id="${data.name}">
                        <h3 class="btn btn-default"><span data-toggle='modal' data-target='#modal_label_domain' onclick='getLabelDomains("${data.name}")'>${data.name}</span>
                        <span class="onoffswitch">
                        <input type="checkbox" ${checked} name="start_interval" class="onoffswitch-checkbox" id="visible_${data.name}" data-order="${k}">
                        <label class="onoffswitch-label" for="visible_${data.name}">
                        <span class="onoffswitch-inner" data-swchon-text="visible" data-swchoff-text="off"></span>
                        <span class="onoffswitch-switch"></span>
                        </label></span> 
                        <span class="onoffswitch">
                        <input type="checkbox" ${checkedpn} name="pnotif_cb" class="onoffswitch-checkbox" id="pnotif_${data.name}">
                        <label class="onoffswitch-label" for="pnotif_${data.name}">
                        <span class="onoffswitch-inner" data-swchon-text="notif" data-swchoff-text="off"></span>
                        <span class="onoffswitch-switch"></span>
                        </label></span>       
                        </h3></li>`;
            if(checked==""){//update color di Informasi jumlah data
                var substring = data.name.split(' ', 1)[0];
                $('.'+substring).css("color","red");
            }
        }
        $("#spinner").hide();
        $("#sortable-body").show();
        $("#sortable").html(content);
        });
        
    }).fail(function () {
        
    });
    
    function getLabelDomains(label){ //bisa di dev ONLY
        $("#labeldomainfor").html("Label Domain for "+label);
        var url = "<?=INF_LABEL_DOMAIN?>";
        url +=label;
        $.get( url).done(function(data) {
            var res = data.domains;
            var html = ``;
            $.each( res, function( index, value ) {
//                alert( key + ": " + value );
                html +=`<div class='row' style='padding:2px'>
                        <input type='text' class='form-control col-xs-3 col-sm-3 col-md-3 col-lg-3' value='${value}' id='domain_${index}' style='width:70%' readonly> 
                        <button onclick='changeDomain("${label}","${index}")' class='btn btn-primary pull-right col-xs-3 col-sm-3 col-md-3 col-lg-3' id='btn-edit-domain-${index}' value='0'>Edit</button>
                        <br></div>`;
            });            
//            data = JSON.stringify(data.domains);
            $("#labeldomain").html(html);
        }).fail(function (jqXHR, textStatus, error) {
            $("#labeldomain").html(jqXHR.responseText);
        });
    }
    
    function getLabelDomain2(label){ //bisa di prod ONLY
        $("#labeldomainfor").html("Label Domain for "+label);
        var post = {
            "code":49, 
            "name":label
        }
        $.ajax({
            url:"php/inflixer/api.php",
            type: "POST",
            data: JSON.stringify(post),
        }).done(function (data) {
//            console.log(data);
            var res = data.domains;
            var html = ``;
            $.each( res, function( index, value ) {
//                alert( key + ": " + value );
                html +=`<div class='row' style='padding:2px'>
                        <input type='text' class='form-control col-xs-3 col-sm-3 col-md-3 col-lg-3' value='${value}' id='domain_${index}' style='width:70%' readonly> 
                        <button onclick='changeDomain("${label}","${index}")' class='btn btn-primary pull-right col-xs-3 col-sm-3 col-md-3 col-lg-3' id='btn-edit-domain-${index}' value='0'>Edit</button>
                        <br></div>`;
            });            
//            data = JSON.stringify(data.domains);
            $("#labeldomain").html(html);
        }).fail(function (xhr) {
            console.log(xhr);
            $("#labeldomain").html("Error");
        });
    }
    
    $('input').on('focusin', function(){
        console.log("Saving value " + $(this).val());
        $(this).data('val', $(this).val());
    });
    
    var prev,current="";
    function changeDomain(label,index){
        var submitType = $('#btn-edit-domain-'+index).val();
        if(submitType==0){
            $(this).data('prev', $('#domain_'+index).val());
            prev = $(this).data('prev');
            console.log(prev);
            $('#domain_'+index).prop('readonly',false).focus();
            $('#btn-edit-domain-'+index).val(1);
            $('#btn-edit-domain-'+index).html('Save');
        }else if(submitType==1){
            $(this).data('current', $('#domain_'+index).val());
            current = $(this).data('current');
            console.log(current);
            //==================
            $.confirm({
                    title: 'Confirm',
                    icon: 'fa fa-exclamation-triangle',
                    type: 'orange',
                    content: `Yakin akan mengganti domain <b>${label}</b>  dari <b>`+prev+`</b> menjadi <b>`+current+'</b> ?',
                    buttons: {
                        Ya: function () {
                             var post = {
                                "code":24, 
                                "name":label,
                                "old_domain":prev,
                                "new_domain":current
                            }
                            console.log(JSON.stringify(post));
                            $.ajax({
                                url:"php/inflixer/api.php",
                                type: "POST",
                                data: JSON.stringify(post),
                            }).done(function (data) {
                                console.log(data);
                                var datastr = JSON.stringify(data);
                                if(data.name){
                                    alertSuccess('Success<br>'+datastr);
                                }else if(data.success==false){
                                    alertFail('Fail<br>'+datastr);
                                }
                            }).fail(function (xhr) {
                                alertFail('Error');
                            });

                            $('#domain_'+index).prop('readonly',true);
                            $('#btn-edit-domain-'+index).val(0);
                            $('#btn-edit-domain-'+index).html('Edit');
                        },
                        cancel: function () {
                            $('#domain_'+index).val(prev);
                            $('#domain_'+index).prop('readonly',true);
                            $('#btn-edit-domain-'+index).val(0);
                            $('#btn-edit-domain-'+index).html('Edit');
                        },
                    }
                });
        }
    }
    
    function slog(name) {
        $.confirm({
            title: 'Log',
            closeIcon: true,
            content: 'url:http://dev.motion.co.id/robo/v6/'+name,
            onContentReady: function () {
                var self = this;
            },
            //columnClass: 'xlarge',
            boxWidth: "100%",
            useBootstrap: false
        });
    }
    
    pageSetUp();

    var pagefunction = function () {

        ajaxtable = $('#latest').dataTable({
            "sDom": "<'dt-toolbar'<'col-xs-12 col-sm-6'f><'col-sm-6 col-xs-12 hidden-xs'l>r>" +
                "t" +
            "<'dt-toolbar-footer'<'col-sm-6 col-xs-12 hidden-xs'i><'col-xs-12 col-sm-6'p>>",
            "autoWidth": true,
            //"ordering" : false,
            "oLanguage": {
                "sSearch": '<span class="input-group-addon"><i class="glyphicon glyphicon-search"></i></span>'
            },
//            "scrollY":     "70vh",
//            "scrollCollapse": true,
            "order": [], //Initial no order.
            // Load data for the table's content from an Ajax source
        });
    }


    loadScript("js/plugin/datatables/jquery.dataTables.min.js", function () {
        loadScript("js/plugin/datatables/dataTables.bootstrap.min.js", function () {
            loadScript("js/plugin/datatable-responsive/datatables.responsive.min.js", pagefunction)
        });
    });

</script>
