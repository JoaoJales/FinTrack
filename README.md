# 💸 FinTrack

> Aplicação web de **gestão de finanças pessoais** construída com Laravel, Alpine.js e Tailwind CSS. Permite controlar receitas, despesas, contas bancárias e categorias com dashboards visuais e filtros avançados.

---

## 📌 Sobre o Projeto

O **FinTrack** é uma aplicação de finanças pessoais que permite ao usuário:

- Registrar e categorizar transações financeiras (entradas e saídas)
- Gerenciar múltiplas contas bancárias com saldo consolidado
- Criar categorias personalizadas com ícone e cor
- Visualizar o desempenho financeiro mensal via gráficos interativos
- Filtrar o extrato por período, tipo, conta, categoria e valor

---

## ✅ Funcionalidades

### Dashboard
- Saldo total consolidado de todas as contas
- Variação percentual do resultado em relação ao mês anterior
- Últimas 5 transações
- Gráfico de **gastos e ganhos por categoria**
- Gráfico de **evolução mensal**
- Card de desempenho do mês atual (resultado líquido, total de entradas e saídas)
- Lista de contas bancárias com saldo individual
- Insight automático da maior despesa do mês

### Transações
- Cadastro de transações com valor, data, descrição, conta e categoria
- Edição via modal com preenchimento automático dos campos
- Exclusão com diálogo de confirmação
- Filtros avançados:
    - Busca por descrição
    - Mês
    - Período livre (data início / data fim)
    - Tipo (entrada ou saída)
    - Conta bancária
    - Categoria
    - Valor mínimo e máximo
- Paginação com manutenção dos filtros ativos (`withQueryString`)

### Categorias
- Criação de categorias personalizadas com **nome**, **tipo**, **ícone** e **cor**
- Pré-visualização em tempo real ao criar/editar
- Filtro por tipo via tabs (Gasto / Ganho)
- Edição em modal dedicada por categoria
- Exclusão com diálogo de confirmação

### Contas Bancárias
- Cadastro de contas vinculadas a instituições financeiras
- Logo da instituição exibida nas transações e no dashboard
- Definição de conta padrão para novas transações

---

## 🛠 Stack Tecnológica

| Camada | Tecnologia |
|---|---|
| Backend | Laravel 11 |
| Frontend | Blade + Alpine.js + Tailwind CSS |
| Banco de dados | PostgreSQL |
| Gráficos | ApexCharts |
| Ícones | Boxicons |
| Formatação de código | Laravel Pint |
| Datas | Carbon (locale pt_BR) |

### Banco de dados (PostgreSQL obrigatório)

O projeto assume **PostgreSQL** em todos os ambientes (app, Docker e testes). O dashboard e a página de saldos usam funções SQL específicas do PostgreSQL (por exemplo `TO_CHAR` e `EXTRACT`). **SQLite e MySQL não são suportados** para rodar a aplicação nem a suíte de testes automatizados.

---

## 📦 Requisitos

- PHP **^8.2**
- Composer **^2.x**
- Node.js **^20.x** + NPM
- PostgreSQL **^15**
- Docker

---

## 🚀 Instalação

### Com Docker (recomendado)

O repositório inclui um `Makefile` com atalhos que sobem a stack e configuram a aplicação dentro dos containers.

```bash
git clone https://github.com/seu-usuario/fintrack.git
cd fintrack

make setup
```

Ao final, a aplicação fica em **http://localhost:8080**. Na primeira execução, o `setup` copia `.env.docker.example` para `.env.docker` (se ainda não existir), sincroniza com `.env`, sobe os serviços, instala dependências, gera a chave, roda `migrate:fresh --seed` e compila os assets.

### Instalação local (sem Docker)

```bash
# 1. Clonar o repositório
git clone https://github.com/seu-usuario/fintrack.git
cd fintrack

# 2. Instalar dependências PHP
composer install

# 3. Instalar dependências JS
npm install

# 4. Copiar o arquivo de ambiente
cp .env.example .env

# 5. Gerar a chave da aplicação
php artisan key:generate

# 6. Configurar o banco de dados no .env e rodar as migrations
php artisan migrate --seed

# 7. Compilar os assets
npm run dev
```

Se o PostgreSQL do `docker compose` estiver publicado na porta **5433** no host (padrão deste repositório), ao rodar testes na máquina host defina `DB_PORT=5433` (variável de ambiente) ou ajuste o [`phpunit.xml`](phpunit.xml) temporariamente.

---

## Testes automatizados

A suíte usa **PostgreSQL**, com credenciais e banco definidos no [`phpunit.xml`](phpunit.xml) (banco `fintrack_testing`, usuário `fintrack`, senha `secret`).

### Com Docker (`make test`)

Com a stack no ar (`make up` ou após `make setup`), na raiz do projeto:

```bash
make test
```

