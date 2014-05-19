<?php

// Copyright (c) 2014, Łukasz Walukiewicz <lukasz@walukiewicz.eu>. Some Rights Reserved.
// Licensed under CC BY-NC-SA 4.0 <http://creativecommons.org/licenses/by-nc-sa/4.0/>.
// Part of the walkner-agreedb project <http://lukasz.walukiewicz.eu/p/walkner-agreedb>

include __DIR__ . '/__common__.php';

$page = empty($_GET['page']) ? 1 : (int)$_GET['page'];
$perPage = empty($_GET['perPage']) ? 30 : (int)$_GET['perPage'];

$pagedAgreements = new PagedData($page, $perPage);

$where = '';
$conditions = array();
$filter = array(
  'id' => filter_numeric_param('id'),
  'company' => filter_string_param('company'),
  'address' => filter_string_param('address'),
  'krs' => filter_string_param('krs'),
  'nip' => filter_string_param('nip'),
  'date' => filter_date_param('date'),
  'alarmDate' => filter_date_param('date'),
  'subject' => filter_string_param('subject'),
  'owner' => filter_string_param('owner')
);

if (empty($filter['id']))
{
  if (!empty($filter['date']))
  {
    $conditions[] = 'a.date=' . strtotime($filter['date']);
  }

  apply_string_condition($conditions, $filter, 'company');
  apply_string_condition($conditions, $filter, 'address');
  apply_string_condition($conditions, $filter, 'subject');
  apply_string_condition($conditions, $filter, 'owner');
  apply_maxlen_condition($conditions, 10, $filter, 'krs');
  apply_maxlen_condition($conditions, 10, $filter, 'nip');
}
else
{
  $conditions[] = 'a.id = ' . $filter['id'];
}

$user = user_get_data();

if ($user === null)
{
  $conditions[] = 'a.restricted=0';
}
else if (!$user->manage)
{
  $conditions[] = <<<SQL
a.restricted=0
OR (a.restricted=1 AND {$user->id} IN(SELECT au.user FROM agreements_users au WHERE au.agreement=a.id))
SQL;
}

$canManage = user_can_manage();

if (!empty($conditions))
{
  $where = 'WHERE ' . implode(' AND ', $conditions);
}

$q = <<<SQL
SELECT COUNT(*) AS total
FROM agreements a
{$where}
SQL;

$totalItems = (int)fetch_one($q)->total;

$q = <<<SQL
SELECT a.*
FROM agreements a
{$where}
ORDER BY a.company ASC
LIMIT {$pagedAgreements->getOffset()}, {$perPage}
SQL;

$agreements = fetch_all($q);

if (is_ajax())
{
  output_json($agreements);
}

$today = strtotime(date('Y-m-d') . ' 00:00:00');

foreach ($agreements as $agreement)
{
  $agreement->className = '';
  $agreement->title = '';

  if ($agreement->alarmDate == 0)
  {
    continue;
  }
  else if ($agreement->alarmDate <= $today)
  {
    $agreement->className = 'error';
  }
  else if ($agreement->alarmDate <= $today + 24 * 7 * 3600)
  {
    $agreement->className = 'warning';
  }

  $agreement->title = e($agreement->alarmText) . ' (' . date('Y-m-d', $agreement->alarmDate) . ')';
}

$pagedAgreements->fill($totalItems, $agreements);

?>

<? decorate('Umowy') ?>

<div class="page-header">
  <? if ($canManage): ?>
  <ul class="page-actions">
    <li><a class="btn" href="<?= url_for('/agreements/add.php') ?>"><i class="icon-plus"></i> Dodaj nową umowę</a>
  </ul>
  <? endif ?>
  <h1>Umowy <small><?= $pagedAgreements->getOffset() + count($agreements) ?> z <?= $totalItems ?></small></h1>
</div>

