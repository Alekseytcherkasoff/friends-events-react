<?php
return array(
    'admin/del_user/([0-9]+)' => 'admin/del_user/$1', // удалить пользователя
    'admin/del_event/([0-9]+)' => 'admin/del_event/$1',
    'admin/users' => 'admin/users',
    'admin' => 'admin/index',
    'user/del_event/([0-9]+)' => 'user/del_event/$1',
    'user/del_participation/([0-9]+)' => 'user/del_participation/$1',
    'user/add-event' => 'user/add_event',
    'user/change-avatar' => 'user/change_avatar',
    'user/my-event' => 'user/my_event',
    'user' => 'user/index',
    'sign_up/([0-9]+)' => 'main/sign_up/$1',
    'registration' => 'main/registration',
    'login' => 'main/login',
    'logout' => 'main/logout',
    '' => 'main/index',  // главная страница
);


?>