<?php


namespace core\model;

use core\CRUD;


class Professor extends CRUD
{


    const TABELA = "PROFESSORES";
    const COL_COD_PESSOA = "COD_PESSOA";
    const COL_COD_INSTITUICAO = "COD_INSTITUICAO";
    const COL_EMAIL = "EMAIL";

    public function listar($campos = null, $busca = [], $ordem = null, $limite = null)
    {
        $database = "academico";

        $campos = $campos != null ? $campos : " * ";
        $ordem = $ordem != null ? $ordem : self::TABELA . "." . self::COL_COD_PESSOA;
        $limite = $limite != null ? " TOP {$limite} " : " TOP 1000 ";


        $campos = $limite . " " . $campos;

        $tabela = self::TABELA;
        $where_condicao = " 1 = 1 ";
        $where_valor = [];

        if ($busca && count($busca) > 0) {
            if (isset($busca[self::COL_COD_PESSOA]) && !empty($busca[self::COL_COD_PESSOA])) {
                $where_condicao .= " AND " . self::TABELA . "." . self::COL_COD_PESSOA . " = ? ";
                $where_valor[] = $busca[self::COL_COD_PESSOA];

                $tabela = self::TABELA .
                    " INNER JOIN PAUTAS ON PROFESSORES.COD_PROFESSOR = PAUTAS.COD_PROFESSOR " .
                    " INNER JOIN PESSOAS ON PROFESSORES.COD_PESSOA = PESSOAS.COD_PESSOA ";

                // Filtratndo apenas as pautas dos cursos técnicos integrados
                $where_condicao .= " AND COD_TIPO_CURSO = ? ";
                $where_valor[] = '265';
            }

            if (isset($busca['ano_letivo']) && !empty($busca['ano_letivo'])) {

                if ($busca['ano_letivo'] == 'atual') {
                    $where_condicao .= " AND ANO_LET = YEAR(GETDATE()) ";
                } else {
                    $where_condicao .= " AND ANO_LET = ? ";
                    $where_valor[] = $busca['ano_letivo'];
                }
            }

            if (isset($busca['turma']) && !empty($busca['turma'])) {
                $where_condicao .= " AND " .  Turma::COL_ID . " = ? ";
                $where_valor[] = $busca['turma'];

                $tabela = self::TABELA .
                    " INNER JOIN PAUTAS ON PROFESSORES.COD_PROFESSOR = PAUTAS.COD_PROFESSOR" .
                    " INNER JOIN PESSOAS ON PROFESSORES.COD_PESSOA = PESSOAS.COD_PESSOA ";

                // Filtratndo apenas as pautas dos cursos técnicos integrados
                $where_condicao .= " AND COD_TIPO_CURSO = ? ";
                $where_valor[] = '265';
            }

            if (isset($busca['professores'])) {

                $tabela = self::TABELA . " INNER JOIN PESSOAS ON PROFESSORES.COD_PESSOA = PESSOAS.COD_PESSOA ";
            }
        }

        // Filtrando apenas os professores do CAMPUS CERES
        $where_condicao .= " AND " . self::TABELA . "." . self::COL_COD_INSTITUICAO . " = ? ";
        $where_valor[] = '3';


        $retorno = [];

        try {
            $retorno = $this->read($database, $tabela, $campos, $where_condicao, $where_valor, null, null);
            // echo $this->pegarUltimoSQL();
        } catch (\Throwable $th) {
            echo $this->pegarUltimoSQL();
            echo "Mensagem: " . $th->getMessage() . "\n Local: " . $th->getTraceAsString();
            return false;
        }

        return $retorno;
    }
}
