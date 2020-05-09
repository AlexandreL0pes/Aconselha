<?php

namespace core\model;

use core\CRUD;
use Exception;

class Perfil extends CRUD {
    const TABELA = "Perfil";
    const COL_ID = "id";
    const COL_NOME = "nome";
    const COL_DESCRICAO = "descricao";


    public function adicionar($dados) {
        try {

            $retorno = $this->create(self::TABELA, $dados);

        } catch (\Throwable $th) {
            echo "Mensagem: " . $th->getMessage() . "\n Local: " . $th->getTraceAsString();
            return false;
        }

        return $retorno;
    }

    public function alterar($dados) {
        if (!isset($dados[self::COL_ID])) {
            throw new Exception("É necessário informar o ID do perfil");
        }

        $where_condicao = self::COL_ID . " = ?";
        $where_valor[] = $dados[self::COL_ID];

        try {
            $this->update(self::TABELA, $dados, $where_condicao, $where_valor);
        } catch (\Throwable $th) {
            echo "Mensagem: " . $th->getMessage() . "\n Local: " . $th->getTraceAsString();
            return false;
        }

        return $dados[self::COL_ID];

    }

    public function listar($campos = null, $busca = [], $ordem = null, $limite = null) {
        
        $campos = $campos != null ? $campos : " * ";
        $ordem = $ordem != null ? $ordem : self::COL_ID . " ASC";
        $limite = $limite != null ? $limite : 10;

        $where_condicao = " 1 = 1 ";
        $where_valor = [];

        if ($busca && count($busca) > 0){
            // Fazer a busca por nome 
            if (isset($busca[self::COL_NOME]) && !empty($busca[self::COL_NOME])){
                $where_condicao .= " AND " . self::COL_NOME . " LIKE ? ";
                $where_valor[] = "%{$busca[self::COL_NOME]}%";
            }
        }

        $retorno = [];

        try {
            $retorno = $this->read(null, self::TABELA, $campos, $where_condicao, $where_valor, null, $ordem, $limite);  
        } catch (\Throwable $th) {
            echo "Mensagem: " . $th->getMessage() . "\n Local: " . $th->getTraceAsString();
            return false;
        }

        return $retorno;


    }

}
