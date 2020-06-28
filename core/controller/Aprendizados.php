<?php


namespace core\controller;

use core\model\Aluno;
use core\model\Aprendizado;
use core\model\EstudanteAprendizado;
use core\model\Reuniao;
use core\model\Turma;

class Aprendizados
{

    /**
     * Efetua o cadastro de uma Avaliação de Ensino-Aprendizado
     *
     * @param  mixed $dados
     * @return bool
     */
    public function cadastrar($dados)
    {

        $disciplina = $dados['disciplina'];
        $observacao = $dados['descricao'];
        $dataAtual = date('Y-m-d h:i:s');
        $estudantes = $dados['estudantes'];


        // Obtem o dado da reunião atual, com base na token do usuário logado
        $token = $dados['token'];
        $r = new Reunioes();
        $reuniao = $r->obterReuniaoTurma($token);


        $avaliacao = new Aprendizado();

        $resultadoAprendizado = $avaliacao->adicionar([
            Aprendizado::COL_ID_REUNIAO => $reuniao,
            Aprendizado::COL_DISCIPLINA => $disciplina,
            Aprendizado::COL_OBSERVACAO => $observacao,
            Aprendizado::COL_DATA => $dataAtual
        ]);

        if ($resultadoAprendizado > 0) {
            $estudantesAprendizado = new EstudanteAprendizado();

            $erros = array();

            foreach ($estudantes as $estudante) {
                $resultadoEstudante = $estudantesAprendizado->adicionar([
                    EstudanteAprendizado::COL_ID_APRENDIZADO => $resultadoAprendizado,
                    EstudanteAprendizado::COL_MATRICULA => $estudante
                ]);

                if (!($resultadoEstudante > 0)) {
                    array_push($erros, $estudante);
                }
            }

            if (empty($erros)) {
                http_response_code(200);
                return json_encode(array('message' => 'O aprendizado foi salvo com sucesso!'));
            } else {
                http_response_code(500);
                return json_encode(array('message' => 'Houve um erro no cadastro dos estudantes', 'errors' => $erros));
            }
        } else {
            http_response_code(500);
            return json_encode(array('message' => 'Não foi possível cadastrar o aprendizado!'));
        }
    }


    /**
     * Efetua a alteração dos dados de uma Avaliação de Ensino-Aprenzizado
     *
     * @param  mixed $dados
     * @return void
     */
    public function alterar($dados)
    {

        $aprendizado_id = $dados['aprendizado'];
        $disciplina = $dados['disciplina'];
        $observacao = $dados['descricao'];

        $estudantes = $dados['estudantes'];

        $avaliacao = new Aprendizado();

        $resultadoAvaliacao = $avaliacao->alterar([
            Aprendizado::COL_ID => $aprendizado_id,
            Aprendizado::COL_DISCIPLINA => $disciplina,
            Aprendizado::COL_OBSERVACAO => $observacao
        ]);

        if ($resultadoAvaliacao > 0) {
            $estudantesAvaliacao = new EstudanteAprendizado();
            $estudantesAvaliacao->excluir([EstudanteAprendizado::COL_ID_APRENDIZADO => $aprendizado_id]);

            $erros = array();

            foreach ($estudantes as $estudante) {
                $resultadoEstudantes = $estudantesAvaliacao->adicionar([
                    EstudanteAprendizado::COL_ID_APRENDIZADO => $resultadoAvaliacao,
                    EstudanteAprendizado::COL_MATRICULA => $estudante
                ]);

                if (!($resultadoEstudantes > 0)) {
                    array_push($erros, $estudante);
                }
            }

            if (empty($erros)) {
                http_response_code(200);
                return json_encode(array('message' => 'O aprendizado foi alterado com sucesso!'));
            } else {
                http_response_code(500);
                return json_encode(array('message' => 'Houve um erro na alteração dos estudantes!', $erros => $erros));
            }
        } else {
            http_response_code(500);
            return json_encode(array('message' => 'Houve um erro na alteração do aprendizado!'));
        }
    }

