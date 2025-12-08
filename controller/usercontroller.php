<?php
    /* 
    require_once __DIR__ . '/../vendor/autoload.php';

    use Myproject\Website\UserModel;

    $userModel = new UserModel();
    $userModel->createUser();
    */

    namespace Myproject\Website\Controller;

    use Myproject\Website\Model\UserModel;

    class UserController{
        public function createUser(){
            $userModel = new UserModel();
            $userModel->createUser();
        }
    }

    ?>


    <!-- to restart(after edit): composer dump-autoload -->