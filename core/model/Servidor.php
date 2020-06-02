<?php


namespace core\model;


use core\CRUD;


class Servidor extends CRUD
{


    // Isso aqui é uma View
    const TABELA = "VW_PROFESSORES_FUNCIONARIOS";
    const COL_COD_PESSOA = "COD_PESSOA";
    const COL_NOME_PESSOA = "NOME_PESSOA";
    const COL_EMAIL = "EMAIL";
    const COL_COD_INSTITUICAO = "COD_INSTITUICAO";

    public function listar($campos = null, $busca = [], $ordem = null, $limite = null)
    {
        $database = "academico";

        $campos = $campos != null ? $campos : " * ";
        $ordem = $ordem != null ? $ordem : self::COL_COD_PESSOA;
        $limite = $limite != null ? " TOP {$limite} " : " TOP 1000 ";

        $campos = $limite . " " . $campos;


        $tabela = self::TABELA;
        $where_condicao = " 1 = 1 ";
        $where_valor = [];


        if ($busca && count($busca) > 0) {
            if (isset($busca[self::COL_COD_PESSOA]) && !empty($busca[self::COL_COD_PESSOA])) {
                $where_condicao .= " AND " . self::COL_COD_PESSOA . " = ? ";
                $where_valor[] = $busca[self::COL_COD_PESSOA];
                
            }
        }

        $campos = " DISTINCT " . $campos;
        $where_condicao .= " AND " . self::COL_COD_INSTITUICAO . " = ? ";
        $where_valor[] = '3';

        $where_condicao .= " AND ATIVO = ? ";
        $where_valor[] = '1';

        $where_condicao .= " AND CHARINDEX(' ', NOME_PESSOA) > 0";


        $retorno = [];

        try {
            $retorno = $this->read($database, $tabela, $campos, $where_condicao, $where_valor, null, $ordem, null);
            // echo $this->pegarUltimoSQL();
        } catch (\Throwable $th) {
            echo $this->pegarUltimoSQL();

            echo "Mensagem: " . $th->getMessage() . "\n Local: " . $th->getTraceAsString();

            return false;
        }

        return $retorno;
    }
}
