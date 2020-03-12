<?php

namespace core\model;

use core\CRUD;
use Exception;


class Avaliacao extends CRUD {
    const TABELA = "avaliacao";
    const COL_ID = "id";
    const COL_ID_CONSELHO = "idTurmaConselho";
    const COL_PROFESSOR = "cod_professor";
    const COL_MATRICULA = "cod_matricula";
    const COL_PERFIL = "idPerfil";
    const COL_DATA = "data";
    const COL_OBSERVACAO = "observacao";
    const COL_PAUTA = "cod_pauta";
    const COL_ACAO = "idAcao"; 

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
            throw new Exception("É necessário informar o id da avaliação");
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
        $ordem = $ordem != null ? $ordem : self::COL_ID_CONSELHO . " ASC ";
        $limite = $limite != null ? $limite : 10;

        $where_condicao = " 1 = 1 ";
        $where_valor = [];

        if ($busca && count($busca) > 0) {
            if (isset($busca[self::COL_ID_CONSELHO]) && !empty($busca[self::COL_ID_CONSELHO]) ) {
                $where_condicao .= " AND " . self::COL_ID_CONSELHO . " = ? ";
                $where_valor[] = $busca[self::COL_ID_CONSELHO];
            }
            
            if (isset($busca[self::COL_MATRICULA]) && !empty($busca[self::COL_MATRICULA]) ) {
                $where_condicao .= " AND " . self::COL_MATRICULA . " = ? ";
                $where_valor[] = $busca[self::COL_MATRICULA];
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



?>