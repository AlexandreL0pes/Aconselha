<?php


namespace core\controller;

use core\model\Aluno;
use core\model\Aprendizado;
use core\model\EstudanteAprendizado;

class Aprendizados
{
    public function cadastrar($dados)
    {

        $reuniao = $dados['reuniao'];
        $disciplina = $dados['disciplina'];
        $observacao = $dados['descricao'];
        $dataAtual = date('Y-m-d h:i:s');
        $estudantes = $dados['estudantes'];

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

    public function alterar($dados)
    {

        $aprendizado_id = $dados['aprendizado'];
        $reuniao = $dados['reuniao'];
        $disciplina = $dados['disciplina'];
        $observacao = $dados['descricao'];

        $estudantes = $dados['estudantes'];

        $avaliacao = new Aprendizado();

        $resultadoAvaliacao = $avaliacao->alterar([
            Aprendizado::COL_ID => $aprendizado_id,
            Aprendizado::COL_ID_REUNIAO => $reuniao,
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

    public function listarAprendizadosReuniao($dados)
    {


        $reuniao_id = $dados['reuniao'];
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
}