    /**
     * Obtem os dados referentes à uma Avaliação de Ensino-Aprendizado
     *
     * @param  mixed $dados
     * @return void
     */
    public function selecionar($dados)
    {
        $aprendizado_id = $dados['aprendizado'];

        $avaliacao = new Aprendizado();

        $campos = Aprendizado::COL_ID . ", " .
            Aprendizado::COL_ID_REUNIAO . ", " .
            Aprendizado::COL_DISCIPLINA . ", " .
            Aprendizado::COL_DATA . ", " .
            Aprendizado::COL_OBSERVACAO;

        $busca = [Aprendizado::COL_ID => $aprendizado_id];


        $resultadoAprendizado = $avaliacao->listar($campos, $busca, null, 1)[0];
        $d = new Disciplinas();
        if (!empty($resultadoAprendizado)) {
            $estudantes = $this->estudantesAprendizado($aprendizado_id);

            $disciplina = $d->selecionar($resultadoAprendizado[Aprendizado::COL_DISCIPLINA]);

            $aprendizadoCompleto = [
                'aprendizado' => $resultadoAprendizado[Aprendizado::COL_ID],
                'reuniao' => $resultadoAprendizado[Aprendizado::COL_ID_REUNIAO],
                'data' => $resultadoAprendizado[Aprendizado::COL_DATA],
                'disciplina' => $disciplina,
                'estudantes' => $estudantes,
                'observacao' => $resultadoAprendizado[Aprendizado::COL_OBSERVACAO]
            ];

            http_response_code(200);
            return json_encode($aprendizadoCompleto);
        } else {
            http_response_code(500);
            return json_encode(array('message' => 'O aprendizado solicitado não foi encontrado!'));
        }
    }

    /**
     * Obtem todos os estudantes relacionados à uma Avaliação de Ensino-Aprendizado
     *
     * @param  mixed $aprendizado_id
     * @return void
     */
    private function estudantesAprendizado($aprendizado_id)
    {

        $estudantesAvaliacao = new EstudanteAprendizado();
        $busca = [EstudanteAprendizado::COL_ID_APRENDIZADO => $aprendizado_id];

        $estudantes_id = $estudantesAvaliacao->listar(EstudanteAprendizado::COL_MATRICULA, $busca, null, 100);
        $estudantes = [];

        $aluno = new Alunos();
        foreach ($estudantes_id as $estudante_id) {
            $retornoAluno = $aluno->selecionar($estudante_id[EstudanteAprendizado::COL_MATRICULA]);

            array_push($estudantes, $retornoAluno);
        }

        return $estudantes;
    }

    /**
     * Exclui uma avaliação de Ensino-Aprendizado
     *
     * @param  mixed $dados
     * @return void
     */
    public function excluir($dados)
    {
        $aprendizado_id = $dados['aprendizado'];

        $condicao = [EstudanteAprendizado::COL_ID_APRENDIZADO => $aprendizado_id];

        $estudantesAvaliacao = new EstudanteAprendizado();

        $retornoEstudantes = $estudantesAvaliacao->excluir($condicao);

        if ($retornoEstudantes) {
            $aprendizado = new Aprendizado();
            $condicao = [Aprendizado::COL_ID => $aprendizado_id];
            $retornoAprendizado = $aprendizado->excluir($condicao);
            if ($retornoAprendizado) {
                http_response_code(200);
                return json_encode(array('message' => 'O aprendizado foi excluído!'));
            } else {
                http_response_code(500);
                return json_encode(array('message' => 'Houve um erro durante a exclusão dos estudantes'));
            }
        } else {
            http_response_code(500);
            return json_encode(array('message' => 'Houve um erro na exclusão do aprendizado!'));
        }
    }

