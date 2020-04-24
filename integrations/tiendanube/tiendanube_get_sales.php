
<?php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    include_once './con_mysql.php';
    include_once "./integatrion_methods_v1.php";


    // Converts it into a PHP object

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $request = file_get_contents('php://input');
        $req_dump = print_r( $request, true );
        $aux = json_decode($req_dump, true);
        $idStore = $aux['store_id'];
        $idItem = $aux['id'];


        $con = new ConnectionMySQL();
        $con->CreateConnection();
        $ListUsers = "SELECT * FROM tiendanube WHERE storeid = '".$idStore."'";
        $Data = $con->ExecuteQuery($ListUsers);
        $con->CloseConnection();


        while( $fila = mysqli_fetch_assoc($Data) ){
            GetDataFromTN($fila["storeid"], $fila["access_token"], $_GET["webhook"], $idItem, $_GET["type"]);
        }
    }


?>
