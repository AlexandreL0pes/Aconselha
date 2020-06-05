<?php



namespace core\controller;

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
        $usuario = new Usuario();

        $campos = Usuario::COL_ID . ", " .
            Usuario::COL_TURMA . ", " .
            Usuario::COL_PESSOA;
        $busca = [
            Usuario::COL_TURMA => $turma,
            Usuario::COL_PERMISSAO => Autenticacao::PROFESSOR,
            'periodo' => 'atual'
        ];

        $conselheiro = $usuario->listar($campos, $busca, null, 1)[0];
        return $conselheiro;
    }
}
