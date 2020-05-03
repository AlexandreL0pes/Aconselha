<?php

namespace core;

use PDO;
use PDOException;

class CRUD {

    private $dados_conexao = [
        "driver" => "mysql",
        "host" => null,
        "user" => null,
        "password" => null,
        "db" => null
    ];

    /**
     * Conexão com a PDO
     *
     * @var null
     */
    protected $conexao = null;

    /**
     * Statement
     *
     * @var null
     */
    protected $stmt = null;

    /**
     * Armazena a última consulta realizada para depuração
     *
     * @var string
     */
    private $ultimo_sql = "";

    /**
     * CRUD constructor.
     */
    public function __construct() {
        $this->openDB();
        $this->closeDB();
    }

    /**
     * Abre a conexão com o Banco de Dados
     *
     * @param bool $autocommit
     */
    protected function openDB($database = null, $autocommit = false ) {
        $dados_manifest = file_get_contents(ROOT . 'config-dev.json');
        
        $database = $database == null ? 'default' : $database;

        // Se passar o banco como argumento usa o mssql, senão mysql
        $dados = json_decode($dados_manifest)->$database;

        if ($dados !== null) {

            $this->dados_conexao['host'] = $dados->host;
            $this->dados_conexao['user'] = $dados->user;
            $this->dados_conexao['password'] = $dados->password;
            $this->dados_conexao['db'] = $dados->db;
            $this->dados_conexao['drive'] = $dados->drive;

            try {

                //Define utf8 como a formatação padrão de caracteres
                // $opcoes = array(
                //     PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
                //     PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                // );

                //Habilita o auto-commit
                if (!$autocommit) {
                    $opcoes[PDO::ATTR_PERSISTENT] = true;
                }

                if ($this->dados_conexao['drive'] == "mysql") {
                    $con = "mysql:host={$this->dados_conexao['host']};dbname={$this->dados_conexao['db']}";
                    $opcoes = array(
                        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                    );
                }else{
                    $con = "sqlsrv:Server={$this->dados_conexao['host']};Database={$this->dados_conexao['db']}";
                    $opcoes = null;
                }
                // echo $con;
                $this->conexao = new PDO(
                    $con,
                    $this->dados_conexao['user'],
                    $this->dados_conexao['password']
                    ,$opcoes
                );

            } catch (PDOException $e) {
                echo "Erro ao iniciar conexão: " . $e->getMessage() . "\n" . $e->getTraceAsString();
            }

        } else {
            echo "Dados para conexão inválidos";
        }
    }

    /**
     * Fecha a conexão com o Banco de Dados
     */
    protected function closeDB() {
        if ($this->conexao === null)
            throw new PDOException("Não há conexão aberta para fechar");

        $this->conexao = null;
    }

    protected function pegarUltimoSQL() {
        return $this->ultimo_sql;
    }

    /**
     * Efetua a inserção no Banco de dados
     *
     * @param $tabela - Nome da tabela
     * @param array $dados - Array com o indice da cada valor com o mesmo nome da coluna do Banco de Dados
     * @return bool
     */
    protected function create($tabela, array $dados) {
        if (count($dados) == 0)
            throw new PDOException("Não há dados para serem inseridos");

        $this->openDB();

        // Variáveis auxiliares
        $campos = [];
        $valores = [];
        $parametros = [];

        foreach ($dados as $i => $v) {
            $campos[] = $i;
            $parametros[] = "?";
            $valores[] = $v;
        }

        // Prepara o valores para montar a consulta
        $campos = implode(', ', $campos);
        $parametros = "(" . implode(', ', $parametros) . ")";

        // Monta a consulta SQL
        $sql = "INSERT INTO " . $tabela . " (" . $campos . ") VALUES " . $parametros;

        //Armazena a consulta SQL para verificação
        $this->ultimo_sql = $sql;

        $this->stmt = $this->conexao->prepare($sql);

        for ($i = 1; $i <= count($valores); $i++) {
            $this->stmt->bindValue($i, $valores[$i - 1]);
        }

        $resultado = $this->stmt->execute();

        if ($this->stmt->rowCount() > 0) {
            $id = $this->conexao->lastInsertId();

            $this->closeDB();

            // Se o campo for auto incremento, retorna o valor do id, senão retorna true para confirmar a inserção
            if ($id > 0) return $id;
            else return true;
        } else
            return $resultado;
    }

    /**
     * Efetua a atualização no Banco de Dados
     *
     * @param $tabela - Nome da tabela onde a alteração será realizada
     * @param array $dados - Array com o indice da cada valor com o mesmo nome da coluna do Banco de Dados
     * @param string $where_condicao - Condição a ser verificada durante a atualização. Ex.: "campo = ?"
     * @param array $where_valor - Array com os valores que serão inseridos pelo método bindValue()
     * @return mixed
     */
    protected function update($tabela, array $dados, $where_condicao = "", array $where_valor = []) {
        if (count($dados) == 0)
            throw new PDOException("Não há dados para serem atualizados");

        $this->openDB();

        $campos = [];
        foreach ($dados as $i => $v) {
            $campos[] = $i . " = ?";
        }

        $campos = implode(', ', $campos);

        if ($where_condicao != "") {
            $where_condicao = " WHERE " . $where_condicao;
        }

        // Prepara a consulta SQL
        $sql = "UPDATE " . $tabela . " SET " . $campos . " " . $where_condicao;

        //Armazena a consulta SQL para verificação
        $this->ultimo_sql = $sql;

        $this->stmt = $this->conexao->prepare($sql);

        $i = 1;
        foreach ($dados as $valor) {
            $this->stmt->bindValue($i, $valor);
            $i++;
        }

        for ($val = 0; $val < count($where_valor); $val++) {
            $this->stmt->bindValue($i, $where_valor[$val]);
            $i++;
        }

        $resultado = $this->stmt->execute();

        $this->closeDB();

        return $resultado;
    }