<form id="agreementsFilter" action="<?= url_for("agreements/") ?>">
  <input type="hidden" name="perPage" value="<?= $perPage ?>">
  <table class="table table-bordered table-condensed">
    <thead>
      <tr>
        <th><label for="filter-company">Nazwa firmy (<kbd>ALT+1</kbd>)</label>
        <th><label for="filter-address">Adres/siedziba firmy (<kbd>2</kbd>)</label>
        <th class="min"><label for="filter-krs">KRS (<kbd>3</kbd>)</label>
        <th class="min"><label for="filter-nip">NIP (<kbd>4</kbd>)</label>
        <th class="min"><label for="filter-date">Data umowy (<kbd>5</kbd>)</label>
        <th><label for="filter-subject">Przedmiot umowy (<kbd>6</kbd>)</label>
        <th><label for="filter-owner">Właściciel (<kbd>7</kbd>)</label>
        <th class="actions">Akcje
      </tr>
    </thead>
    <thead>
      <tr class="filters">
        <td><input id=filter-company name=company type=text value="<?= e($filter['company']) ?>" maxlength="200" accesskey="1" autofocus>
        <td><input id=filter-address name=address type=text value="<?= e($filter['address']) ?>" maxlength="200" accesskey="2">
        <td><input id=filter-krs name=krs type=text value="<?= e($filter['krs']) ?>" maxlength=10 pattern="^[0-9]{3,10}$" accesskey="3">
        <td><input id=filter-nip name=nip type=text value="<?= e($filter['nip']) ?>" maxlength=10 pattern="^[0-9]{3,10}$" accesskey="4">
        <td><input id=filter-date name=date type=date value="<?= e($filter['date']) ?>" placeholder="YYYY-MM-DD" accesskey="5">
        <td><input id=filter-subject name=subject type=text value="<?= e($filter['subject']) ?>" maxlength="200" accesskey="6">
        <td><input id=filter-owner name=owner type=text value="<?= e($filter['owner']) ?>" maxlength="200" accesskey="7">
        <td class="actions">
          <button class="btn" type="submit" title="Filtruj umowy"><i class="icon-filter"></i></button>
          <a class="btn" href="<?= url_for("agreements") ?>" title="Wyczyść filtry"><i class="icon-remove"></i></a>
      </tr>
    </thead>
    <tbody>
      <? if ($totalItems === 0): ?>
      <tr>
        <td colspan=999>Nie znaleziono żadnych umów.</td>
      </tr>
      <? else: ?>
      <? foreach ($pagedAgreements AS $agreement): ?>
      <tr class="<?= $agreement->className ?>" title="<?= $agreement->title ?>">
        <td><?= dash_if_empty($agreement->company) ?>
        <td><?= nl2br(dash_if_empty($agreement->address)) ?>
        <td><?= dash_if_empty($agreement->krs) ?>
        <td><?= dash_if_empty($agreement->nip) ?>
        <td><?= date('Y-m-d', $agreement->date) ?>
        <td><?= dash_if_empty($agreement->subject) ?>
        <td><?= dash_if_empty($agreement->owner) ?>
        <td class="actions">
          <a class="btn btn-primary" title="Pobierz plik umowy" href="<?= url_for("agreements/download.php?id={$agreement->id}") ?>"><i class="icon-download-alt icon-white"></i></a>
          <a class="btn" title="Wyświetl szczegóły umowy" href="<?= url_for("agreements/view.php?id={$agreement->id}") ?>"><i class="icon-list-alt"></i></a>
          <? if ($canManage): ?>
          <a class="btn" title="Edytuj umowę" href="<?= url_for("agreements/edit.php?id={$agreement->id}") ?>"><i class="icon-pencil"></i></a>
          <a class="btn btn-danger" title="Usuń umowę" href="<?= url_for("agreements/delete.php?id={$agreement->id}") ?>"><i class="icon-remove icon-white"></i></a>
          <? endif ?>
      </tr>
      <? endforeach ?>
      <? endif ?>
    </tbody>
  </table>
</form>

<?= $pagedAgreements->render(url_for("agreements/?perPage={$perPage}&amp;" . http_build_query($filter))) ?>

<? begin_slot('js') ?>
<script>
$(function()
{
  $('tbody').on('click', '.btn-danger', function()
  {
    return confirm('Czy na pewno chcesz bezpowrotnie usunąć wybraną umowę?');
  });
});
</script>
<? append_slot() ?>
