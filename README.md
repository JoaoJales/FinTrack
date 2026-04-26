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
| Banco de dados | MySQL |
| Gráficos | ApexCharts |
| Ícones | Boxicons |
| Formatação de código | Laravel Pint |
| Datas | Carbon (locale pt_BR) |

---

## 📦 Requisitos

- PHP **^8.2**
- Composer **^2.x**
- Node.js **^20.x** + NPM
- MySQL **^8.0**

---

## 🚀 Instalação

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

---

## 💻 Comandos Principais

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
