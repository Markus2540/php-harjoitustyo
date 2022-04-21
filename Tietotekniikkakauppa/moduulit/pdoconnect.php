<?php
try {
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false
    ];
    $pdo = new PDO("mysql:host=localhost;dbname=kauppatietokanta;charset=utf8mb4", 'root', '', $options);
    
    //foreach ($pdo->query('SELECT * FROM FOO') as $row) {
    //    print_r($row);
    //}
} catch (PDOException $e) {
    throw new PDOException($e->getMessage(), (int)$e->getCode());
}