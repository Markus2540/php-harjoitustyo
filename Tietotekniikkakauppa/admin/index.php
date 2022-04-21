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
        <title>Hallintatoimintojen etusivu</title>
        <meta name="description" content="Hallintatoimintojen etusivu">
        <link rel="stylesheet" href="css/admin.css">
        <meta http-equiv="Content-Security-Policy" content="default-src 'self';">
    </head>
    <body>
        <div class="wrapper">
            <nav>
                <h1>Navigointi</h1>
                <?php require_once 'perusosat/navigaatio.php'; ?>
            </nav>
            <main>
                <h1>Pääsisältö</h1>
            </main>
        </div>
    </body>
</html>