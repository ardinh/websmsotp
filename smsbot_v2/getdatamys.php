<?php

require_once('helper/database.php');

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($mysqli->connect_errno) {
    $reply = replyErrorDatabaseConnect();
    goto finish;
}

$table = TABLE_SMS_QUEUE;
$status = STATUS_BEGIN;
$query = "SELECT id, msisdn, content, status, insert_time FROM `$table` WHERE SUBSTRING(msisdn, 1, 2) = 60 order by id desc limit 1";
// echo $query;
$stmt = $mysqli->prepare($query);
if ($stmt === false) {
    goto finish;
}

if ($stmt->execute() === FALSE) {
    goto finish;
}

$result = $stmt->get_result();
if ($result === false) {
    goto finish;
}

$ids = array();
$rows = $result->fetch_all(MYSQLI_NUM);
$ip = $_SERVER['REMOTE_ADDR']; 
// $ids['id'] = $rows[0][0];
$ids['msisdn'] = $rows[0][1];
$ids['content'] = $rows[0][2];
$ids['status'] = $rows[0][3];
$ids['inset_time'] = $rows[0][4];
// echo $ip;
header('Content-type: text/javascript');
print_r($ids);
die();
if (count($rows) == 0) {
    $reply = replyOkSms(array());
    goto finish;
}

/*if(count($ids)>0){
    $m = "ada otp yg belum terkirim selama 15 menit, cek koneksi hp otp";

    sendMessage($m);
}*/
die();
finish:
if (isset($stmt) && $stmt !== false) {
    $stmt->close();
}

if (isset($stmt) && $mysqli !== false) {
    $mysqli->close();
}

function sendMessage($c){
    $TOKEN  = "913796608:AAE4mQlAy8odVJnVQesSmC0FZ7qL0KZqxdQ";  // ganti token ini dengan token bot mu
    //$chatid = "431337786"; // ini id saya di telegram @hasanudinhs silakan diganti dan disesuaikan
    //$chatid = array("431337786","95364085","585882561");//agung, pak helmie, ifa
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
        //    die();
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
        print_r($debug);
    }
}
