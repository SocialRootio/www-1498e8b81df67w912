
<?php
    
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    include_once './con_mysql.php';
    include_once "./integatrion_methods_v1.php";

    $con = new ConnectionMySQL();
    $con->CreateConnection();

    // Converts it into a PHP object
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $request = file_get_contents('php://input');
        $req_dump = print_r( $request, true );
        $aux = json_decode($req_dump, true);
        $idStore = $aux['store_id'];
        // $fp = file_put_contents( 'requestToto.log', $aux["store_id"] );        

        $ListUsers = "SELECT storeid, access_token FROM tiendanube WHERE storeid = '".$idStore."'";
        
        $Data = $con->ExecuteQuery($ListUsers);

        while( $fila = mysqli_fetch_assoc($Data) ){

            $fp = file_put_contents( 'adada.log', "Leggo hasta aca" );        

            GetDataFromTN($fila["storeid"], $fila["access_token"], $_GET["webhook"]);
        }

        // # Close Connection.
        $con->CloseConnection();
    } 
    
   

  


?>
