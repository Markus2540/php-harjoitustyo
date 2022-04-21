<?php
$title = "Template title";
$description = "Template description.";
$this_page = "tuotteet.php";
require_once '../classes/DataInputValidation.php';
$div = new DataInputValidation();

//If the url of the page is faulty, the following error message will be shown.
$tuotteetvirheilmoitus = "Tuotesivun osoitekenttä on vääränlainen. Ole hyvä ja lataa "
        . "sivu uudestaan seuraamalla navigointipalkissa olevaa linkkiä.";

if (isset($_GET["kategoria"])) {
    if(empty(trim($_GET["kategoria"]))) {
        $tuotteetvirheilmoitus = "Hakusana on tyhjä";
    } elseif ($div->just_text(trim($_GET["kategoria"]))) {
        $tuotteetvirheilmoitus = "Hakusanassa on laittomia merkkejä.";
    } else {
        $kategoria = (string) trim($_GET["kategoria"]);
        $tuotteetvirheilmoitus = "";
        $this_page .= "?kategoria=" . $kategoria;
    }
}

require_once '../perusosat/mainheader.php';
require_once '../moduulit/pdoconnect.php';
require_once '../moduulit/dbconnect.php';
require_once '../moduulit/ostoskoritoiminnot.php';

if (empty($tuotteetvirheilmoitus)) {
    $stmt = $pdo->prepare('SELECT tuotenumero, tuotenimi, valmistaja, hinta, '
            . 'alv, alennus, alealkaa, aleloppuu, lyhytkuvaus, varastossa, '
            . 'myymalassa, myyntialkaa, kuvat FROM tuotteet WHERE kategoria = '
            . ':kategoria AND (vedettymyynnista > CURRENT_TIMESTAMP OR '
            . 'vedettymyynnista IS NULL)');
    $stmt->bindValue('kategoria', $kategoria, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC); //Returns an array.
    $rowcount = count($result);
}
$stmt = null;
$pdo = null;
mysqli_close($link);

//HTML element ID must be unique.
$unique_id = 1;
?>

<main>
    <?php
    if (isset($rowcount) && $rowcount > 0) {
        foreach ($result as $rivi) {
            $kuvat = explode(",", $rivi["kuvat"]);?>
    <div class="yksittainentuote">
        <div class="pienituotekuva">
            <a href="tuote.php?id=<?php echo puhdistus($rivi["tuotenumero"]);?>">
                <img src="kuvat/<?php echo puhdistus($kuvat[0]);?>" alt="Tuotteen 
                    <?php echo puhdistus($rivi["tuotenimi"]);?> havaintokuva." 
                    height="180"></a>
        </div>
        <div class="tuotetietoalue">
            <a href="tuote.php?id=<?php echo puhdistus($rivi["tuotenumero"]);?>">
                <h3><?php echo puhdistus($rivi["tuotenimi"]);?></h3></a>
            <p>Valmistaja: <?php echo puhdistus($rivi["valmistaja"]);?></p>
            <p><?php echo puhdistus($rivi["lyhytkuvaus"]);?></p>
        </div>
        <div class="tilausalue">
            <p>Hinta: 
                <?php
                if ($rivi["alennus"] > 0 && date("Y-m-d H:i:s") > $rivi["alealkaa"] && 
                        date("Y-m-d H:i:s") < $rivi["aleloppuu"]) {
                    echo number_format(puhdistus($rivi["hinta"] * 
                            (1 - ($rivi["alennus"]) / 100)), '2', ',', ' ') . 
                            "€ Ale " . puhdistus($rivi["alennus"]) . "%";
                } else {
                    echo number_format(puhdistus($rivi["hinta"]), 2, ',', ' ') . "€";
                }
                ?>
                <br>
                Myymälässä: <?php echo puhdistus($rivi["myymalassa"]);?><br>
                Varastossa: <?php echo puhdistus($rivi["varastossa"]);?>
            </p>
            <?php
            if (date("Y-m-d H:i:s") > $rivi["myyntialkaa"]) {?>
            <form action="<?php echo puhdistus($this_page); ?>" method="post" 
                  class="tilauskeskitettava">
                <input type="hidden" name="tilattavatuotenumero" value=
                       "<?php echo puhdistus($rivi["tuotenumero"]);?>">
                
                <label for="tilausmaara<?php echo $unique_id; ?>">Tilausmäärä:</label><br>
                    <input type="number" id="tilausmaara
                        <?php echo $unique_id; $unique_id++; ?>" name="tilausmaara" 
                        min="1" max="<?php echo puhdistus($rivi["varastossa"]);?>"><br>
                    
                    <input type="submit" value="Lisää ostoskoriin" name="btnlisaakoriin">
            </form><?php
            } else {
                echo "<p>Tuote ei ole vielä tilattavissa.</p>";
            }
            ?>
        </div>
    </div>
    <?php
        }
    } elseif (isset($rowcount) && $rowcount === 0) {
        echo "<p>Tässä kategoriassa ei ole näytettäviä tuotteita.</p>";
    } else {
        echo "<p>" . puhdistus($tuotteetvirheilmoitus) . "</p>";
    }
    ?>
    
    <div class="push"></div>
</main>

<?php
require_once '../perusosat/mainfooter.php';