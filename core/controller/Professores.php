<?php


namespace core\controller;

use core\model\Permissao;
use core\model\Professor;
use core\model\Turma;
use core\model\Usuario;
use core\sistema\Autenticacao;

class Professores
{

    /**
     * Retorna todas as turmas que um professor deu/dá aulas
     *
     * @param  mixed $dados
     * @return void
     */
    public function listarTurmas($dados = [])
    {
        $pessoa = $dados['professor'];

        $campos = Turma::COL_ID;

        $busca = [Professor::COL_COD_PESSOA => $pessoa];


        $professor = new Professor();

        $turmas = $professor->listar($campos, $busca, null, null);

        $turmas_id = [];
        if (count($turmas) > 0 && !empty($turmas[0])) {
            $turmas_id = array_map(function ($turma) {
                return $turma[Turma::COL_ID];
            }, $turmas);
        }
        return $turmas_id;
    }


    /**
     * Retorna todas as turmas que um professor dá aulas
     *
     * @param  mixed $dados
     * @return array
     */
    public function listarTurmasAtuais($dados = [])
    {
        $pessoa = $dados['professor'];

        $campos = Turma::COL_ID;

        $busca = [Professor::COL_COD_PESSOA => $pessoa, 'ano_letivo' => 'atual'];


        $professor = new Professor();

        $turmas = $professor->listar($campos, $busca, null, null);

        $turmas_id = [];
        if (count($turmas) > 0 && !empty($turmas[0])) {
            $turmas_id = array_map(function ($turma) {
                return $turma[Turma::COL_ID];
            }, $turmas);
        }
        return $turmas_id;
    }


    /**
     * Retorna as turma que um professor dá aula que tem um reunião em andamento
     *
     * @param  mixed $dados
     * @return void
     */
    public function listarTurmasReuniao($dados = [])
    {

        // $pessoa = $dados['professor'];
        $pessoa = Autenticacao::obterProfessor($dados['token']);
        // Obtem todas as turmas de um professor
        $turmas_professor = $this->listarTurmasAtuais(['professor' => $pessoa]);

        $r = new Reunioes();
        // Obtem as turmas que estão em reunião
        $reunioes = $r->listarReunioesAndamento();
        $reunioes = json_decode($reunioes, true);

        // echo "Turmas Professor\n";
        // print_r($turmas_professor);

        // echo "Turmas Reunião\n";
        // print_r($reunioes);

        $reunioes_professor = [];

        foreach ($reunioes as $reuniao) {
            if (in_array($reuniao['codigo'], $turmas_professor)) {
                array_push($reunioes_professor, $reuniao);
            }
        }

        // echo "Intersecção \n";
        // print_r($reunioes_professor);
        return json_encode($reunioes_professor);
    }

    /**
     * Retorna todas as turma sde um prof com informações completas 
     *
     * @param  mixed $dados
     * @return void
     */
    public function obterTurmasProfessor($dados = [])
    {
        $pessoa = Autenticacao::obterProfessor($dados['token']);
        $codigos = $this->listarTurmasAtuais(['professor' => $pessoa]);

        $t = new Turmas();
        $turmas = [];
        foreach ($codigos as $codigo) {
            $turma = $t->informacoesTurma(['turma' => $codigo]);
            $turma = json_decode($turma, true);
            array_push($turmas, $turma);
        }
        return json_encode($turmas);
    }

    /**
     * Retorna o códgo de pessoa e nome de um professor
     *
     * @param  mixed $cod_pessoa
     * @return void
     */
    public function selecionar($cod_pessoa = null)
    {
        $campos = "PROFESSORES" . "." . Professor::COL_COD_PESSOA . ", " .
            "{fn CONCAT(SUBSTRING(PESSOAS.NOME_PESSOA, 1, CHARINDEX(' ', PESSOAS.NOME_PESSOA) - 1), {fn CONCAT(' ', REVERSE(SUBSTRING(REVERSE(PESSOAS.NOME_PESSOA), 1, CHARINDEX(' ', REVERSE(PESSOAS.NOME_PESSOA)) - 1)))})} as nome";

        $busca = [Professor::COL_COD_PESSOA => $cod_pessoa];

        $p = new Professor();
        $professor = ($p->listar($campos, $busca, null, 1))[0];

        if (!empty($professor)) {
            $professor = [
                'nome' => $professor['nome'],
                'id' => $professor[Professor::COL_COD_PESSOA]
            ];
        }

        return $professor;
    }

