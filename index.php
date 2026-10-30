<?php
// FRONT CONTROLLER

// 1. Общие настройки
ini_set('display_errors', 1);  // включили отображение ошибок
error_reporting(E_ALL); // Показывать все ошибки

// cookie сессии: недоступна из JavaScript и не отправляется с запросов других сайтов
session_set_cookie_params(array(
    'lifetime' => 0,
    'path' => '/',
    'httponly' => true,
    'samesite' => 'Lax',
));
session_start();

// 2. Подключение файлов системы
define('ROOT', dirname(__FILE__)); //константа ROOT - полный путь до индексного файла
require_once(ROOT . '/components/Router.php');
require_once(ROOT . '/components/Db.php');
require_once(ROOT . '/components/Api.php');
require_once(ROOT . '/components/ApiRouter.php');

// 3. Запросы вида /api/... обрабатывает API, остальные — прежний Router (страницы на PHP)
if (ApiRouter::isApiRequest()) {
    $api = new ApiRouter();
    $api->run();
} else {
    $router = new Router();
    $router->run();
}