O alvo aguarda o Postgres ficar pronto, cria o banco `fintrack_testing` se ainda não existir e roda `php artisan test` **dentro** do container `fintrack_app`, com `DB_HOST=postgres` e `DB_PORT=5432` (rede do Compose), alinhado ao [`phpunit.xml`](phpunit.xml).

### Sem Docker (PHP no host)

É necessária a extensão **`pdo_pgsql`**. O [`phpunit.xml`](phpunit.xml) usa host `127.0.0.1` e porta `5432` (se o Postgres do Compose estiver só na **5433** no host, use `DB_PORT=5433 php artisan test`).

Crie o banco de testes uma vez, se ainda não existir:

```sql
CREATE DATABASE fintrack_testing OWNER fintrack;
```

Depois:

```bash
php artisan test
```

No repositório, o workflow [`.github/workflows/tests.yml`](.github/workflows/tests.yml) sobe um serviço PostgreSQL e executa `composer install`, migrações e `php artisan test` em cada push/PR.

---

## 🔧 Comandos Make (Docker)

Todos os alvos abaixo assumem **Docker Compose** ativo; a app roda no container `fintrack_app`.

### Ambiente

| Comando | Descrição |
|---|---|
| `make up` | Sobe todos os containers em segundo plano |
| `make down` | Para e remove os containers da stack |
| `make build` | Reconstrói a imagem do serviço `app` |
| `make restart` | Reinicia os containers |
| `make logs` | Acompanha os logs (`docker compose logs -f`) |
| `make ps` | Lista os containers em execução |

### Aplicação

| Comando | Descrição |
|---|---|
| `make setup` | Primeira configuração: `.env.docker`, sobe stack, `composer install`, `npm install`, `npm run build`, `key:generate`, `migrate:fresh --seed` |
| `make bash` | Abre um shell interativo dentro do container da app |
| `make artisan CMD="…"` | Executa `php artisan` no container. Ex.: `make artisan CMD="route:list"` |
| `make migrate` | Roda `php artisan migrate` |
| `make fresh` | `migrate:fresh --seed` |
| `make seed` | `php artisan db:seed` |
| `make test-data` | Roda o seeder `TestDataSeeder` |
| `make test` | Roda `php artisan test` no container (Postgres do compose, banco `fintrack_testing`) |
| `make tinker` | Abre o Tinker |

### Assets

| Comando | Descrição |
|---|---|
| `make npm-dev` | `npm run dev` (watch) dentro do container |
| `make npm-build` | `npm run build` (produção) dentro do container |

### Qualidade de código

| Comando | Descrição |
|---|---|
| `make pint` | Formata com Laravel Pint |
| `make pint-test` | Verifica formatação sem alterar arquivos |

### Banco de dados

| Comando | Descrição |
|---|---|
| `make psql` | Abre o cliente `psql` no container PostgreSQL |

---

## 💻 Comandos Principais

Comandos executados **na máquina host** (instalação local). Se você usa **Docker**, os equivalentes estão na seção [Comandos Make (Docker)](#comandos-make-docker).

### Desenvolvimento

| Comando | Descrição |
|---|---|
| `php artisan serve` | Inicia o servidor de desenvolvimento |
| `npm run dev` | Compila assets em modo watch (Vite) |
| `npm run build` | Compila assets para produção |

### Banco de Dados

| Comando | Descrição |
|---|---|
| `php artisan migrate` | Roda todas as migrations pendentes |
| `php artisan migrate:fresh` | Recria todas as tabelas do zero |
| `php artisan migrate:fresh --seed` | Recria as tabelas e popula com seeders |
| `php artisan migrate:rollback` | Reverte a última migration |
| `php artisan db:seed` | Roda todos os seeders |
| `php artisan db:seed --class=NomeSeeder` | Roda um seeder específico |

### Qualidade de Código

| Comando | Descrição |
|---|---|
| `./vendor/bin/pint` | Formata todo o código seguindo PSR-12 |
| `./vendor/bin/pint --test` | Verifica formatação sem alterar arquivos |
| `./vendor/bin/pint app/Models` | Formata apenas um diretório específico |
| `./vendor/bin/pint app/Http/Controllers/TransactionController.php` | Formata um arquivo específico |


---

## 🎨 Padrões de Código

### Controllers
Controllers são enxutos — apenas orquestram o request, delegam para services e retornam views. Sem lógica de negócio direta.

### Services
Toda lógica de negócio e queries complexas ficam nos services. Controllers injetam os services via construtor.

### Alpine.js (`x-data`)
- Estado de formulário (`formMethod`, `formAction`) sempre no componente pai (page)
- Estado de UI local (cores, ícones, pré-visualização) no componente filho (modal)

### Blade
- Componentes reutilizáveis em `resources/views/components/`

### Formatação
O projeto usa **Laravel Pint** com preset PSR-12. Rodar antes de todo commit:

```bash
./vendor/bin/pint
```
