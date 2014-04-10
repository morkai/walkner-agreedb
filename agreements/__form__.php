<?php

// Copyright (c) 2014, Łukasz Walukiewicz <lukasz@walukiewicz.eu>. Some Rights Reserved.
// Licensed under CC BY-NC-SA 4.0 <http://creativecommons.org/licenses/by-nc-sa/4.0/>.
// Part of the walkner-agreedb project <http://lukasz.walukiewicz.eu/p/walkner-agreedb>

?>
<div class="control-group">
  <label for=agreement-company class="control-label">Nazwa firmy:</label>
  <input id=agreement-company name=agreement[company] class="span6" type=text value="<?= $agreement->company ?>" autofocus required maxlength=200>
</div>
<div class="control-group">
  <label for=agreement-address class="control-label">Adres firmy:</label>
  <textarea id=agreement-address name=agreement[address] class="span6" rows="4" maxlength=200><?= $agreement->address ?></textarea>
</div>
<div class="row">
  <div class="control-group span2">
    <label for=agreement-krs class="control-label">KRS:</label>
    <input id=agreement-krs name=agreement[krs] class="span2" type=text value="<?= $agreement->krs ?>" maxlength=10 pattern="^[0-9]{10}$" placeholder="0000000000">
  </div>
  <div class="control-group span2">
    <label for=agreement-nip class="control-label">NIP:</label>
    <input id=agreement-nip name=agreement[nip] class="span2" type=text value="<?= $agreement->nip ?>" maxlength=10 pattern="^[0-9]{10}$" placeholder="0000000000">
  </div>
</div>
<div class="row">
  <div class="control-group span2">
    <label for=agreement-date class="control-label">Data umowy:</label>
    <input id=agreement-date name=agreement[date] class="span2" type=date value="<?= date('Y-m-d', $agreement->date) ?>" required placeholder="YYYY-MM-DD">
  </div>
  <div class="control-group span4">
    <label for=agreement-subject class="control-label">Przedmiot umowy:</label>
    <input id=agreement-subject name=agreement[subject] class="span4" type=text value="<?= $agreement->subject ?>" required maxlength=200>
  </div>
</div>
<div class="control-group">
  <label for=agreement-filepath class="control-label">Ścieżka do pliku w sieci:</label>
  <input id=agreement-filepath name=agreement[filepath] class="span6" type=text value="<?= $agreement->uploaded ? '' : $agreement->filepath ?>" maxlength=250 pattern="^\\\\.+\\.+\.[a-zA-Z0-9]+$" placeholder="\\COMPUTER\Share\File.ext">
  <span class="help-block">
    Ścieżka do pliku na dysku sieciowym.<br>
    Jeżeli nie zostanie podana, to trzeba wybrać plik do załadowania z dysku lokalnego.
  </span>
</div>
<div class="control-group">
  <label for=agreement-upload class="control-label">
    <? if ($agreement->uploaded): ?>
    Nowy plik z dysku lokalnego:
    <? else: ?>
    Plik z dysku lokalnego:
    <? endif ?>
  </label>
  <input id=agreement-upload name=upload class="span6" type=file>
  <? if ($mode === 'edit' && $agreement->uploaded): ?>
  <span class="help-block">
    Wybierz tylko wtedy, gdy chcesz zamienić <a href="<?= url_for("agreements/download.php?id={$agreement->id}") ?>">aktualny plik umowy</a>.
  </span>
  <? endif ?>
</div>
<div class="control-group">
  <label for=agreement-filename class="control-label">Nazwa pliku:</label>
  <input id=agreement-filename name=agreement[filename] class="span6" type=text value="<?= $agreement->filename ?>" maxlength="250" pattern="^.+\.[a-zA-Z0-9]+$" placeholder="File.ext">
  <span class="help-block">
    Nazwa pliku wraz z rozszerzeniem, pod którą zapisywany ma być plik podczas ściągania go przez użytkownika.<br>
    Jeżeli wartość nie zostanie podana, to nazwa będzie równa nazwie wyżej wybranego pliku.
  </span>
</div>
<div class="control-group is-last">
  <label for=agreement-users class="control-label">Ogranicz dostęp do użytkowników:</label>
  <input id=agreement-users name=agreement[users] type=text value="">
</div>
<div class="form-actions">
  <input class="btn btn-large btn-primary" type="submit" value="Zapisz">
</div>

<? begin_slot('js') ?>
<script>
$(function()
{
  var $company = $('#agreement-company');
  var $users = $('#agreement-users');

  $users.select2({
    width: $company.width(),
    allowClear: true,
    minimumInputLength: 3,
    multiple: true,
    ajax: {
      quietMillis: 250,
      url: '<?= url_for("/users/index.php") ?>',
      dataType: 'json',
      data: function(term, page)
      {
        return {
          query: term,
          page: page,
          perPage: 5
        };
      },
      results: function (data)
      {
        if (!Array.isArray(data))
        {
          data = [];
        }

        return {results: data.map(userToSelect2)};
      }
    }
  });

  $users.select2('data', <?= json_encode($agreement->users) ?>.map(userToSelect2));

  function userToSelect2(user)
  {
    return {
      id: user.id,
      text: user.name
    };
  }

  <? if ($mode === 'add'): ?>
  var $filepath = $('#agreement-filepath');
  var $upload = $('#agreement-upload');

  $('form').on('submit', function()
  {
    if ($filepath.val() === '' && $upload.val() === '')
    {
      alert('Podaj Ścieżkę do pliku w sieci lub wybierz Plik z dysku lokalnego!');

      $filepath.focus();

      return false;
    }

    return true;
  });
  <? endif ?>
});
</script>
<? append_slot() ?>