    /**
     * Obtem todas as Avaliações de Ensino-Aprendizado de um conselho
     *
     * @param  mixed $dados
     * @return void
     */
    public function listarAprendizadosReuniao($dados)
    {

        // Caso a token seja passada, usar ela, caso não use o id
        if (isset($dados['token']) && !isset($dados['reuniao'])) {
            $token = $dados['token'];
            $r = new Reunioes();
            $reuniao_id = $r->obterReuniaoTurma($token);
        } else {
            $reuniao_id = $dados['reuniao'];
        }

        $campos = Aprendizado::COL_ID . ", " . Aprendizado::COL_ID_REUNIAO . ", " . Aprendizado::COL_DISCIPLINA . ", " . Aprendizado::COL_OBSERVACAO;
        $busca = [Aprendizado::COL_ID_REUNIAO => $reuniao_id];
        $ordem = Aprendizado::COL_DATA . " DESC ";

        $aprendizado = new Aprendizado();

        $aprendizados = $aprendizado->listar($campos, $busca, $ordem, 1000);

        $retorno = [];

        $d = new Disciplinas();
        if (!empty($aprendizados) && !empty($aprendizados[0])) {
            foreach ($aprendizados as $aprendizado) {
                $disciplina = ['id' => $aprendizado[Aprendizado::COL_DISCIPLINA], 'nome' => 'Disciplina ' . $aprendizado[Aprendizado::COL_DISCIPLINA]];
                $disciplina = $d->selecionar($aprendizado[Aprendizado::COL_DISCIPLINA]);

                $estudantes = $this->estudantesAprendizado($aprendizado[Aprendizado::COL_ID]);

                array_push($retorno, [
                    "aprendizado" => $aprendizado[Aprendizado::COL_ID],
                    "disciplina" => $disciplina,
                    "estudantes" => $estudantes
                ]);
            }
        }

        return json_encode($retorno);
    }

    /**
     * Retorna todos as Avaliações de Aprendizado de um turma
     *
     * @param  mixed $dados
     * @return void
     */
    public function listarAprendizadosTurma($dados = [])
    {
        $campos = Aprendizado::TABELA . "." . Aprendizado::COL_ID . ", " . Aprendizado::COL_ID_REUNIAO . ", " . Aprendizado::COL_DISCIPLINA . ", " . Aprendizado::COL_OBSERVACAO;
        $busca = [Turma::COL_ID => $dados['turma']];

        $a = new Aprendizado();

        $aprendizados = $a->listar($campos, $busca, null, 1000);

        $retorno = [];

        $d = new Disciplinas();
        if (!empty($aprendizados) && !empty($aprendizados[0])) {
            foreach ($aprendizados as $aprendizado) {
                $disciplina = ['id' => $aprendizado[Aprendizado::COL_DISCIPLINA], 'nome' => 'Disciplina ' . $aprendizado[Aprendizado::COL_DISCIPLINA]];
                $disciplina = $d->selecionar($aprendizado[Aprendizado::COL_DISCIPLINA]);

                $estudantes = $this->estudantesAprendizado($aprendizado[Aprendizado::COL_ID]);

                array_push($retorno, [
                    "aprendizado" => $aprendizado[Aprendizado::COL_ID],
                    "disciplina" => $disciplina,
                    "estudantes" => $estudantes
                ]);
            }
        }

        return json_encode($retorno);
    }
    
    /**
     * Obtem todos as Avaliações de Ensino-Aprendizado que envolvem um estudante
     *
     * @param  mixed $dados
     * @return void
     */
    public function listarAprendizadoAluno($dados)
    {
        $campos = Aprendizado::TABELA . "." . Aprendizado::COL_ID . ", " . Aprendizado::COL_ID_REUNIAO . ", " . Aprendizado::COL_DISCIPLINA . ", " . Aprendizado::COL_OBSERVACAO . ", " . Aprendizado::COL_DATA;
        $busca = ['COD_MATRICULA' => $dados['aluno']];

        $a = new Aprendizado();

        $aprendizados = $a->listar($campos, $busca, null, 1000);

        $retorno = [];

        $d = new Disciplinas();
        if (!empty($aprendizados) && !empty($aprendizados[0])) {
            foreach ($aprendizados as $aprendizado) {
                $disciplina = ['id' => $aprendizado[Aprendizado::COL_DISCIPLINA], 'nome' => 'Disciplina ' . $aprendizado[Aprendizado::COL_DISCIPLINA]];
                $disciplina = $d->selecionar($aprendizado[Aprendizado::COL_DISCIPLINA]);

                $estudantes = $this->estudantesAprendizado($aprendizado[Aprendizado::COL_ID]);

                array_push($retorno, [
                    "aprendizado" => $aprendizado[Aprendizado::COL_ID],
                    "disciplina" => $disciplina,
                    "estudantes" => $estudantes,
                    "data" => $aprendizado[Aprendizado::COL_DATA]
                ]);
            }
        }

        return json_encode($retorno);
    }
}
