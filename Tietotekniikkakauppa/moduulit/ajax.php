<?php
/*
 * Käytin aluksi netistä löytämääni JQueryllä toteutettua Live Search-toimintoa, 
 * mutta korvasin sen tavallisella Javascriptillä, koska JQueryyn olen tutustunut 
 * vain parin tunnin ajan.
 */

require_once '../moduulit/pdoconnect.php';

if (isset($_POST["search"]) && !empty(trim($_POST["search"]))) {
    $search_term = trim($_POST["search"]);
    
    if (preg_match("%[^a-zåäöA-ZÅÄÖ0-9 ]%", $search_term)) {
            echo json_encode("Käytä hakukentässä vain kirjaimia, numeroita ja "
                    . "välilyöntejä.", JSON_UNESCAPED_UNICODE);
        } else {
            $sql = "SELECT tuotenumero, tuotenimi, valmistaja, hinta, alennus, "
                    . "alealkaa, aleloppuu, kategoria, varastossa, myymalassa "
                    . "FROM tuotteet WHERE (vedettymyynnista IS NULL || "
                    . "(vedettymyynnista > CURRENT_TIMESTAMP)) && (tuotenimi "
                    . "LIKE :tuotenimi || valmistaja LIKE :valmistaja || "
                    . "kategoria LIKE :kategoria) LIMIT 5";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue('tuotenimi', "%" . $search_term . "%", PDO::PARAM_STR);
            $stmt->bindValue('valmistaja', "%" . $search_term . "%", PDO::PARAM_STR);
            $stmt->bindValue('kategoria', "%" . $search_term . "%", PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($result as &$rivi) {
                if ($rivi["alennus"] > 0 && $rivi["alealkaa"] < date("Y-m-d H:i:s") && 
                        $rivi["aleloppuu"] > date("Y-m-d H:i:s")) {
                    $rivi["hinta"] *= (1 - ($rivi["alennus"] / 100));
                }
                unset($rivi["alealkaa"]);
                unset($rivi["aleloppuu"]);
            }
            unset($rivi);

            echo json_encode($result, JSON_UNESCAPED_UNICODE);
        }
}


/*function puhdistus($text) {
    return htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}*/

//require_once '../moduulit/dbconnect.php';

/*if (isset($_POST["search"])) {
    $name = puhdistus($_POST["search"]);
    if (preg_match('%[^a-zåäöA-ZÅÄÖ0-9 ]%', $name)) {
        ?>
    <ul>
        <li><?php echo "Laiton merkki hakukentässä."; ?></li>
        <?php
    } else {
        $sql = "SELECT tuotenumero, tuotenimi, valmistaja, hinta, alennus, alealkaa, aleloppuu, kategoria, varastossa, myymalassa, kuvat, myyntialkaa, tuotekuvaus FROM tuotteet WHERE "
            . "(vedettymyynnista IS NULL || (vedettymyynnista > date(\"Y-m-d H:i:s\"))) && (tuotenimi LIKE ? || valmistaja LIKE ? || kategoria LIKE ?) LIMIT 5";
        $stmt = $link->prepare($sql);
        $stmt->bind_param('sss', $param_hakutermi, $param_hakutermi, $param_hakutermi);
        $param_hakutermi = "%" . $name . "%";
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($tuotenumero, $tuotenimi, $valmistaja, $hinta, $alennus, $alealkaa, $aleloppuu, $kategoria, $varastossa, $myymalassa, $kuvat, $myyntialkaa, $tuotekuvaus);
            echo "<ul>";
            while ($stmt->fetch()) {
                $kuvat = explode(",", $kuvat);
                ?>
    <li onclick='fill("<?php echo $tuotenimi; ?>")'>
        <a>
            <?php echo $tuotenimi; ?>
    </li></a>
        <?php
            }
        }
    }
}*/