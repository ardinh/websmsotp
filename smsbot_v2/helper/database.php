<?php

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'dev_smsbot');

define('TABLE_SMS_QUEUE', 'sms_queue');
define('TABLE_SMS_IN', 'sms_in');
define('TABLE_SMS_IN_KUOTA', 'sms_in_kuota');
define('TABLE_SMS_KUOTA', 'sms_kuota');

define('STATUS_BEGIN', 1);
define('STATUS_FETCHED', 2);
define('STATUS_SENT', 3);
