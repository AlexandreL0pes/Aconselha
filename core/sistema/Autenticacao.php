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

        // Define o tempo de validade do COOKIE
        $lembrar_acesso = $lembrar ? time() + 604800 : null;

        setcookie(self::COOKIE_USUARIO, $usuario_id, $lembrar_acesso,  "/", PATH_COOKIE);
        setcookie(self::COOKIE_ACESSO, $nova_senha, $lembrar_acesso,  "/", PATH_COOKIE);

        return true;
    }

    public static function logout()
    {
        if (isset($_COOKIE[self::COOKIE_USUARIO]) && isset($_COOKIE[self::COOKIE_ACESSO])) {
            setcookie(self::COOKIE_USUARIO, "", time() - 1, "/", PATH_COOKIE);
            setcookie(self::COOKIE_ACESSO, "",  time() - 1, "/", PATH_COOKIE);
        }
    }

    public static function verificarLogin($token = null)
    {
        // if (isset($_COOKIE[self::COOKIE_USUARIO]) && isset($_COOKIE[self::COOKIE_ACESSO])) {
        //     return json_encode(true);
        // } else {
        //     return json_encode(false);
        // }

        $jwt = $this->decodificarToken($token);

        if ($jwt) {
            return true;
        }

        return false;
    }

    public static function decodificarToken($token = null)
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
                echo "Mensagem: \n " . $th->getMessage() . "\n Local: \n" . $th->getTraceAsString();
                return false;
            }
        }
        return $secret_key;
    }

    public static function codificarToken($dados = [])
    {
        $file = file_get_contents(ROOT . 'config-dev.json');
        $decoded_file = json_decode($file)->jwt;
        $secret_key = $decoded_file->secret_key;
        $alg = $decoded_file->alg;

        $issuer_claim = $_SERVER['SERVER_NAME'];
        $issuedat_claim = time();
        $notbefore_claim = $issuedat_claim + 10;

        $token = array(
            "iss" => $issuer_claim,
            "iat" => $issuedat_claim,
            "nbf" => $notbefore_claim,
            "data" => $dados
        );

        $jwt = JWT::encode($token, $secret_key);

        return $jwt;
    }
}
