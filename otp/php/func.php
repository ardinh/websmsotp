<?php
//require_once 'session.php';
/**
 * Created by PhpStorm.
 * User: mobilesolution
 * Date: 8/16/18
 * Time: 11:16 AM
 */
$curdir = dirname(__FILE__);
require_once($curdir."/const.php");
require_once($curdir."/koneksi.php");

//require_once($curdir."/const.php");
function trimtxt($t){
    return preg_replace('/\v(?:[\v\h]+)/', '', $t);
}
function replaceAccents($str) {
    $search = explode(",",
        "ç,æ,œ,á,é,í,ó,ú,à,è,ì,ò,ù,ä,ë,ï,ö,ü,ÿ,â,ê,î,ô,û,å,ø,Ø,Å,Á,À,Â,Ä,È,É,Ê,Ë,Í,Î,Ï,Ì,Ò,Ó,Ô,Ö,Ú,Ù,Û,Ü,Ÿ,Ç,Æ,Œ");
    $replace = explode(",",
        "c,ae,oe,a,e,i,o,u,a,e,i,o,u,a,e,i,o,u,y,a,e,i,o,u,a,o,O,A,A,A,A,A,E,E,E,E,I,I,I,I,O,O,O,O,U,U,U,U,Y,C,AE,OE");
    return str_replace($search, $replace, $str);
}

function normalizeTitle($title){
    $title .= " ";
    $newtitle = strtolower($title);
    $newtitle = urldecode($newtitle);
    $newtitle = replaceAccents($newtitle);
    $toremove = array(
        " sub indo ",
        "subtitle indonesiaa",
        "subtitle indonesia",
        "subitle indonesia",
        "indonesia sub",
        "nonton dan download",
        "indonesia subtitle",
        "short video",
        "nonton film online",
        "nonton film streaming",
        "nonton streaming",
        "nonton steaming",
        "bioskop keren",
        "nonton drama korea",
        "nonton drama",
        "nonton serial",
        "film seri",
        "di bioskopkeren",
        "bioskopkeren",
        "nonton drakor",
        "nonton movie",
        "nonton bioskop",
        "(ongoing)",
        "(tamat)",
        "nonton film",
        "nonon film",
        "nonton online",
        "nonton",
        "drama korea",
        "chinese movie",
        "india movie",
        "movie india",
        "movie korea",
        "thailand movie",
        "japan movie",
        "korea movie",
        "horror thailand",
        "korean movie",
        "'",
        "jf",
        "anime",
        "gt",
        "&#8217"
    );

    $toremovewithspace = array(
        ":",";","?","'","!",".",",","-","–","&#8211"
    );

    foreach ($toremove as $key => $value) {
        $newtitle = str_replace($value, "", $newtitle);
    }
    foreach ($toremovewithspace as $key => $value) {
        $newtitle = str_replace($value, " ", $newtitle);
    }

    if(substr(trim($newtitle),0,3) != "cam"){
        $newtitle = str_replace(" cam ", "", $newtitle);
    }

    $toremoveyear=array();
    for ($i=1940; $i <= date("Y"); $i++) {
        $toremoveyear[] = $i;
    }

    foreach ($toremoveyear as $key => $value) {
        if(substr(trim($newtitle),0,4) != $value){
            $newtitle = str_replace($value, "", $newtitle);
        }
    }

    //$pos = strpos($newtitle,"season") > 0 ? strpos($newtitle,"season") : strlen($newtitle);
    //$newtitle = substr($newtitle, 0,$pos);

    $pos = strpos($newtitle,"subtitle") > 0 ? strpos($newtitle,"subtitle") : strlen($newtitle);
    $newtitle = substr($newtitle, 0,$pos);

    $newtitle = str_replace("&","and",$newtitle);
    $newtitle = str_replace("()","",$newtitle);
    $newtitle = preg_replace(array('/\s{2,}/', '/[\t\n]/'), ' ', $newtitle);

    return trim(ucwords($newtitle));
}

