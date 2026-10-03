# Management System

Sistema administrativo orientado a objetos desenvolvido em **PHP puro**, sem framework, com Composer, arquitetura MVC, PDO, MySQL e PHPUnit.

## Recursos

- Login seguro, sessões protegidas, CSRF, RBAC e auditoria
- Dashboard e gestão de departamentos, colaboradores, projetos e transações
- Operações financeiras atômicas com rollback
- Relatórios com filtros, CTE, `DENSE_RANK`, agregações e exportação CSV
- Migrações, seed idempotente, health check e CI em PHP 8.2–8.4 com MySQL 8.4

## Arquitetura

```text
public/index.php     Front controller
routes/              Rotas
src/Core/            Kernel MVC, roteador, container, PDO e sessão
src/Http/            Controllers e middleware
src/Security/        Autenticação e CSRF
src/Views/           Interface renderizada no servidor
database/migrations/ Estrutura versionada do MySQL
tests/               Testes automatizados
```

O container resolve dependências por construtor, o roteador compõe middleware e controllers, e consultas com entrada externa usam prepared statements.

## Requisitos e instalação no XAMPP

Requer PHP 8.2+, Composer 2 e MySQL 8+.

```bash
cd E:/xampp/htdocs/projetos/management-system
composer install
copy .env.example .env
php bin/console migrate
php bin/console seed
php -S localhost:8080 -t public
```

Abra `http://localhost:8080`. Acesso inicial: `admin@example.com` / `Admin@123`. Troque essa senha antes de usar em ambiente compartilhado.

## Docker

```bash
copy .env.example .env
docker compose up --build
```

## Qualidade

```bash
composer cs
composer analyse
composer test
composer check
```

## Acesso e workflow

Os papéis Administrador, Gestor e Analista recebem permissões granulares como `employees.manage`, `transactions.manage` e `reports.view`.

Toda mudança nasce em uma issue, segue em branch tipada (`feat/`, `chore/`, `test/`, `docs/`) e entra em `main` por pull request validado pela CI. Commits seguem Conventional Commits.

## Licença

Distribuído sob a [licença MIT](LICENSE).


