<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$url = "json/conversaciones/". "prueba1" . ".json";
if (filesize($url) <= 0){
	echo "Vacio";
	exit();
}

$file = fopen($url, "r");
$json = fread($file, filesize($url));
fclose($file);
$json = json_decode($json, true);

foreach ($json as $key => $value) {

}



?>