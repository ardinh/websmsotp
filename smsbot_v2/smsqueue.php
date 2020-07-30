<?php

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

$noind = array('814','815','816','855','856','857','858');
$no = array('817','818','819','859','877','878','838','831','832','833');
$no3 = array('895','896','897','898','899');
$noind = array('814','815','816','855','856','857','858');
if(in_array(substr($json[PAYLOAD_MSISDN], 2,3),$no3)){
    if (strlen($json[PAYLOAD_MSISDN])-2 < 10) {
        $reply = replyErrorFormat();
        goto finish;   
    }
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($mysqli->connect_errno) {
        $reply = replyErrorDatabaseConnect();
        goto finish;
    }
    $query = "SELECT session FROM session_zenziva";
    $stmt = $mysqli->prepare($query);
    if ($stmt === false) {
        $reply = replyErrorDatabasePrepare();
        goto finish;
    }

    if ($stmt->execute() === FALSE) {
        $reply = replyErrorDatabaseExecute();
        goto finish;
    }

    $result = $stmt->get_result();
    if ($result === false) {
        $reply = replyErrorDatabaseResult();
        goto finish;
    }

    $rows = $result->fetch_all(MYSQLI_NUM);

    if (count($rows) == 0) {
        $reply = replyOkSms(array());
        goto finish;
    }


    for ($i = 0; $i < count($rows); $i++) {
        $row = $rows[$i];
    }
    $header = $row[0];

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

    zenziva($header,$json[PAYLOAD_SMS_CONTENT],$json[PAYLOAD_MSISDN]);
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

function zenziva($header,$pesan,$msisdn){
    $msisdn = ltrim($msisdn,"62");
    $msisdn = "0".$msisdn;
    $curl = curl_init();
    $header1 = $header;
    echo $msisdn;
    echo $header;
    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://gsm.zenziva.net/dashboard/single/send",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HEADER => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => "pesan=$pesan&notuj=$msisdn",
      CURLOPT_HTTPHEADER => array(
        "Accept: */*",
        "Accept-Encoding: gzip, deflate",
        "Cache-Control: max-age=0",
        "Connection: keep-alive",
        "Content-Type: application/x-www-form-urlencoded; charset=utf-8",
        "Host: gsm.zenziva.net",
        "Postman-Token: cb25b881-a2fb-4a2f-a97e-37c66e440950,48b57a9b-9d53-4ae0-8361-2411e87fb96a",
        "User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.92 Safari/537.36",
        "cache-control: no-cache",
        "cookie: z_session=$header"
      ),
    ));

    $output = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
      echo "cURL Error #:" . $err;
    } else {
      $headers = [];
      $output = rtrim($output);
      $data = explode("\n",$output);
      print_r($data);
      if(strpos($data[7], "Set-Cookie") !== false){
        $header = $data[7];
        $header = get_string_between($header,"z_session=",";");
        echo $header;
        if($header == "deleted"){
            sendMessage("session salah mohon ambil manual pada website zenziva");
            return;
        }
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($mysqli->connect_errno) {
            $reply = replyErrorDatabaseConnect();
            goto finish;
        }
        $query = "UPDATE session_zenziva SET session='$header' where id = 1";
        echo $query;
        $stmt = $mysqli->prepare($query);
        if ($stmt === false) {
            $reply = replyErrorDatabasePrepare();
            goto finish;
        }

        if ($stmt->execute() === FALSE) {
            $reply = replyErrorDatabaseExecute();
            goto finish;
        }

        finish:
        if (isset($stmt) && $stmt !== false) {
            $stmt->close();
        }

        if (isset($stmt) && $mysqli !== false) {
            $mysqli->close();
        }
      }

    }
}

function get_string_between($string, $start, $end){
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}