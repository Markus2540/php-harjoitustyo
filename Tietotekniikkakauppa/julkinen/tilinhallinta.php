<?php
$title = "Tilitietojen hallinta";
$description = "Voit hallita tilisi tietoja täältä.";
$this_page = "tilinhallinta.php";


require_once '../perusosat/mainheader.php';
require_once '../moduulit/dbconnect.php';

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: etusivu.php");
    exit;       
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
    <h1>Tilitietojen tarkasteleminen ja muokkaaminen</h1>
    
    <p>Voit tarkastella ja muokata omia tietojasi tällä sivulla. Tilaushistoriasi 
        on näkyvillä sivun ylälaidassa olevan linkin takana.</p>
    
    <table>
        <tr>
            <td>Etunimi: </td>
            <td><?php echo puhdistus($_SESSION["etunimi"]); ?></td>
        </tr>
        <tr>
            <td>Sukunimi: </td>
            <td><?php echo puhdistus($_SESSION["sukunimi"]); ?></td>
        </tr>
        <tr>
            <td>Käyttäjätunnus: </td>
            <td><?php echo puhdistus($_SESSION["kayttajatunnus"]); ?></td>
        </tr>
        <tr>
            <td>Lähiosoite: </td>
            <td><?php echo puhdistus($_SESSION["lahiosoite"]); ?></td>
        </tr>
        <tr>
            <td>Postinumero: </td>
            <td><?php echo puhdistus($_SESSION["postinumero"]); ?></td>
        </tr>
        <tr>
            <td>Postitoimipaikka: </td>
            <td><?php echo puhdistus($_SESSION["postitoimipaikka"]); ?></td>
        </tr>
    </table>
    <br>
    <a href="osoitetietojenmuuttaminen.php">Päivitä osoitetiedot</a>
    <br><br>
    <a href="muutasalasana.php">Muuta salasanasi</a>
    
    <div class="push"></div>
</main>

<?php
require_once '../perusosat/mainfooter.php';