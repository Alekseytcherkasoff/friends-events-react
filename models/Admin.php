<?php

class Admin
{

    /**
     * Обычные пользователи (без админов). Пароль не отдаём.
     */
    public static function selectAllUsers()
    {
        $db = Db::getConnection();
        $sql = 'SELECT `id`, `login`, `surname`, `name`, `patronymic` '
            . 'FROM `user` WHERE `flag` = 0 ORDER BY `id`';
        $result = $db->query($sql);
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Удаляет пользователя вместе с его событиями и записями на события.
     * Админа удалить нельзя.
     * @return bool true, если пользователь удалён
     */
    public static function DelUserById($id)
    {
        $db = Db::getConnection();
        try {
            $db->beginTransaction();

            // участники событий этого пользователя
            $result = $db->prepare(
                'DELETE p FROM participant p JOIN event e ON e.id = p.event WHERE e.user = :id'
            );
            $result->bindValue(':id', (int)$id, PDO::PARAM_INT);
            $result->execute();

            // его собственные записи на чужие события
            $result = $db->prepare('DELETE FROM `participant` WHERE `user` = :id');
            $result->bindValue(':id', (int)$id, PDO::PARAM_INT);
            $result->execute();

            // его события
            $result = $db->prepare('DELETE FROM `event` WHERE `user` = :id');
            $result->bindValue(':id', (int)$id, PDO::PARAM_INT);
            $result->execute();

            // сам пользователь (только flag = 0)
            $result = $db->prepare('DELETE FROM `user` WHERE `id` = :id AND `flag` = 0');
            $result->bindValue(':id', (int)$id, PDO::PARAM_INT);
            $result->execute();
            $deleted = $result->rowCount() > 0;

            if ($deleted) {
                $db->commit();
            } else {
                $db->rollBack(); // не откатываем зря чужие данные, если пользователя нет
            }
            return $deleted;
        } catch (PDOException $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            throw $e;
        }
    }

    /**
     * Участники события (раньше в запросе была ошибка: user.event вместо event)
     */
    public static function delAllParticipant($id)
    {
        $db = Db::getConnection();
        $result = $db->prepare('DELETE FROM `participant` WHERE `event` = :id');
        $result->bindValue(':id', (int)$id, PDO::PARAM_INT);
        return $result->execute();
    }

    /**
     * Удаляет событие и его участников одной транзакцией
     */
    public static function delOneEvent($id)
    {
        $db = Db::getConnection();
        try {
            $db->beginTransaction();

            $result = $db->prepare('DELETE FROM `participant` WHERE `event` = :id');
            $result->bindValue(':id', (int)$id, PDO::PARAM_INT);
            $result->execute();

            $result = $db->prepare('DELETE FROM `event` WHERE `id` = :id');
            $result->bindValue(':id', (int)$id, PDO::PARAM_INT);
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

    public static function getFioById($id)
    {
        $db = Db::getConnection();
        $sql = 'SELECT `id`, `surname`, `name`, `patronymic` FROM `user` WHERE `id` = :id';
        $result = $db->prepare($sql);
        $result->bindValue(':id', (int)$id, PDO::PARAM_INT);
        $result->execute();
        return $result->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Все события с ФИО организатора одним запросом
     */
    public static function selectAllEvents()
    {
        $db = Db::getConnection();
        $sql = 'SELECT e.id, e.time, e.date, e.title, e.description, '
            . 'u.surname, u.name, u.patronymic '
            . 'FROM event e LEFT JOIN user u ON u.id = e.user '
            . 'ORDER BY e.date, e.time';
        $result = $db->query($sql);
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

}
