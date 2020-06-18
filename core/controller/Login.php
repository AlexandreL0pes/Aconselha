<?php


namespace core\controller;


use core\sistema\Autenticacao;

class Login
{

    /**
     * Efetua o login de um usuário no sistema
     *
     * @param  mixed $dados
     * @return void
     */
    public function login($dados)
    {
        $retorno = Autenticacao::login($dados['login'], $dados['senha'], $dados['lembrar'], false);

        if ($retorno) {
            http_response_code(200);
            return json_encode(array('message' => 'Logado com sucesso!', 'jwt' => $retorno['jwt'], 'expireAt' => $retorno['expireAt'], 'type' => $retorno['type']));
        } else {
            http_response_code(401);
            return json_encode(array('message' => 'Não foi possível realizar o login!'));
        }
    }


    /**
     * Verifica o login de usuário com base na token
     *
     * @param  mixed $dados
     * @return void
     */
    public function verificarLogin($dados)
    {
        if (isset($dados['token']) && $dados['token'] != null) {
            $retorno = Autenticacao::verificarLogin($dados['token']);

            // print_r($retorno);
            if ($retorno) {
                http_response_code(200);
                return json_encode(array('message' => "Usuário Logado!", 'type' => end($retorno['permissoes'])));
            } else {
                http_response_code(400);
                return json_encode(array('message' => "Usuário não logado!"));
            }
        }

        return false;
    }
}
