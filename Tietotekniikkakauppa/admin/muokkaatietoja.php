<?php
session_start();
if (!isset($_SESSION["adminloggedin"]) || $_SESSION["adminloggedin"] !== true) {
    header("location: ../julkinen/etusivu.php");
    exit;
}
require_once '../moduulit/perustoimintoja.php';
require_once '../moduulit/pdoconnect.php';
require_once '../classes/DataInputValidation.php';
$this_page = "muokkaatietoja.php";

$div = new DataInputValidation();

$kategoriat = ["Esikasatut", "Kotelot", "Prosessorit", "Emolevyt", "Muistit", 
    "Naytonohjaimet", "Kiintolevyt", "Virtalahteet", "Asemat", "Jaahdytys", 
    "Naytot", "Tabletit", "Puhelimet ja tarvikkeet"];

$tuotenumero = "";
//If the url of the page is faulty, the following error message will be shown.
$tuotenumero_err = "Osoitekentästä puuttuu muokattavan tuotteen ilmaiseva osa, "
        . "etsi tuote uudestaan.";

$uusituotenimi = $uusivalmistaja = $uusihinta = $uusialv = $uusialennus = 
        $uusialealkaa = $uusialeloppuu = $uusikategoria = $uusilyhytkuvaus = 
        $uusituotekuvaus = $uusivarastossa = $uusimyymalassa = 
        $uusimyyntialkaa = $uusivedettymyynnista = $uusikuvat = "";

$uusituotenimi_err = $uusivalmistaja_err = $uusihinta_err = $uusialv_err = 
        $uusialennus_err = $uusialealkaa_err = $uusialeloppuu_err = 
        $uusikategoria_err = $uusilyhytkuvaus_err = $uusituotekuvaus_err = 
        $uusivarastossa_err = $uusimyymalassa_err = $uusimyyntialkaa_err = 
        $uusivedettymyynnista_err = $uusikuvat_err = "";
/*
 * $_GET["id"] == $_SESSION["tuotenumero"] is used for protection against form 
 * action modification.
 */
