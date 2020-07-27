<?php

namespace core\controller;

use core\model\Reuniao;
use core\model\Turma;
use core\sistema\Autenticacao;

class Reunioes
{


	/**
	 * Cadastra uma reunião
	 *
	 * @param  mixed $dados
	 * @return void
	 */
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

	/**
	 * Finaliza a reunião especificada
	 *
	 * @param  mixed $dados['reuniao']
	 * @return void
	 */
	public function finalizarReuniao($dados)
	{
		$reuniao = $dados['reuniao'];

		$r = new Reuniao();

		$resultado = $r->alterar([
			Reuniao::COL_ID => $reuniao,
			Reuniao::COL_FINALIZADO => 1
		]);

		if ($resultado > 0) {
			http_response_code(200);
			return json_encode(array('message' => 'A reunião foi finalizada!'));
		} else {
			http_response_code(500);
			return json_encode(array('message' => 'Não foi possível finalizar as reuniões'));
		}
	}

	/**
	 * Finaliza todas as reuniões passadas
	 *
	 * @param  array $dados['reunioes']
	 * @return void
	 */
	public function finalizarReunioes($dados)
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

	/**
	 * Cadastra a memória da reunião
	 *
	 * @param  mixed $dados
	 * @return void
	 */
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

	/**
	 * Obtem a memória de reunião salva
	 *
	 * @param  mixed $dados
	 * @return void
	 */
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
	 * Retorna todas informações das reuniões em andamento
	 * Andamento => COL_FINALIZADO == 1 
	 *
	 * @param  mixed $dados
	 * @return void
	 */
	public function listarReunioesAndamento($dados = [])
	{

		$curso_id = (isset($dados['curso'])) ? $dados['curso']  : null;

		$reuniao = new Reuniao();


		$campos = Reuniao::COL_ID . ", " .
			Reuniao::COL_COD_TURMA;

		$busca = [Reuniao::COL_FINALIZADO => '0'];
		$reunioes = $reuniao->listar($campos, $busca, null, 1000);


		$turmas = new Turmas();

		$reunioesFiltradas = [];

		// Caso o curso seja passado, lista apenas as reuniões do curso
		// Caso não exista curso, todas as reuniões serão retornadas

		// Verifica se existem reuniões retornadas 
		if (count($reunioes) > 0 && !empty($reunioes[0])) {

			// Verifica se existe curso especificado

			foreach ($reunioes as $reuniao) {
				$turma = $turmas->informacoesTurma(['turma' => $reuniao[Turma::COL_ID]]);

				$turma = json_decode($turma, true);
				$turma['reuniao'] = $reuniao[Reuniao::COL_ID];


				if ($curso_id != null && $turmas->verificarTurmaCurso($reuniao[Turma::COL_ID], $curso_id)) {
					$reunioesFiltradas[] = $turma;
				}

				if ($curso_id == null) {
					$reunioesFiltradas[] = $turma;
				}
			}
		}


		http_response_code(200);
		return json_encode($reunioesFiltradas);
	}

	/**
	 * Retorna todos os códigos das turmas que estão presentes em reunião
	 *
	 * @param  mixed $dados
	 * @return void
	 */
	public function listarTurmasEmReuniao($dados = [])
	{
		$reunioes = $this->listarReunioesAndamento($dados);
		$reunioes = json_decode($reunioes, true);

		$turmas = [];

		if (count($reunioes) > 0 && !empty($reunioes[0])) {
			$turmas = array_map(function ($turma) {
				return $turma['codigo'];
			}, $reunioes);
		}

		return $turmas;
	}

	/**
	 * Retorna todas informações das turmas fora de reunião
	 *
	 * @param  mixed $dados
	 * @return array
	 */
	public function reunioesNaoIniciadas($dados)
	{

		//  Retorna uma lista contendo os COD_TURMA de turmas que não participam do conselho a mais de 30 dias
		$turmasForaReuniaoAtual = $this->turmasReunioesFinalizadas();

		// Retorna uma lista de turmas que já participaram de uma reunião
		$turmasParticipantesReunioesPassadas = $this->turmasAvaliadasReuniao();

		$turmas = new Turmas();

		// Retorna uma lista das turmas que nunca participaram do conselho
		$turmasForaReuniao = $turmas->turmasForaReuniao($turmasParticipantesReunioesPassadas);


		$turmas_fora = array_merge($turmasForaReuniaoAtual, $turmasForaReuniao);
		$turmas_fora = array_unique($turmas_fora);

		$curso = (isset($dados['curso']) && !empty($dados['curso'])) ? $dados['curso'] : null;
		$turmasCompletas = [];

		if (count($turmas_fora) > 0 && !empty($turmas_fora[0])) {
			foreach ($turmas_fora as $t) {
				$retorno = $turmas->informacoesTurma(['turma' => $t]);
	
				$retorno = json_decode($retorno, true);
	
				if ($curso != null) {
					if ($retorno['codigo_curso'] == $curso) {
						unset($retorno['codigo_curso']);
						array_push($turmasCompletas, $retorno);
					}
				} else {
					unset($retorno['codigo_curso']);
					array_push($turmasCompletas, $retorno);
				}
			}		
		}

		// print_r($turmasCompletas);

		http_response_code(200);
		return json_encode($turmasCompletas);
	}


