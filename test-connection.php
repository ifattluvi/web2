<?php
$pdo = require 'Connection.php';
$statement = $pdo->query('select * from users');
print_r($statement->fetchAll());