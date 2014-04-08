<?php

// Copyright (c) 2014, Łukasz Walukiewicz <lukasz@walukiewicz.eu>. Some Rights Reserved.
// Licensed under CC BY-NC-SA 4.0 <http://creativecommons.org/licenses/by-nc-sa/4.0/>.
// Part of the walkner-agreedb project <http://lukasz.walukiewicz.eu/p/walkner-agreedb>

include __DIR__ . '/__common__.php';

no_access_if_cant_manage();

bad_request_if(empty($_GET['id']));

$user = fetch_one('SELECT * FROM users WHERE id=?', array(1 => $_GET['id']));

not_found_if(empty($user));

unset($user->password);

escape($user);

?>

<? decorate('Użytkownik') ?>

<div class="page-header">
  <ul class="page-actions">
    <li><a class="btn" href="<?= url_for("users/edit.php?id={$user->id}") ?>"><i class="icon-pencil"></i> Edytuj</a>
    <li><a class="btn btn-danger" href="<?= url_for("users/delete.php?id={$user->id}") ?>"><i class="icon-remove icon-white"></i> Usuń</a>
  </ul>
  <h1>
    <a href="<?= url_for("users") ?>">Użytkownicy</a> \
    <?= $user->name ?>
  </h1>
</div>

<div class="well">
  <dl class="properties">
    <dt>Imię i nazwisko:
    <dd><?= $user->name ?>
    <dt>Login:
    <dd><?= $user->login ?>
    <dt>Zarządzanie umowami:
    <dd><?= $user->manage ? 'Tak' : 'Nie' ?>
  </dl>
</div>
