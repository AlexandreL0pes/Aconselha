<?php

namespace core\model;

use core\CRUD;
use Exception;

class Pessoa extends CRUD {
    const TABELA = "pessoa";
    CONST COL_ID = "id";
    CONST COL_NOME = "nome";

    public function adicionar($dados){
        try {
            $retorno = $this->create(self::TABELA, $dados);

        } catch (Exception $e) {
            echo "Mensagem: " . $e->getMessage() . "\n Local: " . $e->getTraceAsString();
            return false;
        }
        return $retorno;
    }

    public function listar($campos = null, $busca = [], $ordem = null) {
        $campos = $campos != null ? $campos : "*";
        $ordem = $ordem != null ? $ordem : null;
        $busca = null;

        $ordem = self::COL_NOME . " ASC ";
        $where_condicao = "1 = 1";
        $where_valor = [];

        $retorno = [];

        try {
            
            $retorno = $this->read(null, self::TABELA, null, $where_condicao, $where_valor, null, $ordem, null);
        } catch (Exception $e) {
            
            echo "Mensagem: " . $e->getMessage() . "\n Local: " . $e->getTraceAsString();
            return false;
        }
        return $retorno;
    }
  
    public function listarMS($campos = null, $busca = [], $ordem = null) {
        $campos = $campos != null ? $campos : "*";
        $ordem = $ordem != null ? $ordem : null;
        $busca = null;

        $ordem = self::COL_NOME . " ASC";
        $where_condicao = "1 = 1";
        $where_valor = [];

        $retorno = [];

        try {
            
            $retorno = $this->read("academico", self::TABELA, null, $where_condicao, $where_valor, null, $ordem, null);
        } catch (Exception $e) {

            echo "Mensagem: " . $e->getMessage() . "\n Local: " . $e->getTraceAsString();

        }

        return $retorno;
    
    }

}
?>