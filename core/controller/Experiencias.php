<?php

namespace core\controller;

use core\model\Experiencia;
use core\model\DisciplinaExperiencia;
use core\model\Classificacao;
use core\model\Perfil;
use core\model\Turma;

class Experiencias
{


    /**
     * Efetua o cadastro de uma Avaliação de Experiência
     *
     * @param  mixed $dados
     * @return void
     */
    public function cadastrar($dados)
    {


        // Obtem o dado da reunião atual, com base na token do usuário logado
        $token = $dados['token'];
        $r = new Reunioes();
        $reuniao = $r->obterReuniaoTurma($token);

        $titulo = $dados['titulo'];
        $observacao = $dados['descricao'];
        $dataAtual = date('Y-m-d h:i:s');
        $classificacao_id = $dados['classificacao'];

        $disciplinas = $dados['disciplinas'];

        $experiencia = new Experiencia();

        $resultadoExperiencia = $experiencia->adicionar([
            Experiencia::COL_ID_REUNIAO => $reuniao,
            Experiencia::COL_TITULO => $titulo,
            Experiencia::COL_OBSERVACAO => $observacao,
            Experiencia::COL_DATA => $dataAtual,
            Experiencia::COL_CLASSIFICACAO => $classificacao_id
        ]);

        if ($resultadoExperiencia > 0) {
            $disciplinasExperiencia = new DisciplinaExperiencia();

            $erros = array();

            foreach ($disciplinas as $disciplina) {
                $resultadoDisciplinas = $disciplinasExperiencia->adicionar([
                    DisciplinaExperiencia::COL_ID_EXPERIENCIA => $resultadoExperiencia,
                    DisciplinaExperiencia::COL_DISCIPLINA => $disciplina
                ]);

                if (!($resultadoDisciplinas > 0)) {
                    array_push($erros, $disciplina);
                }
            }

            if (empty($erros)) {
                http_response_code(200);
                return json_encode(array('message' => 'A Experiência foi cadastrada!'));
            } else {
                http_response_code(500);
                return json_encode(array('message' => 'Não foi possível cadastrar todos as disciplinas', 'errors' => $erros));
            }
        } else {
            http_response_code(500);
            return json_encode(array('message' => 'Não foi possível cadastrar a experiência'));
        }
    }


    /**
     * Efetua a alteração de uma Avaliação de Experiência
     *
     * @param  mixed $dados
     * @return void
     */
    public function alterar($dados)
    {
        $experiencia_id = $dados['experiencia'];
        $titulo = $dados['titulo'];
        $descricao = $dados['descricao'];

        $classificacao = $dados['classificacao'];
        $disciplinas = $dados['disciplinas'];

        $experiencia = new Experiencia();

        $resultadoExperiencia = $experiencia->alterar([
            Experiencia::COL_ID => $experiencia_id,
            Experiencia::COL_TITULO => $titulo,
            Experiencia::COL_OBSERVACAO => $descricao,
            Experiencia::COL_CLASSIFICACAO => $classificacao
        ]);

        if ($resultadoExperiencia > 0) {
            $disciplinasExperiencia = new DisciplinaExperiencia();

            $disciplinasExperiencia->excluir([DisciplinaExperiencia::COL_ID_EXPERIENCIA => $experiencia_id]);

            $erros = array();
            foreach ($disciplinas as $disciplina) {
                $resultadoDisciplinas = $disciplinasExperiencia->adicionar([
                    DisciplinaExperiencia::COL_ID_EXPERIENCIA => $resultadoExperiencia,
                    DisciplinaExperiencia::COL_DISCIPLINA => $disciplina
                ]);

                if (!($resultadoDisciplinas > 0)) {
                    array_push($erros, $disciplina);
                }
            }

            if (empty($erros)) {
                http_response_code(200);
                return json_encode(array('message' => 'A experiência foi alterada com sucesso!'));
            } else {
                http_response_code(500);
                return json_encode(array('message' => 'Houve erro na alteração das disciplinas em experiencia', 'errors' => $erros));
            }
        } else {
            http_response_code(500);
            return json_encode(array('message' => 'Houve um erro na alteração das disciplinas'));
        }
    }

    /**
     * Obtem os dados de uma Avaliação de Experiência
     *
     * @param  mixed $dados
     * @return void
     */
    public function selecionar($dados)
    {
        $experiencia_id = $dados['experiencia'];

        $experiencia = new Experiencia();

        // $campos = "e.*, c." . Classificacao::COL_NOME;
        $campos = Experiencia::COL_ID . ", " .
            Experiencia::COL_ID_REUNIAO . ", " .
            Experiencia::COL_TITULO . ", " .
            Experiencia::COL_OBSERVACAO . ", " .
            Experiencia::COL_DATA . ", " .
            "c." . Classificacao::COL_ID . " as id_classificacao, " .
            "c." . Classificacao::COL_NOME

            // Classificacao::COL_NOME;
        ;

        $busca = [Experiencia::COL_ID => $experiencia_id];

        $resultadoExperiencia = $experiencia->listar($campos, $busca, null, 1)[0];
        if (!empty($resultadoExperiencia)) {
            $disciplinas = $this->disciplinasExperiencia($experiencia_id);
            // print_r($resultadoExperiencia);
            $classificacao = ['id' => $resultadoExperiencia['id_classificacao'], 'nome' => $resultadoExperiencia[Perfil::COL_NOME]];
            $experienciaCompleta = [
                'experiencia' => $resultadoExperiencia[Experiencia::COL_ID],
                'titulo' => $resultadoExperiencia[Experiencia::COL_TITULO],
                'descricao' => $resultadoExperiencia[Experiencia::COL_OBSERVACAO],
                'data' => $resultadoExperiencia[Experiencia::COL_DATA],
                'classificacao' => $classificacao,
                'disciplinas' => $disciplinas
            ];

            http_response_code(200);
            return json_encode($experienciaCompleta);
        } else {
            http_response_code(500);
            return json_encode(array('message' => 'A experiência solicitada não foi encontrada!'));
        }
    }

