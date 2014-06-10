<?php

// Copyright (c) 2014, Łukasz Walukiewicz <lukasz@walukiewicz.eu>. Some Rights Reserved.
// Licensed under CC BY-NC-SA 4.0 <http://creativecommons.org/licenses/by-nc-sa/4.0/>.
// Part of the walkner-agreedb project <http://lukasz.walukiewicz.eu/p/walkner-agreedb>

include_once __DIR__ . '/../__common__.php';

function users_prepare_data($data)
{
  $data['manage'] = empty($data['manage']) ? 0 : (int)$data['manage'];

  if (!empty($data['password']) && !empty($data['password2']) && $data['password'] === $data['password2'])
  {
    $data['password'] = hash('sha256', $data['password']);
  }
  else
  {
    unset($data['password']);
  }

  unset($data['password2']);

  return $data;
}
