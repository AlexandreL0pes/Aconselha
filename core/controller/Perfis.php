<?php

namespace core\controller;

use core\model\Perfil;


class Perfis
{
    /**
     * Retorna todos os perfis cadastrados no banco de dados
     */
    public function listarPerfis()
    {
        return json_encode((new Perfil)->listar(null, null, null, 1000));
    }
}
