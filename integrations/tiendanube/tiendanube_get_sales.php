
<?php
    
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    include_once '../con_mysql.php';
    include_once "./tiendanube_methods.php";

    
    # Vereficamos si este archivo fue solicitado por un POST REQUEST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        
        # Parseamos los datos enviados por POST 
        $request = file_get_contents('php://input');
        $req_dump = print_r( $request, true );
        $aux = json_decode($req_dump, true);
        
        # Seteamos variables
        $idStore = $aux['store_id'];
        $idItem = $aux['id'];

        # Buscamos el usuario solicatante por una Query de SQL
        $con = new ConnectionMySQL();
        $con->CreateConnection();
        $ListUsers = "SELECT * FROM tiendanube WHERE storeid = '".$idStore."'";
        $Data = $con->ExecuteQuery($ListUsers);
        $con->CloseConnection();

        # Enviamos toda la data traida para que se genere un nuevo item en el webhook de notificaciones.
        while( $fila = mysqli_fetch_assoc($Data) ){
            GetDataFromTN($fila["storeid"], $fila["access_token"], $_GET["webhook"], $idItem, $_GET["type"]);
        }
    } 


?>
