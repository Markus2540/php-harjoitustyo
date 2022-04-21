<?php
$title = "Luo tunnukset";
$description = "Voit luoda tunnukset tällä sivulla.";
$this_page = "tunnustenluonti.php";

require_once '../perusosat/mainheader.php';
require_once '../moduulit/dbconnect.php';

if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: etusivu.php");
    exit;
}

$uusikayttajatunnus = $uusisalasana = $uusi_vahvista_salasana = $etunimi = 
        $sukunimi = $lahiosoite = $postinumero = $postitoimipaikka = "";
$uusikayttajatunnus_err = $uusisalasana_err = $uusi_vahvista_salasana_err = 
        $etunimi_err = $sukunimi_err = $lahiosoite_err = $postinumero_err = 
        $postitoimipaikka_err = "";

if($_SERVER["REQUEST_METHOD"] == "POST") {
    //Käyttäjätunnuksen tarkistaminen
    if(empty(trim($_POST["uusikayttajatunnus"]))) {
        $uusikayttajatunnus_err = "Ole hyvä ja anna käyttäjätunnus.";
    } elseif ($div->validate_username(trim($_POST["uusikayttajatunnus"]))) {
        $uusikayttajatunnus_err = "Käyttäjätunnuksesa voi olla vain "
                . "kirjaimia, numeroita ja alaviivoja.";
    } else {
        $sql = "SELECT asnro FROM asiakas WHERE kayttajatunnus = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            mysqli_stmt_bind_param($stmt, "s", $param_kayttajatunnus);
            
            $param_kayttajatunnus = trim($_POST["uusikayttajatunnus"]);
            
            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $uusikayttajatunnus_err = "Tämä käyttäjätunnus on jo varattu.";
                } else {
                    $uusikayttajatunnus = trim($_POST["uusikayttajatunnus"]);
                }
            } else {
                echo "Jotain meni pieleen.";
            }
                mysqli_stmt_close($stmt);
            }
        }
    
        //Etunimen tarkistaminen
        if(empty(trim($_POST["etunimi"]))) {
            $etunimi_err = "Etunimi vaaditaan.";
        } elseif ($div->validate_name(trim($_POST["etunimi"]))) {
            $etunimi_err = "Etunimessä voi käyttää vain aakkosia, heittomerkkejä, "
                    . "välilyöntejä ja väliviivoja.";
        } else {
            $etunimi = trim($_POST["etunimi"]);
        }
        
        //Sukunimen tarkistaminen
        if(empty(trim($_POST["sukunimi"]))) {
            $sukunimi_err = "Sukunimi vaaditaan.";
        } elseif ($div->validate_name(trim($_POST["sukunimi"]))) {
            $sukunimi_err = "Sukunimessä voi käyttää vain aakkosia, heittomerkkejä, "
                    . "välilyöntejä ja väliviivoja.";
        } else {
            $sukunimi = trim($_POST["sukunimi"]);
        }
        
        //Lähiosoitteen tarkistaminen
        if(empty(trim($_POST["lahiosoite"]))){
            $lahiosoite_err = "Lähiosoite vaaditaan";
        } elseif ($div->validate_address(trim($_POST["lahiosoite"]))) {
            $lahiosoite_err = "Lähiosoitteessa voi käyttää vain aakkosia, numeroita, "
                    . "välilyöntejä ja väliviivoja.";
        } else {
            $lahiosoite = trim($_POST["lahiosoite"]);
        }
        
        //Postinumeron tarkistaminen
        if(empty(trim($_POST["postinumero"]))){
            $postinumero_err = "Postinumero vaaditaan";
        } elseif ($div->just_numbers(trim($_POST["postinumero"]))) {
            $postinumero_err = "Postinumerossa voi olla vain numeroita.";
        } elseif (strlen(trim($_POST["postinumero"])) != 5) {
            $postinumero_err = "Postinumerossa pitää olla 5 numeroa";
        } else {
            $postinumero = (string) trim($_POST["postinumero"]);
        }
        
        //Postiosoitteen tarkistaminen
        if(empty(trim($_POST["postitoimipaikka"]))){
            $postitoimipaikka_err = "Postitoimipaikka vaaditaan.";
        } elseif ($div->validate_post_office(trim($_POST["postitoimipaikka"]))) {
            $postitoimipaikka_err = "Vain aakkoset, välilyönnit ja "
                    . "väliviivat sallitaan";
        } else {
            $postitoimipaikka = trim($_POST["postitoimipaikka"]);
        }
        
        //Salasanan tarkistaminen
        if(empty(trim($_POST["uusisalasana"]))){
            $uusisalasana_err = "Syötä salasana";
        } elseif ($div->validate_password(trim($_POST["uusisalasana"]))) {
            $uusisalasana_err = "Salasanassa voi olla vain pieniä ja "
                    . "suuria aakkosia (ei åäö), numeroita ja erikoismerkeistä "
                    . "!\"#¤\%&/()=?";
        } elseif(strlen(trim($_POST["uusisalasana"])) < 8){ 
            $uusisalasana_err = "Salasanan pitää olla vähintään 8 merkkiä pitkä.";
        } elseif ($div->password_strength(trim($_POST["uusisalasana"])) < 3) { 
            $uusisalasana_err = "Salasanan monimutkaisuusvaatimus ei täyty";
        } else {
            $uusisalasana = trim($_POST["uusisalasana"]);
        }
        
        if(empty(trim($_POST["uusivahvistasalasana"]))){
            $uusi_vahvista_salasana_err = "Vahvista salasana.";
        } else {
            $uusi_vahvista_salasana = trim($_POST["uusivahvistasalasana"]);
            if(empty($uusisalasana_err) && ($uusisalasana !== $uusi_vahvista_salasana)){
                $uusi_vahvista_salasana_err = "Salasanat eivät vastanneet toisiaan.";
            }
        }
        
        if(empty($uusikayttajatunnus_err) && empty($uusisalasana_err) && 
                empty($uusi_vahvista_salasana_err) && empty($etunimi_err) && 
                empty($sukunimi_err) && empty($lahiosoite_err) && empty($postinumero_err) && 
                empty($postitoimipaikka_err)){
            $sql = 'INSERT INTO asiakas (etunimi, sukunimi, kayttajatunnus, salasana, '
                    . 'lahiosoite, postinumero, postitoimipaikka) VALUES (?, ?, ?, ?, ?, ?, ?)';
            if($stmt = mysqli_prepare($link, $sql)){
                mysqli_stmt_bind_param($stmt, 'sssssss', $param_etunimi, $param_sukunimi, 
                        $param_kayttajatunnus, $param_salasana, $param_lahiosoite, 
                        $param_postinumero, $param_postitoimipaikka);
                
                $param_etunimi = $etunimi;
                $param_sukunimi = $sukunimi;
                $param_kayttajatunnus = $uusikayttajatunnus;
                $param_salasana = password_hash($uusisalasana, PASSWORD_DEFAULT);
                $param_lahiosoite = $lahiosoite;
                $param_postinumero = $postinumero;
                $param_postitoimipaikka = $postitoimipaikka;
                
                if(mysqli_stmt_execute($stmt)){
                    header("location: login.php");
                    exit;
                } else {
                    echo 'Tapahtui virhe.';
                }
                
                mysqli_stmt_close($stmt);
            }
        }
        mysqli_close($link);
}