function getContent($url,$type = null, $post = null,$header = null){
    $ch = curl_init();
    $timeout = 5;
    curl_setopt($ch, CURLOPT_URL, $url);

    if(!empty($type)){
        if($type == "POST" || $type == "GET" || $type == "DELETE"){
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);
        }
    }

    if(!empty($post)){
        if(is_array($post)){
            $data = json_encode($post);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
    }
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    if(is_array($header)){
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    }
    curl_setopt($ch, CURLOPT_VERBOSE, 1);
//    

    $html = curl_exec($ch);
    curl_close($ch);
    return $html;
}

function getyearfromrawtitle($rawtitle){
    //echo $rawtitle;
    $res = null;
    $tmp = explode(" ",$rawtitle);
    foreach ($tmp as $k){
        //echo $k;
        if(is_numeric($k) and strlen($k)==4){
            $res = $k;
            break;
        }
    }
    return trim($res);
}

function isTitleExists($rawtitle,$koneksi){
    if(empty($koneksi)) $koneksi = mysqli_connect("localhost",DB_CRAWLER_USERNAME,DB_CRAWLER_PASS,DB_CRAWLER);
    $tablename = TABLE_CRAWL;
    $sel = "select * from $tablename where rawtitle = '$rawtitle'";
    $res = mysqli_query($koneksi,$sel);
    if(mysqli_num_rows($res)>0){
        return true;
    }
    return false;
}

function getCategoriesBK(){
    $alamatlink = BK_URL;
    $htmlpage = file_get_html($alamatlink);
    $cats = array();
    foreach($htmlpage->find('div.sidebar-right li a') as $e) {
        $cats[] = trim($e->innertext);
    }

    return $cats;
}

function getCrew($inflixerid){
    $ret = array();
    $link = INF_FILM_URL.$inflixerid;    
    $logimdb=getContent($link);
    $casts="https://api.inflixer.com/v3/movie/$inflixerid?api_key=2y10yGuEGjVjDBDWTWIAz4va7uJh60eNfjIG0UDCvYIAvtgvVsy7zX12m";
    //echo $casts;
    $logimdb=getContent($casts);
    $castcariimdb = json_decode($logimdb, true);
    if(isset($castcariimdb['casts'])){
        $casts = $castcariimdb['casts'];
        $ret = $casts['crew'];
    }
    return $ret;
    
}

function get_string_between($string, $start, $end){
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}

function updatekondisiold($koneksi,$tablename,$kondisi,$id,$trxid){
    $q = "update $tablename set kondisi = '$kondisi',trx_id='$trxid' where id = '$id'";

    $simpantitle = mysqli_query($koneksi, $q);

    if ($simpantitle) {
        if(mysqli_affected_rows($koneksi)>0) return true;
    } else {return false;
    }
}

function updatekondisi($koneksi,$tablename,$id,$kondisi,$trxid,$inflixerid=""){
    $infid = "";
    if(!empty($inflixerid)){
        $infid = ",inflixerid = $inflixerid";
    }
    $q = "update $tablename set 
          kondisi = '$kondisi',
          trx_id='$trxid' 
          $infid
          where id = '$id'";
//   echo $q;
    $simpantitle = mysqli_query($koneksi, $q);

    if ($simpantitle) {
        if(mysqli_affected_rows($koneksi)>0) return true;
    } else {return false;
    }
}

function videoAdd($titleid,$payloadVideo){
    $post = array(
        "total_videos" => count($payloadVideo),
        "videos" => $payloadVideo
    );
    $url = INF_VIDEO_ADD_URL;
//    $url = str_replace("[ID]", $titleid, $url);
    $insert = getContent($url,"POST",$post);

    return $insert;
}

function addMovieTMDB($content,$type){
    $url = INF_ADD_MOVIE_TMDB;
    if($type == 'seri'){
        $url = INF_ADD_TV_TMDB;
    }
    $post = array(
        "html" => $content
    );
//    echo $url.PHP_EOL;
//    echo print_r(base64_decode($content)).PHP_EOL;
//    echo print_r($post).PHP_EOL;
//    echo $type.PHP_EOL;
    $sf = getContent($url,"POST",$post);
//    echo $sf.PHP_EOL;
    return $sf;
}

