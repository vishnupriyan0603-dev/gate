# GATE Exam Preparation — Project Guidelines

## Overview & Architecture
This workspace (`live/`) represents the active deployment and development environment:
- **Root Directory (`live/`)**: Front controller (`index.php`), Apache routing (`.htaccess`), and static assets (`assets/`, `lottie/`, `media/`, `models/`).
- **Application Directory (`main/`)**: Contains the CodeIgniter 4 (PHP 8.2+) backend, Vite/TypeScript frontend, migrations, database seeds, and test suites.

## Backend Guidelines (CodeIgniter 4 / PHP 8.2+)
- **Docroot & URLs**: The server docroot points to `live/` (or `main/public/`). Keep spaces URL-encoded in paths (`gate%20exam%20preparation`).
- **Controllers & Routing**:
  - `main/app/Config/Routes.php` is the single source of truth for routing.
  - `main/app/Controllers/Api.php` handles all JSON API responses.
  - Page controllers (`Home`, `Course`, `Training`, `Performance`, `Documents`, `Calendar`, `Settings`) are strictly for view rendering.
- **Services & External Logic**:
  - Keep third-party integrations in `main/app/Services/` (e.g., `GroqService.php` for AI logic, `NotionService.php` for task sync).
- **Database & Migrations**:
  - MySQL database `gateeaxmtraining` on `localhost:3306` (user: `root`).
  - Models live in `main/app/Models/`; migrations in `main/app/Database/Migrations/`.
  - Raw seeds and fixtures: `main/db/init.sql` and `main/notion_tasks_extracted.json`.
- **Environment & Secrets**:
  - Configuration is driven by `main/.env` (copied from `env`).
  - Never commit credentials or API keys.

## Frontend Guidelines (Vite + TypeScript)
- **Source Location**: `main/frontend/src/`
- **Entrypoints**: `main.ts` is the primary entry point; per-page modules reside in `dashboard`, `training`, `course`, `performance`, `calendar`, `documents`, `settings`.
- **Shared Utilities**: Common API calls and UI utilities belong in `api.ts` and `ui.ts`.
- **Styling**: Tailwind CSS v4 via `@tailwindcss/vite` and DaisyUI.
- **Build Rules**:
  - `tsc --noEmit` must pass before running `vite build`.
  - Frontend builds output assets into `public/assets/` (`app.js`, `app.css`).

## Common Development Commands (Windows PowerShell)
- **Spark CLI**:
  ```powershell
  php spark serve                   # Start local CI4 dev server
  php spark migrate --all           # Run pending migrations
  php spark db:seed <SeederName>    # Run database seeder
  php spark routes                  # List active routes
  php spark cache:clear             # Clear framework cache
  ```
- **Frontend**:
  ```powershell
  npm --prefix main/frontend run dev     # Start Vite dev server (:5173)
  npm --prefix main/frontend run build   # Typecheck and build frontend
  ```
- **Tests**:
  ```powershell
  ./vendor/bin/phpunit                  # Run PHPUnit test suite
  ./vendor/bin/phpunit --filter <Name>  # Run targeted test
  ```

