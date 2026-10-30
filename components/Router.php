<?php

class Router
{

    private $routes;  // массив, где будут храниться маршруты

    public function __construct()
    {
        $routesPath = ROOT . '/config/routes.php';
        $this->routes = include($routesPath);
    }

    private function getURI()
    {
        // Возвращает путь запроса без параметров после "?"
        if (!empty($_SERVER['REQUEST_URI'])) {
            return trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        }
        return '';
    }


    public function run()  // принимает управление от фронтконтроллера
    {
        $uri = $this->getURI();

        foreach ($this->routes as $uriPattern => $path) {
            // Шаблон должен совпасть со всем адресом целиком (раньше совпадал любой его кусок)
            if (!preg_match("~^$uriPattern$~", $uri)) {
                continue;
            }

            // Внутренний путь из внешнего по правилу: контроллер/действие/параметры
            $internalRoute = preg_replace("~^$uriPattern$~", $path, $uri);

            $segments = explode('/', $internalRoute);
            $controllerName = ucfirst(array_shift($segments)) . 'Controller';
            $actionName = 'action' . ucfirst(array_shift($segments));
            $parameters = $segments;

            $controllerFile = ROOT . '/controllers/' . $controllerName . '.php';
            if (!file_exists($controllerFile)) {
                break;
            }
            include_once($controllerFile);

            $controllerObject = new $controllerName;
            call_user_func_array(array($controllerObject, $actionName), $parameters);
            return; // нашли маршрут и выполнили действие — дальше не ищем
        }

        http_response_code(404);
        echo 'Страница не найдена';
    }

}
