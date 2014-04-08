<?php

// Copyright (c) 2014, Łukasz Walukiewicz <lukasz@walukiewicz.eu>. Some Rights Reserved.
// Licensed under CC BY-NC-SA 4.0 <http://creativecommons.org/licenses/by-nc-sa/4.0/>.
// Part of the walkner-agreedb project <http://lukasz.walukiewicz.eu/p/walkner-agreedb>

include __DIR__ . '/__common__.php';

if (!empty($_POST['agreement']))
{
  $data = agreements_prepare_data($_POST['agreement']);

  if (agreements_validate_data($data))
  {
    agreements_upload_file($data);

    $db = get_conn();
    $users = $data['users'];

    unset($data['users']);

    try
    {
      $db->beginTransaction();

      exec_insert('agreements', $data);

      $agreementId = $db->lastInsertId();

      if (!empty($users))
      {
        $stmt = prepare_stmt('INSERT INTO agreements_users SET agreement=:agreement, user=:user');

        foreach ($users as $user)
        {
          exec_stmt($stmt, array(
            ':agreement' => $agreementId,
            ':user' => $user->id
          ));
        }
      }

      $db->commit();

      set_flash('Nowa umowa została dodana pomyślnie!');
      go_to('/agreements/add.php');
    }
    catch (Exception $x)
    {
      $db->rollBack();

      set_flash($x->getMessage(), 'error');

      $data['users'] = $users;
    }
  }
}
else
{
  $data = array();
}

$mode = 'add';
$agreement = (object)array_merge(array(
  'company' => '',
  'address' => '',
  'krs' => '',
  'nip' => '',
  'regon' => '',
  'date' => time(),
  'subject' => '',
  'uploaded' => 0,
  'filename' => '',
  'filepath' => '',
  'restricted' => 0,
  'users' => array()
), $data);

?>

<? decorate('Dodawanie umowy') ?>

<div class="page-header">
  <h1>
    <a href="<?= url_for("agreements") ?>">Umowy</a> \
    Dodawanie
  </h1>
</div>

<form action="<?= url_for("agreements/add.php") ?>" method=post autocomplete=off enctype="multipart/form-data">
  <? include __DIR__ . '/__form__.php' ?>
</form>
