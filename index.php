<?php
//to run: http://localhost/website/
require_once __DIR__ . '/vendor/autoload.php';

use Myproject\Website\Controller\UserController;

$controller = new UserController();
$controller->createUser();
?>