if (isset($_POST["btntallennamuutokset"]) && $_GET["id"] == $_SESSION["tuotenumero"]) {
    if (empty(trim($_POST["uusituotteennimi"]))) {
        $uusituotenimi_err = "Tuotteen nimi vaaditaan";
    } elseif ($div->product_name(trim($_POST["uusituotteennimi"]))) {
        $uusituotenimi_err = "Tuotteen nimessä voi olla vain a-zåäöA-ZÅÄÖ0-9 ,.@- merkit";
    } else {
        $uusituotenimi = trim($_POST["uusituotteennimi"]);
    }
    
    if (empty(trim($_POST["uusivalmistaja"]))) {
        $uusivalmistaja_err = "Tuotteen valmistaja vaaditaan";
    } elseif ($div->product_name(trim($_POST["uusivalmistaja"]))) {
        $uusivalmistaja_err = "Valmistajan nimessä voi olla vain a-zåäöA-ZÅÄÖ0-9 ,.@- merkit";
    } else {
        $uusivalmistaja = trim($_POST["uusivalmistaja"]);
    }
    
    if (empty(trim($_POST["uusihinta"]))) {
        $uusihinta_err = "Hinta vaaditaan.";
    } elseif ($div->validate_money(trim($_POST["uusihinta"]))) {
        $uusihinta_err = "Hinnassa voidaan käyttää vain numeroita ja "
                . "desimaalierottimena pistettä. Pisteitä voi olla maksimissaan vain yksi.";
    } else {
        $uusihinta = trim($_POST["uusihinta"]);
    }
    
    if (empty(trim($_POST["uusialv"]))) {
        $uusialv_err = "Arvolisäveroprosentti vaaditaan.";
    } elseif ($div->just_numbers(trim($_POST["uusialv"]))) {
        $uusialv_err = "Arvolisäveroprosentissa voi käyttää vain numeroita.";
    } else {
        $uusialv = trim($_POST["uusialv"]);
    }
    
    if ($div->validate_money(trim($_POST["uusialennus"]))) {
        $uusialennus_err = "Alennusprosentissa voi käyttää vain numeroita ja "
                . "pistettä desimaalierottimena. Sadasosan tarkkuus.";
    } else {
        $uusialennus = trim($_POST["uusialennus"]);
    }
    
    if (empty(trim($_POST["uusialealkaa"]))) {
        $uusialealkaa = NULL;
    } elseif ($div->validate_datetime_local(trim($_POST["uusialealkaa"]))) {
        $uusialealkaa_err = "Syötetty päivämäärä tai aika ei kelpaa.";
    } else {
        $uusialealkaa = trim($_POST["uusialealkaa"]);
    }
    
    if (empty(trim($_POST["uusialeloppuu"]))) {
        $uusialeloppuu = NULL;
    } elseif ($div->validate_datetime_local(trim($_POST["uusialeloppuu"]))) {
        $uusialeloppuu_err = "Syötetty päivämäärä tai aika ei kelpaa.";
    } else {
        $uusialeloppuu = trim($_POST["uusialeloppuu"]);
    }
    
    if (empty(trim($_POST["uusikategoria"]))) {
        $uusikategoria_err = "Kategoria vaaditaan.";
    } elseif ($div->validate_category(trim($_POST["uusikategoria"]))) {
        $uusikategoria_err = "Kategoriassa sallitaan vain numeroita, kirjaimia ja välilyöntejä.";
    } elseif (!in_array(trim($_POST["uusikategoria"]), $kategoriat)) {
        $uusikategoria_err = "Kategoria voi olla vain ennalta määritetty kategoria.";
    } else {
        $uusikategoria = trim($_POST["uusikategoria"]);
    }
    
    if (empty(trim($_POST["uusilyhytkuvaus"]))) {
        $uusilyhytkuvaus = NULL;
    } elseif ($div->validate_description(trim($_POST["uusilyhytkuvaus"]))) {
        $uusilyhytkuvaus_err = "Vain a-zåäöA-ZÅÄÖ0-9 -,.:® merkit sallitaan "
                . "lyhyessä kuvauksessa.";
    } elseif (mb_strlen(trim($_POST["uusilyhytkuvaus"])) > 400) {
        $uusilyhytkuvaus_err = "Pidä lyhyt kuvaus maksimissaan 400 merkkiä pitkänä. "
                . "Annettu kuvaus oli " . mb_strlen(trim($_POST["uusilyhytkuvaus"])) . 
                " merkkiä pitkä.";
    } else {
        $uusilyhytkuvaus = trim($_POST["uusilyhytkuvaus"]);
    }
    
    if (empty(trim($_POST["uusituotekuvaus"]))) {
        $uusituotekuvaus = NULL;
    } elseif ($div->validate_description(trim($_POST["uusituotekuvaus"]))) {
        $uusituotekuvaus_err = "Vain a-zåäöA-ZÅÄÖ0-9 -,.:® merkit sallitaan tuotekuvauksessa.";
    } else {
        $uusituotekuvaus = trim($_POST["uusituotekuvaus"]);
    }
    
    if (empty(trim($_POST["uusivarastossa"]))) {
        $uusivarastossa_err = "Varastotilanne vaaditaan";
    } elseif ($div->just_numbers(trim($_POST["uusivarastossa"]))) {
        $uusivarastossa_err = "Vain kokonaisluku sallitaan";
    } else {
        $uusivarastossa = trim($_POST["uusivarastossa"]);
    }
    
    if (empty(trim($_POST["uusimyymalassa"]))) {
        $uusimyymalassa_err = "Myymälätilanne vaaditaan";
    } elseif ($div->just_numbers(trim($_POST["uusimyymalassa"]))) {
        $uusimyymalassa_err = "Vain kokonaisluku sallitaan";
    } else {
        $uusimyymalassa = trim($_POST["uusimyymalassa"]);
    }
    
    if (empty(trim($_POST["uusimyyntialkaa"]))) {
        $uusimyyntialkaa = NULL;
    } elseif ($div->validate_datetime_local(trim($_POST["uusimyyntialkaa"]))) {
        $uusimyyntialkaa_err = "Syötetty päivämäärä tai aika ei kelpaa.";
    } else {
        $uusimyyntialkaa = trim($_POST["uusimyyntialkaa"]);
    }
    
    if (empty(trim($_POST["uusivedettymyynnista"]))) {
        $uusivedettymyynnista = NULL;
    } elseif ($div->validate_datetime_local(trim($_POST["uusivedettymyynnista"]))) {
        $uusivedettymyynnista_err = "Syötetty päivämäärä tai aika ei kelpaa.";
    } else {
        $uusivedettymyynnista = trim($_POST["uusivedettymyynnista"]);
    }
    
    if (empty(trim($_POST["uusikuvat"]))) {
        $uusikuvat_err = "Tuotteen kuvat vaaditaan. Jos tuotteesta ei ole kuvia, "
                . "laita tähän kenttään teksti -> eikuvaapieni.png,eikuvaa.png";
    } elseif ($div->validate_picture_string(trim($_POST["uusikuvat"]))) {
        $uusikuvat_err = "Tässä kentässä voi olla vain pieniä ja suuria aakkosia "
                . "(ei åäö), numeroita, pisteitä ja pilkkuja.";
    } else {
        $uusikuvat = trim($_POST["uusikuvat"]);
    }
    
    /*
     * In the future something like this could/should be replaced with a single 
     * error variable.
     */
    if (empty($uusituotenimi_err) && empty($uusivalmistaja_err) && 
            empty($uusihinta_err) && empty($uusialv_err) && empty($uusialennus_err) 
            && empty($uusialealkaa_err) && empty($uusialeloppuu_err) && 
            empty($uusikategoria_err) && empty($uusilyhytkuvaus_err) && 
            empty($uusituotekuvaus_err) && empty($uusivarastossa_err) && 
            empty($uusimyymalassa_err) && empty($uusimyyntialkaa_err) && 
            empty($uusivedettymyynnista_err) && empty($uusikuvat_err)) {
        $sql = "UPDATE tuotteet SET tuotenimi = :tuotenimi, valmistaja = "
                . ":valmistaja, hinta = :hinta, alv = :alv, alennus = :alennus, "
                . "alealkaa = :alealkaa, aleloppuu = :aleloppuu, kategoria = "
                . ":kategoria, lyhytkuvaus = :lyhytkuvaus, tuotekuvaus = "
                . ":tuotekuvaus, varastossa = :varastossa, myymalassa = "
                . ":myymalassa, myyntialkaa = :myyntialkaa, vedettymyynnista = "
                . ":vedettymyynnista, kuvat = :kuvat WHERE tuotenumero = :tuotenumero";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue('tuotenimi', $uusituotenimi, PDO::PARAM_STR);
        $stmt->bindValue('valmistaja', $uusivalmistaja, PDO::PARAM_STR);
        $stmt->bindValue('hinta', $uusihinta, PDO::PARAM_STR);
        $stmt->bindValue('alv', $uusialv, PDO::PARAM_INT);
        $stmt->bindValue('alennus', $uusialennus, PDO::PARAM_STR);
        $stmt->bindValue('alealkaa', $uusialealkaa, PDO::PARAM_STR);
        $stmt->bindValue('aleloppuu', $uusialeloppuu, PDO::PARAM_STR);
        $stmt->bindValue('kategoria', $uusikategoria, PDO::PARAM_STR);
        $stmt->bindValue('lyhytkuvaus', $uusilyhytkuvaus, PDO::PARAM_STR);
        $stmt->bindValue('tuotekuvaus', $uusituotekuvaus, PDO::PARAM_STR);
        $stmt->bindValue('varastossa', $uusivarastossa, PDO::PARAM_INT);
        $stmt->bindValue('myymalassa', $uusimyymalassa, PDO::PARAM_INT);
        $stmt->bindValue('myyntialkaa', $uusimyyntialkaa, PDO::PARAM_STR);
        $stmt->bindValue('vedettymyynnista', $uusivedettymyynnista, PDO::PARAM_STR);
        $stmt->bindValue('kuvat', $uusikuvat, PDO::PARAM_STR);
        $stmt->bindValue('tuotenumero', $_SESSION["tuotenumero"], PDO::PARAM_INT);
        if ($stmt->execute()) {
            header("location: index.php");
            exit;
        }
    }
}

