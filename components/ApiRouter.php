<?php

/**
 * Маршрутизатор для адресов /api/...
 * В отличие от старого Router учитывает HTTP-метод (GET, POST, PUT, DELETE).
 */
class ApiRouter
{

    // [метод, шаблон адреса, контроллер, действие]
    private $routes = array(
        array('GET',    'api/events',                       'ApiMain',  'events'),
        array('GET',    'api/me',                           'ApiMain',  'me'),
        array('POST',   'api/login',                        'ApiMain',  'login'),
        array('POST',   'api/logout',                       'ApiMain',  'logout'),
        array('POST',   'api/register',                     'ApiMain',  'register'),
        array('POST',   'api/events/([0-9]+)/join',         'ApiMain',  'join'),

        array('GET',    'api/user/events',                  'ApiUser',  'events'),
        array('POST',   'api/user/events',                  'ApiUser',  'createEvent'),
        array('DELETE', 'api/user/events/([0-9]+)',         'ApiUser',  'deleteEvent'),
        array('GET',    'api/user/participations',          'ApiUser',  'participations'),
        array('DELETE', 'api/user/participations/([0-9]+)', 'ApiUser',  'deleteParticipation'),
        array('PUT',    'api/user/avatar',                  'ApiUser',  'changeAvatar'),

        array('GET',    'api/admin/events',                 'ApiAdmin', 'events'),
        array('DELETE', 'api/admin/events/([0-9]+)',        'ApiAdmin', 'deleteEvent'),
        array('GET',    'api/admin/users',                  'ApiAdmin', 'users'),
        array('DELETE', 'api/admin/users/([0-9]+)',         'ApiAdmin', 'deleteUser'),
    );

    public static function isApiRequest()
    {
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        return $path === '/api' || strpos($path, '/api/') === 0;
    }

    public function run()
    {
        // Ошибки PHP не должны попадать в тело ответа и ломать JSON: они идут в лог
        ini_set('display_errors', '0');

        try {
            $path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
            $method = $_SERVER['REQUEST_METHOD'];
            $allowed = array();

            foreach ($this->routes as $route) {
                list($routeMethod, $pattern, $controller, $action) = $route;

                if (!preg_match('~^' . $pattern . '$~', $path, $matches)) {
                    continue;
                }
                if ($routeMethod !== $method) {
                    $allowed[] = $routeMethod;
                    continue;
                }

                array_shift($matches); // убираем полное совпадение, остаются параметры из адреса
                $this->call($controller, $action, $matches);
                return;
            }

            if ($allowed) {
                header('Allow: ' . implode(', ', array_unique($allowed)));
                Api::error(405, 'Метод не поддерживается для этого адреса');
            }
            Api::error(404, 'Такого адреса нет');
        } catch (Throwable $e) {
            error_log($e);
            Api::error(500, 'Внутренняя ошибка сервера');
        }
    }

    private function call($controller, $action, $params)
    {
        $className = $controller . 'Controller';
        require_once(ROOT . '/controllers/' . $className . '.php');
        $object = new $className();
        call_user_func_array(array($object, $action), $params);
    }

}
