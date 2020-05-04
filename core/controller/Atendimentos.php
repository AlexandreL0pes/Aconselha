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
            } else {
                http_response_code(500);
                return array('message' => 'Não foi possível cadastrar todos os professores do encaminhamento.', 'errors' => $erros);
            }
        } else {
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
            } else {
                http_response_code(500);
                return array('message' => 'Não foi possível alterar todos os professores do encaminhamento.', 'errors' => $erros);
            }
        } else {
            http_response_code(500);
            return array('message' => 'Não foi possível alterar o encaminhamento.');
        }
    }

    public function selecionarAtendimento($dados)
    {
        $avaliacao_id = $dados['avaliacao'];
        $avaliacao = new Avaliacao();

        $campos = Avaliacao::COL_ID . ", " . Avaliacao::COL_ID_REUNIAO . ", " .  Avaliacao::COL_ESTUDANTE . ", " .  Avaliacao::COL_OBSERVACAO . ", " .  Avaliacao::COL_ACAO;
        $busca = [Avaliacao::COL_ID => $avaliacao_id];
        $atendimento = ($avaliacao->listar($campos, $busca, null, 1))[0];

        if (!empty($atendimento)) {
            $professores = $this->professoresAtendimento($avaliacao_id);
            // TODO: Consultar o id do aluno e retornar o nome
            $aluno = ['id' => $atendimento[Avaliacao::COL_ESTUDANTE], 'nome' => 'Aluno de tal'];
    
            var_dump($atendimento);
            echo empty($atendimento);
            $atendimentoCompleto = [
                'avaliacao' => $atendimento[Avaliacao::COL_ID],
                'reuniao' => $atendimento[Avaliacao::COL_ID_REUNIAO],
                'estudante' => $aluno,
                'professores' => $professores,
                'queixa' => $atendimento[Avaliacao::COL_OBSERVACAO],
                'intervencao' => $atendimento[Avaliacao::COL_ACAO]
            ];
            http_response_code(200);
            return json_encode($atendimentoCompleto);
        }else{
            http_response_code(500);
            return array('message' => 'Não foi encontrado uma avaliação com o id especificado!');
        }
    }

    public function professoresAtendimento($avaliacao_id)
    {
        $encaminhamento = new Encaminhamento();

        $busca = [Encaminhamento::COL_ID_AVALIACAO => $avaliacao_id];
        $professores_id = $encaminhamento->listar(Encaminhamento::COL_PROFESSOR, $busca, null, null);

        $professores = [];
        foreach ($professores_id as $professor_id) {
            // TODO: Atribuir aqui o nome do professor vindo do Q-Acadêmico
            $nome = 'Fulano de Tal';

            array_push($professores, ['id' => $professor_id[Avaliacao::COL_PROFESSOR], 'nome' => $nome]);
        }
        return $professores;
    }

    public function excluirAtendimento($dados)
    {
        $avaliacao_id = $dados['avaliacao'];

        $condicao = [Encaminhamento::COL_ID_AVALIACAO => $avaliacao_id];
        $encaminhamento = new Encaminhamento();

        $retornoEncaminhamento = $encaminhamento->excluir($condicao);

        if ($retornoEncaminhamento && $retornoEncaminhamento > 0) {
            $avaliacao = new Avaliacao();
            $condicao = [Avaliacao::COL_ID => $avaliacao_id];
            $retornoAvaliacao = $avaliacao->excluir($condicao);

            if ($retornoAvaliacao && $retornoAvaliacao > 0) {
                http_response_code(200);
                return array('message' => 'O encaminhamento foi excluído!');
            } else {
                http_response_code(500);
                return array('message' => 'Houve um erro na exclusão do encaminhamento!');
            }
        } else {
            http_response_code(500);
            return array('message' => 'Houve um erro na exclusão dos professores envolvidos no encaminhamento!');
        }
    }
}
