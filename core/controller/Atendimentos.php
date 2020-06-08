<?php

namespace core\controller;

use core\model\Encaminhamento;
use core\model\Acao;
use core\model\Atendimento;

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

        $atendimento = new Atendimento();

        $resultado = $atendimento->adicionar([
            Atendimento::COL_ID_REUNIAO => $reuniao,
            Atendimento::COL_ESTUDANTE =>  $estudante,
            Atendimento::COL_DATA => $dataAtual,
            Atendimento::COL_OBSERVACAO => $observacao,
            Atendimento::COL_ACAO => $intervencao
        ]);

        if ($resultado > 0) {
            $encaminhamento = new Encaminhamento();

            $erros = array();

            foreach ($professores as $professor) {
                $resultadoEncaminhamento = $encaminhamento->adicionar(
                    [
                        Encaminhamento::COL_ID_ATENDIMENTO => $resultado,
                        Encaminhamento::COL_PROFESSOR => $professor
                    ]
                );
                if (!($resultadoEncaminhamento > 0)) {
                    array_push($erros, $professor);
                }
            }

            if (empty($erros)) {
                http_response_code(200);
                // return array('message' => 'Os atendimentos foram cadastrados!');
                return json_encode(array('message' => 'Os atendimentos foram cadastrados!'));
            } else {
                http_response_code(500);
                return json_encode(array('message' => 'Não foi possível cadastrar todos os professores do encaminhamento.', 'errors' => $erros));
            }
        } else {
            http_response_code(500);
            return json_encode(array('message' => 'Não foi possível cadastrar o encaminhamento.'));
        }
    }

    public function alterar($dados)
    {
        $atendimento_id = $dados['atendimento'];
        $reuniao = $dados['reuniao'];
        $dataAtual = date('Y-m-d');
        $estudante = $dados['estudante'];
        $observacao = $dados['queixa'];
        $intervencao = $dados['intervencao'];

        $professores = $dados['professores'];

        $atendimento = new Atendimento();

        $resultado = $atendimento->alterar([
            Atendimento::COL_ID => $atendimento_id,
            Atendimento::COL_ID_REUNIAO => $reuniao,
            Atendimento::COL_ESTUDANTE =>  $estudante,
            Atendimento::COL_DATA => $dataAtual,
            Atendimento::COL_OBSERVACAO => $observacao,
            Atendimento::COL_ACAO => $intervencao
        ]);

        if ($resultado > 0) {


            $encaminhamento = new Encaminhamento();

            $encaminhamento->excluir([Encaminhamento::COL_ID_ATENDIMENTO => $atendimento_id]);
            $erros = array();

            foreach ($professores as $professor) {
                $resultadoEncaminhamento = $encaminhamento->adicionar(
                    [
                        Encaminhamento::COL_ID_ATENDIMENTO => $resultado,
                        Encaminhamento::COL_PROFESSOR => $professor
                    ]
                );
                if (!($resultadoEncaminhamento > 0)) {
                    array_push($erros, $professor);
                }
            }

            if (empty($erros)) {
                http_response_code(200);
                return json_encode(array('message' => 'Os atendimentos foram alterados!'));
            } else {
                http_response_code(500);
                return json_encode(array('message' => 'Não foi possível alterar todos os professores do encaminhamento.', 'errors' => $erros));
            }
        } else {
            http_response_code(500);
            return json_encode(array('message' => 'Não foi possível alterar o encaminhamento.'));
        }
    }

    public function selecionarAtendimento($dados)
    {
        $atendimento_id = $dados['atendimento'];
        $atendimento = new Atendimento();

        $campos = Atendimento::COL_ID . ", " . Atendimento::COL_ID_REUNIAO . ", " .  Atendimento::COL_ESTUDANTE . ", " .  Atendimento::COL_OBSERVACAO . ", a." .  Atendimento::COL_ACAO;
        $busca = [Atendimento::COL_ID => $atendimento_id];
        $atendimento = ($atendimento->listar($campos, $busca, null, 1))[0];

        if (!empty($atendimento)) {
            $professores = $this->professoresAtendimento($atendimento_id);

            // Obtem as informações do aluno, com base na matrícula
            $a = new Alunos();
            $aluno = $a->selecionar(['matricula' => $atendimento[Atendimento::COL_ESTUDANTE]]);
            $aluno = json_decode($aluno, true);

            $atendimentoCompleto = [
                'atendimento' => $atendimento[Atendimento::COL_ID],
                'reuniao' => $atendimento[Atendimento::COL_ID_REUNIAO],
                'estudante' => $aluno,
                'professores' => $professores,
                'queixa' => $atendimento[Atendimento::COL_OBSERVACAO],
                'intervencao' => $atendimento[Atendimento::COL_ACAO]
            ];
            http_response_code(200);
            return json_encode($atendimentoCompleto);
        } else {
            http_response_code(500);
            return json_encode(array('message' => 'Não foi encontrado uma avaliação com o id especificado!'));
        }
    }

    private function professoresAtendimento($atendimento_id)
    {
        $encaminhamento = new Encaminhamento();

        $busca = [Encaminhamento::COL_ID_ATENDIMENTO => $atendimento_id];
        $professores_id = $encaminhamento->listar(Encaminhamento::COL_PROFESSOR, $busca, null, null);

        $professores = [];
        foreach ($professores_id as $professor_id) {
            // TODO: Atribuir aqui o nome do professor vindo do Q-Acadêmico
            $nome = 'Fulano de Tal';

            array_push($professores, ['id' => $professor_id[Encaminhamento::COL_PROFESSOR], 'nome' => $nome]);
        }
        return $professores;
    }

    public function excluirAtendimento($dados)
    {
        $atendimento_id = $dados['atendimento'];

        $condicao = [Encaminhamento::COL_ID_ATENDIMENTO => $atendimento_id];
        $encaminhamento = new Encaminhamento();

        $retornoEncaminhamento = $encaminhamento->excluir($condicao);

        if ($retornoEncaminhamento && $retornoEncaminhamento > 0) {
            $atendimento = new Atendimento();
            $condicao = [Atendimento::COL_ID => $atendimento_id];
            $retornoAtendimento = $atendimento->excluir($condicao);

            if ($retornoAtendimento && $retornoAtendimento > 0) {
                http_response_code(200);
                return json_encode(array('message' => 'O encaminhamento foi excluído!'));
            } else {
                http_response_code(500);
                return json_encode(array('message' => 'Houve um erro na exclusão do encaminhamento!'));
            }
        } else {
            http_response_code(500);
            return json_encode(array('message' => 'Houve um erro na exclusão dos professores envolvidos no encaminhamento!'));
        }
    }

    public function listarAtendimentosReuniao($dados)
    {
        $reuniao_id = $dados['reuniao'];

        $campos = "at." . Atendimento::COL_ID . ", at." . Atendimento::COL_ID_REUNIAO . ", a." . Acao::COL_NOME . ", at." . Atendimento::COL_ESTUDANTE;
        // $campos = "av." . Avaliacao::COL_ID . ", av." . Avaliacao::COL_ID_REUNIAO . ", a." . Acao::COL_NOME . ", av." . Avaliacao::COL_ESTUDANTE;
        $busca = [Atendimento::COL_ID_REUNIAO => $reuniao_id];

        $ordem = Atendimento::COL_DATA . " DESC ";
        $atendimento = new Atendimento();

        $atendimentos = $atendimento->listar($campos, $busca, $ordem, 100);
        $retorno = [];

        if (!empty($atendimentos) && !empty($atendimentos[0])) {
            foreach ($atendimentos as $atendimento) {
                // TODO: Consultar o Q-Academico os dados do aluno
                $aluno = ['matricula' => $atendimento[Atendimento::COL_ESTUDANTE], 'nome' => 'Um nome estático', 'curso' => 'Informática para Interte'];

                array_push($retorno, ['encaminhamento' => $atendimento[Atendimento::COL_ID], 'intervencao' => $atendimento[Acao::COL_NOME], 'aluno' => $aluno]);
            }

            return json_encode($retorno);
        } else {
            return json_encode(array('message' => 'Nenhum atendimento foi encontrado'));
        }
    }
}
