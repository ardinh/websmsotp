<?php
$input = file_get_contents('php://input');
$arr = explode("&", $input);
$message   = explode("=", $arr[0])[1];
$app_name   = explode("=", $arr[1])[1];
$sender   = explode("=", $arr[2])[1];
$sender = str_replace("-", "", $sender);
$sender = str_replace("+", "", $sender);
$sender = get_string_between($sender.';',"B",";");

$data = json_decode(getdata($sender));
file_put_contents("file.txt", $data);
file_put_contents("file1.txt", $sender);
if($data[0][0] == null){
	$reply = array('reply' => "Pastikan nomor telah di input ke app Mydrakor, dan juga nomor Whatsapp sama dengan nomor yang di inputkan");	
}else{
	$reply = array('reply' => $data[0][0]);
}
echo json_encode($reply);

function get_string_between($string, $start, $end){
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}

function getdata($msisdn){
	$url = "https://otp.inflixer.com/getdata.php?msisdn=".$msisdn;
	$curl = curl_init();

	curl_setopt_array($curl, array(
	  CURLOPT_URL =>$url ,
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => "",
	  CURLOPT_MAXREDIRS => 10,
	  CURLOPT_TIMEOUT => 30,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => "GET",
	  CURLOPT_POSTFIELDS => "",
	  CURLOPT_HTTPHEADER => array(
	    "Accept: */*",
	    "Accept-Encoding: gzip, deflate",
	    "Cache-Control: no-cache",
	    "Connection: keep-alive",
	    "Cookie: __cfduid=d67196b3d77aa87e3a624ee4db4a84cf51594091724",
	    "Host: otp.inflixer.com",
	    "Postman-Token: b233e8dc-6eac-432e-b82e-ea12109f07b6,114d9fec-6d14-456b-bed7-18d79f22aadb",
	    "User-Agent: PostmanRuntime/7.17.1",
	    "cache-control: no-cache"
	  ),
	));

	$response = curl_exec($curl);
	$err = curl_error($curl);

	curl_close($curl);

	if ($err) {
	  return; $err;
	} else {
	  return $response;
	}
}
?>