function scrapeFetch($sid,$stype,$mtype,$infid=""){
    $url = INF_SCRAPE_FETCH_URL;
//    var_dump($mtype);
    $url .= "&source_id=$sid&source_type=$stype&media_type=$mtype";

    if(!empty($infid))$url.="&titles_id=$infid";
//    echo $url;
    $insert = getContent($url);
    return $insert;
}

function latestMovie(){
    global $koneksi;
    $ret = array();
    $tablecrawler = TABLE_CRAWL;
    $tableAll = "c_all";
    $q = "select rawtitle,inflixertitle,normaltitle,inflixerid,source,moviepageurl,poster,poster_inflixer,type,director,year,id,kondisi,created from $tableAll
          where created between makedate(year(now()), date_format(now(),'%j')-1) and makedate(year(now()), date_format(now(),'%j')+1) 
          and type != 'koleksi' limit 500";
//          ORDER BY created DESC ";

//    $q .= "UNION select rawtitle,inflixertitle,inflixerid,source,moviepageurl,poster,poster_inflixer,type,director,year,id,kondisi,created from $tableJF$ecrawler
//          where created between makedate(year(now()), date_format(now(),'%j')-1) and makedate(year(now()), date_format(now(),'%j')+1) 
//          and type != 'koleksi'";

    $q = "SELECT * FROM ($q) temp ORDER BY created DESC";
//    echo $q;
    $res = mysqli_query($koneksi,$q);
    $ret = array();
    while ($data = mysqli_fetch_assoc($res)){
        $ret[] = $data;
    }
    return $ret;
}
//
//function totalCrawled_all($source,$type = null){
//    global $koneksi;
//    $ret = array();
//    $q = "select count(*) as total from c_all WHERE source LIKE '$source'";
//    if(!empty($type)) $q .= " AND type = '$type'";
//    $res = mysqli_query($koneksi,$q);
//    $ret = array();
//    $data = mysqli_fetch_assoc($res);
//    //echo $q;
//    return $data['total'];
//}

function totalCrawled($tablecrawler = TABLE_CRAWL,$source = null,$type = null){
    global $koneksi;
    $ret = array();
    $q = "select count(*) as total from $tablecrawler";
    if(!empty($type)) $q .= " where type = '$type'";
    if(!empty($source) && !empty($type)) $q .= " and source = '$source'";
    if(!empty($source) && empty($type)) $q .= " where source = '$source'";
    $res = mysqli_query($koneksi,$q);
    $ret = array();
    $data = mysqli_fetch_assoc($res);
    //echo $q;
    return $data['total'];
}
function totalDataPerSource($type = null,$stat=null){
    global $koneksi;
    $ret = array();
    $w = "";
    if(!empty($type)) $w .= " type = '$type' and";
    if(!empty($stat)){
        if($stat==1){
            $w .= " (kondisi like '%Fetch%' or kondisi like 'On%') and";
        }elseif($stat==2){
            $w .= " ((kondisi not like '%Fetch%' and kondisi not like 'On%')or kondisi is null) and";
        }
    }

    if(!empty($w)){
        $w = "where $w";
        $w = substr($w,0,-3);
    }
    $q = "select source,count(*) as total from c_all $w group by source";


    $res = mysqli_query($koneksi,$q);
    $ret = array();
    while ($data = mysqli_fetch_assoc($res)){
        $ret[$data['source']] = $data['total'];
    }

    //echo $q;
    return $ret;
}

function totalMatched($tablecrawler = TABLE_CRAWL,$source = null,$type = null){
    global $koneksi;
    $ret = array();
    $q = "select count(*) as total from $tablecrawler
    where (kondisi like '%Fetch%' or kondisi like 'On%')";
    if(!empty($source)) $q .= " and source = '$source'";
    if(!empty($type)) $q .= " and type = '$type'";
//    echo $q;
    $res = mysqli_query($koneksi,$q);
    $ret = array();
    $data = mysqli_fetch_assoc($res);

    return $data['total'];
}

