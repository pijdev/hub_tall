# PROJETO: Starter Kit Administrativo

## Descrição:

Um ponto de partida seguro e moderno para construir sistemas administrativos com a TALL Stack (Tailwind CSS v4, Alpine.js 3, Laravel 13 + Livewire 4) e Flux UI.

## Stack Tecnológica

### Backend
- **Laravel** 13.17+
- **Livewire** 4.1+
- **Flux UI** 2.13.1 (componentes Blade/Livewire)
- **Laravel Fortify** 1.37.2 (autenticação)
- **Laravel AI** 0.8.1 (integração com IA)
- **Laravel Blaze** 1.0 (otimização de componentes Blade)
- **Laravel Chisel** 0.1.0

### Frontend
- **Tailwind CSS** 4.0.7
- **Vite** 8.0.0
- **Alpine.js** 3 (via Livewire)
- **CropperJS** 2.1.1 (crop de imagens)
- **@tailwindcss/typography** 0.5.20
- **@laravel/passkeys** 0.2.0 (WebAuthn)

### Ferramentas de Desenvolvimento
- **Pest** 4.7 (testes)
- **Larastan** 3.9 (análise estática)
- **Laravel Pint** 1.27 (formatação de código)
- **Laravel Boost** 2.4
- **Laravel Pail** 1.2.5 (logs)
- **Laravel PAO** 1.0.6

## Requisitos

- PHP 8.3+
- Composer
- Node.js 20+ e NPM
- SQLite (padrão) ou qualquer banco compatível com Laravel (MySQL, PostgreSQL, SQL Server)

## Instalação

### Opção 1: Setup Automático (Recomendado)

```bash
composer run setup
```

Este comando executa automaticamente:
- Instala dependências do Composer
- Cria o arquivo `.env` a partir do `.env.example`
- Gera a chave da aplicação
- Executa as migrações
- Instala dependências do NPM
- Compila os assets

### Opção 2: Instalação Manual

```bash
# Instalar dependências do PHP
composer install

# Instalar dependências do frontend
npm install

# Configurar ambiente
cp .env.example .env
php artisan key:generate

# Executar migrações
php artisan migrate

# Compilar assets
npm run build
```

## Desenvolvimento

```bash
# Iniciar servidor de desenvolvimento (PHP + Queue + Vite)
composer run dev
```

Este comando inicia três processos simultaneamente:
- Servidor PHP Artisan
- Worker de filas
- Servidor de desenvolvimento Vite com hot reload

## Testes

```bash
# Executar todos os testes
php artisan test --compact

# Executar com verificação de código
composer run test
```

O comando `composer run test` executa:
- Verificação de estilo de código (Pint)
- Análise estática (Larastan/PHPStan)
- Testes automatizados (Pest)

## Qualidade de Código

```bash
# Formatar código PHP
composer run lint

# Verificar formatação sem alterar arquivos
composer run lint:check

# Verificar tipos
composer run types:check
```

## Estrutura do Projeto

```
app/
├── Actions/          # Actions da aplicação
├── Ai/              # Integrações com IA
├── Concerns/        # Traits e concerns reutilizáveis
├── Console/         # Comandos Artisan personalizados
├── Data/            # Data Transfer Objects (DTOs)
├── Enums/           # Enums da aplicação
├── Http/            # Controllers, Middleware, Requests
├── Livewire/        # Componentes Livewire
├── Models/          # Modelos Eloquent
├── Notifications/   # Notificações
├── Policies/        # Policies de autorização
├── Providers/       # Service Providers
├── Rules/           # Regras de validação customizadas
└── helpers.php      # Funções auxiliares globais

config/              # Arquivos de configuração
database/
├── factories/       # Factories para testes
├── migrations/      # Migrações do banco de dados
└── seeders/         # Seeders

resources/
└── views/           # Views Blade e Livewire SFC

routes/              # Definição de rotas
stubs/               # Stubs para geração de código
tests/               # Testes Pest/PHPUnit
```

## Banco de Dados

O projeto usa **SQLite** como banco de dados padrão (arquivo `database/database.sqlite`).

Para usar outros bancos, edite o arquivo `.env`:

```env
# MySQL
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nome_do_banco
DB_USERNAME=usuario
DB_PASSWORD=senha

# PostgreSQL
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=nome_do_banco
DB_USERNAME=usuario
DB_PASSWORD=senha
```

## Recursos Incluídos

- ✅ Autenticação completa (Laravel Fortify)
- ✅ Suporte a Passkeys/WebAuthn
- ✅ Painel administrativo com Flux UI
- ✅ Integração com IA (Laravel AI)
- ✅ Otimização de componentes Blade (Blaze)
- ✅ Crop de imagens (CropperJS)
- ✅ Tipografia (Tailwind Typography)
- ✅ Testes automatizados (Pest)
- ✅ Análise estática (Larastan)
- ✅ Formatação automática (Pint)
- ✅ Hot reload com Vite
- ✅ Queue worker integrado no desenvolvimento

## Comandos Úteis

```bash
# Listar rotas
php artisan route:list

# Limpar cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Criar componentes
php artisan make:component NomeComponente
php artisan make:livewire NomeComponente
php artisan make:model NomeModelo -mfcr

# Executar migrações
php artisan migrate
php artisan migrate:rollback
php artisan migrate:fresh --seed
```

## Licença

[MIT](https://www.tldrlegal.com/license/mit-license)