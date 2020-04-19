<?php

// First, include Requests
include('../../assets/libraries/Requests.php');

// Next, make sure Requests can load internal classes
Requests::register_autoloader();

$mCode = "acavaelcodedeTN";

//Seteamos el cuerpo del mensaje que vamos a enviar luego
$data = array(
        'code' => $mCode, // Le mandamos el code que recibimos antes
        'grant_type' => "authorization_code", // Esto lo requiere TiendaNube
        'client_secret' => "CRLWSO1XkkV7OlZ65bHFLSzPS0g6EwAbVQuDmYMWPIwiYWrW", // CLient_secret de la app
        'client_id' => "1516" // ID de la app
);

// Avisamos que vamos a enviar contenido en jSon. y contamos los caracteres de $payload
$headers = array(
    'Content-Type' => 'application/json'
);

$url = 'https://socialroot.requestcatcher.com/';
$headers = array('Content-Type' => 'application/json');

// Now let's make a request!
$request = Requests::post($url, $headers, $data);

// Check what we received
var_dump($request);

?>
