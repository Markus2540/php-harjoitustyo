<?php
session_start();
if (!isset($_SESSION["adminloggedin"]) || $_SESSION["adminloggedin"] !== true) {
    header("location: ../julkinen/etusivu.php");
    exit;
}
$this_page = "tuotteidenmuokkaaminen.php";
require_once '../moduulit/perustoimintoja.php';
require_once '../moduulit/pdoconnect.php';
require_once '../classes/DataInputValidation.php';

$div = new DataInputValidation();

$hakutermi = "";
$hakutermi_err = "";
if (isset($_POST["btnetsimuokattava"])) {
    if (empty(trim($_POST["hakusana"]))) {
        $hakutermi_err = "Hakusana puuttuu.";
    } elseif (preg_match('%[^a-zåäöA-ZÅÄÖ0-9 ]%', trim($_POST["hakusana"]))) {
        $hakutermi_err = "Hakusanassa voi käyttää vain kirjaimia, numeroita ja välilyöntejä.";
    } else {
        $hakutermi = $_POST["hakusana"];
    }
    
    if (empty($hakutermi_err)) {
        $sql = "SELECT tuotenumero, tuotenimi, valmistaja, kategoria FROM tuotteet "
                . "WHERE tuotenimi LIKE :tuotenimi || valmistaja LIKE :valmistaja";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue('tuotenimi', "%" . $hakutermi . "%", PDO::PARAM_STR);
        $stmt->bindValue('valmistaja', "%" . $hakutermi . "%", PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
$stmt = null;
$pdo = null;
?>
<!DOCTYPE html>
<html lang="fi">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="author" content="Markus2540">
        <title>Tuotteiden muokkaamissivu</title>
        <meta name="description" content="Tuotteiden muokkaamissivu">
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
                <h1>Etsi muokattava tuote</h1>
                <p>Etsi muokattava tuote tuotenimen tai valmistajan perusteella. 
                    Käytä vain kirjaimia, numeroita ja välilyöntejä.</p>
                
                <form action="<?php echo puhdistus($this_page); ?>" method="post">
                    <input type="text" name="hakusana">
                    <input type="submit" value="Etsi" name="btnetsimuokattava">
                </form>
                <?php if (isset($hakutermi_err)){echo "<p>" . $hakutermi_err . "</p>";} ?>
                <?php
                if (isset($result)) {
                    if ($result) {
                        ?>
                <table class="perustaulukko">
                    <tr>
                        <th>Valmistaja</th>
                        <th>Tuotenimi</th>
                        <th>Kategoria</th>
                    </tr>
                <?php
                        foreach ($result as $rivi) {
                            ?>
                    <tr>
                        <td><?php echo puhdistus($rivi["valmistaja"]); ?></td>
                        <td><a href="muokkaatietoja.php?id=<?php 
                        echo puhdistus($rivi["tuotenumero"]); ?>"><?php 
                        echo puhdistus($rivi["tuotenimi"]); ?></a></td>
                        <td><?php echo puhdistus($rivi["kategoria"]); ?></td>
                    </tr>
                    <?php
                        }
                        ?>
                </table>
                <?php
                    } else {
                        echo "<p>Ei hakutuloksia.</p>";
                    }
                }
                ?>
            </main>
        </div>
    </body>
</html>