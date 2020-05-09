<?php


namespace core\controller;

use core\model\Classificacao;


class Classificacoes {

    public function listar()
    {
        $classificacao = new Classificacao();
        $retorno = $classificacao->listar(null, null, null, 1000);
        return json_encode($retorno);
    }
}