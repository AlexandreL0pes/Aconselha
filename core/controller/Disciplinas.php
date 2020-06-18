<?php


namespace core\controller;

use core\model\Disciplina;

class Disciplinas
{
    
    /**
     * Retorna o código e nome de um disciplina
     *
     * @param  mixed $disciplina
     * @return void
     */
    public function selecionar($disciplina)
    {
        // Selecionar disciplina pelo código pauta
        $campos = Disciplina::TABELA . "." . Disciplina::COL_COD_DISCIPLINA . " as codigo, " .
            Disciplina::COL_DESC_DISCIPLINA . " as nome";

        $busca = [Disciplina::COL_COD_DISCIPLINA => $disciplina];

        $d = new Disciplina();

        $disciplina = ($d->listar($campos, $busca, null, 1))[0];

        return $disciplina;
    }
        
    /**
     * Retorna todas as disciplinas atuais de uma turma
     *
     * @param  mixed $dados
     * @return void
     */
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

    /**
     * Retorna a disciplina com base em sua pauta
     *
     * @param  mixed $pauta
     * @return void
     */
    public function selecionarDiscplinaPauta($pauta = null)
    {
        $campos = Disciplina::TABELA . "." . Disciplina::COL_COD_DISCIPLINA . " as codigo, " .
            Disciplina::COL_DESC_DISCIPLINA . " as nome";

        $busca = ['pauta' => $pauta];

        $d = new Disciplina();
        $disciplina = ($d->listar($campos, $busca, null, 1))[0];

        return $disciplina;
    }
}
