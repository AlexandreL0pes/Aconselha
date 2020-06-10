<?php


namespace core\controller;

use core\model\Professor;
use core\model\Turma;
use core\sistema\Autenticacao;

class Professores
{

    // Lista todas as turmas que um professor já deu aula
    public function listarTurmas($dados = [])
    {
        $pessoa = $dados['professor'];

        $campos = Turma::COL_ID;

        $busca = [Professor::COL_COD_PESSOA => $pessoa];


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

        // $pessoa = $dados['professor'];
        $pessoa = Autenticacao::obterProfessor($dados['token']);
        // Obtem todas as turmas de um professor
        $turmas_professor = $this->listarTurmasAtuais(['professor' => $pessoa]);

        $r = new Reunioes();
        // Obtem as turmas que estão em reunião
        $reunioes = $r->listarReunioesAndamento();
        $reunioes = json_decode($reunioes, true);

        // echo "Turmas Professor\n";
        // print_r($turmas_professor);

        // echo "Turmas Reunião\n";
        // print_r($reunioes);

        $reunioes_professor = [];

        foreach ($reunioes as $reuniao) {
            if (in_array($reuniao['codigo'], $turmas_professor)) {
                array_push($reunioes_professor, $reuniao);
            }
        }

        // echo "Intersecção \n";
        // print_r($reunioes_professor);
        return json_encode($reunioes_professor);
    }

    public function obterTurmasProfessor($dados = [])
    {
        $pessoa = Autenticacao::obterProfessor($dados['token']);
        $codigos = $this->listarTurmasAtuais(['professor' => $pessoa]);

        $t = new Turmas();
        $turmas = [];
        foreach ($codigos as $codigo) {
            $turma = $t->informacoesTurma(['turma' => $codigo]);
            $turma = json_decode($turma, true);
            array_push($turmas, $turma);
        }
        return json_encode($turmas);
    }

    public function selecionar($cod_pessoa = null)
    {
        $campos = "PROFESSORES" . "." . Professor::COL_COD_PESSOA . ", " .
            "{fn CONCAT(SUBSTRING(PESSOAS.NOME_PESSOA, 1, CHARINDEX(' ', PESSOAS.NOME_PESSOA) - 1), {fn CONCAT(' ', REVERSE(SUBSTRING(REVERSE(PESSOAS.NOME_PESSOA), 1, CHARINDEX(' ', REVERSE(PESSOAS.NOME_PESSOA)) - 1)))})} as nome";

        $busca = [Professor::COL_COD_PESSOA => $cod_pessoa];

        $p = new Professor();
        $professor = ($p->listar($campos, $busca, null, 1))[0];

        if (!empty($professor)) {
            $professor = [
                'nome' => $professor['nome'],
                'id' => $professor[Professor::COL_COD_PESSOA]
            ];
        }

        return $professor;
    }

    public function professoresAtuaisTurma($cod_turma = null)
    {
        $campos = Professor::TABELA . "." . Professor::COL_COD_PESSOA;
        $busca = ['turma' => $cod_turma, 'ano_letivo' => 'atual'];

        $p = new Professor();
        $cod_pessoas = $p->listar($campos, $busca, null, null);
        $pessoas = [];
        if (!empty($cod_pessoas[0])) {
            $pessoas = array_map(function ($pessoa) {
                return $pessoa[Professor::COL_COD_PESSOA];
            }, $cod_pessoas);
        }

        return $pessoas;
    }
}
