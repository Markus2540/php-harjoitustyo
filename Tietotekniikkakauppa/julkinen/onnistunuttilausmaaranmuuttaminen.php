<?php
$title = "Ilmoitus onnistuneesta tilausmäärän muuttamisesta";
$description = "Tällä sivulla ilmoitetaan onnistuneesta tilausmäärän muuttamisesta.";
$this_page = "onnistunuttilausmaaranmuuttaminen.php";

require_once '../perusosat/mainheader.php';
require_once '../moduulit/dbconnect.php';

if(!isset($_SESSION["viimeinenlisattytuote"])) {
    header("location: etusivu.php");
    exit;
}

$sql = "SELECT tuotenimi, valmistaja, hinta, alv, alennus, alealkaa, aleloppuu "
        . "FROM tuotteet WHERE tuotenumero = ?";

if ($stmt = $link->prepare($sql)) {
    $stmt->bind_param("i", $param_tuotenumero);
    
    $param_tuotenumero = $_SESSION["viimeinenlisattytuote"];
    
    if ($stmt->execute()) {
        $stmt->bind_result($tuotenimi, $valmistaja, $hinta, $alv, $alennus, 
                $alealkaa, $aleloppuu);
        $stmt->fetch();
    }
    
    $stmt->close();
}
$link->close();
?>

<main>
    <h1>Ilmoitus onnistuneesta tilausmäärän muuttamisesta.</h1>
    
    <p>Valmistajan <?php echo puhdistus($valmistaja); ?> tuotten <?php 
    echo puhdistus($tuotenimi); ?> tilausmäärää on muutettu. Uusi tilausmäärä on <?php 
    echo puhdistus($_SESSION["kappaletta"]); ?>, kappalehinta on 
        <?php
        if ($alennus > 0 && $alealkaa < date("Y-m-d H:i:s") && $aleloppuu > date("Y-m-d H:i:s")) {
            $hinta *= (1 * (1 - ($alennus / 100)));
            echo number_format(puhdistus($hinta), 2, ',', ' ');
        } else {
            echo number_format(puhdistus($hinta), 2, ',', ' ');
        }
        ?>€, jolloin kokonaishinnaksi tulee <?php 
        echo number_format(puhdistus($_SESSION["kappaletta"] * $hinta), 2, ',', ' '); 
        ?>€. Tuotteen alv on <?php echo puhdistus($alv); ?>%.</p>
    
    <p><a href="ostoskori.php">Tarkastele ostoskoria</a></p>
    
    <div class="push"></div>
</main>

<?php
require_once '../perusosat/mainfooter.php';