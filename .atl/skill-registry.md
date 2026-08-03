# Skill Registry

**Orchestrator use only.** Read this registry once per session to resolve skill paths, then pass pre-resolved paths directly to each sub-agent's launch prompt. Sub-agents receive path and load skill directly — they do NOT read this registry.

Generated: 2026-07-29
Project: vendedores-nvd
Persistence: engram

## User-Level Skills

| Skill | Description | Trigger | Path |
|-------|-------------|----------|------|
| `angular-architect` | Generates Angular 17+ standalone components, configures advanced routing with lazy loading and guards, implements NgRx state management, applies RxJS patterns, and optimizes bundle performance. | Use when building Angular 17+ applications with standalone components or signals, setting up NgRx stores, establishing RxJS reactive patterns, performance tuning, or writing Angular tests for enterprise apps. | `C:\Users\PC AMD\.agents\skills\angular-architect\SKILL.md` |
| `find-skills` | Helps users discover and install agent skills when they ask questions like "how do I do X", "find a skill for X", "is there a skill that can...", or express interest in extending capabilities. | Use when user is looking for functionality that might exist as an installable skill. | `C:\Users\PC AMD\.agents\skills\find-skills\SKILL.md` |
| `frontend-design` | Create distinctive, production-grade frontend interfaces with high design quality. Generates creative, polished code and UI design that avoids generic AI aesthetics. | Use when user asks to build web components, pages, artifacts, posters, or applications (websites, landing pages, dashboards, React components, HTML/CSS layouts, or styling/beautifying any web UI). | `C:\Users\PC AMD\.config\opencode\skills\frontend-design\SKILL.md` |
| `go-testing` | Go testing patterns for Gentleman.Dots, including Bubbletea TUI testing. | Use when writing Go tests, using teatest, or adding test coverage. | `C:\Users\PC AMD\.config\opencode\skills\go-testing\SKILL.md` |
| `laravel-specialist` | Build and configure Laravel 10+ applications, including Eloquent models, Sanctum auth, Horizon queues, RESTful APIs, and Livewire components. | Use when creating Laravel models, queue workers, Sanctum auth flows, Livewire components, optimizing Eloquent queries, or writing Pest/PHPUnit tests for Laravel features. | `C:\Users\PC AMD\.agents\skills\laravel-specialist\SKILL.md` |
| `sdd-apply` | Implement tasks from change, writing actual code following specs and design. | Use when orchestrator launches you to implement one or more tasks from a change. | `C:\Users\PC AMD\.config\opencode\skills\sdd-apply\SKILL.md` |
| `sdd-archive` | Sync delta specs to main specs and archive a completed change. | Use when orchestrator launches you to archive a change after implementation and verification. | `C:\Users\PC AMD\.config\opencode\skills\sdd-archive\SKILL.md` |
| `sdd-design` | Create technical design document with architecture decisions and approach. | Use when orchestrator launches you to write or update technical design for a change. | `C:\Users\PC AMD\.config\opencode\skills\sdd-design\SKILL.md` |
| `sdd-explore` | Explore and investigate ideas before committing to a change. | Use when orchestrator launches you to think through a feature, investigate the codebase, or clarify requirements. | `C:\Users\PC AMD\.config\opencode\skills\sdd-explore\SKILL.md` |
| `sdd-init` | Initialize Spec-Driven Development context in any project. | Use when user wants to initialize SDD in a project, or says "sdd init", "iniciar sdd", "openspec init". | `C:\Users\PC AMD\.config\opencode\skills\sdd-init\SKILL.md` |
| `sdd-propose` | Create a change proposal with intent, scope, and approach. | Use when orchestrator launches you to create or update a proposal for a change. | `C:\Users\PC AMD\.config\opencode\skills\sdd-propose\SKILL.md` |
| `sdd-spec` | Write specifications with requirements and scenarios (delta specs for changes). | Use when orchestrator launches you to write or update specs for a change. | `C:\Users\PC AMD\.config\opencode\skills\sdd-spec\SKILL.md` |
| `sdd-tasks` | Break down a change into an implementation task checklist. | Use when orchestrator launches you to create or update the task breakdown for a change. | `C:\Users\PC AMD\.config\opencode\skills\sdd-tasks\SKILL.md` |
| `sdd-verify` | Validate that implementation matches specs, design, and tasks. | Use when orchestrator launches you to verify a completed (or partially completed) change. | `C:\Users\PC AMD\.config\opencode\skills\sdd-verify\SKILL.md` |
| `skill-creator` | Creates new AI agent skills following Agent Skills spec. | Use when user asks to create a new skill, add agent instructions, or document patterns for AI. | `C:\Users\PC AMD\.config\opencode\skills\skill-creator\SKILL.md` |
| `skill-registry` | Create or update the skill registry for the current project. | Use when user says "update skills", "skill registry", "actualizar skills", "update registry", or after installing/removing skills. | `C:\Users\PC AMD\.config\opencode\skills\skill-registry\SKILL.md` |
| `software-architecture` | Guide for quality focused software architecture. | Use when user wants to write code, design architecture, analyze code, or any case related to software development. | `C:\Users\PC AMD\.config\opencode\skills\software-architecture\SKILL.md` |
| `ui-ux-pro-max` | UI/UX design intelligence with 50+ styles, 21 palettes, 50 font pairings, 20 charts, and 9 stacks. | Use when designing UI/UX for web/mobile applications requiring comprehensive design guidance. | `C:\Users\PC AMD\.config\opencode\skills\ui-ux-pro-max\SKILL.md` |

