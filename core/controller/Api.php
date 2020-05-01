<?php

namespace core\controller;

use core\sistema\Requisicao;

class Api
{
    public function init()
    {
        $requisicao = new Requisicao();
        $request = $requisicao->get(true);

        $dados = $request['dados'];
        $acao = $request['acao'];

        $api_controller = explode('/', $acao);

        $classe = "\\core\\controller\\" . $api_controller[0];
        $metodo = $api_controller[1];

        if (!class_exists($classe)) {
            http_response_code(406);
            return array ('message' => 'Classe não encontrada!');
        }
        $class = new $classe();

        if (method_exists($class, $metodo)) {
            print_r(call_user_func_array([$class, $metodo], [$dados, $this]));
        } else {
            http_response_code(406);
            return array ('message' => 'Método não encontrado!');
        }
    }
}
