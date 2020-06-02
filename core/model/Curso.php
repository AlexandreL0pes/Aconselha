<?php


namespace core\model;

use core\CRUD;

class Curso extends CRUD
{
    const TABELA = "CURSOS";
    const COL_ID = "COD_CURSO";
    const COL_TIPO_CURSO = "COD_TIPO_CURSO";
    const COL_DESC_CURSO = "DESC_CURSO";
    const COL_INSTITUICAO = "COD_INSTITUICAO";


    public function listar($campos = null, $busca = [], $ordem = null, $limite = null)
    {
        $database = "academico";

        $campos = $campos != null ? $campos : " * ";
        $ordem = $ordem != null ? $ordem : self::COL_ID . " ASC ";
        $limite = $limite != null ? " TOP {$limite} " : " TOP 1000 ";

        $campos = $limite . " " . $campos;

        $where_condicao = " 1 = 1 ";
        $where_valor = [];


        if ($busca && count($busca) > 0) {
            if (isset($busca[self::COL_ID]) && !empty($busca[self::COL_ID])) {
                $where_condicao .= " AND " . self::COL_ID . " = ? ";
                $where_valor[] = $busca[self::COL_ID];
            }
        }

        // Filtra apenas o cursos técnicos
        $where_condicao .= " AND " . self::COL_TIPO_CURSO . " = ? ";
        $where_valor[] = '265';

        // Filtra apenas os cursos do IF CERES 
        $where_condicao .= " AND " . self::COL_INSTITUICAO . " = ? ";
        $where_valor[] = '3';

        $retorno = [];

        try {
            $retorno = $this->read($database, self::TABELA, $campos, $where_condicao, $where_valor, null, $ordem, null);
        } catch (\Throwable $th) {
            echo "Mensagem: " . $th->getMessage() . "\n Local: " . $th->getTraceAsString();
            return false;
        }

        return $retorno;
    }
}