    /**
     * Seleciona todos os professores atuais de uma turma
     *
     * @param  mixed $cod_turma
     * @return array
     */
    public function professoresAtuaisTurma($cod_turma = null)
    {
        $campos = Professor::TABELA . "." . Professor::COL_COD_PESSOA;
        $busca = ['turma' => $cod_turma, 'ano_letivo' => 'atual'];

        $p = new Professor();
        $cod_pessoas = $p->listar($campos, $busca, null, null);
        $pessoas = [];
        if (!empty($cod_pessoas[0])) {
            $pessoas = array_map(function ($pessoa) {
                return $pessoa[Professor::COL_COD_PESSOA];
            }, $cod_pessoas);
        }

        return $pessoas;
    }

    /**
     * Obtem as principais informações do professor, como primeirnome e cod_pessoa
     *
     * @param  mixed $dados
     * @return void
     */
    public function obterInformacao($dados)
    {
        if (!isset($dados['token'])) {
            http_response_code(400);
            return json_encode(array('message' => 'É necessário informar a turma.'));
        }
        $token = $dados['token'];

        $cod_pessoa = Autenticacao::obterProfessor($token);

        if ($cod_pessoa) {
            $professor = $this->selecionar($cod_pessoa);
            $professor['cod_pessoa'] = $professor['id'];
            $professor['primeiro_nome'] = explode(" ", $professor['nome'])[0];
            $professor['ultimo_nome'] = explode(" ", $professor['nome'])[1];

            unset($professor['nome']);
            unset($professor['id']);

            http_response_code(200);
            return json_encode($professor);
        }

        http_response_code(500);
        return json_encode(array('message' => 'Não foi possível obter as informações'));
    }


    public function listarUsuariosProfessores()
    {
        $campos = Usuario::COL_ID . ", " . Usuario::COL_MATRICULA . ", " . Usuario::COL_PESSOA;
        $busca = ['permissao' => Autenticacao::PROFESSOR];


        $u = new Usuario();

        $usuarios = $u->listar($campos, $busca, null, 1000);


        $professores = [];

        if (count($usuarios) > 0 && !empty($usuarios[0])) {
            foreach ($usuarios as $usuario) {
                $professor = $this->selecionar($usuario[Usuario::COL_PESSOA]);

                $professores[] = [
                    'usuario' => $usuario[Usuario::COL_ID],
                    'email' => $usuario[Usuario::COL_MATRICULA],
                    'pessoa' => $usuario[Usuario::COL_PESSOA],
                    'nome' => $professor['nome']
                ];
            }
        }

        http_response_code(200);
        return json_encode($professores);
    }

    public function selecionarProfessor($dados = [])
    {
        $cod_usuario = $dados['usuario'];

        $campos = Usuario::COL_ID . ", " . Usuario::COL_MATRICULA . ", " . Usuario::COL_PESSOA;
        $busca = [Usuario::COL_ID => $cod_usuario, 'permissao' => Autenticacao::PROFESSOR];


        $u = new Usuario();

        $usuarios = $u->listar($campos, $busca, null, 1000);


        $professores = [];

        if (count($usuarios) > 0 && !empty($usuarios[0])) {
            foreach ($usuarios as $usuario) {
                $professor = $this->selecionar($usuario[Usuario::COL_PESSOA]);

                $professores[] = [
                    'usuario' => $usuario[Usuario::COL_ID],
                    'email' => $usuario[Usuario::COL_MATRICULA],
                    'pessoa' => $usuario[Usuario::COL_PESSOA],
                    'nome' => $professor['nome']
                ];
            }
        }

        http_response_code(200);
        return json_encode($professores);
    }

    public function alterarSenha($dados)
    {
        $cod_usuario = $dados['usuario'];

        $data = [
            Usuario::COL_ID => $cod_usuario,
            Usuario::COL_MATRICULA => $dados['email'],
            Usuario::COL_SENHA => $dados['senha']
        ];

        $u = new Usuario();

        $retorno = $u->alterar($data);

        if ($retorno > 0) {
            http_response_code(200);
            return json_encode(array('message' => "A senha foi alterada!"));
        } else {
            http_response_code(500);
            return json_encode(array('message' => "Não foi possível alterar os dados!"));
        }
    }
}
