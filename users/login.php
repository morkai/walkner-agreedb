<?php

// Copyright (c) 2014, Łukasz Walukiewicz <lukasz@walukiewicz.eu>. Some Rights Reserved.
// Licensed under CC BY-NC-SA 4.0 <http://creativecommons.org/licenses/by-nc-sa/4.0/>.
// Part of the walkner-agreedb project <http://lukasz.walukiewicz.eu/p/walkner-agreedb>

include __DIR__ . '/__common__.php';

bad_request_if(empty($_POST['login']) || empty($_POST['password']));

$q = <<<SQL
SELECT id, name, login, manage
FROM users
WHERE login=:login AND password=:password
LIMIT 1
SQL;

$user = fetch_one($q, array(
  ':login' => $_POST['login'],
  ':password' => hash('sha256', $_POST['password'])
));

if (empty($user))
{
  set_flash('Nieprawidłowy login i/lub hasło!', 'error');
  go_to('/');
}

$_SESSION['user'] = $user;

go_to('/agreements');
