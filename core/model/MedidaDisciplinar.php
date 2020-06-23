<?php


namespace core\model;

use core\CRUD;


class MedidaDisciplinar extends CRUD
{

	const TABELA = "MEDIDAS_DISCIPLINARES";
	const COL_COD_MEDIDA_DISCIPLINAR = "COD_MEDIDA_DISCIPLINAR";
	const COL_MATRICULA = "MATRICULA";
	const COL_COD_TURMA_ATUAL = "COD_TURMA_ATUAL";
	const COL_OBSERVACOES = "OBSERVACOES";
	const COL_DATA = "DT_MEDIDA_DISCIPLINAR";

	const COL_COD_TIPO_MEDIDA_DISCIPLINAR = "COD_TIPO_MEDIDA_DISCIPLINAR";
	const COL_DESC_TIPO_MEDIDA_DISCIPLINAR =  "DESC_TIPO_MEDIDA_DISCIPLINAR";

	public function listar($campos = null, $busca = [], $ordem = null, $limite = null)
	{
		$database = "academico";

		$campos = $campos != null ? $campos : " * ";
		$ordem = $ordem != null ? $ordem : self::COL_COD_MEDIDA_DISCIPLINAR;
		$limite = $limite != null ? " TOP {$limite} " : " TOP 1000 ";

		$campos = $limite . " " . $campos;

		$where_condicao = " 1 = 1 ";
		$where_valor = [];



		if ($busca && count($busca) > 0) {
			if (isset($busca[self::COL_COD_TURMA_ATUAL]) && !empty($busca[self::COL_COD_TURMA_ATUAL])) {
				$where_condicao .= " AND " . self::COL_COD_TURMA_ATUAL . " = ? ";
				$where_valor[] = $busca[self::COL_COD_TURMA_ATUAL];

				$tabela = self::TABELA .
					" INNER JOIN MATRICULAS ON MATRICULAS.COD_MATRICULA = MEDIDAS_DISCIPLINARES.COD_MATRICULA " .
					" INNER JOIN TIPOS_MEDIDAS_DISCIPLINARES ON MEDIDAS_DISCIPLINARES.COD_TIPO_MEDIDA_DISCIPLINAR = TIPOS_MEDIDAS_DISCIPLINARES.COD_TIPO_MEDIDA_DISCIPLINAR ";
			}

			if (isset($busca[self::COL_MATRICULA]) && !empty($busca[self::COL_MATRICULA])) {
				$where_condicao .= " AND " . self::COL_MATRICULA . " = ? ";
				$where_valor[] = $busca[self::COL_MATRICULA];

				$tabela = self::TABELA .
					" INNER JOIN TIPOS_MEDIDAS_DISCIPLINARES ON MEDIDAS_DISCIPLINARES.COD_TIPO_MEDIDA_DISCIPLINAR = TIPOS_MEDIDAS_DISCIPLINARES.COD_TIPO_MEDIDA_DISCIPLINAR " .
					" INNER JOIN MATRICULAS ON MATRICULAS.COD_MATRICULA = MEDIDAS_DISCIPLINARES.COD_MATRICULA " .
					" INNER JOIN ALUNOS ON  MATRICULAS.COD_ALUNO = ALUNOS.COD_ALUNO" .
					" INNER JOIN PESSOAS ON ALUNOS.COD_PESSOA = PESSOAS.COD_PESSOA ";
			}

			if (isset($busca[self::COL_COD_MEDIDA_DISCIPLINAR]) && !empty($busca[self::COL_COD_MEDIDA_DISCIPLINAR])) {
				$where_condicao .= " AND " . self::COL_COD_MEDIDA_DISCIPLINAR . " = ? ";
				$where_valor[] = $busca[self::COL_COD_MEDIDA_DISCIPLINAR];

				$tabela = self::TABELA .
					" INNER JOIN MATRICULAS ON MATRICULAS.COD_MATRICULA = MEDIDAS_DISCIPLINARES.COD_MATRICULA " .
					" INNER JOIN TIPOS_MEDIDAS_DISCIPLINARES ON MEDIDAS_DISCIPLINARES.COD_TIPO_MEDIDA_DISCIPLINAR = TIPOS_MEDIDAS_DISCIPLINARES.COD_TIPO_MEDIDA_DISCIPLINAR ";
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
