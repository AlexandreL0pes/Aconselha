<?php


namespace core\sistema;

use core\model\Usuario;
use \Firebase\JWT\JWT;


class Autenticacao
{
    const COOKIE_USUARIO = "usuario";
    const COOKIE_ACESSO = "acesso";
    const COOKIE_PERMISSAO = "token";

    const GERENTE = 1;
    const COORDENADOR = 2;
    const PROFESSOR = 3;
    const REPRESENTANTE = 4;


    public static function login($usuario_login, $senha, $lembrar, $criptografar_senha = false)
    {

        $nova_senha = ($criptografar_senha) ? hash('SHA512', $senha) : $senha;

        $usuario = new Usuario();

        $resultado = $usuario->autenticarUsuario($usuario_login, $nova_senha);

        if (count($resultado) > 0) {
            $usuario_id = $resultado[Usuario::COL_ID];
            $permissao = $resultado[Usuario::COL_PERMISSAO];
        } else {
            return false;
        }

        $validade_token = ($lembrar) ? 604800 : 18000;

        $retorno = Autenticacao::codificarToken([
            "id" => $usuario_id,
            "permissao" => $permissao
        ], $validade_token);
        return $retorno;
    }


    public static function verificarLogin($token = null)
    {

        $jwt = Autenticacao::decodificarToken($token);
        if ($jwt) {
            return true;
        }

        return false;
    }

    public function verificarPermissao($token = null, $permissao = null)
    {
        $decoded = JWT::decodificarToken($token);

        if ($decoded['data']['permissao'] == $permissao) {
            return true;
        }

        return false;
    }

    private static function decodificarToken($token = null)
    {
        $file = file_get_contents(ROOT . 'config-dev.json');
        $decoded_file = json_decode($file)->jwt;
        $secret_key = $decoded_file->secret_key;
        $alg = $decoded_file->alg;


        if ($token) {
            try {
                $decoded = JWT::decode($token, $secret_key, array($alg));
                return $decoded;
            } catch (\Throwable $th) {
                // echo "Mensagem: \n " . $th->getMessage() . "\n Local: \n" . $th->getTraceAsString();
                return false;
            }
        }
        return $secret_key;
    }

    private static function codificarToken($dados = [], $duration = null)
    {
        $file = file_get_contents(ROOT . 'config-dev.json');
        $decoded_file = json_decode($file)->jwt;
        $secret_key = $decoded_file->secret_key;
        $alg = $decoded_file->alg;

        $issuer_claim = $_SERVER['SERVER_NAME'];
        $issuedat_claim = time();
        $notbefore_claim = $issuedat_claim + 10;
        $expire_claim = $issuedat_claim + $duration;

        $token = array(
            "iss" => $issuer_claim,
            "iat" => $issuedat_claim,
            "nbf" => $notbefore_claim,
            "exp" => $expire_claim,
            "data" => $dados
        );

        $jwt = JWT::encode($token, $secret_key);

        return $jwt;
    }
}
