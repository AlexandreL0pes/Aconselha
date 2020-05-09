<?php


namespace core\controller;

use core\model\Analise;
use core\model\Diagnostica;
use core\model\Perfil;

class Diagnosticas
{


    public function cadastrar($dados)
    {
        $reuniao = $dados['reuniao'];
        $estudante = $dados['estudante'];
        $professor = $dados['professor'];
        $perfis = $dados['perfis'];
        $dataAtual = date('Y-m-d h:i:s');

        $diagnostica = new Diagnostica();

        $resultadoDiagnostica = $diagnostica->adicionar([
            Diagnostica::COL_ID_REUNIAO => $reuniao,
            Diagnostica::COL_DATA => $dataAtual,
            Diagnostica::COL_ESTUDANTE => $estudante,
            Diagnostica::COL_PROFESSOR => $professor
        ]);

        if ($resultadoDiagnostica > 0) {
            $analise = new Analise();

            $erros = array();
            // TODO: No alterar, excluir todos os registros q tem a avaliação e cadastrar novamente
            foreach ($perfis as $perfil) {
                $resultadoAnalise = $analise->adicionar([
                    Analise::COL_DIAGNOSTICA => $resultadoDiagnostica,
                    Analise::COL_PERFIL => $perfil
                ]);

                if (!($resultadoAnalise > 0)) {
                    array_push($erros, $perfil);
                }
            }

            if (empty($erros)) {
                http_response_code(200);
                return array('message' => 'A avaliação diagnóstica foi salva!');
            } else {
                http_response_code(500);
                return array('message' => 'Não foi possível cadastrar todos os perfis', 'erros' => $erros);
            }
        } else {
            http_response_code(500);
            return array('message' => 'Não foi possível cadastrar a avaliação diagnóstica!');
        }
    }

    public function alterar($dados)
    {
        $diagnostica_id = $dados['diagnostica'];
        $perfis = $dados['perfis'];
        $dataAtual = date('Y-m-d h:i:s');


        $diagnostica = new Diagnostica();

        $resultadoDiagnostica = $diagnostica->alterar([
            Diagnostica::COL_ID => $diagnostica_id,
            Diagnostica::COL_DATA => $dataAtual
        ]);

        if ($resultadoDiagnostica > 0) {
            $analise = new Analise();

            $analise->excluir([Analise::COL_DIAGNOSTICA => $diagnostica_id]);
            $erros = array();

            foreach ($perfis as $perfil) {
                $resultadoAnalise = $analise->adicionar([
                    Analise::COL_DIAGNOSTICA => $diagnostica_id,
                    Analise::COL_PERFIL => $perfil
                ]);

                if (!($resultadoAnalise > 0)) {
                    array_push($erros, $perfil);
                }
            }

            if (empty($erros)) {
                http_response_code(200);
                return array('message' => 'Os perfis foram alterados!');
            } else {
                http_response_code(500);
                return array('message' => 'Não foi possíve alterar os perfis!');
            }
        } else {
            http_response_code(500);
            return array('message' => 'Não foi possível encontrar a avaliação selecionada!');
        }
    }

    public function selecionarDiagnotica($dados)
    {
        $diagnostica_id = $dados['diagnostica'];
        $diagnostica = new Diagnostica();

        $campos = Diagnostica::COL_ID . ", " . Diagnostica::COL_ID_REUNIAO . ", " . Diagnostica::COL_PROFESSOR . ", " . Diagnostica::COL_ESTUDANTE . ", " . Diagnostica::COL_DATA;
        $busca = [Diagnostica::COL_ID => $diagnostica_id];
        $diagnostica = ($diagnostica->listar($campos, $busca, null, 1))[0];

        if (!empty($diagnostica)) {
            $perfis = $this->perfisAnalise($diagnostica_id);
            // $aluno = $aluno->selecionarAluno(aluno_id);
            $aluno = ['id' => $diagnostica[Diagnostica::COL_ESTUDANTE], 'nome' => 'Aluno ' . $diagnostica[Diagnostica::COL_ESTUDANTE]];
            // $professor = $professor->selecionarProfessor(professor_id)
            $professor = ['id' => $diagnostica[Diagnostica::COL_PROFESSOR], 'nome' => 'Professor ' . $diagnostica[Diagnostica::COL_PROFESSOR]];


            $diagnosticaCompleto = [
                'diagnostica' => $diagnostica[Diagnostica::COL_ID],
                'reuniao' => $diagnostica[Diagnostica::COL_ID_REUNIAO],
                'estudante' => $aluno,
                'professor' =>  $professor,
                'perfis' => $perfis
            ];

            http_response_code(200);
            return json_encode($diagnosticaCompleto);
        } else {
            http_response_code(500);
            return array('message' => 'Não foi encontrada uma avaliação diagnóstica com o id especificado!');
        }
    }

    private function perfisAnalise($diagnostica_id)
    {
        $analise = new Analise();

        $campos = "a." . Analise::COL_PERFIL . " as id, p." . Perfil::COL_NOME;
        $busca = [Analise::COL_DIAGNOSTICA => $diagnostica_id];

        $perfis = $analise->listar($campos, $busca, null, null);

        return $perfis;
    }

    public function excluirDiagnostica($dados)
    {
        $diagnostica_id = $dados['diagnostica'];

        $condicao = [Analise::COL_DIAGNOSTICA => $diagnostica_id];
        $analise = new Analise();

        $retornoAnalise = $analise->excluir($condicao);

        if ($retornoAnalise && $retornoAnalise > 0) {
            $diagnostica = new Diagnostica();

            $condicao = [Diagnostica::COL_ID => $diagnostica_id];
            $retornoDiagnostica = $diagnostica->excluir($condicao);

            if ($retornoDiagnostica && $retornoDiagnostica > 0) {
                http_response_code(200);
                return array('message' => 'A avaliação dignóstica foi excluída!');
            } else {
                http_response_code(500);
                return array('message' => 'Houve um erro na exclusão da avaliação diagnóstica');
            }
        } else {
            http_response_code(500);
            return array('message' => 'Houve um erro na exclusão da avaliação diagnóstica');
        }
    }

    public function listarDiagnosticasReuniao($dados)
    {
        $reuniao_id = $dados['reuniao'];
        $campos = Diagnostica::COL_ID . ", " . Diagnostica::COL_ESTUDANTE;
        $busca = [Diagnostica::COL_ID_REUNIAO => $reuniao_id];

        $diagnostica = new Diagnostica();

        $diagnosticas = $diagnostica->listar($campos, $busca, null, 1000);

        $retorno = [];

        if (!empty($diagnosticas) && !empty($diagnosticas[0])) {
            foreach ($diagnosticas as $diagnostica) {
                // TODO: Consultar o nome do aluno no banco QAcademico 
                $aluno = ['id' => $diagnostica[Diagnostica::COL_ESTUDANTE], 'nome' => 'Aluno ' . $diagnostica[Diagnostica::COL_ESTUDANTE]];
                array_push($retorno, [
                    "diagnostica" => $diagnostica[Diagnostica::COL_ID],
                    "aluno" => $aluno
                ]);
            }

            http_response_code(200);
            return json_encode($retorno);
        }else{
            http_response_code(500);
            return json_encode(array('message' => 'Nenhuma avaliação diagnóstica foi encontrada!'));
        }
    }
}
