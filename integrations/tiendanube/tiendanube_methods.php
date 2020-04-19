<?php

// Crea el metodo para tomar storeid, token y webhook..
function SetWebHookOnTiendaNube($StoreId, $Token, $WebHookUrl, $TypePreference){

  // Seteamos que la preferencia para crear el Webhook de TN sea ORDER/CREATED
  $PreferenceToUser = "order/created";

    if(!isset($TypePreference)){
      file_put_contents( 'ErrorPreferences.log', "No exsite la variable TypePreference");
      die();
    }

    // Creamos preferencias para recibir datos de TiendaNube
    $Preferences = array (
        'event' =>  $TypePreference,
        'url' => 'https://socialroot.io/get_sales_tienda_nube.php?webhook='.$WebHookUrl,
    );

    // Comprimimos las preferencias a jSON
    $Payload = json_encode($Preferences);

    // Avisamos que vamos a enviar contenido en jSon. y contamos los caracteres de $payload
    $Options = array(
          'Content-Type' => 'application/json',
          'Authentication:' => 'bearer'.$Token,
          'User-Agent:' => 'SocialRoot (hola@socialroot.io)'
    );

    //Enviamos los headers, y el body, todo para recibir el FAMOSO Acccess_token y el Store_ID
    $Response = Requests::post("https://www.tiendanube.com/apps/authorize/token", $Options, $Payload);

    # Mostramos en pantalla la respuesta
    print_r("Response:".$Response);

    // Creamos conexion de base de datos
    $con = new ConnectionMySQL();
    $con->CreateConnection();

    // Recibimos por url la UserID
    $userID = mysqli_real_escape_string($_GET["user_id"]);

    // Creamos la query
    $SQL = "INSERT INTO tiendanube ( user_id, storeid, access_token, store_domain, store_name)
    VALUES ( '".$userID ."', '".$StoreId."', '".$Token."', '', '')";

    # ExecuteQuery
    $con->ExecuteQuery($SQL);

    # Close Connection
    $con->CloseConnection();

}



function GetDataFromTN($StoreID, $Token, $WebHook, $idITEM){


    $datetime = new DateTime();
    # Prepare new cURL resource
    $date = date("c");
    $ch = curl_init("https://api.tiendanube.com/v1/$StoreID/orders/$idITEM");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLINFO_HEADER_OUT, true);


    # Set HTTP Header for POST request
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Content-Type: application/json",
        "Authentication: bearer $Token",
        "User-Agent: SocialRoot (hola@socialroot.io)"));


    # Submit the POST request and save the response
    $result = curl_exec($ch);



    curl_close($ch);

    # Sending data to create the Object to send to the WebHook
    WebHookSocialRootPOST($result, $WebHook);
}

function WebHookSocialRootPOST($ResponseTN, $WebHook){


    $DataReponseParse = json_decode($ResponseTN, true);

    # Taking data from TN and creating array to send for WebHook
    $Data = array();



    // for($i=0; $i < count($DataReponseParse); $i++){

        # Making json with data from TN
        $x = array (
            'fecha' => $DataReponseParse["created_at"],
            'id' => $DataReponseParse["id"],
        );


        $payload = json_encode($x);

        # Creating the request
        $ch = curl_init($WebHook);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);


        # Set HTTP Header for POST request
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($payload))
        );

        # Submit the POST request and save the response
        $result = curl_exec($ch);

        curl_close($ch);
        sleep(2);
    // }
}

?>
