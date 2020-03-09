<?php
    //  header('Location: public_html/');

    // $mysqli = new mysqli("db", "root", "123456789", "myDb");

    // if ($mysqli ->connect_errno) {
    //   echo "Failed to connect to mysql" . $mysqli->connect_errno;
    //   exit();
    // }

    // if ($result = $mysqli -> query("SELECT * FROM pessoa")) {
    //   echo "Returned rows are: " . $result -> num_rows;
    //   // Free result set
    //   $result -> free_result();
    // }

     require_once __DIR__ . '/vendor/autoload.php';
     require_once __DIR__ . '/config.php';
     
     use core\model\Pessoa;
     
     $pessoa = new Pessoa();
     
     $resultado = $pessoa->listar();
     
    //  phpinfo();
    echo '<h4>MySQL</h4><pre>';
    print_r($resultado);
    echo '</pre>';
    
    echo '<h4>MSSQL</h4><pre>';
    print_r($pessoa->listarMS());
    echo '</pre>';
    // $pessoa->listarMS();
?>
