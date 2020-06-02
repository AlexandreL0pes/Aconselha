<?php


namespace core\controller;


use core\model\Aluno;



class Alunos {


    public function selecionar($dados = [])
    {
        $matriculaAluno = $dados['matricula'];


        $campos = Aluno::COL_MATRICULA . ", " . 
        "CONCAT(SUBSTRING(PESSOAS.NOME_PESSOA, 1, CHARINDEX(' ', PESSOAS.NOME_PESSOA) - 1),' ', REVERSE(SUBSTRING(REVERSE(PESSOAS.NOME_PESSOA), 1, CHARINDEX(' ', REVERSE(PESSOAS.NOME_PESSOA)) - 1))) as nome";

        $busca = [Aluno::COL_MATRICULA => $matriculaAluno];

        $aluno = new Aluno();

        $retornoAluno = ($aluno->listar($campos, $busca, null, 1))[0];

        if (!empty($retornoAluno)) {
            
            $alunoJSON = [
                'nome' => $retornoAluno['nome'],
                'matricula' => $retornoAluno[Aluno::COL_MATRICULA]
            ];

            http_response_code(200);
            return json_encode($alunoJSON);

        }else{
            http_response_code(500);
            return json_encode(array('message' => 'Nenhum aluno foi encontrado!'));
        }
    }
}