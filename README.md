# Sistema para Conselhos de Classe

## Configuração inicial
Para configurar a conexão com o banco de dados, é necessário criar o arquivo `config-dev.json`.
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

Em seguida é necessária a instalação das dependências do arquivo `composer.json`.

Para isso o composer deve estar instalado e configurado na máquina. O mesmo pode ser obtido no link abaixo.

> [Composer](https://getcomposer.org/)
