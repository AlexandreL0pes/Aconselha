<?php



namespace core\model;

use core\CRUD;

class Turma extends CRUD {

    const TABELA = "TURMAS";
    const COL_ID = "COD_TURMA";
    const COL_CURSO = "COD_CURSO";
    const COL_SIGLA_TURMA = "SIGLA_TURMA";
    const COL_DESC_TURMA = "DESC_TURMA"; 


    public function listar($campos = null, $busca = [], $ordem = null, $limite = null)
    {
        $database = "academico";

        $campos = $campos != null ? $campos : " * ";
        $ordem = $ordem != null ? $ordem : self::COL_ID . " ASC ";
        $limite = $limite != null ? " TOP {$limite}" : " TOP 1000";

        $campos = $limite . " " . $campos;

        $where_condicao = " 1 = 1 ";
        $where_valor = [];

        if ($busca && count($busca) > 0) {
            if (isset($busca[self::COL_ID]) && !empty($busca[self::COL_ID])) {
                $where_condicao .= " AND " . self::COL_ID . " = ? ";
                $where_valor[] = $busca[self::COL_ID];
            }
        }

        $where_condicao .= " AND " . self::COL_ID . " NOT LIKE ? ";
        $where_valor[] = '%DEP%';
        $where_condicao .= " AND " . self::COL_ID . " NOT LIKE ? ";
        $where_valor[] = '%VER';
        
        $retorno = [];

        try {
            $retorno = $this->read($database, self::TABELA, $campos, $where_condicao, $where_valor, null, $ordem, null);
            // echo $this->pegarUltimoSQL();
            // echo "\n";
        } catch (\Throwable $th) {
            echo "Mensagem: " . $th->getMessage() . "\n Local: " . $th->getTraceAsString();
            return false;
        }

        return $retorno;
    }

}