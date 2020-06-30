<?php


namespace core\controller;

use core\model\Aluno;
use core\model\Professor;
use core\model\Turma;
use core\model\Usuario;
use core\sistema\Autenticacao;
use core\sistema\Util;
use DateTime;

class Turmas
{
    /**
     * Retorna as informações de uma turma
     *
     * @param  mixed $dados
     * @return void
     */
    public function informacoesTurma($dados)
    {

        // Caso a token seja passada, usar ela, caso não use o id
        if (isset($dados['token']) && !isset($dados['turma'])) {
            $token = $dados['token'];
            $codigoTurma = Autenticacao::obterTurma($token);
        } else {
            $codigoTurma = $dados['turma'];
        }


        $campos = Turma::COL_ID . ", " .
            Turma::COL_DESC_TURMA . ", " .
            Turma::COL_CURSO;

        $busca = [Turma::COL_ID => $codigoTurma];

        $turma = new Turma();
        $retornoTurma = ($turma->listar($campos, $busca, null, 1))[0];

        $r = new Representantes();
        $representante = $r->selecionarRepresentante(['turma' => $codigoTurma]);
        $representante = json_decode($representante, true);
        $vr = new ViceRepresentantes();
        $vice = $vr->selecionarViceRepresentante(['turma' => $codigoTurma]);
        $vice = json_decode($vice, true);

        $c = new Conselheiros();
        $conselheiro = $c->obterConselheiro($codigoTurma);
        $conselheiro = json_decode($conselheiro, true);
        if (isset($conselheiro['login'])) {
            unset($conselheiro['login']);
            unset($conselheiro['pessoa']);
        }

        if (!empty($retornoTurma)) {
            $nomeTurma = $this->processarNome($retornoTurma[Turma::COL_ID]);
            $cursoTurma = $this->processarCurso($retornoTurma[Turma::COL_DESC_TURMA]);


            $turmaJson = [
                'codigo' => $codigoTurma,
                'nome' => $nomeTurma,
                'curso' => $cursoTurma,
                'codigo_curso' => $retornoTurma[Turma::COL_CURSO],
                'lideres' => [
                    'representante' => $representante,
                    'vice' => $vice,
                    'conselheiro' => $conselheiro
                ]
            ];

            http_response_code(200);
            return json_encode($turmaJson);
        } else {
            http_response_code(500);
            return json_encode(array('message' => 'Nenhuma turma foi encontrada!'));
        }
    }

    /**
     * Processa o nome de uma turma, com base no código
     *
     * @param  mixed $codigoTurma
     * @return string
     */
    public function processarNome($codigoTurma = null)
    {
        if ($codigoTurma == null) {
            return "";
        }

        $ano_turma = (explode(".", $codigoTurma))[2];

        $numeroTurma = substr($ano_turma, 0, 1);
        $letraTurma = substr($ano_turma, 1, 1);

        $nome = $numeroTurma . "° " . $letraTurma;
        return $nome;
    }

    /**
     * Processa o nome de um curso
     *
     * @param  mixed $descricaoTurma
     * @return string
     */
    public function processarCurso($descricaoTurma = null)
    {
        if ($descricaoTurma == null) {
            return "";
        }

        $tecnico_removido = (explode('Técnico em ', $descricaoTurma))[1];
        $curso = (explode(" Integrado", $tecnico_removido))[0];

        return $curso;
    }

    /**
     * Obtem o nome e matrícula de estudantes de uma turma
     *
     * @param  mixed $dados
     * @return void
     */
    public function listarEstudantes($dados)
    {
        $turma = $dados['turma'];

        $aluno = new Aluno();
        $campos = "MATRICULAS.MATRICULA as matricula, " .
            "{fn CONCAT(SUBSTRING(PESSOAS.NOME_PESSOA, 1, CHARINDEX(' ', PESSOAS.NOME_PESSOA) - 1), {fn CONCAT(' ', REVERSE(SUBSTRING(REVERSE(PESSOAS.NOME_PESSOA), 1, CHARINDEX(' ', REVERSE(PESSOAS.NOME_PESSOA)) - 1)))})} as nome, " .
            " ROUND(COEFICIENTE_RENDIMENTO ,1) as coeficiente_rendimento ";

        $busca = [Aluno::COL_COD_TURMA_ATUAL => $turma];
        $alunos = $aluno->listar($campos, $busca, null, null);
        $retorno = [];


        if (count($alunos) > 0) {
            foreach ($alunos as $aluno) {
                $aluno['classificacao'] = Util::classificarCoeficiente($aluno['coeficiente_rendimento']);
                $aluno['coeficiente_rendimento'] = number_format($aluno['coeficiente_rendimento'], 1, ',', '.');
                $retorno[] = $aluno;
            }
        }

        http_response_code(200);
        return json_encode($retorno);
    }

