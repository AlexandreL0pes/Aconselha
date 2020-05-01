<?php

namespace core\controller;

class Teste {

    public function teste($dados) {
        // var_dump($dados);
        if (isset($dados['nome']) && $dados['nome'] === '2017103202030090') {
            http_response_code(200);
            return array ('stauts' => 200, 'message' => 'Deu certo!');
        }
        http_response_code(403);
        return array ('stauts' => 403, 'message' => 'Não deu certo!');
    }
}