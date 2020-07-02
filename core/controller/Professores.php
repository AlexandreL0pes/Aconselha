<?php


namespace core\controller;

use core\model\Permissao;
use core\model\Pessoa;
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


    /**
     * Obtem todos os usuários cadastrados no banco que tem permissão de professor 
     *
     * @return array
     */
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

    /**
     * Obtem os dados de um usuário professor
     *
     * @param  mixed $dados
     * @return array
     */
    public function selecionarProfessor($dados = [])
    {
        $cod_usuario = $dados['usuario'];

        $campos = Usuario::COL_ID . ", " . Usuario::COL_MATRICULA . ", " . Usuario::COL_PESSOA;
        $busca = [Usuario::COL_ID => $cod_usuario, 'permissao' => Autenticacao::PROFESSOR];


        $u = new Usuario();

        $usuario = $u->listar($campos, $busca, null, 1000)[0];


        $professores = [];

        if (count($usuario) > 0) {
            $professor = $this->selecionar($usuario[Usuario::COL_PESSOA]);

            $professores = [
                'usuario' => $usuario[Usuario::COL_ID],
                'email' => $usuario[Usuario::COL_MATRICULA],
                'pessoa' => $usuario[Usuario::COL_PESSOA],
                'nome' => $professor['nome']
            ];
        }

        http_response_code(200);
        return json_encode($professores);
    }

    /**
     * Altera as credenciais do usuário com permissão de professor
     *
     * @param  mixed $dados
     * @return array
     */
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

    /**
     * Obtem os professores cadastrados no SISTEMA ACADEMICO e importa eles para o bd local, com a permissão de professor
     *
     * @return array
     */
    public function atualizarUsuariosProfessores()
    {
        // Seleciona todos os professores do IF Ceres dos cursos técnicos 
        // Verifica se cada um deles está salvo no bd
        // Se não tiver, cadastra um novo usuário com senha padrão 
        // Verificar se já tem um usuário com a pessoa
        // Adicionar a permissão

        $p = new Professor();
        $campos =   " PESSOAS." . Professor::COL_COD_PESSOA . ", " . " PESSOAS.EMAIL ";
        $busca = ['professores' => 1];
        $retorno = $p->listar($campos, $busca, null, 10000);

        $senha =  uniqid();

        $err = [];
        $qtd_professores_add = 0;
        foreach ($retorno as $professor) {
            if (!$this->verificarUsuarioExistente($professor[Professor::COL_COD_PESSOA])) {
                $qtd_professores_add++;
                $email = ($professor[Professor::COL_EMAIL] != "") ? $professor[Professor::COL_EMAIL] : $professor[Professor::COL_COD_PESSOA] . "@provisorio.ifgoiano.edu.br";

                $u = $this->cadastrar($professor[Professor::COL_COD_PESSOA], $email, $senha);

                if ($u == false) {
                    $err[] = $professor[Professor::COL_COD_PESSOA];
                }
            }
        }

        if (count($err) > 0) {
            http_response_code(500);
            return json_encode(array('message' => 'Houveram erros durante a importação', 'error' => $err));
        }

        http_response_code(200);
        return json_encode(array('message' => "Todos os professores foram cadastrados com sucesso!", 'qtd_professores_add' => $qtd_professores_add));
    }

    /**
     * Verifica se um usuário existe, com base no COD_PESSOA 
     *
     * @param  mixed $pessoa
     * @return bool
     */
    public function verificarUsuarioExistente($pessoa = null)
    {
        if ($pessoa == null) {
            throw new \Exception("É ncessário informar a matrícula");
        }

        $campos = Usuario::COL_ID;
        $busca = [
            Usuario::COL_PESSOA => $pessoa
        ];

        $u = new Usuario();
        $usuario = $u->listar($campos, $busca, null, 1)[0];

        if (!empty($usuario)) {
            return true;
        }
        return false;
    }

    
    /**
     * Cadastro de um usuário com permissão de professor
     *
     * @param string $pessoa
     * @param string $email
     * @param string $senha
     * @return array
     */
    public function cadastrar($pessoa, $email, $senha)
    {
        $data_inicio = date('Y-m-d');
        $u = new Usuario();

        // Tenta adicionar o usuário
        $usuario = $u->adicionar([
            Usuario::COL_MATRICULA => $email,
            Usuario::COL_DATA_INICIO => $data_inicio,
            Usuario::COL_SENHA => $senha,
            Usuario::COL_PESSOA => $pessoa
        ]);

        // Caso o e-mail seja repetido, gera-se um novo e-mail e coloca no usuário
        if ($usuario == false) {
            $email = $pessoa . "@provisorio.ifgoiano.edu.br";
            $usuario = $u->adicionar([
                Usuario::COL_MATRICULA => $email,
                Usuario::COL_DATA_INICIO => $data_inicio,
                Usuario::COL_SENHA => $senha,
                Usuario::COL_PESSOA => $pessoa
            ]);
        }
        $permissao = false;

        if ($usuario) {
            $permissao = $this->addPermissao($usuario);
        }

        if ($usuario  && $permissao) {
            return $usuario;
        } else {
            return false;
        }
    }



    /**
     * Adiciona a permissão de Representante para um usuário
     *
     * @param  mixed $usuario_id
     * @return void
     */
    public function addPermissao($usuario_id)
    {
        if (!isset($usuario_id)) {
            throw new Exception("É necessário informar o id do usuário");
        }

        $resultado = true;
        $p = new Permissao();

        if (!$p->verificarPermissao($usuario_id, Autenticacao::PROFESSOR)) {
            $resultado = $p->adicionar($usuario_id, Autenticacao::PROFESSOR);
        }
        return $resultado;
    }
}
