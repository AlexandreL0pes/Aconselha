<?php


namespace core\controller;

use core\model\Avaliacao;
use core\model\Analise;

class Diagnosticas
{


    public function cadastrar($dados)
    {
        $reuniao = $dados['reuniao'];
        $estudante = $dados['estudante'];
        $professor = $dados['professor'];
        $perfis = $dados['perfis'];
        $dataAtual = date('Y-m-d h:i:s');

        $avaliacao = new Avaliacao();

        $resultadoAvaliacao = $avaliacao->adicionar([
            Avaliacao::COL_ID_REUNIAO => $reuniao,
            Avaliacao::COL_DATA => $dataAtual,
            Avaliacao::COL_ESTUDANTE => $estudante,
            Avaliacao::COL_PROFESSOR => $professor
        ]);

        if ($resultadoAvaliacao > 0) {
            $analise = new Analise();

            $erros = array();
            // TODO: No alterar, excluir todos os registros q tem a avaliação e cadastrar novamente
            foreach ($perfis as $perfil) {
                $resultadoAnalise = $analise->adicionar([
                    Analise::COL_AVALIACAO => $resultadoAvaliacao,
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
        $avaliacao_id = $dados['avaliacao'];
        $perfis = $dados['perfis'];
        $dataAtual = date('Y-m-d h:i:s');


        $avaliacao = new Avaliacao();

        $resultadoAvaliacao = $avaliacao->alterar([
            Avaliacao::COL_ID => $avaliacao_id,
            Avaliacao::COL_DATA => $dataAtual
        ]);

        if ($resultadoAvaliacao > 0) {
            $analise = new Analise();

            $analise->excluir([Analise::COL_AVALIACAO => $avaliacao_id]);
            $erros = array();

            foreach ($perfis as $perfil) {
                $resultadoAnalise = $analise->adicionar([
                    Analise::COL_AVALIACAO => $avaliacao_id,
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
        $avaliacao_id = $dados['avaliacao'];
        $avaliacao = new Avaliacao();

        $campos = Avaliacao::COL_ID . ", " . Avaliacao::COL_ID_REUNIAO . ", " . Avaliacao::COL_PROFESSOR . ", " . Avaliacao::COL_ESTUDANTE . ", " . Avaliacao::COL_DATA;
        $busca = [Avaliacao::COL_ID => $avaliacao_id];
        $diagnostica = ($avaliacao->listar($campos, $busca, null, 1))[0];

        if (!empty($diagnostica)) {
            // $perfis = $this->perfisDiagnostica()
            $perfis = [1];
            // $aluno = $aluno->selecionarAluno(aluno_id);
            $aluno = ['id' => $diagnostica[Avaliacao::COL_ESTUDANTE], 'nome' => 'Aluno de Tal'];
            // $professor = $professor->selecionarProfessor(professor_id)
            $professor = ['id' => $diagnostica[Avaliacao::COL_PROFESSOR], 'nome' => 'Professor de Tal'];


            $diagnosticaCompleto = [
                'avaliacao' => $diagnostica[Avaliacao::COL_ID],
                'reuniao' => $diagnostica[Avaliacao::COL_ID_REUNIAO],
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

    public function excluirDiagnostica($dados)
    {
        $avaliacao_id = $dados['avaliacao'];

        $condicao = [Analise::COL_AVALIACAO => $avaliacao_id];
        $analise = new Analise();

        $retornoAnalise = $analise->excluir($condicao);

        if ($retornoAnalise && $retornoAnalise > 0) {
            $avaliacao = new Avaliacao();

            $condicao = [Avaliacao::COL_ID => $avaliacao_id];
            $retornoAvaliacao = $avaliacao->excluir($condicao);

            if ($retornoAvaliacao && $retornoAvaliacao > 0) {
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
}
