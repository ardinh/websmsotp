<?php

require_once('helper/database.php');
// require_once('helper/reply.php');
// print_r($input);
// die();

$input = file_get_contents('php://input');
$arr = explode("&", $input);
$message   = explode("=", $arr[0])[1];
$app_name   = explode("=", $arr[1])[1];
$sender   = explode("=", $arr[2])[1];
$sender = str_replace("-", "", $sender);
$sender = str_replace("+", "", $sender);
$msisdn = get_string_between($sender.';',"B",";");

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($mysqli->connect_errno) {
    $reply = replyErrorDatabaseConnect();
    goto finish;
}

// $msisdn = $_GET['msisdn'];
// print_r(json_encode($balance));
// die();

$msisdn2 = get_string_between($message.';','otp', ';');
$fc = substr($msisdn2, 0,1);
$sc = substr($msisdn2, 0,2);
$ssc = substr($msisdn2, 0,4);

if($fc == "+" && $ssc = "+628"){
    $msisdn2 = substr_replace($msisdn2, "", 0,1);   
}else if($fc == 0 && $sc == "08"){
    $msisdn2 = substr_replace($msisdn2, "62", 0,1);
}else if($fc == 0 && $sc == "01"){
    $msisdn2 = substr_replace($msisdn2, "60", 0,1);
}

if($msisdn2 == $msisdn){
    $query = "SELECT content FROM sms_queue WHERE msisdn = '".$msisdn."' and DATE_ADD(insert_time,INTERVAL 2 MINUTE) > now() ORDER BY id desc";
}else{
    $query = "SELECT content FROM sms_queue WHERE msisdn = '".$msisdn2."' and DATE_ADD(insert_time,INTERVAL 2 MINUTE) > now() ORDER BY id desc";
}

// $query = "SELECT content FROM sms_queue WHERE msisdn = '".$msisdn."' and DATE_ADD(insert_time,INTERVAL 2 MINUTE) > now() ORDER BY id desc";
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
$data = $rows;

file_put_contents("file.txt", $data);
file_put_contents("file1.txt", $sender);
if($data[0][0] == null){
	$reply = array('reply' => "Pastikan nomor telah di input ke app Mydrakor, dan juga nomor Whatsapp sama dengan nomor yang di inputkan");	
}else{
    preg_match_all('!\d+!', $data[0][0], $matches);

    $num = $matches[0][0];
	$reply = array('reply' => "Kode Otp Mydrakor Anda : ".$num."\nKode ini berlaku selama 1 menit");
}
echo json_encode($reply);
/*for ($i = 0; $i < count($rows); $i++) {
    $row = $rows[$i];
    $ids[] = $row[0];
}*/

// print_r($ids);
die();
finish:
if (isset($stmt) && $stmt !== false) {
    $stmt->close();
}

if (isset($stmt) && $mysqli !== false) {
    $mysqli->close();
} 


function get_string_between($string, $start, $end){
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}