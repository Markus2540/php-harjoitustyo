<?php
$title = "Tuotesivu";
$description = "Template description.";
$this_page = "tuote.php";

require_once '../perusosat/mainheader.php';
require_once '../moduulit/dbconnect.php';
require_once '../moduulit/pdoconnect.php';
require_once '../moduulit/ostoskoritoiminnot.php';

$haettavaid = "";
$haettavaid_err = "";

$id = puhdistus($_GET["id"]);
if(empty($id)) {
    $haettavaid_err = "ID vaaditaan";
    header("location: etusivu.php");
    exit;
} else if ($div->just_numbers($id)) {
    $haettavaid_err = "ID voi olla vain numero";
    header("location: etusivu.php");
    exit;
} else {
    $haettavaid = $id;
}

if(empty($haettavaid_err)) {
    $sql = "SELECT tuotenumero, tuotenimi, valmistaja, hinta, alv, alennus, "
            . "alealkaa, aleloppuu, lyhytkuvaus, tuotekuvaus, varastossa, "
            . "myymalassa, myyntialkaa, vedettymyynnista, kuvat FROM tuotteet "
            . "WHERE tuotenumero = ?";
    $stmt = $link->prepare($sql);
    $stmt->bind_param('i', $param_tuotenumero);
    $param_tuotenumero = $haettavaid;
    $stmt->execute();
    $stmt->store_result();
    
    if($stmt->num_rows == 1) {
        $stmt->bind_result($tuotenumero, $tuotenimi, $valmistaja, $hinta, $alv, 
                $alennus, $alealkaa, $aleloppuu, $lyhytkuvaus, $tuotekuvaus, 
                $varastossa, $myymalassa, $myyntialkaa, $vedettymyynnista, $kuvat);
        $stmt->fetch();
    } else {
        header("location: etusivu.php");
        exit;
    }
    $stmt->close();

    /*list($tuotenumero, $tuotenimi, $valmistaja, $hinta, $alv, $alennus, $alealkaa, 
            $aleloppuu, $lyhytkuvaus, $tuotekuvaus, $varastossa, $myymalassa, $myyntialkaa, 
            $vedettymyynnista, $kuvat) = mysqliFetchSingleProduct($haettavaid, $link);*/
}
$link->close();

//Converts string $kuvat into an array.
$kuvat = explode(",",$kuvat); 

?>

<main>
    <div class="productpagewrapper">
        <div class="productimagearea">
            <img src="kuvat/<?php echo $kuvat[1]; ?>" alt="Havaintokuva" id="isotuotekuva">
            <div class="littleimages">
                <p hidden id="passimages"><?php echo puhdistus(implode(",", $kuvat)); ?></p>
                <script defer src="javascript/imagegallery.js"></script>
            </div>
        </div>
        <div class="productinfoarea">
            <h1><?php echo puhdistus($tuotenimi); ?></h1>
            <p>Valmistaja: <?php echo puhdistus($valmistaja); ?></p>
            <p>Lähetettävissä: <?php echo puhdistus($varastossa); ?>. Myymälässä: <?php 
            echo puhdistus($myymalassa); ?>.</p>
            <?php
            if ($alennus > 0 && ($alealkaa < date("Y-m-d H:i:s")) && 
                    $aleloppuu > (date("Y-m-d H:i:s"))) {
                echo "<p>Hinta: " . number_format((puhdistus($hinta) * 
                        (1 - ($alennus/100))), 2, ',', ' ') . "€ (norm. " . 
                        number_format(puhdistus($hinta), 2, ',', ' ') . "€)</p>";
            } else {
                echo "<p>Hinta: " . number_format(puhdistus($hinta), 2, ',', ' ') . "€</p>";
            }
            ?>
            <?php
            if (isset($vedettymyynnista) && date("Y-m-d H:i:s") > $vedettymyynnista) {
                echo "<p>Tämä tuote on vedetty myynnistä!</p>";
            } elseif (date("Y-m-d H:i:s") < $myyntialkaa) {
                echo "<p>Tämä tuote ei ole vielä myynnissä.</p>";
            } else {
                ?>
                <form action="<?php echo puhdistus($this_page); ?>" method=
                      "post" class="tilauskeskitettava">
                    <input type="hidden" name="tilattavatuotenumero" value=
                           "<?php echo puhdistus($tuotenumero); ?>">
                    <label for="tilausmaara">Tilausmäärä:</label><br>
                    <input type="number" id="tilausmaara" name="tilausmaara" min=
                           "1" max="<?php echo puhdistus($varastossa); ?>"><br>
                    <input type="submit" value="Lisää ostoskoriin" name="btnlisaakoriin">
                </form>
            <?php
            }
            ?>
            <h3>Tuotekuvaus:</h3>
            <?php echo !empty($tuotekuvaus) ? nl2p(puhdistus($tuotekuvaus)) : nl2p(puhdistus($lyhytkuvaus)); ?>
        </div>
    </div>
    <div class="push"></div>
</main>

<?php
require_once '../perusosat/mainfooter.php';