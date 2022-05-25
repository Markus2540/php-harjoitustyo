<?php
session_start();
if (!isset($_SESSION["adminloggedin"]) || $_SESSION["adminloggedin"] !== true) {
    header("location: ../julkinen/etusivu.php");
    exit;
}
$this_page = "tuotteidenlisaaminen.php";
require_once '../moduulit/perustoimintoja.php';
require_once '../moduulit/dbconnect.php';
require_once '../classes/DataInputValidation.php';

$div = new DataInputValidation();

$kategoriat = ["Esikasatut", "Kotelot", "Prosessorit", "Emolevyt", "Muistit", 
    "Naytonohjaimet", "Kiintolevyt", "Virtalahteet", "Asemat", "Jaahdytys", 
    "Naytot", "Tabletit", "Puhelimet ja tarvikkeet"];

$tuotteennimi = $valmistaja = $hinta = $alv = $kategoria = $lyhytkuvaus = 
        $tuotekuvaus = $varastossa = $myymalassa = $myyntialkaa = $virheilmoitus = "";

$tuotteennimi_err = $valmistaja_err = $hinta_err = $alv_err = $kategoria_err = 
        $lyhytkuvaus_err = $tuotekuvaus_err = $varastossa_err = $myymalassa_err = 
        $myyntialkaa_err = $kuvat_err = "";
$uploaddir = "C:\\xampp\\htdocs\\php-harjoitustyo\\Tietotekniikkakauppa\\julkinen\\kuvat\\";
$kuvat = [];
$file_temp_paths = [];
$new_file_paths = [];

