<?php

namespace core\model;

use core\CRUD;

class Aluno extends CRUD
{


    const TABELA = "ALUNOS";
    const COL_MATRICULA = "MATRICULA";
    const COL_NOME_PESSOA = "NOME_PESSOA";
    const COL_COD_TURMA_ATUAL = "COD_TURMA_ATUAL";
    const COL_COEFICIENTE_RENDIMENTO = "COEFICIENTE_RENDIMENTO";

    public function listar($campos = null, $busca = [], $ordem = null, $limite = null)
    {
        $database = "academico";

        $campos = $campos != null ? $campos : " * ";
        $ordem = $ordem != null ? $ordem : self::COL_MATRICULA;
        $limite = $limite != null ? " TOP {$limite} " : " TOP 1000 ";

        $campos = $limite . " " . $campos;


        $tabela = self::TABELA;
        $where_condicao = " 1 = 1 ";
        $where_valor = [];


        if ($busca && count($busca) > 0) {
            if (isset($busca[self::COL_COD_TURMA_ATUAL]) && !empty($busca[self::COL_COD_TURMA_ATUAL])) {
                $where_condicao .= " AND " . self::COL_COD_TURMA_ATUAL . " = ? ";
                $where_valor[] = $busca[self::COL_COD_TURMA_ATUAL];

                $tabela = self::TABELA .
                    " INNER JOIN MATRICULAS ON ALUNOS.COD_ALUNO = MATRICULAS.COD_ALUNO " .
                    " INNER JOIN PESSOAS ON ALUNOS.COD_PESSOA = PESSOAS.COD_PESSOA ";

                $ordem = "PESSOAS.NOME_PESSOA";
            }
            if (isset($busca[self::COL_MATRICULA]) && !empty($busca[self::COL_MATRICULA])) {
                $where_condicao .= " AND " . self::COL_MATRICULA . " = ? ";
                $where_valor[] = $busca[self::COL_MATRICULA];

                $tabela = self::TABELA .
                    " INNER JOIN MATRICULAS ON ALUNOS.COD_ALUNO = MATRICULAS.COD_ALUNO " .
                    " INNER JOIN PESSOAS ON ALUNOS.COD_PESSOA = PESSOAS.COD_PESSOA ";
            }
        }

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
