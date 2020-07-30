<?php
/**
 * Created by PhpStorm.
 * User: mobilesolution
 * Date: 2/6/19
 * Time: 4:16 PM
 */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("Access-Control-Allow-Origin: *");
//header("acces")
$l = isset($_GET["l"]) ? $_GET["l"] : false;
$b64 = isset($_GET["b64"]) ? base64_decode($_GET["b64"]) : false;
//echo urlencode($l);die();
if($b64) $l = $b64;
$res = getContent($l);
//var_dump($res);
die($res);
function getContent($url,$type = null, $post = null){
    $ch = curl_init();
    $timeout = 5;
    curl_setopt($ch, CURLOPT_URL, $url);

    if(!empty($type)){
        if($type == "POST" || $type == "GET"){
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);
        }
    }

    if(!empty($post)){
        if(is_array($post)){
            $data = json_encode($post);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
    }
    
    $headers = [
        'Cache-Control: no-cache',
        'User-Agent: Mozilla/5.0 (X11; Linux i686 on x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2909.25 Safari/537.36',
    ];

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
//    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
//        'Content-Type: application/json'
//    ));

    $html = curl_exec($ch);
    $info = curl_getinfo($ch);
//    echo "<pre>";
//    var_dump($info);
    curl_close($ch);
    
    
    return $html;
}