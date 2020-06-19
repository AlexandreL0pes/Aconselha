<?php


namespace core\sistema;

use core\model\Permissao;
use core\model\Usuario;
use \Firebase\JWT\JWT;


class Autenticacao
{
    const COOKIE_USUARIO = "usuario";
    const COOKIE_ACESSO = "acesso";
    const COOKIE_PERMISSAO = "token";

    const PERMISSAO_RETIRADA = 0;
    const GERENTE = 1;
    const COORDENADOR = 2;
    const PROFESSOR = 3;
    const CONSELHEIRO = 4;
    const REPRESENTANTE = 5;
    const VICE_REPRESENTANTE = 6;


    public static function login($usuario_login, $senha, $lembrar, $criptografar_senha = false)
    {

        $nova_senha = ($criptografar_senha) ? hash('SHA512', $senha) : $senha;

        $usuario = new Usuario();

        $resultado = $usuario->autenticarUsuario($usuario_login, $nova_senha);


        if ($resultado) {
            $p = new Permissao();
            $permissoes = $p->obterPermissoes($resultado[Usuario::COL_ID]);
            $p = [];
            if (count($permissoes) > 0) {
                foreach ($permissoes as $per) {
                    if (isset($per['acesso'])) {
                        array_push($p, $per['acesso']);
                    }
                }
            }
            // Atribui o array ordenado, para mandar de volta a permissao mais baixa
            sort($p);
            $permissoes = $p ;
        }


        if (count($resultado) > 0) {

            // Define a duração do token
            $validade_token = ($lembrar) ? 604800 : 18000;
            //Remove os valores indesejados na token
            unset($resultado[Usuario::COL_SENHA], $resultado[Usuario::COL_DATA_INICIO], $resultado[Usuario::COL_DATA_FIM]);

            // Adiciona a permissão de usuário na token
            $resultado['permissoes'] = $permissoes;

            // Cria a token com os dados e a duração definida
            $retorno = Autenticacao::codificarToken($resultado, $validade_token);

            // print_r($resultado);
            return array('jwt' => $retorno, 'expireAt' => $validade_token, 'type' => end($permissoes));
        } else {
            return false;
        }
    }


    public static function verificarLogin($token = null)
    {

        $jwt = Autenticacao::decodificarToken($token);
        if ($jwt) {
            return (array) $jwt->data;
        }
        return false;
    }

    public static function verificarPermissao($token = null, $permissao = null)
    {
        $decoded = Autenticacao::decodificarToken($token);


        if (isset($decoded->data->permissoes) && in_array($permissao, $decoded->data->permissoes)) {
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
        $notbefore_claim = $issuedat_claim;
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

    public static function isGerente($token = null)
    {
        return Autenticacao::verificarPermissao($token, Autenticacao::GERENTE);
    }

    public static function isProfessor($token = null)
    {
        return Autenticacao::verificarPermissao($token, Autenticacao::PROFESSOR);
    }

    public static function isConselheiro($token = null)
    {
        return Autenticacao::verificarPermissao($token, Autenticacao::CONSELHEIRO);
    }

    public static function isCoordenador($token = null)
    {
        return Autenticacao::verificarPermissao($token, Autenticacao::COORDENADOR);
    }

    public static function isRepresentante($token = null)
    {
        return Autenticacao::verificarPermissao($token, Autenticacao::REPRESENTANTE);
    }

    public static function isViceRepresentante($token = null)
    {
        return Autenticacao::verificarPermissao($token, Autenticacao::VICE_REPRESENTANTE);
    }

    public static function obterProfessor($token = null)
    {
        $dados = Autenticacao::decodificarToken($token);
        $dados = (array) $dados->data;

        $cod_professor = (isset($dados[Usuario::COL_PESSOA])) ? $dados[Usuario::COL_PESSOA] : false;

        return $cod_professor;
    }

    public static function obterTurma($token = null)
    {
        $dados = Autenticacao::decodificarToken($token);
        $dados = (array) $dados->data;

        $cod_turma = (isset($dados[Usuario::COL_TURMA])) ? $dados[Usuario::COL_TURMA] : false;

        return $cod_turma;
    }
}
