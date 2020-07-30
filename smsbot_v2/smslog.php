<?php

/**
 *  Request
 *  -------
 *  Url: http://dev.motion.co.id/smsbot/queue.php
 *  Method: POST
 *  Body: {"c":1,"i":6282228886446,"s":"isi sms"}
 *
 *  Response
 *  --------
 *  In the format of {"c":x,...}
 *  Parameter "c" value:
 *   1       => OK
 *  -1       => Unknown error
 *  -1000001 => Method not POST
 *  -1000002 => Wrong format it should be in the format of {"c":1,"i":xxx,"s":"xxx"}
 *  -1000003 => Cannot decode body into json
 *  -2000001 => Cannot connect to database server
 *  -2000002 => Cannot prepare database query
 *  -2000003 => Cannot bind parameter in the query
 *  -2000004 => Cannot execute database query
 */

define('DIRECT_ACCESS', true);

require_once('helper/payload.php');
require_once('helper/reply.php');
require_once('helper/endpoint.php');
require_once('helper/database.php');

$reply = replyErrorCommon();

$input = file_get_contents('php://input');
if (!isMethodPost()) {
    $reply = replyErrorMethod();
    goto finish;
}

$json = json_decode($input, true);
if ($json == null) {
    $reply = replyErrorFormat();
    goto finish;
}

if (
    !(array_key_exists(PAYLOAD_CODE, $json) && $json[PAYLOAD_CODE] == API_SMS_LOG) ||
    !array_key_exists(PAYLOAD_MSISDN, $json) ||
    !array_key_exists(PAYLOAD_SMS_CONTENT, $json) ||
    !array_key_exists(PAYLOAD_ERROR, $json) ||
    !array_key_exists(PAYLOAD_DEVICE_ID, $json)
) {
    $reply = replyErrorFormatJson();
    goto finish;
}


$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($mysqli->connect_errno) {
    $reply = replyErrorDatabaseConnect();
    goto finish;
}

$query = 'INSERT INTO `sms_log` (`msisdn`, `content`, `devid`,`status`) VALUES (?, ?, ?, ?)';
$stmt = $mysqli->prepare($query);
if ($stmt === false) {
    $reply = replyErrorDatabasePrepare();
    goto finish;
}

if ($stmt->bind_param('ssis', $msisdn, $smsContent, $devid, $stat) === FALSE) {
    $reply = replyErrorDatabaseBind();
    goto finish;
}

$msisdn = $json[PAYLOAD_MSISDN];
$smsContent = $json[PAYLOAD_SMS_CONTENT];
$stat = $json[PAYLOAD_ERROR];
$devid = $json[PAYLOAD_DEVICE_ID];
// die($json[PAYLOAD_ERROR]);
if ($stmt->execute() === FALSE) {
    $reply = replyErrorDatabaseExecute();
    goto finish;
}

$reply = replyOkCommon();

if(strpos($msisdn, "*")!==false){
    $m = "Check kuota errorpada device ".$devid." segera lakukan pengecekan lebih lanjut pada MMI Code yang digunakan, MMI Code sebelumnya adahal ".$msisdn;
    sendMessage($m);
}

finish:
if (isset($stmt) && $stmt !== false) {
    $stmt->close();
}

if (isset($stmt) && $mysqli !== false) {
    $mysqli->close();
}

$response = json_encode($reply);
die($response);

function sendMessage($c){
    $TOKEN  = "913796608:AAE4mQlAy8odVJnVQesSmC0FZ7qL0KZqxdQ";  // ganti token ini dengan token bot mu
    //$chatid = "431337786"; // ini id saya di telegram @hasanudinhs silakan diganti dan disesuaikan
    //$chatid = array("431337786","95364085","585882561","834307676");//agung, pak helmie, ifa, ardi 
    $chatid = array("834307676","95364085");//ardi,pak helmie
    $pesan  = $c;

    foreach ($chatid as $key => $value) {
        // ----------- code -------------
        $method = "sendMessage";
        //    $method   = "sendDocument";
        $url    = "https://api.telegram.org/bot" . $TOKEN . "/". $method;
        $post = [
            'chat_id' => $value,
            // 'parse_mode' => 'HTML', // aktifkan ini jika ingin menggunakan format type HTML, bisa juga diganti menjadi Markdown
            'text' => $pesan,
            //        "document" => $pesan
        ];
        $header = [
            "X-Requested-With: XMLHttpRequest",
            "User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.84 Safari/537.36"
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        //curl_setopt($ch, CURLOPT_REFERER, $refer);
        //curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post );
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $datas = curl_exec($ch);
        $error = curl_error($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $debug['text'] = $pesan;
        $debug['code'] = $status;
        $debug['status'] = $error;
        $debug['respon'] = json_decode($datas, true);
        // print_r($debug);
    }

}   