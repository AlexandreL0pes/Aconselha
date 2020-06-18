<?php


namespace core\controller;

use core\model\Classificacao;


class Classificacoes
{

    /**
     * Retorna todas as classificações disponíveis para uma Experiência
     *
     * @return void
     */
    public function listar()
    {
        $classificacao = new Classificacao();
        $retorno = $classificacao->listar(null, null, null, 1000);
        return json_encode($retorno);
    }
}
