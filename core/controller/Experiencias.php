<?php

namespace core\controller;

use core\model\Experiencia;
use core\model\DisciplinaExperiencia;


class Experiencias
{
    private $experiencia_id = null;
    private $titulo = null;
    private $observacao = null;
    private $reuniao_id = null;
    private $classificacao_id = null;

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
                return array('message' => 'A Experiência foi cadastrada!');
            } else {
                http_response_code(500);
                return array('message' => 'Não foi possível cadastrar todos as disciplinas', 'errors' => $erros);
            }
        } else {
            http_response_code(500);
            return array('message' => 'Não foi possível cadastrar a experiência');
        }
    }
}
