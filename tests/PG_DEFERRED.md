# Testes adiados (PostgreSQL)

As rotas `GET /`, `GET /dashboard` e `GET /balance` disparam consultas com SQL específico de PostgreSQL:

- `App\Services\DashboardService::getMonthlyPerformance` — `TO_CHAR`, `groupByRaw`
- `App\Services\BalanceService::getMonthlyResults` e `getLineChartData` — `EXTRACT` mensal

O `phpunit.xml` do projeto usa SQLite em memória; testes de integração HTTP completos para essas páginas **não** entram no pipeline SQLite até:

1. Refatorar as queries para SQL portável (ou ramos por driver), ou
2. Rodar uma suite separada com `@group postgres` e `DB_CONNECTION=pgsql` no CI.
