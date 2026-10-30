<?php

include_once ROOT.'/models/User.php';

class UserController
{
    function __construct() {
        if (!isset($_SESSION['flag']) or $_SESSION['flag'] != 0)
        {      
            header("Location: /");
            exit;
        }
    }


    public function actionAdd_event()
    {
        if (isset($_POST['submit'])) {
            $time = $_POST['time'];
            $date = $_POST['date'];
            $title = $_POST['title'];
            $description = $_POST['description'];
            $user = $_SESSION['user'];
            
            $insertEvent = User::insertEvent($time, $date, $title, $description, $user);
            if($insertEvent)
            {
                echo '<script type="text/javascript">alert("Мероприятие добавлено. Поздравляю!");</script>';
                echo '<script type="text/javascript">location.replace("/");</script>'; 
            }
            else
            {
                echo '<script type="text/javascript">alert("Что-то пошло не так.");</script>';
                echo '<script type="text/javascript">location.replace("/");</script>'; 
            }
        }
        
        
        require_once(ROOT . '/views/user/add-event.php');
        return true;
    }


    
    public function actionChange_avatar()
    {
        if (isset($_POST['submit'])) {
            $avatar = $_POST['inlineRadioOptions'];
            $user =  $_SESSION['user'];

            $insertNewAvatar = User::insertNewAvatar($user, $avatar);

            if($insertNewAvatar)
            {
                echo '<script type="text/javascript">alert("Аватар изменен. Поздравляю!");</script>';
                echo '<script type="text/javascript">location.replace("/user");</script>'; 
            }
            else
            {
                echo '<script type="text/javascript">alert("Что-то пошло не так.");</script>';
                echo '<script type="text/javascript">location.replace("/user");</script>'; 
            }

        }

        
        require_once(ROOT . '/views/user/change_avatar.php');
        return true;
    }


    public function actionMy_event()
    {
        $selectAllMyEventsById = User::selectAllMyEventsById($_SESSION['user']);               
        require_once(ROOT . '/views/user/my_event.php');
        return true;
    }

    public function actionIndex()
    {
        $getFioById = User::getFioById($_SESSION['user']);
        $selectAllEventsById = User::selectAllEventsById($_SESSION['user']);      
        require_once(ROOT . '/views/user/index.php');
        return true;
    }


    public function actionDel_event($id)
    {
        $delOneEvent = User::delOneEvent($id, $_SESSION['user']);
        header("Location: /user");
        return true;
    }
    
    public function actionDel_participation($id)
    {
        $delOneParticipation = User::delOneParticipation($id, $_SESSION['user']);
        header("Location: /user/my-event");
        return true;
    }



}
?>