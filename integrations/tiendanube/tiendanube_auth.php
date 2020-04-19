<?php

// First, include Requests
include('../../assets/libraries/Requests.php');

include_once '../con_mysql.php';
include_once './tiendanube_methods.php';

// Next, make sure Requests can load internal classes
Requests::register_autoloader();

//Recibimos el code desde TiendaNube
$mCode = $_GET["code"];
// Definimos la URL para tomar el token
$Url = "https://www.tiendanube.com/apps/authorize/token";

//Chequeamos si NO esta seteado el USER ID
// Leemos las cookies de USER_ID y WEBHOOK creadas en Settigs-method.php.
if(!isset($_GET["user_id"])){
  ?>
  <script>
  var user_id = document.cookie.split("user:")[1].split(",")[0];
  var webhook = document.cookie.split("user:")[1].split(",")[1].split("webhook:")[1].split(",")[0];
  location.href = "https://socialroot.io/integrations/tiendanube/tiendanube_auth.php?code=<?php echo $mCode ?>&user_id="+user_id+"&callback="+webhook;
  </script>

  <?php
  exit();
}

  //Seteamos el cuerpo del mensaje que vamos a enviar luego
$Body = array(
        'code' => $mCode, // Le mandamos el code que recibimos antes
        'grant_type' => "authorization_code", // Esto lo requiere TiendaNube
        'client_secret' => "CRLWSO1XkkV7OlZ65bHFLSzPS0g6EwAbVQuDmYMWPIwiYWrW", // CLient_secret de la app
        'client_id' => "1516" // ID de la app
);


  // Avisamos que vamos a enviar contenido en jSon. y contamos los caracteres de $payload
$Headers = array(
      'Content-Type' => 'application/json'
);

//Enviamos todo y recibimos el Access_token y el Store_ID
$TokenStore_Response = Requests::post($Url, $Headers, $Body);

print_r($TokenStore_Response);

// Decodificamos los datos que entran en jSon y les asignamos la variable DataResponseDecode
$Decoded_TokenStore_Response = json_decode($Token_and_StoreID_Response, true);

print_r($Decoded_TokenStore_Response);

// ACA HAY QUE CHEQUEAR SI EXISTE Y CORTAR.
// ACA HAY QUE CHEQUEAR SI EXISTE Y CORTAR.


// Llamamos al objeto ConnectionMySQL incluido en con_mysql.php

// Crea el Usuario en la tabla TiendaNube y agrega los datos de StoreID y AccessToken.
//SetWebHookOnTiendaNube($DataReponseDecode["user_id"], $DataReponseDecode["access_token"], $_GET["callback"], $PreferenceToUser);

?>
