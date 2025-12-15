Goal

Add a `tenant_id` column to the centralized `subscriptions` table so the application can scope and query subscriptions by tenant when subscription rows are stored centrally.

Why

- The application stores SubscriptionPlan and subscription rows in the central (main) database.
- Some parts of the code (Filament tenant billing page / Cashier integration) add a `tenant_id` filter when querying `subscriptions`. Without the column present in the central `subscriptions` table those queries fail with "Unknown column 'subscriptions.tenant_id'".
- Adding `tenant_id` lets the central subscriptions table point back to the tenant that owns the subscription while keeping subscriptions centralized.

What I added

- Migration: `database/migrations/tenants/2025_12_13_000002_add_tenant_id_to_subscriptions_table.php`
  - Adds `tenant_id` (string, nullable) and an index.
  - Drops the index and column on rollback.

Recommended follow-up tasks

1) Run migrations

- Central (main/admin) DB migrations:

- Ensure your default DB connection points to the central database and run central migrations as usual:

```bash
php artisan migrate
```

- Tenant-scoped migrations (this change lives under `database/migrations/tenants/`):

- If you use `stancl/tenancy` run tenant migrations across all tenants with:

```bash
php artisan tenants:migrate
```

- To run only this tenant migration file (targeted):

```bash
php artisan tenants:migrate --path="database/migrations/tenants/2025_12_13_000002_add_tenant_id_to_subscriptions_table.php"
```

Note: the repository separates central (admin) migrations under `database/migrations/` and tenant-specific migrations under `database/migrations/tenants/`. This migration was placed under the tenants folder per project convention.

2) Backfill existing `subscriptions.tenant_id` values (if you have existing subscriptions)

- If existing subscriptions can be associated to tenants via `user_id` (e.g., a tenant's owner user lives in a `users` table that references tenant id), write a short script or run SQL to populate `tenant_id`.

Example: if `users` table has `tenant_id` column (adjust column/table names to match your schema):

```sql
UPDATE subscriptions s
JOIN users u ON u.id = s.user_id
SET s.tenant_id = u.tenant_id
WHERE s.tenant_id IS NULL;
```

If your user/tenant mapping is different, write a small command or tinker script to populate values reliably.

3) Ensure future subscription creation populates `tenant_id`

Option A (recommended): Set `tenant_id` on the Billable model when creating subscriptions

- If you create subscriptions via Cashier on the `Tenant` model (or on a model resolved under tenant context), ensure the subscription creation logic sets `tenant_id` explicitly.

Example (where subscription is created for a tenant):

```php
// inside the code that creates a subscription for the tenant
$subscription = $tenant->newSubscription('default', $plan->stripe_price_id)
    ->create($paymentMethod);

// set tenant_id on the subscription row (central DB)
$subscription->tenant_id = tenant()->id ?? $tenant->id ?? null;
$subscription->save();
```

Option B: Use a model observer on the Subscription model

- Create an observer that listens for `creating` on the Subscription model and sets `tenant_id` from the active tenancy context.

4) Test tenant billing flows

- After migration and backfill, visit a tenant billing page and confirm queries no longer fail with "Unknown column 'subscriptions.tenant_id'".
- Create a new subscription as a tenant and confirm `tenant_id` is populated.

5) Centralized vs per-tenant subscriptions

- This change keeps subscriptions centralized (single `subscriptions` table on the main DB) and links them to tenants with `tenant_id`. This is consistent with keeping `SubscriptionPlan` centralized and allows the admin to manage plans centrally while tenants subscribe.
- If you prefer per-tenant subscription tables, you can instead move subscription storage to tenant databases; that requires changing how Cashier/Billable resolves connections (not covered here).

Notes for Copilot / future devs

- The `tenant_id` is a string (supports subdomain slug or tenant identifier). When writing code that filters subscriptions by tenant, always use the same identifier format (e.g., `tenant()->id` or `tenant()->domains()->first()->domain`).
- When adding or modifying migrations for central tables that relate to tenants, add a similar `tenant_id` column when necessary and include backfill instructions.
- When patching code that creates subscriptions, prefer explicitly setting `tenant_id` right after subscription creation to avoid race conditions.

Contact

If you want, I can also add a small observer that fills `tenant_id` automatically on subscription creation (and create a small backfill command). Say so and I'll implement it next.
