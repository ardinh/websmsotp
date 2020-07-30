<?php

$postdata =array("c" => 3,"i"=> "6285950333217","s" => "Hai ! Terima kasih sudah menggunakan MyDrakor. Jangan lewatkan Movie/TV updatenya. Selamat menikmati!","t" => "765RT-32H23-JK255-176SW");
$h = json_encode($postdata);
// print_r($h);
// die();
$opts = array('http' =>
    array(
        'method'  => 'POST',
        'header'  =>  "Content-Type: application/json".
                      "Accept: */*".
                      "Accept-Encoding: gzip, deflate".
                      "Cache-Control: no-cache".
                      "Connection: keep-alive".
                      "cache-control: no-cache",
        'content' => $h
    )
);

$context  = stream_context_create($opts);

$result = file_get_contents('http://dev.motion.co.id/smsbot/smsqueue.php', false, $context);

echo $result;
var_dump($http_response_header);