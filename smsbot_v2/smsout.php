<?php

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
    !(array_key_exists(PAYLOAD_CODE, $json) && $json[PAYLOAD_CODE] == API_SMS_OUT) ||
    !array_key_exists(PAYLOAD_DEVICE_ID, $json)
) {
    $reply = replyErrorFormatJson();
    goto finish;
}

$devid = $json[PAYLOAD_DEVICE_ID];
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($mysqli->connect_errno) {
    $reply = replyErrorDatabaseConnect();
    goto finish;
}

$query = "SELECT operator FROM `sms_dvc_3` WHERE devid=$devid";
// echo $query;
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
$no = array();
$row = array();
$rows = $result->fetch_all(MYSQLI_NUM);
$q = "";
if($rows[0][0] != ""){
    $row = explode(",", $rows[0][0]);

    for ($i=0; $i < count($row); $i++) { 
        $query = "SELECT nomor FROM `sms_operator` WHERE id_operator=$row[$i]";

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
        $no = array_merge($no,explode(",",$rows[0][0]));
    }
    $noo = join(',',$no);
    $q = " and SUBSTRING(msisdn, 3, 3) in ($noo)";
}


$table = TABLE_SMS_QUEUE;
$status = STATUS_BEGIN;
$query = "SELECT id, msisdn, content FROM `sms_queue` WHERE status=$status AND ISNULL(devid)$q and DATE_ADD(insert_time,INTERVAL 2 MINUTE) > now() ORDER BY id ASC LIMIT 10";

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

$result = $stmt->get_result();
if ($result === false) {
    $reply = replyErrorDatabaseResult();
    goto finish;
}

$ids = array();
$rows = $result->fetch_all(MYSQLI_NUM);

if (count($rows) == 0) {
    $reply = replyOkSms(array());
    goto finish;
}


for ($i = 0; $i < count($rows); $i++) {
    $row = $rows[$i];
    if ($devid == 3 || $devid == 4 || $devid == 7 || $devid == 8 || $devid == 9 || $devid == 10 || $devid == 11 || $devid == 5 || $devid == 6) {
        $content = $row[2];

        preg_match_all('!\d+!', $content, $matches);

        // $a = numberTowords($matches[0][0]);
        $a = "( ".$matches[0][0]." )";
        $a = str_replace($matches[0][0], $a, $content);
        $b = date("ymdhim");
        $a = $a." ".$b;
        // print_r($a);
        // die();
        $rows[$i][2] = $a;
        // $row = $row;
    }
    $ids[] = $row[0];
}

$table = TABLE_SMS_QUEUE;
$devid = $json[PAYLOAD_DEVICE_ID];
$status = STATUS_FETCHED;
$ids2 = join(',', $ids);
$query = "UPDATE `$table` SET devid=$devid, status=$status WHERE id IN ($ids2)";
$stmt = $mysqli->prepare($query);
if ($stmt === false) {
    $reply = replyErrorDatabasePrepare();
    goto finish;
}

if ($stmt->execute() === FALSE) {
    $reply = replyErrorDatabaseExecute();
    goto finish;
}

$reply = replyOkSms($rows);

finish:
if (isset($stmt) && $stmt !== false) {
    $stmt->close();
}

if (isset($stmt) && $mysqli !== false) {
    $mysqli->close();
}

$response = json_encode($reply);
die($response);


function numberTowords($num)
{ 
    $ones = array( 
        1 => "SATU", 
        2 => "DUA", 
        3 => "TIGA", 
        4 => "EMPAT", 
        5 => "LIMA", 
        6 => "ENAM", 
        7 => "TUJUH", 
        8 => "DELAPAN", 
        9 => "SEMBILAN", 
    ); //limit t quadrillion 
    $num = number_format($num,2,".",","); 
    $num_arr = explode(".",$num); 
    $wholenum = $num_arr[0]; 
    $decnum = $num_arr[1]; 
    $whole_arr = array_reverse(explode(",",$wholenum)); 
    krsort($whole_arr); 
    $rettxt = ""; 
    foreach($whole_arr as $key => $i){ 
        if($i < 20){ 
            $rettxt .= $ones[$i]." "; 
        }elseif($i < 100){ 
            $rettxt .= $tens[substr($i,0,1)]; 
            $rettxt .= " ".$ones[substr($i,1,1)]; 
        }else{ 
            $rettxt .= $ones[substr($i,0,1)]." "; 
            $rettxt .= " ".$ones[substr($i,1,1)]; 
            $rettxt .= " ".$ones[substr($i,2,1)]; 
        } 
        if($key > 0){ 
            // $rettxt .= " ".$hundreds[$key]." "; 
        } 
    } 
    if($decnum > 0){ 
        $rettxt .= " and "; 
        if($decnum < 20){ 
            $rettxt .= $ones[$decnum]; 
        }elseif($decnum < 100){ 
            $rettxt .= $tens[substr($decnum,0,1)]; 
            $rettxt .= " ".$ones[substr($decnum,1,1)]; 
        } 
    } 
    return $rettxt; 
}