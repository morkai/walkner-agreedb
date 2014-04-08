<?php

// Copyright (c) 2014, Łukasz Walukiewicz <lukasz@walukiewicz.eu>. Some Rights Reserved.
// Licensed under CC BY-NC-SA 4.0 <http://creativecommons.org/licenses/by-nc-sa/4.0/>.
// Part of the walkner-agreedb project <http://lukasz.walukiewicz.eu/p/walkner-agreedb>

include __DIR__ . '/__common__.php';

no_access_if_cant_manage();

if (!empty($_POST['user']))
{
  $data = users_prepare_data($_POST['user']);

  exec_insert('users', $data);

  $lastId = get_conn()->lastInsertId();

  set_flash('Nowy użytkownik został dodany pomyślnie!');
  go_to('/users/add.php');
}

$user = (object)array(
  'name' => '',
  'login' => '',
  'password' => '',
  'password2' => '',
  'manage' => false
);

?>

<? decorate('Dodawanie użytkownika') ?>

<div class="page-header">
  <h1>
    <a href="<?= url_for("users") ?>">Użytkownicy</a> \
    Dodawanie
  </h1>
</div>

<form action="<?= url_for("users/add.php") ?>" method=post autocomplete=off>
  <? include __DIR__ . '/__form__.php' ?>
</form>
