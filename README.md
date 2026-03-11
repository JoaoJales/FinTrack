# 💰 FinTrack

**FinTrack** é um sistema de controle financeiro pessoal moderno e intuitivo, desenvolvido para ajudar os usuários a gerenciar suas contas, categorizar seus gastos e ter uma visão clara da sua saúde financeira.

---

## 🚀 Funcionalidades (MVP)

* **Gestão de Contas (Accounts):** Controle de múltiplas contas (Corrente, Poupança, Investimentos, Carteira) vinculadas a instituições financeiras.
* **Instituições Financeiras (Institutions):** Cadastro de bancos e fintechs.
* **Categorização Inteligente (Categories):** * Categorias de Sistema (Globais): Categorias padrão (ex: Alimentação, Moradia) disponíveis para todos os usuários logo no primeiro acesso.
    * Categorias Personalizadas: Cada usuário pode criar suas próprias Categorias pessoais.
* **Transações (Transactions):** Registro detalhado de Receitas (`income`) e Despesas (`expense`).



---

## 🛠️ Tecnologias Utilizadas

* **Linguagem:** PHP 8.2+
* **Framework:** Laravel 11.x 
* **Banco de Dados:** PostgreSQL

---

## ⚙️ Modelagem do Banco de Dados

O sistema é composto pelas seguintes entidades principais:

1. **Users:** Autenticação e posse dos dados.
2. **Institutions:** Catálogo de bancos (com código BACEN, logo e cor).
3. **Accounts:** As carteiras do usuário, contendo o saldo inicial e o tipo da conta.
4. **Categories:** Estrutura híbrida onde `user_id = null` representa uma categoria global do sistema, protegida contra exclusão/edição.
5. **Transactions:** O livro-caixa, conectando a Conta, a Categoria e o Usuário, registrando o valor, tipo e a data exata da movimentação.

---

## 📦 Como rodar o projeto localmente

### Pré-requisitos
* PHP >= 8.2
* Composer
* PostgreSQL rodando localmente (ou via Docker)

### Passo a Passo

1. **Clone o repositório**
```bash
git clone [https://github.com/seu-usuario/fintrack.git](https://github.com/seu-usuario/fintrack.git)
cd fintrack
```

2. **Instale as dependências do PHP**
```bash
composer install
```


3. **Configure as Variáveis de Ambiente**
   Copie o arquivo de exemplo e configure sua conexão com o PostgreSQL:
```bash
cp .env.example .env
```


*Abra o `.env` e ajuste as credenciais de `DB_CONNECTION=pgsql`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME` e `DB_PASSWORD`.*
4. **Gere a chave da aplicação**
```bash
php artisan key:generate
```


5. **Rode as Migrations e os Seeders**
   Isso criará a estrutura do banco e populará as Instituições e Categorias globais:
```bash
php artisan migrate --seed
```


6. **Inicie o servidor de desenvolvimento**
```bash
php artisan serve
```


Acesse a aplicação em `http://localhost:8000`.

---


