# <img src="https://avatars1.githubusercontent.com/u/7063040?v=4&s=200.jpg" alt="HU" width="24" /> Desafio Bravo

## Indíce

-   [Sobre](#about)
-   [Requisitos](#requirements)
-   [Setup](#setup)
-   [Funcionamento](#work)
    -   [Converter Moeda](#work-currency-convert)
    -   [Login](#work-login)
    -   [Adicionar Moeda](#work-currency-add)
    -   [Remover Moeda](#work-currency-remove)
-   [Comandos do Composer](#composer-commands)
-   [Estrutura do Projeto](#filesystem)
-   [Endpoints](#endpoints)
-   [Teste de Estresse](#stress-test)
-   [Informações Adicionais](#details)

<a name="about"></a>

## Sobre

Construa uma API, que responda JSON, para conversão monetária. Ela deve ter uma moeda de lastro (USD) e fazer conversões entre diferentes moedas com cotações de verdade e atuais.

A API deve, originalmente, converter entre as seguintes moedas:

-   USD
-   BRL
-   EUR
-   BTC
-   ETH

<a name="requirements"></a>

## Requisitos

-   PHP >= 7.3
-   OpenSSL PHP Extension
-   PDO PHP Extension
-   php-curl extension
-   php-sqlite extension
-   Mbstring PHP Extension
-   [composer](https://getcomposer.org/doc/00-intro.md)
-   [docker-compose](https://docs.docker.com/compose/install) (caso use container)

<a name="setup"></a>

## Setup

Instale as dependências:

```sh
composer install
```

Configure o arquivo .env: \*

```sh
nano .env
```

<sub>\*Se o arquivo não for criado automaticamente depois do composer install, faça uma cópia do .env.example.</sub>

Rode as migrations e seeds:

```sh
composer migrate --seed
```

Inicie a aplicação:

```sh
composer start
```

A aplicação estará disponível no endereço: http://localhost:8000.

<a name="work"></a>

## Funcionamento

<a name="work-currency-convert"></a>

### Converter Moeda

Requisição para realizar a conversão entre 2 moedas:

-   **from**: Moeda de origem.
-   **to**: Moeda para qual o valor será convertido.
-   **amount**: Valor da moeda de origem que será convertido.

```sh
curl 'http://localhost:8000/currencies?from=BTC&to=EUR&amount=123.45'
```

<a name="work-login"></a>

### Login

Requisição para obter um token de acesso que permite usar as rotas para adicionar e remover moedas:

-   **email**: usuário cadastrado no banco (padrão do .env.example é: **admin@hurbchallenge.com**).
-   **password**: senha do usuário (padrão do .env.example é: **secret123**).

<sub>OBS.: Se alguma dessas informações for mudada no .env, será necessário mudar na requisição também.</sub>

```sh
curl 'http://localhost:8000/login' -d 'email=admin@hurbchallenge.com&password=secret123'
```

<a name="work-currency-add"></a>

### Adicionar Moeda

Requisição para adicionar moeda:

-   **currency**: Moeda a ser adicionada.
-   **usd_value**: Valor da moeda equivalente a 1 dólar americano (USD).
-   **{token}**: Token gerado no endpoint /login.

```sh
curl 'http://localhost:8000/currencies' -X 'POST' -d 'currency=CAD&usd_value=1.26' -H 'Authorization: Bearer {token}'
```

<a name="work-currency-remove"></a>

### Remover Moeda

Requisição para remover moeda:

-   **{currency}**: Código da moeda a ser removida.
-   **{token}**: Token gerado no endpoint /login.

```sh
curl 'http://localhost:8000/currencies/{currency}' -X 'DELETE' -H 'Authorization: Bearer {token}'
```

<a name="composer-commands"></a>

## Comandos do Composer

Rodar testes:

```sh
composer tests
```

Iniciar projeto utilizando php-cli:

```sh
composer start
```

Configurar/Limpar banco:

```sh
composer migrate:fresh --seed
```

Iniciar container:

```sh
composer docker
```

<a name="filesystem"></a>

## Estrutura do Projeto

Os arquivos mencionados foram os que eu criei ou manipulei no framework:

```
├── app - Contém o código-fonte principal do projeto.
│   ├── Http - Scripts para criar as tabelas do banco de dados.
│   ├──── Controllers - Controladores do sistema.
│   ├────── AuthController - Controlador de autenticação.
│   ├────── CurrencyController - Controlador de cotações.
│   │
│   ├──── Models - Modelos do sistema.
│   ├────── User - Modelo de autenticação.
│   ├────── Currency - Modelo de cotações.
│   │
│   ├──── Repositories - Repositórios do sistema.
│   ├────── AuthRepository - Repositório de autenticação.
│   ├────── CurrencyRepository - Repositório de cotações.
│   │
│   ├──── Services - Classes de serviços para auxiliar na lógica extra.
│   └────── CurrencyService - Validações na logica de cotações.
│
├── bootstrap - A instância $app principal do projeto.
│   └── app - Configurações do projeto.
│
├── database - Arquivos do banco de dados.
│   ├── factories - Factories para incluir dados ao realizar testes.
│   ├── migrations - Migrations para criação de tabelas no banco.
│   └── seeders - Seeders para popular o banco na instação do projeto.
│
├── routes - Arquivos de rota.
│   └── web - Rotas da API.
│
├── docker-compose - Arquivos de configuração do container.
│   └── apache2 - Arquivos de configuração do apache2.
|
├── tests - Testes do sistema.
│   ├── Feature - Testes dos endpoints da API.
│
```

<a name="endpoints"></a>

## Endpoints

```
POST /login
GET /currencies
POST /currencies
DELETE /currencies/{currency}
```

<a name="stress-test"></a>

## Teste de Estresse

<img src="./stress-test.jpg" alt="stress test" />

<a name="details"></a>

## Informações adicionais

#### O sistema de autenticação está usando JWT (Json Web Token), os tokens são gerados na rota /login.

---
