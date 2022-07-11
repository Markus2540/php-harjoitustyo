<?php
$title = "Etsinnän tulos";
$description = "Etsinnän tulos.";
$this_page = "etsi.php";

$hakutermi = "";
//If the url of the page is faulty, the following error message will be shown.
$hakutermi_err = "Tämän sivun osoitteessa on puutteita. Ole hyvä ja syötä uusi hakusana.";
$tuotteet = [];

require_once '../perusosat/mainheader.php';
require_once '../moduulit/ostoskoritoiminnot.php';

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
        $pdo_statements->search_from_products($hakutermi);
    }
}

?>

<main>
    <?php
    /*
     * If the search term does not contain errors, build division element for 
     * each product. If the product is on discount, modify price. If the search 
     * term contains errors, display error.
     */
    if (empty($hakutermi_err)) {
        foreach ($tuotteet as &$tuote) {
            $tuote['kuvat'] = explode(",", $tuote['kuvat']);

            if ($tuote['alennus'] > 0 && $tuote['alealkaa'] < date("Y-m-d H:i:s") 
                    && $tuote['aleloppuu'] > date("Y-m-d H:i:s")) {
                $tuote['hinta'] *= (1 - ($tuote['alennus'] / 100));
                $tuote['hinta'] = number_format($tuote['hinta'], 2, ',', ' ');
            }
            
            ?>
            <div class="yksittainentuote">
                <div class="pienituotekuva">
                    <a href="tuote.php?id=<?php echo puhdistus($tuote['tuotenumero']); 
                    ?>"><img src="kuvat/<?php echo puhdistus($tuote['kuvat'][0]); ?>" 
                           alt="Tuotteen <?php echo puhdistus($tuote['tuotenimi']); 
                           ?> havaintokuva." height="180px"></a>
                </div>
                <div class="tuotetietoalue">
                    <a href="tuote.php?id=<?php echo puhdistus($tuote['tuotenumero']); 
                    ?>"><h3><?php echo puhdistus($tuote['tuotenimi']); ?></h3></a>
                    <p>Valmistaja: <?php echo puhdistus($tuote['valmistaja']); ?></p>
                    <p><?php echo puhdistus($tuote['lyhytkuvaus']); ?></p>
                </div>
                <div class="tilausalue">
                    <p>Hinta: <?php echo puhdistus($tuote['hinta']); ?>€<br>
                        Myymälässä: <?php echo puhdistus($tuote['myymalassa']); ?><br>
                        Varastossa: <?php echo puhdistus($tuote['varastossa']); ?></p>
                    <?php if (date("Y-m-d H:i:s") > $tuote['myyntialkaa']) {?>
                    <form action="<?php echo puhdistus($this_page); ?>" 
                          method="post" class="tilauskeskitettava">
                        <input type="hidden" name="tilattavatuotenumero" 
                               value="<?php echo puhdistus($tuote['tuotenumero']); ?>">
                        <label for="tilausmaara">Tilausmäärä:</label><br>
                            <input type="number" id="tilausmaara" 
                                   name="tilausmaara" min="1" max=
                                   "<?php echo puhdistus($tuote['varastossa']); ?>"><br>
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
        unset($tuote);
    } else {
        echo "<p>" . puhdistus($hakutermi_err) . "</p>";
    }
    ?>
    
    <div class="push"></div>
</main>

<?php
require_once '../perusosat/mainfooter.php';