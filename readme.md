# Azhoras — Sistema de Banco de Horas

Sistema web de gerenciamento de banco de horas desenvolvido em **PHP puro** seguindo os princípios **SOLID**, com autenticação, controle de permissões, lançamento de horas e notificações por e-mail.

---

## Tecnologias

- PHP 8.x (puro, sem framework)
- MySQL
- Composer (PSR-4 autoload)
- PHPMailer (envio de e-mails)
- vlucas/phpdotenv (variáveis de ambiente)
- league/oauth2-client + thenetworg/oauth2-azure (autenticação Microsoft 365)

---

## Estrutura do Projeto

```
azhoras/
├── Config/
│   ├── helpers.php          # Funções auxiliares (createRoute, route)
│   ├── named_routes.php     # Rotas nomeadas
│   └── routes.php           # Definição de todas as rotas
├── Public/
│   └── index.php            # Entry point da aplicação
├── src/
│   ├── Auth/
│   │   ├── Controllers/
│   │   │   ├── LoginController.php
│   │   │   └── RegisterController.php
│   │   ├── Interfaces/
│   │   │   └── UserRepositoryInterface.php
│   │   ├── Repositories/
│   │   │   └── UserRepository.php
│   │   ├── Services/
│   │   │   ├── AuthService.php
│   │   │   └── MicrosoftAuthService.php  # NOVO
│   │   └── Validators/
│   │       ├── AuthValidator.php
│   │       └── ProfileValidator.php      # NOVO
│   ├── Container/
│   │   ├── Container.php    # Service Container com Reflection
│   │   └── bootstrap.php    # Registro de bindings
│   ├── Controller/
│   │   ├── AbstractController.php
│   │   ├── DashboardController.php
│   │   ├── IndexController.php
│   │   ├── ManagerController.php
│   │   └── ProfileController.php         # NOVO
│   ├── HourBank/
│   │   ├── Controllers/
│   │   │   └── HourBankController.php
│   │   ├── Interfaces/
│   │   │   └── HourBankRepositoryInterface.php
│   │   ├── Repositories/
│   │   │   └── HourBankRepository.php
│   │   ├── Services/
│   │   │   └── HourBankService.php
│   │   └── Validators/
│   │       └── HourBankValidator.php
│   ├── Http/
│   │   ├── Middleware/
│   │   │   ├── AuthMiddleware.php
│   │   │   └── ManagerMiddleware.php
│   │   └── RequestHandler.php
│   ├── Infrastructure/
│   │   ├── Database/
│   │   │   └── Connection.php
│   │   └── Mail/
│   │       ├── Mailer.php
│   │       └── MailTemplates.php
│   └── View/
│       ├── _partials/
│       │   ├── head.php
│       │   └── footer.php
│       ├── auth/
│       │   ├── login.php
│       │   └── register.php
│       ├── profile/
│       │   └── complete.php              # NOVO
│       ├── dashboard.php
│       ├── hour-bank/
│       │   ├── index.php
│       │   ├── pending.php
│       │   └── pending-user.php
│       ├── manager/
│       │   └── users.php
│       └── index.php
├── vendor/
├── .env
├── .gitignore
└── composer.json
```

---

## Instalação

### 1. Clone o repositório

```bash
git clone https://github.com/seu-usuario/azhoras.git
cd azhoras
```

### 2. Instale as dependências

```bash
composer install
```

### 3. Configure o `.env`

Copie o arquivo de exemplo e preencha as variáveis:

```bash
cp .env.example .env
```

```env
# Banco de Dados
DB_HOST=localhost
DB_NAME=azhoras
DB_USER=root
DB_PASSWORD=sua_senha

# Aplicação
APP_URL=http://localhost:8000

# E-mail (SMTP corporativo)
MAIL_HOST=smtp.suaempresa.com.br
MAIL_PORT=587
MAIL_USERNAME=sistema@suaempresa.com.br
MAIL_PASSWORD=sua_senha
MAIL_FROM=sistema@suaempresa.com.br
MAIL_FROM_NAME="Sistema Azhoras"
MAIL_ENCRYPTION=tls

# Microsoft 365 (OAuth2)
MICROSOFT_CLIENT_ID=seu_client_id
MICROSOFT_CLIENT_SECRET=seu_client_secret
MICROSOFT_TENANT_ID=seu_tenant_id
MICROSOFT_REDIRECT_URI=http://localhost:8000/auth/microsoft/callback
```