## Project-Level Skills

| Skill | Description | Trigger | Path |
|-------|-------------|---------|------|
| `design-implementer` | Implement professional UI designs from DESIGN.md files. Parses YAML frontmatter (colors, typography, spacing, components), generates Tailwind config + CSS custom properties, handles local images, applies responsive behavior per spec. | Use when implementing UI from a `*-DESIGN.md` design system file. | `C:\Users\PC AMD\Desktop\TEMPLATE\vendedores-nvd\.claude\skills\design-implementer\SKILL.md` |
| `design-spec-creator` | Create structured `*-DESIGN.md` design specification files with YAML tokens, component specs, and visual guidelines for AI design agents. | Use when you need to create a new DESIGN.md, analyze a brand, or define a design system from brand guidelines, screenshots, or descriptions. | `C:\Users\PC AMD\Desktop\TEMPLATE\vendedores-nvd\.claude\skills\design-spec-creator\SKILL.md` |

## Project Convention Files

- `CLAUDE.md` — Project overview and development instructions (PHP + Angular hybrid architecture)
- `PRD.md` — Product requirements document (order restructuring initiative)
- `AGENTS.md` (at `~/.config/opencode/AGENTS.md`) — Global agent personality, rules, and skill auto-loading config

Read convention files listed above for project-specific patterns and rules.

## Tech Stack Detected

### Backend (PHP)
- **Framework**: Custom MVC-like architecture (no framework)
- **Language**: PHP 8.0+
- **API Versioning**: `/api/v1/*`
- **Authentication**: Session-based using native PHP sessions (session name: `sid`)
- **Database**: MySQL/MariaDB via mysqli with prepared statements
- **Routing**: Custom router with pattern matching in `routes/`
- **Structure**:
  - `api/` — API endpoint handlers (Accounts, Auth, Orders, Products, Customers, etc.)
  - `models/` — Database models implementing `JsonSerializable`
  - `controllers/` — Web page controllers (Home, App, Sellers)
  - `helpers/` — Utility classes (Router, Response, Request, Session, Logger)
  - `filters/` — Security layer (SessionFilter, AccountFilter)
  - `config/` — Configuration classes (Database, Session, Email, PayU)
  - `services/` — Business logic services
- **Dependencies** (composer.json):
  - PHPMailer (email)
  - TCPDF + mPDF (PDF generation)
  - vlucas/phpdotenv (environment variables)
  - Guzzle HTTP client
- **Entry Points**: `rt.php` (main router), `api.php` (API), `index.php` (web), `Application.php` (CLI)

