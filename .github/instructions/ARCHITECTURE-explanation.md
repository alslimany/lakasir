# ARCHITECTURE.md — Explanation & Copilot Guidance

Purpose
- High-level architecture documentation for the Multitenancy SaaS system.

Key points
- Database-per-tenant approach using `stancl/tenancy`.
- Central domain for admin and tenant subdomains for tenant panels.
- Subscription lifecycle: trial, active, expired, with middleware gating.
- External integrations: Stripe, Moamalat, webhooks.

Copilot tasks
- When modifying tenancy or billing flows, consult this file for system-level impact.
- Use the diagrams to ensure new components follow the same layering.

Verification
- Confirm that `tenancy()` helper is used when switching contexts.
- Ensure webhook routes and handlers align with architecture expectations.