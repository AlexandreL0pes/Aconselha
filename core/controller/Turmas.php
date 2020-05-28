<?php


namespace core\controller;

use core\model\Turma;

class Turmas
{
    public function informacoesTurma($dados)
    {
        $turma = $dados['turma'];

        $campos = Turma::COL_ID . ", " .
            Turma::COL_DESC_TURMA;

        $busca = [Turma::COL_DESC_TURMA => $turma];

        $turma = new Turma();
        $retornoTurma = ($turma->listar($campos, $busca, null, 1))[0];

        $nomeTurma = $this->processarNome($retornoTurma[Turma::COL_ID], $retornoTurma[Turma::COL_DESC_TURMA]);

        print_r($nomeTurma);
        echo "\n";
        print_r($retornoTurma);
    }

    public function processarNome($codigoTurma = "", $descricaoTurma = "")
    {
        // [COD_TURMA] => 20161.03AGP10I.1A
        // [DESC_TURMA] => Técnico em Agropecuária Integrado ao Ensino Médio, 1º período - Turma A ( 2016/1 )

        // $codigoTurma = '20161.03AGP10I.1A';
        // $descricaoTurma = "Técnico em Agropecuária Integrado ao Ensino Médio, 1º período - Turma A ( 2016/1 )";


        $ano_turma = (explode(".", $codigoTurma))[2];



        $numeroTurma = substr($ano_turma, 0, 1);
        $letraTurma = substr($ano_turma, 1, 1);

        // echo "\n" . $numeroTurma . "\n", $letraTurma;

        $a = (explode('Técnico em ', $descricaoTurma))[1];
        $descricaoTurma = (explode("Integrado", $a))[0];


        // print_r($ano_turma);
        $nome = $numeroTurma . "° " . $letraTurma;
        return $nome;
    }

}
