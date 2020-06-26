<?php

namespace core\controller;

use core\model\Perfil;


class Perfis
{
    /**
     * Retorna todos os perfis cadastrados no banco de dados
     */
    public function listarPerfis()
    {
        return json_encode((new Perfil)->listar(null, null, null, 1000));
    }

    public function listarPerfisRelevantes($dados)
    {
        if (!isset($dados['turma'])) {
            http_response_code(400);
            return json_encode(array('message' => 'É necessário informar a turma.'));
        }

        $d = new Perfil();

        $campos = " nome, count(idPerfil) as qtd, Perfil.tipo ";
        $busca = [
            'perfis_relevantes' => $dados['turma']
        ];

        $perfis = $d->listar($campos, $busca, " qtd ASC ", 5);

        if (count($perfis) > 0) {
            http_response_code(200);
            return json_encode($perfis);
        }


        http_response_code(500);
        return json_encode(array('message' => 'Não foi encontrada nenhum perfil'));
    }

    public function listarPerfisRelevantesMatricula($dados)
    {
        if (!isset($dados['aluno'])) {
            http_response_code(400);
            return json_encode(array('message' => 'É necessário informar o aluno.'));
        }

        $d = new Perfil();

        $campos = " nome, count(idPerfil) as qtd, Perfil.tipo ";
        $busca = [
            'perfis_relevantes_matricula' => $dados['aluno']
        ];

        $perfis = $d->listar($campos, $busca, " qtd DESC ", 5);

        if (count($perfis) > 0) {
            http_response_code(200);
            return json_encode($perfis);
        }
        
    }
}
