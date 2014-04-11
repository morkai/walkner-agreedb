<?php

// Copyright (c) 2014, Łukasz Walukiewicz <lukasz@walukiewicz.eu>. Some Rights Reserved.
// Licensed under CC BY-NC-SA 4.0 <http://creativecommons.org/licenses/by-nc-sa/4.0/>.
// Part of the walkner-agreedb project <http://lukasz.walukiewicz.eu/p/walkner-agreedb>

include __DIR__ . '/../__common__.php';

$page = empty($_GET['page']) ? 1 : (int)$_GET['page'];
$perPage = empty($_GET['perPage']) ? 30 : (int)$_GET['perPage'];

$pagedAgreements = new PagedData($page, $perPage);

function filter_dayz_param($param)
{
  $dayz = empty($_GET[$param]) ? '' : trim($_GET[$param]);

  if (is_numeric($dayz))
  {
    $dayz = '=' . $dayz;
  }

  $matches = array();

  if (!preg_match('/(=|>|<|>=|<=|<>|!=)\s*(-?[0-9]+)/', $dayz, $matches))
  {
    return array_key_exists($param, $_GET) ? array() : array(
      'cond' => '<=',
      'days' => 7
    );
  }

  if ($matches[1] === '!=')
  {
    $matches[1] = '<>';
  }

  return array(
    'cond' => $matches[1],
    'days' => $matches[2]
  );
}

$today = strtotime(date('Y-m-d') . ' 00:00:00');
$where = '';
$filter = array(
  'id' => filter_numeric_param('id'),
  'company' => filter_string_param('company'),
  'nip' => filter_string_param('nip'),
  'date' => filter_date_param('date'),
  'subject' => filter_string_param('subject'),
  'alarmDate' => filter_date_param('alarmDate'),
  'alarmText' => filter_string_param('alarmText'),
  'dayz' => filter_dayz_param('dayz')
);

if (empty($filter['id']))
{
  if (empty($filter['alarmDate']))
  {
    $conditions[] = 'a.alarmDate <> 0';
  }
  else
  {
    $conditions[] = 'a.alarmDate=' . strtotime($filter['alarmDate']);
  }

  if (!empty($filter['dayz']))
  {
    $conditions[] = 'a.alarmDate ' . $filter['dayz']['cond'] . ' ' . ($today + ($filter['dayz']['days'] * 24 * 3600));
  }

  if (empty($filter['alarmDate']))
  {
    if (empty($filter['dayz']))
    {
      $conditions[] = 'a.alarmDate > 0 ';
    }
  }
  else
  {
    $conditions[] = 'a.alarmDate=' . strtotime($filter['alarmDate']);
  }

  if (!empty($filter['date']))
  {
    $conditions[] = 'a.date=' . strtotime($filter['date']);
  }

  apply_string_condition($conditions, $filter, 'company');
  apply_string_condition($conditions, $filter, 'subject');
  apply_string_condition($conditions, $filter, 'alarmText');
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
ORDER BY a.alarmDate ASC
LIMIT {$pagedAgreements->getOffset()}, {$perPage}
SQL;

$agreements = fetch_all($q);

if (is_ajax())
{
  output_json($agreements);
}

foreach ($agreements as $agreement)
{
  $agreement->dayz = '-';
  $agreement->className = '';

  if ($agreement->alarmDate == 0)
  {
    continue;
  }

  if ($agreement->alarmDate <= $today)
  {
    $agreement->className = 'error';
  }
  else if ($agreement->alarmDate <= $today + 24 * 7 * 3600)
  {
    $agreement->className = 'warning';
  }

  $agreement->dayz = round(($agreement->alarmDate - $today) / (24 * 3600), 1);
}

$pagedAgreements->fill($totalItems, $agreements);

$filter['dayz'] = empty($filter['dayz']) ? '' : "{$filter['dayz']['cond']}{$filter['dayz']['days']}";

?>

<? decorate('Alarmy') ?>

<div class="page-header">
  <h1>Alarmy</h1>
</div>

<form id="agreementsFilter" action="<?= url_for("alarms/") ?>">
  <input type="hidden" name="perPage" value="<?= $perPage ?>">
  <table class="table table-bordered table-condensed">
    <thead>
      <tr>
        <th class="min"><label for="filter-id">ID (<kbd>ALT+1</kbd>)</label>
        <th><label for="filter-company">Nazwa firmy (<kbd>2</kbd>)</label>
        <th class="min"><label for="filter-nip">NIP (<kbd>3</kbd>)</label>
        <th class="min"><label for="filter-dayz">Dni do wyzwolenia (<kbd>4</kbd>)</label>
        <th class="min"><label for="filter-date">Data umowy (<kbd>5</kbd>)</label>
        <th><label for="filter-subject">Przedmiot umowy (<kbd>6</kbd>)</label>
        <th class="min"><label for="filter-alarmDate">Data alarmu (<kbd>7</kbd>)</label>
        <th><label for="filter-alarmText">Przedmiot alarmu (<kbd>8</kbd>)</label>
        <th class="actions">Akcje
      </tr>
    </thead>
    <thead>
      <tr class="filters">
        <td><input id=filter-id name=id type=number value="<?= e($filter['id']) ?>" min="1" autofocus accesskey="1">
        <td><input id=filter-company name=company type=text value="<?= e($filter['company']) ?>" maxlength="200" accesskey="2">
        <td><input id=filter-nip name=nip type=text value="<?= e($filter['nip']) ?>" maxlength=10 pattern="^[0-9]{3,10}$" accesskey="3">
        <td><input id=filter-dayz name=dayz type=text value="<?= e($filter['dayz']) ?>" maxlength=10 accesskey="4">
        <td><input id=filter-date name=date type=date value="<?= e($filter['date']) ?>" placeholder="YYYY-MM-DD" accesskey="5">
        <td><input id=filter-subject name=subject type=text value="<?= e($filter['subject']) ?>" maxlength="200" accesskey="6">
        <td><input id=filter-alarmDate name=alarmDate type=date value="<?= e($filter['alarmDate']) ?>" placeholder="YYYY-MM-DD" accesskey="7">
        <td><input id=filter-alarmText name=alarmText type=text value="<?= e($filter['alarmText']) ?>" maxlength="200" accesskey="8">
        <td class="actions">
          <button class="btn" type="submit" title="Filtruj umowy"><i class="icon-filter"></i></button>
          <a class="btn" href="<?= url_for("alarms") ?>" title="Wyczyść filtry"><i class="icon-remove"></i></a>
      </tr>
    </thead>
    <tbody>
      <? if ($totalItems === 0): ?>
      <tr>
        <td colspan=999>Nie znaleziono żadnych umów z ustawioną datą alarmu.</td>
      </tr>
      <? else: ?>
      <? foreach ($pagedAgreements AS $agreement): ?>
      <tr class="<?= $agreement->className ?>">
        <td><?= dash_if_empty($agreement->id) ?>
        <td><?= dash_if_empty($agreement->company) ?>
        <td><?= dash_if_empty($agreement->nip) ?>
        <td><?= $agreement->dayz ?>
        <td><?= date('Y-m-d', $agreement->date) ?>
        <td><?= dash_if_empty($agreement->subject) ?>
        <td><?= $agreement->alarmDate ? date('Y-m-d', $agreement->alarmDate) : '-' ?>
        <td><?= dash_if_empty($agreement->alarmText) ?>
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

<?= $pagedAgreements->render(url_for("alarms/?perPage={$perPage}&amp;" . http_build_query($filter))) ?>

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
