<?php

namespace core\controller;

use core\model\Reuniao;
use core\model\Turma;

class Reunioes
{

	private $reuniao_id = null;
	private $turma_cod = null;
	private $data = null;
	private $etapaConselho = null;
	private $finalizado = null;
	private $memoria = null;


	public function __set($atributo, $valor)
	{
		$this->$atributo = $valor;
	}

	public function __get($atributo)
	{
		return $this->$atributo;
	}

	public function cadastrar($dados)
	{
		$turmas = $dados['turmas'];

		$dataAtual = date('Y-m-d');
		$etapaAtual = 1;

		$reuniao = new Reuniao();

		foreach ($turmas as $turma) {
			$resultado = $reuniao->adicionar(
				[
					Reuniao::COL_COD_TURMA => $turma,
					Reuniao::COL_DATA => $dataAtual,
					Reuniao::COL_ETAPA_CONSELHO => $etapaAtual
				]
			);
		}

		if ($resultado > 0) {
			http_response_code(200);
			return json_encode(array('message' => 'As reuniões foram iniciadas!'));
		} else {
			http_response_code(500);
			return json_encode(array('message' => 'Não foi possível iniciar as reuniões!'));
		}
	}

	public function finalizarReuniao($dados)
	{
		$reunioes = $dados['reunioes'];

		$reuniao = new Reuniao();

		$erros = array();
		foreach ($reunioes as $r) {
			$resultado = $reuniao->alterar(
				[
					Reuniao::COL_ID => $r,
					Reuniao::COL_FINALIZADO => 1
				]
			);
			if (!($resultado > 0)) {
				array_push($erros, $r);
			}
		}
		if (sizeof($erros) == 0) {
			http_response_code(200);
			return json_encode(array('message'  => 'As reuniões foram finalizadas!'));
		} else {
			http_response_code(500);
			return json_encode(array('message' => 'Não foi possível finalizar as reuniões!'));
		}
	}

	public function salvarMemoria($dados)
	{
		$reuniao_id = $dados['reuniao'];
		$memoria = $dados['memoria'];

		$reuniao = new Reuniao();
		$memoriaReuniao = [Reuniao::COL_ID => $reuniao_id, Reuniao::COL_MEMORIA => $memoria];
		$resultado = $reuniao->alterar($memoriaReuniao);

		if ($resultado && $resultado > 0) {
			http_response_code(200);
			return json_encode(array('message' => 'A memória da reunião foi salva!'));
		} else {
			http_response_code(500);
			return json_encode(array('message' => 'Não foi possível salvar a memória solicitada!'));
		}
	}

	public function selecionarMemoria($dados)
	{
		$reuniao_id = $dados['reuniao'];

		$reuniao = new Reuniao();

		$campos = Reuniao::COL_MEMORIA;
		$busca = [Reuniao::COL_ID => $reuniao_id];
		$memoriaReuniao = ($reuniao->listar($campos, $busca, null, 1))[0];

		if (count($memoriaReuniao) > 0) {
			http_response_code(200);
			return json_encode($memoriaReuniao);
		} else {
			http_response_code(500);
			return json_encode(array('message' => 'Não foi possível obter a memória da reunião solicitada!'));
		}
	}


	/**
	 * Lista as reuniões não finalizadas
	 */
	public function listarReunioesAndamento($dados)
	{

		$reuniao = new Reuniao();


		$campos = Reuniao::COL_ID . ", " .
			Reuniao::COL_COD_TURMA;

		$busca = [Reuniao::COL_FINALIZADO => '0'];
		$reunioes = $reuniao->listar($campos, $busca, null, 1000);


		$turmas = new Turmas();

		$reunioesFiltradas = [];

		// Caso o curso seja passado, lista apenas as reuniões do curso
		// Caso não exista curso, todas as reuniões serão retornadas

		if (isset($dados['curso']) && !empty($dados['curso'])) {

			foreach ($reunioes as $reuniao) {
				if ($turmas->verificarTurmaCurso($reuniao[Turma::COL_ID], $dados['curso'])) {
					$turma = $turmas->informacoesTurma(['turma' => $reuniao[Turma::COL_ID]]);

					$turma = json_decode($turma, true);
					$turma['reuniao'] = $reuniao[Reuniao::COL_ID];

					array_push($reunioesFiltradas, $turma);
				}
			}
		} else {

			foreach ($reunioes as $reuniao) {
				$turma = $turmas->informacoesTurma(['turma' => $reuniao[Turma::COL_ID]]);

				$turma = json_decode($turma, true);
				$turma['reuniao'] = $reuniao[Reuniao::COL_ID];

				array_push($reunioesFiltradas, $turma);
			}
		}


		http_response_code(200);
		return json_encode($reunioesFiltradas);
	}

	public function reunioesNaoIniciadas($dados)
	{

		//  Retorna uma lista contendo os COD_TURMA de turmas que não participam do conselho a mais de 30 dias
		$turmasForaReuniaoAtual = $this->turmasReunioesFinalizadas();

		// Retorna uma lista de turmas que já participaram de uma reunião
		$turmasParticipantesReunioesPassadas = $this->turmasAvaliadasReuniao();

		$turmas = new Turmas();

		// Retorna uma lista das turmas que nunca participaram do conselho
		$turmasForaReuniao = $turmas->turmasForaReuniao($turmasParticipantesReunioesPassadas);


		$turmasCompletas = [];

		foreach ($turmasForaReuniaoAtual as $t) {
			$retorno = $turmas->informacoesTurma(['turma' => $t]);

			$retorno = json_decode($retorno, true);
			array_push($turmasCompletas, $retorno);
		}

		foreach ($turmasForaReuniao as $t) {
			$retorno = $turmas->informacoesTurma(['turma' => $t]);

			$retorno = json_decode($retorno, true);
			array_push($turmasCompletas, $retorno);
		}

		// print_r($turmasCompletas);

		http_response_code(200);
		return json_encode($turmasCompletas);
	}

	/**
	 * Retorna uma lista contendo os COD_TURMA de turmas que não participam do conselho a mais de 30 dias
	 */
	public function turmasReunioesFinalizadas()
	{
		$reuniao = new Reuniao();

		$campos = Reuniao::COL_COD_TURMA;

		$busca = [Reuniao::COL_FINALIZADO => '1', 'periodo' => 30, 'ano' => 'atual'];

		$retorno = $reuniao->listar($campos, $busca, null, 1000);

		$turmasIds = [];
		if (count($retorno) > 0 && !empty($retorno[0])) {
			$turmasIds = array_map(function ($id) {
				return $id[Reuniao::COL_COD_TURMA];
			}, $retorno);
		}

		return $turmasIds;
	}

	/**
	 * Retorna uma lista de turmas que já participaram de uma reunião
	 */
	public function turmasAvaliadasReuniao()
	{
		$reuniao = new Reuniao();

		$campos = " DISTINCT " . Reuniao::COL_COD_TURMA;

		$busca = ['ano' => 'atual'];

		$retorno = $reuniao->listar($campos, $busca, null, 1000);

		$turmasIds = [];
		if (count($retorno) > 0 && !empty($retorno[0])) {
			$turmasIds = array_map(function ($id) {
				return $id[Reuniao::COL_COD_TURMA];
			}, $retorno);
		}


		return $turmasIds;
	}
}
