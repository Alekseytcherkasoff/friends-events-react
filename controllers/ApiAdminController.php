<?php

include_once ROOT . '/models/Admin.php';

class ApiAdminController
{

    // GET /api/admin/events
    public function events()
    {
        Api::requireAdmin();
        Api::json(array('events' => Admin::selectAllEvents()));
    }

    // GET /api/admin/users
    public function users()
    {
        Api::requireAdmin();
        Api::json(array('users' => Admin::selectAllUsers()));
    }

    // DELETE /api/admin/events/{id}
    public function deleteEvent($id)
    {
        Api::requireAdmin();

        if (!Admin::delOneEvent($id)) {
            Api::error(404, 'Мероприятие не найдено');
        }
        Api::json(array('success' => true));
    }

    // DELETE /api/admin/users/{id}
    public function deleteUser($id)
    {
        Api::requireAdmin();

        if (!Admin::DelUserById($id)) {
            Api::error(404, 'Пользователь не найден (администратора удалить нельзя)');
        }
        Api::json(array('success' => true));
    }

}
