<?php
defined('DIRECT_ACCESS') OR exit();

define('OK_COMMON', 1);

define('ERR_COMMON', -1);
define('ERR_METHOD', -1000001);
define('ERR_FORMAT', -1000002);
define('ERR_FORMAT_JSON', -1000003);
define('ERR_DB_CONNECT', -2000001);
define('ERR_DB_PREPARE', -2000002);
define('ERR_DB_BIND', -2000003);
define('ERR_DB_EXECUTE', -2000004);
define('ERR_DB_RESULT', -2000005);

function replyMessage($code, $msg) {
    return array(
        PAYLOAD_CODE => $code,
        PAYLOAD_MESSAGE => $msg,
    );
}

function replyOkCommon() {
    return replyMessage(OK_COMMON, "OK");
}

function replyOkSms($list) {
    return array(
        PAYLOAD_CODE => OK_COMMON,
        PAYLOAD_SMS_OUT => $list,
    );
}

function replyErrorCommon() {
    return replyMessage(ERR_COMMON, "Unknown Error");
}

function replyErrorMethod() {
    return replyMessage(ERR_METHOD, "Wrong method!");
}

function replyErrorFormat() {
    return replyMessage(ERR_FORMAT, "Wrong format!");
}

function replyErrorFormatJson() {
    return replyMessage(ERR_FORMAT_JSON, "Wrong json!");
}

function replyErrorDatabaseConnect() {
    return replyMessage(ERR_DB_CONNECT, "Cannot connect db!");
}

function replyErrorDatabasePrepare() {
    return replyMessage(ERR_DB_PREPARE, 'Cannot prepare query!');
}

function replyErrorDatabaseBind() {
    return replyMessage(ERR_DB_BIND, 'Cannot bind param!');
}

function replyErrorDatabaseExecute() {
    return replyMessage(ERR_DB_BIND, 'Cannot execute statement!');
}

function replyErrorDatabaseResult() {
    return replyMessage(ERR_DB_RESULT, 'Cannot get result of the query!');
}
