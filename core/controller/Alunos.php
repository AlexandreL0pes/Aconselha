<?php


namespace core\controller;


use core\model\Aluno;
use core\model\Aprendizado;
use core\model\MedidaDisciplinar;

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
            "{fn CONCAT(SUBSTRING(PESSOAS.NOME_PESSOA, 1, CHARINDEX(' ', PESSOAS.NOME_PESSOA) - 1), {fn CONCAT(' ', REVERSE(SUBSTRING(REVERSE(PESSOAS.NOME_PESSOA), 1, CHARINDEX(' ', REVERSE(PESSOAS.NOME_PESSOA)) - 1)))})} as nome";

        $busca = [Aluno::COL_MATRICULA => $matricula];

        $a = new Aluno();

        $aluno = ($a->listar($campos, $busca, null, 1))[0];

        if (!empty($aluno)) {

            $aluno = [
                'nome' => $aluno['nome'],
                'matricula' => $aluno[Aluno::COL_MATRICULA]
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

        print_r($medidas);
    }

    public function obterCoeficienteGeral($matricula = null)
    {
        $campos = Aluno::COL_COEFICIENTE_RENDIMENTO;

        $busca = [Aluno::COL_MATRICULA => $matricula];

        $a = new Aluno();

        $coef = $a->listar($campos, $busca, null, 1)[0];

        return $coef;
    }


    public function obterEstatisticas($dados)
    {
        if (!isset($dados['aluno'])) {
            http_response_code(200);
            return json_encode(array('message' => 'É necessário informar o aluno'));
        }

        $coef = $this->obterCoeficienteGeral($dados['aluno']);


        // $a = new Aprendizados();
        // $aprendizadosAluno = $a->

        // print_r($coef);
    }
}
