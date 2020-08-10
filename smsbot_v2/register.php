<?php

define('DIRECT_ACCESS', true);

require_once('helper/payload.php');
require_once('helper/reply.php');
require_once('helper/endpoint.php');
require_once('helper/database.php');
require_once ("vendor/autoload.php");

$reply = replyErrorCommon();

$firstname = $_GET['f'];
$lastname = $_GET['l'];
$username = $_GET['u'];
$password = $_GET['p'];
$email = $_GET['e'];
$level = 0;
$api_key = $password;
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($mysqli->connect_errno) {
    $reply = replyErrorDatabaseConnect();
    goto finish;
}

$table = TABLE_SMS_QUEUE;

$query  = "INSERT INTO user (`username`, `password`, `level`, `api_key`) VALUES (?, ?, ?, ?)";
$stmt = $mysqli->prepare($query);
if ($stmt === false) {
    $reply = replyErrorDatabasePrepare();
    goto finish;
}

if ($stmt->bind_param('ssis', $username, $password, $level,$api_key) === FALSE) {
        $reply = replyErrorDatabaseBind();
        goto finish;
    }

$status = STATUS_SENT;
if ($stmt->execute() === FALSE) {
    $reply = replyErrorDatabaseExecute();
    goto finish;
}


// $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
// if ($mysqli->connect_errno) {
//     $reply = replyErrorDatabaseConnect();
//     goto finish;
// }

// $table = TABLE_SMS_QUEUE;
// $query = "INSERT INTO `$table` (`msisdn`, `content`, `status`, `update_time`) VALUES (?, ?, ?, NOW())";
// $stmt = $mysqli->prepare($query);
// if ($stmt === false) {
//     $reply = replyErrorDatabasePrepare();
//     goto finish;
// }

// if ($stmt->bind_param('isi', $msisdn, $smsContent, $status) === FALSE) {
//     $reply = replyErrorDatabaseBind();
//     goto finish;
// }

// $msisdn = $json[PAYLOAD_MSISDN];
// $smsContent = $json[PAYLOAD_SMS_CONTENT];
// $status = STATUS_BEGIN;
// if ($stmt->execute() === FALSE) {
//     $reply = replyErrorDatabaseExecute();
//     goto finish;
// }


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