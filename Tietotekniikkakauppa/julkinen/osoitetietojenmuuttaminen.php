<?php
$title = "Osoitetietojen muuttaminen";
$description = "Voit muuttaa osoitetietoja täältä.";
$this_page = "osoitetietojenmuuttaminen.php";

require_once '../perusosat/mainheader.php';
require_once '../moduulit/dbconnect.php';

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: etusivu.php");
    exit;       
}

$vaihdettavalahiosoite = $vaihdettavapostinumero = $vaihdettavapostitoimipaikka = "";
$vaihdettavalahiosoite_err = $vaihdettavapostinumero_err = $vaihdettavapostitoimipaikka_err = "";

if(isset($_POST["btnmuutaosoite"])) {
        if(empty(trim($_POST["vaihdettavalahiosoite"]))){
            $vaihdettavalahiosoite_err = "Lähiosoite vaaditaan";
        } elseif ($div->validate_address(trim($_POST["vaihdettavalahiosoite"]))) {
            $vaihdettavalahiosoite_err = "Lähiosoitteessa voi olla vain suomen kielen "
                    . "aakkosia, numeroita ja välilyöntejä.";
        } else {
            $vaihdettavalahiosoite = trim($_POST["vaihdettavalahiosoite"]);
        }
    
        if(empty(trim($_POST["vaihdettavapostinumero"]))){
            $vaihdettavapostinumero_err = "Postinumero vaaditaan";
        } elseif ($div->just_numbers(trim($_POST["vaihdettavapostinumero"]))) {
            $vaihdettavapostinumero_err = "Postinumerossa voi olla vain numeroita.";
        } elseif (strlen(trim($_POST["vaihdettavapostinumero"])) != 5) {
            $vaihdettavapostinumero_err = "Postinumerossa pitää olla 5 numeroa";
        } else {
            $vaihdettavapostinumero = (string) trim($_POST["vaihdettavapostinumero"]);
        }
        
        if(empty(trim($_POST["vaihdettavapostitoimipaikka"]))){
            $vaihdettavapostitoimipaikka_err = "Postitoimipaikka vaaditaan.";
        } elseif ($div->validate_post_office(trim($_POST["vaihdettavapostitoimipaikka"]))) {
            $vaihdettavapostitoimipaikka_err = "Postitoimipaikassa voi olla vain suomen kielen aakkosia, "
                    . "väliviivoja ja välilyöntejä.";
        } else {
            $vaihdettavapostitoimipaikka = trim($_POST["vaihdettavapostitoimipaikka"]);
        }
        
        if(empty($vaihdettavalahiosoite_err) && empty($vaihdettavapostinumero_err) && 
                empty($vaihdettavapostitoimipaikka_err)) {
            $sql = "UPDATE asiakas SET lahiosoite = ?, postinumero = ?, "
                    . "postitoimipaikka = ? WHERE asnro = ?";
            if ($stmt = mysqli_prepare($link, $sql)) {
                mysqli_stmt_bind_param($stmt, 'sssi', $param_lahiosoite, 
                        $param_postinumero, $param_postitoimipaikka, $param_asnro);
                
                $param_lahiosoite = $vaihdettavalahiosoite;
                $param_postinumero = $vaihdettavapostinumero;
                $param_postitoimipaikka = $vaihdettavapostitoimipaikka;
                $param_asnro = $_SESSION["asnro"];
                
                if(mysqli_stmt_execute($stmt)) {
                    $_SESSION["lahiosoite"] = $vaihdettavalahiosoite;
                    $_SESSION["postinumero"] = $vaihdettavapostinumero;
                    $_SESSION["postitoimipaikka"] = $vaihdettavapostitoimipaikka;
                    header("location: tilinhallinta.php");
                    exit();
                } else {
                    echo "Tapahtui virhe.";
                }
                mysqli_stmt_close($stmt);
            }
            
            /*if(mysqli_query($link, $paivityslauseke)) {
                header("location: tilinhallinta.php");
             * exit;
            } else {
                echo "Error updating record: " . mysqli_error($link);
            }*/
            
            mysqli_close($link);
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
    <h1>Päivitä osoitetiedot</h1>
    
    <p>Päivitä osoitetietosi oikeiksi ja hyväksy muutokset "Muuta osoite"-painikkeella.</p>
    
    <form action="<?php echo puhdistus($this_page); ?>" method="POST" 
          class="osoitteenmuokkaaminen">
        <label for="vaihdettavalahiosoite">Lähiosoite: </label>
            <input type="text" id="vaihdettavalahiosoite" name="vaihdettavalahiosoite" 
                   placeholder="<?php echo puhdistus($_SESSION["lahiosoite"]); ?>" 
                   value="<?php echo puhdistus($vaihdettavalahiosoite); ?>">
            <span><?php echo puhdistus($vaihdettavalahiosoite_err); ?></span><br>
            <label for="vaihdettavapostinumero">Postinumero: </label>
                <input type="text" id="vaihdettavapostinumero" name="vaihdettavapostinumero" 
                       placeholder="<?php echo puhdistus($_SESSION["postinumero"]); ?>" 
                       value="<?php echo puhdistus($vaihdettavapostinumero); ?>">
                <span><?php echo puhdistus($vaihdettavapostinumero_err); ?></span><br>
            <label for="vaihdettavapostitoimipaikka">Postitoimipaikka: </label>
                <input type="text" id="vaihdettavapostitoimipaikka" 
                       name="vaihdettavapostitoimipaikka" placeholder=
                       "<?php echo puhdistus($_SESSION["postitoimipaikka"]); ?>" 
                       value="<?php echo puhdistus($vaihdettavapostitoimipaikka); ?>">
                <span><?php echo puhdistus($vaihdettavapostitoimipaikka_err); ?></span><br>
            
            <input type="submit" value="Muuta osoite" name="btnmuutaosoite">
    </form>
    
    <div class="push"></div>
</main>

<?php
require_once '../perusosat/mainfooter.php';