### Frontend — Seller Panel (Angular SPA)
- **Framework**: Angular 19.1 standalone components
- **Language**: TypeScript 5.6, strict mode, ES2022 target
- **State Management**: NgRx Store 19.0.0
- **Routing**: Route-based lazy loading via `loadComponent()` with guards (`authGuard`, `loginGuard`)
- **HTTP**: Functional interceptors (`SessionInterceptor` for 401 handling)
- **Styling**: Tailwind CSS 3.4 + SCSS + Iconify dynamic icons
- **Testing**: Karma + Jasmine 5.2, Chrome launcher, coverage
- **Locale**: es-AR with custom USD currency symbol override
- **Feature Modules**: auth, balances, clients, config, cotizacion, dashboard, layout, orders, products, router, shared
- **Build**: Angular CLI 19.1.5, production output to `dist/`
- **Build Scripts**: `build.sh`, `build.ps1`, `setup-deploy.sh`
- **API Config**: `environment.ts` switches between `localhost:8000` (dev) and `/api/v1/` (prod)
- **Design Systems**: `tyme-rosario-DESIGN.md`, `ferrari-DESIGN.md`, `renault-DESIGN.md`, `lamborghini-DESIGN.md`

### Database
- **Type**: MySQL/MariaDB
- **Connection**: mysqli with prepared statements
- **Models**: Implement `JsonSerializable` interface
- **Schema**: `u918235402_tymeros.sql`

### Third-Party Integrations
- PayU payment gateway
- PHPMailer for transactional emails
- TCPDF + mPDF for PDF generation (invoices, reports)
- Guzzle for HTTP client requests

## Architecture Patterns

### Backend
1. **MVC-like Structure**: Controllers (web), Models (data), Helpers (utilities), Filters (security)
2. **API Handlers**: Classes in `api/` directory with static methods per resource
3. **Models**: Database entities with `JsonSerializable` interface, prepared statements
4. **Filters**: SessionFilter (auth) + AccountFilter (authorization by account type)
5. **Routing**: Custom router using regex pattern matching in `routes/api.php` and `routes/web.php`
6. **Services**: Business logic separated into `services/` directory

### Frontend (Angular Seller Panel)
1. **Standalone Components**: No NgModule, direct `imports` in `@Component`
2. **Feature-based Organization**: Directories by domain (auth, orders, products, clients, etc.)
3. **Services**: Singleton services in `src/app/*/services/`
4. **Guards**: Route protection with `authGuard` (authenticated) and `loginGuard` (guest-only)
5. **Interceptors**: Functional interceptor for session/401 handling
6. **Lazy Loading**: Feature modules loaded on demand via `loadComponent()` and `loadChildren()`

### Design System
- Pipeline: `design-spec-creator` produces `*-DESIGN.md` → `design-implementer` consumes them to generate code
- Existing brand designs: tyme-rosario, ferrari, renault, lamborghini

## Development Workflow
1. **Local Dev**: `php -S localhost:8000 -t .` + `ng serve` on port 4200 (proxied)
2. **Build**: `ng build` or `npm run build` for production
3. **Testing**: `ng test` (Karma + Jasmine) for frontend
4. **API Dev**: Add routes in `routes/api.php`, implement handlers in `api/`, models in `models/`
5. **PHP Dev**: Custom MVC — no framework conventions to follow

## Security
- **Session Name**: `sid`, SameSite: Lax, Secure: false (dev)
- **Auth Validation**: `SessionFilter::validateApiSession()` (backend)
- **Account Authorization**: `AccountFilter::filterApiCustomerAccount()` (by account type)
- **API Calls**: `withCredentials: true` for cookie-based session

## Notes
- `design-spec-creator` and `design-implementer` form a design-to-code pipeline at project level
- All SDD skills (sdd-*) are managed by Spec-Driven Development orchestration workflow
- `angular-architect`, `frontend-design`, and `ui-ux-pro-max` apply when working with Angular/frontend code
- `software-architecture` applies for architecture decisions
- Project uses PHP session-based auth with no framework — pure PHP custom MVC
- Seller panel is Angular 19.1 SPA for sellers to manage customers, products, orders, and balances
- PRD.md documents an existing initiative to restructure order endpoints for data integrity