function totalUnmatched($tablecrawler = TABLE_CRAWL,$source = null,$type = null){
    global $koneksi;
    $ret = array();
    
    $q = "select count(*) as total from $tablecrawler
    where (kondisi not like '%Fetch%' and kondisi not like 'On%')";
    if(!empty($type)) $q .= " and type = '$type'";
    if(!empty($source)) $q .= " and source = '$source'";
    
    $res = mysqli_query($koneksi,$q);
    $ret = array();
    $data = mysqli_fetch_assoc($res);

    return $data['total'];
}

function latestEpisode(){
    global $koneksi;
    $tablecrawler = "c_all";
    $tableseasons = TABLE_CRAWL_SEASONS;
    $tablepisodes = "c_all_episodes";
    $q = "
select $tablecrawler.rawtitle,$tablecrawler.inflixerid,$tablecrawler.poster,$tablecrawler.moviepageurl,$tablecrawler.id as title_id,$tablecrawler.kondisi as kondisiseri,$tablecrawler.normaltitle,$tablecrawler.year,$tablecrawler.director,
       $tablecrawler.season,$tablecrawler.source, 
       $tablepisodes.number as episode,episodepageurl, $tablepisodes.created, $tablepisodes.updated,$tablepisodes.kondisi
from $tablepisodes
join $tablecrawler on $tablecrawler.id = $tablepisodes.`title_id` 
where $tablepisodes.created between makedate(year(now()), date_format(now(),'%j')-1) 
and makedate(year(now()), date_format(now(),'%j')+1) 
ORDER by $tablepisodes.created DESC limit 1000";
//echo $q;
    $res = mysqli_query($koneksi,$q);
    $ret = array();
    while ($data = mysqli_fetch_assoc($res)){
        $ret[] = $data;
    }
    return $ret;
}

function getConfig($key = ""){
    global $koneksi;
    $tableconf = TABLE_CONF;
   
    $q = "select * from $tableconf ";
    if(!empty($key)) $q .= " where conf_key='$key'";
    $res = mysqli_query($koneksi,$q);
    $ret = array();
    while ($data = mysqli_fetch_assoc($res)){
        $ret[] = $data;
    }
    return $ret;
}

function lastRun($name='log.txt'){
    $filename = '/var/www/robo.inflixer.com/v5/server/'.$name;
    if (file_exists($filename)) {
        return date ("Y-m-d H:i:s", filemtime($filename));
    }
}

function replaceDomain($url,$conf_key){
    $latestDomain = getConfig($conf_key)[0]["conf_value"];
    $parse = parse_url($latestDomain);
//    $latestDomain = $parse['host'];
//    $prevdomain = get_string_between($url,"://","/");
    $latestDomain = $parse['scheme']."://".$parse['host'];//
    $parse = parse_url($url);//
    $prevdomain = $parse['scheme']."://".$parse['host'];//
    $url = str_replace($prevdomain,$latestDomain,$url);
    return $url;
}

//gdrive
function fetchMovieAnime($id){
    global $koneksi;
    $tablecrawler = TABLE_CRAWL_ANIME;
    $q = "select *
from $tablecrawler
where $tablecrawler.id = $id";

    $res =-1;
    $select = mysqli_query($koneksi,$q);
    $msg = "";
    $data = mysqli_fetch_assoc($select);

    $inflixerid = $data['inflixerid'];

    $payload = array(
        "label"     => "Gold X",
        "url"       => $data['embed'],
        "subtitle"  => "",
        "season"    => null,
        "episode"   => null,
        "type"      => "embed"
    );

    $sf = scrapeFetch($inflixerid,array($payload));
    $sfd = json_decode($sf,true);

    if($sfd['success']==true and $sfd['total_videos']>0){
        $res=1;
        $msg = 'data berhasil difetch,'.$sf;

        updatekondisi($koneksi,$tablecrawler,'Fetch Done Admin',$data['id']);
    }else{
        $msg = 'data gagal difetch,'.'<br>'.$sf.'<br>';
    }

    $return = array(
        "code" => $res,
        "msg" => $msg
    );

    return $return;
}

