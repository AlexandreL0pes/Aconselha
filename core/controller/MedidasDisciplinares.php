<?php


namespace core\controller;

use core\model\MedidaDisciplinar;

class MedidasDisciplinares
{

    /**
     * Retorna todas as medidas disciplinares dos estudantes de uma turma
     *
     * @param  mixed $cod_turma
     * @return array
     */
    public function listarMedidasTurma($cod_turma = null)
    {
        $campos = MedidaDisciplinar::COL_COD_MEDIDA_DISCIPLINAR . ", " .
            MedidaDisciplinar::COL_MATRICULA . ", " .
            MedidaDisciplinar::COL_DATA . ", " .
            MedidaDisciplinar::COL_DESC_TIPO_MEDIDA_DISCIPLINAR;


        $busca = [MedidaDisciplinar::COL_COD_TURMA_ATUAL => $cod_turma];

        $md = new MedidaDisciplinar();

        $medidas = $md->listar($campos, $busca, null, null);

        $retorno = [];

        if (!empty($medidas)) {
            $retorno = $medidas;
        }

        return $retorno;
    }

    /**
     * Retorna todas as medidas disciplinares de um estudante
     *
     * @param  mixed $matricula
     * @return array
     */
    public function listarMedidasMatricula($matricula = null)
    {

        $campos = MedidaDisciplinar::COL_COD_MEDIDA_DISCIPLINAR . ", " .
            MedidaDisciplinar::COL_MATRICULA . ", " .
            MedidaDisciplinar::COL_DATA . ", " .
            MedidaDisciplinar::COL_DESC_TIPO_MEDIDA_DISCIPLINAR;

        $busca = [MedidaDisciplinar::COL_MATRICULA => $matricula];

        $md = new MedidaDisciplinar();
        $medidas = $md->listar($campos, $busca, MedidaDisciplinar::COL_DATA . " DESC ", null);

        if (!empty($medidas)) {
            $retorno = $medidas;
        }

        return $retorno;
    }
}
