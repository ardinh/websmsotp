<?php

require_once('helper/database.php');

// $input = $_GET['d'];
// print_r($input);
// die();

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($mysqli->connect_errno) {
    $reply = replyErrorDatabaseConnect();
    goto finish;
}

$table = "sms_dvc";
$query = "SELECT devid FROM `$table` where devid != 10001";
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

if (count($rows) == 0) {
    $reply = replyOkSms(array());
    goto finish;
}
if (count($rows) == 0) {
    $reply = replyOkSms(array());
    goto finish;
}

for ($i = 0; $i < count($rows); $i++) {
    $row = $rows[$i];
    $ids[] = $row[0];
    
}
callCheckKuota($ids);
die();
finish:
if (isset($stmt) && $stmt !== false) {
    $stmt->close();
}

if (isset($stmt) && $mysqli !== false) {
    $mysqli->close();
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