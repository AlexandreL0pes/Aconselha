<?php

namespace core\model;

use core\CRUD;
use Exception;


class Avaliacao extends CRUD {
    const TABELA = "Avaliacao";
    const COL_ID = "id";
    const COL_ID_REUNIAO = "idReuniao";
    const COL_PROFESSOR = "cod_professor";
    const COL_ESTUDANTE = "cod_matricula";
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
        $ordem = $ordem != null ? $ordem : self::COL_ID_REUNIAO . " ASC ";
        $limite = $limite != null ? $limite : 10;

        $where_condicao = " 1 = 1 ";
        $where_valor = [];
        $tabela = self::TABELA;

        if ($busca && count($busca) > 0) {
            if (isset($busca[self::COL_ID_REUNIAO]) && !empty($busca[self::COL_ID_REUNIAO]) ) {
                $where_condicao .= " AND " . self::COL_ID_REUNIAO . " = ? ";
                $where_valor[] = $busca[self::COL_ID_REUNIAO];
            }
            
            if (isset($busca[self::COL_ESTUDANTE]) && !empty($busca[self::COL_ESTUDANTE]) ) {
                $where_condicao .= " AND " . self::COL_ESTUDANTE . " = ? ";
                $where_valor[] = $busca[self::COL_ESTUDANTE];
            }

            if (isset($busca[self::COL_ID]) && !empty($busca[self::COL_ID])) {
                $where_condicao .= " AND " . self::COL_ID . " = ? ";
                $where_valor[] = $busca[self::COL_ID];
            }

            if (isset($busca['avaliacao']) && !empty($busca['avaliacao'])) {
                if ($busca['avaliacao'] == "atendimento") {
                    $tabela = self::TABELA . " av INNER JOIN " . Acao::TABELA ." a on av.". Avaliacao::COL_ACAO . " = a." . Acao::COL_ID;

                }
            }

        }

        $retorno = [];

        try {
            $retorno = $this->read(null, $tabela, $campos, $where_condicao, $where_valor, null, $ordem, $limite);
        } catch (\Throwable $th) {
            echo "Mensagem: " . $th->getMessage() . "\n Local: " . $th->getTraceAsString();
            return false;
        }

        return $retorno;
    }

    public function excluir($condicao)
    {
        $where_condicao = " 1 = 1 ";
        $where_valor = [];

        if ($condicao && count($condicao) > 0) {
            if (isset($condicao[self::COL_ID]) && !empty($condicao[self::COL_ID])) {
                $where_condicao .= " AND " . self::COL_ID . " = ? ";
                $where_valor[] = $condicao[self::COL_ID];
            }
        }

        $retorno = [];

        try {
            $retorno = $this->delete(self::TABELA, $where_condicao, $where_valor);
        } catch (\Throwable $th) {
            echo "Mensagem: " . $th->getMessage() . "\n Local: " . $th->getTraceAsString();
            return false;
        }
        
        return $retorno;
    }
}



?>