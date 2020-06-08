<?php


namespace core\controller;

use core\model\Curso;


class Cursos
{

    public function selecionarCurso($cod_curso)
    {

        $campos = Curso::COL_ID . ", " .
            Curso::COL_DESC_CURSO;

        $busca = [Curso::COL_ID => $cod_curso];

        $curso = new Curso();

        $retornoCurso = ($curso->listar($campos, $busca, null, 1))[0];

        $curso = [];
        if (!empty($retornoCurso)) {
            $nome = $this->processarCurso($retornoCurso[Curso::COL_DESC_CURSO]);

            $curso = [
                'codigo' => $cod_curso,
                'nome' => $nome
            ];

        }

        return $curso;
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
        $campos = Curso::COL_ID . ", " .
            Curso::COL_DESC_CURSO;

        $curso = new Curso();

        $retornoCursos = $curso->listar($campos, null, null, null);

        $cursos = [];

        $c = new Coordenadores();
        if (count($retornoCursos) > 0 && !empty($retornoCursos[0])) {
            foreach ($retornoCursos as $retornoCurso) {
                $nome = $this->processarCurso($retornoCurso[Curso::COL_DESC_CURSO]);
                $coordenador = $c->obterCoordenadorCurso(['curso' => $retornoCurso[Curso::COL_ID]]);
                $coordenador = json_decode($coordenador, true);
                $curso = [
                    'codigo' => $retornoCurso[Curso::COL_ID],
                    'nome' => $nome,
                    'coordenador' => $coordenador
                ];

                array_push($cursos, $curso);
            }
        }

        http_response_code(200);
        return json_encode($cursos);
    }
}
