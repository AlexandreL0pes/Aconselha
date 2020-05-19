<?php

namespace core\model;

use core\CRUD;
use Exception;


class Diagnostica extends CRUD
{
    const TABELA = "Diagnostica";
    const COL_ID = "id";
    const COL_ID_REUNIAO = "idReuniao";
    const COL_PROFESSOR = "COD_PROFESSOR";
    const COL_ESTUDANTE = "COD_MATRICULA";
    const COL_DATA = "data";

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
        if (!isset($dados[self::COL_ID])) {
            throw new Exception("É necessário informar o id da diagnóstica");
        }

        $where_condicao = self::COL_ID . " = ? ";
        $where_valor[] = $dados[self::COL_ID];

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
        $ordem = $ordem != null ? $ordem : Diagnostica::COL_ID . " ASC ";
        $limite = $limite != null ? $limite : 1000;

        $where_condicao = " 1 = 1 ";
        $where_valor = [];
        $group_by = null;


        $tabela = self::TABELA;

        if ($busca && count($busca) > 0) {
            if (isset($busca[self::COL_ID]) && !empty($busca[self::COL_ID])) {
                $where_condicao .= " AND " . self::COL_ID . " = ? ";
                $where_valor[] = $busca[self::COL_ID];
            }
            if (isset($busca[self::COL_ID_REUNIAO]) && !empty($busca[self::COL_ID_REUNIAO])) {
                $where_condicao .= " AND Diagnostica." . self::COL_ID_REUNIAO . " = ? ";
                $where_valor[] = $busca[self::COL_ID_REUNIAO];
            }

            if (isset($busca['relevantes']) && !empty($busca['relevantes'])) {

                $reuniao_id = ($busca['relevantes']['reuniao']) ? $busca['relevantes']['reuniao'] : null;
                $condicao = ($reuniao_id) ? "WHERE " . self::COL_ID_REUNIAO . " = ? " : "";

                $tabela = "( SELECT
                            group_concat(DISTINCT COD_PROFESSOR) as professor,
                            COD_MATRICULA as matricula,
                            idPerfil as perfil,
                            count(*) as qtdperfil
                        from Diagnostica
                        INNER JOIN Analise A on Diagnostica.id = A.idDiagnostica
                        ". $condicao ."
                        group by idPerfil,COD_MATRICULA
                        having  qtdperfil >= 1
                        order by qtdperfil desc ) as tabela";
                $group_by = " matricula ";
                $ordem = null;
                array_unshift($where_valor, $reuniao_id);
            }
        }

        $retorno = [];
        try {
            $retorno = $this->read(null, $tabela, $campos, $where_condicao, $where_valor, $group_by, $ordem, $limite);
            echo $this->pegarUltimoSQL();
        } catch (\Throwable $th) {
            echo "Mensagem: " . $th->getMessage() . "\n Local: " . $th->getTraceAsString();
            echo $this->pegarUltimoSQL();
            return false;
        }

        return $retorno;
    }

    public function excluir($condicao)
    {
        $where_condicao = " 1 = 1 ";
        $where_valor = [];

        if ($condicao && count($condicao) > 0) {
            if (isset($condicao[self::COL_ID]) && !empty($condicao[self::COL_ID])) {
                $where_condicao .= " AND " . self::COL_ID . " = ? ";
                $where_valor[] = $condicao[self::COL_ID];
            }
        }

        $retorno = [];

        try {
            $retorno = $this->delete(self::TABELA, $where_condicao, $where_valor);
        } catch (\Throwable $th) {
            echo "Mensagem: " . $th->getMessage() . "\n Local: " . $th->getTraceAsString();
            return false;
        }

        return $retorno;
    }
}
