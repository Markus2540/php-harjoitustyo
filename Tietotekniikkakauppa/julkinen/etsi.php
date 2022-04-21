<?php
$title = "Etsinnän tulos";
$description = "Etsinnän tulos.";
$this_page = "etsi.php";

$hakutermi = "";
//If the url of the page is faulty, the following error message will be shown.
$hakutermi_err = "Tämän sivun osoitteessa on puutteita. Ole hyvä ja syötä uusi hakusana.";

if (isset($_GET["hakusana"])) {
    if(empty(trim($_GET["hakusana"]))) {
        $hakutermi_err = "Hakutermi vaaditaan. Hakutermissä voit käyttää vain "
                . "numeroita, pieniä ja suuria suomen kielen aakkosia ja "
                . "erikoismerkeistä -miinusmerkkiä ja välilyöntiä.";
    } elseif (preg_match('%[^a-zåäöA-ZÅÄÖ0-9- ]%', trim($_GET["hakusana"]))) {
        $hakutermi_err = "Hakutermissä voit käyttää vain numeroita, pieniä ja "
                . "suuria suomen kielen aakkosia, miinusmerkkiä ja välilyöntiä.";
    } else {
        $hakutermi = trim($_GET["hakusana"]);
        $hakutermi_err = "";
        $this_page .= "?hakusana=" . $hakutermi;
    }
}

require_once '../perusosat/mainheader.php';
require_once '../moduulit/dbconnect.php';
require_once '../moduulit/pdoconnect.php';
require_once '../moduulit/ostoskoritoiminnot.php';



?>

<main>
    <?php
    if (empty($hakutermi_err)) {
        //MariaDB does not have REGEXP_LIKE.
        //$sql = "SELECT tuotenumero, tuotenimi, valmistaja, hinta, alennus, alealkaa, aleloppuu, kategoria, varastossa, myymalassa, kuvat FROM tuotteet WHERE "
        //        . "(vedettymyynnista IS NULL || vedettymyynnista > CURRENT_TIMESTAMP) && (REGEXP_LIKE(tuotenimi, '?') || REGEXP_LIKE(valmistaja, '?') || REGEXP_LIKE(kategoria, '?'))";
        $sql = "SELECT tuotenumero, tuotenimi, valmistaja, hinta, alennus, "
                . "alealkaa, aleloppuu, kategoria, varastossa, myymalassa, kuvat, "
                . "myyntialkaa, lyhytkuvaus FROM tuotteet WHERE(vedettymyynnista "
                . "IS NULL || (vedettymyynnista > CURRENT_TIMESTAMP)) && "
                . "(tuotenimi LIKE ? || valmistaja LIKE ? || kategoria LIKE ?)";
        $stmt = $link->prepare($sql);
        $stmt->bind_param('sss', $param_hakutermi, $param_hakutermi, $param_hakutermi);
        $param_hakutermi = "%" . $hakutermi . "%";
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($tuotenumero, $tuotenimi, $valmistaja, $hinta, 
                    $alennus, $alealkaa, $aleloppuu, $kategoria, $varastossa, 
                    $myymalassa, $kuvat, $myyntialkaa, $lyhytkuvaus);
            while ($stmt->fetch()) {  
                $kuvat = explode(",", $kuvat);
                if ($alennus > 0 && $alealkaa < date("Y-m-d H:i:s") 
                        && $aleloppuu > date("Y-m-d H:i:s")) {
                    $hinta *= (1 - ($alennus / 100));
                }
                ?>
                <div class="yksittainentuote">
                    <div class="pienituotekuva">
                        <a href="tuote.php?id=<?php echo puhdistus($tuotenumero); 
                        ?>"><img src="kuvat/<?php echo puhdistus($kuvat[0]); ?>" 
                               alt="Tuotteen <?php echo puhdistus($tuotenimi); 
                               ?> havaintokuva." height="180px"></a>
                    </div>
                    <div class="tuotetietoalue">
                        <a href="tuote.php?id=<?php echo puhdistus($tuotenumero); 
                        ?>"><h3><?php echo puhdistus($tuotenimi); ?></h3></a>
                        <p>Valmistaja: <?php echo puhdistus($valmistaja); ?></p>
                        <p><?php echo puhdistus($lyhytkuvaus); ?></p>
                    </div>
                    <div class="tilausalue">
                        <p>Hinta: <?php echo puhdistus($hinta); ?>€<br>
                            Myymälässä: <?php echo puhdistus($myymalassa); ?><br>
                            Varastossa: <?php echo puhdistus($varastossa); ?></p>
                        <?php if (date("Y-m-d H:i:s") > $myyntialkaa) {?>
                        <form action="<?php echo puhdistus($this_page); ?>" 
                              method="post" class="tilauskeskitettava">
                            <input type="hidden" name="tilattavatuotenumero" 
                                   value="<?php echo puhdistus($tuotenumero); ?>">
                            <label for="tilausmaara">Tilausmäärä:</label><br>
                                <input type="number" id="tilausmaara" 
                                       name="tilausmaara" min="1" max=
                                       "<?php echo puhdistus($varastossa); ?>"><br>
                                <input type="submit" value="Lisää ostoskoriin" 
                                       name="btnlisaakoriin">
                        </form>
                        <?php } else {?>
                        <p>Tuote ei ole vielä tilattavissa.</p>
                        <?php } ?>
                    </div>
                </div>
    
                <?php
            }
        } elseif ($stmt->num_rows == 0) {
            $hakutermi_err = "Hakutermillä ei löytynyt tuloksia. Hakutermissä "
                    . "voit käyttää vain numeroita, pieniä ja suuria suomen kielen "
                    . "aakkosia ja erikoismerkeistä -miinusmerkkiä ja välilyöntiä.";
            echo "<p>" . puhdistus($hakutermi_err) . "</p>";
        }
        $stmt->close();
    } else {
        echo "<p>" . puhdistus($hakutermi_err) . "</p>";
    }
    $link->close();
    ?>
    
    <div class="push"></div>
</main>

<?php
require_once '../perusosat/mainfooter.php';