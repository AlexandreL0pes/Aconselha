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
                return array('message' => 'O aprendizado foi salvo com sucesso!');
            } else {
                http_response_code(500);
                return array('message' => 'Houve um erro no cadastro dos estudantes', 'errors' => $erros);
            }
        } else {
            http_response_code(500);
            return array('message' => 'Não foi possível cadastrar o aprendizado!');
        }
    }
}
