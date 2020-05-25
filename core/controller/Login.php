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
            return json_encode(array('message' => 'Logado com sucesso!', 'jwt' => $retorno['jwt'], 'expireAt' => $retorno['expireAt'], 'type' => $retorno['type']));
        } else {
            http_response_code(401);
            return json_encode(array('message' => 'Não foi possível realizar o login!'));
        }
    }


    public function verificarLogin($dados)
    {
        if (isset($dados['token']) && $dados['token'] != null) {
            $retorno = Autenticacao::verificarLogin($dados['token']);

            if ($retorno) {
                http_response_code(200);
                return json_encode(array('message'=> "Usuário Logado!", 'type' => $retorno['permissao']));
            }else{
                http_response_code(400);
                return json_encode(array('message' => "Usuário não logado!"));
            }
        }

        return false;
    }

    public function verificarCoordenador($dados)
    {
        $token = $dados['token'];

        $acesso = Autenticacao::verificarPermissao($token, Autenticacao::COORDENADOR);

        if ($acesso) {
            http_response_code(200);
            return json_encode(array('message' => 'Usuário logado'));
        }else{
            http_response_code(400);
            return json_encode(array('message' => 'O usuário não possui tal nível de acesso.'));
        }
    }
    public function verificarRepresentante($dados)
    {
        $token = $dados['token'];

        $acesso = Autenticacao::verificarPermissao($token, Autenticacao::REPRESENTANTE);

        if ($acesso) {
            http_response_code(200);
            return json_encode(array('message' => 'Usuário logado'));
        }else{
            http_response_code(400);
            return json_encode(array('message' => 'O representante não possui tal nível de acesso.'));
        }
    }

    public function verificarProfessor($dados)
    {
        $token = $dados['token'];

        $acessoProfessor = Autenticacao::verificarPermissao($token, Autenticacao::PROFESSOR);
        $acessoCoordenador = Autenticacao::verificarPermissao($token,Autenticacao::COORDENADOR);
        if ($acessoProfessor || $acessoCoordenador) {
            http_response_code(200);
            return json_encode(array('message' => 'Usuário logado'));
        }else{
            http_response_code(400);
            return json_encode(array('message' => 'O usuário não possui tal nível de acesso.'));
        }
    }

    public function verificarGerente($dados)
    {
        $token = $dados['token'];

        $acesso = Autenticacao::verificarPermissao($token, Autenticacao::GERENTE);

        if ($acesso) {
            http_response_code(200);
            return json_encode(array('message' => 'Usuário logado'));
        }else{
            http_response_code(400);
            return json_encode(array('message' => 'O usuário não possui tal nível de acesso.'));
        }
    }
}