function fetchEpisodeAnime($id,$season=null,$episode=null){
    global $koneksi;
    $tablename = TABLE_CRAWL_ANIME_SERI;
    $tablecrawler = TABLE_CRAWL_ANIME_SERI;
    // $tableseasons = TABLE_CRAWL_SEASONS;
    $tablepisodes = TABLE_CRAWL_ANIME_EPISODE;
    $q = "select $tablecrawler.inflixerid,$tablepisodes.rawtitle, $tablecrawler.season, $tablepisodes.episode, $tablepisodes.link_video,$tablepisodes.created, $tablepisodes.update, $tablepisodes.kondisi, $tablecrawler.kondisi as kondisiseri, $tablepisodes.id as epid, $tablepisodes.link_video from $tablepisodes
    join $tablecrawler on $tablepisodes.fk_anime = $tablecrawler.id
    where $tablecrawler.id = $id";

    if(!empty($season)) $q .= " and $tablecrawler.season=$season";
    if(!empty($episode)) $q .= " and $tablepisodes.episode=$episode";
    // echo $q.PHP_EOL;
    // die();
    $res =-1;
    $select = mysqli_query($koneksi,$q);
    $msg = "";
    $kondisiepisode = 'Siap Fetch';

    $msgall = "";
    if(mysqli_num_rows($select)>0){
        while ($data = mysqli_fetch_assoc($select)){
            $inflixerid = $data['inflixerid'];
            $season = $data['season'];
            $episode = $data['episode'];
            // echo $data['rawtitle']." | ".$data['season']." | ".$data['episode']." | ".$data['epid'];
            if($data['kondisiseri']=='Siap Fetch' || $data['kondisiseri']=='Siap Fetch By Reduce'){
                if(is_numeric($inflixerid)){
                    $res = 1;
                    $msg = 'data berhasil diubah menjadi siap fetch';
                    $payload = array(
                        "label"     => "Gold X",
                        "url"       => $data['link_video'],
                        "subtitle"  => "",
                        "season"    => $season,
                        "episode"   => $episode,
                        "type"      => "embed"
                    );
                    $sf = scrapeFetch($inflixerid,array($payload));

                    $sfd = json_decode($sf,true);
                    // $sfd['success']=true;
                    // $sfd['total_videos'] = 1;
                    // $sf = "berhasil";
                    if($sfd['success']==true and $sfd['total_videos']>0){
                        $msg = 'data berhasil difetch';
                        $kondisiepisode = 'Fetch Done Admin';
                    }
                    $uk = updatekondisi($koneksi,$tablepisodes,$kondisiepisode,$data['epid']);
                    $msgall .= "S{$season}Eps{$episode} ".$msg."<br>".$sf."<br>";
                }
            }
        }
        $select->close();
    }

    $return = array(
        "code" => $res,
        "msg" => $msgall
    );

    return $return;
}

function totalCrawledAnimes($type = null){
    global $koneksi;
    $ret = array();
    $tablecrawler = TABLE_CRAWL_ANIME;
    if($type=='seri'){
        $tablecrawler = TABLE_CRAWL_ANIME_SERI;
    }
    $q = "select count(*) as total from $tablecrawler";
    // if(!empty($type)) $q .= " where type = '$type'";
    $res = mysqli_query($koneksi,$q);
    $ret = array();
    $data = mysqli_fetch_assoc($res);

    if($type=='total'){
        $tablecrawler = TABLE_CRAWL_ANIME_SERI;
        $q = "select count(*) as total from $tablecrawler";
        // if(!empty($type)) $q .= " where type = '$type'";
        $res = mysqli_query($koneksi,$q);
        $ret = array();
        $d = mysqli_fetch_assoc($res);
        $data['total'] = $data['total'] +$d['total'];
    }
    return $data['total'];
}

