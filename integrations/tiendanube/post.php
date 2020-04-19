<?php

// First, include Requests
include('../../assets/libraries/Requests.php');

// Next, make sure Requests can load internal classes
Requests::register_autoloader();



//Seteamos el cuerpo del mensaje que vamos a enviar luego
$DataBody = array(
        'code' => $mCode, // Le mandamos el code que recibimos antes
        'grant_type' => "authorization_code", // Esto lo requiere TiendaNube
        'client_secret' => "CRLWSO1XkkV7OlZ65bHFLSzPS0g6EwAbVQuDmYMWPIwiYWrW", // CLient_secret de la app
        'client_id' => "1516" // ID de la app
);

// Avisamos que vamos a enviar contenido en jSon. y contamos los caracteres de $payload
$Options = array(
    'Content-Type' => 'application/json',
    'Content-Length:' => strlen($DataBody)
);

// Now let's make a request!
$request = Requests::post('https://socialroot.requestcatcher.com/', $Options, $DataBody);

// Check what we received
var_dump($request);

?>
