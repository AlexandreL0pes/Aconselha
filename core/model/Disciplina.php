<?php

namespace core\model;

use core\CRUD;


class Disciplina extends CRUD
{

    const TABELA = "DISCIPLINAS";
    const COL_COD_DISCIPLINA = "COD_DISCIPLINA";
    const COL_DESC_DISCIPLINA = "DESC_DISCIPLINA";
    const COL_CURSO = "COD_CURSO";
    const COL_TURMA = "COD_TURMA";

    public function listar($campos = null, $busca = [], $ordem = null, $limite = null)
    {
        $database = "academico";

        $campos = $campos != null ? $campos : " * ";
        $ordem = $ordem != null ? $ordem : self::TABELA . "." . self::COL_COD_DISCIPLINA;
        $limite = $limite != null ? " TOP {$limite} " : " TOP 1000 ";

        $campos = $limite . " " . $campos;

        $tabela = self::TABELA;

        $where_condicao = " 1 = 1 ";
        $where_valor = [];


        if ($busca && count($busca) > 0) {
            if (isset($busca['curso']) && !empty($busca['curso'])) {
                $where_condicao .= " AND " . 'COD_CURSO' . " = ? ";
                $where_valor[] = $busca['curso'];

                $tabela = self::TABELA .
                    " INNER JOIN DISCIPLINAS_MATRIZES_CURRICULARES on DISCIPLINAS.COD_DISCIPLINA = DISCIPLINAS_MATRIZES_CURRICULARES.COD_DISCIPLINA " .
                    " INNER JOIN TURMAS on DISCIPLINAS_MATRIZES_CURRICULARES.COD_MATRIZ_CURRICULAR = TURMAS.COD_MATRIZ_CURRICULAR ";
            }

            if (isset($busca['turma']) && !empty($busca['turma'])) {
                $where_condicao .= " AND " . 'COD_TURMA' . " = ? ";
                $where_valor[] = $busca['turma'];

                $where_condicao .= " AND N_PERIODO = PERIODO";

                $tabela = self::TABELA .
                    " INNER JOIN DISCIPLINAS_MATRIZES_CURRICULARES on DISCIPLINAS.COD_DISCIPLINA = DISCIPLINAS_MATRIZES_CURRICULARES.COD_DISCIPLINA " .
                    " INNER JOIN TURMAS on DISCIPLINAS_MATRIZES_CURRICULARES.COD_MATRIZ_CURRICULAR = TURMAS.COD_MATRIZ_CURRICULAR ";
            }
            if (isset($busca[self::COL_COD_DISCIPLINA]) && !empty($busca[self::COL_COD_DISCIPLINA])) {
                $where_condicao .= " AND " . self::COL_COD_DISCIPLINA . " = ? ";
                $where_valor[] = $busca[self::COL_COD_DISCIPLINA];

            }
            
        }

        $retorno = [];

        try {
            $retorno = $this->read($database, $tabela, $campos, $where_condicao, $where_valor, null, $ordem, null);
        } catch (\Throwable $th) {
            echo "Mensagem: " . $th->getMessage() . "\n Local: " . $th->getTraceAsString();
            // echo $this->pegarUltimoSQL();
            return false;
        }

        return $retorno;
    }
}
