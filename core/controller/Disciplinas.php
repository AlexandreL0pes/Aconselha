<?php


namespace core\controller;

use core\model\Disciplina;

class Disciplinas
{

    public function listarDisciplinasTurma($dados = [])
    {

        $codigoTurma = $dados['turma'];


        $campos = Disciplina::TABELA . "." . Disciplina::COL_COD_DISCIPLINA . " as codigo, " .
            Disciplina::COL_DESC_DISCIPLINA . " as nome";

        $busca = ['turma' => $codigoTurma];

        $d = new Disciplina();

        $disciplinas = ($d->listar($campos, $busca, null, 1000));

        http_response_code(200);
        return json_encode($disciplinas);
    }
}
