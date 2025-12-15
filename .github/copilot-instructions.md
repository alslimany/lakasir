# Copilot Instructions for Lakasir Repository

Purpose
- This file instructs Copilot-style assistants how to act when working inside this repository.

High-level repo summary
- Laravel-backed POS SaaS with multitenancy (stancl/tenancy), Filament admin panels, and subscription management (Stripe + Moamalat support).
- Key domains: admin central panel (`/admin`), tenant panels (tenant subdomains), POS (cashier), billing/subscriptions, translations (JSON), and RTL support for Arabic.

Primary contacts for context
- Files to read first: `README.md`, `MULTITENANCY_SAAS.md`, `IMPLEMENTATION_SUMMARY.md`, `MOAMALAT_PAYMENT_INTEGRATION.md`, `ARABIC_RTL_SUBSCRIPTION_IMPLEMENTATION.md`.

When you start work
- Always run a quick repo scan (open the README and architecture docs above) to understand which subsystems are involved.
- If making code changes, update or add documentation in `instructions/` and relevant `.md` files.

Coding conventions & expectations
- Keep changes minimal and surgical. Prefer fixing root causes over superficial patches.
- PHP: follow existing project style (PSR-like). Match surrounding code formatting.
- Do not add one-letter variable names, avoid inline comments in code unless requested.

Filament & Widgets
- Filament v3 used. For table widgets, prefer using `getTableHeading()` for headings and `->modelLabel()`/`->pluralModelLabel()` to localize empty-state messages.
- When adding filters, import Filament filters (e.g., `use Filament\Tables\Filters\TernaryFilter;`).

Translations & RTL
- Translations live in `lang/*.json` (English `en.json`, Arabic `ar.json`). Add keys to both files when introducing user-facing strings.
- The app sets `config('app.direction')` for RTL; `resources/views/filament/rtl-styles.blade.php` contains RTL-specific CSS.
- Ensure empty-state texts that use `:model` get proper `modelLabel()` / `pluralModelLabel()` on Filament tables.

Tenancy & billing
- Tenancy is database-per-tenant using `stancl/tenancy`. Use `tenancy()->tenant` to access current tenant context.
- Subscription flows may use Stripe and/or Moamalat. See `MOAMALAT_PAYMENT_INTEGRATION.md` for Moamalat specifics.
- Billing routes live under tenant routes; `CheckTenantStatus` middleware controls access to billing and feature gating.

Testing & verification
- Run unit/feature tests with `php artisan test` when modifying logic; aim not to break unrelated tests.
- When editing translations or views, use `php artisan view:clear`, `php artisan config:clear`, and `php artisan cache:clear` locally.

Files and areas of caution
- `app/Filament/*` — Filament resources and widgets: be careful to maintain `getTableHeading()` and translations.
- `app/Http/Middleware/*` — tenancy and billing access rules. Changing these affects access flows.
- `lang/*.json` — maintain JSON validity; add both English and Arabic keys.
- `resources/views/filament/*.blade.php` — RTL styles and Filament templates; test with Arabic locale.
- `app/Services/RegisterTenant.php` and `app/Services/SubscriptionService.php` — registration and subscription lifecycle.
- `routes/tenant.php` and central webhook routes — ensure route protections and webhook validation.

Commit & PR guidance
- Keep commits small and descriptive. Include related docs/test updates.
- For substantial feature changes, include a short `CHANGES_SUMMARY.md` or update one that exists.
- If adding migrations, include instructions and mention `tenants:migrate` vs `migrate` as appropriate.

Suggested Copilot prompts
- "Add a translation key for 'X' in `lang/en.json` and `lang/ar.json`, and replace hardcoded string in `resources/views/...`".
- "For the Filament table in `app/Filament/Tenant/Widgets/...`, set `modelLabel()` and `pluralModelLabel()` to fix Arabic empty-state messages.".
- "Create an instructions file summarizing `MOAMALAT_PAYMENT_INTEGRATION.md` and list verification steps.".

Safety & responsibilities
- Do not modify vendor files in place. Prefer extending via providers or app code.
- If a change could affect billing/payment flows, do not deploy without QA and coordinator approval.

Where to add developer notes
- Use the `instructions/` folder for per-document guidance and short explainer files.

---

If you need more specific instructions for a subsystem, open the corresponding `instructions/<file>-explanation.md` file.