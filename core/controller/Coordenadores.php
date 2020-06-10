<?php


namespace core\controller;

use core\model\Curso;
use core\model\Servidor;
use core\model\Usuario;
use core\sistema\Autenticacao;

class Coordenadores
{

    /**
     * Cadastra um usuário com privilégios de Coordenador 
     *
     * @param $dados
     * @return json 
     */
    public function cadastrar($dados)
    {

        // print_r($dados);

        $curso = $dados['curso'];
        $data_inicio = date('Y-m-d');
        $permissao = Autenticacao::COORDENADOR;
        $senha = $dados['senha'];
        $pessoa = $dados['coordenador'];
        $matricula = $dados['email'];

        // $matricula = (isset($servidor[Servidor::COL_EMAIL])) ? $servidor[Servidor::COL_EMAIL] : "";

        $usuario = new Usuario();


        $retorno = $usuario->adicionar([
            Usuario::COL_MATRICULA => $matricula,
            Usuario::COL_CURSO => $curso,
            Usuario::COL_DATA_INICIO => $data_inicio,
            Usuario::COL_PERMISSAO => $permissao,
            Usuario::COL_SENHA => $senha,
            Usuario::COL_PESSOA => $pessoa
        ]);

        // Salvar aqui a permissao do coordenador

        if ($retorno > 0) {
            http_response_code(200);
            return json_encode(array('message' => "O coordenador foi cadastrado!"));
        } else {
            http_response_code(500);
            return json_encode(array('message' => "Não foi possível cadastrar o coordenador!"));
        }
    }

    /**
     * Altera as credenciais de um usuário com permissão de Coordenador 
     * @param $dados
     * @return json
     */
    public function alterar($dados)
    {
        $coordenador = $dados['coordenador'];
        $matricula = $dados['matricula'];
        $curso = $dados['curso'];


        $data = array(
            Usuario::COL_ID => $coordenador,
            Usuario::COL_MATRICULA => $matricula,
            Usuario::COL_CURSO => $curso
        );

        if (isset($dados['senha']) && !empty($dados['senha'])) {
            $data[Usuario::COL_SENHA] = $dados['senha'];
        }

        $usuario = new Usuario();
        $retorno = $usuario->alterar($data);

        if ($retorno > 0) {
            http_response_code(200);
            return json_encode(array("message" => "O usuário foi alterado"));
        } else {
            http_response_code(500);
            return json_encode(array("message" => "Não foi possível alterar o coordenador!"));
        }
    }

    /**
     * Obtem os dados de um Coornenador, com base no curso 
     *
     * @param $curso
     * @return array
     */
    public function selecionarCoordenadorAtual($curso)
    {
        $usuario = new Usuario();

        $busca = [
            Usuario::COL_CURSO => $curso,
            'periodo' => 'atual'
        ];

        $coordenador = $usuario->listar(null, $busca, null, 1)[0];
        return $coordenador;
    }

    /**
     * Desabilita o coordenador colocando uma data fim para o usuário
     * Assim, tal usuário não é mais capaz de logar no sistema 
     *
     * @param $usuario_id
     * @param $acesso_id
     * @return bool
     */
    private function desabilitarCoordenador($curso)
    {
        // TODO: Apenas tirar a permissão de coordenador do usuário, visto que o usuário também pode ser um professor ...
        $coordenador = $this->selecionarCoordenadorAtual($curso);

        $coordenador_id = $coordenador[Usuario::COL_ID];
        $data_fim = date("Y-m-d");
        $dados = [
            Usuario::COL_ID => $coordenador_id,
            Usuario::COL_DATA_FIM => $data_fim,
            // Usuario::COL_PERMISSAO => null
        ];

        $usuario = new Usuario();
        $retorno = $usuario->alterar($dados);

        return $retorno;
    }

