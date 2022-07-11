<?php

class PDOStatements {
    /**
     * Login function, pass username and password as arguments.
     */
    public function pdo_login($kayttajatunnus, $salasana) {
        require_once '../moduulit/pdoconnect.php';
        $sql = "SELECT asnro, etunimi, sukunimi, kayttajatunnus, salasana, "
                . "lahiosoite, postinumero, postitoimipaikka FROM asiakas "
                . "WHERE kayttajatunnus = :kayttajatunnus";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue('kayttajatunnus', $kayttajatunnus, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user === false) {
            $_SESSION["login_error"] = "Käyttäjänimeä ei löytynyt.";
            header("location: login.php");
            exit;
        } elseif (password_verify($salasana, $user["salasana"])) {
            session_regenerate_id();
            
            $_SESSION["loggedin"] = true;
            $_SESSION["asnro"] = $user["asnro"];
            $_SESSION["etunimi"] = $user["etunimi"];
            $_SESSION["sukunimi"] = $user["sukunimi"];
            $_SESSION["kayttajatunnus"] = $user["kayttajatunnus"];
            $_SESSION["lahiosoite"] = $user["lahiosoite"];
            $_SESSION["postinumero"] = $user["postinumero"];
            $_SESSION["postitoimipaikka"] = $user["postitoimipaikka"];
            
            if (isset($_SESSION["ostoskorinid"])) {
                //Jos kirjautumattoman käyttäjän ostoskorissa on 
                //tuotteita, lisätään ostoskoriin asnro.
                $sql2 = "UPDATE ostoskori SET asnro = :asnro WHERE id = :id";
                $stmt2 = $pdo->prepare($sql2);
                $stmt2->bindValue('asnro', $_SESSION["asnro"], PDO::PARAM_INT);
                $stmt2->bindValue('id', $_SESSION["ostoskorinid"], PDO::PARAM_INT);
                $stmt2->execute();
                
                $stmt2 = null;
            }

            header("location: " . $this_page);
            exit;
        } else {
            $_SESSION["login_error"] = "Käyttäjänimi tai salasana väärin.";
            header("location: login.php");
            exit;
        }
        
        $stmt = null;
        $pdo = null;
    }
    
    
    /**
     * Search for products and save them into an array. Pass searchterm as an argument.
     */
    public function search_from_products($searchterm) {
        require_once '../moduulit/pdoconnect.php';
        $sql = "SELECT tuotenumero, tuotenimi, valmistaja, hinta, alennus, "
                . "alealkaa, aleloppuu, kategoria, varastossa, myymalassa, kuvat, "
                . "myyntialkaa, lyhytkuvaus FROM tuotteet WHERE(vedettymyynnista "
                . "IS NULL || (vedettymyynnista > CURRENT_TIMESTAMP)) && "
                . "(tuotenimi LIKE :tuotenimi || valmistaja LIKE :valmistaja || "
                . "kategoria LIKE :kategoria)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue('tuotenimi', "%" . $searchterm . "%", PDO::PARAM_STR);
        $stmt->bindValue('valmistaja', "%" . $searchterm . "%", PDO::PARAM_STR);
        $stmt->bindValue('kategoria', "%" . $searchterm . "%", PDO::PARAM_STR);
        $stmt->execute();
        global $tuotteet;
        $tuotteet = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $stmt = null;
        $pdo = null;
    }
    
    /**
     * Change password, pass old password and new password as arguments.
     */
    public function change_password($old_password, $new_password) {
        require_once '../moduulit/pdoconnect.php';
        $sql = "SELECT salasana, asnro FROM asiakas WHERE asnro = :asnro";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue('asnro', $_SESSION['asnro'], PDO::PARAM_INT);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (password_verify($old_password, $user['salasana'])) {
            $sql = "UPDATE asiakas SET salasana = :salasana WHERE asnro = :asnro";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue('salasana', password_hash($new_password, 
                    PASSWORD_DEFAULT), PDO::PARAM_STR);
            
            $stmt->bindValue('asnro', $_SESSION['asnro'], PDO::PARAM_INT);
            if ($stmt->execute()) {
                header("location: tilinhallinta.php");
                exit;
            } else {
                $_SESSION["password_change_error"] = "Virhe vaihdettaessa salasanaa";
            }
        } else {
            $GLOBALS['salasana_err'] = "Virheellinen salasana";
        }
        
        $stmt = null;
        $pdo = null;
    }
}