    /**
     * Apaga a informação no Banco de Dados
     *
     * @param $tabela - Nome da tabela que será usada na ação
     * @param string $where_condicao - Condição a ser verificada antes de apagar o dado. Ex.: "campo = ?"
     * @param array $where_valor - Array com os valores que serão inseridos pelo método bindValue()
     * @return mixed
     */
    protected function delete($tabela, $where_condicao = "", $where_valor = []) {

        $this->openDB();

        if ($where_condicao != "") {
            $where_condicao = " WHERE " . $where_condicao;
        }

        // Prepara a consulta SQL
        $sql = "DELETE FROM " . $tabela . $where_condicao;

        $this->ultimo_sql = $sql;

        //Armazena a consulta SQL para verificação
        $this->stmt = $this->conexao->prepare($sql);

        for ($i = 1; $i <= count($where_valor); $i++) {
            $this->stmt->bindValue($i, $where_valor[$i - 1]);
        }

        $resultado = $this->stmt->execute();

        $this->closeDB();

        return $resultado;
    }

    /**
     * Efetua consultas no Banco de Dados
     *
     * @param null $database - Nome da base para a consulta
     * @param $tabela - Nome da tabela com ou sem JOIN
     * @param null $campos - Quais os campos que devem ser retornados pelo método, separados por ','
     * @param null $where_condicao - Condição a ser verificada antes de selecionar os dados. Ex.: "campo = ?"
     * @param array $where_valor - Array com os valores que serão inseridos pelo método bindValue()
     * @param null $group_by - Nome do campo para realizar o agrupamento dos dados
     * @param null $ordem - O nome dos campos e a ordenação de cada um deles. Ex.: "campo1 ASC, campo2 DESC"
     * @param null $limite - Valor que restringe a quantidade de dados que será retornada pela consulta
     * @return array
     */
    protected function read(
        $database,
        $tabela,
        $campos = null,
        $where_condicao = null,
        $where_valor = [],
        $group_by = null,
        $ordem = null,
        $limite = null) {

        $campos = $campos == null ? "*" : $campos;
            
        $this->openDB($database);

        // Prepara a consulta SQL
        $sql = "SELECT " . $campos . " FROM " . $tabela . " ";
        $sql .= $where_condicao != null ? "WHERE " . $where_condicao . " " : "";
        $sql .= $group_by != null ? "GROUP BY " . $group_by . " " : "";
        $sql .= $ordem != null ? "ORDER BY " . $ordem . " " : "";
        $sql .= $limite != null ? "LIMIT " . $limite . " " : "";

        //Armazena a consulta SQL para verificação
        $this->ultimo_sql = $sql;

        $this->stmt = $this->conexao->prepare($sql);

        for ($i = 1; $i <= count($where_valor); $i++) {
            $this->stmt->bindValue($i, $where_valor[$i - 1]);
        }

        $this->stmt->execute();

        $resultado = $this->stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($resultado) > 0 && isset($resultado[0])) {
            $lista = $resultado;
        } else {
            $lista = [$resultado];
        }

        $this->closeDB();

        return $lista;
    }

    protected function readInner(
        $tabela,
        $campos = null,
        $innerjoin = [],
        $innerjoin2 = [],
        $where_condicao = null,
        $where_valor = [],
        $ordem = null,
        $group_by = null,
        $limite = null
    ) {

        $campos = $campos == null ? "*" : $campos;

        $this->openDB();

        // Prepara a consulta SQL
        $sql = "SELECT " . $campos . " FROM " . $tabela . " ";
        $sql .= $innerjoin > 0 ? "INNER JOIN " . $innerjoin[0] . " ON " . $innerjoin[1] . " = " . $innerjoin[2] . " " : "";
        $sql .= $innerjoin2 > 0 ? "INNER JOIN " . $innerjoin2[0] . " ON " . $innerjoin2[1] . " = " . $innerjoin2[2] . " " : "";
        $sql .= $where_condicao != null ? "WHERE " . $where_condicao . " " : "";
        $sql .= $group_by != null ? "GROUP BY " . $group_by . " " : "";
        $sql .= $ordem != null ? "ORDER BY " . $ordem . " " : "";
        $sql .= $limite != null ? "LIMIT " . $limite . " " : "";

        //Armazena a consulta SQL para verificação
        $this->ultimo_sql = $sql;

        $this->stmt = $this->conexao->prepare($sql);

        for ($i = 1; $i <= count($where_valor); $i++) {
            $this->stmt->bindValue($i, $where_valor[$i - 1]);
        }

        $this->stmt->execute();

        $resultado = $this->stmt->fetchAll(PDO::FETCH_OBJ);

        if (count($resultado) > 0 && isset($resultado[0])) {
            $lista = $resultado;
        } else {
            $lista = [$resultado];
        }

        $this->closeDB();

        return $lista;
    }
}