> ⚠️ Valores com espaço no `.env` devem estar entre aspas. Ex: `MAIL_FROM_NAME="Sistema Azhoras"`

### 4. Crie o banco de dados

Execute os SQLs na ordem abaixo no seu MySQL:

```sql
-- Tabela de usuários
CREATE TABLE users (
    id          INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    name        VARCHAR(100)    NOT NULL,
    email       VARCHAR(255)    NOT NULL UNIQUE,
    password    VARCHAR(255)    NOT NULL,
    cpf         VARCHAR(20)     NULL UNIQUE,
    cargo       VARCHAR(100)    NOT NULL,
    setor       ENUM('ti', 'rh', 'fin', 'op') NOT NULL,
    role        ENUM('employee', 'manager', 'admin') NOT NULL DEFAULT 'employee',
    created_at  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_email (email)
);

-- Tabela de banco de horas
CREATE TABLE hour_bank (
    id              INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    user_id         INT UNSIGNED    NOT NULL,
    type            ENUM('credit', 'debit') NOT NULL,
    minutes         INT UNSIGNED    NOT NULL,
    reference_date  DATE            NOT NULL,
    is_weekend      BIT(1)          NOT NULL DEFAULT 0,
    is_holiday      BIT(1)          NOT NULL DEFAULT 0,
    is_night        BIT(1)          NOT NULL DEFAULT 0,
    start_time      TIME            NOT NULL,
    end_time        TIME            NOT NULL,
    additional_rate DECIMAL(4,2)    NOT NULL DEFAULT 1.50,
    justification   VARCHAR(500)    NULL,
    status          ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
    approved_by     INT UNSIGNED    NULL,
    approved_at     TIMESTAMP       NULL,
    created_by      INT UNSIGNED    NOT NULL,
    created_at      TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (user_id)     REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by)  REFERENCES users(id),
    FOREIGN KEY (approved_by) REFERENCES users(id),
    INDEX idx_user_id         (user_id),
    INDEX idx_reference_date  (reference_date)
);

-- Tabela de saldo
CREATE TABLE hour_bank_balance (
    id            INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    user_id       INT UNSIGNED    NOT NULL UNIQUE,
    total_minutes INT UNSIGNED    NOT NULL DEFAULT 0,
    updated_at    TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

### 5. Inicie o servidor

```bash
php -S localhost:8000 -t Public
```

---

## Funcionalidades

### Autenticação
- Registro de usuário com validação completa (nome, CPF, e-mail, cargo, setor, senha)
- Login com sessão `$_SESSION`
- **Login com Microsoft 365 via OAuth2** *(novo)*
- Logout
- Middleware de autenticação — rotas protegidas redirecionam para `/login`

### Login com Microsoft 365 *(novo)*
- Autenticação via OAuth2 com Azure AD
- Usuário novo é criado automaticamente no banco na primeira autenticação
- CPF é opcional para usuários Microsoft (`NULL` permitido)
- Após primeiro login, usuário é redirecionado para completar o perfil (cargo, setor, CPF)
- Fluxo de callback com validação de `state` para prevenção de CSRF

### Completar Perfil *(novo)*
- Tela exibida automaticamente para usuários autenticados via Microsoft que ainda não completaram o perfil
- Campos: CPF (com máscara automática), cargo e setor
- Após preenchimento, sessão é atualizada e usuário é redirecionado ao dashboard

### Perfis de Usuário

| Perfil | Permissões |
|---|---|
| `employee` | Lançar horas, ver próprios lançamentos e saldo |
| `manager` | Tudo do employee + aprovar/rejeitar lançamentos + ver todos os colaboradores |
| `admin` | Mesmas permissões do manager |

### Banco de Horas
- Lançamento de horas extras com horário de início e término
- Cálculo automático do total em minutos
- Regras CLT aplicadas automaticamente:
  - Limite de 2h extras por dia (Art. 59)
  - Adicional de 50% para dias úteis
  - Adicional de 100% para domingos e feriados
  - Adicional de 20% para jornada noturna (após 22h)
- Fluxo de aprovação: `pending → approved / rejected`
- Saldo nunca negativo (validado no service e via trigger no banco)

### Gestão (Perfil Gestor)
- Tela com lista de todos os colaboradores
- Exibe saldo, total de lançamentos e pendências por colaborador
- Acesso direto aos lançamentos pendentes de cada colaborador
- Aprovar ou rejeitar lançamentos individualmente

### Notificações por E-mail
- Ao realizar um lançamento, todos os gestores e admins recebem e-mail automático via SMTP
- E-mail em HTML com detalhes do lançamento e botão de acesso direto aos pendentes

---

## Roteamento

O sistema possui um router próprio com suporte a **parâmetros dinâmicos**:

```php
// Rota estática
'/dashboard' => createRoute(DashboardController::class, 'index', 'GET', 'dashboard'),

