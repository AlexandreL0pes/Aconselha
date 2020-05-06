<?php

		require_once '../vendor/autoload.php';
		require_once '../config.php';

		use core\controller\Api;
		// Lembra de tirar essa merda quando for subir pra produção!!!!
		header('Access-Control-Allow-Origin: *');
		$api = new Api();
		$api->init();
?>