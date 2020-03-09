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

## Utilização 
O serviço ainda não está disponível! :/

## Instalação/Execução

**Configurando as credenciais** 

Caso seja necessário, para configurar a conexão com o banco de dados, é preciso criar o arquivo `config-dev.json`.
```json
{
  "database": {
    "host": "localhost",
    "user": "usuario",
    "password": "senha",
    "db": "nome_banco",
    "drive": "mysql"
  }
}
```
**Configurando o Docker** 🐳

### Iniciando os containers
```bash
$ docker-compose up -d
```

#### Após a execução dos comandos, o servidor estará disponível em [localhost:80/](http://localhost:80/)

## Licença

[MIT](https://opensource.org/licenses/MIT)

