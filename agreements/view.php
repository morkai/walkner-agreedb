<?php

// Copyright (c) 2014, Łukasz Walukiewicz <lukasz@walukiewicz.eu>. Some Rights Reserved.
// Licensed under CC BY-NC-SA 4.0 <http://creativecommons.org/licenses/by-nc-sa/4.0/>.
// Part of the walkner-agreedb project <http://lukasz.walukiewicz.eu/p/walkner-agreedb>

include __DIR__ . '/__common__.php';

bad_request_if(empty($_GET['id']));

$q = <<<SQL
SELECT *
FROM agreements
WHERE id=:id
LIMIT 1
SQL;

$agreement = fetch_one($q, array(':id' => $_GET['id']));

not_found_if(empty($agreement));

$agreement->date = date('Y-m-d', $agreement->date);
$agreement->alarmDate = $agreement->alarmDate ? date('Y-m-d', $agreement->alarmDate) : '-';

?>

<? decorate('Umowa') ?>

<div class="page-header">
  <ul class="page-actions">
    <li><a class="btn" href="<?= url_for("agreements/download.php?id={$agreement->id}") ?>"><i class="icon-download-alt"></i> Pobierz plik umowy</a>
    <? if (user_can_manage()): ?>
    <li><a class="btn" href="<?= url_for("agreements/edit.php?id={$agreement->id}") ?>"><i class="icon-pencil"></i> Edytuj</a>
    <li><a class="btn btn-danger" href="<?= url_for("agreements/delete.php?id={$agreement->id}") ?>"><i class="icon-remove icon-white"></i> Usuń</a>
    <? endif ?>
  </ul>
  <h1>
    <a href="<?= url_for("agreements") ?>">Umowy</a> \
    <?= e($agreement->id) ?>
  </h1>
</div>

<dl class="well properties">
  <dt>ID:
  <dd><?= e($agreement->id) ?>
  <dt>Nazwa firmy:
  <dd><?= e($agreement->company) ?>
  <dt>Adres firmy:
  <dd><?= nl2br(e($agreement->address)) ?>
  <dt>KRS:
  <dd><?= e($agreement->krs) ?>
  <dt>NIP:
  <dd><?= e($agreement->nip) ?>
  <dt>Data umowy:
  <dd><?= $agreement->date ?>
  <dt>Przedmiot umowy:
  <dd><?= e($agreement->subject) ?>
  <dt>Data alarmu:
  <dd><?= $agreement->alarmDate ?>
  <dt>Przedmiot alarmu:
  <dd><?= dash_if_empty($agreement->alarmText) ?>
</dl>

<? begin_slot('js') ?>
<script>
  $(function()
  {
    $('.btn-danger').on('click', function()
    {
      return confirm('Czy na pewno chcesz bezpowrotnie usunąć wybraną umowę?');
    });
  });
</script>
<? append_slot() ?>
