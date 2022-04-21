<?php
$title = "Ilmoitus onnistuneesta tuotteen lisäämisestä ostoskoriin";
$description = "Ilmoitus onnistuneesta tuotteen lisäämisestä ostoskoriin.";
$this_page = "onnistunutlisaaminen.php";

require_once '../perusosat/mainheader.php';
require_once '../moduulit/dbconnect.php';

if(!isset($_SESSION["viimeinenlisattytuote"])) {
    header("location: etusivu.php");
    exit;
}

/*
$sql = "SELECT tuotenimi, valmistaja, hinta, alv FROM tuotteet WHERE tuotenumero = ?";
if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $param_tuotenumero);
    $param_tuotenumero = $_SESSION["viimeinenlisattytuote"];
    
    if (mysqli_stmt_execute($stmt)) { 
        mysqli_stmt_store_result($stmt);
        if (mysqli_stmt_num_rows($stmt) == 1) {
            mysqli_stmt_bind_result($stmt, $tuotenimi, $valmistaja, $hinta, $alv);
            
            mysqli_stmt_fetch($stmt);
        }
    }
    
    mysqli_stmt_close($stmt);
}
mysqli_close($link);*/



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

/*
$stmt = $link->prepare($sql);

$stmt->bind_param("i", $param_tuotenumero);

$param_tuotenumero = $_SESSION["viimeinenlisattytuote"];

$stmt->execute();

$stmt->bind_result($tuotenimi, $valmistaja, $hinta, $alv);

$stmt->fetch();

$stmt->close();

$link->close();*/
?>

<main>
    <h1>Tuote lisätty onnistuneesti ostoskoriin!</h1>
    
    <p>Valmistajan <?php echo puhdistus($valmistaja); ?> tuote <?php 
    echo puhdistus($tuotenimi); ?> lisätty ostoskoriin. Lisätty määrä <?php 
    echo puhdistus($_SESSION["kappaletta"]); ?>, kappalehinta on 
        <?php
        if ($alennus > 0 && $alealkaa < date("Y-m-d H:i:s") && $aleloppuu > date("Y-m-d H:i:s")) {
            $hinta *= (1 * (1 - ($alennus / 100)));
            echo number_format(puhdistus($hinta), 2, ',', ' ');
        } else {
            echo number_format(puhdistus($hinta), 2, ',', ' ');
        }
        ?>€, jolloin kokonaishinnaksi tulee 
            <?php echo number_format(puhdistus($_SESSION["kappaletta"] * $hinta), 2, ',', ' '); 
            ?>€. Tuotteen alv on <?php echo puhdistus($alv); ?>%.</p>
    
    <p><a href="ostoskori.php">Tarkastele ostoskoria</a></p>
    <div class="push"></div>
</main>

<?php
require_once '../perusosat/mainfooter.php';