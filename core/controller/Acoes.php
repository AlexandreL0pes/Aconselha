<?php

namespace core\controller;

use core\model\Acao;


class Acoes
{
    public function listarAcoes()
    {
        $acao = new Acao();

        $retorno = $acao->listar(null, null, null, 1000);
        return json_encode($retorno);
    }
}
