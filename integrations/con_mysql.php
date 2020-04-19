<?php
 // Creamos el metodo
    class ConnectionMySQL{

    private $host;
    private $user;
    private $password;
    private $database;
    private $conn;

    public function __construct(){

        define('HOST',      '162.213.255.212');
        define('USER',      'socialroot_r00th4x0rsmrr0b0t');
        define('PASSWORD',  'l4pru3b4s0c1al3sl4b0mb4');
        define('DATABASE',  'socialroot_m41nd4t4b4s3x1241dfr1dscxzd1');

        $this->host=HOST;
        $this->user=USER;
        $this->password=PASSWORD;
        $this->database=DATABASE;

    }

    public function CreateConnection(){

    $this->conn = new mysqli($this->host, $this->user, $this->password, $this->database);

    }

    public function CloseConnection(){

    $this->conn->close();
    }

    public function ExecuteQuery($sql){

    $result = $this->conn->query($sql);
    return $result;

    }

  }

?>
