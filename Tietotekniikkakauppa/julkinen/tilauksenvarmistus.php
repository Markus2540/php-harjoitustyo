<?php
$title = "Tilauksen läpikäynti";
$description = "Tällä sivulla käydään läpi tilauksen tiedot";
$this_page = "tilauksenvarmistus.php";

require_once '../perusosat/mainheader.php';
require_once '../moduulit/pdoconnect.php';
require_once '../moduulit/ostoskoritoiminnot.php';

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

if (!isset($_SESSION["ostoskorinid"])) {
    header("location: ostoskori.php");
    exit;
}
$total = 0;

if (isset($_POST["btnkirjautumatonkassalle"]) && !isset($_SESSION["loggedin"])) {
    $first_name = $last_name = $street_address = $postal_code = $post_office = "";
    $first_name_err = $last_name_err = $street_address_err = $postal_code_err = 
            $post_office_err = "";
    
    if (empty(trim($_POST["firstname"]))) {
        $first_name_err = "Etunimi vaaditaan.";
    } elseif ($div->validate_name(trim($_POST["firstname"]))) {
        $first_name_err = "Vain pienet ja suuret aakkoset, välilyönnit, heittomerkit "
                . "ja väliviivat hyväksytään.";
    } else {
        $first_name = trim($_POST["firstname"]);
    }
    
    if (empty(trim($_POST["lastname"]))) {
        $last_name_err = "Sukunimi vaaditaan.";
    } elseif ($div->validate_name(trim($_POST["lastname"]))) {
        $last_name_err = "Vain pienet ja suuret aakkoset, välilyönnit, heittomerkit "
                . "ja väliviivat hyväksytään.";
    } else {
        $last_name = trim($_POST["lastname"]);
    }
    
    if (empty(trim($_POST["streetaddress"]))) {
        $street_address_err = "Katuosoite vaaditaan.";
    } elseif ($div->validate_address(trim($_POST["streetaddress"]))) {
        $street_address_err = "Vain pienet ja suuret aakkoset, numerot, välilyönnit "
                . "ja väliviivat hyväksytään.";
    } else {
        $street_address = trim($_POST["streetaddress"]);
    }
    
    if (empty(trim($_POST["postalcode"]))) {
        $postal_code_err = "Postinumero vaaditaan.";
    } elseif ($div->just_numbers(trim($_POST["postalcode"])) || 
            strlen(trim($_POST["postalcode"])) !== 5) {
        $postal_code_err = "Postinumerossa voi olla vain numeroita ja pituuden "
                . "pitää olla 5 numeroa.";
    } else {
        $postal_code = trim($_POST["postalcode"]);
    }
    
    if (empty(trim($_POST["postoffice"]))) {
        $post_office_err = "Postitoimipaikka vaaditaan.";
    } elseif ($div->validate_post_office(trim($_POST["postoffice"]))) {
        $post_office_err = "Vain pienet ja suuret aakkoset, välilyönnit ja väliviivat "
                . "sallitaan.";
    } else {
        $post_office = trim($_POST["postoffice"]);
    }
    
    if (empty($first_name_err) && empty($last_name_err) && empty($street_address_err) && 
            empty($postal_code_err) && empty($post_office_err)) {
        $_SESSION["etunimi"] = $first_name;
        $_SESSION["sukunimi"] = $last_name;
        $_SESSION["lahiosoite"] = $street_address;
        $_SESSION["postinumero"] = $postal_code;
        $_SESSION["postitoimipaikka"] = $post_office;
        $_SESSION["reittivarmistus"] = true;
        
        header("location: tilauksenhyvaksyminen.php");
        exit;
    }
}
//Sessiomuuttujan reittivarmistus avulla haluan varmistaa, että asiakas menee tilauksen 
//hyväksymissivulle tämän sivun kautta.


if (isset($_POST["btnkirjautunutkassalle"]) && $_SESSION["loggedin"] === true) {
    $_SESSION["reittivarmistus"] = true;
    header("location: tilauksenhyvaksyminen.php");
    exit;
}
?>

<main>
    <h1>Tietojen varmistus</h1>
    
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
                if ($rivi["alennus"] > 0 && date("Y-m-d H:i:s") > $rivi["alealkaa"] && 
                        date("Y-m-d H:i:s") < $rivi["aleloppuu"]) {
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
    
    <?php
    /*
     * Tässä osiossa luodaan asiakkaalle osoitetietokenttä riippuen siitä onko 
     * henkilö kirjautunut sisään vai ei. Kirjautuneen käyttäjän kohdalla 
     * hyödynnetään tallennettuja tietoja ja kirjautumattomalle käyttäjälle 
     * annetaan täytettävä lomake, joka tarkistetaan tällä sivulla olevan toiminnon 
     * kautta ennen tilauksen hyväksymissivustolle päästämistä.
     */
    if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {?>
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
    
    <p>Tarvittaessa voit käydä muuttamassa osoitetiedot 
        <a href="osoitetietojenmuuttaminen.php">Osoitetietojen muuttaminen</a>-sivulta</p>
    
    <hr class="erottaja">
    
    <!--<h2>Kaikki kunnossa? <a href="tilauksenhyvaksyminen.php">Jatka hyväksymään tilaus</a></h2>-->
    <h2>Kaikki kunnossa?</h2>
    <form action="<?php echo puhdistus($this_page); ?>" method="post">
        <input type="submit" value="Jatka hyväksymään tilaus" name="btnkirjautunutkassalle">
    </form>
    
    <?php } else { ?>
    <hr class="erottaja">
    
    <h2>Toimitustiedot</h2>
    
    <p>Ole hyvä ja syötä toimitusta varten vaadittavat tiedot.</p>
    
    <form action="<?php echo puhdistus($this_page); ?>" method="post">
        <table class="spacebelow">
            <tr>
                <td>Etunimi:</td>
                <td><input type="text" name="firstname" required value=
                           "<?php if (isset($first_name)) {echo puhdistus($first_name);} ?>"></td>
                <td><?php if (isset($first_name_err)) {echo puhdistus($first_name_err);} ?></td>
            </tr>
            <tr>
                <td>Sukunimi:</td>
                <td><input type="text" name="lastname" required value=
                           "<?php if (isset($last_name)) {echo puhdistus($last_name);} ?>"></td>
                <td><?php if (isset($last_name_err)) {echo puhdistus($last_name_err);} ?></td>
            </tr>
            <tr>
                <td>Lähiosoite:</td>
                <td><input type="text" name="streetaddress" required value=
                           "<?php if (isset($street_address)) {echo puhdistus($street_address);} ?>"></td>
                <td><?php if (isset($street_address_err)) {echo puhdistus($street_address_err);} ?></td>
            </tr>
            <tr>
                <td>Postinumero:</td>
                <td><input type="text" name="postalcode" required minlength="5" 
                           maxlength="5" value="<?php if (isset($postal_code)) {
                               echo puhdistus($postal_code);} ?>"></td>
                <td><?php if (isset($postal_code_err)) {
                    echo puhdistus($postal_code_err);} ?></td>
            </tr>
            <tr>
                <td>Postitoimipaikka:</td>
                <td><input type="text" name="postoffice" required value=
                           "<?php if (isset($post_office)) {echo puhdistus($post_office);} ?>"></td>
                <td><?php if (isset($post_office_err)) {echo puhdistus($post_office_err);} ?></td>
            </tr>
        </table>
        <input type="submit" value="Jatka kassalle" name="btnkirjautumatonkassalle">
    </form>
    <?php }
    ?>

    
    <div class="push"></div>
</main>

<?php
require_once '../perusosat/mainfooter.php';