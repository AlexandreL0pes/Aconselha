<?php


namespace core\controller;

use core\model\Curso;


class Cursos {

    public function selecionarCurso($dados)
    {
        $codigoCurso = $dados['curso'];

        $campos = Curso::COL_ID . ", " . 
                     Curso::COL_DESC_CURSO;
        
        $busca = [Curso::COL_ID => $codigoCurso];

        $curso = new Curso;

        $retornoCurso = ($curso->listar($campos, $busca, null, 1))[0];

        if (!empty($retornoCurso)) {
            $nome = $this->processarCurso($retornoCurso[Curso::COL_DESC_CURSO]);

            $curso = [
                'codigo' => $codigoCurso, 
                'nome' => $nome
            ];

            http_response_code(200);
            return json_encode($curso);
        }else {
            http_response_code(500);
            return json_encode(array('message' => "Nenhum curso foi encontrado!"));
        }

    }

    private function processarCurso($descricaoCurso = null)
    {
        if ($descricaoCurso == null) {
            return "";
        }

        $tecnico_removido = (explode('Técnico em ', $descricaoCurso))[1];
        $curso = (explode(" Integrado", $tecnico_removido))[0];

        return $curso;
    }

    public function listarCursos()
    {
        echo "Oi";
    }
}