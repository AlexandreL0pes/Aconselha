<?php


namespace core\controller;


use core\model\Aluno;
use core\model\Aprendizado;
use core\model\MedidaDisciplinar;
use core\model\Turma;

class Alunos
{


    /**
     * Retorna o nome e matrícula de um estudante, com base na sua matricula
     *
     * @param  mixed $matricula
     * @return void
     */
    public function selecionar($matricula)
    {


        $campos = Aluno::COL_MATRICULA . ", " .
            Aluno::COL_COD_CURSO . ", " .
            Aluno::COL_COD_TURMA_ATUAL . ", " .
            "{fn CONCAT(SUBSTRING(PESSOAS.NOME_PESSOA, 1, CHARINDEX(' ', PESSOAS.NOME_PESSOA) - 1), {fn CONCAT(' ', REVERSE(SUBSTRING(REVERSE(PESSOAS.NOME_PESSOA), 1, CHARINDEX(' ', REVERSE(PESSOAS.NOME_PESSOA)) - 1)))})} as nome ";

        $busca = [Aluno::COL_MATRICULA => $matricula];

        $a = new Aluno();

        $aluno = ($a->listar($campos, $busca, null, 1))[0];

        if (!empty($aluno)) {

            $aluno = [
                'nome' => $aluno['nome'],
                'matricula' => $aluno[Aluno::COL_MATRICULA],
                'curso' => $aluno[Aluno::COL_COD_CURSO],
                'turma' => $aluno[Aluno::COL_COD_TURMA_ATUAL]
            ];
        }

        return $aluno;
    }


    public function obterMedidasDisciplinares($dados)
    {
        if (!isset($dados['aluno'])) {
            http_response_code(200);
            return json_encode(array('message' => 'É necessário informar o aluno'));
        }

        $md = new MedidasDisciplinares();

        $medidas = $md->listarMedidasMatricula($dados['aluno']);

        $a = new Alunos();

        $medidas_completas = [];

        if (count($medidas) > 0 && !empty($medidas[0])) {
            foreach ($medidas as $medida) {
                $aluno = $a->selecionar($medida['MATRICULA']);
                $m = [
                    'cod_medida' => $medida['COD_MEDIDA_DISCIPLINAR'],
                    'aluno' => $aluno,
                    'data' => $medida['DT_MEDIDA_DISCIPLINAR'],
                    'descricao' => $medida['DESC_TIPO_MEDIDA_DISCIPLINAR']
                ];
                array_push($medidas_completas, $m);
            }
        }
        http_response_code(200);
        return json_encode($medidas_completas);
    }

    public function obterCoeficienteGeral($matricula = null)
    {
        $campos = Aluno::COL_COEFICIENTE_RENDIMENTO;

        $busca = [Aluno::COL_MATRICULA => $matricula];

        $a = new Aluno();

        $coef = $a->listar($campos, $busca, null, 1)[0];

        if (!empty($coef)) {
            $coef = round($coef[Aluno::COL_COEFICIENTE_RENDIMENTO], 2);
            return $coef;
        }

        return $coef;
    }


    public function obterEstatisticas($dados)
    {
        if (!isset($dados['aluno'])) {
            http_response_code(200);
            return json_encode(array('message' => 'É necessário informar o aluno'));
        }

        $coef = $this->obterCoeficienteGeral($dados['aluno']);


        $a = new Aprendizados();

        $aprendizadosAluno = $a->listarAprendizadoAluno($dados);
        $aprendizadosAluno = json_decode($aprendizadosAluno, true);
        $aprendizadosAluno = count($aprendizadosAluno);

        $medidas = $this->obterMedidasDisciplinares($dados);

        $medidas = json_decode($medidas, true);
        $medidas = count($medidas);


        $estatisticas = [
            'coeficiente_geral' => $coef,
            'aprendizados' => $aprendizadosAluno,
            'medidas' => $medidas
        ];

        return json_encode($estatisticas);
    }

    public function obterAprendizados($dados)
    {
        if (!isset($dados['aluno'])) {
            http_response_code(200);
            return json_encode(array('message' => 'É necessário informar o aluno'));
        }

        $a = new Aprendizados();

        $aprendizados = $a->listarAprendizadoAluno($dados);
        #$aprendizados = json_decode($aprendizados, true);

        http_response_code(200);
        return $aprendizados;
    }

    public function obterInformacoes($dados)
    {
        if (!isset($dados['aluno'])) {
            http_response_code(400);
            return json_encode(array('message' => 'É necessário informar o aluno.'));
        }

        // Selecionar Aluno
        // Selecionar Curso
        $a = $this->selecionar($dados['aluno']);

        // $curso_id = $a['curso'];
        // unset($a['curso']);
        // $c = new Cursos();
        // $curso = $c->selecionarCurso($curso_id);

        $turma_id = $a['turma'];
        unset($a['turma']);

        $t = new Turmas();
        $turma = $t->informacoesTurma(['turma' => $turma_id]);
        $turma = json_decode($turma);

        $info = [
            'aluno' => $a,
            'turma' => $turma
        ];

        http_response_code(200);
        return json_encode($info);
    }
}
