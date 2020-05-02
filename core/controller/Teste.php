<?php

namespace core\controller;

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
}
