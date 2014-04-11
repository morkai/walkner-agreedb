<?php

// Copyright (c) 2014, Łukasz Walukiewicz <lukasz@walukiewicz.eu>. Some Rights Reserved.
// Licensed under CC BY-NC-SA 4.0 <http://creativecommons.org/licenses/by-nc-sa/4.0/>.
// Part of the walkner-agreedb project <http://lukasz.walukiewicz.eu/p/walkner-agreedb>

?>
<fieldset>
  <div class="control-group">
    <label for=user-name class="control-label">Imię i nazwisko:</label>
    <input id=user-name name=user[name] class="span3" type=text value="<?= $user->name ?>" autofocus required>
  </div>
  <div class="control-group">
    <label for=user-name class="control-label">Login:</label>
    <input id=user-name name=user[login] class="span3" type=text value="<?= $user->login ?>" required>
  </div>
  <div class="row">
    <div class="control-group span3">
      <label for=user-name class="control-label">Hasło:</label>
      <input id=user-name name=user[password] class="span3" type=password value="">
    </div>
    <div class="control-group span3">
      <label for=user-position class="control-label">Potwierdzenie hasła:</label>
      <input id=user-position name=user[password2] class="span3" type=password value="">
    </div>
  </div>
  <div class="control-group">
    <label class="checkbox"><input id=user-manage name=user[manage] type=checkbox value="1" <?= $user->manage ? 'checked' : '' ?>> Zarządzanie</label>
  </div>
  <div class="form-actions">
    <input class="btn btn-large btn-primary" type="submit" value="Zapisz">
  </div>
</fieldset>
