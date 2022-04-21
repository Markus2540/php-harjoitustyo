<?php

class DataInputValidation {
    /*
     * Many if not all of the data validation functions in here returns false when
     * the given data is valid. True is returned when the given value is invalid.
     */
    
    
    /**
     * Returns true if password contains any other characters than the ones 
     * defined inside the following regular expression. Returns false when the 
     * input is valid.
     * 
     * preg_match('%[^a-zA-Z0-9!"#¤\%&/()=?]%', $password)
     * @assert ("abcDEF123!\"#¤%&/()=?") === false
     * @assert ("Salasana12!") === false
     * @assert ("Salasana12;") === true
     * @assert ("!\"#¤%&/()=?") === false
     * @assert ("@£\$€{[]}\\") === true
     * @assert ("Salasana12\\") === true
     */
    public function validate_password($password) {
        if (preg_match('%[^a-zA-Z0-9!"#¤\%&/()=?]%', $password)) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Used to determine the complexity of the password. Returns an integer.
     * @assert (";") === 0
     * @assert ("a") === 1
     * @assert ("aA") === 2
     * @assert ("Salasana12") === 3
     * @assert ("Salasana12!") === 4
     * @assert ("Salasana12!;") === 4
     */
    public function password_strength($password) {
        $password_strength = 0;
        if (preg_match('%[a-z]%', $password)) {$password_strength++;}
        if (preg_match('%[A-Z]%', $password)) {$password_strength++;}
        if (preg_match('%[0-9]%', $password)) {$password_strength++;}
        if (preg_match('%[!"#¤\%&/()=?]%', $password)) {$password_strength++;}
        return $password_strength;
    }
    
    /**
     * Returns false when the given value is valid.
     * 
     * preg_match('%[^a-zA-Z0-9_]%', $username)
     * @assert ("John_Doe_42") === false
     * @assert ("John.Doe") === true
     */
    public function validate_username($username) {
        if (preg_match('%[^a-zA-Z0-9_]%', $username)) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Returns false when the given value is valid.
     * 
     * preg_match('%[^A-ZÅÄÖa-zåäö\' -]%', $name)
     * @assert ("Mäkinen") === false
     * @assert ("O'Neill") === false
     * @assert ("Marja-Leena") === false
     * @assert ("Jaakko Ilmari") === false
     * @assert ("John42") === true
     * @assert ("Günther") === true
     */
    public function validate_name($name) {
        if (preg_match('%[^A-ZÅÄÖa-zåäö\' -]%', $name)) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Used to validate address. Returns false when the given value is valid.
     * 
     * preg_match('%[^A-ZÅÄÖa-zåäö0-9 -]%', $address)
     * @assert ("Kotikatu 7A23") === false
     * @assert ("Järvikatu") === false
     * @assert ("Katu 7-9") === false
     * @assert ("Kotikatu 7@23") === true
     */
    public function validate_address($address) {
        if (preg_match('%[^A-ZÅÄÖa-zåäö0-9 -]%', $address)) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Used for validating input for post office. Returns false when given input 
     * is valid in terms of preg_match.
     * 
     * preg_match('%[^A-ZÅÄÖa-zåäö -]%', $p_o)
     * @assert ("Suur-Miehikkälä") === false
     * @assert ("Lehtimäki kk") === false
     * @assert ("5") === true
     */
    public function validate_post_office($p_o) {
        if (preg_match('%[^A-ZÅÄÖa-zåäö -]%', $p_o)) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Returns false when given input contains only numbers.
     * 
     * preg_match('%[^0-9]%', $number)
     * 
     * @assert (42) === false
     * @assert ("42") === false
     * @assert ("00100") === false
     * @assert ("number42") === true
     */
    public function just_numbers($number) {
        if (preg_match('%[^0-9]%', $number)) {
            return true;
        } else {
            return false;
        }
    }
    
    /*
     * This function validates datetime-local format but it accepts values that aren't 
     * real dates and times.
     */
    /**
     * @assert ("1970-01-01T00:00") === false
     * @assert ("2020-50-50T99:99") === false
     * @assert ("2020-50-50t99:99") === true
     */
    /*public function validate_datetime_local_test($datetime_local) {
        if (!preg_match('%[0-9]{4}-{1}[0-9]{2}-{1}[0-9]{2}T{1}[0-9]{2}:{1}[0-9]{2}%', $datetime_local)) {
            return true;
        } else {
            return false;
        }
    }*/
    
    /*
     * The following functions are used for testing datetime-local values.
     */
    /**
     * @assert (01, 01, 1970) === true
     * @assert ("01", "01", "1970") === true
     * @assert (02, 29, 2024) === true
     * @assert (02, 29, 2022) === false
     * @assert (00, 12, 2022) === false
     */
    /*function validate_date_of_datetime_local ($mm, $dd, $yyyy) {
        return checkdate($mm, $dd, $yyyy);
    }*/
    
    /**
     * Returns false when the given value is a valid datetime-local value.
     * @assert ("1970-01-01T00:00") === false
     * @assert ("2024-02-29T23:59") === false
     * @assert ("2022-04-04T18:03") === false
     * @assert ("2022-13-04T12:12") === true
     * @assert ("2022-04-32T12:12") === true
     * @assert ("2022-04-04T24:12") === true
     * @assert ("2022-04-04T23:60") === true
     * @assert ("2022-04-04t12:12") === true
     * @assert ("2022-04-04T12:-12") === true
     */
    public function validate_datetime_local ($datetime_local) {
        $datetime_local_array = explode('-', str_replace(['T', ':'], '-', $datetime_local));
        if (count($datetime_local_array) !== 5) {return true;}
        
        $only_numbers = true;
        foreach ($datetime_local_array as $int) {
            if (preg_match('%[^0-9]%', $int)) {
                $only_numbers = false;
            }
        }
        
        //Validate time.
        if ($only_numbers === true) {
            if ($datetime_local_array[3] < 0 || $datetime_local_array[3] > 23) {
                return true;
            }
            if ($datetime_local_array[4] < 0 || $datetime_local_array[4] > 59) {
                return true;
            }
        } else {
            return true;
        }
        
        //Validate date.
        if ((checkdate($datetime_local_array[1], $datetime_local_array[2], 
                $datetime_local_array[0]) === true)) {
            return false;
        } else {
            return true;
        }
    }
    
    /**
     * Returns false when given value is valid monetary value.
     * 
     * !preg_match('%[^0-9.]%', $value_to_validate)
     * 
     * @assert ("15") === false
     * @assert ("15.5") === false
     * @assert ("15.50") === false
     * @assert ("15,50") === true
     * @assert ("test") === true
     * @assert ("15.5.5") === true
     * @assert ("15.555") === true
     */
    public function validate_money ($value_to_validate) {
        if (!preg_match('%[^0-9.]%', $value_to_validate) && 
                substr_count($value_to_validate, '.') < 2) {
            if (substr_count($value_to_validate, '.') === 0) {return false;}
            if (substr_count($value_to_validate, '.') === 1 && 
                    (strpos($value_to_validate, '.') === strlen($value_to_validate) - 3 || 
                    strpos($value_to_validate, '.') === strlen($value_to_validate) - 2)) {
                return false;
            } else {
                return true;
            }
        } else {
            return true;
        }
    }
    
    /**
     * Returns false when given string contains only letters and spaces.
     * 
     * preg_match('%[^a-zA-Z ]%', $text)
     * 
     * @assert ("Just text") === false
     * @assert ("Text and 2") === true
     */
    public function just_text ($text) {
        if (preg_match('%[^a-zA-Z ]%', $text)) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * preg_match('%[^a-zåäöA-ZÅÄÖ0-9 ,.@-]%', $search_term)
     * 
     * @assert ("Product 3,5 @ 5-6") === false
     * @assert ("Product=7") === true
     */
    public function product_name ($search_term) {
        if (preg_match('%[^a-zåäöA-ZÅÄÖ0-9 ,.@-]%', $search_term)) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Returns false if given value is valid.
     * @assert ("Puhelimet ja tarvikkeet") === false
     * @assert ("Puhelimet_ja_tarvikkeet") === true
     */
    public function validate_category ($category) {
        if (preg_match('%[^a-zåäöA-ZÅÄÖ0-9 ]%', $category)) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Returns false if product description is valid.
     * 
     * preg_match('%[^a-zåäöA-ZÅÄÖ0-9 ,.:®\s-]%', $description)
     * @assert ("Kuvaus, ®-") === false
     * @assert ("Kuvaus %") === true
     */
    public function validate_description ($description) {
        if (preg_match('%[^a-zåäöA-ZÅÄÖ0-9 ,.:®\s-]%', $description)) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * When pictures are added to the database their filenames are saved as a string
     * with letters, numbers, dots and commas. Returns false when given string is 
     * valid.
     * 
     * preg_match('%[^a-zA-Z0-9.,]%', $string)
     * @assert ("kuva1.jpg,kuva2.jpg") === false
     * @assert ("kuva?.jpg,kuva2.jpg") === true
     */
    public function validate_picture_string ($string) {
        if (preg_match('%[^a-zA-Z0-9.,]%', $string)) {
            return true;
        } else {
            return false;
        }
    }
}