<?php


namespace core\controller;

use core\model\Aluno;
use core\model\Usuario;
use core\sistema\Autenticacao;

class Representantes
{

    public function cadastrar($dados)
    {


        // COD_MATRICULA 
        // COD_TURMA 
        // senha


        // Ver se a matrícula está em um representante atual, se tiver altera, senão cadastra
        $matricula = $dados['matricula'];
        $turma = $dados['turma'];
        $senha = $dados['senha'];

        $data_inicio = date('Y-m-d');

        $permissao = Autenticacao::REPRESENTANTE;

        $usuario = new Usuario();


        $retorno = $usuario->adicionar([
            Usuario::COL_MATRICULA => $matricula,
            Usuario::COL_TURMA => $turma,
            Usuario::COL_SENHA => $senha,
            Usuario::COL_PERMISSAO => $permissao,
            Usuario::COL_DATA_INICIO => $data_inicio
        ]);

        if ($retorno > 0) {
            http_response_code(200);
            return json_encode(array('message' => 'O representante foi cadastrado!'));
        } else {
            http_response_code(500);
            return json_encode(array('message' => 'Não foi possível cadastrar o representante!'));
        }
    }

    public function representanteCadastrado($matricula)
    {
        $usuario = new Usuario();

        $campos = Usuario::COL_ID . ", " . Usuario::COL_MATRICULA;
        $busca = [
            Usuario::COL_MATRICULA => $matricula,
            'periodo' => 'atual',
            Usuario::COL_PERMISSAO => Autenticacao::REPRESENTANTE
        ];

        $representante = ($usuario->listar($campos, $busca, null, 1))[0];

        return !empty($representante);
    }

    public function selecionarRepresentantesAtuais($turma)
    {
        $usuario = new Usuario();
        $campos = Usuario::COL_MATRICULA;
        $busca = [
            Usuario::COL_TURMA => $turma,
            'periodo' => 'atual',
            Usuario::COL_PERMISSAO => Autenticacao::REPRESENTANTE
        ];

        $representantes = $usuario->listar($campos, $busca, null, 5);

        // print_r($representantes);
        return  $representantes;
    }


    public function obterRepresentantes($turma)
    {
        $representantes = $this->selecionarRepresentantesAtuais($turma);

        $retorno = [];

        if (count($representantes) > 0 && !empty($representantes[0])) {
            foreach ($representantes as $representante) {

                $aluno = new Alunos();
                $representante = $aluno->selecionar($representante[Usuario::COL_MATRICULA]);

                $representante = [
                    'codigo' => $representante['matricula'],
                    'nome' => $representante['nome']
                ];

                array_push($retorno, $representante);
            }
        }

        http_response_code(200);
        return json_encode($retorno);
    }
}
