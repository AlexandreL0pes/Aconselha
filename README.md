# Aconselha
![](https://img.shields.io/github/issues/AlexandreL0pes/sadc)
![](https://img.shields.io/github/forks/AlexandreL0pes/sadc)
![](https://img.shields.io/github/stars/AlexandreL0pes/sadc)
![](https://img.shields.io/github/license/AlexandreL0pes/sadc)

O Aconselha oferece aos usuários automatização na gerência de conselhos de classe, integrando todos os indivíduos da reunião, sendo eles docentes, representantes de sala, coordenações e equipe pedagógica. 

![Aconselha - Tela Inicial](docs/ACONSELHA.jpg)


## Descrição 

Aconselha é um sistema de apoio à decisão para conselhos de classe. O objetivo principal desse projeto é possibilitar o armazenamento e consulta de dados levantados durantes as reuniões de conselho de classe, além de apresentar informações relevantes acerca do ensino-aprendizado dos estudantes.

## Pré-Requisitos
- Docker 

## Instalação/Execução

**Configurando as credenciais** 

Primeiramente, é necessário configurar a conexão com o banco de dados MySQL local, para isso crie o arquivo `config-dev.json`, adicionando credenciais válidas.
```json
  "database": {
    "host": "localhost",
    "user": "usuario",
    "password": "senha",
    "db": "nome_banco",
    "drive": "mysql"
  },
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
    "secret_key": "sua_chave_super_secreta",
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

## Tecnologias utilizadas
- PHP 7.2 - Linguagem do Back-End
- MySQL 5.7 - Base de Dados Principal
- SQL Server 2017 - Base de dados Acadêmicos (Legada)
- Bulma - Framework CSS
- [Vanilla JS](http://vanilla-js.com/) - Framework Javascript 😂

## Autores
[NEPeTI - Núcleo de Estudos e Pesquisa em Tecnologia da Informação](https://informatica.ifgoiano.edu.br/index.php/nepeti)



