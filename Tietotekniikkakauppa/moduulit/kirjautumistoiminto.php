<?php
//require_once '../moduulit/dbconnect.php';

$kayttajatunnus = $salasana = "";
$kayttajatunnus_err = $salasana_err = "";

if (isset($_POST['btnmainlogin']) || isset($_POST['btnloginpagelogin'])) {
    if(empty(trim($_POST["kayttajatunnus"]))) {
        $kayttajatunnus_err = "Ole hyvä ja anna käyttäjätunnus.";
    } else {
        $kayttajatunnus = trim($_POST["kayttajatunnus"]);
    }
    
    if(empty(trim($_POST["salasana"]))) {
        $salasana_err = "Ole hyvä ja anna salasana.";
    } else {
        $salasana = trim($_POST["salasana"]);
    }
    
    if(empty($kayttajatunnus_err) && empty($salasana_err)) {
        /*$sql = "SELECT asnro, etunimi, sukunimi, kayttajatunnus, salasana, "
                . "lahiosoite, postinumero, postitoimipaikka FROM asiakas WHERE "
                . "kayttajatunnus = ?";
        
        if($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $param_kayttajatunnus);
            
            $param_kayttajatunnus = $kayttajatunnus;
            
            if(mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1) {
                    mysqli_stmt_bind_result($stmt, $asnro, $etunimi, $sukunimi, 
                            $kayttajatunnus, $hashed_password, $lahiosoite, 
                            $postinumero, $postitoimipaikka);
                    
                    if(mysqli_stmt_fetch($stmt)) {
                        if(password_verify($salasana, $hashed_password)) {
                            session_regenerate_id();
                            
                            $_SESSION["loggedin"] = true;
                            $_SESSION["asnro"] = $asnro;
                            $_SESSION["etunimi"] = $etunimi;
                            $_SESSION["sukunimi"] = $sukunimi;
                            $_SESSION["kayttajatunnus"] = $kayttajatunnus;
                            $_SESSION["lahiosoite"] = $lahiosoite;
                            $_SESSION["postinumero"] = $postinumero;
                            $_SESSION["postitoimipaikka"] = $postitoimipaikka;
                            
                            if (isset($_SESSION["ostoskorinid"])) {
                                //Jos kirjautumattoman käyttäjän ostoskorissa on 
                                //tuotteita, lisätään ostoskoriin asnro.
                                $sql = "UPDATE ostoskori SET asnro = ? WHERE id = ?";
                                $stmt = $link->prepare($sql);
                                $stmt->bind_param("ii", $param_asnro, $param_id);
                                $param_asnro = $_SESSION["asnro"];
                                $param_id = $_SESSION["ostoskorinid"];
                                $stmt->execute();
                            }
                            
                            header("location: ");
                            exit;
                        } else {
                            $login_err = "Käyttäjätunnus tai salasana väärin.";
                        }
                    }
                } else {
                    $login_err = "Käyttäjätunnus tai salasana väärin.";
                }
            } else {
                echo "Jotain meni pieleen.";
            }
            
            mysqli_stmt_close($stmt);
        }*/
    }
    //mysqli_close($link);
    
    $pdo_statements->pdo_login($kayttajatunnus, $salasana);
}