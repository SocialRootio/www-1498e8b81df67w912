<?php

  include_once("./tiendanube_methods.php");
  include_once("../con_mysql.php");


  // $Token = $_POST["access_token"];

  // $StoreID = $_POST["storeid"];

  $WebHook = $_POST["tn_webhook"];

  // $Preferences = $_POST["preference"];

  $StatusVentas = $_POST["ventas_active"];


  $con = new ConnectionMySQL();
  $con->CreateConnection();

  $SQL = "UPDATE tiendanube SET ventas_active = ".$StatusVentas." WHERE tn_webhook = $WebHook";
  echo $SQL;

  $con->ExecuteQuery($SQL);
  $con->CloseConnection();


  // SetWebHookOnTiendaNube($StoreID, $Token, $WebHook, $Preferences, ($StatusVentas == 1) ? true : false);

?>
