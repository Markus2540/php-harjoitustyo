<?php
session_start();

require_once '../moduulit/perustoimintoja.php';
require_once '../classes/DataInputValidation.php';
if (!isset($div)) {
    $div = new DataInputValidation();
}


?>
<!DOCTYPE html>
<html lang="fi">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="author" content="Markus2540">
        <meta name="description" content="<?php echo puhdistus($description); ?>">
        <link rel="stylesheet" href="../julkinen/css/main.css">
        <title><?php echo puhdistus($title); ?></title>
        <meta http-equiv="Content-Security-Policy" content="default-src 'self';">
        <script defer src="../julkinen/javascript/perustoiminnot.js"></script>
    </head>
    <body>
        <header>
            <div id="innerheader">
                <div class="dropdown">
                    <button id="showcategories">Navigointi</button>
                    <div id="categories" class="dropdowncontent">
                        <a href="etusivu.php">Etusivu</a>
                        <a href="tuotteet.php?kategoria=Esikasatut">Esikasatut</a>
                        <a href="tuotteet.php?kategoria=Kotelot">Kotelot</a>
                        <a href="tuotteet.php?kategoria=Prosessorit">Prosessorit</a>
                        <a href="tuotteet.php?kategoria=Emolevyt">Emolevyt</a>
                        <a href="tuotteet.php?kategoria=Muistit">Muistit</a>
                        <a href="tuotteet.php?kategoria=Naytonohjaimet">Näytönohjaimet</a>
                        <a href="tuotteet.php?kategoria=Kiintolevyt">Kiintolevyt</a>
                        <a href="tuotteet.php?kategoria=Virtalahteet">Virtalähteet</a>
                        <a href="tuotteet.php?kategoria=Asemat">Asemat</a>
                        <a href="tuotteet.php?kategoria=Jaahdytys">Jäähdytys</a>
                        <a href="tuotteet.php?kategoria=Naytot">Näytöt</a>
                        <a href="tuotteet.php?kategoria=Puhelimet+ja+tarvikkeet">Puhelimet ja tarvikkeet</a>
                        <a href="tuotteet.php?kategoria=Tabletit">Tabletit ja tarvikkeet</a>
                    </div>
                </div>
                <div id="searchbar">
                    <form action="etsi.php" method="get">
                        <ul id="headersearchbar">
                            <li>
                                <input type="text" placeholder="Etsi..." name="hakusana" id="search">
                            </li>
                            <li>
                                <input type="submit" value="Etsi" id="headersearchbutton">
                            </li>
                        </ul>
                    </form>
                    <div id="ehdotukset">
                        <ul id="ehdotuslista"></ul>
                    </div>
                </div>
                
                <div class="dropdown">
                    <button id="showloginandcart">Tili/kori</button>
                    <div id="kirjautumisikkuna" class="logincontent">
                        <?php
                        if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {

                            require_once '../moduulit/kirjautumistoiminto.php';
                            //Kirjautumaton asiakas näkee tämän toiminnon rakentaman kirjautumisikkunan.
                            echo "<h2 class=\"logincontent\">Kirjaudu</h2>";
                            echo "<form action=\"" . puhdistus($this_page) . "\" method=\"post\" class=\"logincontent\">";
                            //echo "<form action=\"\" method=\"post\" class=\"logincontent\">";
                            echo "<label for=\"kayttajatunnus\" class=\"logincontent\">Käyttäjätunnus:";
                            echo "<input type=\"text\" id=\"kayttajatunnus\" name=\"kayttajatunnus\" class=\"logincontent\"></label>";
                            echo "<label for=\"salasana\" class=\"logincontent\">Salasana:";
                            echo "<input type=\"password\" id=\"salasana\" name=\"salasana\" class=\"logincontent\"></label><br><br>";
                            echo "<input type=\"submit\" value=\"Kirjaudu\" name=\"btnmainlogin\"><br><br>";
                            echo "<a href=\"tunnustenluonti.php\">Luo tunnukset</a>";
                            echo "</form>";
                            echo "<p><a href=\"ostoskori.php\">Ostoskori</a></p>";
                        } else {
                            //Tämä näkymä rakennetaan kirjautuneelle asiakkaalle.
                            echo "<h2 class=\"logincontent\">" . puhdistus($_SESSION["sukunimi"]) . " " . puhdistus($_SESSION["etunimi"]) . "</h2>";
                            echo "<p><a href=\"tilinhallinta.php\">Tilinhallinta</a></p>";
                            echo "<p><a href=\"ostoskori.php\">Ostoskori</a></p>";
                            echo "<p><a href=logout.php>Kirjaudu ulos</a></p>";
                        }
                        ?>
                    </div>
                </div>
                
            </div>
        </header>