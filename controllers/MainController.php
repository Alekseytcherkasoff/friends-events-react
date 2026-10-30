<?php

include_once ROOT.'/models/Main.php';

class MainController
{


    public function actionIndex()
    {
        $selectAllEvents = Main::selectAllEvents();

        require_once(ROOT . '/views/main/index.php');
        return true;
    }



    public function actionLogin()
    {

        if (isset($_POST['submit'])) {

            $login = $_POST['login'];
            $password = $_POST['password'];
            // Проверяем существует ли пользователь
            $userId = Main::checkUserData($login, $password);

            if ($userId == false) {
                // Если данные неправильные - показываем ошибку
                echo '<script type="text/javascript">alert("Неправильные данные для входа на сайт");</script>';
                echo '<script type="text/javascript">location.replace("'.$_SERVER['HTTP_REFERER'].'");</script>'; 
            }
            else {
                // Если данные правильные, запоминаем пользователя (сессия)
                Main::auth($userId);
 

                // Перенаправляем пользователя в закрытую часть - кабинет 
                if ($_SESSION['flag'] == '1') {
                    $redirection = '/admin';
                }
                else {
                    $redirection = '/user';
                } 
                header("Location: $redirection");
            } 
        }        

        require_once(ROOT . '/views/main/login.php');
        return true;
    }

    public function actionRegistration()
    {
        if (isset($_POST['submit'])) {

            $login = $_POST['login'];
            $password = $_POST['password'];
            $name = $_POST['name'];
            $surname = $_POST['surname'];
            $patronymic = $_POST['patronymic'];
            $insertUser = Main::insertUser($login, $password, $name, $surname, $patronymic);

            if($insertUser)
            {
                echo '<script type="text/javascript">alert("Вы зарегистрированы. Поздравляю!");</script>';
                echo '<script type="text/javascript">location.replace("/login");</script>'; 
            }
            else
            {
                echo '<script type="text/javascript">alert("Неправильные данные. Такой логин и пароль уже зарегистрированы.");</script>';
                echo '<script type="text/javascript">location.replace("'.$_SERVER['HTTP_REFERER'].'");</script>'; 
            }
        }

        require_once(ROOT . '/views/main/registration.php');
        return true;
    }
    
    

    public function actionSign_up($id)
    {
        if (!isset($_SESSION['user']))
        {
            echo '<script type="text/javascript">alert("Нужно залогиниться! (Войти в систему в качестве зарегистрированного пользователя)");</script>';
            echo '<script type="text/javascript">location.replace("/");</script>';
            return true;
        }
        $event = $id;
        $user = $_SESSION['user'];
        $sign_up = Main::sign_up($event, $user);
        if(!$sign_up)
        {
            echo '<script type="text/javascript">alert("Похоже Вы уже зарегистрировались на это мероприятие.");</script>';
            echo '<script type="text/javascript">location.replace("/");</script>'; 
        }
        else
        {
            echo '<script type="text/javascript">alert("Поздарвляю! Вы зарегистрировались.");</script>';
            echo '<script type="text/javascript">location.replace("/");</script>'; 
        }
        //header("Location: /");
        return true;
    }

    
    /**
     * Удаляем данные о пользователе из сессии
     */
    public function actionLogout()
    {

        unset($_SESSION["user"]);
        unset($_SESSION["flag"]);
        header("Location: /");
    }

}