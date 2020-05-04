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
}
