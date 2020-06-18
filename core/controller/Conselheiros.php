<?php



namespace core\controller;

use core\model\Permissao;
use core\model\Turma;
use core\model\Usuario;
use core\sistema\Autenticacao;
use \Exception;

class Conselheiros
{

	/**
	 * Cadastra um usuário com permissão de conselheiro 
	 *
	 * @param  mixed $dados
	 * @return void
	 */
	public function cadastrar($dados)
	{
		$turma = $dados['turma'];
		$data_inicio = date('Y-m-d');
		$senha = $dados['senha'];
		$pessoa = $dados['pessoa'];
		$matricula = $dados['email'];

		$u = new Usuario();
		$usuario = $this->verificarUsuarioExistente($pessoa);

		$permissao = true;

		if ($usuario) {
			$permissao = $this->addPermissao($usuario);
			$usuario = $u->alterar([
				Usuario::COL_ID => $usuario,
				Usuario::COL_MATRICULA => $matricula,
				Usuario::COL_TURMA => $turma,
				Usuario::COL_SENHA => $senha,
				Usuario::COL_PESSOA => $pessoa
			]);
		} else {
			$usuario = $u->adicionar([
				Usuario::COL_MATRICULA => $matricula,
				Usuario::COL_TURMA => $turma,
				Usuario::COL_DATA_INICIO => $data_inicio,
				Usuario::COL_SENHA => $senha,
				Usuario::COL_PESSOA => $pessoa
			]);

			$permissao = $this->addPermissao($usuario);
		}

		if ($usuario > 0 && $permissao) {
			http_response_code(200);
			return json_encode(array('message' => 'O conselheiro foi cadastrado!'));
		} else {
			http_response_code(500);
			return json_encode(array('message' => 'Não foi possível cadastrar o coordenador!'));
		}
	}

	/**
	 * Atualiza o coordenador de curso
	 *
	 * @param  mixed $dados
	 * @return void
	 */
	public function atualizarConselheiro($dados)
	{
		$turma_id = $dados['turma'];
		$resultado = $this->desabilitarConselheiro($turma_id);

		if ($resultado) {
			$retorno = $this->cadastrar($dados);
			if ($retorno) {
				http_response_code(200);
				return json_encode(array("message" => "O conselheiro foi atualizado com sucesso!"));
			}

			http_response_code(500);
			return json_encode(array('message' => "Não foi possível desabilitar o atual coordenador"));
		}
	}

	/**
	 * Retira a permissão de coordenador de um usuário
	 *
	 * @param  mixed $turma
	 * @return void
	 */
	private function desabilitarConselheiro($turma)
	{
		$conselheiro = $this->selecionarConselheiroAtual($turma);
		$conselheiro_id = $conselheiro[Usuario::COL_ID];

		$retorno = $this->delPermissao($conselheiro_id);

		return $retorno;
	}

	/**
	 * Obtem as informações de um conselheiro
	 *
	 * @param  mixed $turma
	 * @return void
	 */
	public function obterConselheiro($turma)
	{

		$conselheiro = $this->selecionarConselheiroAtual($turma);

		$retorno = [];

		// print_r($conselheiro);
		if (count($conselheiro) > 0) {
			$s = new Servidores();
			$servidor = $s->selecionarServidor($conselheiro[Usuario::COL_PESSOA]);
			$retorno = [
				'id' => $conselheiro[Usuario::COL_ID],
				'login' => $conselheiro[Usuario::COL_MATRICULA],
				'nome' => $servidor['nome'],
				'pessoa' => $conselheiro[Usuario::COL_PESSOA]
			];
		}

		http_response_code(200);
		return json_encode($retorno);
	}


	/**
	 * Altera a senha de acesso de um usuário coordeandor
	 *
	 * @param  mixed $dados
	 * @return void
	 */
	public function alterarSenha($dados)
	{
		$usuario = $dados['codigo'];

		$data = array(
			Usuario::COL_PESSOA => $usuario,
			Usuario::COL_MATRICULA => $dados['email'],
			Usuario::COL_SENHA => $dados['senha']
		);


		$usuario = new Usuario();
		$retorno = $usuario->alterar($data);

		if ($retorno > 0) {
			http_response_code(200);
			return json_encode(array('message' => "A senha foi alterada!"));
		} else {
			http_response_code(500);
			return json_encode(array("message" => "Não foi possível alterar os dados!"));
		}
	}
	
	/**
	 * Obtem o conselheiro atual de uma turma
	 *
	 * @param  mixed $turma
	 * @return void
	 */
	public function selecionarConselheiroAtual($turma)
	{
		$usuario = new Usuario();


		$busca = [
			Usuario::COL_TURMA => $turma,
			'permissao' => Autenticacao::CONSELHEIRO,
			'periodo' => 'atual'
		];

		$conselheiro = $usuario->listar(null, $busca, null, 1)[0];
		return $conselheiro;
	}


	/**
	 * Retorna informações completas sobre o conselheiro
	 *
	 * @param  mixed $dados
	 * @return json
	 */
	public function selecionarConselheiro($dados)
	{
		$turma_id = $dados['turma'];
		$t = new Turmas();
		$turma = $t->informacoesTurma(['turma' => $turma_id]);
		$turma = json_decode($turma, true);
		$conselheiro = $this->obterConselheiro($turma_id);
		$conselheiro = json_decode($conselheiro, true);

		$retorno = [
			'turma' => $turma,
			'conselheiro' => $conselheiro
		];

		if ($turma > 0 && $conselheiro > 0) {

			http_response_code(200);
			return json_encode($retorno);
		} else {
			http_response_code(500);
			return json_encode(array("message" => "Não foi possível selecionar o conselheiro"));
		}
	}

	/**
	 * Verifica a existência de um usuário e caso exista, retorna o id do usuário
	 *
	 * @param  mixed $cod_pessoa
	 * @return void
	 */
	private function verificarUsuarioExistente($cod_pessoa = null)
	{
		if ($cod_pessoa == null) {
			throw new Exception("É necessário informar o COD_PESSOA");
		}
		$campos = Usuario::COL_ID;

		$busca = [Usuario::COL_PESSOA => $cod_pessoa];

		$u = new Usuario();
		$usuario = $u->listar($campos, $busca, null, 1)[0];

		if (!empty($usuario)) {
			return $usuario[Usuario::COL_ID];
		}

		return false;
	}


	/**
	 * Adiciona ao usuário a permissão de conselheiro
	 *
	 * @param  mixed $usuario_id
	 * @return void
	 */
	public function addPermissao($usuario_id)
	{
		if (!isset($usuario_id)) {
			throw new Exception("É necessário informar o id do usuário");
		}

		$resultado = true;
		$p = new Permissao();

		if (!$p->verificarPermissao($usuario_id, Autenticacao::CONSELHEIRO)) {
			$resultado = $p->adicionar($usuario_id, Autenticacao::CONSELHEIRO);
		}

		return $resultado;
	}

	/**
	 * Remove a permissão de conselheiro de um usuário
	 *
	 * @param  mixed $usuario_id
	 * @return void
	 */
	public function delPermissao($usuario_id)
	{
		if (!isset($usuario_id)) {
			throw new Exception("É necessário informar o usuário");
		}

		$resultado = true;
		$p = new Permissao();

		if ($p->verificarPermissao($usuario_id, Autenticacao::CONSELHEIRO)) {
			$resultado = $p->remover($usuario_id, Autenticacao::CONSELHEIRO);
		}

		return $resultado;
	}
}
