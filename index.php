<?php
// Точка входа JSON API. Страницы показывает React-приложение из папки web/ (см. .htaccess).

// Ошибки в лог, а не в ответ: иначе они сломают JSON
ini_set('display_errors', 0);
error_reporting(E_ALL);

// cookie сессии: недоступна из JavaScript и не отправляется с запросов других сайтов
session_set_cookie_params(array(
    'lifetime' => 0,
    'path' => '/',
    'httponly' => true,
    'samesite' => 'Lax',
));
session_start();

define('ROOT', dirname(__FILE__));
require_once(ROOT . '/components/Db.php');
require_once(ROOT . '/components/Api.php');
require_once(ROOT . '/components/ApiRouter.php');

$api = new ApiRouter();
$api->run();