function totalMatchedAnime($type = null){
    global $koneksi;
    $ret = array();
    $tablecrawler = TABLE_CRAWL_ANIME;
    if($type=='seri'){
        $tablecrawler = TABLE_CRAWL_ANIME_SERI;
    }
    $q = "select count(*) as total from $tablecrawler
    where kondisi like '%Fetch%'";
    // if(!empty($type)) $q .= " and type = '$type'";
    $res = mysqli_query($koneksi,$q);
    $ret = array();
    $data = mysqli_fetch_assoc($res);
    if($type=='total'){
        $tablecrawler = TABLE_CRAWL_ANIME_SERI;
        $q = "select count(*) as total from $tablecrawler
    where kondisi like '%Fetch%'";
        // if(!empty($type)) $q .= " where type = '$type'";
        $res = mysqli_query($koneksi,$q);
        $ret = array();
        $d = mysqli_fetch_assoc($res);
        $data['total'] = $data['total'] +$d['total'];
    }
    return $data['total'];
}

function totalUnmatchedAnime($type=null){
    global $koneksi;
    $ret = array();
    $tablecrawler = TABLE_CRAWL_ANIME;
    if($type=='seri'){
        $tablecrawler = TABLE_CRAWL_ANIME_SERI;
    }
    $q = "select count(*) as total from $tablecrawler
    where kondisi not like '%Fetch%'";
    // if(!empty($type)) $q .= " and type = '$type'";

    $res = mysqli_query($koneksi,$q);
    $ret = array();
    $data = mysqli_fetch_assoc($res);

    if($type=='total'){
        $tablecrawler = TABLE_CRAWL_ANIME_SERI;
        $q = "select count(*) as total from $tablecrawler
    where kondisi not like '%Fetch%'";
        // if(!empty($type)) $q .= " where type = '$type'";
        $res = mysqli_query($koneksi,$q);
        $ret = array();
        $d = mysqli_fetch_assoc($res);
        $data['total'] = $data['total'] +$d['total'];
    }
    return $data['total'];
}

function latestMovieAnime(){
    global $koneksi;
    $ret = array();
    $tablecrawler = TABLE_CRAWL_ANIME;
    $q = "select * from $tablecrawler
          where created between makedate(year(now()), date_format(now(),'%j')-1)
          and makedate(year(now()), date_format(now(),'%j')+1)
          ORDER BY created DESC ";

    $res = mysqli_query($koneksi,$q);
    $ret = array();
    while ($data = mysqli_fetch_assoc($res)){
        $ret[] = $data;
    }
    return $ret;
}

function latestEpisodeAnime(){
    global $koneksi;
    $tablecrawler = TABLE_CRAWL_ANIME_SERI;
    $tablepisodes = TABLE_CRAWL_ANIME_EPISODE;
    $q = "
select $tablecrawler.rawtitle,$tablecrawler.inflixerid,$tablecrawler.poster,$tablecrawler.webpageurl,$tablecrawler.id as title_id,$tablecrawler.kondisi as kondisiseri,$tablecrawler.normaltitle,$tablecrawler.season,
       $tablepisodes.episode,link_video, $tablepisodes.created, $tablepisodes.update,$tablepisodes.kondisi
from $tablepisodes
join $tablecrawler on $tablecrawler.id = $tablepisodes.`fk_anime`
where $tablepisodes.created between makedate(year(now()), date_format(now(),'%j')-1)
and makedate(year(now()), date_format(now(),'%j')+1)
ORDER by $tablepisodes.created DESC ";

// echo $q.PHP_EOL;
// die();

    $res = mysqli_query($koneksi,$q);
    $ret = array();
    while ($data = mysqli_fetch_assoc($res)){
        $ret[] = $data;
    }
    return $ret;
}

function getVideoSourceOrder(){
    $url = INF_VIDEOLABEL_ORDER;
    $res = getContent($url);
    $rd = json_decode($res);
    return $rd;
}

function getLabel($source){
    $configs = getConfig($source."_label");
    if(!empty($configs)){
        $label = $configs[0]['conf_value'];
    } else {
        $label = $source;
    }       
    return $label;
}

