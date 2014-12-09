<?php

// Copyright (c) 2014, Łukasz Walukiewicz <lukasz@walukiewicz.eu>. Some Rights Reserved.
// Licensed under CC BY-NC-SA 4.0 <http://creativecommons.org/licenses/by-nc-sa/4.0/>.
// Part of the walkner-agreedb project <http://lukasz.walukiewicz.eu/p/walkner-agreedb>

include __DIR__ . '/__common__.php';

bad_request_if(empty($_GET['id']));

$q = <<<SQL
SELECT uploaded, filepath, filename, restricted
FROM agreements
WHERE id=:id
LIMIT 1
SQL;

$agreement = fetch_one($q, array(':id' => $_GET['id']));

not_found_if(empty($agreement));

agreements_check_access($agreement);

$filepath = ($agreement->uploaded ? APP_UPLOADS_PATH : '') . $agreement->filepath;

if (!file_exists($filepath))
{
  header('HTTP/1.1 404 Not Found');

  decorate('Plik nie istnieje');

  echo render_message(
    'Niestety, ale nie wybrany plik umowy nie istnieje lub nie udało się go odczytać.',
    'error',
    'Plik nie istnieje',
    false
  );

  exit;
}

$ext = explode('.', $agreement->filename);
$ext = $ext[count($ext) - 1];

$mimeTypes = array(
  'pdf' => 'application/pdf',
  'txt' => 'text/plain',
  'html' => 'text/html',
  'exe' => 'application/octet-stream',
  'zip' => 'application/zip',
  'doc' => 'application/msword',
  'xls' => 'application/vnd.ms-excel',
  'ppt' => 'application/vnd.ms-powerpoint',
  'gif' => 'image/gif',
  'png' => 'image/png',
  'jpeg' => 'image/jpg',
  'jpg' => 'image/jpg',
  'php' => 'text/plain'
);

if (empty($mimeTypes[$ext]))
{
  $ext = 'exe';
}

$mimeType = $mimeTypes[$ext];

header('Content-Type: ' . $mimeType);
header('Content-Disposition: attachment; filename="' . $agreement->filename . '"');
header('Content-Transfer-Encoding: binary');
header('Accept-Ranges: bytes');
header('Cache-Control: private');
header('Pragma: private');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');

readfile($filepath);
