<?php

// Copyright (c) 2014, Łukasz Walukiewicz <lukasz@walukiewicz.eu>. Some Rights Reserved.
// Licensed under CC BY-NC-SA 4.0 <http://creativecommons.org/licenses/by-nc-sa/4.0/>.
// Part of the walkner-agreedb project <http://lukasz.walukiewicz.eu/p/walkner-agreedb>

include __DIR__ . '/__common__.php';

no_access_if_cant_manage();

bad_request_if(empty($_GET['id']));

$q = <<<SQL
SELECT *
FROM agreements
WHERE id=:id
LIMIT 1
SQL;

$agreement = fetch_one($q, array(':id' => $_GET['id']));

not_found_if(empty($agreement));

$referer = get_referer("/agreements/view.php?id={$agreement->id}");

if (!empty($_POST['agreement']))
{
  $data = agreements_prepare_data($_POST['agreement']);

  if (agreements_validate_data($data))
  {
    agreements_upload_file($data, $agreement);

    $db = get_conn();
    $users = $data['users'];

    unset($data['users']);

    try
    {
      $db->beginTransaction();

      exec_update('agreements', $data, "id={$agreement->id}");

      exec_stmt("DELETE FROM agreements_users WHERE agreement=:agreement", array(':agreement' => $agreement->id));

      if (!empty($users))
      {
        $stmt = prepare_stmt('INSERT INTO agreements_users SET agreement=:agreement, user=:user');

        foreach ($users as $user)
        {
          exec_stmt($stmt, array(
            ':agreement' => $agreement->id,
            ':user' => $user->id
          ));
        }
      }

      $db->commit();

      set_flash('Umowa została zmodyfikowana pomyślnie!');
      go_to($referer);
    }
    catch (Exception $x)
    {
      $db->rollBack();

      set_flash($x->getMessage(), 'error');

      $data['users'] = $users;
    }
  }

  $agreement = (object)array_merge((array)$agreement, $data);
}

$mode = 'edit';

escape($agreement);

if (empty($agreement->users))
{
  $q = <<<SQL
SELECT u.id, u.name
FROM users u
WHERE u.id IN(SELECT au.user FROM agreements_users au WHERE au.agreement=:agreement)
ORDER BY u.name ASC
SQL;

  $agreement->users = fetch_all($q, array(
    ':agreement' => $agreement->id
  ));
}

?>

<? decorate('Edycja zadania') ?>

<div class="page-header">
  <h1>
    <a href="<?= url_for("agreements") ?>">Umowy</a> \
    <a href="<?= url_for("agreements/view.php?id={$agreement->id}") ?>"><?= $agreement->id ?></a> \
    Edycja
  </h1>
</div>

<form action="<?= url_for("agreements/edit.php?id={$agreement->id}") ?>" method=post autocomplete=off enctype="multipart/form-data">
  <input type="hidden" name="referer" value="<?= e($referer) ?>">
  <? include __DIR__ . '/__form__.php' ?>
</form>
