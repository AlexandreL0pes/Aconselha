<?php


namespace core\controller;

use core\model\Aluno;
use core\model\Turma;

class Turmas
{
    public function informacoesTurma($dados)
    {
        $codigoTurma = $dados['turma'];

        $campos = Turma::COL_ID . ", " .
            Turma::COL_DESC_TURMA;

        $busca = [Turma::COL_ID => $codigoTurma];

        $turma = new Turma();
        $retornoTurma = ($turma->listar($campos, $busca, null, 1))[0];

        if (!empty($retornoTurma)) {
            $nomeTurma = $this->processarNome($retornoTurma[Turma::COL_ID]);
            $cursoTurma = $this->processarCurso($retornoTurma[Turma::COL_DESC_TURMA]);

            // print_r($nomeTurma);
            // echo "\n";
            // print_r($cursoTurma);
            // echo "\n";
            // print_r($retornoTurma);

            $turmaJson = [
                'codigo' => $codigoTurma,
                'nome' => $nomeTurma,
                'curso' => $cursoTurma
            ];

            http_response_code(200);
            return json_encode($turmaJson);
        } else {
            http_response_code(500);
            return json_encode(array('message' => 'Nenhuma turma foi encontrada!'));
        }
    }

    public function processarNome($codigoTurma = null)
    {
        if ($codigoTurma == null) {
            return "";
        }

        $ano_turma = (explode(".", $codigoTurma))[2];

        $numeroTurma = substr($ano_turma, 0, 1);
        $letraTurma = substr($ano_turma, 1, 1);

        $nome = $numeroTurma . "° " . $letraTurma;
        return $nome;
    }

    public function processarCurso($descricaoTurma = null)
    {
        if ($descricaoTurma == null) {
            return "";
        }

        $tecnico_removido = (explode('Técnico em ', $descricaoTurma))[1];
        $curso = (explode(" Integrado", $tecnico_removido))[0];

        return $curso;
    }

    public function listarEstudantes($dados)
    {
        $turma = $dados['turma'];

        $aluno = new Aluno();
        $campos = "MATRICULAS.MATRICULA as matricula, " .
            "PESSOAS.NOME_PESSOA as nome ";

        $busca = [Aluno::COL_COD_TURMA_ATUAL => $turma];
        $retorno = $aluno->listar($campos, $busca, null, null);

        return json_encode($retorno);
    }
}
