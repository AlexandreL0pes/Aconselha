<?php

namespace core\model;

use core\CRUD;
use Exception;

class Analise  extends CRUD
{
    const TABELA = "Analise";
    const COL_ID = "id";
    const COL_DIAGNOSTICA = "idDiagnostica";
    const COL_PERFIL = "idPerfil";

    public function adicionar($dados)
    {
        try {
            $retorno = $this->create(self::TABELA, $dados);
        } catch (\Throwable $th) {
            echo "Mensagem: " . $th->getMessage() . "\n Local: " . $th->getTraceAsString();
            return false;
        }

        return $retorno;
    }

    public function alterar($dados)
    {
        if (!isset($dados[self::COL_DIAGNOSTICA])) {
            throw new Exception("É necessário informar o id da Analise");
        }

        $where_condicao = self::COL_DIAGNOSTICA . " = ? ";
        $where_valor[] = $dados[self::COL_DIAGNOSTICA];

        try {
            $this->update(self::TABELA, $dados, $where_condicao, $where_valor);
        } catch (\Throwable $th) {
            echo "Mensagem: " . $th->getMessage() . "\n Local: " . $th->getTraceAsString();
            return false;
        }

        return $dados[self::COL_ID];
    }

    public function listar($campos = null, $busca = [], $ordem = null, $limite = null)
    {
        $campos = $campos != null ? $campos : " * ";
        $ordem = $ordem != null ? $ordem : self::COL_ID . " ASC ";
        $limite = $limite != null ? $limite : 100;

        $where_condicao = " 1 = 1 ";
        $where_valor = [];

        $tabela = self::TABELA . " a INNER JOIN " . Perfil::TABELA . " p on a." . Analise::COL_PERFIL . " = p." . Perfil::COL_ID;

        if ($busca && count($busca) > 0) {
            if (isset($busca[self::COL_DIAGNOSTICA]) && !empty($busca[self::COL_DIAGNOSTICA])) {
                $where_condicao .= " AND " . self::COL_DIAGNOSTICA . " = ? ";
                $where_valor[] = $busca[self::COL_DIAGNOSTICA];
            }
            if (isset($busca[self::COL_ID]) && !empty($busca[self::COL_ID])) {
                $where_condicao .= " AND " . self::COL_ID . " = ? ";
                $where_valor[] = $busca[self::COL_ID];
            }
        }

        $retorno = [];

        try {
            $retorno = $this->read(null, $tabela, $campos, $where_condicao, $where_valor, null, null, null);
        } catch (\Throwable $th) {
            echo "Mensagem: " . $th->getMessage() . "\n Local: " . $th->getTraceAsString();
            return false;
        }

        return $retorno;
    }

    public function excluir($condicao = [])
    {
        $where_condicao = " 1 = 1 ";
        $where_valor = [];

        if ($condicao && count($condicao) > 0) {
            if (isset($condicao[self::COL_DIAGNOSTICA]) && !empty($condicao[self::COL_DIAGNOSTICA])) {
                $where_condicao .= " AND " . self::COL_DIAGNOSTICA . " = ? ";
                $where_valor[] = $condicao[self::COL_DIAGNOSTICA];
            }
        }

        $retorno = [];

        try {
            $retorno = $this->delete(self::TABELA, $where_condicao, $where_valor);
        } catch (\Throwable $th) {
            echo "Mensagem: " . $th->getMessage() . "\n Local:" . $th->getTraceAsString();
            return false;
        }

        return $retorno;
    }
}
