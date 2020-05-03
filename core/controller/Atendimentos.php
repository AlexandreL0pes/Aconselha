<?php

namespace core\controller;

use core\model\Avaliacao;
use core\model\Encaminhamento;

class Atendimentos
{
    private $atendimento_id = null;
    private $reuniao_id = null;
    private $estudante_id = null;
    private $data = null;
    private $observacao = null;
    private $acao_id = null;


    public function __set($atributo, $valor)
    {
        $this->$atributo = $valor;
    }

    public function __get($atributo)
    {
        return $this->$atributo;
    }

    public function cadastrar($dados)
    {
        $reuniao = $dados['reuniao'];
        $dataAtual = date('Y-m-d');
        $estudante = $dados['estudante'];
        $observacao = $dados['queixa'];
        $intervencao = $dados['intervencao'];

        $professores = $dados['professores'];

        $avaliacao = new Avaliacao();

        $resultado = $avaliacao->adicionar([
            Avaliacao::COL_ID_REUNIAO => $reuniao,
            Avaliacao::COL_ESTUDANTE =>  $estudante,
            Avaliacao::COL_DATA => $dataAtual,
            Avaliacao::COL_OBSERVACAO => $observacao,
            Avaliacao::COL_ACAO => $intervencao
        ]);

        if ($resultado > 0) {
            $encaminhamento = new Encaminhamento();

            $erros = array();

            foreach ($professores as $professor) {
                $resultadoEncaminhamento = $encaminhamento->adicionar(
                    [
                        Encaminhamento::COL_ID_AVALIACAO => $resultado,
                        Encaminhamento::COL_PROFESSOR => $professor
                    ]
                );
                if (!($resultadoEncaminhamento > 0)) {
                    array_push($erros, $professor);
                }
            }

            if (empty($erros)) {
                http_response_code(200);
                return array('message' => 'Os atendimentos foram cadastrados!');
            }else{
                http_response_code(500);
                return array('message' => 'Não foi possível cadastrar todos os professores do encaminhamento.', 'errors' => $erros);
            }
        }else{
            http_response_code(500);
            return array('message' => 'Não foi possível cadastrar o encaminhamento.');
        }
    }

    public function alterar($dados)
    {
        $avaliacao_id = $dados['avaliacao'];
        $reuniao = $dados['reuniao'];
        $dataAtual = date('Y-m-d');
        $estudante = $dados['estudante'];
        $observacao = $dados['queixa'];
        $intervencao = $dados['intervencao'];

        $professores = $dados['professores'];

        $avaliacao = new Avaliacao();

        $resultado = $avaliacao->alterar([
            Avaliacao::COL_ID => $avaliacao_id,
            Avaliacao::COL_ID_REUNIAO => $reuniao,
            Avaliacao::COL_ESTUDANTE =>  $estudante,
            Avaliacao::COL_DATA => $dataAtual,
            Avaliacao::COL_OBSERVACAO => $observacao,
            Avaliacao::COL_ACAO => $intervencao
        ]);

        if ($resultado > 0) {


            $encaminhamento = new Encaminhamento();
            
            $encaminhamento->excluir([Encaminhamento::COL_ID_AVALIACAO => $avaliacao_id]);
            $erros = array();

            foreach ($professores as $professor) {
                $resultadoEncaminhamento = $encaminhamento->adicionar(
                    [
                        Encaminhamento::COL_ID_AVALIACAO => $resultado,
                        Encaminhamento::COL_PROFESSOR => $professor
                    ]
                );
                if (!($resultadoEncaminhamento > 0)) {
                    array_push($erros, $professor);
                }
            }

            if (empty($erros)) {
                http_response_code(200);
                return array('message' => 'Os atendimentos foram alterados!');
            }else{
                http_response_code(500);
                return array('message' => 'Não foi possível alterar todos os professores do encaminhamento.', 'errors' => $erros);
            }
        }else{
            http_response_code(500);
            return array('message' => 'Não foi possível alterar o encaminhamento.');
        }
    }

}
