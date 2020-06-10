<?php


namespace core\model;

use core\CRUD;

class Permissao extends CRUD
{

    const TABELA = "Permissao";

    const COL_USUARIO = "usuario";
    const COL_ACESSO = "acesso";


    /**
     * Adiciona a permissão especificada em um usuário
     *
     * @param $dados
     * @return bool
     */
    public function adicionar($usuario_id = null, $acesso_id = null)
    {
        $dados = [];
        try {
            $dados = [self::COL_USUARIO => $usuario_id, self::COL_ACESSO => $acesso_id];

            $retorno = $this->create(self::TABELA, $dados);
        } catch (\Throwable $th) {
            echo "Mensagem: " . $th->getMessage() . "\n Local: " . $th->getTraceAsString();
            return false;
        }

        return $retorno;
    }


    /**
     * Remove a permissão do usuário 
     *
     * @param $usuario_id
     * @param $acesso_id
     * @return bool
     */
    public function remover($usuario_id = null, $acesso_id = null)
    {
        $where_condicao = self::COL_USUARIO . " = ? AND " . self::COL_ACESSO . " = ? ";
        $where_valor = [$usuario_id, $acesso_id];

        try {
            $this->delete(self::TABELA, $where_condicao, $where_valor);
        } catch (\Throwable $th) {
            echo "Mensagem: " . $th->getMessage() . "\n Local: " . $th->getTraceAsString();
            return false;
        }

        return true;
    }

    /**
     * Verifica se um usuário possui o nível de acesso especificado 
     *
     * @param $usuario_id
     * @param $acesso_id
     * @return bool
     */

    public function verificarPermissao($usuario_id = null, $acesso_id = null)
    {
        $where_condicao = self::COL_USUARIO . " = ? AND " . self::COL_ACESSO . " = ? ";
        $where_valor = [$usuario_id, $acesso_id];

        try {
            $retorno = ($this->read(self::TABELA, $where_condicao, $where_valor))[0];
        } catch (\Throwable $th) {
            echo "Mensage: " . $th->getMessage() . "\n Local: " . $th->getTraceAsString();
        }

        // Caso o retorno não esteja vazio, o usuário tem o acesso especificado
        return !empty($retorno);
    }

    public function obterPermissoes($usuario_id = null)
    {
        $where_condicao = self::COL_USUARIO . " = ? ";
        $where_valor[] = $usuario_id;

        $retorno = $this->read(null, self::TABELA, self::COL_ACESSO, $where_condicao, $where_valor, null, self::COL_ACESSO . " DESC ", null);

        return $retorno;
    }
}
