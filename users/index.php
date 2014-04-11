<?php

// Copyright (c) 2014, Łukasz Walukiewicz <lukasz@walukiewicz.eu>. Some Rights Reserved.
// Licensed under CC BY-NC-SA 4.0 <http://creativecommons.org/licenses/by-nc-sa/4.0/>.
// Part of the walkner-agreedb project <http://lukasz.walukiewicz.eu/p/walkner-agreedb>

include __DIR__ . '/__common__.php';

no_access_if_cant_manage();

$page = empty($_GET['page']) ? 1 : (int)$_GET['page'];
$perPage = empty($_GET['perPage']) ? 20 : (int)$_GET['perPage'];

$pagedUsers = new PagedData($page, $perPage);

$query = get_search_query();
$where = '';

if (!empty($query))
{
  $where = "WHERE name LIKE '%{$query}%' OR login LIKE '%{$query}%'";
}

$totalItems = fetch_one("SELECT COUNT(*) AS total FROM users {$where}")->total;

$users = fetch_all(sprintf(
  "SELECT id, name, login, manage FROM users %s ORDER BY name LIMIT %d,%d",
  $where,
  $pagedUsers->getOffset(),
  $perPage
));
$users = array_map(function($user)
{
  escape($user);

  $user->name = dash_if_empty($user->name);
  $user->manage = $user->manage == 1;

  return $user;
}, $users);

if (is_ajax())
{
  output_json($users);
}

$pagedUsers->fill($totalItems, $users);

?>

<? decorate('Użytkownicy') ?>

<div class="page-header">
  <ul class="page-actions">
    <li>
      <form class="form-search" action="<?= url_for("users") ?>">
        <div class="input-append">
          <input class="span3" name="query" type="search" value="<?= $query ?>" results=5 autofocus><input class="btn" type="submit" value="Szukaj">
        </div>
      </form>
    <li><a class="btn" href="<?= url_for('/users/add.php') ?>"><i class="icon-plus"></i> Dodaj nowego użytkownika</a>
  </ul>
  <h1>Użytkownicy</h1>
</div>

<table class="table table-bordered table-condensed table-striped">
  <thead>
    <tr>
      <th>Imię i nazwisko
      <th>Login
      <th>Zarządzanie
      <th class="actions">Akcje
    </tr>
  </thead>
  <tbody>
    <? foreach ($pagedUsers AS $user): ?>
    <tr>
      <td><?= $user->name ?>
      <td><?= $user->login ?>
      <td><?= $user->manage ? 'Tak' : 'Nie' ?>
      <td class="actions">
        <a class="btn" title="Wyświetl szczegóły użytkownika" href="<?= url_for("users/view.php?id={$user->id}") ?>"><i class="icon-list-alt"></i></a>
        <a class="btn" title="Edytuj użytkownika" href="<?= url_for("users/edit.php?id={$user->id}") ?>"><i class="icon-pencil"></i></a>
        <a class="btn btn-danger" title="Usuń użytkownika" href="<?= url_for("users/delete.php?id={$user->id}") ?>"><i class="icon-remove icon-white"></i></a>
    </tr>
    <? endforeach ?>
  </tbody>
</table>

<?= $pagedUsers->render(url_for("users/?perPage={$perPage}&amp;query={$query}")) ?>

<? begin_slot('js') ?>
<script>
$(function()
{
  $('tbody').on('click', '.btn-danger', function()
  {
    return confirm('Czy na pewno chcesz bezpowrotnie usunąć wybranego użytkownika?');
  });
});
</script>
<? append_slot() ?>
