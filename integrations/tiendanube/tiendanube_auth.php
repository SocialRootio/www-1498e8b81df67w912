<?php

// First, include Requests
include('../../assets/libraries/Requests.php');

include_once '../con_mysql.php';
include_once './tiendanube_methods.php';

// Next, make sure Requests can load internal classes
Requests::register_autoloader();

//Recibimos el code desde TiendaNube
$mCode = $_GET["code"];


//Chequeamos si NO esta seteado el USER ID
// Leemos las cookies de USER_ID y WEBHOOK creadas en Settigs-method.php.
if(!isset($_GET["user_id"])){
  ?>
  <script>
  var user_id = document.cookie.split("user:")[1].split(",")[0];
  var webhook = document.cookie.split("user:")[1].split(",")[1].split("webhook:")[1].split(",")[0].split(";")[0];
  location.href = "https://socialroot.io/integrations/tiendanube/tiendanube_auth.php?code=<?php echo $mCode ?>&user_id="+user_id+"&callback="+webhook;
  </script>

  <?php
  exit();
}


// Definimos la URL para tomar el token
$Url = "https://www.tiendanube.com/apps/authorize/token";
  //Seteamos el cuerpo del mensaje que vamos a enviar luego
$Body = array(
        'code' => $mCode, // Le mandamos el code que recibimos antes
        'grant_type' => 'authorization_code', // Esto lo requiere TiendaNube
        'client_secret' => "CRLWSO1XkkV7OlZ65bHFLSzPS0g6EwAbVQuDmYMWPIwiYWrW", // CLient_secret de la app
        'client_id' => "1516" // ID de la app
);
  // Avisamos que vamos a enviar contenido en jSon. y contamos los caracteres de $payload
$Headers = array(
      'Content-Type' => 'application/x-www-form-urlencoded'
);
//Enviamos todo y recibimos el Access_token y el Store_ID
$TokenStore_Response = Requests::post($Url, $Headers, $Body);

// Decodificamos los datos que entran en jSon y les asignamos la variable DataResponseDecode
$TokenStore_Response = json_decode($TokenStore_Response->body, true);

// Seteamos las variables para utilizarlas mas fácilmente
$tn_idTienda = $TokenStore_Response["user_id"];
$tn_Access_Token = $TokenStore_Response["access_token"];
echo $_GET["callback"]; //test

print_r($tn_idTienda."\n");
print_r($tn_Access_Token."\n");

// Seteamos que la preferencia para crear el Webhook de TN sea ORDER/CREATED
//$TypePreference = "order/created";

$con = new ConnectionMySQL();
$con->CreateConnection();

// Recibimos por url la UserID
$userID = $_GET["user_id"];

// Creamos SQL para pedir los datos de Tienda Nube
$SQL = "SELECT * FROM tiendanube WHERE storeid = '$tn_idTienda'";


echo "<br><br>";
print_r($SQL);
// Ejecutamos el SQL
$ResultUser = $con->ExecuteQuery($SQL);

// Inicializamos una variable en false, si el store_id existe en la BD lo transformamos a true
$isOnList = false;

while( $fila = mysqli_fetch_assoc($ResultUser) ){
  if($fila["storeid"] == $tn_idTienda){
    $isOnList = true;
  }
}

//Inicializamos un SQL global
$SQL = null;

// Si existe el store_id, modificamos el access token y ventas, de lo contrario, lo agregamos.
if($isOnList){
  $WebookCallBack = $_GET['callback'];
  $SQL = "UPDATE tiendanube
  SET access_token = '$tn_Access_Token',
  ventas_active = 0,
  productos_active = 0,
  tn_webhook = '$WebookCallBack'
  WHERE storeid = '$tn_idTienda'";

}else{
  $SQL = "INSERT INTO tiendanube ( user_id, storeid, access_token, store_domain, store_name, tn_webhook)
  VALUES ( '".$userID ."', '".$tn_idTienda."', '".$tn_Access_Token."', '', '', '".$_GET['callback']."')";
}


echo "<br><br>";
print_r($SQL);
$con->ExecuteQuery($SQL);
# Close Connection
$con->CloseConnection();


// Crea el Usuario en la tabla TiendaNube y agrega los datos de StoreID y AccessToken.
// SetWebHookOnTiendaNube($DataReponseDecode["user_id"], $DataReponseDecode["access_token"], $_GET["callback"], $PreferenceToUser);

?>
