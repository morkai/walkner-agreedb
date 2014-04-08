<?php

// Copyright (c) 2014, Łukasz Walukiewicz <lukasz@walukiewicz.eu>. Some Rights Reserved.
// Licensed under CC BY-NC-SA 4.0 <http://creativecommons.org/licenses/by-nc-sa/4.0/>.
// Part of the walkner-agreedb project <http://lukasz.walukiewicz.eu/p/walkner-agreedb>

include __DIR__ . '/__common__.php';

no_access_if_cant_manage();

bad_request_if(empty($_GET['id']));

exec_stmt('DELETE FROM users WHERE id=?', array(1 => $_GET['id']));

set_flash('Użytkownik został usunięty pomyślnie!');
go_to("/users/");
