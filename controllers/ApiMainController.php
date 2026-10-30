<?php

include_once ROOT . '/models/Main.php';
include_once ROOT . '/models/User.php';

class ApiMainController
{

    // GET /api/events — список всех событий (для вошедшего пользователя есть признак joined)
    public function events()
    {
        $events = Main::selectAllEvents(Api::currentUserId());
        Api::json(array('events' => $events));
    }

    // GET /api/me — кто сейчас вошёл (user = null, если никто)
    public function me()
    {
        $id = Api::currentUserId();
        $user = ($id === null) ? null : Api::userPayload($id);

        if ($id !== null && $user === null) { // пользователя удалили, а сессия осталась
            unset($_SESSION['user'], $_SESSION['flag']);
        }
        Api::json(array('user' => $user));
    }

    // POST /api/login  {login, password}
    public function login()
    {
        $data = Api::body();
        $login = Api::field($data, 'login');
        $password = isset($data['password']) && is_string($data['password']) ? $data['password'] : '';

        if ($login === '' || $password === '') {
            Api::error(422, 'Введите логин и пароль');
        }

        $userId = Main::checkUserData($login, $password);
        if ($userId === false || !Main::auth($userId)) {
            Api::error(401, 'Неправильные данные для входа на сайт');
        }

        Api::json(array('user' => Api::userPayload($userId)));
    }

    // POST /api/logout
    public function logout()
    {
        unset($_SESSION['user'], $_SESSION['flag']);
        session_regenerate_id(true);
        Api::json(array('success' => true));
    }

    // POST /api/register  {login, password, surname, name, patronymic}
    public function register()
    {
        $data = Api::body();
        $login = Api::field($data, 'login');
        $password = isset($data['password']) && is_string($data['password']) ? $data['password'] : '';
        $surname = Api::field($data, 'surname');
        $name = Api::field($data, 'name');
        $patronymic = Api::field($data, 'patronymic');

        if ($login === '' || $surname === '' || $name === '' || $patronymic === '') {
            Api::error(422, 'Заполните все поля');
        }
        if (mb_strlen($login) < 3) {
            Api::error(422, 'Логин должен быть не короче 3 символов');
        }
        if (strlen($password) < 6) {
            Api::error(422, 'Пароль должен быть не короче 6 символов');
        }
        foreach (array($login, $surname, $name, $patronymic) as $value) {
            if (mb_strlen($value) > 100) {
                Api::error(422, 'Слишком длинное значение в одном из полей');
            }
        }

        if (!Main::insertUser($login, $password, $name, $surname, $patronymic)) {
            Api::error(409, 'Такой логин уже зарегистрирован');
        }

        Api::json(array('success' => true), 201);
    }

    // POST /api/events/{id}/join — записаться на событие
    public function join($id)
    {
        $userId = Api::requireLogin();

        if (!User::getEvent($id)) {
            Api::error(404, 'Такого мероприятия нет');
        }
        if (!Main::sign_up($id, $userId)) {
            Api::error(409, 'Похоже, Вы уже зарегистрировались на это мероприятие');
        }

        Api::json(array('success' => true));
    }

}
