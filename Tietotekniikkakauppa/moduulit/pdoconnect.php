<?php
try {
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false
    ];
    $pdo = new PDO("mysql:host=localhost;dbname=kauppatietokanta;charset=utf8mb4", 'root', '', $options);
    
    //Used for testing
    //foreach ($pdo->query('SELECT * FROM FOO') as $row) {
    //    print_r($row);
    //}
} catch (PDOException $e) {
    /*$file = __DIR__."/pdoerrors.txt";
    $message = "\n" . $e->getMessage() . " in file " . $e->getFile() . " on line " . $e->getLine();
    file_put_contents($file, $message, FILE_APPEND);*/
    throw new PDOException($e->getMessage(), (int)$e->getLine());
}