<?php
/*
 * Most of these have been replaced and improved in DataInputValidation.php.
 */

function puhdistus($text) {
    return htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function vahva_salasana($text) {
    $salasanan_vahvuus = 0;
    if (preg_match('%[a-z]%', $text)) {$salasanan_vahvuus++;}
    if (preg_match('%[A-Z]%', $text)) {$salasanan_vahvuus++;}
    if (preg_match('%[0-9]%', $text)) {$salasanan_vahvuus++;}
    if (preg_match('%[!"#¤\%&/()=?]%', $text)) {$salasanan_vahvuus++;}
    return $salasanan_vahvuus;
}

//Seuraavia funktioita käytetään lomakkeissa käytettävän datetime-localin 
//oikeellisuuden tarkistamiseen.
function validate_date_of_datetime_local ($given_datetime) {
    $date_array = explode('-', str_replace('T', '-', $given_datetime));
    return checkdate($date_array[1], $date_array[2], $date_array[0]);
}
function validate_time_of_datetime_local ($given_datetime) {
    $time_array = explode('-', str_replace(['T', ':'], '-', $given_datetime));
    if ($time_array[3] < 0 || $time_array[3] > 23) {return false;}
    if ($time_array[4] < 0 || $time_array[4] > 59) {return false;}
    if (count($time_array) !== 5) {return false;}
    //if ($time_array[5] < 0 || $time_array[5] > 59) {return false;} //Datetime-local YYYY-MM-DDThh:mm
    return true;
}
function validate_datetime ($given_datetime) {
    if ((validate_date_of_datetime_local($given_datetime) === true) && 
            (validate_time_of_datetime_local($given_datetime) === true)) {
        return true;
    } else {
        return false;
    }
}

//Seuraava funktio tarkastaa syötetyn rahamäärän oikean muodon. Rahamäärä on 
//oikean muotoinen, jos siinä on vain numeroita ja maksimissaan yksi piste ja 
//jos syötetyssä summassa on piste sen paikka on ennen toiseksi viimeisintä 
//numeroa, eli 10.55 olisi oikein kirjoitettu pisteellinen rahasumma.
function validate_amount_of_money ($value_to_validate) {
    if (!preg_match('%[^0-9.]%', $value_to_validate) && 
            substr_count($value_to_validate, '.') < 2) {
        if (substr_count($value_to_validate, '.') === 1 && 
                strpos($value_to_validate, '.') === (strlen($value_to_validate) - 3)) {
            return true;
        } elseif (substr_count($value_to_validate, '.') === 1 && 
                strpos($value_to_validate, '.') !== (strlen($value_to_validate) - 3)) {
            return false;
        } else {
            return true;
        }
    }
}

//https://stackoverflow.com/a/14467470
function nl2p($string) {
    $paragraphs = '';
    
    foreach (explode("\n", $string) as $line) {
        if (trim($line)) {
            $paragraphs .= "<p>" . $line . "</p>";
        }
    }

    return $paragraphs;
}