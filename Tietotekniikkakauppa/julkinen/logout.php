<?php
session_start();

//Session muuttujien nollaaminen.
$_SESSION = array();

//Session tuhoaminen.
session_destroy();

//Uudelleenohjaus.
header("location: etusivu.php");
exit;