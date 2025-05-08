<?php

$pdo = require 'Connection.php';
$statement = $pdo->query('SELECT * FROM users');
print_r($statement->fetchAll());