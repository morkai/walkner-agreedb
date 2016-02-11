<?php

// Copyright (c) 2014, Łukasz Walukiewicz <lukasz@walukiewicz.eu>. Some Rights Reserved.
// Licensed under CC BY-NC-SA 4.0 <http://creativecommons.org/licenses/by-nc-sa/4.0/>.
// Part of the walkner-agreedb project <http://lukasz.walukiewicz.eu/p/walkner-agreedb>

include_once __DIR__ . '/../__common__.php';

function agreements_prepare_data($data)
{
  $data['date'] = empty($data['date']) ? time() : strtotime($data['date']);
  $data['upload'] = empty($_FILES['upload']) || $_FILES['upload']['error'] !== UPLOAD_ERR_OK ? null : $_FILES['upload'];
  $data['uploaded'] = empty($data['filepath']) && $data['upload'] !== null ? 1 : 0;
  $data['alarmDate'] = empty($data['alarmDate']) ? 0 : strtotime($data['alarmDate']);

  if ($data['uploaded'])
  {
    $data['filepath'] = md5($data['upload']['tmp_name']);
  }

  if (empty($data['filepath']))
  {
    unset($data['filepath']);
    unset($data['uploaded']);
  }

  if (empty($data['filename']))
  {
    if (!empty($data['uploaded']))
    {
      $data['filename'] = $data['upload']['name'];
    }
    else if (!empty($data['filepath']))
    {
      $data['filename'] = basename($data['filepath']);
    }
  }

  $data['users'] = empty($data['users']) ? array() : explode(',', $data['users']);

  array_walk($data['users'], 'intval');

  if (!empty($data['users']))
  {
    $data['users'] = fetch_all('SELECT id, name FROM users WHERE id IN(' . implode(', ', $data['users']) . ')');
  }

  $data['restricted'] = empty($data['users']) ? 0 : 1;

  return $data;
}

function agreements_validate_data($data)
{
  return !empty($data['company'])
    && !empty($data['date'])
    && !empty($data['subject'])
    && !empty($data['filename']);
}

function agreements_upload_file(&$data, $agreement = null)
{
  $upload = $data['upload'];

  unset($data['upload']);

  if ($upload === null)
  {
    return;
  }

  move_uploaded_file($upload['tmp_name'], APP_UPLOADS_PATH . $data['filepath']);
  @unlink($upload['tmp_name']);

  if ($agreement !== null && $agreement->uploaded)
  {
    @unlink(APP_UPLOADS_PATH . $agreement->filepath);
  }
}

function agreements_check_access($agreement)
{
  if (!$agreement->restricted)
  {
    return;
  }

  $user = user_get_data();

  no_access_if_not($user);

  if ($user->manage)
  {
    return;
  }

   $q = <<<SQL
SELECT 1
FROM agreements_users
WHERE agreement=:agreement AND user=:user
LIMIT 1
SQL;

  no_access_if_not(fetch_one($q, array(':agreement' => $agreement->id, ':user' => $user->id)));
}
