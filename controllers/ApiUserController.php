<?php

include_once ROOT . '/models/User.php';

class ApiUserController
{

    // GET /api/user/events — события, которые создал пользователь
    public function events()
    {
        $userId = Api::requireUser();
        Api::json(array('events' => User::selectAllEventsById($userId)));
    }

    // POST /api/user/events  {time, date, title, description}
    public function createEvent()
    {
        $userId = Api::requireUser();
        $data = Api::body();

        $time = Api::field($data, 'time');
        $date = Api::field($data, 'date');
        $title = Api::field($data, 'title');
        $description = Api::field($data, 'description');

        if ($time === '' || $date === '' || $title === '' || $description === '') {
            Api::error(422, 'Заполните все поля');
        }

        // дата в формате дд.мм.гггг, как в вашей базе
        $parsed = DateTime::createFromFormat('d.m.Y', $date);
        if (!$parsed || $parsed->format('d.m.Y') !== $date) {
            Api::error(422, 'Дата должна быть в формате дд.мм.гггг');
        }

        foreach (array($time, $title, $description) as $value) {
            if (mb_strlen($value) > 255) {
                Api::error(422, 'Слишком длинное значение (не больше 255 символов)');
            }
        }

        if (!User::insertEvent($time, $date, $title, $description, $userId)) {
            Api::error(500, 'Не удалось сохранить мероприятие');
        }

        Api::json(array('success' => true), 201);
    }

    // DELETE /api/user/events/{id} — удалить своё событие
    public function deleteEvent($id)
    {
        $userId = Api::requireUser();

        if (!User::delOneEvent($id, $userId)) {
            Api::error(404, 'Мероприятие не найдено или оно не ваше');
        }
        Api::json(array('success' => true));
    }

    // GET /api/user/participations — события, на которые записан пользователь
    public function participations()
    {
        $userId = Api::requireUser();
        Api::json(array('participations' => User::selectAllMyEventsById($userId)));
    }

    // DELETE /api/user/participations/{id} — отменить свою запись
    public function deleteParticipation($id)
    {
        $userId = Api::requireUser();

        if (!User::delOneParticipation($id, $userId)) {
            Api::error(404, 'Запись не найдена');
        }
        Api::json(array('success' => true));
    }

    // PUT /api/user/avatar  {avatar: "avatar1".."avatar8"}
    public function changeAvatar()
    {
        $userId = Api::requireUser();
        $data = Api::body();
        $avatar = Api::field($data, 'avatar');

        if (!preg_match('/^avatar[1-8]$/', $avatar)) {
            Api::error(422, 'Выберите один из аватаров');
        }

        User::insertNewAvatar($userId, $avatar);
        Api::json(array('success' => true, 'avatar' => $avatar));
    }

}
