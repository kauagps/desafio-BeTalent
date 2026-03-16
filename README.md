# Multi-Gateway Payment API - BeTalent Challenge

Este projeto é uma API RESTful desenvolvida em Laravel para o gerenciamento de pagamentos multi-gateway. O sistema implementa uma lógica de contingência (fallback), onde, caso o gateway prioritário falhe, o sistema tenta automaticamente processar a transação através de um gateway secundário.

## 🚀 Nível Alcançado: Nível 2
Esta implementação atende aos requisitos dos Níveis 1 e 2, incluindo:
- Autenticação via Sanctum.
- CRUD de Clientes e Produtos.
- Pagamento multi-gateway com Fallback dinâmico.
- Gerenciamento de estoque (baixa automática e validação).
- Configuração de ambiente via Docker.

## 🛠️ Tecnologias Utilizadas
- **PHP 8.2+**
- **Laravel 10/11**
- **MySQL 8.0**
- **Docker & Docker Compose**
- **Sanctum** (Autenticação)

## 🚀 Como Rodar o Projeto (Ambiente Docker)

Este projeto foi totalmente conteinerizado utilizando **Docker** e **Laravel Sail** adaptado, garantindo que o ambiente de avaliação seja idêntico ao de desenvolvimento, sem necessidade de instalar PHP ou MySQL localmente.

Além disso, o **Mock dos Gateways** já foi incluído no arquivo `docker-compose.yml`, subindo automaticamente junto com a aplicação para facilitar os testes do **Nível 2**.

## 📋 Pré-requisitos

Certifique-se de ter instalado em sua máquina:
* [Docker Desktop](https://www.docker.com/products/docker-desktop) (ou Docker Engine)
* [Git](https://git-scm.com/)

---

## Passo a Passo da Instalação

**1. Clone o repositório e acesse a pasta:**
```bash
git clone https://github.com/kauagps/desafio-BeTalent.git
cd desafio-BeTalent
```
**2. Configure as variáveis de ambiente:**
Faça uma cópia do arquivo [.env.example](.env.example) de exemplo para criar o seu .env:

**3. Suba os containers da aplicação:**
Este comando fará o build da imagem do PHP, iniciará o banco de dados MySQL e subirá o container do Mock da BeTalent nas portas 3001 e 3002.
```bash
docker compose up -d --build
```
>Aguarde até que o terminal mostre que os serviços laravel.test, mysql e gateways-mock estão com o status "Started".

**4. Instale as dependências do Laravel:**
```bash
docker compose exec laravel.test composer install
```
**5. Gere a chave da aplicação:**
```bash
docker compose exec laravel.test php artisan key:generate
```
**6. Crie as tabelas e popule o Banco de Dados (Seeders):**
Este é o passo mais importante. Ele vai criar a estrutura do banco e popular os clientes, produtos e registrar os Gateways (Gateway A e Gateway B) com as URLs corretas da rede interna do Docker.
```bash
docker compose exec laravel.test php artisan migrate:fresh --seed
```
## Como Testar o Nível 2 (Multi-Gateways e Fallback)

A API principal estará rodando em http://localhost.

Você pode importar a collection do Postman/Insomnia utilizada durante o desafio [Collection](./Desafio Be Talent.postman_collection.json), ou ir atraves ta pasta aqui no repositorio [Collection Folder](./postman/collections/Desafio Be Talent) e testar as rotas.

JSON de usuário padrão para login:
```bash
{
    "email": "admin@teste.com",
    "password": "password"
}
```

**Notas sobre a Arquitetura do Mock:**
Para otimizar o processo de avaliação, a infraestrutura foi configurada com as seguintes decisões:
- Rede Interna: O Laravel se comunica com o Mock através da rede interna do Docker (http://gateways-mock:3001 e http://gateways-mock:3002), evitando problemas de resolução de localhost no Windows/Linux.
- Autenticação do Gateway 1: Conforme permitido nas instruções do desafio, o container do mock foi iniciado com a variável de ambiente REMOVE_AUTH=true no docker-compose.yml, simplificando o fluxo de testes.
- Teste de Fallback: O código está preparado para tentar o Gateway 1. Se houver falha (por exemplo, enviando o cvv: "100"), o sistema fará o fallback automático e processará a transação no Gateway 2.

## O que foi implementado (Nível 2)

    Arquitetura Multi-Gateway com Fallback: O sistema tenta processar o pagamento no Gateway principal. Em caso de falha (ex: simulação com CVV 100), ele automaticamente faz o fallback e tenta no segundo Gateway, garantindo que o usuário não receba erro se um deles funcionar.

    Cálculo de Carrinho no Back-end: O valor total da transação é calculado no servidor baseando-se nos produtos e quantidades enviados, mitigando riscos de manipulação de valores no front-end.

    Controle de Estoque: O sistema valida a disponibilidade do produto e subtrai a quantidade do estoque após a aprovação da compra.

    Docker Compose: Orquestração completa incluindo o container matheusprotzen/gateways-mock integrado à mesma rede da API.

    Collection do Postman: Exportada e inclusa na raiz do projeto (nome_do_arquivo.json) para facilitar os testes das rotas.

## Dificuldades Encontradas e Pendências

Gostaria de ser totalmente transparente em relação à execução deste teste: **esta foi a minha primeira vez utilizando o framework Laravel**. Todo o desenvolvimento apresentado neste repositório foi fruto de pesquisa, estudo e aprendizado construídos em tempo real, lado a lado com a execução do código.

Além da curva de aprendizado da própria ferramenta e da linguagem, me deparei com diversos termos técnicos e arquiteturas complexas (como orquestração de rede no Docker, comunicação entre APIs isoladas e contratos rígidos de JSON). Precisei pausar o desenvolvimento diversas vezes para estudar a teoria por trás dessas tecnologias, garantindo que eu não estava apenas gerando código, mas sim compreendendo o que estava sendo construído.

Devido a essa jornada de aprender a tecnologia do zero enquanto corria contra o relógio, o prazo final ficou curto. Minha decisão foi focar em entregar o "coração" do desafio funcionando com perfeição (o Checkout, a comunicação com a API e o Fallback), deixando algumas exigências como dívida técnica:

1. **Testes Automatizados (TDD):** A implementação de testes unitários e de integração com PHPUnit não pôde ser concluída a tempo. Os testes de sucesso e falha foram validados manualmente via Postman.
2. **Autenticação Dinâmica no Gateway 1:** Para focar no fluxo de fallback, o container do mock foi iniciado com a variável `REMOVE_AUTH=true`. O ideal seria implementar uma chamada prévia na rota `/login` do gateway para obter e gerenciar o Bearer Token.
3. **Rotas Privadas e Roles:** As rotas de CRUD (produtos, usuários) com validação de permissões (ADMIN, MANAGER, etc) e a rota de reembolso não foram finalizadas.
4. **Campo do Cartão:** A persistência dos últimos 4 dígitos do cartão (`card_last_numbers`) na tabela de transações acabou ficando de fora da modelagem final.

**O maior aprendizado:**
Apesar das pendências, a experiência de construir um ecossistema com Laravel, MySQL e Mocks isolados via Docker Compose foi um salto técnico imenso. Consegui estruturar a lógica de um sistema tolerante a falhas (fallback) lidando com payloads estritos, o que me deu uma visão prática incrível do desenvolvimento back-end moderno.
