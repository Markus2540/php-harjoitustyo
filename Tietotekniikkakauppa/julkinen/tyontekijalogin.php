<?php
session_start();
function puhdistus($text) {
    return htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
require_once '../moduulit/dbconnect.php';
require_once '../classes/DataInputValidation.php';
$this_page = "tyontekijalogin.php";

$tunnus = $tunnussana = "";
$tunnus_err = $tunnussana_err = "";
$div = new DataInputValidation();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //Tarkistetaan käyttäjätunnus
    if(empty(trim($_POST["tunnus"]))) {
        $tunnus_err = "Ole hyvä ja anna käyttäjätunnus.";
    } elseif ($div->validate_username(trim($_POST["tunnus"]))) {
        $tunnus_err = "Käyttäjätunnuksessa voi olla vain kirjaimia, numeroita ja alaviivoja.";
    } else {
        $tunnus = trim($_POST["tunnus"]);
    }
    
    if(empty(trim($_POST["tunnussana"]))) {
        $tunnussana_err = "Ole hyvä ja anna salasana.";
    } else {
        $tunnussana = trim($_POST["tunnussana"]);
    }
    
    if (empty($tunnus_err) && empty($tunnussana_err)) {
        $sql = "SELECT id, etunimi, sukunimi, kayttajatunnus, salasana, "
                . "vaaria_salasanayrityksia, viimeinen_vaara_salasana FROM "
                . "henkilokunta WHERE kayttajatunnus = ?";
        $stmt = $link->prepare($sql);
        $stmt->bind_param('s', $param_kayttajatunnus);
        $param_kayttajatunnus = $tunnus;
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows == 1) {
            $stmt->bind_result($id, $etunimi, $sukunimi, $kayttajatunnus, 
                    $hashed_password, $vaaria_salasanayrityksia, $viimeinen_vaara_salasana);
            $stmt->fetch();
            /*
             * Add brute force protection.
             */
            if ($vaaria_salasanayrityksia > 4 && (strtotime(date("Y-m-d H:i:s")) < 
                    strtotime($viimeinen_vaara_salasana) + 300)) {
                $kirjautumisvirheilmoitus = "Voit yrittää kirjautua tilille "
                        . "uudelleen 5 minuuttia edellisen epäonnistuneen "
                        . "sisäänkirjautumisyrityksen jälkeen.";
            } else {
                if (password_verify($tunnussana, $hashed_password)) {
                    session_regenerate_id();

                    $_SESSION["adminloggedin"] = true;
                    $_SESSION["id"] = $id;
                    $_SESSION["etunimi"] = $etunimi;
                    $_SESSION["sukunimi"] = $sukunimi;
                    $_SESSION["kayttajatunnus"] = $kayttajatunnus;
                    
                    //Nollataan virheellisten salasanayritysten määrä ja 
                    //laitetaan viimeisen väärän salasanan aika arvoon NULL.
                    $sql = "UPDATE henkilokunta SET vaaria_salasanayrityksia = 0, "
                            . "viimeinen_vaara_salasana = NULL WHERE id = ?";
                    $stmt = $link->prepare($sql);
                    $stmt->bind_param('i', $param_id);
                    $param_id = $_SESSION["id"];
                    $stmt->execute();

                    header("location: ../admin/index.php");
                    exit;
                } else {
                    //Kirjaa epäonnistunut kirjautumisyritys lisäämällä perättäisten 
                    //väärien salasanayritysten määrään 1 ja kirjaamalla tämän 
                    //epäonnistuneen yrityksen datetime käyttäjätunnusta 
                    //vastaavaan riviin.
                    $sql = "UPDATE henkilokunta SET vaaria_salasanayrityksia = ?, "
                            . "viimeinen_vaara_salasana = ? WHERE id = ?";
                    $stmt = $link->prepare($sql);
                    $stmt->bind_param('isi', $param_vaaria_salasanayrityksia, 
                            $param_viimeinen_vaara_salasana, $param_id);
                    $param_vaaria_salasanayrityksia = ++$vaaria_salasanayrityksia;
                    $param_viimeinen_vaara_salasana = date("Y-m-d H:i:s");
                    $param_id = $id;
                    $stmt->execute();
                    
                    $kirjautumisvirheilmoitus = "Virheellinen salasana tai käyttäjätunnus.";
                }
            }
        } else {
            $kirjautumisvirheilmoitus = "Virheellinen salasana tai käyttäjätunnus.";
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
        <meta name="author" content="Tekijä">
        <title>Työntekijän sisäänkirjautuminen</title>
        <meta name="description" content="Työntekijän sisäänkirjautuminen">
        <link rel="stylesheet" href="css/tyontekija.css">
    </head>
    <body>
        <main>
            <h1>Työntekijän sisäänkirjautuminen</h1>
            
            <form action="<?php echo puhdistus($this_page); ?>" method="post">
                <table>
                    <tr>
                        <td class="oikealaita">Käyttäjätunnus:</td>
                        <td><input type="text" name="tunnus"></td>
                    </tr>
                    <tr>
                        <td class="oikealaita">Salasana:</td>
                        <td><input type="password" name="tunnussana"></td>
                    </tr>
                </table>
                <br>
                <input type="submit" value="Kirjaudu sisään" class="keskita">
            </form>
            <?php
            if (isset($kirjautumisvirheilmoitus)) {
                echo "<p id=\"kirjautumisvirheilmoitus\">" . 
                        puhdistus($kirjautumisvirheilmoitus) . "</p>";
            }
            ?>
        </main>
    </body>
</html>