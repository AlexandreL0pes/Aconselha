<?php

namespace core\controller;

use core\model\Aluno;
use core\model\Permissao;
use core\model\Usuario;
use core\sistema\Autenticacao;
use Exception;

class ViceRepresentantes
{

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
            return json_encode(array('message' => "O coordenador foi cadastrado!"));
        } else {
            http_response_code(500);
            return json_encode(array('message' => 'Não foi possível cadastrar o coordenador!'));
        }
    }

    public function selecionarViceRepresentanteAtual($turma)
    {
        $usuario = new Usuario();
        $busca = [
            Usuario::COL_TURMA => $turma,
            'periodo' => 'atual',
            'permissao' => Autenticacao::VICE_REPRESENTANTE
        ];

        $representantes = $usuario->listar(null, $busca, null, 1)[0];

        return  $representantes;
    }

    public function selecionarViceRepresentante($dados)
    {
        $vice = $this->selecionarViceRepresentanteAtual($dados['turma']);
        $retorno = [];
        if (!empty($vice)) {
            $aluno = new Alunos();
            $vice = $aluno->selecionar($vice[Usuario::COL_MATRICULA]);

            $vice = [
                'codigo' => $vice['matricula'],
                'nome' => $vice['nome']
            ];

            array_push($retorno, $vice);
        }
        http_response_code(200);
        return json_encode($retorno);
    }

    public function obterViceRepresentante($turma)
    {
        $vice = $this->selecionarViceRepresentanteAtual($turma);

        $retorno = [];

        if (!empty($vice)) {

            $aluno = new Alunos();
            $vice = $aluno->selecionar($vice[Usuario::COL_MATRICULA]);

            $vice = [
                'codigo' => $vice['matricula'],
                'nome' => $vice['nome']
            ];

            array_push($retorno, $vice);
        }

        http_response_code(200);
        return json_encode($retorno);
    }

    public function atualizarViceRepresentante($dados)
    {
        $turma_id = $dados['turma'];
        $resultado = $this->desabilitarViceRepresentante($turma_id);

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

    public function desabilitarViceRepresentante($turma)
    {
        $vice = $this->selecionarViceRepresentanteAtual($turma);
        $vice_id = $vice[Usuario::COL_ID];
        $retorno = $this->delPermissao($vice_id);

        // Retira a turma do usuário
        $u = new Usuario();
        $u->alterar([Usuario::COL_TURMA => null, Usuario::COL_ID => $vice_id]);

        return $retorno;
    }

    public function alterarSenha($dados)
    {
        $turma = $dados['turma'];

        $usuario = $this->selecionarViceRepresentanteAtual($turma);

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
            return json_encode(array('message' => "Não foi possível aterar os dados!"));
        }
    }
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

    public function addPermissao($usuario_id)
    {
        if (!isset($usuario_id)) {
            throw new Exception("É necessário informar o id do usuário");
        }

        $resultado = true;
        $p = new Permissao();

        if (!$p->verificarPermissao($usuario_id, Autenticacao::VICE_REPRESENTANTE)) {
            $resultado = $p->adicionar($usuario_id, Autenticacao::VICE_REPRESENTANTE);
        }
        return $resultado;
    }

    public function delPermissao($usuario_id)
    {
        if (!isset($usuario_id)) {
            throw new Exception("É necessário informar o id do usuário");
        }

        $resultado = true;
        $p = new Permissao();

        if ($p->verificarPermissao($usuario_id, Autenticacao::VICE_REPRESENTANTE)) {
            $resultado = $p->remover($usuario_id, Autenticacao::VICE_REPRESENTANTE);
        }
        return $resultado;
    }
}