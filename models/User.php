<?php

namespace models;

require_once __DIR__ . '/../config/Connection.php';

use config\Connection;
use PDO;

class User
{
    public static function get()
    {
        $pdo = Connection::make();
        $sql = 'SELECT * FROM users';
        $statement = $pdo->query($sql);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function create($data)
    {
        $pdo = Connection::make();
        $sql = 'INSERT INTO users (firstname, lastname, gender, age, weight) VALUES (:firstname, :lastname, :gender, :age, :weight)';
        $statement = $pdo->prepare($sql);
        $statement->bindParam(':firstname', $data['firstname']);
        $statement->bindParam(':lastname', $data['lastname']);
        $statement->bindParam(':gender', $data['gender']);
        $statement->bindParam(':age', $data['age']);
        $statement->bindParam(':weight', $data['weight']);

        return $statement->execute();

        // return $statement->execute([
        //    ':firstname' => $data['firstname'],
        //    ':lastname' => $data['lastname'],
        //    ':gender' => $data['gender'],
        //    ':age' => $data['age'],
        //    ':weight' => $data['weight'],
        // ]);
    }

    public static function find($id)
    {
        $pdo = Connection::make();
        $sql = 'SELECT * FROM users WHERE id = :id';
        $statement = $pdo->prepare($sql);
        $statement->bindParam(':id', $id);
        $statement->execute();

        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    public static function update($data)
    {
        $pdo = Connection::make();
        $sql = 'UPDATE users SET firstname = :firstname, lastname = :lastname, gender = :gender, age = :age, weight = :weight WHERE id = :id';
        $statement = $pdo->prepare($sql);
        $statement->bindParam(':id', $data['id']);
        $statement->bindParam(':firstname', $data['firstname']);
        $statement->bindParam(':lastname', $data['lastname']);
        $statement->bindParam(':gender', $data['gender']);
        $statement->bindParam(':age', $data['age']);
        $statement->bindParam(':weight', $data['weight']);

        return $statement->execute();
    }

    public static function delete($id)
    {
        $pdo = Connection::make();
        $sql = 'DELETE FROM users WHERE id = :id';
        $statement = $pdo->prepare($sql);
        $statement->bindParam(':id', $id);

        return $statement->execute();
    }
}
