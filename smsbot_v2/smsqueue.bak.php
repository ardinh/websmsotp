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
require_once ("vendor/autoload.php");


$client = new Nexmo\Client(new Nexmo\Client\Credentials\Basic("51abfce5", "ayQ22qeqMt6EHlFy"));


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
    !(array_key_exists(PAYLOAD_CODE, $json) && $json[PAYLOAD_CODE] == API_SMS_QUEUE) ||
    !array_key_exists(PAYLOAD_MSISDN, $json) ||
    !array_key_exists(PAYLOAD_SMS_CONTENT, $json) ||
    !array_key_exists(PAYLOAD_KEY, $json)
) {
    $reply = replyErrorFormatJson();
    goto finish;
}

if ($json[PAYLOAD_KEY] !== "765RT-32H23-JK255-176SW") {
    $reply = replyErrorFormat();
    goto finish;
}

$fc = substr($json[PAYLOAD_MSISDN], 0,1);
$sc = substr($json[PAYLOAD_MSISDN], 0,2);
$ssc = substr($json[PAYLOAD_MSISDN], 0,4);

if($fc == "+" && $ssc = "+628"){
    $json[PAYLOAD_MSISDN] = substr_replace($json[PAYLOAD_MSISDN], "", 0,1);   
}else if($fc == 0 && $sc == "08"){
    $json[PAYLOAD_MSISDN] = substr_replace($json[PAYLOAD_MSISDN], "62", 0,1);
}else if($fc == 0 && $sc == "01"){
    $json[PAYLOAD_MSISDN] = substr_replace($json[PAYLOAD_MSISDN], "60", 0,1);
}

if(substr($json[PAYLOAD_MSISDN], 0,3)!=="628"){
    if(substr($json[PAYLOAD_MSISDN], 0,2)=="60" || substr($json[PAYLOAD_MSISDN], 0,3)=="601" || substr($json[PAYLOAD_MSISDN], 0,4)=="+601"){

        if (strlen($json[PAYLOAD_MSISDN])-2 < 9) {
            $reply = replyErrorFormat();
            goto finish;   
        }

        $message = $client->message()->send([
            'to' => $json[PAYLOAD_MSISDN],
            'from' => 'MYDRAKOR',
            'text' => $json[PAYLOAD_SMS_CONTENT]
        ]);
        $stat = false;
        if($message['status'] == 0 ){
            $stat = true;
        }
        // print_r($message);

        $m = array(
                'to' => $message['to'],
                'message_id' => $message['message-id'],
                'status' => $message['status'],
                'remaining_balance' => $message['remaining-balance'],
                'price' => $message['message-price'],
            );
        
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($mysqli->connect_errno) {
            $reply = replyErrorDatabaseConnect();
            goto finish;
        }

        $table = TABLE_SMS_QUEUE;
        $query = "INSERT INTO `$table` (`msisdn`, `content`, `status`, `update_time`) VALUES (?, ?, ?, NOW())";
        $stmt = $mysqli->prepare($query);
        if ($stmt === false) {
            $reply = replyErrorDatabasePrepare();
            goto finish;
        }

        if ($stmt->bind_param('isi', $msisdn, $smsContent, $status) === FALSE) {
            $reply = replyErrorDatabaseBind();
            goto finish;
        }

        $msisdn = $json[PAYLOAD_MSISDN];
        $smsContent = $json[PAYLOAD_SMS_CONTENT];
        $status = STATUS_SENT;
        if ($stmt->execute() === FALSE) {
            $reply = replyErrorDatabaseExecute();
            goto finish;
        }
        sendlog($m['to'],$m['message_id'],$m['status'],$m['remaining_balance'],$m['price']);
        if($m['remaining_balance'] <= 1){
            $m = "Saldo Nexmo API sisa €".$m['remaining_balance']." silahkan top up saldo Nexmo agar API send SMS No Malaysia bisa di jalankan";
            sendMessage($m);
        }
        
        $reply = $m;
        goto finish;
    }else{
        $reply = replyErrorFormat();
        goto finish;
    }
}

if (strlen($json[PAYLOAD_MSISDN])-2 < 10) {
    $reply = replyErrorFormat();
    goto finish;   
}
switch (substr($json[PAYLOAD_MSISDN], 0,4)) {
    case "6280":
        $reply = replyErrorFormat();
        goto finish;
        break;
    case "6284":
        $reply = replyErrorFormat();
        goto finish;
        break;
    case "6286":
        $reply = replyErrorFormat();
        goto finish;
        break;
} 

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($mysqli->connect_errno) {
    $reply = replyErrorDatabaseConnect();
    goto finish;
}

$table = TABLE_SMS_QUEUE;
$query = "INSERT INTO `$table` (`msisdn`, `content`, `status`, `update_time`) VALUES (?, ?, ?, NOW())";
$stmt = $mysqli->prepare($query);
if ($stmt === false) {
    $reply = replyErrorDatabasePrepare();
    goto finish;
}

if ($stmt->bind_param('isi', $msisdn, $smsContent, $status) === FALSE) {
    $reply = replyErrorDatabaseBind();
    goto finish;
}

$msisdn = $json[PAYLOAD_MSISDN];
$smsContent = $json[PAYLOAD_SMS_CONTENT];
$status = STATUS_BEGIN;
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

$response = json_encode($reply);
die($response);

function sendlog($i,$e,$er,$b,$p){
    $curl = curl_init();

    $data = array('c' =>'5' ,
                'i' =>$i ,
                'e' =>$e ,
                'er' =>$er ,
                'b' =>$b ,
                'p' =>$p
            );

    curl_setopt_array($curl, array(
      CURLOPT_URL => "http://dev.motion.co.id/smsbot/smslognotind.php",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => json_encode($data),
      CURLOPT_HTTPHEADER => array(
        "Accept: */*",
        "Accept-Encoding: gzip, deflate",
        "Cache-Control: no-cache",
        "Connection: keep-alive",
        "Content-Length: 94",
        "Content-Type: text/plain",
        "Host: dev.motion.co.id",
        "Postman-Token: f9d39898-f42f-4240-98e1-ac9acae85493,78878fa4-7de9-46a7-a1cf-d3227bac6f2f",
        "User-Agent: PostmanRuntime/7.17.1",
        "cache-control: no-cache"
      ),
    ));

    // print_r($curl);

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
      echo "cURL Error #:" . $err;
    } else {
      echo $response;
    }
}

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