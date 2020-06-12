<?php



namespace core\controller;

use core\model\Turma;
use core\model\Usuario;
use core\sistema\Autenticacao;

class Conselheiros
{


    public function obterConselheiro($turma)
    {

        $conselheiro = $this->selecionarConselheiroAtual($turma);

        $retorno = [];

        if (count($conselheiro) > 0) {
            $s = new Servidores();
            $servidor = $s->selecionarServidor($conselheiro[Usuario::COL_PESSOA]);
            $retorno = [
                'codigo' => $conselheiro[Usuario::COL_ID],
                'nome' => $servidor['nome']
            ];
        }

        http_response_code(200);
        return json_encode($retorno);
    }

    public function selecionarConselheiroAtual($turma)
    {
        // $turma = '20201.03AGP10I.1A';
        $usuario = new Usuario();

        $campos = Usuario::COL_ID . ", " .
            Usuario::COL_TURMA . ", " .
            Usuario::COL_PESSOA;
        $busca = [
            Usuario::COL_TURMA => $turma,
            'permissao' => Autenticacao::CONSELHEIRO,
            'periodo' => 'atual'
        ];

        $conselheiro = $usuario->listar($campos, $busca, null, 1)[0];
        return $conselheiro;
    }

    public function selecionarConselheiro($dados)
    {
        $turma_id = $dados['turma'];
        $t = new Turmas();
        $turma = $t->informacoesTurma(['turma' => $turma_id]);
        $turma = json_decode($turma, true);
        $conselheiro = $this->obterConselheiro($turma_id);
        $conselheiro = json_decode($conselheiro, true);

        $retorno = [
            'turma' => $turma,
            'conselheiro' => $conselheiro
        ];

        if ($turma > 0 && $conselheiro > 0) {

            http_response_code(200);
            return json_encode($retorno);
        } else {
            http_response_code(500);
            return json_encode(array("message" => "Não foi possível selecionar o conselheiro"));
        }
    }
}
