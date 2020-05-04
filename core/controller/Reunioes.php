<?php

namespace core\controller;

use core\model\Reuniao;

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
			return array('message' => 'As reuniões foram iniciadas!');
		} else {
			http_response_code(500);
			return array('message' => 'Não foi possível iniciar as reuniões!');
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
			return array('message'  => 'As reuniões foram finalizadas!');
		} else {
			http_response_code(500);
			return array('message' => 'Não foi possível finalizar as reuniões!');
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
			return array('message' => 'A memória da reunião foi salva!');
		} else {
			http_response_code(500);
			return array('message' => 'Não foi possível salvar a memória solicitada!');
		}
	}
}
