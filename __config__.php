<?php

define('APP_PDO_DSN', 'mysql:host=127.0.0.1;dbname=walkner_agreedb');
define('APP_PDO_USER', 'root');
define('APP_PDO_PASS', '');

define('APP_DOMAIN', 'localhost:8888');
define('APP_BASE_URL', '/walkner/walkner-agreedb/');

define('APP_UPLOADS_PATH', __DIR__ . '/__files__/');

error_reporting(E_ALL);
date_default_timezone_set('Europe/Warsaw');
ini_set('default_charset', 'utf-8');
