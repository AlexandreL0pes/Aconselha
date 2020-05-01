<?php
header('Location: public_html/');
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/Teste.php';

//  (new Teste())->inserir();
//  (new Teste())->alterar();
//  (new Teste())->listar();




// use core\model\Pessoa;

// $pessoa = new Pessoa();

// $resultado = $pessoa->listar();

// phpinfo();
// echo '<h4>MySQL</h4><pre>';
// print_r($resultado);
// echo '</pre>';

// echo '<h4>MSSQL</h4><pre>';
// print_r($pessoa->listarMS());
// echo '</pre>';
// // $pessoa->listarMS();

// use core\model\Conselho;

// $conselho = new Conselho();

// $dados =         [
// 'cod_turma' => 'TESTE',
// 'data' => '2018-10-10',
// 'etapaConselho' => '1',
// 'finalizado' => '0'
// ];

// $resultado = $conselho->adicionar($dados);
