<?php
$title = "Salasanan muuttaminen";
$description = "Voit muuttaa salasanasi tällä sivulla.";
$this_page = "muutasalasana.php";

require_once '../perusosat/mainheader.php';
//require_once '../moduulit/dbconnect.php';

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: etusivu.php");
    exit;       
}

$salasana = $uusi_salasana = $vahvista_uusi_salasana = "";
$salasana_err = $uusi_salasana_err = $vahvista_uusi_salasana_err = "";

if(isset($_POST["btnvaihdasalasana"])) {
    //Uuden salasanan tarkistaminen
    if(empty(trim($_POST["uusisalasana"]))){
        $uusi_salasana_err = "Syötä uusi salasana";
    } elseif ($div->validate_password(trim($_POST["uusisalasana"]))) {
        $uusi_salasana_err = "Salasanassa voi olla vain pieniä ja suuria aakkosia "
                . "(ei åäö), numeroita ja erikoismerkeistä !\"#¤\%&/()=?";
    } elseif(strlen(trim($_POST["uusisalasana"])) < 8){
        $uusi_salasana_err = "Salasanan pitää olla vähintään 8 merkkiä pitkä.";
    } elseif ($div->password_strength(trim($_POST["uusisalasana"])) < 3) { 
        $uusi_salasana_err = "Salasanan monimutkaisuusvaatimus ei täyty";
    } else {
        $uusi_salasana = trim($_POST["uusisalasana"]);
    }

    if(empty(trim($_POST["vahvistauusisalasana"]))){
        $vahvista_uusi_salasana_err = "Vahvista uusi salasana.";
    } else {
        $vahvista_uusi_salasana = trim($_POST["vahvistauusisalasana"]);
        if(empty($uusi_salasana_err) && ($uusi_salasana != $vahvista_uusi_salasana)){
            $vahvista_uusi_salasana_err = "Salasanat eivät vastanneet toisiaan.";
        }
    }
    
    if(empty(trim($_POST["salasana"]))) {
        $salasana_err = "Ole hyvä ja anna salasana.";
    } else {
        $salasana = trim($_POST["salasana"]);
    }
    
    if (empty($salasana_err) && empty($uusi_salasana_err) && 
            empty($vahvista_uusi_salasana_err)) {
        //Replaced with pdo_statements.
        /*$sql = "SELECT salasana, asnro FROM asiakas WHERE asnro = ?";
        
        if($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "i", $param_asnro);
            
            $param_asnro = htmlspecialchars($_SESSION["asnro"]);
            
            if(mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1) {
                    mysqli_stmt_bind_result($stmt, $hashed_password, $asnro);
                    
                    if(mysqli_stmt_fetch($stmt)) {
                        if(password_verify($salasana, $hashed_password)) {
                            $sql = "UPDATE asiakas SET salasana = ? WHERE asnro = ?";
                            
                            if($stmt = mysqli_prepare($link, $sql)) {
                                mysqli_stmt_bind_param($stmt, "si", 
                                        $param_uusi_salasana, $param_asnro);
                                
                                $param_uusi_salasana = password_hash($uusi_salasana, 
                                        PASSWORD_DEFAULT);
                                
                                if(mysqli_stmt_execute($stmt)) {
                                    header("location: tilinhallinta.php");
                                    exit;
                                } else {
                                    echo 'Tapahtui virhe';
                                }
                                mysqli_stmt_close($stmt);
                            }
                        } else {
                            $salasana_err = "Virheellinen salasana";
                        }
                    }
                }
            }
        }
        mysqli_close($link);*/
        
        $pdo_statements->change_password($salasana, $uusi_salasana);
    }
}
?>

<div class="accountmanagementnavigation">
    <ul>
        <li><a href="tilinhallinta.php">Omat tiedot</a></li>
        <li><a href="tilaushistoria.php">Tilaushistoria</a></li>
        <li><a href="ostoskori.php">Tarkastele ostoskoria</a></li>
    </ul>
</div>
<main>
    <h1>Salasanan muuttaminen</h1>
    
    <p>Muuta salasanasi antamalla aluksi salasanakenttään tämänhetkisen salasanasi 
        ja uusi salasana sitä seuraaviin kenttiin. Onnistuneen salasanan muuttamisen 
        jälkeen selain menee automaattisesti tilinhallinnan etusivulle.</p>
    
    <form action="<?php echo puhdistus($this_page); ?>" method="POST">
        <table>
            <tr>
                <td>Nykyinen salasanasi: </td>
                <td><input type="password" name="salasana" value=
                           "<?php /*echo puhdistus($salasana);*/ ?>"></td>
                <td><?php echo puhdistus($salasana_err); ?></td>
            </tr>
            <tr>
                <td>Uusi salasanasi: <span id="salasanaohje">?</span> </td>
                <td><input type="password" name="uusisalasana" value=
                           "<?php /*echo puhdistus($uusi_salasana);*/ ?>" 
                           id="uusisalasana"></td>
                <td><span id="salasanainfo"><?php echo puhdistus($uusi_salasana_err); ?></span></td>
            </tr>
            <tr>
                <td>Toista uusi salasanasi: </td>
                <td><input type="password" name="vahvistauusisalasana" value=
                           "<?php /*echo puhdistus($vahvista_uusi_salasana);*/ ?>" 
                           id="uusivahvistasalasana"></td>
                <td><span id="salasanantoistoinfo"><?php 
                echo puhdistus($vahvista_uusi_salasana_err); ?></span></td>
            </tr>
        </table>
        <input type="submit" value="Vaihda salasana!" name="btnvaihdasalasana">
    </form>
    <script defer src="javascript/salasananVahvuus.js"></script>
    
    <?php 
    if (isset($_SESSION['password_change_error'])) {
        ?><h2><?php echo puhdistus($_SESSION['password_change_error']); ?></h2><?php
    }
    ?>
    
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
unset($_SESSION['password_change_error']);