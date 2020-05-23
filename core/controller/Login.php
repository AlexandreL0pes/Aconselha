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
            return json_encode(array('message' => 'Logado com sucesso!', 'jwt' => $retorno, 'expireAt' => 5000));
        } else {
            http_response_code(401);
            return json_encode(array('message' => 'Não foi possível realizar o login!'));
        }
    }

    public function logout()
    {
        Autenticacao::logout();
        return true;
    }

    public function verificarLogin()
    {
        return json_encode(Autenticacao::verificarLogin());
    }
}
