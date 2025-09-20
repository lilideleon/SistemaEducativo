<?php
require "config/config.php";
require "core/routes.php";
require "config/database.php";
require "controllers/Login.php";

$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST['username'] = 'admin';
$_POST['password'] = 'admin';

$controller = new LoginController();
$controller->Validate();