if (isset($_GET["id"])) {
    if (empty(trim($_GET["id"]))) {
        $tuotenumero_err = "Haettavan tuotteen ID puuttuu.";
    } elseif ($div->just_numbers(trim($_GET["id"]))) {
        $tuotenumero_err = "ID voi olla vain numero.";
    } else {
        $tuotenumero = (int) trim($_GET["id"]);
        $tuotenumero_err = "";
        $this_page = "muokkaatietoja.php?id=" . $tuotenumero;
    }
    
    if (empty($tuotenumero_err)) {
        $sql = "SELECT tuotenumero, tuotenimi, valmistaja, hinta, alv, alennus, "
                . "alealkaa, aleloppuu, kategoria, lyhytkuvaus, tuotekuvaus, "
                . "varastossa, myymalassa, myyntialkaa, vedettymyynnista, kuvat "
                . "FROM tuotteet WHERE tuotenumero = :tuotenumero";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue('tuotenumero', $tuotenumero, PDO::PARAM_INT);
        $stmt->execute();
        $tuote = $stmt->fetch(PDO::FETCH_ASSOC);
        /*
         * The following session variable prevents modification of some other product
         * that was initially searched.
         */
        $_SESSION["tuotenumero"] = (int) $tuote["tuotenumero"];
        /*
         * If for some reason datetime values in the database contains seconds other
         * than 00 the datetime-local field in the webpage displays also the seconds.
         * That would cause errors when saving changes to the database, therefore we 
         * have to format the datetime-local values so that seconds will not be dispayed
         * in the webpage.
         */
        if (!is_null($tuote["alealkaa"])) {
            $tuote["alealkaa"] = date('Y-m-d\TH:i', strtotime($tuote["alealkaa"]));
        }
        if (!is_null($tuote["aleloppuu"])) {
            $tuote["aleloppuu"] = date('Y-m-d\TH:i', strtotime($tuote["aleloppuu"]));
        }
        if (!is_null($tuote["myyntialkaa"])) {
            $tuote["myyntialkaa"] = date('Y-m-d\TH:i', strtotime($tuote["myyntialkaa"]));
        }
        if (!is_null($tuote["vedettymyynnista"])) {
            $tuote["vedettymyynnista"] = date('Y-m-d\TH:i', strtotime($tuote["vedettymyynnista"]));
        }
    }
    
    $pdo = null;
    $stmt = null;
}
?>
<!DOCTYPE html>
<html lang="fi">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="author" content="Markus2540">
        <title>Tuotetietojen muokkaaminen</title>
        <meta name="description" content="Tuotetietojen muokkaus-sivu">
        <link rel="stylesheet" href="css/admin.css">
        <meta http-equiv="Content-Security-Policy" content="default-src 'self';">
        <script defer src="javascript/inputHelper.js"></script>
    </head>
    <body>
        <div class="wrapper">
            <nav>
                <h1>Navigointi</h1>
                <?php require_once 'perusosat/navigaatio.php'; ?>
            </nav>
            <main>
                <?php if (!empty($tuotenumero)) { ?>
                
                
                <h1><?php echo puhdistus($tuote["tuotenimi"]); ?></h1>
                <form action="<?php echo puhdistus($this_page); ?>" method="post">
                    <table id="tuotteenmuokkaustaulukko">
                        <tr>
                            <td>Nimi<span class="jsproductname 
                                                   inputhelper"> ? </span>:</td>
                            <td><input type="text" name="uusituotteennimi" value=
                                       "<?php echo puhdistus($tuote["tuotenimi"]);?>" 
                                       pattern="[a-zåäöA-ZÅÄÖ0-9 ,.@-]+" required></td>
                            <td><?php if (isset($uusituotenimi_err)) {
                                echo $uusituotenimi_err;} ?></td>
                        </tr>
                        <tr>
                            <td>Valmistaja<span class="jsproductmanufacturer 
                                                inputhelper"> ? </span>:</td>
                            <td><input type="text" name="uusivalmistaja" value=
                                       "<?php echo puhdistus($tuote["valmistaja"]);?>" 
                                       pattern="[a-zåäöA-ZÅÄÖ0-9 ,.@-]+" required></td>
                            <td><?php if (isset($uusivalmistaja_err)) {
                                echo $uusivalmistaja_err;} ?></td>
                        </tr>
                        <tr>
                            <td>Hinta<span class="jsproductprice 
                                            inputhelper"> ? </span>:</td>
                            <td><input type="text" name="uusihinta" value=
                                       "<?php echo puhdistus($tuote["hinta"]);?>" 
                                       class="price" pattern="[0-9.]+" required></td>
                            <td><?php if (isset($uusihinta_err)) {
                                echo $uusihinta_err;} ?></td>
                        </tr>
                        <tr>
                            <td>Alv:</td>
                            <td><input type="number" name="uusialv" value=
                                       "<?php echo puhdistus($tuote["alv"]);?>" 
                                       required></td>
                            <td><?php if (isset($uusialv_err)) {
                                echo $uusialv_err;} ?></td>
                        </tr>
                        <tr>
                            <td>Alennus<span class="jsproductdiscount 
                                                   inputhelper"> ? </span>:</td>
                            <td><input type="text" name="uusialennus" class="price" value=
                                       "<?php echo puhdistus($tuote["alennus"]);?>"
                                       pattern="[0-9.]*"></td>
                            <td><?php if (isset($uusialennus_err)) {
                                echo $uusialennus_err;} ?></td>
                        </tr>
                        <tr>
                            <td>Ale alkaa:</td>
                            <td><input type="datetime-local" name="uusialealkaa" value=
                                       "<?php echo puhdistus($tuote["alealkaa"]);?>"></td>
                            <td><?php if (isset($uusialealkaa_err)) {
                                echo $uusialealkaa_err;} ?></td>
                        </tr>
                        <tr>
                            <td>Ale loppuu:</td>
                            <td><input type="datetime-local" name="uusialeloppuu" value=
                                       "<?php echo puhdistus($tuote["aleloppuu"]);?>"></td>
                            <td><?php if (isset($uusialeloppuu_err)) {
                                echo $uusialeloppuu_err;} ?></td>
                        </tr>
                        <tr>
                            <td>Kategoria:</td>
                            <td>
                                <select name="uusikategoria">
                                    <optgroup label="Tietokoneet">
                                        <option value="Esikasatut" 
                                            <?php if ($tuote["kategoria"] === "Esikasatut"){
                                                echo "selected";}?>>Esikasatut</option>
                                        <option value="Kotelot" 
                                            <?php if ($tuote["kategoria"] === "Kotelot"){
                                                echo "selected";}?>>Kotelot</option>
                                        <option value="Prosessorit" 
                                            <?php if ($tuote["kategoria"] === "Prosessorit"){
                                                echo "selected";}?>>Prosessorit</option>
                                        <option value="Emolevyt" 
                                            <?php if ($tuote["kategoria"] === "Emolevyt"){
                                                echo "selected";}?>>Emolevyt</option>
                                        <option value="Muistit" 
                                            <?php if ($tuote["kategoria"] === "Muistit"){
                                                echo "selected";}?>>Muistit</option>
                                        <option value="Naytonohjaimet" 
                                            <?php if ($tuote["kategoria"] === "Naytonohjaimet"){
                                                echo "selected";}?>>Näytönohjaimet</option>
                                        <option value="Kiintolevyt" 
                                            <?php if ($tuote["kategoria"] === "Kiintolevyt"){
                                                echo "selected";}?>>Kiintolevyt</option>
                                        <option value="Virtalahteet" 
                                            <?php if ($tuote["kategoria"] === "Virtalahteet"){
                                                echo "selected";}?>>Virtalähteet</option>
                                        <option value="Asemat" 
                                            <?php if ($tuote["kategoria"] === "Asemat"){
                                                echo "selected";}?>>Asemat</option>
                                        <option value="Jaahdytys" 
                                            <?php if ($tuote["kategoria"] === "Jaahdytys"){
                                                echo "selected";}?>>Jäähdytys</option>
                                        <option value="Naytot" 
                                            <?php if ($tuote["kategoria"] === "Naytot"){
                                                echo "selected";}?>>Näytöt</option>
                                    </optgroup>
                                    <option value="Tabletit" 
                                        <?php if ($tuote["kategoria"] === "Tabletit"){
                                            echo "selected";}?>>Tabletit ja tarvikkeet</option>
                                    <option value="Puhelimet ja tarvikkeet" 
                                        <?php if ($tuote["kategoria"] === "Puhelimet ja tarvikkeet"){
                                            echo "selected";}?>>Puhelimet ja tarvikkeet</option>
                                </select>
                            </td>
                            <td><?php if (isset($uusikategoria_err)) {echo $uusikategoria_err;} ?></td>
                        </tr>
                        <tr>
                            <td>Lyhyt kuvaus<span class="jsproductshortdescription 
                                                   inputhelper"> ? </span>:</td>
                            <td><textarea name="uusilyhytkuvaus" id="lyhytkuvaus" 
                                          pattern="[a-zåäöA-ZÅÄÖ0-9 ,.:®\s-]*" maxlength="400"><?php 
                                          echo puhdistus($tuote["lyhytkuvaus"]);?></textarea></td>
                            <td><span id="lyhyenkuvauksenpituus"></span>
                                <?php if (isset($uusilyhytkuvaus_err)) {
                                    echo $uusilyhytkuvaus_err;} ?></td>
                        </tr>
                        <tr>
                            <td>Tuotekuvaus<span class="jsproductdescription 
                                                 inputhelper"> ? </span>:</td>
                            <td><textarea name="uusituotekuvaus" id="tuotekuvaus" 
                                          pattern="[a-zåäöA-ZÅÄÖ0-9 ,.:®\s-]*"
                                          ><?php echo puhdistus($tuote["tuotekuvaus"]);
                                          ?></textarea></td>
                            <td><?php if (isset($uusituotekuvaus_err)) {
                                echo $uusituotekuvaus_err;} ?></td>
                        </tr>
                        <tr>
                            <td>Varastossa:</td>
                            <td><input type="number" name="uusivarastossa" value=
                                       "<?php echo puhdistus($tuote["varastossa"]);?>" 
                                       required></td>
                            <td><?php if (isset($uusivarastossa_err)) {
                                echo $uusivarastossa_err;} ?></td>
                        </tr>
                        <tr>
                            <td>Myymälässä:</td>
                            <td><input type="number" name="uusimyymalassa" value=
                                       "<?php echo puhdistus($tuote["myymalassa"]);?>" 
                                       required></td>
                            <td><?php if (isset($uusimyymalassa_err)) {
                                echo $uusimyymalassa_err;} ?></td>
                        </tr>
                        <tr>
                            <td>Myynti alkaa:</td>
                            <td><input type="datetime-local" name="uusimyyntialkaa" value=
                                       "<?php echo puhdistus($tuote["myyntialkaa"]);?>"></td>
                            <td><?php if (isset($uusimyyntialkaa_err)) {
                                echo $uusimyyntialkaa_err;} ?></td>
                        </tr>
                        <tr>
                            <td>Vedetty myynnistä:</td>
                            <td><input type="datetime-local" name="uusivedettymyynnista" value=
                                       "<?php echo puhdistus($tuote["vedettymyynnista"]);?>"></td>
                            <td><?php if (isset($uusivedettymyynnista_err)) {
                                echo $uusivedettymyynnista_err;} ?></td>
                        </tr>
                        <tr>
                            <td>Kuvat<span class="jsproductpicture2 
                                                 inputhelper"> ? </span>:</td>
                            <td><input type="text" name="uusikuvat" value=
                                       "<?php echo puhdistus($tuote["kuvat"]);?>" pattern="[a-zA-Z0-9.,]*"></td>
                            <td><?php if (isset($uusikuvat_err)) {
                                echo $uusikuvat_err;} ?></td>
                        </tr>
                    </table>
                    <input type="submit" value="Tallenna" name="btntallennamuutokset"
                           id="submitbtn">
                </form>
                
                <?php
                } else { ?>
                <p><?php echo puhdistus($tuotenumero_err); ?></p>
                <?php
                } ?>
            </main>
        </div>
        <div id="tummennus">
            <div id="ilmoitustummennuksenkeskella">
                <p id="ilmoituksenteksti"></p>
                <input type="button" id="btnilmoituksenkuittaus" value="Selvä">
            </div>
        </div>
    </body>
</html>