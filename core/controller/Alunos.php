<?php


namespace core\controller;


use core\model\Aluno;



class Alunos {


    public function selecionar($matricula)
    {
        # $matriculaAluno = $dados['matricula'];


        $campos = Aluno::COL_MATRICULA . ", " . 
        // "CONCAT(SUBSTRING(PESSOAS.NOME_PESSOA, 1, CHARINDEX(' ', PESSOAS.NOME_PESSOA) - 1),' ', REVERSE(SUBSTRING(REVERSE(PESSOAS.NOME_PESSOA), 1, CHARINDEX(' ', REVERSE(PESSOAS.NOME_PESSOA)) - 1))) as nome";
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