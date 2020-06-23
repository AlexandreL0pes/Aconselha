<?php

namespace core\model;

use core\CRUD;
use Exception;

class Perfil extends CRUD
{
    const TABELA = "Perfil";
    const COL_ID = "id";
    const COL_NOME = "nome";
    const COL_DESCRICAO = "descricao";
    // TIPO -> BOOLEAN
    // 1 -> POSITIVO
    // 0 -> NEGATIVO
    const COL_TIPO = "tipo";


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
            throw new Exception("É necessário informar o ID do perfil");
        }

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

    public function listar($campos = null, $busca = [], $ordem = null, $limite = null)
    {

        $campos = $campos != null ? $campos : " * ";
        $ordem = $ordem != null ? $ordem : self::COL_ID . " ASC";
        $limite = $limite != null ? $limite : 10;

        $where_condicao = " 1 = 1 ";
        $where_valor = [];

        $tabela = self::TABELA;
        $group_by = null;
        if ($busca && count($busca) > 0) {
            // Fazer a busca por nome 
            if (isset($busca[self::COL_NOME]) && !empty($busca[self::COL_NOME])) {
                $where_condicao .= " AND " . self::COL_NOME . " LIKE ? ";
                $where_valor[] = "%{$busca[self::COL_NOME]}%";
            }
            if (isset($busca[self::COL_ID]) && !empty($busca[self::COL_ID])) {
                $where_condicao .= " AND " . self::COL_ID . " LIKE ? ";
                $where_valor[] = "%{$busca[self::COL_ID]}%";
            }
            if (isset($busca['perfis_relevantes']) && !empty($busca['perfis_relevantes'])) {
                $where_condicao .= " AND " . Turma::COL_ID . " = ? ";
                $where_valor[] = $busca['perfis_relevantes'];

                $tabela = self::TABELA .
                    " inner join Analise on Perfil.id = Analise.idPerfil" .
                    " inner join Diagnostica  on Analise.idDiagnostica = Diagnostica.id" .
                    " inner join Reuniao  on Diagnostica.idReuniao = Reuniao.id";

                $group_by = " idPerfil ";
            }
        }

        $retorno = [];

        try {
            $retorno = $this->read(null, $tabela, $campos, $where_condicao, $where_valor, $group_by, $ordem, $limite);
            // echo $this->pegarUltimoSQL();
        } catch (\Throwable $th) {
            echo "Mensagem: " . $th->getMessage() . "\n Local: " . $th->getTraceAsString();
            return false;
        }

        return $retorno;
    }

    public function selecionarPerfil($perfil_id)
    {
        $campos = self::COL_ID . ", " . self::COL_NOME . ", " . self::COL_TIPO;
        $busca = [self::COL_ID => $perfil_id];

        $resultado = $this->listar($campos, $busca, null, 1);

        return $resultado;
    }
}
