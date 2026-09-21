# AGENTS.md

CodeIgniter 4 (PHP 8.2+) backend + Vite/TS frontend. Docroot is `public/`; never point server at repo root.

## Layout
- `app/Controllers/Api.php` — all JSON API; page controllers (`Home,Course,Training,Performance,Documents,Calendar,Settings`) only render views.
- `app/Services/` — `GroqService.php`, `NotionService.php` (external AI/sync logic).
- `app/Models/`, `app/Database/` — models/migrations/seeds (migration files excluded from composer classmap).
- `app/Views/` — excluded from PHPUnit coverage; `app/Config/Routes.php` is source of truth for URLs.
- `frontend/src/` — TS entry `main.ts`, per-page modules (`dashboard,training,course,performance,calendar,documents,settings`), shared `api.ts,ui.ts`.
- `db/init.sql` — raw DB seed; `notion_tasks_extracted.json` — local fixture for Notion sync.
- `writable/` (cache/logs/session/uploads), `builds`, `build/` — generated, git-ignored.

## Backend (XAMPP, Windows PowerShell)
- Base URL: `http://localhost/gate%20exam%20preparation/public/` with `app.indexPage = ''` — requires mod_rewrite; keep space in path URL-encoded.
- Config via `.env` (copy from `env`); never commit. DB: MySQLi `gateeaxmtraining` on localhost:3306, user root.
- Commands: `php spark serve` (dev), `php spark migrate --all`, `php spark db:seed <Seeder>`, `php spark routes`, `php spark cache:clear`.
- Namespaces: `App\` → `app/`, `Config\` → `app/Config/`; tests support `Tests\Support\` → `tests/_support`.

## Frontend
- `frontend/`: `npm run dev` (Vite :5173, `/api` proxied to `public/`), `npm run build` = `tsc --noEmit && vite build` → outputs to `public/assets/` as `app.js`/`app.css` + vendor chunks (`emptyOutDir: true` wipes that dir), `npm run preview`.
- Tailwind v4 via `@tailwindcss/vite` + DaisyUI; `tsconfig.json` is typecheck gate — fix TS errors before Vite build.

## Tests
- `composer test` / `vendor/bin/phpunit` (bootstrap: framework Test bootstrap; suite `App` → `./tests`). Single test: `vendor/bin/phpunit --filter <Name> --testdox`.
- phpunit.xml sets `app.baseURL=http://example.com/` and path constants; DB `database.tests.*` block is commented out — DB tests fall back to default connection, so prefer SQLite/in-memory or uncomment for isolated MySQL.
- Logs/coverage go to `build/logs/`, cache to `build/.phpunit.cache`.
