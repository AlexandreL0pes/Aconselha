<?php


namespace core\controller;


use core\model\Aluno;



class Alunos {

    
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
}