<?php

class User
{

    /**
     * Данные пользователя (без пароля)
     */
    public static function getFioById($id)
    {
        $db = Db::getConnection();
        $sql = 'SELECT `id`, `login`, `surname`, `name`, `patronymic`, `avatar` FROM `user` WHERE `id` = :id';
        $result = $db->prepare($sql);
        $result->bindValue(':id', (int)$id, PDO::PARAM_INT);
        $result->execute();
        return $result->fetch(PDO::FETCH_ASSOC);
    }

    public static function insertEvent($time, $date, $title, $description, $user)
    {
        $db = Db::getConnection();
        $sql = 'INSERT INTO event (time, date, title, description, user) '
            . 'VALUES (:time, :date, :title, :description, :user)';

        $result = $db->prepare($sql);
        $result->bindValue(':time', (string)$time, PDO::PARAM_STR);
        $result->bindValue(':date', (string)$date, PDO::PARAM_STR);
        $result->bindValue(':title', (string)$title, PDO::PARAM_STR);
        $result->bindValue(':description', (string)$description, PDO::PARAM_STR);
        $result->bindValue(':user', (int)$user, PDO::PARAM_INT);
        return $result->execute();
    }

    public static function insertNewAvatar($user, $avatar)
    {
        $db = Db::getConnection();
        $sql = 'UPDATE `user` SET `avatar` = :avatar WHERE `id` = :user';

        $result = $db->prepare($sql);
        $result->bindValue(':avatar', (string)$avatar, PDO::PARAM_STR);
        $result->bindValue(':user', (int)$user, PDO::PARAM_INT);
        return $result->execute();
    }

    /**
     * События, созданные пользователем
     */
    public static function selectAllEventsById($user)
    {
        $db = Db::getConnection();
        $sql = 'SELECT `id`, `time`, `date`, `title`, `description` FROM `event` '
            . 'WHERE `user` = :user ORDER BY `date`, `time`';
        $result = $db->prepare($sql);
        $result->bindValue(':user', (int)$user, PDO::PARAM_INT);
        $result->execute();
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Одно событие (массив из одной строки, как и раньше)
     */
    public static function getEvent($id)
    {
        $db = Db::getConnection();
        $sql = 'SELECT `id`, `time`, `date`, `title`, `description` FROM `event` WHERE `id` = :id';
        $result = $db->prepare($sql);
        $result->bindValue(':id', (int)$id, PDO::PARAM_INT);
        $result->execute();
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * События, на которые записан пользователь (id = id записи участия)
     */
    public static function selectAllMyEventsById($user)
    {
        $db = Db::getConnection();
        $sql = 'SELECT p.id, p.event, e.time, e.date, e.title, e.description '
            . 'FROM participant p JOIN event e ON e.id = p.event '
            . 'WHERE p.user = :user ORDER BY e.date, e.time';
        $result = $db->prepare($sql);
        $result->bindValue(':user', (int)$user, PDO::PARAM_INT);
        $result->execute();
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Удаляет событие вместе с участниками, но только если оно принадлежит $user
     * @return bool true, если событие найдено и удалено
     */
    public static function delOneEvent($id, $user)
    {
        $db = Db::getConnection();
        try {
            $db->beginTransaction();

            $result = $db->prepare(
                'DELETE p FROM participant p JOIN event e ON e.id = p.event '
                . 'WHERE e.id = :id AND e.user = :user'
            );
            $result->bindValue(':id', (int)$id, PDO::PARAM_INT);
            $result->bindValue(':user', (int)$user, PDO::PARAM_INT);
            $result->execute();

            $result = $db->prepare('DELETE FROM `event` WHERE `id` = :id AND `user` = :user');
            $result->bindValue(':id', (int)$id, PDO::PARAM_INT);
            $result->bindValue(':user', (int)$user, PDO::PARAM_INT);
            $result->execute();
            $deleted = $result->rowCount() > 0;

            $db->commit();
            return $deleted;
        } catch (PDOException $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            throw $e;
        }
    }

    /**
     * Отменяет запись на событие, но только свою
     * @return bool true, если запись найдена и удалена
     */
    public static function delOneParticipation($id, $user)
    {
        $db = Db::getConnection();
        $sql = 'DELETE FROM `participant` WHERE `id` = :id AND `user` = :user';
        $result = $db->prepare($sql);
        $result->bindValue(':id', (int)$id, PDO::PARAM_INT);
        $result->bindValue(':user', (int)$user, PDO::PARAM_INT);
        $result->execute();
        return $result->rowCount() > 0;
    }

}
