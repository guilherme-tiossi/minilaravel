# MiniLaravel

## Português

Projeto de estudo em PHP puro, com o objetivo de entender e implementar, do zero, os principais componentes de um framework moderno inspirado no Laravel para aprender mais profundamente como ele funciona por dentro: ciclo de requisição, container de dependências, middlewares, filas, eventos, acesso a dados, etc.
Tudo é construído manualmente priorizando clean code, separação de responsabilidades e aprendizado.

### Principais conceitos implementados

- **Router próprio**, baseado em objetos de rota
- **Controllers organizados**
- **Sistema de Middlewares**
  - Chain of Responsibility
  - Middlewares globais
  - CORS
  - Rate Limiter
- **Container de Inversão de Dependência (DI)**
  - Resolução automática de dependências
  - Service Providers
- **Camada de acesso a dados**
  - DAOs (Data Access Objects)
  - Conexão via PDO
  - Suporte a transações
- **Sistema de filas**
  - Workers múltiplos
  - Controle de retries
  - Processamento concorrente básico
- **Eventos e Listeners**
- **Validação de dados de requisição**
- **CLI própria** (`miniartisan`)
  - Comandos como `run`, `routes`, `help`
- **Servidor embutido**
  - Rodando por padrão na porta `8080`
- **Estrutura inspirada em frameworks como Laravel**, mas com implementação própria

### Objetivo do projeto

- Entender, na prática, como frameworks PHP modernos são estruturados
- Aprimorar domínio de PHP puro
- Explorar conceitos como:
  - Inversão de dependência
  - Filas e concorrência
  - Arquitetura orientada a eventos
  - Pipeline de middlewares
  - Separação clara entre camadas

### Como iniciar

Basta clonar o projeto e executar:

```bash
docker-compose up -d
```
O projeto ficará disponível em: `http://localhost:8080`

---

## English

Study project written in pure PHP, focused on understanding and implementing the core building blocks of a modern web framework inspired by Laravel to more deeply understand how they work internally: request lifecycle, dependency injection, middleware pipelines, queues, events, and data access layers.
Everything is built from scratch with a strong focus on clean structure and learning.

### Main implemented concepts

- **Custom router**, based on route objects
- **Organized controller layer**
- **Middleware system**
  - Chain of Responsibility
  - Global middlewares
  - CORS handling
  - Rate limiter
- **Dependency Injection Container**
  - Automatic dependency resolution
  - Service Providers
- **Data access layer**
  - DAOs (Data Access Objects)
  - PDO database connection
  - Transaction support
- **Queue system**
  - Multiple intelligent workers
  - Retry handling
  - Basic concurrent processing
- **Events and listeners**
- **Request data validation**
- **Custom CLI** (`miniartisan`)
  - Commands like `run`, `routes`, `help`
- **Built-in server**
  - Running on port `8080`
- **Structure inspired by Laravel**, with fully custom implementations

### Project purpose

- Learn how modern PHP frameworks are internally built
- Improve technical abilities in pure PHP
- Practice architectural concepts such as:
  - Dependency Inversion
  - Queues and workers
  - Event-driven architecture
  - Middleware pipelines
  - Clear separation of concerns

### How to run

Clone the project and run:

```bash
docker-compose up -d
```
The project will be available at: `http://localhost:8080`