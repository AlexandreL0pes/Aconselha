<?php

use core\CRUD;
use core\model\Conselho;
use core\model\Assunto;
use core\model\Classificacao;
use core\model\Perfil;
use core\model\Acao;
use core\model\Avaliacao;
use core\model\Conselheiro;
use core\model\Coordenador;
use core\model\Encaminhamento;
use core\model\Representante;
use core\model\Usuario;

class Teste extends CRUD
{
    public function inserirConselho()
    {

        $conselho = new Conselho();

        $dados =         [
            'cod_turma' => '2',
            'data' => '2018-10-10',
            'etapaConselho' => '1',
            'finalizado' => '0'
        ];

        $resultado = $conselho->adicionar($dados);
        echo "<pre>";
        print_r($this->pegarUltimoSQL());
        print_r("\n111" . $resultado);
        echo "</pre>";
    }

    public function alterarConselho()
    {
        $dados = [
            'id' => 1,
            'data' => '2018-09-09',
            'finalizado' => 1
        ];
        $conselho = new Conselho();

        $resultado = $conselho->alterar($dados);

        echo ($this->pegarUltimoSQL());
        print_r($resultado);
    }

    public function listarConselho()
    {
        $conselho = new Conselho();

        $busca = [
            "data" => '2017-10-10 00:00:00'
        ];

        $lista = $conselho->listar(" id, cod_turma, data, etapaConselho, finalizado ", $busca, Conselho::COL_DATA . " DESC", 100);

        echo "<pre>";
        print_r($lista);
        echo "</pre>";
    }

    public function inserirAssunto()
    {
        $assunto = new Assunto();

        $dados = [
            "idTurmaConselho" => 1,
            "idClassificacao" => 2,
            "observacao" => 'Aulas teóricas baseadas apenas em slides, sem outro material de apoio.'
        ];

        $resultado = $assunto->adicionar($dados);
        echo '<pre>';
        print_r($resultado);
        echo '<pre>';
    }

    public function alterarAssunto()
    {
        $dados = [
            'id' => 5,
            Assunto::COL_CLASSIFICACAO => 1,
            'observacao' => 'Alteração realizada'
        ];

        $assunto = new Assunto();

        $resultado = $assunto->alterar($dados);

        print_r($resultado);
    }

    public function listarAssunto()
    {
        $assunto = new Assunto();

        $campos = " " . Assunto::COL_ID . ", " .
            Assunto::COL_CONSELHO . ", " .
            Assunto::COL_CLASSIFICACAO . ", " .
            Assunto::COL_OBSERVACAO . " ";
        $busca = [
            Assunto::COL_CLASSIFICACAO => 2
        ];

        $lista = $assunto->listar(null, $busca, null, 5);

        echo '<pre>';
        print_r($lista);
        echo '<pre>';
    }

    public function inserir()
    {
        $assunto = new Usuario();

        $dados = [
            "cod" => 12243,
            "cod_turma" => 488,
            'data_inicio' => '2020-03-12',
            'senha' => '12345'
        ];

        $resultado = $assunto->adicionar($dados);
        echo '<pre>';
        print_r($resultado);
        echo '<pre>';
    }

    public function alterar()
    {
        $assunto = new Usuario();
        
        $dados = [
            'id' => 2,
            "cod" => 123,
            "cod_turma" => 488,
            'data_inicio' => '2020-03-12',
            'senha' => '12345'
        ];

        $resultado = $assunto->alterar($dados);
        echo '<pre>';
        print_r($resultado);
        echo '<pre>';
    }

    public function listar()
    {
        $assunto = new Usuario();

        $campos = null;
        $busca = [
            'periodo' => 'atual'        ];

        $lista = $assunto->listar(null, $busca, null, 5);

        echo '<pre>';
        print_r($lista);
        echo '<pre>';
    }
}
