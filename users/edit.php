<?php

// Copyright (c) 2014, Łukasz Walukiewicz <lukasz@walukiewicz.eu>. Some Rights Reserved.
// Licensed under CC BY-NC-SA 4.0 <http://creativecommons.org/licenses/by-nc-sa/4.0/>.
// Part of the walkner-agreedb project <http://lukasz.walukiewicz.eu/p/walkner-agreedb>

include __DIR__ . '/__common__.php';

no_access_if_cant_manage();

bad_request_if(empty($_GET['id']));

$user = fetch_one('SELECT * FROM users WHERE id=?', array(1 => $_GET['id']));

not_found_if(empty($user));

if (!empty($_POST['user']))
{
  $data = users_prepare_data($_POST['user']);

  exec_update('users', $data, "id={$user->id}");

  set_flash('Użytkownik został zmodyfikowany pomyślnie!');
  go_to(get_referer("/users/view.php?id={$user->id}"));
}

escape($user);

?>

<? decorate('Edycja użytkownika') ?>

<div class="page-header">
  <h1>
    <a href="<?= url_for("users") ?>">Użytkownicy</a> \
    <a href="<?= url_for("users/view.php?id={$user->id}") ?>"><?= $user->name ?></a> \
    Edycja
  </h1>
</div>

<form action="<?= url_for("users/edit.php?id={$user->id}") ?>" method=post autocomplete=off>
  <? include __DIR__ . '/__form__.php' ?>
</form>
