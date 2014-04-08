<?php

// Copyright (c) 2014, Łukasz Walukiewicz <lukasz@walukiewicz.eu>. Some Rights Reserved.
// Licensed under CC BY-NC-SA 4.0 <http://creativecommons.org/licenses/by-nc-sa/4.0/>.
// Part of the walkner-agreedb project <http://lukasz.walukiewicz.eu/p/walkner-agreedb>

include __DIR__ . '/__common__.php';

no_access_if_cant_manage();

bad_request_if(empty($_GET['id']));

$agreement = fetch_one('SELECT id, uploaded, filepath FROM agreements WHERE id=? LIMIT 1', array(1 => $_GET['id']));

not_found_if(empty($agreement));

exec_stmt('DELETE FROM agreements WHERE id=?', array(1 => $_GET['id']));

if ($agreement->uploaded)
{
  @unlink(APP_UPLOADS_PATH . $agreement->filepath);
}

set_flash('Umowa została usunięta pomyślnie!');
go_to("/agreements/");