// Rota com parâmetro dinâmico
'/hour-bank/pending/user/{user_id}' => createRoute(HourBankController::class, 'pendingByUser', 'GET', 'hour-bank.pending.user'),
```

Parâmetros são acessíveis via:

```php
$this->request->param('user_id');
```

---

## Rotas Disponíveis

| Método | Rota | Controller | Permissão |
|---|---|---|---|
| GET | `/` | IndexController@index | Público |
| GET | `/login` | LoginController@index | Público |
| POST | `/login/store` | LoginController@store | Público |
| GET | `/logout` | LoginController@logout | Autenticado |
| GET | `/auth/microsoft` | LoginController@microsoftRedirect | Público |
| GET | `/auth/microsoft/callback` | LoginController@microsoftCallback | Público |
| GET | `/register` | RegisterController@index | Público |
| POST | `/register/store` | RegisterController@store | Público |
| GET | `/dashboard` | DashboardController@index | Autenticado |
| GET | `/profile/complete` | ProfileController@index | Autenticado |
| POST | `/profile/complete/store` | ProfileController@store | Autenticado |
| GET | `/hour-bank` | HourBankController@index | Autenticado |
| POST | `/hour-bank/store` | HourBankController@store | Autenticado |
| GET | `/hour-bank/pending` | HourBankController@pending | Gestor/Admin |
| POST | `/hour-bank/approve` | HourBankController@approve | Gestor/Admin |
| POST | `/hour-bank/reject` | HourBankController@reject | Gestor/Admin |
| GET | `/hour-bank/pending/user/{user_id}` | HourBankController@pendingByUser | Gestor/Admin |
| GET | `/manager/users` | ManagerController@users | Gestor/Admin |

---

## Princípios SOLID Aplicados

| Princípio | Aplicação |
|---|---|
| **SRP** | Cada classe tem uma única responsabilidade (Repository, Service, Validator, Controller separados) |
| **OCP** | Novas rotas e funcionalidades são adicionadas sem alterar o núcleo do router |
| **LSP** | Qualquer implementação de `UserRepositoryInterface` ou `HourBankRepositoryInterface` é substituível |
| **ISP** | Interfaces enxutas e específicas por domínio |
| **DIP** | Controllers e Services dependem de interfaces, não de implementações concretas |

---

## Service Container

O projeto possui um container de injeção de dependências próprio baseado em `Reflection`:

```php
// Resolve automaticamente todas as dependências
$controller = $container->make(RegisterController::class);
```

Bindings de interfaces são registrados em `src/Container/bootstrap.php`:

```php
$container->bind(UserRepositoryInterface::class, fn() => new UserRepository());
```

---

## Segurança

- Senhas armazenadas com `password_hash()` (BCRYPT)
- Senhas aleatórias geradas com `random_bytes()` para usuários Microsoft
- Proteção contra SQL Injection via PDO com prepared statements
- Validação de `state` OAuth2 para prevenção de CSRF no fluxo Microsoft
- Validação de todos os inputs no backend
- Middleware de autenticação e autorização por perfil
- Credenciais isoladas em `.env` (nunca no repositório)
- `.env` e `vendor/` no `.gitignore`

---

## Changelog

### 06/03/2026
- Integração com **Microsoft 365** via OAuth2 (login corporativo)
- Novo fluxo de **completar perfil** para usuários autenticados via Microsoft
- CPF alterado para `NULL` na tabela `users` para suportar usuários Microsoft
- Adicionado `ProfileController` e `ProfileValidator`
- Adicionado `MicrosoftAuthService` com validação de CSRF via `state`
- Novas rotas: `/auth/microsoft`, `/auth/microsoft/callback`, `/profile/complete`, `/profile/complete/store`
- Máscara automática de CPF via JavaScript nas views de registro e completar perfil
- Correção do `.env` — valores com espaço devem estar entre aspas

---

## Licença

Projeto proprietário — uso interno. Todos os direitos reservados.