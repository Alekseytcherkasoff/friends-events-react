<?php

class Main
{

    /**
     * Проверяем логин и пароль.
     * Пароли хранятся хешами (password_hash). Старые пароли в открытом виде
     * принимаются один раз и сразу заменяются хешем, поэтому существующие
     * пользователи не теряют доступ.
     * @return int|false id пользователя или false
     */
    public static function checkUserData($login, $password)
    {
        $login = (string)$login;
        $password = (string)$password;

        $db = Db::getConnection();
        $result = $db->prepare('SELECT id, password FROM user WHERE login = :login LIMIT 1');
        $result->bindValue(':login', $login, PDO::PARAM_STR);
        $result->execute();
        $user = $result->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return false;
        }

        $stored = (string)$user['password'];

        if (self::isHash($stored)) {
            if (!password_verify($password, $stored)) {
                return false;
            }
            if (password_needs_rehash($stored, PASSWORD_DEFAULT)) {
                self::updatePasswordHash($user['id'], $password);
            }
            return (int)$user['id'];
        }

        // старая запись с паролем в открытом виде
        if (hash_equals($stored, $password)) {
            self::updatePasswordHash($user['id'], $password);
            return (int)$user['id'];
        }

        return false;
    }

    private static function isHash($value)
    {
        return strlen($value) >= 60 && preg_match('/^\$(2y|argon2id?)\$/', $value) === 1;
    }

    private static function updatePasswordHash($userId, $password)
    {
        $db = Db::getConnection();
        $result = $db->prepare('UPDATE user SET password = :password WHERE id = :id');
        $result->bindValue(':password', password_hash($password, PASSWORD_DEFAULT), PDO::PARAM_STR);
        $result->bindValue(':id', (int)$userId, PDO::PARAM_INT);
        $result->execute();
    }


    /**
     * Запоминаем пользователя в сессии
     */
    public static function auth($userId)
    {
        $db = Db::getConnection();
        $result = $db->prepare('SELECT flag FROM user WHERE id = :id');
        $result->bindValue(':id', (int)$userId, PDO::PARAM_INT);
        $result->execute();
        $row = $result->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return false;
        }

        session_regenerate_id(true);
        $_SESSION['user'] = (int)$userId;
        $_SESSION['flag'] = (int)$row['flag'];
        return true;
    }


    public static function loginExists($login)
    {
        $db = Db::getConnection();
        $result = $db->prepare('SELECT 1 FROM user WHERE login = :login LIMIT 1');
        $result->bindValue(':login', (string)$login, PDO::PARAM_STR);
        $result->execute();
        return (bool)$result->fetchColumn();
    }


    /**
     * @return bool false, если такой логин уже занят
     */
    public static function insertUser($login, $password, $name, $surname, $patronymic)
    {
        if (self::loginExists($login)) {
            return false;
        }

        $db = Db::getConnection();
        $sql = 'INSERT INTO user (login, password, name, surname, patronymic) '
            . 'VALUES (:login, :password, :name, :surname, :patronymic)';

        try {
            $result = $db->prepare($sql);
            $result->bindValue(':login', (string)$login, PDO::PARAM_STR);
            $result->bindValue(':password', password_hash((string)$password, PASSWORD_DEFAULT), PDO::PARAM_STR);
            $result->bindValue(':name', (string)$name, PDO::PARAM_STR);
            $result->bindValue(':surname', (string)$surname, PDO::PARAM_STR);
            $result->bindValue(':patronymic', (string)$patronymic, PDO::PARAM_STR);
            return $result->execute();
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { // нарушение уникальности (гонка запросов)
                return false;
            }
            throw $e;
        }
    }


    public static function getDataManager($id)
    {
        $db = Db::getConnection();
        $sql = 'SELECT `surname`, `name`, `patronymic`, `avatar` FROM `user` WHERE `id` = :id';
        $result = $db->prepare($sql);
        $result->bindValue(':id', (int)$id, PDO::PARAM_INT);
        $result->execute();
        return $result->fetch(PDO::FETCH_ASSOC);
    }

    public static function getParticipant($id)
    {
        $db = Db::getConnection();
        $result = $db->prepare('SELECT COUNT(`id`) FROM `participant` WHERE `event` = :id');
        $result->bindValue(':id', (int)$id, PDO::PARAM_INT);
        $result->execute();
        return (int)$result->fetchColumn();
    }


    /**
     * Все события с организатором и числом участников одним запросом.
     * Если передан $userId, у каждого события есть признак joined (записан ли этот пользователь).
     * Формат результата прежний: manager (массив) и participant (число).
     */
    public static function selectAllEvents($userId = null)
    {
        $db = Db::getConnection();
        $sql = 'SELECT e.id, e.time, e.date, e.title, e.description,
                       u.surname, u.name, u.patronymic, u.avatar,
                       (SELECT COUNT(*) FROM participant p WHERE p.event = e.id) AS participant_count,
                       EXISTS(SELECT 1 FROM participant pj WHERE pj.event = e.id AND pj.user = :uid) AS joined
                FROM event e
                LEFT JOIN user u ON u.id = e.user
                ORDER BY e.date, e.time';
        $result = $db->prepare($sql);
        $result->bindValue(':uid', $userId === null ? 0 : (int)$userId, PDO::PARAM_INT);
        $result->execute();

        $groups = array();
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $groups[] = array(
                'id' => $row['id'],
                'time' => $row['time'],
                'date' => $row['date'],
                'title' => $row['title'],
                'description' => $row['description'],
                'manager' => array(
                    'surname' => $row['surname'],
                    'name' => $row['name'],
                    'patronymic' => $row['patronymic'],
                    'avatar' => $row['avatar'],
                ),
                'participant' => (int)$row['participant_count'],
                'joined' => (bool)$row['joined'],
            );
        }
        return $groups;
    }


    /**
     * Запись на событие
     * @return bool false, если уже записан или события не существует
     */
    public static function sign_up($event, $user)
    {
        $db = Db::getConnection();
        $sql = 'INSERT INTO participant (event, user) '
            . 'SELECT id, :user FROM event WHERE id = :event';

        try {
            $result = $db->prepare($sql);
            $result->bindValue(':user', (int)$user, PDO::PARAM_INT);
            $result->bindValue(':event', (int)$event, PDO::PARAM_INT);
            $result->execute();
            return $result->rowCount() > 0;
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { // уже записан (UNIQUE event + user)
                return false;
            }
            throw $e;
        }
    }

}
