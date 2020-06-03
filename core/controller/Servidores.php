<?php


namespace core\controller;

use core\model\Servidor;


class Servidores
{

    public function listarServidores($dados = [])
    {

        $campos = Servidor::COL_COD_PESSOA . ", " .
            // "CONCAT(SUBSTRING(NOME_PESSOA, 1, CHARINDEX(' ', NOME_PESSOA) - 1),' ', REVERSE(SUBSTRING(REVERSE(NOME_PESSOA), 1, CHARINDEX(' ', REVERSE(NOME_PESSOA)) - 1))) as nome";
            "{fn CONCAT(SUBSTRING(NOME_PESSOA, 1, CHARINDEX(' ', NOME_PESSOA) - 1), {fn CONCAT(' ', REVERSE(SUBSTRING(REVERSE(NOME_PESSOA), 1, CHARINDEX(' ', REVERSE(NOME_PESSOA)) - 1)))})} as nome";

        $servidor = new Servidor();

        $servidores = $servidor->listar($campos, null, null, null);

        // print_r($servidores);
        $retorno = [];

        if (!empty($servidores)) {
            foreach ($servidores as $servidor) {
                array_push($retorno, [
                    'codigo' => $servidor[Servidor::COL_COD_PESSOA],
                    'nome' => $servidor['nome']
                ]);
            }
        }

        http_response_code(200);
        return json_encode($retorno);
    }

    public function selecionarServidor($servidorId)
    {

        $campos = Servidor::TABELA . "." . Servidor::COL_COD_PESSOA . ", " .
                "PESSOAS." . Servidor::COL_EMAIL . ", " . 
            // "CONCAT(SUBSTRING(PESSOAS.NOME_PESSOA, 1, CHARINDEX(' ', PESSOAS.NOME_PESSOA) - 1),' ', REVERSE(SUBSTRING(REVERSE(PESSOAS.NOME_PESSOA), 1, CHARINDEX(' ', REVERSE(PESSOAS.NOME_PESSOA)) - 1))) as nome";
            "{fn CONCAT(SUBSTRING(PESSOAS.NOME_PESSOA, 1, CHARINDEX(' ', PESSOAS.NOME_PESSOA) - 1), {fn CONCAT(' ', REVERSE(SUBSTRING(REVERSE(PESSOAS.NOME_PESSOA), 1, CHARINDEX(' ', REVERSE(PESSOAS.NOME_PESSOA)) - 1)))})} as nome";

            $busca = [Servidor::COL_COD_PESSOA => $servidorId];

        $s = new Servidor();

        $servidor = ($s->listar($campos, $busca, null, null))[0];

        return $servidor;
    }

    public function selecionarServidorTeste($dados = [])
    {

        $servidorId = $dados['servidor'];
        $campos = Servidor::TABELA . "." . Servidor::COL_COD_PESSOA . ", " .
                "PESSOAS." . Servidor::COL_EMAIL . ", " . 
            // "CONCAT(SUBSTRING(PESSOAS.NOME_PESSOA, 1, CHARINDEX(' ', PESSOAS.NOME_PESSOA) - 1),' ', REVERSE(SUBSTRING(REVERSE(PESSOAS.NOME_PESSOA), 1, CHARINDEX(' ', REVERSE(PESSOAS.NOME_PESSOA)) - 1))) as nome";
            "{fn CONCAT(SUBSTRING(PESSOAS.NOME_PESSOA, 1, CHARINDEX(' ', PESSOAS.NOME_PESSOA) - 1), {fn CONCAT(' ', REVERSE(SUBSTRING(REVERSE(PESSOAS.NOME_PESSOA), 1, CHARINDEX(' ', REVERSE(PESSOAS.NOME_PESSOA)) - 1)))})} as nome";

            $busca = [Servidor::COL_COD_PESSOA => $servidorId];

        $s = new Servidor();

        $servidor = ($s->listar($campos, $busca, null, null))[0];

        // print_r($servidor);
        return json_encode($servidor);
    }
}