if (isset($_POST["btnvietietokantaan"])) {
    if (empty(trim($_POST["tuotteennimi"]))) {
        $tuotteennimi_err = "Tuotteen nimi vaaditaan";
    } elseif ($div->product_name(trim($_POST["tuotteennimi"]))) {
        $tuotteennimi_err = "Tuotteen nimessä voi olla vain a-zåäöA-ZÅÄÖ0-9 ,.@- merkit";
    } else {
        $tuotteennimi = trim($_POST["tuotteennimi"]);
    }
    
    if (empty(trim($_POST["valmistaja"]))) {
        $valmistaja_err = "Tuotteen valmistaja vaaditaan";
    } elseif ($div->product_name(trim($_POST["valmistaja"]))) {
        $valmistaja_err = "Valmistajan nimessä voi olla vain a-zåäöA-ZÅÄÖ0-9 ,.@- merkit";
    } else {
        $valmistaja = trim($_POST["valmistaja"]);
    }
    
    if (empty(trim($_POST["hinta"]))) {
        $hinta_err = "Hinta vaaditaan.";
    } elseif ($div->validate_money(trim($_POST["hinta"]))) {
        $hinta_err = "Hinnassa voidaan käyttää vain numeroita ja desimaalierottimena pistettä.";
    } else {
        $hinta = trim($_POST["hinta"]);
    }
    
    if (empty(trim($_POST["alv"]))) {
        $alv_err = "Arvolisäveroprosentti vaaditaan.";
    } elseif ($div->just_numbers(trim($_POST["alv"]))) {
        $alv_err = "Arvolisäveroprosentissa voi käyttää vain numeroita.";
    } else {
        $alv = trim($_POST["alv"]);
    }
    
    if (empty(trim($_POST["kategoria"]))) {
        $kategoria_err = "Kategoria vaaditaan.";
    } elseif ($div->validate_category(trim($_POST["kategoria"]))) {
        $kategoria_err = "Kategoriassa sallitaan vain numeroita, kirjaimia ja välilyöntejä.";
    } elseif (!in_array(trim($_POST["kategoria"]), $kategoriat)) {
        $kategoria_err = "Kategoria voi olla vain ennalta määritetty kategoria.";
    } else {
        $kategoria = trim($_POST["kategoria"]);
    }
    
    if (empty(trim($_POST["lyhytkuvaus"]))) {
        $lyhytkuvaus = NULL;
    } elseif ($div->validate_description(trim($_POST["lyhytkuvaus"]))) {
        $lyhytkuvaus_err = "Vain a-zåäöA-ZÅÄÖ0-9 ,.:®\s- merkit sallitaan lyhyessä kuvauksessa.";
    } elseif (mb_strlen(trim($_POST["lyhytkuvaus"])) > 400) {
        $lyhytkuvaus_err = "Pidä lyhyt kuvaus maksimissaan 400 merkkiä pitkänä. "
                . "Annettu kuvaus oli " . mb_strlen(trim($_POST["lyhytkuvaus"])) . " merkkiä pitkä.";
    } else {
        $lyhytkuvaus = trim($_POST["lyhytkuvaus"]);
    }
    
    if (empty(trim($_POST["tuotekuvaus"]))) {
        $tuotekuvaus = NULL;
    } elseif ($div->validate_description(trim($_POST["tuotekuvaus"]))) {
        $tuotekuvaus_err = "Vain a-zåäöA-ZÅÄÖ0-9 ,.:®\s- merkit sallitaan tuotekuvauksessa.";
    } else {
        $tuotekuvaus = trim($_POST["tuotekuvaus"]);
    }
    
    if (empty(trim($_POST["varastossa"]))) {
        $varastossa_err = "Varastotilanne vaaditaan";
    } elseif ($div->just_numbers(trim($_POST["varastossa"]))) {
        $varastossa_err = "Vain kokonaisluku sallitaan";
    } else {
        $varastossa = trim($_POST["varastossa"]);
    }
    
    if (empty(trim($_POST["myymalassa"]))) {
        $myymalassa_err = "Myymälätilanne vaaditaan";
    } elseif ($div->just_numbers(trim($_POST["myymalassa"]))) {
        $myymalassa_err = "Vain kokonaisluku sallitaan";
    } else {
        $myymalassa = trim($_POST["myymalassa"]);
    }
    
    if (empty(trim($_POST["myyntialkaa"]))) {
        $myyntialkaa = NULL;
    } elseif ($div->validate_datetime_local(trim($_POST["myyntialkaa"]))) {
        $myyntialkaa_err = "Syötetty päivämäärä tai aika ei kelpaa.";
    } else {
        $myyntialkaa = trim($_POST["myyntialkaa"]);
    }
    
    /*
     * Uploading and maintaining product images could be improved by creating folders
     * for individual products which makes maintaining pictures easier. This could be 
     * done with mkdir and file_exists filesystem functions.
     */
    $file_temp_path = $_FILES['esittelykuva']['tmp_name'];
    $file_size = filesize($file_temp_path);
    if ($file_size > 5000 && $file_size < 71680) {
        $file_info = finfo_open(FILEINFO_MIME_TYPE);
        $file_type = finfo_file($file_info, $file_temp_path);
        if ($file_type === "image/jpeg") {
            if (!preg_match('%[^a-zA-Z0-9]%', (basename($_FILES['esittelykuva']
                    ['name'], ".jpg"))) && strlen(basename($_FILES['esittelykuva']
                            ['name'])) < 150) {
                $file_name = basename($_FILES['esittelykuva']['name'], ".jpg");
                array_push($file_temp_paths, $file_temp_path);
                array_push($new_file_paths, $uploaddir . $file_name . ".jpg");
                array_push($kuvat, $file_name . ".jpg");
            } else {
                $kuvat_err = "Yhden tai useamman tiedoston nimi ei kelpaa.";
            }
        } else {
            $kuvat_err = "Tiedosto voi olla vain image/jpeg-muotoinen.";
        }
    } else {
        //$kuvat_err = "Tiedosto liian pieni, suuri tai ei tiedostoa ollenkaan"; 
        //Kuvat eivät ole pakollisia jos niitä ei ole.
    }
    
    if (count($kuvat) === 0) {
        array_push($kuvat, "eikuvaapieni.png");
    }
    
    $number_of_uploaded_files = count($_FILES['isotuotekuva']['name']);
    for ($i = 0; $i < $number_of_uploaded_files; $i++) {
        $file_temp_path = $_FILES['isotuotekuva']['tmp_name'][$i];
        $file_size = filesize($file_temp_path);
        if ($file_size > 5000 && $file_size < 409600) {
            
            $file_info = finfo_open(FILEINFO_MIME_TYPE);
            $file_type = finfo_file($file_info, $file_temp_path);
            if ($file_type === "image/jpeg") {
                if (!preg_match('%[^a-zA-Z0-9]%', (basename($_FILES['isotuotekuva']
                        ['name'][$i], ".jpg"))) && strlen(basename($_FILES['isotuotekuva']
                                ['name'][$i])) < 150) {
                    $file_name = basename($_FILES['isotuotekuva']['name'][$i], ".jpg");
                    array_push($file_temp_paths, $file_temp_path);
                    array_push($new_file_paths, $uploaddir . $file_name . ".jpg");
                    array_push($kuvat, $file_name . ".jpg");
                } else {
                    $kuvat_err = "Yhden tai useamman tiedoston nimi ei kelpaa";
                    break;
                }
            } else {
                $kuvat_err = "Tiedosto voi olla vain image/jpeg-muotoinen.";
                break;
            }
        } else {
            //$kuvat_err = "Tiedosto liian pieni, suuri tai ei tiedostoa ollenkaan."; 
            //Kuvia ei ole pakko lisätä.
            break;
        }
    }
    
    if (count($kuvat) === 1) {
        array_push($kuvat, "eikuvaa.png");
    }
    $kuvat = implode(",", $kuvat);
    
    if (empty($tuotteennimi_err) && empty($valmistaja_err) && empty($hinta_err) && 
            empty($alv_err) && empty($kategoria_err) && empty($lyhytkuvaus_err) && 
            empty($tuotekuvaus_err) && empty($varastossa_err) && empty($myymalassa_err) && 
            empty($myyntialkaa_err) && empty($kuvat_err)) {
        $sql = "INSERT INTO tuotteet (tuotenimi, valmistaja, hinta, alv, kategoria, "
                . "lyhytkuvaus, tuotekuvaus, varastossa, myymalassa, myyntialkaa, "
                . "kuvat) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        if ($stmt = $link->prepare($sql)) {
            $stmt->bind_param('ssdisssiiss', $param_tuotteennimi, $param_valmistaja, 
                    $param_hinta, $param_alv, $param_kategoria, $param_lyhytkuvaus, 
                    $param_tuotekuvaus, $param_varastossa, $param_myymalassa, 
                    $param_myyntialkaa, $param_kuvat);
            $param_tuotteennimi = $tuotteennimi;
            $param_valmistaja = $valmistaja;
            $param_hinta = $hinta;
            $param_alv = $alv;
            $param_kategoria = $kategoria;
            $param_lyhytkuvaus = $lyhytkuvaus;
            $param_tuotekuvaus = $tuotekuvaus;
            $param_varastossa = $varastossa;
            $param_myymalassa = $myymalassa;
            $param_myyntialkaa = $myyntialkaa;
            $param_kuvat = $kuvat;
            if ($stmt->execute()) {
                for ($i = 0; $i < count($file_temp_paths); $i++) {
                    move_uploaded_file($file_temp_paths[$i], $new_file_paths[$i]);
                }
                header("location: index.php");
                exit;
            } else {
                $virheilmoitus = "Toinen if.";
            }
        } else {
            $virheilmoitus = "Ensimmäinen if.";
        }
        $stmt->close();
    }
    $link->close();
}
?>
<!DOCTYPE html>
<html lang="fi">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="author" content="Markus2540">
        <title>Tuotteiden lisääminen</title>
        <meta name="description" content="Tätä sivua käytetään tuotteiden lisäämiseen">
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
                <h1>Tuotteen lisääminen</h1>
                
                <form enctype="multipart/form-data" action=
                      "<?php echo puhdistus($this_page); ?>" method="post" 
                      id="tuotteenlisayslomake">
                    <table id="tuotteenlisaystaulukko">
                        <tr>
                            <td>Tuotteen nimi<span class="jsproductname 
                                                   inputhelper"> ? </span>:</td>
                            <td><input type="text" name="tuotteennimi" value=
                                       "<?php echo puhdistus($tuotteennimi); ?>" 
                                       pattern="[a-zåäöA-ZÅÄÖ0-9 ,.@-]+" required></td>
                            <td><?php if (isset($tuotteennimi_err)) {
                                echo puhdistus($tuotteennimi_err);} ?></td>
                        </tr>
                        <tr>
                            <td>Valmistaja<span class="jsproductmanufacturer 
                                                inputhelper"> ? </span>:</td>
                            <td><input type="text" name="valmistaja" value=
                                       "<?php echo puhdistus($valmistaja); ?>" 
                                       pattern="[a-zåäöA-ZÅÄÖ0-9 ,.@-]+" required></td>
                            <td><?php if (isset($valmistaja_err)) {
                                echo puhdistus($valmistaja_err);} ?></td>
                        </tr>
                        <tr>
                            <td>Hinta<span class="jsproductprice 
                                            inputhelper"> ? </span>:</td>
                            <td><input type="text" name="hinta" value=
                                       "<?php echo puhdistus($hinta); ?>" 
                                       class="price" pattern="[0-9.]+" required> €</td>
                            <td><?php if (isset($hinta_err)) {
                                echo puhdistus($hinta_err);} ?></td>
                        </tr>
                        <tr>
                            <td>Alv:</td>
                            <td><input type="number" name="alv" value=
                                       "<?php if (!empty($alv)){
                                           echo puhdistus($alv);} else {
                                               echo "24";} ?>" required > %</td>
                            <td><?php if (isset($alv_err)) {
                                echo puhdistus($alv_err);} ?></td>
                        </tr>
                        <tr>
                            <td>Kategoria:</td>
                            <td><select name="kategoria">
                                    <optgroup label="Tietokoneet">
                                        <option value="Esikasatut">Esikasatut</option>
                                        <option value="Kotelot">Kotelot</option>
                                        <option value="Prosessorit">Prosessorit</option>
                                        <option value="Emolevyt">Emolevyt</option>
                                        <option value="Muistit">Muistit</option>
                                        <option value="Naytonohjaimet">Näytönohjaimet</option>
                                        <option value="Kiintolevyt">Kiintolevyt</option>
                                        <option value="Virtalahteet">Virtalähteet</option>
                                        <option value="Asemat">Asemat</option>
                                        <option value="Jaahdytys">Jäähdytys</option>
                                        <option value="Naytot">Näytöt</option>
                                    </optgroup>
                                    <option value="Tabletit">Tabletit ja tarvikkeet</option>
                                    <option value="Puhelimet ja tarvikkeet">Puhelimet ja tarvikkeet</option>
                                </select>
                            </td>
                            <td><?php if (isset($kategoria_err)) {
                                echo puhdistus($kategoria_err);} ?></td>
                        </tr>
                        <tr>
                            <td>Lyhyt kuvaus<span class="jsproductshortdescription 
                                                   inputhelper"> ? </span>:</td>
                            <td><textarea name="lyhytkuvaus" id="lyhytkuvaus" 
                                          maxlength="400"></textarea></td>
                            <td><span id="lyhyenkuvauksenpituus"></span><?php 
                            if (isset($lyhytkuvaus_err)) {
                                echo puhdistus($lyhytkuvaus_err);} ?></td>
                        </tr>
                        <tr>
                            <td>Tuotekuvaus<span class="jsproductdescription 
                                                 inputhelper"> ? </span>:</td>
                            <td><textarea name="tuotekuvaus" id="tuotekuvaus"></textarea></td>
                            <td><?php if (isset($tuotekuvaus_err)) {
                                echo puhdistus($tuotekuvaus_err);} ?></td>
                        </tr>
                        <tr>
                            <td>Varastossa:</td>
                            <td><input type="number" name="varastossa" value=
                                       "<?php echo puhdistus($varastossa); ?>" 
                                       required></td>
                            <td><?php if (isset($varastossa_err)) {
                                echo puhdistus($varastossa_err);} ?></td>
                        </tr>
                        <tr>
                            <td>Myymälässä:</td>
                            <td><input type="number" name="myymalassa" value=
                                       "<?php echo puhdistus($myymalassa); ?>" 
                                       required></td>
                            <td><?php if (isset($myymalassa_err)) {
                                echo puhdistus($myymalassa_err);} ?></td>
                        </tr>
                        <tr>
                            <td>Myynti alkaa:</td>
                            <td><input type="datetime-local" name="myyntialkaa"></td>
                            <td><?php if (isset($myyntialkaa_err)) {
                                echo puhdistus($myyntialkaa_err);} ?></td>
                        </tr>
                        <tr>
                            <td>Pieni esittelykuva<span class="jsproductpicture 
                                                 inputhelper"> ? </span>:</td>
                            <td><input type="hidden" name="MAX_FILE_SIZE" value="71680">
                                <input type="file" name="esittelykuva" accept=".jpg"></td>
                            <td><?php if (isset($kuvat_err)) {echo $kuvat_err;} ?></td>
                        </tr>
                        <tr>
                            <td>Isot tuotekuvat<span class="jsproductpicture 
                                                 inputhelper"> ? </span>:</td>
                            <td><input type="hidden" name="MAX_FILE_SIZE" value="409600">
                                <input type="file" name="isotuotekuva[]" accept=".jpg" multiple></td>
                            <td><?php if (isset($kuvat_err)) {echo $kuvat_err;} ?></td>
                        </tr>
                    </table>
                    <input type="submit" value="Vie tietokantaan" name="btnvietietokantaan" 
                           id="submitbtn">
                </form>
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