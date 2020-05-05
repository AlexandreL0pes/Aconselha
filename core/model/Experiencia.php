<?php

namespace core\model;

use core\CRUD;
use Exception;

class Experiencia extends CRUD
{

    const TABELA = "Experiencia";
    const COL_ID = "id";
    const COL_TITULO = "titulo";
    const COL_OBSERVACAO = "observacao";
    const COL_REUNIAO = "idReuniao";
    const COL_DATA = "data";
    const COL_CLASSIFICACAO = "idClassificacao";


    public function adicionar($dados)
    {
        try {

            $retorno = $this->create(self::TABELA, $dados);
        } catch (Exception $e) {
            echo "Mensagem: " . $e->getMessage() . "\n Local: " . $e->getTraceAsString();
            return false;
        }

        return $retorno;
    }

    public function alterar($dados)
    {
        if (!isset($dados[self::COL_ID])){
            throw new Exception("É necessário informar o ID do assunto");
        }

        $where_condicao = self::COL_ID . " = ?";
        $where_valor[] = $dados[self::COL_ID];
        
        try {
            $this->update(self::TABELA, $dados, $where_condicao, $where_valor);

        } catch (Exception $e) {
            echo "Mensagem: " . $e->getMessage() . "\n Local: " . $e->getTraceAsString();
            return false;
        }

        return $dados[self::COL_ID];
    }

    public function listar($campos = null, $busca = [], $ordem = null, $limite = null)
    {
        $campos = $campos != null ? $campos : " * ";
        $ordem = $ordem != null ? $ordem : " " . self::COL_REUNIAO;
        $limite = $limite != null ? $limite : 100;

        $where_condicao = " 1 = 1 ";
        $where_valor = [];

        if (isset($busca[self::COL_REUNIAO]) && !empty($busca[self::COL_REUNIAO])) {
            $where_condicao .= " AND " . self::COL_REUNIAO . " = ?";
            $where_valor[] = $busca[self::COL_REUNIAO];
        }

        if (isset($busca[self::COL_CLASSIFICACAO]) && !empty($busca[self::COL_CLASSIFICACAO])) {
            $where_condicao .= " AND " . self::COL_CLASSIFICACAO . " = ?";
            $where_valor[] = $busca[self::COL_CLASSIFICACAO];
        }

        if (isset($busca[self::COL_ID]) && !empty($busca[self::COL_ID])) {
            $where_condicao .= " AND " . self::COL_ID . " = ?";
            $where_valor[] = $busca[self::COL_ID];
        }

        $retorno = [];

        try {
            
            $retorno = $this->read(null, self::TABELA, $campos, $where_condicao, $where_valor, null, $ordem, $limite);

        } catch (\Throwable $th) {
            echo "Mensagem: " . $th->getMessage() . "\n Local: " . $th->getTraceAsString();
            return false;
        }

        return $retorno;
    }
}
