<?php

// Copyright (c) 2014, Łukasz Walukiewicz <lukasz@walukiewicz.eu>. Some Rights Reserved.
// Licensed under CC BY-NC-SA 4.0 <http://creativecommons.org/licenses/by-nc-sa/4.0/>.
// Part of the walkner-agreedb project <http://lukasz.walukiewicz.eu/p/walkner-agreedb>

include __DIR__ . '/__common__.php';

$user = user_get_data();

?>

<? decorate() ?>

<? if ($user): ?>
<p>Witaj, <?= e($user->name) ?>!</p>
<? else: ?>
<form action="<?= url_for("/users/login.php") ?>" method=post autocomplete=off>
  <fieldset>
    <div class="control-group">
      <label for=login class="control-label">Login:</label>
      <input id=login name=login class="span3" type=text value="" autofocus required>
    </div>
    <div class="control-group">
      <label for=password class="control-label">Hasło:</label>
      <input id=password name=password class="span3" type=password value="" required>
    </div>
    <div class="form-actions">
      <input class="btn btn-primary" type="submit" value="Zaloguj się">
    </div>
  </fieldset>
</form>
<? endif ?>