    /**
     * Verifica a turma de um curso
     *
     * @param  mixed $codTurma
     * @param  mixed $codCurso
     * @return bool
     */
    public function verificarTurmaCurso($codTurma, $codCurso)
    {
        $campos = Turma::COL_ID . ", " .
            Turma::COL_CURSO;
        $busca = [Turma::COL_ID => $codTurma];

        $turma = new Turma();
        $retornoTurma = ($turma->listar($campos, $busca, null, 1))[0];

        if ($retornoTurma[Turma::COL_CURSO] == $codCurso) {
            return true;
        }
        return false;
    }


    /**
     * Retorna uma lista das turmas que nunca participaram do conselho
     * @param array $turmasAvaliadas Lista com todas as turmas atuais que já participaram de um conselho
     */
    public function turmasForaReuniao($turmasAvaliadas = [])
    {
        $turma = new Turma();

        $campos = Turma::COL_ID;

        $turmas = implode("', '", $turmasAvaliadas);
        $turmas = "'" . $turmas . "'";

        $busca = ['avaliadas' => $turmas, 'ano' => 'atual'];
        $retorno = $turma->listar($campos, $busca, null, 10000);

        $retorno = array_map(function ($id) {
            return $id[Turma::COL_ID];
        }, $retorno);

        return $retorno;
    }

    /**
     * Retorna a informação das turmas solicitadas
     *
     * @param  mixed $dados
     * @return void
     */
    public function informacoesTurmas($dados)
    {
        $turmas = $dados['turmas'];

        $t = new Turma();

        $informacoesCompletas = [];
        foreach ($turmas as $turma) {
            $campos = Turma::COL_ID . ", " .
                Turma::COL_DESC_TURMA . ", " .
                Turma::COL_CURSO;

            $busca = [Turma::COL_ID => $turma];

            $retorno = ($t->listar($campos, $busca, null, 1))[0];

            if (!empty($retorno)) {
                $nomeTurma = $this->processarNome($retorno[Turma::COL_ID]);
                $cursoTurma = $this->processarCurso($retorno[Turma::COL_DESC_TURMA]);

                $informacaoCompleta = [
                    'codigo' => $turma,
                    'nome' => $nomeTurma,
                    'curso' => $cursoTurma,
                    'codigo_curso' => $retorno[Turma::COL_CURSO]
                ];

                array_push($informacoesCompletas, $informacaoCompleta);
            }
        }

        http_response_code(200);
        return json_encode($informacoesCompletas);
    }


    /**
     * Retorna as informações de uma turma e seus líderes
     *
     * @return void
     */
    public function listarTurmasLideres()
    {
        $campos = Turma::COL_ID . ", " .
            Turma::COL_DESC_TURMA . ", " .
            Turma::COL_CURSO;

        $busca = ['ano' => 'atual'];

        $t = new Turma();
        $r = new Representantes();
        $v = new ViceRepresentantes();
        $retornoTurmas = $t->listar($campos, $busca, null, null);

        $c = new Conselheiros();
        // print_r($retornoTurmas);

        $turmas = [];
        if (count($retornoTurmas) > 0 && !empty($retornoTurmas[0])) {

            foreach ($retornoTurmas as $retornoTurma) {
                $nome = $this->processarNome($retornoTurma[Turma::COL_ID]);

                $curso = $this->processarCurso($retornoTurma[Turma::COL_DESC_TURMA]);

                // Obtem o representante de turma
                $representante = $r->selecionarRepresentante(['turma' => $retornoTurma[Turma::COL_ID]]);
                $representante = json_decode($representante, true);

                // Obtem o vice-representante
                $vice = $v->selecionarViceRepresentante(['turma' => $retornoTurma[Turma::COL_ID]]);
                $vice = json_decode($vice, true);

                $representantes = [];
                if (!empty($representante[0])) {
                    $representantes[] = $representante[0];
                }

                if (!empty($vice[0])) {
                    $representantes[] = $vice[0];
                }
                // print_r($representantes);
                $turma = [
                    'codigo' => $retornoTurma[Turma::COL_ID],
                    'nome' => $nome,
                    'curso' => $curso,
                    'codigo_curso' => $retornoTurma[Turma::COL_CURSO],
                    'representantes' => $representantes
                ];

                array_push($turmas, $turma);
            }
        }

        http_response_code(200);
        return json_encode($turmas);
    }

    /**
     * Retorna os professores atuais de uma turma
     *
     * @param  mixed $dados
     * @return void
     */
    public function listarProfessoresAtuais($dados = [])
    {

        $turma = $dados['turma'];

        $p = new Professores();
        $pessoas = $p->professoresAtuaisTurma($turma);


        $professores = [];

        if (!empty($pessoas)) {
            foreach ($pessoas as $pessoa) {
                $professor = $p->selecionar($pessoa);
                array_push($professores, $professor);
            }
        }

        return json_encode($professores);
    }

