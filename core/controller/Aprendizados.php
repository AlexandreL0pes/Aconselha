<?php


namespace core\controller;

use core\model\Avaliacao;
use core\model\EstudanteAvaliacao;

class Aprendizados
{
    public function cadastrar($dados)
    {

        $reuniao = $dados['reuniao'];
        $disciplina = $dados['disciplina'];
        $observacao = $dados['descricao'];
        $dataAtual = date('Y-m-d h:i:s');
        $estudantes = $dados['estudantes'];

        $avaliacao = new Avaliacao();

        $resultadoAvaliacao = $avaliacao->adicionar([
            Avaliacao::COL_ID_REUNIAO => $reuniao,
            Avaliacao::COL_PAUTA => $disciplina,
            Avaliacao::COL_OBSERVACAO => $observacao,
            Avaliacao::COL_DATA => $dataAtual
        ]);

        if ($resultadoAvaliacao > 0) {
            $estudantesAvaliacao = new EstudanteAvaliacao();

            $erros = array();

            foreach ($estudantes as $estudante) {
                $resultadoEstudante = $estudantesAvaliacao->adicionar([
                    EstudanteAvaliacao::COL_ID_AVALIACAO => $resultadoAvaliacao,
                    EstudanteAvaliacao::COL_MATRICULA => $estudante
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
        /* {
            "acao": "Aprendizados/cadastrar",
          "reuniao": 6,
            "disciplina": 5,
            "estudantes": [1,2,3],
            "descricao": "Uma observação qualquer"
        } */
        $aprendizado_id = $dados['aprendizado'];
        $reuniao = $dados['reuniao'];
        $disciplina = $dados['disciplina'];
        $observacao = $dados['descricao'];

        $estudantes = $dados['estudantes'];

        $avaliacao = new Avaliacao();

        $resultadoAvaliacao = $avaliacao->alterar([
            Avaliacao::COL_ID => $aprendizado_id,
            Avaliacao::COL_ID_REUNIAO => $reuniao,
            Avaliacao::COL_PAUTA => $disciplina,
            Avaliacao::COL_OBSERVACAO => $observacao
        ]);

        if ($resultadoAvaliacao > 0) {
            $estudantesAvaliacao = new EstudanteAvaliacao();
            $estudantesAvaliacao->excluir([EstudanteAvaliacao::COL_ID_AVALIACAO => $aprendizado_id]);

            $erros = array();

            foreach ($estudantes as $estudante) {
                $resultadoEstudantes = $estudantesAvaliacao->adicionar([
                    EstudanteAvaliacao::COL_ID_AVALIACAO => $resultadoAvaliacao,
                    EstudanteAvaliacao::COL_MATRICULA => $estudante
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

        $avaliacao = new Avaliacao();

        $campos = Avaliacao::COL_ID . ", " .
            Avaliacao::COL_ID_REUNIAO . ", " .
            Avaliacao::COL_PAUTA . ", " .
            Avaliacao::COL_DATA . ", " .
            Avaliacao::COL_OBSERVACAO;

        $busca = [Avaliacao::COL_ID => $aprendizado_id];

        $resultadoAprendizado = $avaliacao->listar($campos, $busca, null, 1)[0];
        if (!empty($resultadoAprendizado)) {
            $estudantes = $this->estudantesAprendizado($aprendizado_id);

            // TODO: Selecionar o nome da disciplina pelo id e retornar o nome
            $disciplina = ['id' => $resultadoAprendizado[Avaliacao::COL_PAUTA], 'nome' => 'Disciplina ' . $resultadoAprendizado[Avaliacao::COL_PAUTA]];

            $aprendizadoCompleto = [
                'aprendizado' => $resultadoAprendizado[Avaliacao::COL_ID],
                'reuniao' => $resultadoAprendizado[Avaliacao::COL_ID_REUNIAO],
                'data' => $resultadoAprendizado[Avaliacao::COL_DATA],
                'disciplina' => $disciplina,
                'estudantes' => $estudantes
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

        $estudantesAvaliacao = new EstudanteAvaliacao();
        $busca = [EstudanteAvaliacao::COL_ID_AVALIACAO => $aprendizado_id];

        $estudantes_id = $estudantesAvaliacao->listar(EstudanteAvaliacao::COL_MATRICULA, $busca, null, 100);
        $estudantes = [];

        foreach ($estudantes_id as $estudante_id) {
            $nome = "Estudante " . $estudante_id[EstudanteAvaliacao::COL_MATRICULA];
            array_push($estudantes, ['id' => $estudante_id[EstudanteAvaliacao::COL_MATRICULA], 'nome' => $nome]);
        }

        return $estudantes;
    }

    public function excluir($dados)
    {
        $aprendizado_id = $dados['aprendizado'];

        $condicao = [EstudanteAvaliacao::COL_ID_AVALIACAO => $aprendizado_id];

        $estudantesAvaliacao = new EstudanteAvaliacao();

        $retornoEstudantes = $estudantesAvaliacao->excluir($condicao);

        if ($retornoEstudantes) {
            $aprendizado = new Avaliacao();
            $condicao = [Avaliacao::COL_ID => $aprendizado_id];
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
}
