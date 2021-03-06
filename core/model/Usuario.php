<?php

namespace core\model;

use core\CRUD;
use core\sistema\Autenticacao;
use Exception;

class Usuario extends CRUD
{


    const TABELA = "Usuario";
    const COL_ID = "id";
    const COL_MATRICULA = "COD_MATRICULA";
    const COL_TURMA = "COD_TURMA";
    const COL_CURSO = "COD_CURSO";
    const COL_DATA_INICIO = "data_inicio";
    const COL_DATA_FIM = "data_fim";
    const COL_PERMISSAO = "permissao";
    const COL_SENHA = "senha";
    const COL_PESSOA = "COD_PESSOA";

    public function adicionar($dados)
    {
        try {

            $retorno = $this->create(self::TABELA, $dados);
        } catch (\Throwable $th) {
            // echo $this->pegarUltimoSQL();
            // echo "<br><br><br>Mensagem: " . $th->getMessage() . "<br><br> Local: " . $th->getTraceAsString();
            return false;
        }

        return $retorno;
    }

    public function alterar($dados)
    {
        if (!isset($dados[self::COL_PESSOA]) && !isset($dados[self::COL_ID])) {
            throw new Exception("É necessário informar o id da pessoa ou a matrícula");
        }


        if (isset($dados[self::COL_PESSOA])) {
            $where_condicao = self::COL_PESSOA . " = ?";
            $where_valor[] = $dados[self::COL_PESSOA];

            try {
                $this->update(self::TABELA, $dados, $where_condicao, $where_valor);
            } catch (\Throwable $th) {
                echo "Mensagem: " . $th->getMessage() . "\n Local: " . $th->getTraceAsString();
                return false;
            }
            return $dados[self::COL_PESSOA];

        } else if (isset($dados[self::COL_ID])) {
            $where_condicao = self::COL_ID . " = ?";
            $where_valor[] = $dados[self::COL_ID];

            try {
                $this->update(self::TABELA, $dados, $where_condicao, $where_valor);
            } catch (\Throwable $th) {
                echo "Mensagem: " . $th->getMessage() . "\n Local: " . $th->getTraceAsString();
                return false;
            }

            return $dados[self::COL_ID];
        }
    }

    public function listar($campos = null, $busca = [], $ordem = null, $limite = null)
    {
        $campos = $campos != null ? $campos : " * ";
        $ordem = $ordem != null ? $ordem : " " . self::COL_ID;
        $limite = $limite != null ? $limite : 10;

        $where_condicao = " 1 = 1 ";
        $where_valor = [];

        $tabela = self::TABELA;

        if (isset($busca[self::COL_ID]) && !empty($busca[self::COL_ID])) {
            $where_condicao .= " AND " . self::COL_ID . " = ?";
            $where_valor[] = $busca[self::COL_ID];
        }

        if (isset($busca[self::COL_MATRICULA]) && !empty($busca[self::COL_MATRICULA])) {
            $where_condicao .= " AND " . self::COL_MATRICULA . " = ?";
            $where_valor[] = $busca[self::COL_MATRICULA];
        }

        if (isset($busca[self::COL_TURMA]) && !empty($busca[self::COL_TURMA])) {
            $where_condicao .= " AND " . self::COL_TURMA . " = ?";
            $where_valor[] = $busca[self::COL_TURMA];
        }

        if (isset($busca[self::COL_CURSO]) && !empty($busca[self::COL_CURSO])) {
            $where_condicao .= " AND " . self::COL_CURSO . " = ?";
            $where_valor[] = $busca[self::COL_CURSO];
        }

        if (isset($busca['periodo']) && !empty($busca['periodo'])) {
            if ($busca['periodo'] == "atual") {

                $where_condicao .= " AND " . self::COL_DATA_INICIO . " <= ? ";
                $where_valor[] = date('Y-m-d');
                $where_condicao .= " AND " . self::COL_DATA_FIM . " is null ";
            } else {

                $where_condicao .= " AND YEAR(" . self::COL_DATA_INICIO . ") = ?";
                $where_valor[] = date('Y');
            }
        }

        if (isset($busca['permissao']) && !(empty($busca['permissao']))) {
            $where_condicao .= " AND " . Permissao::TABELA . "." . Permissao::COL_ACESSO . " = ? ";
            $where_valor[] = $busca['permissao'];

            $tabela = self::TABELA . " INNER JOIN " . Permissao::TABELA . " ON " .
                self::COL_ID . " = " . Permissao::COL_USUARIO;
        }

        if (isset($busca[self::COL_PESSOA]) && !empty($busca[self::COL_PESSOA])) {
            $where_condicao .= " AND " . self::COL_PESSOA . " = ? ";
            $where_valor[] = $busca[self::COL_PESSOA];
        }

        $retorno = [];

        try {
            $retorno = $this->read(null, $tabela, $campos, $where_condicao, $where_valor, null, $ordem, $limite);
            // echo $this->pegarUltimoSQL();
            // print_r($where_valor);
        } catch (\Throwable $th) {
            echo $this->pegarUltimoSQL();
            echo "Mensagem: " . $th->getMessage() . "\n Local: " . $th->getTraceAsString();
            return false;
        }

        return $retorno;
    }

    public function autenticarUsuario($usuario_login, $senha)
    {
        $campos = " * ";
        $where_condicao = self::COL_MATRICULA . " = ? AND " . self::COL_SENHA . " = ? AND " . self::COL_DATA_FIM . " IS NULL ";
        $where_valor = [$usuario_login, $senha];

        $retorno = [];

        try {
            $retorno = $this->read(null, self::TABELA, $campos, $where_condicao, $where_valor, null, null, 1);
        } catch (\Throwable $th) {
            echo "Mensagem: " . $th->getMessage() . "\n Local: " . $th->getTraceAsString();
            return false;
        }

        return $retorno[0];
    }

    public function verificarCoordenador($dados)
    {
        $token = $dados['token'];

        $acesso = Autenticacao::verificarPermissao($token, Autenticacao::COORDENADOR);

        if ($acesso) {
            http_response_code(200);
            return json_encode(array('message' => 'Usuário logado'));
        } else {
            http_response_code(400);
            return json_encode(array('message' => 'O usuário não possui tal nível de acesso.'));
        }
    }
}
