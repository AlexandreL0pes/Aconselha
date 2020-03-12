<?php

namespace core\model;

use core\CRUD;
use Exception;

class Coordenador extends CRUD
{

    const TABELA = "coordenador";
    const COL_ID = "id";
    const COL_PROFESSOR = "cod_professor";
    const COL_CURSO = "cod_curso";
    const COL_DATA_INICIO = "data_inicio";
    const COL_DATA_FIM = "data_fim";
    const COL_SENHA = "senha";

    

    public function adicionar($dados)
    {
        try {

            $retorno = $this->create(self::TABELA, $dados);
        } catch (Exception $e) {
            echo "Mensagem: " . $e->getMessage() . "\n Local: " . $e->getTraceAsString();
            return false;
        }

        return $retorno;
    }

    public function alterar($dados)
    {
        if (!isset($dados[self::COL_ID])){
            throw new Exception("É necessário informar o ID do coordenador");
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

    public function listar($campos = null, $busca = [], $ordem = null, $limite = null)
    {
        $campos = $campos != null ? $campos : " * ";
        $ordem = $ordem != null ? $ordem : " " . self::COL_ID;
        $limite = $limite != null ? $limite : 10;

        $where_condicao = " 1 = 1 ";
        $where_valor = [];

        if (isset($busca[self::COL_ID]) && !empty($busca[self::COL_ID])) {
            $where_condicao .= " AND " . self::COL_ID . " = ?";
            $where_valor[] = $busca[self::COL_ID];
        }

        if (isset($busca[self::COL_CURSO]) && !empty($busca[self::COL_CURSO])) {
            $where_condicao .= " AND " . self::COL_CURSO . " = ?";
            $where_valor[] = $busca[self::COL_CURSO];
        }

        if (isset($busca['periodo']) && !empty($busca['periodo'])) {
            if ($busca['periodo'] == "atual") {

                $where_condicao .= " AND " . self::COL_DATA_INICIO . " <= ? ";
                $where_valor[] = date('Y-m-d');
                $where_condicao .= " AND " . self::COL_DATA_FIM . " is null ";


            
            } else {

                $where_condicao .= " AND YEAR(" . self::COL_DATA_INICIO . ") = ?";
                $where_valor[] = date('Y');

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
