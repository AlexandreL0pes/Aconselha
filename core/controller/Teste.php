<?php

namespace core\controller;

use core\sistema\Autenticacao;
use \Firebase\JWT\JWT;

class Teste
{

    public function teste($dados)
    {
        // var_dump($dados);
        if (isset($dados['nome']) && $dados['nome'] === '2017103202030090') {
            http_response_code(200);

            $alunos = array(
                [
                    'firstName' => 'Alexandre',
                    'lastName' => 'Lopes',
                    'age' => 20
                ],
                [
                    'firstName' => 'Láyza',
                    'lastName' => 'Lopes',
                    'age' => 15
                ],
                [
                    'firstName' => 'Alessandra',
                    'lastName' => 'Novais',
                    'age' => 40
                ]
            );
            return json_encode(array('stauts' => 200, 'message' => 'Deu certo!', 'alunos' => $alunos));
        }
        http_response_code(403);
        return json_encode(array('stauts' => 403, 'message' => 'Não deu certo!'));
    }

    public function getDados($dados)
    {

        http_response_code(200);
        $fakeData = ['Status' => 200, 'Message' => 'Deu certo!'];

        return json_encode($fakeData);
    }

    public function jwt($dados)
    {
        $a = Autenticacao::decodificarToken("eyJ0eXAiOiJKV1QiLCJhbGciiJIUzI1NiJ9.eyJpc3MiOiJsb2NhbGhvc3QiLCJpYXQiOjE1OTAyNjE4OTMsIm5iZiI6MTU5MDI2MTg5MywiZGF0YSI6eyJpZCI6MiwibmFtZSI6IkFsZXhhbmRyZSBMb3BlcyIsInBlcm1pc3NhbyI6Mn19.uGU0uTZNmvivGc8CMGX52YM0LesDHNHckszfC0MoXWM");
        print_r($a);
        echo '\n\n\n\n\n';
        $secret_key = "aaaa";

        $issuer_claim =  $_SERVER['SERVER_NAME']; // this can be the servername
        $issuedat_claim = time(); // issued at
        $notbefore_claim = $issuedat_claim; //not before in seconds

        $token = array(
            "iss" => $issuer_claim,
            "iat" => $issuedat_claim,
            "nbf" => $notbefore_claim,
            "data" => array(
                "id" => 2,
                "name" => "Alexandre Lopes",
                "permissao" => Autenticacao::COORDENADOR
            )
        );

        $jwt = JWT::encode($token, $secret_key);
        $decoded = JWT::decode($jwt, $secret_key, array('HS256'));

        $decoded_array = (array) $decoded;

        print_r($jwt);
        echo "\n\n";
        print_r($decoded_array);
    }
}