	/**
	 * Retorna os COD_TURMA de turmas que não participam do conselho a mais de 30 dias
	 *
	 * @return array
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
	 *
	 * @return array
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

	/**
	 * Verifica se a turma contida na token possui está em algum conselho
	 *
	 * @param  mixed $dados
	 * @return void
	 */
	public function verificarTurmaReuniao($dados)
	{
		$token = $dados['token'];

		$cod_turma = Autenticacao::obterTurma($token);


		if ($cod_turma) {

			$turmas_em_reuniao = $this->listarReunioesAndamento();
			$turmas_em_reuniao = json_decode($turmas_em_reuniao, true);


			$reuniao = null;
			foreach ($turmas_em_reuniao as $turma_em_reuniao) {
				if ($turma_em_reuniao['codigo'] === $cod_turma) {
					$reuniao = $turma_em_reuniao['reuniao'];
				}
			}

			if ($reuniao !== null) {
				http_response_code(200);
				return json_encode(array('message' => 'A turma possui uma reunião em andamento!', 'reuniao' => $reuniao));
			} else {
				http_response_code(500);
				return json_encode(array('message' => 'A turma não possui algum conselho em andamento!'));
			}
		} else {
			http_response_code(400);
			return json_encode(array('message' => 'A turma especificada não foi encontrada!'));
		}
	}

	/**
	 * Verifica se a turma está em reunião com base no token
	 *
	 * @param  mixed $token
	 * @return void
	 */
	public function turma_em_reuniao($token)
	{
		$cod_turma = Autenticacao::obterTurma($token);


		if ($cod_turma) {

			$turmas_em_reuniao = $this->listarReunioesAndamento();
			$turmas_em_reuniao = json_decode($turmas_em_reuniao, true);


			$reuniao = null;
			foreach ($turmas_em_reuniao as $turma_em_reuniao) {
				if ($turma_em_reuniao['codigo'] === $cod_turma) {
					$reuniao = $turma_em_reuniao['reuniao'];
				}
			}

			if ($reuniao !== null) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Retorna a reunião em andamento de uma turma, com base na sua turma, com base na token 
	 * kekeke
	 *
	 * @param  mixed $dados
	 * @return void
	 */
	public function obterReuniaoTurma($token = null)
	{


		$cod_turma = Autenticacao::obterTurma($token);

		$reuniao = false;

		if ($cod_turma) {

			$turmas_em_reuniao = $this->listarReunioesAndamento();

			$turmas_em_reuniao = json_decode($turmas_em_reuniao, true);


			foreach ($turmas_em_reuniao as $turma_em_reuniao) {
				if ($turma_em_reuniao['codigo'] === $cod_turma) {
					$reuniao = $turma_em_reuniao['reuniao'];
				}
			}
		}

		return $reuniao;
	}

	/**
	 * Retorna todas as reuniões que foram encerradas
	 *
	 * @param  mixed $dados
	 * @return void
	 */
	public function reunioesEncerradas($dados)
	{
		$curso_id = (isset($dados['curso'])) ? $dados['curso']  : null;

		$r = new Reuniao();


		$campos = Reuniao::COL_ID . ", " .
			Reuniao::COL_COD_TURMA . ", " .
			Reuniao::COL_DATA;

		$busca = [Reuniao::COL_FINALIZADO => '1'];
		$reunioes = $r->listar($campos, $busca, null, 1000);


		$turmas = new Turmas();

		$reunioesFiltradas = [];

		// Caso o curso seja passado, lista apenas as reuniões do curso
		// Caso não exista curso, todas as reuniões serão retornadas

		// Verifica se existem reuniões retornadas 
		if (count($reunioes) > 0 && !empty($reunioes[0])) {

			// Verifica se existe curso especificado

			foreach ($reunioes as $reuniao) {
				$turma = $turmas->informacoesTurma(['turma' => $reuniao[Turma::COL_ID]]);

				$turma = json_decode($turma, true);
				$turma['reuniao'] = $reuniao[Reuniao::COL_ID];

				// $turma['data'] = ;
				$data = strtotime($reuniao[Reuniao::COL_DATA]);
				$data = date('Y', $data);

				$turma['data'] = date('j-m-Y');

				$turma['etapa_avaliativa'] = 1;




				if ($curso_id != null && $turmas->verificarTurmaCurso($reuniao[Turma::COL_ID], $curso_id)) {
					$reunioesFiltradas[$data][] = $turma;
				}

				if ($curso_id == null) {
					$reunioesFiltradas[$data][] = $turma;
				}
			}
		}


		http_response_code(200);
		return json_encode($reunioesFiltradas);
	}
}
