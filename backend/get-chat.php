<?php

include_once "app/controller/MessageController.php";
include_once "app/controller/AuthController.php";
include_once "app/Config.php";

$auth = new AuthController();
$auth->checkAuth();

$mess = new MessageController();
$mess->getChat();
$block_user = isset($_POST['block_user']) ? $_POST['block_user'] : "0"; 
if ($block_user == "1") {
    $mess->insertChatblock(); 
} 