    /**
     * Lista o professor conselheiro de uma turma
     *
     * @param  mixed $dados
     * @return array
     */
    public function listarTurmasConselheiros($dados = [])
    {
        // Várias paradas
        $campos = Turma::COL_ID . ", " .
            Turma::COL_DESC_TURMA . ", " .
            Turma::COL_CURSO;

        $busca = ['ano' => 'atual'];
        $t = new Turma();
        $r = new Representantes();

        $retornoTurmas = $t->listar($campos, $busca, null, null);

        $c = new Conselheiros();

        $turmas = [];

        if (count($retornoTurmas) > 0 && !empty($retornoTurmas[0])) {
            foreach ($retornoTurmas as $retornoTurma) {
                $nome = $this->processarNome($retornoTurma[Turma::COL_ID]);
                $curso = $this->processarCurso($retornoTurma[Turma::COL_DESC_TURMA]);

                $conselheiro = $c->obterConselheiro($retornoTurma[Turma::COL_ID]);
                $conselheiro = json_decode($conselheiro, true);

                $turma = [
                    'codigo' => $retornoTurma[Turma::COL_ID],
                    'nome' => $nome,
                    'curso' => $curso,
                    'codigo_curso' => $retornoTurma[Turma::COL_CURSO],
                    'conselheiro' => $conselheiro
                ];

                array_push($turmas, $turma);
            }
        }

        http_response_code(200);
        return json_encode($turmas);
    }

    /**
     * Retorna os representantes de uma turma
     *
     * @param  mixed $dados
     * @return array
     */
    public function selecionarRepresentantes($dados)
    {
        $turma_id = $dados['turma'];

        $campos = Turma::COL_ID . ", " .
            Turma::COL_DESC_TURMA . ", " .
            Turma::COL_CURSO;

        $busca = [Turma::COL_ID => $turma_id, 'ano' => 'atual'];

        $t = new Turma();
        $r = new Representantes();
        $v = new ViceRepresentantes();
        $retornoTurma = $t->listar($campos, $busca, null, null)[0];


        $turma = [];
        if (!empty($retornoTurma)) {

            $nome = $this->processarNome($retornoTurma[Turma::COL_ID]);

            $curso = $this->processarCurso($retornoTurma[Turma::COL_DESC_TURMA]);

            // Obtem o representante de turma
            $representante = $r->selecionarRepresentante(['turma' => $retornoTurma[Turma::COL_ID]]);
            $representante = json_decode($representante, true);

            // print_r($representante);

            // Obtem o vice-representante
            $vice = $v->selecionarViceRepresentante(['turma' => $retornoTurma[Turma::COL_ID]]);
            $vice = json_decode($vice, true);

            $representante_completo = [];
            if (!empty($representante)) {
                $representante_completo = $representante;
            }
            $vice_representante_completo = [];
            if (!empty($vice)) {
                $vice_representante_completo = $vice;
            }

            $turma = [
                'codigo' => $retornoTurma[Turma::COL_ID],
                'nome' => $nome,
                'curso' => $curso,
                'codigo_curso' => $retornoTurma[Turma::COL_CURSO],
                'representante' => $representante_completo,
                'vice_representante' => $vice_representante_completo
            ];
        }

        http_response_code(200);
        return json_encode($turma);
    }

    /**
     * Retorna o coeficiente de rendimento geral da turma
     *
     * @param  mixed $dados
     * @return array
     */
    public function obterCoeficienteGeral($turma_id = null)
    {

        if (!isset($turma_id)) {
            throw new \Exception("É necessário informar o id da turma");
        }

        $campos = " AVG(" . Turma::COL_COEFICIENTE_RENDIMENTO . ") COEFICIENTE_RENDIMENTO ";

        $busca = ['ano' => 'atual', Turma::COL_COEFICIENTE_RENDIMENTO => $turma_id];

        $t = new Turma();

        $coef = $t->listar($campos, $busca, null, 1)[0];

        if (!empty($coef)) {
            $coef = round($coef[Turma::COL_COEFICIENTE_RENDIMENTO], 2);
            return $coef;
        }
        return $coef;
    }

