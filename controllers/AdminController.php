<?php

include_once ROOT.'/models/Admin.php';

class AdminController
{
    function __construct() {
        if (!isset($_SESSION['flag']) or $_SESSION['flag'] != 1)
        {      
            header("Location: /");
            exit;
        }
    }


    public function actionUsers()
    {
        $i = 1;
        $selectAllUsers = Admin::selectAllUsers();
        require_once(ROOT . '/views/admin/users.php');
        return true;
    }


    public function actionIndex()
    {
        $selectAllEvents = Admin::selectAllEvents();  

        require_once(ROOT . '/views/admin/index.php');
        return true;
    }


    public function actionDel_user($id)
    {
        $delOneGroup = Admin::DelUserById($id);
        header("Location: /admin/users");
        return true;
    }

    public function actionDel_event($id)
    {
        $delAllParticipant = Admin::delAllParticipant($id);
        $delOneEvent = Admin::delOneEvent($id);
        header("Location: /admin");
        return true;
    }


}

?>