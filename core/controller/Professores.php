<?php


namespace core\controller;

use core\model\Professor;
use core\model\Turma;

class Professores
{

    // Lista todas as turmas que um professor já deu aula
    public function listarTurmas($dados = [])
    {
        # code...
    }

    // Lista todas as turmas que um professor está dando aula
    public function listarTurmasAtuais($dados = [])
    {
        $pessoa = $dados['professor'];

        $campos = Turma::COL_ID;

        $busca = [Professor::COL_COD_PESSOA => $pessoa, 'ano_letivo' => 'atual'];


        $professor = new Professor();

        $turmas = $professor->listar($campos, $busca, null, null);

        $turmas_id = [];
        if (count($turmas) > 0 && !empty($turmas[0])) {
            $turmas_id = array_map(function ($turma) {
                return $turma[Turma::COL_ID];
            }, $turmas);
        }
        return $turmas_id;
    }

    // Lista todas as turma de um professor que estão em reunião
    public function listarTurmasReuniao($dados = [])
    {

        $pessoa = $dados['professor'];
        // Obtem todas as turmas de um professor
        $turmas_professor = $this->listarTurmasAtuais(['professor' => $pessoa]);

        $r = new Reunioes();
        // Obtem as turmas que estão em reunião
        $reunioes = $r->listarTurmasEmReuniao();

        // echo "Turmas Professor\n";
        // print_r($turmas_professor);

        // echo "Turmas Reunião\n";
        // print_r($reunioes);

        $t = new Turmas();
        $reunioes_professor = [];
        foreach ($turmas_professor as $turma_professor) {
            if (in_array($turma_professor, $reunioes)) {
                $turma = $t->informacoesTurma(['turma' => $turma_professor]);
                $turma = json_decode($turma, true);
                array_push($reunioes_professor, $turma);
            }
        }

        return json_encode($reunioes_professor);
    }
}