    /**
     * Retorna a classificação das notas dos estudantes
     * 
     *
     * @param  mixed $dados
     * @return void
     */
    public function obterQuantidadeConficienteGeral($dados = [])
    {
        if (!isset($dados['turma'])) {
            throw new \Exception("É necessário informar o id da turma");
        }



        $campos = Aluno::COL_COEFICIENTE_RENDIMENTO;
        $busca = [
            Aluno::COL_COD_TURMA_ATUAL => $dados['turma']
        ];

        $a = new Aluno();
        $coef_rendimento = $a->listar($campos, $busca, null, null);


        $coef = [
            'alto' => 0,
            'medio' => 0,
            'baixo' => 0
        ];



        if (count($coef_rendimento) > 0 && !empty($coef_rendimento[0])) {
            foreach ($coef_rendimento as $c) {
                $classificacao = Util::classificarCoeficiente($c[Aluno::COL_COEFICIENTE_RENDIMENTO]);
                $coef[$classificacao]++;
            }
        }

        http_response_code(200);
        return json_encode($coef);
    }


    /**
     * Retorna as estatísticas de uma turma, sendo elas: 
     * - Coeficiente Geral
     * - Quantidade de Experiências
     * - Quantidade de Ensino-Aprendizado
     * - Quantidade de Medidas Disciplinares
     * @param  mixed $dados
     * @return array
     */
    public function obterEstatistica($dados = [])
    {
        if (!isset($dados['turma'])) {
            http_response_code(400);
            return json_encode(array('message' => 'É necessário informar a turma.'));
        }

        // Obter Coeficiente Geral
        $coeficiente_geral = $this->obterCoeficienteGeral($dados['turma']);

        // Obter quantidade de ensino-aprendizado
        $a = new Aprendizados();
        $aprendizados_turma = $a->listarAprendizadosTurma($dados);
        $aprendizados_turma = json_decode($aprendizados_turma, true);
        $aprendizados_turma = count($aprendizados_turma);

        // Obter Quantidade de experiencias
        $e = new Experiencias();
        $experiencias_turma = $e->listarExperienciasTurma($dados);
        $experiencias_turma = json_decode($experiencias_turma, true);
        $experiencias_turma = count($experiencias_turma);

        // Quantidade Medias disciplinares
        $md = new MedidasDisciplinares();
        $medidas_turma = $md->listarMedidasTurma($dados['turma']);
        $medidas_turma = count($medidas_turma);

        $estatisticas = [
            'coeficiente_geral' => $coeficiente_geral,
            'aprendizados' => $aprendizados_turma,
            'experiencias' => $experiencias_turma,
            'medidas_disciplinares' => $medidas_turma
        ];

        return json_encode($estatisticas);
    }

    /**
     * Lista as medidas disciplinares de uma turma
     *
     * @param  mixed $dados
     * @return array
     */
    public function obterMedidasDisciplinares($dados)
    {
        if (!isset($dados['turma'])) {
            http_response_code(400);
            return json_encode(array('message' => 'É necessário informar a turma.'));
        }

        $md = new MedidasDisciplinares();

        $medidas = $md->listarMedidasTurma($dados['turma']);

        $medidas_completas = [];

        $a = new Alunos();


        if (count($medidas) > 0 && !empty($medidas[0])) {
            foreach ($medidas as $medida) {
                $aluno = $a->selecionar($medida['MATRICULA']);
                $m = [
                    'cod_medida' => $medida['COD_MEDIDA_DISCIPLINAR'],
                    'aluno' => $aluno,
                    'data' => $medida['DT_MEDIDA_DISCIPLINAR'],
                    'descricao' => $medida['DESC_TIPO_MEDIDA_DISCIPLINAR']
                ];
                array_push($medidas_completas, $m);
            }
        }
        http_response_code(200);
        return json_encode($medidas_completas);
    }

    public function listarTurmas($dados)
    {

        $curso_id = isset($dados['curso']) ? $dados['curso'] : null;


        $campos = Turma::COL_ID . ", " .
            Turma::COL_DESC_TURMA . ", " .
            Turma::COL_CURSO;

        $busca = ['ano' => 'atual'];

        $t = new Turma();
        $retornoTurmas = $t->listar($campos, $busca, null, null);

        $c = new Conselheiros();
        // print_r($retornoTurmas);

        $turmas = [];
        if (count($retornoTurmas) > 0 && !empty($retornoTurmas[0])) {

            foreach ($retornoTurmas as $retornoTurma) {
                $nome = $this->processarNome($retornoTurma[Turma::COL_ID]);

                $curso = $this->processarCurso($retornoTurma[Turma::COL_DESC_TURMA]);


                $turma = [
                    'codigo' => $retornoTurma[Turma::COL_ID],
                    'nome' => $nome,
                    'curso' => $curso,
                    'codigo_curso' => $retornoTurma[Turma::COL_CURSO],
                ];


                if ($curso_id == null) {
                    array_push($turmas, $turma);
                } else {
                    if ($this->verificarTurmaCurso($retornoTurma[Turma::COL_ID], $curso_id)) {
                        array_push($turmas, $turma);
                    }
                }
            }
        }

        http_response_code(200);
        return json_encode($turmas);
    }
}
