<?php

require_once '../vendor/autoload.php';
require_once '../config.php';

use core\controller\Api;
// Lembra de tirar essa merda quando for subir pra produção!!!!
// Antes so tinha isso
// header('Access-Control-Allow-Origin: *');

// Daqui 
$origin = $_SERVER['HTTP_ORIGIN'];
$dominios_permitidos = [
	'http://localhost:5500',
	'http://localhost'
];

if (in_array($origin, $dominios_permitidos)) {
	header('Access-Control-Allow-Origin: ' . $origin);
	header('Access-Control-Allow-Credentials: true');
	header('Access-Control-Allow-Methods: GET, POST');
	header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');

	header("Access-Control-Allow-Headers: X-Requested-With");
}
// Até aqui não tinha 
$api = new Api();
$api->init();
