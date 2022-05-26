<?php
$title = "Tuotteiden muokkaamissivu";
$description = "Tuotteiden muokkaamissivu";
require_once 'perusosat/headerandnav.php';
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