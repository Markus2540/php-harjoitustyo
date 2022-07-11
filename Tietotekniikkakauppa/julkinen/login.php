<?php
$title = "Kirjautuminen";
$description = "Voit kirjautua sisään tällä sivustolla.";
$this_page = "login.php";

require_once '../perusosat/mainheader.php';

if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: etusivu.php");
    exit;
}

/*require_once '../moduulit/dbconnect.php';

$kayttajatunnus = $salasana = "";
$kayttajatunnus_err = $salasana_err = $login_err = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){
    if(empty(trim($_POST["kayttajatunnus"]))){
        $kayttajatunnus_err = "Ole hyvä ja anna käyttäjätunnus.";
    } elseif ($div->validate_username(trim($_POST["kayttajatunnus"]))) {
        $kayttajatunnus_err = "Käyttäjätunnuksesa voi olla vain kirjaimia, "
                . "numeroita ja alaviivoja.";
    } else {
        $kayttajatunnus = trim($_POST["kayttajatunnus"]);
    }
    
    if(empty(trim($_POST["salasana"]))){
        $salasana_err = "Ole hyvä ja anna salasana.";
    } elseif ($div->validate_password(trim($_POST["salasana"]))) {
        $salasana_err = "Salasanassa voi olla vain pieniä ja suuria aakkosia "
                . "(ei åäö), numeroita ja erikoismerkeistä !\"#¤\%&/()=?";
    } else {
        $salasana = trim($_POST["salasana"]);
    }
    
    if(empty($kayttajatunnus_err) && empty($salasana_err)){
        $sql = "SELECT asnro, etunimi, sukunimi, kayttajatunnus, salasana, "
                . "lahiosoite, postinumero, postitoimipaikka FROM asiakas "
                . "WHERE kayttajatunnus = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            mysqli_stmt_bind_param($stmt, "s", $param_kayttajatunnus);
            
            $param_kayttajatunnus = $kayttajatunnus;
            
            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){
                    mysqli_stmt_bind_result($stmt, $asnro, $etunimi, $sukunimi, 
                            $kayttajatunnus, $hashed_password, $lahiosoite, 
                            $postinumero, $postitoimipaikka);
                    if(mysqli_stmt_fetch($stmt)){
                        if(password_verify($salasana, $hashed_password)){
                            session_regenerate_id();
                            
                            $_SESSION["loggedin"] = true;
                            $_SESSION["asnro"] = $asnro;
                            $_SESSION["etunimi"] = $etunimi;
                            $_SESSION["sukunimi"] = $sukunimi;
                            $_SESSION["kayttajatunnus"] = $kayttajatunnus;
                            $_SESSION["lahiosoite"] = $lahiosoite;
                            $_SESSION["postinumero"] = $postinumero;
                            $_SESSION["postitoimipaikka"] = $postitoimipaikka;
                            
                            header("location: etusivu.php");
                            exit;
                        } else {
                            $login_err = "Käyttäjänimi tai salasana väärin.";
                        }
                    }
                } else {
                    $login_err = "Käyttäjänimi tai salasana väärin.";
                }
            } else {
                echo "Jotain meni pieleen.";
            }
            
            mysqli_stmt_close($stmt);
        }
    }
    
    mysqli_close($link);
}*/
?>

<main>
    <h1>Kirjaudu sisään.</h1>
    
    <?php
    if (isset($_SESSION["login_error"])){
        ?><p><?php echo puhdistus($_SESSION["login_error"]); ?></p><?php
    }
    ?>
    
    <form action="<?php echo puhdistus($this_page); ?>" method="post" class="loginpageloginform">
        <label>Käyttäjätunnus:</label><br>
        <input type="text" name="kayttajatunnus" value="<?php echo puhdistus($kayttajatunnus); ?>">
        <span><?php echo puhdistus($kayttajatunnus_err); ?></span><br>
        <label>Salasana:</label><br>
        <input type="password" name="salasana">
        <span><?php echo puhdistus($salasana_err); ?></span><br>
        <input type="submit" value="Kirjaudu sisään" name="btnloginpagelogin">
        <p>Ei tiliä? <a href="tunnustenluonti.php">Luo tunnukset</a>.</p>
    </form>
    
    <div class="push"></div>
</main>

<?php
require_once '../perusosat/mainfooter.php';
unset($_SESSION["login_error"]);