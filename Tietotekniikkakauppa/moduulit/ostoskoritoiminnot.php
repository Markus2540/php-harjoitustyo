<?php

$maara = $tuotenumero = "";
$maara_err = $tuotenumero_err = $virheilmoitus = "";

if (isset($_POST['btnlisaakoriin'])) {
    
    /*
     * Ordered amount should contain only positive integers and value must be 1 
     * or more. No error is shown to the user for this, because input field 
     * should not accept anything other than positive integers between 1 and the 
     * amount in the warehouse.
     */
    if(empty(trim($_POST["tilausmaara"]))) {
        $maara_err = "Tilausmäärä vaaditaan.";
    } elseif ($div->just_numbers(trim($_POST["tilausmaara"]))) {
        $maara_err = "Tilausmäärässä voi olla vain numeroita.";
    } elseif (trim($_POST["tilausmaara"]) <= 0) {
        $maara_err = "Tilausmäärä ei voi olla 0 tai negatiivinen numero.";
    } else {
        $maara = (int) trim($_POST["tilausmaara"]);
    }
    
    /*
     * Check the product number for tampering. Product number can only be an 
     * integer 1 or greater. No error is shown.
     */
    if(empty(trim($_POST["tilattavatuotenumero"]))) {
        $tuotenumero_err = "Tuotenumero vaaditaan.";
    } elseif ($div->just_numbers(trim($_POST["tilattavatuotenumero"]))) {
        $tuotenumero_err = "Tuotenumero voi olla vain numero.";
    } elseif (trim($_POST["tilattavatuotenumero"]) <= 0) {
        $tuotenumero_err = "Tuotenumero ei voi olla 0 tai negatiivinen numero";
    } else {
        $tuotenumero = (int) trim($_POST["tilattavatuotenumero"]);
    }
    
    /*
     * When adding a new product to the shopping cart or altering the amount of 
     * ordered items we need to check if the product is currently for sale. This 
     * includes checking product embargo date and that the product has not been 
     * pulled from sale. Also customer should not be able to order more products 
     * than the amount in the storage (not including products on display in the 
     * store).
     */
    if(empty($maara_err) && empty($tuotenumero_err)) {
        $sql = "SELECT tuotenumero, hinta, alv, alennus, varastossa, myyntialkaa, "
                . "vedettymyynnista, alealkaa, aleloppuu FROM tuotteet "
                . "WHERE tuotenumero = :tuotenumero";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue('tuotenumero', $tuotenumero, PDO::PARAM_INT);
        $stmt->execute();
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$product) {
            /*
             * This triggers if the product does not exist. Can happen if the 
             * user modifies the hidden product id value or if the product is 
             * deleted from the database between listing the product and adding 
             * it to the shopping cart.
             */
            $_SESSION["virheilmoitus"] = "Ei tuotetta tällä tuotenumerolla. Tämä "
                    . "voi johtua siitä, että tuote poistettiin tietokannasta "
                    . "tuotteen listaamisen ja ostoskoriin lisäämisen välillä.";
            header("location: errormessage.php");
            exit;
        } elseif ($maara > $product["varastossa"]) {
            /*
             * This triggers if there are less products in the warehouse than the 
             * user tries to add to the cart.
             */
            $_SESSION["virheilmoitus"] = "Tuotetta on varastossa on enään vain " . 
                    $product["varastossa"] . " kappaletta";
            header("location: errormessage.php");
            exit;
        } elseif (($product["vedettymyynnista"] !== NULL && $product["vedettymyynnista"] < 
                date("Y-m-d H:i:s")) || $product["myyntialkaa"] > date("Y-m-d H:i:s")) {
            /*
             * This triggers if the user tries to add a product to the cart that 
             * is not for sale yet or if the product has been removed from sale.
             */
            $_SESSION["virheilmoitus"] = "Tuotteen myynti ei ole vielä alkanut "
                    . "tai tuote on vedetty myynnistä.";
            header("location: errormessage.php");
            exit;
        } else {
            //Check first if shopping cart id is set.
            if (!isset($_SESSION["ostoskorinid"])) {
                if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
                    //Functions for a logged in user
                    $sql = 'INSERT INTO ostoskori (asnro, tila) VALUES (:asnro, :tila)';
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindValue('asnro', $_SESSION["asnro"], PDO::PARAM_INT);
                    $stmt->bindValue('tila', 0, PDO::PARAM_INT);
                    if ($stmt->execute() === FALSE) {
                        $_SESSION["virheilmoitus"] = "Ostoskorin luonnissa "
                                . "tapahtui virhe. Tuotetta ei lisätty "
                                . "ostoskoriin. Ole hyvä ja yritä uudelleen.";
                        header("location: errormessage.php");
                        exit;
                    } else {
                        $_SESSION["ostoskorinid"] = $pdo->lastInsertId();
                    }
                } else if (!isset($_SESSION["loggedin"]) || 
                        $_SESSION["loggedin"] !== true) {
                    //Functions for a user who has not logged in
                    $sql = 'INSERT INTO ostoskori (tila) VALUES (:tila)';
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindValue('tila', 0, PDO::PARAM_INT);
                    if ($stmt->execute() === FALSE) {
                        $_SESSION["virheilmoitus"] = "Ostoskorin luonnissa "
                                . "tapahtui virhe. Tuotetta ei lisätty "
                                . "ostoskoriin. Ole hyvä ja yritä uudelleen.";
                        header("location: errormessage.php");
                        exit;
                    } else {
                        $_SESSION["ostoskorinid"] = $pdo->lastInsertId();
                    }
                }
            }
            /*
             * Adds the product to the shopping cart or alters the amount of 
             * items in the shopping cart.
             */
            $sql = "SELECT maara FROM ostoskorinesine WHERE ostoskorinid = "
                    . ":ostoskorinid AND tuotenumero = :tuotenumero";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue('ostoskorinid', $_SESSION["ostoskorinid"], PDO::PARAM_INT);
            $stmt->bindValue('tuotenumero', $tuotenumero, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch();
            
            /*
             * If the product is already in the cart this changes the amount of 
             * products in the cart. Else adds the product as new to the cart.
             */
            if ($result) {
                $sql = "UPDATE ostoskorinesine SET maara = :maara WHERE "
                        . "ostoskorinid = :ostoskorinid AND "
                        . "tuotenumero = :tuotenumero";
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue('maara', $maara, PDO::PARAM_INT);
                $stmt->bindValue('ostoskorinid', $_SESSION["ostoskorinid"], PDO::PARAM_INT);
                $stmt->bindValue('tuotenumero', $tuotenumero, PDO::PARAM_INT);
                if ($stmt->execute()) {
                    $_SESSION["viimeinenlisattytuote"] = $tuotenumero;
                    $_SESSION["kappaletta"] = $maara;
                    header("location: onnistunuttilausmaaranmuuttaminen.php");
                    exit;
                }
            } else {
                //If the product is on sale, modify the price.
                if ($product["alealkaa"] < date("Y-m-d H:i:s") && 
                        $product["aleloppuu"] > date("Y-m-d H:i:s")) {
                    $product["hinta"] *= (1 - ($product["alennus"] / 100));
                }
                $sql = "INSERT INTO ostoskorinesine (tuotenumero, ostoskorinid, "
                        . "hinta, alv, alennus, maara) VALUES (:tuotenumero, "
                        . ":ostoskorinid, :hinta, :alv, :alennus, :maara)";
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue('tuotenumero', $tuotenumero, PDO::PARAM_INT);
                $stmt->bindValue('ostoskorinid', $_SESSION["ostoskorinid"], PDO::PARAM_INT);
                $stmt->bindValue('hinta', $product["hinta"], PDO::PARAM_STR);
                $stmt->bindValue('alv', $product["alv"], PDO::PARAM_INT);
                $stmt->bindValue('alennus', $product["alennus"], PDO::PARAM_INT);
                $stmt->bindValue('maara', $maara, PDO::PARAM_INT);
                if ($stmt->execute()) {
                    $_SESSION["viimeinenlisattytuote"] = $tuotenumero;
                    $_SESSION["kappaletta"] = $maara;
                    header("location: onnistunutlisaaminen.php");
                    exit;
                }
            }
        }
    }
}