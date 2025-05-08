<?php
require_once __DIR__ . '/../models/User.php';

use models\User;

if(!isset($_GET['id'])){
    header("Location: list-user.php");
    exit;
}

$user = User::find($_GET['id']);

if(!$user) {
    header("Location: list-user.php");
    exit;
}

User::delete($user['id']);
header("Location: list-user.php");
