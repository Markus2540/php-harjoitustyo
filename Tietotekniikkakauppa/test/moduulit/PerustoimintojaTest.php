<?php
/*
 * Better improved versions of these exist in DataInputValidation.php and 
 * DataInputValidationTest.php.
 */
Use PHPunit\Framework\TestCase;

class PerustoimintojaTest extends TestCase {
    public function testPasswordStrength() {
        require 'moduulit/perustoimintoja.php';
        
        $this->assertEquals(0, vahva_salasana(";"));
        $this->assertEquals(1, vahva_salasana("a"));
        $this->assertEquals(2, vahva_salasana("aA"));
        $this->assertEquals(3, vahva_salasana("Salasana12"));
        $this->assertEquals(4, vahva_salasana("Salasana12!"));
        $this->assertEquals(4, vahva_salasana("Salasana12!;"));
    }
    
    public function test_validate_date_of_datetime_local() {
        $this->assertEquals(true, validate_date_of_datetime_local("2018-06-12T19:30"));
        $this->assertEquals(true, validate_date_of_datetime_local("2024-02-29T20:00"));
        $this->assertEquals(false, validate_date_of_datetime_local("2022-02-29T10:00"));
    }
}