function getBaseUrl($source){
    $configs = getConfig($source."_baseurl");
    if(!empty($configs)){
        $baseurl = $source."_baseurl";
    } else {
        $baseurl = "";
    }    
    return $baseurl;
}

function getVideoType($source){
    $configs = getConfig($source."_videotype");
    if(!empty($configs)){
        $videotype = $configs[0]['conf_value'];
    } else {
        $videotype = "external";
    }       
    return $videotype;
}

//LOGIN

function getRequestToken(){    
    $url = INF_CREATE_REQUEST_TOKEN;
    $res = getContent($url);
    $rd = json_decode($res);
    if($rd->success=true){
        $token = $rd->request_token;        
    } else {
        $token = "";
    }
    return $token;
}
function sendOTP($token,$phone) { 
    $post=array(
        "phone" => $phone,
        "request_token" => $token
    );
    $res = getContent(INF_VALIDATE_WITH_LOGIN,"POST",$post);
    $rd = json_decode($res);
    if($rd->success=true){
        $ret = true;
        $_SESSION['status_message']=$rd->status_message;
    } else {
        $ret = false;
        $_SESSION['status_message']=$rd->status_message;
    }
    return $ret;
}

function validateWithGoogle($token,$email,$first_name,$last_name,$avatar) { 
    $post=array(
        "id" => "eytriqw3476iw374td",
        "email" => $email,
        "first_name" => $first_name,
        "last_name" => $last_name,
        "avatar" => $avatar,
        "request_token" => $token,
    );
    $res = getContent(INF_VALIDATE_WITH_GOOGLE,"POST",$post);
//    echo print_r($res);die();
    $rd = json_decode($res);
    if($rd->success=true){
        $session_id = $rd->session_id;
        $_SESSION['session_id']=$session_id;
        $_SESSION['username'] = $first_name;
        $_SESSION['avatar'] = $avatar;
        checkAccount();
    } else {
        $session_id = "";
    }
    return $session_id;
}

function validateWithGoogleAccount($token,$email) { 
    $post=array(
        "email" => $email,
        "request_token" => $token,
    );
    $res = getContent(INF_VALIDATE_WITH_GOOGLE,"POST",$post);
//    echo print_r($res);die();
    $rd = json_decode($res);
    if($rd->success=true){
        $session_id = $rd->session_id;
        checkAccount();
    } else {
        $session_id = "";
    }
    return $session_id;
}

function checkAccount(){
    // $permission = "";
    // $url = INF_ACCOUNT;
    // // echo $url;
    // $res = getContent($url,"GET");    
    // $rd = json_decode($res);
    // $ra = json_decode($res,true);  
    // // print_r($res);
    // print_r($rd);
    // $_SESSION['permission_api'] = $rd->permissions;
    // if($rd->permissions=="superuser"){
    //     $_SESSION['level'] = 100;
    //     if(!isset($_SESSION['username'])){
    //         $_SESSION['username'] = $rd->username;
    //         $_SESSION['avatar'] = $rd->avatar;
    //     }
    // } else {
        $_SESSION['level'] = 0;
    // }
}

function createSessionID($token,$phone) { //with login
    $session_id = "";
    $post=array(
        "phone" => $phone,
        "request_token" => $token
    );
    $res = getContent(INF_VALIDATE_WITH_LOGIN,"POST",$post);
    $rd = json_decode($res);
    if($rd->success=true){
        $otp = $rd->otp;
        $post2=array(
            "otp" => $otp,
            "request_token" => $token
        );
        $res2 = getContent(INF_VERIFY,"POST",$post2);
        $rd2 = json_decode($res2);
        if($rd2->success=true){
            $session_id = $rd2->session_id;
        }
    } 
    return $session_id;
}

function console_log($output, $with_script_tags = true) {
    $js_code = 'console.log(' . json_encode($output, JSON_HEX_TAG) . 
');';
    if ($with_script_tags) {
        $js_code = '<script>' . $js_code . '</script>';
    }
    echo $js_code;
}