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
    !(array_key_exists(PAYLOAD_CODE, $json) && $json[PAYLOAD_CODE] == API_SMS_IN) ||
    !array_key_exists(PAYLOAD_MSISDN, $json) ||
    !array_key_exists(PAYLOAD_SMS_CONTENT, $json) ||
    !array_key_exists(PAYLOAD_KUOTA, $json) ||
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

$query = 'INSERT INTO `sms_in_kuota` (`msisdn`, `content`, `devid`, `kuota`) VALUES (?, ?, ?, ?)';
// $query = 'INSERT INTO `sms_in` (`msisdn`, `content`, `devid`,`kuota`) VALUES (?, ?, ?, ?)';
$stmt = $mysqli->prepare($query);
if ($stmt === false) {
    $reply = replyErrorDatabasePrepare();
    goto finish;
}

if ($stmt->bind_param('ssii', $msisdn, $smsContent, $devid,$kuota) === FALSE) {
    $reply = replyErrorDatabaseBind();
    goto finish;
}

$msisdn = $json[PAYLOAD_MSISDN];
$smsContent = $json[PAYLOAD_SMS_CONTENT];
$kuota = $json[PAYLOAD_KUOTA];
$devid = $json[PAYLOAD_DEVICE_ID];
if ($stmt->execute() === FALSE) {
    $reply = replyErrorDatabaseExecute();
    goto finish;
}

$reply = replyOkCommon();

finish:
if (isset($stmt) && $stmt !== false) {
    $stmt->close();
}

if (isset($stmt) && $mysqli !== false) {
    $mysqli->close();
}
if($kuota < 500){
    $m = "Sisa kuota device ".$devid." berjumlah ".$kuota." sms segera isikan kuota agar device tidak error";
    // print_r($m);
    // die();
    sendMessage($m);
    die();
}

$response = json_encode($reply);
die($response);

function sendMessage($c){
    $TOKEN  = "913796608:AAE4mQlAy8odVJnVQesSmC0FZ7qL0KZqxdQ";  // ganti token ini dengan token bot mu
    //$chatid = "431337786"; // ini id saya di telegram @hasanudinhs silakan diganti dan disesuaikan
    //$chatid = array("431337786","95364085","585882561","834307676");//agung, pak helmie, ifa, ardi 
    $chatid = array("834307676","95364085");//ardi
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

function callCheckKuota($c){

    for ($i=0; $i < count($c); $i++) { 
        $id = $c[$i];
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => "http://localhost/smsbot/smskuota.php",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => "{\"c\":6,\"d\":\"$id\"}",
          CURLOPT_HTTPHEADER => array(
            "Accept: */*",
            "Accept-Encoding: gzip, deflate",
            "Cache-Control: no-cache",
            "Connection: keep-alive",
            "Content-Length: 15",
            "Content-Type: application/json",
            "Host: dev.motion.co.id",
            "Postman-Token: 998e2ac1-ae6d-4047-bbd1-acba18864699,45a9b194-00c1-4034-8c03-c48d282bb835",
            "User-Agent: PostmanRuntime/7.17.1",
            "cache-control: no-cache"
          ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
          echo "cURL Error #:" . $err;
        } else {
          echo $response;
        }
    }
    
}