<?php
defined('DIRECT_ACCESS') OR exit();

define('API_SMS_IN', 1);
define('API_SMS_OUT', 2);
define('API_SMS_QUEUE', 3);
define('API_SMS_QUEUE_STAT', 4);
define('API_SMS_LOG', 5);
define('API_SMS_KUOTA', 6);
define('API_SMS_KUOTA_OUT', 7);
define('API_SMS_KUOTA_STAT', 8);
define('API_SMS_KUOTA_CEK', 9);
define('API_SMS_DVC_UPDATE', 10);
define('API_ADD_SET', 11);

function isMethodPost() {
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}
