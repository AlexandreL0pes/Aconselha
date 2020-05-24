<?php


namespace core\controller;

use core\model\Usuario;
use core\sistema\Autenticacao;

class Coordenadores
{


    /*     {
        "acao": "Coordenadores/cadastrar",
        "matricula": "123123",
        "curso": 1,
        "senha": "criptografia"
    } */
    public function cadastrar($dados)
    {
        $matricula = $dados['matricula'];
        $curso = $dados['curso'];
        $data_inicio = date('Y-m-d');
        $permissao = Autenticacao::COORDENADOR;
        $senha = $dados['senha'];


        $usuario = new Usuario();

        $retorno = $usuario->adicionar([
            Usuario::COL_MATRICULA => $matricula,
            Usuario::COL_CURSO => $curso,
            Usuario::COL_DATA_INICIO => $data_inicio,
            Usuario::COL_PERMISSAO => $permissao,
            Usuario::COL_SENHA => $senha
        ]);

        if ($retorno > 0) {
            http_response_code(200);
            return json_encode(array('message' => "O coordenador foi cadastrado!"));
        } else {
            http_response_code(500);
            return json_encode(array('message' => "Não foi possível cadastrar o coordenador!"));
        }
    }

    public function alterar($dados)
    {
        $coordenador = $dados['coordenador'];
        $matricula = $dados['matricula'];
        $curso = $dados['curso'];


        $data = array(
            Usuario::COL_ID => $coordenador,
            Usuario::COL_MATRICULA => $matricula,
            Usuario::COL_CURSO => $curso
        );

        if (isset($dados['senha']) && !empty($dados['senha'])) {
            $data[Usuario::COL_SENHA] = $dados['senha'];
        }

        $usuario = new Usuario();
        $retorno = $usuario->alterar($data);

        if ($retorno > 0) {
            http_response_code(200);
            return json_encode(array("message" => "O usuário foi alterado"));
        } else {
            http_response_code(500);
            return json_encode(array("message" => "Não foi possível alterar o coordenador!"));
        }
    }
}
