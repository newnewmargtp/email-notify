<?php
set_time_limit(60 * 60 * 24);
ini_set("memory_limit", "1024M");

const FETCH_PID_FILE = '/var/run/emails-notify.pid';
const VALIDATE_PID_FILE = '/var/run/emails-validate.pid';
const LOG_FILE = '/var/log/emails-notify.log';

const THREADS_NUMBER = 25;
const LETTER_TEMPLATE = '$USER, your subscription is expiring soon';
const TEMPLATE_VARS_USER = '$USER';

const DB_HOST = 'localhost';
const DB_DB = 'productionDB';
const DB_USER = 'xx';
const DB_PASS = 'xx';

const DB_BATCH_NUMBER = 2500;

const EMAIL_FROM = 'no-reply@somedomain.com';
const EMAIL_TEMPLATE = "<!doctype html>
<html>
<head>
    <title>Something important</title>
    <meta name=\"description\" content=\"some SEO stuff\">
    <meta name=\"keywords\" content=\"some key words\">
</head>
<body>
{text}
</body>
</html>";

const EMAIL_VARS_TEXT = '{text}';
