<?php



namespace core\model;

use core\CRUD;

class Turma extends CRUD
{

    const TABELA = "TURMAS";
    const COL_ID = "COD_TURMA";
    const COL_CURSO = "COD_CURSO";
    const COL_SIGLA_TURMA = "SIGLA_TURMA";
    const COL_DESC_TURMA = "DESC_TURMA";
    const COL_ANO_LET = "ANO_LET";
    const COL_COEFICIENTE_RENDIMENTO = "COEFICIENTE_RENDIMENTO";


    public function listar($campos = null, $busca = [], $ordem = null, $limite = null)
    {
        $database = "academico";

        $campos = $campos != null ? $campos : " * ";
        $ordem = $ordem != null ? $ordem : self::COL_ID . " ASC ";
        $limite = $limite != null ? " TOP {$limite}" : " TOP 1000";

        $campos = $limite . " " . $campos;

        $where_condicao = " 1 = 1 ";
        $where_valor = [];

        $tabela = self::TABELA;
        $group_by = null;
        if ($busca && count($busca) > 0) {
            if (isset($busca[self::COL_ID]) && !empty($busca[self::COL_ID])) {
                $where_condicao .= " AND " . self::COL_ID . " = ? ";
                $where_valor[] = $busca[self::COL_ID];
            }

            if (isset($busca['avaliadas'])) {
                $where_condicao .= " AND " . self::COL_ID . " NOT IN ({$busca['avaliadas']}) ";
            }

            if (isset($busca['ano'])) {
                if ($busca['ano'] == 'atual') {
                    $where_condicao .= " AND " . self::COL_ANO_LET . " = YEAR(GETDATE())";
                }
            }

            if (isset($busca[self::COL_COEFICIENTE_RENDIMENTO])) {
                $tabela = $tabela .
                    " INNER JOIN MATRICULAS on TURMAS.COD_CURSO = MATRICULAS.COD_CURSO
                     INNER JOIN ALUNOS ON MATRICULAS.COD_ALUNO = ALUNOS.COD_ALUNO
                     INNER JOIN PESSOAS on ALUNOS.COD_PESSOA = PESSOAS.COD_PESSOA ";

                $where_condicao .= " AND (" .
                    self::TABELA . "." . self::COL_CURSO . " = 80 OR " .
                    self::TABELA . "." . self::COL_CURSO . " = 862 OR " .
                    self::TABELA . "." . self::COL_CURSO . " = 851" .
                    " ) ";

                $where_condicao .= " AND COD_TURMA_ATUAL = ? ";
                $where_valor[] = $busca[self::COL_COEFICIENTE_RENDIMENTO];
                $group_by = " COD_TURMA_ATUAL ";
                $ordem = " COD_TURMA_ATUAL ";
            }
        }

        $where_condicao .= " AND " . self::COL_ID . " NOT LIKE ? ";
        $where_valor[] = '%DEP%';
        $where_condicao .= " AND " . self::COL_ID . " NOT LIKE ? ";
        $where_valor[] = '%VER';

        $where_condicao .= " AND (COD_CURSO = 80 OR COD_CURSO = 862 OR COD_CURSO = 851) ";
        $retorno = [];

        try {
            $retorno = $this->read($database, $tabela, $campos, $where_condicao, $where_valor, $group_by, $ordem, null);
            // echo $this->pegarUltimoSQL();
            // echo "\n";
        } catch (\Throwable $th) {
            echo "Mensagem: " . $th->getMessage() . "\n Local: " . $th->getTraceAsString();
            return false;
        }

        return $retorno;
    }
}
