# SADC
Um sistema de apoio à decisão para conselhos de classe.

![](https://img.shields.io/github/issues/AlexandreL0pes/sadc)
![](https://img.shields.io/github/forks/AlexandreL0pes/sadc)
![](https://img.shields.io/github/stars/AlexandreL0pes/sadc)
![](https://img.shields.io/github/license/AlexandreL0pes/sadc)

## Descrição 

SADC é um sistema de apoio à decisão para conselhos de classe. O objetivo principal desse projeto é possibilitar o armazenamento e consulta de dados levantados durantes as reuniões de conselho de classe, além de apresentar informações relevantes acerca do ensino-aprendizado dos estudantes.

## Pré-Requisitos
- Docker 

## Instalação/Execução

**Configurando as credenciais** 

Primeiramente, é necessário configurar a conexão com o banco de dados MySQL local, para isso crie o arquivo `config-dev.json`, adicionando credenciais válidas.
```json
{
  "database": {
    "host": "localhost",
    "user": "usuario",
    "password": "senha",
    "db": "nome_banco",
    "drive": "mysql"
  },
}
```

Depois, é necessário configurar a conexão com o banco de dados MSSQL local, para isso, dentro do arquivo `config-dev.json`, adicione as credenciais válidas.
```json
  "academico": {
    "host": "db",
    "user": "usuario",
    "password": "senha",
    "db": "nome_banco",
    "drive": "mssql"
  },

```

Por fim, defina as váriaveis responsáveis pela encriptação das tokens de acesso geradas pelo sistemas, ainda dentro do arquivo `config-dev.json`, adicione os dados.

```json
  "jwt": {
    "key": "sua_chave_super_secreta",
    "alg": "seu_algoritmo_de_encriptação",
  }
```

### Configurando o Docker 🐳

#### Iniciando os containers

```bash
$ docker-compose up -d
```

#### Após a execução dos comandos, o servidor estará disponível em [localhost:80/](http://localhost:80/)

## Utilização 
O serviço ainda não está disponível! :/

## Licença

[MIT](https://opensource.org/licenses/MIT)

