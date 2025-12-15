# MULTITENANCY_SAAS.md — Explanation & Copilot Guidance

Purpose
- Document and guide on the multitenancy SaaS architecture used in Lakasir.

Key points
- stancl/tenancy is used for database-per-tenant separation.
- Subscription plans, trial, and feature gating summarized.
- Setup instructions for environment, migrations, and seeding subscription plans.

Copilot tasks
- Consult before modifying tenant lifecycle logic or adding tenant-scoped features.
- When adding new migrations related to tenants, ensure `tenants:migrate` vs `migrate` guidance is clear.