    /**
     * Obtem todas as disciplinas envolvidas em uma Avaliação de Experiência
     *
     * @param  mixed $experiencia_id
     * @return void
     */
    private function disciplinasExperiencia($experiencia_id)
    {
        $disciplinasExperiencia = new DisciplinaExperiencia();

        $busca = [DisciplinaExperiencia::COL_ID_EXPERIENCIA => $experiencia_id];
        $disciplinas_id = $disciplinasExperiencia->listar(DisciplinaExperiencia::COL_DISCIPLINA, $busca, null, 100);

        $disciplinas = [];
        $d = new Disciplinas();

        if ($disciplinas_id && count($disciplinas_id[0]) > 0) {
            foreach ($disciplinas_id as $disciplina_id) {
                // echo ($disciplina_id[DisciplinaExperiencia::COL_DISCIPLINA]);
                $disciplina = $d->selecionar($disciplina_id[DisciplinaExperiencia::COL_DISCIPLINA]);
                // print_r($disciplina);
                // array_push($disciplinas, ['id' => $disciplina_id[DisciplinaExperiencia::COL_DISCIPLINA], 'nome' => $nome]);
                array_push($disciplinas, $disciplina);
            }
        }

        return $disciplinas;
    }

    /**
     * Efetua a exclusão de uma Avaliação de Experiência
     *
     * @param  mixed $dados
     * @return void
     */
    public function excluir($dados)
    {
        $experiencia_id = $dados['experiencia'];

        $condicao = [DisciplinaExperiencia::COL_ID_EXPERIENCIA => $experiencia_id];
        $disciplinaExperiencia = new DisciplinaExperiencia();

        $retornoDisciplina = $disciplinaExperiencia->excluir($condicao);

        if ($retornoDisciplina && $retornoDisciplina > 0) {
            $experiencia = new Experiencia();
            $condicao = [Experiencia::COL_ID => $experiencia_id];
            $retornoExperiencia = $experiencia->excluir($condicao);

            if ($retornoExperiencia && $retornoExperiencia > 0) {
                http_response_code(200);
                return json_encode(array('message' => 'A experiência foi excluída!'));
            } else {
                http_response_code(500);
                return json_encode(array('message' => 'Houve um erro durante a exclusão das disciplinas!'));
            }
        } else {
            http_response_code(500);
            return json_encode(array('message' => 'Houve um erro na exclusão da experiência!'));
        }
    }

    /**
     * Obtem todas as Avaliações de Experiência de uma reunião
     *
     * @param  mixed $dados
     * @return void
     */
    public function listarExperienciasReuniao($dados)
    {

        // Caso a token seja passada, usar ela, caso não use o id
        if (isset($dados['token']) && !isset($dados['reuniao'])) {
            $token = $dados['token'];
            $r = new Reunioes();
            $reuniao_id = $r->obterReuniaoTurma($token);
        } else {
            $reuniao_id = $dados['reuniao'];
        }

        $campos = "e." . Experiencia::COL_ID . ", " . Experiencia::COL_ID_REUNIAO . ", " . Experiencia::COL_TITULO . " c." . Classificacao::COL_NOME;
        $campos = "e.*, c." . Classificacao::COL_NOME;
        $busca = [Experiencia::COL_ID_REUNIAO => $reuniao_id];

        $experiencia = new Experiencia();
        $experiencias = $experiencia->listar($campos, $busca, null, 1000);

        $retorno = [];

        if (!empty($experiencias) && !empty($experiencias[0])) {
            foreach ($experiencias as $experiencia) {
                $disciplinas = $this->disciplinasExperiencia($experiencia[Experiencia::COL_ID]);
                $classificacao = $experiencia[Classificacao::COL_NOME];

                array_push($retorno, [
                    "experiencia" => $experiencia[Experiencia::COL_ID],
                    "titulo" => $experiencia[Experiencia::COL_TITULO],
                    "classificacao" => $classificacao,
                    "disciplinas" => $disciplinas
                ]);
            }
        }

        http_response_code(200);
        return json_encode($retorno);
    }

    /**
     * Retorna todas as Experiências de uma turma
     *
     * @param  mixed $dados
     * @return void
     */
    public function listarExperienciasTurma($dados)
    {
        if (!isset($dados['turma'])) {
            throw new \Exception("É necessário informar o id da turma");
        }

        $turma_id = $dados['turma'];

        $campos = Experiencia::TABELA . "." . Experiencia::COL_ID . ", " . Experiencia::COL_ID_REUNIAO . ", " . Experiencia::COL_TITULO . ", " . Classificacao::COL_NOME;
        $busca = [Turma::COL_ID => $turma_id];

        $experiencia = new Experiencia();
        $experiencias = $experiencia->listar($campos, $busca, null, 1000);

        $retorno = [];

        if (!empty($experiencias) && !empty($experiencias[0])) {
            foreach ($experiencias as $experiencia) {
                $disciplinas = $this->disciplinasExperiencia($experiencia[Experiencia::COL_ID]);
                $classificacao = $experiencia[Classificacao::COL_NOME];

                array_push($retorno, [
                    "experiencia" => $experiencia[Experiencia::COL_ID],
                    "titulo" => $experiencia[Experiencia::COL_TITULO],
                    "classificacao" => $classificacao,
                    "disciplinas" => $disciplinas
                ]);
            }
        }

        http_response_code(200);
        return json_encode($retorno);
    }
}
