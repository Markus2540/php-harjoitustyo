<?php
$title = "Virheilmoitus";
$description = "Tämä sivu tulee näkyviin virheen sattuessa.";
$this_page = "errormessage.php";

require_once '../perusosat/mainheader.php';
require_once '../moduulit/dbconnect.php';

if (!isset($_SESSION["virheilmoitus"]) || empty($_SESSION["virheilmoitus"])) {
    header("location: etusivu.php");
    exit;
}
?>

<main>
    <p><?php echo puhdistus($_SESSION["virheilmoitus"]); ?></p>
    
    <div class="push"></div>
</main>

<?php
require_once '../perusosat/mainfooter.php';
$_SESSION["virheilmoitus"] = [];