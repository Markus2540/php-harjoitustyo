<?php
session_start();
if (!isset($_SESSION["adminloggedin"]) || $_SESSION["adminloggedin"] !== true) {
    header("location: ../julkinen/etusivu.php");
    exit;
}
$this_page = "index.php";
require_once '../moduulit/perustoimintoja.php';
?>
<!DOCTYPE html>
<html lang="fi">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="author" content="Markus2540">
        <title><?php echo puhdistus($title); ?></title>
        <meta name="description" content="<?php echo puhdistus($description); ?>">
        <link rel="stylesheet" href="css/admin.css">
        <meta http-equiv="Content-Security-Policy" content="default-src 'self';">
    </head>
    <body>
    <div class="wrapper">
        <nav>
            <h1>Navigointi</h1>
            <ul>
                <li><a href="index.php">Etusivu</a></li>
                <li><a href="tuotteidenlisaaminen.php">Tuotteiden lisääminen</a></li>
                <li><a href="tuotteidenmuokkaaminen.php">Tuotteiden muokkaaminen</a></li>
                <li>-</li>
                <li>-</li>
                <li>-</li>
                <li>-</li>
                <li>-</li>
                <li><a href="../julkinen/logout.php">Kirjaudu ulos</a></li>
            </ul>
        </nav>