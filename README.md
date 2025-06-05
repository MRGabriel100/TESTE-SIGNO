# TESTE-SIGNO

Script para Criação de Enquetes

COMO UTILIZAR:

Primeiro altere os seguintes campos no arquivo .env:

DB_HOST=#IP DO HOST
DB_PORT=#PORTA
DB_DATABASE=#NOME DO BANCO DE DADOS
DB_USERNAME=# NOME DE USUÁRIO
DB_PASSWORD=#SENHA DO BANCO

Depois inicie o MySQL

Abra o terminal e digite o comando:

php artisan migrate

isso irá gerar a tabela enquetes, após isso digite o comando:

php artisan serve - para iniciar o laravel