<?php
$title = "Tilauksen hyväksyminen";
$description = "Tilauksen hyväksyminen.";
$this_page = "tilauksenhyvaksyminen.php";

require_once '../perusosat/mainheader.php';
require_once '../moduulit/pdoconnect.php';

if (!isset($_SESSION["reittivarmistus"]) || $_SESSION["reittivarmistus"] === false) {
    header("location: ostoskori.php");
    exit;
} /*elseif (time() - $_SESSION["reittivarmistus"] > 60) {
    header("location: tilauksenvarmistus.php");
    exit;
}*/

if (isset($_SESSION["ostoskorinid"])) {
    $sql = "SELECT tuotteet.tuotenumero, tuotteet.valmistaja, tuotteet.tuotenimi, "
            . "tuotteet.hinta, tuotteet.alennus, tuotteet.alealkaa, tuotteet.aleloppuu, "
            . "ostoskorinesine.maara, ostoskorinesine.id FROM ostoskorinesine "
            . "INNER JOIN tuotteet ON ostoskorinesine.tuotenumero = tuotteet.tuotenumero "
            . "WHERE ostoskorinesine.ostoskorinid = :ostoskorinid";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue('ostoskorinid', $_SESSION["ostoskorinid"], PDO::PARAM_INT);
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (count($products) === 0) {
        header("location: ostoskori.php");
        exit;
    }
}

$total = 0;


?>

<main>
    <h1>Tilauksen yhteenveto</h1>
    
    <h3>Tilauksen tuotteet:</h3>
    
    <div class="shoppingcartdiv">
    <table class="shoppingcarttable">
        <tr>
            <th>Valmistaja</th>
            <th>Tuotenimi</th>
            <th>Määrä</th>
            <th>Tuotteen kokonaishinta</th>
        </tr>
        <?php
            foreach ($products as $rivi) {?>
        <tr>
            <td><?php echo puhdistus($rivi["valmistaja"]); ?></td>
            <td><?php echo puhdistus($rivi["tuotenimi"]); ?></td>
            <td><?php echo puhdistus($rivi["maara"]); ?></td>
            <td>
                <?php
                if ($rivi["alennus"] > 0 && date("Y-m-d H:i:s") > $rivi["alealkaa"] 
                        && date("Y-m-d H:i:s") < $rivi["aleloppuu"]) {
                    $rivi["hinta"] *= (1 - ($rivi["alennus"] / 100));
                    echo number_format(puhdistus($rivi["hinta"] * 
                            $rivi["maara"]), 2, ',', ' ') . "€";
                    $total += ($rivi["hinta"] * $rivi["maara"]);
                } else {
                    echo number_format(puhdistus($rivi["hinta"] * 
                            $rivi["maara"]), 2, ',', ' ') . "€";
                    $total += ($rivi["hinta"] * $rivi["maara"]);
                }
                ?>
            </td>
        </tr>
            <?php }
        ?>
        <tr>
            <th>Tilaus yhteensä:</th>
            <th colspan="2"></th>
            <th><?php echo number_format(puhdistus($total), 2, ',', ' ') . "€"; ?></th>
        </tr>
    </table>
    </div>
    
    <hr class="erottaja">
    
    <h3>Osoitetiedot:</h3>
    <table class="perustaulukko">
        <tr>
            <td>Etunimi:</td>
            <td><?php echo puhdistus($_SESSION["etunimi"]); ?></td>
        </tr>
        <tr>
            <td>Sukunimi:</td>
            <td><?php echo puhdistus($_SESSION["sukunimi"]); ?></td>
        </tr>
        <tr>
            <td>Lähiosoite:</td>
            <td><?php echo puhdistus($_SESSION["lahiosoite"]); ?></td>
        </tr>
        <tr>
            <td>Postinumero:</td>
            <td><?php echo puhdistus($_SESSION["postinumero"]); ?></td>
        </tr>
        <tr>
            <td>Postitoimipaikka:</td>
            <td><?php echo puhdistus($_SESSION["postitoimipaikka"]); ?></td>
        </tr>
    </table>
    
    <hr class="erottaja">
    
    <div class="push"></div>
</main>

<?php
require_once '../perusosat/mainfooter.php';
$_SESSION["reittivarmistus"] = false;