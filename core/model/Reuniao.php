<?php

namespace core\model;

use core\CRUD;
use Exception;

class Reuniao extends CRUD
{

    const TABELA = 'Reuniao';
    const COL_ID = 'id';
    const COL_COD_TURMA = 'cod_turma';
    const COL_DATA = 'data';
    const COL_ETAPA_CONSELHO = 'etapaConselho';
    const COL_FINALIZADO = 'finalizado';


    /**
     * @param $dados
     * @return bool
     */
    public function adicionar($dados)
    {

        try {
            $retorno = $this->create(self::TABELA, $dados);
        } catch (\Throwable $e) {
            echo "Mensagem: " . $e->getMessage() . "\n Local: " . $e->getTraceAsString();
            return false;
        }

        return $retorno;
    }

    /**
     * @param $dados
     * @return bool
     */
    public function alterar($dados) {
        if (!isset($dados[self::COL_ID])) {
            throw new Exception("É necessário informar o ID da reunião");
        }

        $where_condicao = self::COL_ID . " = ?";
        $where_valor[] = $dados[self::COL_ID];

        try {
            $this->update(self::TABELA, $dados, $where_condicao, $where_valor);
        } catch (Exception $e) {
            echo "Mensagem: " . $e->getMessage() . "\n Local: " . $e->getTraceAsString();
            return false;
        }
        return $dados[self::COL_ID];
    }

    public function listar($campos = null, $busca = [], $ordem = null, $limite = null) {

        $campos = $campos != null ? $campos : " * ";
        $ordem = $ordem != null ? $ordem : " " . self::COL_COD_TURMA . ", " . self::COL_ETAPA_CONSELHO;
        $limite = $limite != null ? $limite : 10;


        $where_condicao = " 1 = 1 ";
        $where_valor = [];

        if ($busca && count($busca) > 0) {

            if (isset($busca[self::COL_COD_TURMA]) && !empty($busca[self::COL_COD_TURMA])) {
                $where_condicao .= " AND " . self::COL_COD_TURMA . " = ?";
                $where_valor[] = $busca[self::COL_COD_TURMA];
            }

            if (isset($busca[self::COL_DATA]) && !empty($busca[self::COL_DATA])) {
                $where_condicao .= " AND YEAR(" . self::COL_DATA . ") = ?";
                $where_valor[] = date('Y');
            }

            if (isset($busca[self::COL_ETAPA_CONSELHO]) && !empty($busca[self::COL_ETAPA_CONSELHO])) {
                $where_condicao .= " AND " . self::COL_ETAPA_CONSELHO . " = ?";
                $where_valor[] = $busca[self::COL_ETAPA_CONSELHO];
            }
        
            if (isset($busca[self::COL_FINALIZADO]) && !empty($busca[self::COL_FINALIZADO])) {
                $where_condicao .= " AND " . self::COL_FINALIZADO . " = ?";
                $where_valor[] = $busca[self::COL_FINALIZADO];
            }
        }

        $retorno = [];


        try {

            $retorno = $this->read(null, self::TABELA, $campos, $where_condicao, $where_valor, null, $ordem, $limite);
        
        } catch (Exception $e) {
            echo "Mensagem: " . $e->getMessage() . "\n Local: " . $e->getTraceAsString();
        }

        return $retorno;
    }
}
