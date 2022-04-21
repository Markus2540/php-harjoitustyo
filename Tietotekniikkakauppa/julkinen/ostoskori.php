<?php
$title = "Ostoskori";
$description = "Voit tarkastella ostoskorissasi olevia tuotteita tällä sivulla.";
$this_page = "ostoskori.php";

require_once '../perusosat/mainheader.php';
require_once '../moduulit/dbconnect.php';

$poistettavaid = "";
$poistettavaid_err = "";
$total = 0;

if (isset($_POST['btnpoistaostoskorista'])) {
    //tarkistetaan piilotettu arvo poistettavaid.
    if(empty(trim($_POST["poistettavaid"]))) {
        $poistettavaid_err = "Poistettava id vaaditaan.";
    } elseif (preg_match('%[^0-9]%', trim($_POST["poistettavaid"]))) {
        $poistettavaid_err = "Poistettava id voi olla vain numero.";
    } elseif (trim($_POST["poistettavaid"]) <= 0) {
        $poistettavaid_err = "Poistettava id ei voi olla 0 tai negatiivinen numero";
    } else {
        $poistettavaid = (int) trim($_POST["poistettavaid"]);
    }
    
    if (empty($poistettavaid_err)) {
        $sql = "DELETE FROM ostoskorinesine WHERE ostoskorinid = ? AND id = ?";
        if ($stmt = $link->prepare($sql)) {
            $stmt->bind_param('ii', $param_ostoskorinid, $param_poistettavaid);
            $param_ostoskorinid = $_SESSION["ostoskorinid"];
            $param_poistettavaid = $poistettavaid;
            
            if ($stmt->execute()) {
                header("location: ostoskori.php");
                exit;
            }
            $stmt->close();
        }
        $link->close();
    }
}
?>

<main>
    <h1>Ostoskori</h1>
    
    <p>Tällä sivulla voit tarkastella ostoskoriasi, poistaa tuotteita ostoskorista 
        tai siirtyä kassalle hyväksymään ja maksamaan tilauksesi. Selkeyden vuoksi 
        tuotteiden tilausmäärä tulee muuttaa tuotesivulta, johon on kätevä linkki 
        ostoskorin Tuotenimi-kentässä.</p>
    
    <?php
    //Tällä tehdään ostoskoritaulukko jos ostoskorinid on luotu. 
    //Tämän voisi kyllä tehdä paremman näköiseksi, mutta olkoon tässä näin.
    if (!isset($_SESSION["ostoskorinid"])) {
        echo "<p>Ostoskoria ei ole vielä luotu.</p>";
    } else if (isset($_SESSION["ostoskorinid"])) { ?>
    <div class="shoppingcartdiv">
        <table class="shoppingcarttable">
            <thead>
                <tr>
                    <th>Valmistaja</th>
                    <th>Tuotenimi</th>
                    <th>Määrä</th>
                    <th>Hinta</th>
                    <th>Poisto</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (isset($_SESSION["ostoskorinid"])) {
                $sql = "SELECT tuotteet.tuotenumero, tuotteet.valmistaja, "
                        . "tuotteet.tuotenimi, tuotteet.hinta, tuotteet.alennus, "
                        . "tuotteet.alealkaa, tuotteet.aleloppuu, ostoskorinesine.maara, "
                        . "ostoskorinesine.id FROM ostoskorinesine INNER JOIN tuotteet "
                        . "ON ostoskorinesine.tuotenumero = tuotteet.tuotenumero "
                        . "WHERE ostoskorinesine.ostoskorinid = ?";
                if ($stmt = $link->prepare($sql)) {
                    $stmt->bind_param('i', $param_ostoskorinid);
                    $param_ostoskorinid = $_SESSION["ostoskorinid"];

                    if ($stmt->execute()) {
                        $stmt->bind_result($tuotenumero, $valmistaja, $tuotenimi, 
                                $hinta, $alennus, $alealkaa, $aleloppuu, $maara, $id);

                        while ($stmt->fetch()) { ?>
                <tr>
                    <td><?php echo puhdistus($valmistaja); ?></td>
                    <td><a href="tuote.php?id=<?php echo puhdistus($tuotenumero); ?>"><?php
                    echo puhdistus($tuotenimi); ?></a></td>
                    <td><?php echo puhdistus($maara); ?></td>
                    <td><?php
                        if ($alennus > 0 && date("Y-m-d H:i:s") > $alealkaa && 
                                date("Y-m-d H:i:s") < $aleloppuu) { $hinta *= (1 - 
                                        ($alennus / 100)); 
                                echo number_format(puhdistus($hinta * $maara), 2, ',', ' '); 
                                $total += $hinta * $maara;
                        } else {
                            echo number_format(puhdistus($hinta * $maara), 2, ',', ' ');
                            $total += $hinta * $maara;
                        }?>€
                    </td>
                    <td>
                        <form action="ostoskori.php" method="post">
                            <input type="hidden" name="poistettavaid" value=
                                   "<?php echo puhdistus($id); ?>">
                            <input type="submit" value="Poista" name="btnpoistaostoskorista">
                        </form>
                    </td>
                </tr>
                <?php
                        }
                        ?>
                <tr>
                    <td>Yhteensä</td>
                    <td></td>
                    <td></td>
                    <td><?php echo number_format(puhdistus($total), 2, ',', ' '); ?>€</td>
                    <td></td>
                </tr>
                <?php
                    }
                }
            }
                ?>
            </tbody>
        </table>
    </div>
    <?php
        
        if ($stmt->num_rows > 0) {
            echo "<p><a href=\"tilauksenvarmistus.php\">Tästä kassalle</a></p>";
        }
        $stmt->close();
        $link->close();
    }
    ?>
    
    <div class="push"></div>
</main>

<?php

require_once '../perusosat/mainfooter.php';
?>