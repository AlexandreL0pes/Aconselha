<?php

namespace core\controller;

use core\model\Acao;


class Acoes
{
    /**
     * Retorna todas as ações disponíveis para um Atendimento Psicopedagógico
     *
     * @return void
     */
    public function listarAcoes()
    {
        $acao = new Acao();

        $retorno = $acao->listar(null, null, null, 1000);
        return json_encode($retorno);
    }
}