    /**
     * Atualiza um coordenador de curso, desabilitando o antigo
     *
     * @param $dados
     * @return json
     */
    public function atualizarCoordenador($dados)
    {
        $curso_id = $dados['curso'];
        $resultado = $this->desabilitarCoordenador($curso_id);


        if ($resultado > 0) {
            $retorno = $this->cadastrar([
                'curso' => $dados['curso'],
                'senha' => $dados['senha'],
                'email' => $dados['email'],
                'coordenador' => $dados['coordenador']
            ]);

            if ($retorno) {
                http_response_code(200);
                return json_encode(array("message" => "O coordenador foi atualizado com sucesso!"));
            }
            http_response_code(500);
            return json_encode(array("message" => "Não foi possível desabilitar o atual coordenador"));
        }
    }

    /**
     * Altera a senha de um usuário 
     *
     * @param $dados
     * @return json
     */
    public function alterarSenha($dados)
    {
        $curso = $dados['curso'];
        $email = $dados['email'];

        $coordenador = $this->selecionarCoordenadorAtual($curso);

        $data = array(
            Usuario::COL_ID => $coordenador[Usuario::COL_ID],
            Usuario::COL_MATRICULA => $dados['email'],
            Usuario::COL_SENHA => $dados['senha']
        );


        $usuario = new Usuario();
        $retorno = $usuario->alterar($data);
        if ($retorno > 0) {
            http_response_code(200);
            return json_encode(array("message" => "A senha foi alterada!"));
        } else {
            http_response_code(500);
            return json_encode(array("message" => "Não foi possível alterar a senha!"));
        }
    }

    /**
     * Obtem as informações de um usuário coordenador 
     *
     * @param $dados
     * @return json
     */
    public function selecionarCoordenador($dados)
    {

        $cursoId = $dados['curso'];

        $c = new Cursos();
        $curso = $c->selecionarCurso($cursoId);

        $coordenador = $this->obterInfoCoordenadorAtual($cursoId);


        $retorno = [
            'curso' => $curso,
            'coordenador' => $coordenador
        ];

        if ($curso > 0 && $coordenador > 0) {
            http_response_code(200);
            return json_encode($retorno);
        } else {
            http_response_code(500);
            return json_encode(array("message" => "Não foi possível selecionar o coordenador!"));
        }
    }


    /**
     * Obtem as informações de um usuário coordenador atual, com base no curso 
     *
     * @param $cod_curso Código do curso
     * @return json
     */
    public function obterInfoCoordenadorAtual($cod_curso)
    {
        $coordenador = $this->selecionarCoordenadorAtual($cod_curso);

        $coordenador = [
            'id' => $coordenador['id'],
            'login' => $coordenador[Usuario::COL_MATRICULA],
            'pessoa' => $coordenador[Usuario::COL_PESSOA]
        ];

        return $coordenador;
    }

    /**
     * Obtem o curso do coordenador, com base na token de acesso 
     *
     * @param $dados
     * @return json
     */
    public function obterCurso($dados)
    {
        $token = $dados['token'];

        $data = Autenticacao::verificarLogin($token);

        $curso = $data[Usuario::COL_CURSO];

        if (isset($curso) && !empty($curso)) {
            http_response_code(200);
            return json_encode(array('curso' => $curso));
        } else {
            http_response_code(400);
            return json_encode(array('message' => 'Não existe curso anexado ao coordenador!'));
        }
    }

    /**
     * Obtem todas as informações de um usuário coordenador atual, com base no curso 
     *
     * @param $cod_curso Código do curso
     * @return json
     */
    public function obterCoordenadorCurso($dados)
    {

        $cursoId = $dados['curso'];


        $coordenador = $this->selecionarCoordenadorAtual($cursoId);


        $retorno = [];

        if (!empty($coordenador) && $coordenador[Usuario::COL_PESSOA] != null) {
            $s = new Servidores();
            $servidor = $s->selecionarServidor($coordenador[Usuario::COL_PESSOA]);
            $retorno = [
                'codigo' => $coordenador[Usuario::COL_ID],
                'nome' => $servidor['nome']
            ];
        }

        http_response_code(200);
        return json_encode($retorno);
    }
}
