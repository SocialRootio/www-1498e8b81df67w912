<?php



function GetDataFromTN($StoreID, $Token, $WebHook, $idITEM, $type){

    # Generamos un POST REQUEST para recibir los datos de la preferencia seteada ( En pricipio en tiendanube_get_sales.php ).
    $ch = curl_init("https://api.tiendanube.com/v1/$StoreID/".$type."s/$idITEM");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLINFO_HEADER_OUT, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Content-Type: application/json",
        "Authentication: bearer $Token",
        "User-Agent: SocialRoot (hola@socialroot.io)"));
    $result = curl_exec($ch);
    curl_close($ch);


    # Una vez recibida la data, enviamos los datos para parsearlos y seleccionar lo que sea necesario para mostrar
    WebHookSocialRootPOST($result, $WebHook, $type, $Token, $StoreID);
}



function WebHookSocialRootPOST($ResponseTN, $WebHook, $Type, $Token, $StoreID){

        # Parseamos la data que vino de TN
        $DataReponseParse = json_decode($ResponseTN, true);

        # Definimos la url del producto como nula
        $URLProduct = "#";

        # Si la suscripcion fue de una venta, buscamos la url del producto
        if($Type == "order"){

          # Guardamos la ID del producto
          $ProductID = $DataReponseParse["products"][0]["product_id"];

          # Hacemos un GET a Tienda Nube para obtener el producto con la ID guardada
          $ch = curl_init("https://api.tiendanube.com/v1/$StoreID/products/$ProductID");
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
          curl_setopt($ch, CURLINFO_HEADER_OUT, true);
          curl_setopt($ch, CURLOPT_HTTPHEADER, array(
              "Content-Type: application/json",
              "Authentication: bearer $Token",
              "User-Agent: SocialRoot (hola@socialroot.io)"));
          $result = curl_exec($ch);
          curl_close($ch);

          # Guardamos el Response de TN
          $DataResult = json_decode($result, true);

          # Tomamos la url del producto del Response
          $URLProduct = $DataResult["canonical_url"];

          file_put_contents( 'ResultadoProductos.log', $DataResult);
          file_put_contents( 'urlProducts.log', "https://api.tiendanube.com/v1/$StoreID/products/$ProductID");
        }



        # Generamos un POST REQUEST para recibir los datos de la preferencia seteada ( En pricipio en tiendanube_get_sales.php ).

        /****************************************************************************************
         *
         * EXPLICACIÓN PARA TOMAR DATOS DE LA API ( KEY -> VALOR )
         *
         *
         * Cada "Categoria" de la API tiene sus propios valores ( elementos ); Para poder
         * acceder a estos valores y setear la key de ese valor, lo planteamos de  la
         * siguiente manera:
         *
         * '{key}' => $DataResponsePare['nombre del elemento de la API']
         *
         * Por ejemplo, creemos una key del tipo fecha y con el valor de la fecha:
         *
         * 'fecha' => $DataResponseParse['created_at']
         *
         * Otro ejemplo con una key del nombre 'nombre' y valor del nombre
         *
         * 'nombre' => $DataResponseParse['name']
         *
         * Cada categoria de la API tiene diferentes atributos, por ende para modificar
         * algo, es necesario chequear cual es el atributo en el listado de atributos.
         *
         * PD: La key es la que aparecerá en la UI de datos como key, y puede ser cualquiera
         * que se necesite, un nombre random como, por ejemplo, socialroot_venta.
         *
         *
         * LOS VALORES A SETEAR SE PONEN DENTRO DE LOS () DE LA VARIABLE $SetKeyAndData
         *
         ***************************************************************************************/

        $SetKeyAndData = array (
            'nombre' => $DataReponseParse["billing_name"],
            'ciudad' => $DataReponseParse["billing_city"],
            'provincia' => $DataReponseParse["billing_province"],
            'producto' => $DataReponseParse["products"][0]["name"],
            'precio' => $DataReponseParse["products"][0]["price"],
            'imagen' => $DataReponseParse["products"][0]["image"]["src"],
            'enlace' => $URLProduct
        );

        # Transformamos los datos en un JSON
        $payload = json_encode($SetKeyAndData);

        # Creamos un POST REQUEST hacia el WebHook de la notificación
        $ch = curl_init($WebHook);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($payload))
        );
        $result = curl_exec($ch);
        curl_close($ch);


}

?>
