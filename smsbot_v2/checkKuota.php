<?php

require_once('helper/database.php');
// print_r($input);
// die();

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($mysqli->connect_errno) {
    $reply = replyErrorDatabaseConnect();
    goto finish;
}

date_default_timezone_set('Asia/Jakarta');

$balance = getBalance();
if($balance['value'] <= 1){
    $m = "Saldo Nexmo API sisa €".$balance['value']." silahkan top up saldo Nexmo agar API send SMS No Malaysia bisa di jalankan";
    // sendMessage($m);
}
$balance = array(1100, 'Your Balance Value is €'.str_replace(".", ",", $balance['value']), '€'.str_replace(".", ",", $balance['value']), date("Y-m-d H:i:s"));
// print_r(json_encode($balance));
// die();
$table = TABLE_SMS_IN_KUOTA;
$status = STATUS_BEGIN;
$query = "SELECT sms_in_kuota.devid, sms_in_kuota.content, sms_in_kuota.kuota, sms_in_kuota.insert_time,sms_in_kuota.msisdn FROM sms_in_kuota WHERE sms_in_kuota.id in (SELECT MAX(sms_in_kuota.id) from sms_in_kuota where DATE_ADD(insert_time,INTERVAL 1 DAY) > now() GROUP by sms_in_kuota.devid) ORDER BY sms_in_kuota.devid asc";
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
array_push($rows, json_decode(json_encode($balance),true));

if (count($rows) == 0) {
    $reply = replyOkSms(array());
    goto finish;
}

/*for ($i = 0; $i < count($rows); $i++) {
    $row = $rows[$i];
    $ids[] = $row[0];
}*/

// print_r($ids);
print_r(json_encode($rows));
die();
if(count($ids)>0){
    $d = 0;
    if(count($ids) < $input){
        $d = $input - count($ids);
        print_r($d);
        $m = "check device OTP! Device yang berjalan hanya ".$d;
    }else{
        $m = "semua device berjalan lancar";
    }

    sendMessage($m);
}
die();
finish:
if (isset($stmt) && $stmt !== false) {
    $stmt->close();
}

if (isset($stmt) && $mysqli !== false) {
    $mysqli->close();
}

function getBalance(){
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://rest.nexmo.com/account/get-balance?api_key=51abfce5&api_secret=ayQ22qeqMt6EHlFy",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "GET",
      CURLOPT_HTTPHEADER => array(
        "Accept: */*",
        "Accept-Encoding: gzip, deflate",
        "Cache-Control: no-cache",
        "Connection: keep-alive",
        "Cookie: __cfduid=da4295a5cac727da4f3e4e826d5187bf01587694784",
        "Host: rest.nexmo.com",
        "Postman-Token: b84de688-6db3-4489-ba7c-7b2e79771bbc,44c08096-fa5a-446c-89a7-988659ded1bd",
        "User-Agent: PostmanRuntime/7.17.1",
        "cache-control: no-cache"
      ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
      return "cURL Error #:" . $err;
    } else {
      return json_decode($response,true);
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