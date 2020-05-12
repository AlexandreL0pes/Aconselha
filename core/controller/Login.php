<?php


namespace core\controller;


use core\sistema\Autenticacao;

class Login
{

    public function login($dados)
    {
        $retorno = Autenticacao::login($dados['login'], $dados['senha'], $dados['lembrar'], false);
        if ($retorno) {
            http_response_code(200);
            return json_encode(array('message' => 'Deu certo','cookies' => $_COOKIE));
        } else {
            http_response_code(401);
            return json_encode(array('message' => 'Não foi possível realizar o login!','cookies' => $_COOKIE));
        }
    }

    public function logout()
    {
        Autenticacao::logout();
        return true;
    }
}
