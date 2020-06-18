<?php


namespace core\controller;

use core\model\Aluno;
use core\model\Permissao;
use core\model\Usuario;
use core\sistema\Autenticacao;
use Exception;

class Representantes
{

    /**
     * Cadastra um usuário com permissão de representante de turma
     *
     * @param  mixed $dados
     * @return void
     */
    public function cadastrar($dados)
    {

        // Ver se a matrícula está em um representante atual, se tiver altera, senão cadastra
        $matricula = $dados['matricula'];
        $turma = $dados['turma'];
        $senha = $dados['senha'];

        $data_inicio = date('Y-m-d');


        $u = new Usuario();


        $usuario = $this->verificarUsuarioExistente($matricula);

        $permissao = true;

        if ($usuario) {
            $permissao = $this->addPermissao($usuario);
            $usuario = $u->alterar([
                Usuario::COL_ID => $usuario,
                Usuario::COL_MATRICULA => $matricula,
                Usuario::COL_TURMA => $turma,
                Usuario::COL_SENHA => $senha
            ]);
        } else {
            $usuario = $u->adicionar([
                Usuario::COL_MATRICULA => $matricula,
                Usuario::COL_TURMA => $turma,
                Usuario::COL_DATA_INICIO => $data_inicio,
                Usuario::COL_SENHA => $senha
            ]);

            $permissao = $this->addPermissao($usuario);
        }

        if ($usuario > 0 && $permissao) {
            http_response_code(200);
            return json_encode(array('message' => "O representante foi cadastrado!"));
        } else {
            http_response_code(500);
            return json_encode(array('message' => 'Não foi possível cadastrar o representante!'));
        }
    }

    
    /**
     * Retorna o representante atual de uma turma
     *
     * @param  mixed $turma
     * @return void
     */
    public function selecionarRepresentanteAtual($turma)
    {
        $usuario = new Usuario();
        $busca = [
            Usuario::COL_TURMA => $turma,
            'periodo' => 'atual',
            'permissao' => Autenticacao::REPRESENTANTE
        ];

        $representantes = $usuario->listar(null, $busca, null, 1)[0];

        return  $representantes;
    }
    
    /**
     * Retorna o código e nome do representante
     *
     * @param  mixed $dados
     * @return void
     */
    public function selecionarRepresentante($dados)
    {
        $representante = $this->selecionarRepresentanteAtual($dados['turma']);
        $retorno = [];
        if (!empty($representante)) {
            $aluno = new Alunos();
            $representante = $aluno->selecionar($representante[Usuario::COL_MATRICULA]);

            $representante = [
                'codigo' => $representante['matricula'],
                'nome' => $representante['nome']
            ];

            array_push($retorno, $representante);
        }
        http_response_code(200);
        return json_encode($retorno);
    }
    
    /**
     * Retorna as informações de um representante atual de uma turma
     *
     * @param  mixed $turma
     * @return void
     */
    public function obterRepresentante($turma)
    {
        $representante = $this->selecionarRepresentanteAtual($turma);

        $retorno = [];

        if (!empty($representante)) {

            $aluno = new Alunos();
            $representante = $aluno->selecionar($representante[Usuario::COL_MATRICULA]);

            $representante = [
                'codigo' => $representante['matricula'],
                'nome' => $representante['nome']
            ];

            array_push($retorno, $representante);
        }

        http_response_code(200);
        return json_encode($retorno);
    }

        
    /**
     * Atualiza o representante atual de uma turma
     *
     * @param  mixed $dados
     * @return void
     */
    public function atualizarRepresentante($dados)
    {
        $turma_id = $dados['turma'];
        $resultado = $this->desabilitarRepresentante($turma_id);

        if ($resultado) {
            $retorno = $this->cadastrar($dados);

            if ($retorno) {
                http_response_code(200);
                return json_encode(array('message' => 'O representante foi atualizado com sucesso!'));
            }

            http_response_code(500);
            return json_encode(array('message' => 'Não foi possíve desabilitar o atual coordenador!'));
        }
    }
    
    /**
     * Retira a permissão de representante de um usuário
     *
     * @param  mixed $turma
     * @return void
     */
    private function desabilitarRepresentante($turma)
    {
        $representante = $this->selecionarRepresentanteAtual($turma);
        $representante_id = $representante[Usuario::COL_ID];
        $retorno = $this->delPermissao($representante_id);

        // Retira a turma do usuário
        $u = new Usuario();
        $u->alterar([Usuario::COL_TURMA => null, Usuario::COL_ID => $representante_id]);

        return $retorno;
    }
    
    /**
     * Altera a senha de um usuário representante
     *
     * @param  mixed $dados
     * @return void
     */
    public function alterarSenha($dados)
    {
        $turma = $dados['turma'];

        $usuario = $this->selecionarRepresentanteAtual($turma);

        $usuario = $usuario[Usuario::COL_ID];

        $data = array(
            Usuario::COL_ID => $usuario,
            Usuario::COL_MATRICULA => $dados['matricula'],
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
     * Verifica se um usuário existe, com base na matrícula
     *
     * @param  mixed $matricula
     * @return void
     */
    public function verificarUsuarioExistente($matricula = null)
    {
        if ($matricula == null) {
            throw new Exception("É ncessário informar a matrícula");
        }

        $campos = Usuario::COL_ID;
        $busca = [
            Usuario::COL_MATRICULA => $matricula
        ];

        $u = new Usuario();
        $usuario = $u->listar($campos, $busca, null, 1)[0];

        if (!empty($usuario)) {
            return $usuario[Usuario::COL_ID];
        }
        return false;
    }
    
    /**
     * Adiciona a permissão de Representante para um usuário
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

        if (!$p->verificarPermissao($usuario_id, Autenticacao::REPRESENTANTE)) {
            $resultado = $p->adicionar($usuario_id, Autenticacao::REPRESENTANTE);
        }
        return $resultado;
    }
    
    /**
     * Remove a permissão de representante de um usuário
     *
     * @param  mixed $usuario_id
     * @return void
     */
    public function delPermissao($usuario_id)
    {
        if (!isset($usuario_id)) {
            throw new Exception("É necessário informar o id do usuário");
        }

        $resultado = true;
        $p = new Permissao();

        if ($p->verificarPermissao($usuario_id, Autenticacao::REPRESENTANTE)) {
            $resultado = $p->remover($usuario_id, Autenticacao::REPRESENTANTE);
        }
        return $resultado;
    }
    
    /**
     * Retorna a turma, com base na token 
     *
     * @param  mixed $dados
     * @return void
     */
    public function obterTurma($dados)
    {
        $token = $dados['token'];

        $cod_turma = Autenticacao::obterTurma($token);

        if ($cod_turma) {
            http_response_code(200);
            return json_encode(array('message' => 'A turma foi obtida com sucesso', 'turma' => $cod_turma));
        } else {
            http_response_code(500);
            return json_encode(array('message' => 'Não foi possível obter a turma'));
        }
    }
}