?>

<main>
    
    <div class="tunnustenluontiikkuna">
        <h1>Luo tunnukset</h1>
        <p>Lomakkeen kaikki kentät ovat pakollisia tunnuksen luontia varten.</p>
        <form action="<?php echo puhdistus($this_page); ?>" method="post" id=
              "tunnustenluontilomake">
            <label for="tunnustenluontietunimi">Etunimi</label><br>
                <input type="text" name="etunimi" id="tunnustenluontietunimi" value=
                       "<?php echo puhdistus($etunimi); ?>" required>
            <span><?php echo puhdistus($etunimi_err); ?></span><br>
            <label for="tunnustenluontisukunimi">Sukunimi</label><br>
                <input type="text" name="sukunimi" id="tunnustenluontisukunimi" 
                       value="<?php echo puhdistus($sukunimi); ?>" required>
                <span><?php echo puhdistus($sukunimi_err); ?></span><br>
            <label for="uusikayttajatunnus">Käyttäjätunnus</label><br>
                <input type="text" id="uusikayttajatunnus" name=
                       "uusikayttajatunnus" value="<?php 
                       echo puhdistus($uusikayttajatunnus); ?>" required>
                <span><?php echo puhdistus($uusikayttajatunnus_err); ?></span><br>
            <label for="uusisalasana">Salasana</label> <span id="salasanaohje">?</span><br>
                <input type="password" name="uusisalasana" id=
                       "uusisalasana" value="<?php 
                       echo puhdistus($uusisalasana); ?>" required>
                <span id="salasanainfo"><?php 
                echo puhdistus($uusisalasana_err); ?></span><br>
            <label for="uusivahvistasalasana">Toista salasana</label><br>
                <input type="password" name="uusivahvistasalasana" id=
                       "uusivahvistasalasana" value="<?php 
                       echo puhdistus($uusi_vahvista_salasana); ?>" required>
                <span id="salasanantoistoinfo"><?php 
                echo puhdistus($uusi_vahvista_salasana_err); ?></span><br>
            <label for="tunnustenluontilahiosoite">Lähiosoite</label><br>
                <input type="text" name="lahiosoite" id="tunnustenluontilahiosoite" 
                       value="<?php echo puhdistus($lahiosoite); ?>" required>
                <span><?php echo puhdistus($lahiosoite_err); ?></span><br>
                <label for="postinumero">Postinumero</label><br>
                <input type="text" name="postinumero" id="postinumero" value=
                       "<?php echo puhdistus($postinumero); ?>" required>
                <span><?php echo puhdistus($postinumero_err); ?></span><br>
            <label for="tunnustenluontipostitoimipaikka">Postitoimipaikka</label><br>
                <input type="text" name="postitoimipaikka" id="tunnustenluontipostitoimipaikka" 
                       value="<?php echo puhdistus($postitoimipaikka); ?>" required>
                <span><?php echo puhdistus($postitoimipaikka_err); ?></span><br>
            <input type="submit" value="Rekisteröidy">
            <input type="reset" value="Nollaa">
            
            <p>Unohditko salasanasi? <a href="login.php">Nollaa salasana</a>.</p>
        </form>
    </div>
    <script defer src="javascript/salasananVahvuus.js"></script>
    <div class="push"></div>
</main>

<div id="tummennus">
    <div id="ilmoitustummennuksenkeskella">
        <p id="ilmoituksenteksti"></p>
        <input type="button" id="btnilmoituksenkuittaus" value="Selvä">
    </div>
</div>

<?php
require_once '../perusosat/mainfooter.php';