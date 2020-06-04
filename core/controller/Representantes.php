<?php


namespace core\controller;

use core\model\Aluno;
use core\model\Usuario;
use core\sistema\Autenticacao;

class Representantes
{


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
                $representante = $aluno->selecionar(['matricula' => $representante[Usuario::COL_MATRICULA]]);
                // print_r($representante);
                $representante = json_decode($representante, true);
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
