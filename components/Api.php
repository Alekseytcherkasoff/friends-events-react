<?php

/**
 * Помощники для JSON-API: ответы, чтение тела запроса, проверка входа и роли.
 * Все методы, которые отвечают клиенту, завершают скрипт (exit), поэтому
 * после Api::json() или Api::error() код дальше не выполняется.
 */
class Api
{

    public static function json($data, $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public static function error($status, $message)
    {
        self::json(array('error' => $message), $status);
    }

    /**
     * Читает JSON из тела запроса.
     * Требуем Content-Type: application/json: обычная HTML-форма с другого
     * сайта так отправить не может, это защищает от подделки запросов (CSRF).
     */
    public static function body()
    {
        $contentType = isset($_SERVER['CONTENT_TYPE']) ? $_SERVER['CONTENT_TYPE'] : '';
        if (stripos($contentType, 'application/json') !== 0) {
            self::error(415, 'Ожидается Content-Type: application/json');
        }

        $data = json_decode(file_get_contents('php://input'), true);
        if (!is_array($data)) {
            self::error(400, 'Некорректный JSON');
        }
        return $data;
    }

    /**
     * Строковое поле из тела запроса (обрезаем пробелы; нет поля или не строка — пустая строка)
     */
    public static function field($data, $key)
    {
        if (!isset($data[$key]) || !is_string($data[$key])) {
            return '';
        }
        return trim($data[$key]);
    }

    public static function currentUserId()
    {
        return isset($_SESSION['user']) ? (int)$_SESSION['user'] : null;
    }

    /** Любой вошедший пользователь */
    public static function requireLogin()
    {
        $id = self::currentUserId();
        if ($id === null) {
            self::error(401, 'Нужно войти в систему');
        }
        return $id;
    }

    /** Обычный пользователь (flag = 0) */
    public static function requireUser()
    {
        $id = self::requireLogin();
        if (!isset($_SESSION['flag']) || (int)$_SESSION['flag'] !== 0) {
            self::error(403, 'Доступно только обычным пользователям');
        }
        return $id;
    }

    /** Администратор (flag = 1) */
    public static function requireAdmin()
    {
        $id = self::requireLogin();
        if (!isset($_SESSION['flag']) || (int)$_SESSION['flag'] !== 1) {
            self::error(403, 'Доступно только администратору');
        }
        return $id;
    }

    /** Данные пользователя для ответа клиенту (без пароля) */
    public static function userPayload($userId)
    {
        $user = User::getFioById($userId);
        if (!$user) {
            return null;
        }
        return array(
            'id' => (int)$user['id'],
            'login' => $user['login'],
            'surname' => $user['surname'],
            'name' => $user['name'],
            'patronymic' => $user['patronymic'],
            'avatar' => $user['avatar'],
            'role' => ((int)$_SESSION['flag'] === 1) ? 'admin' : 'user',
        );
    }

}
