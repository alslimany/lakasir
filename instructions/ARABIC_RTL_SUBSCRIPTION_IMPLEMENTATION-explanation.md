# ARABIC_RTL_SUBSCRIPTION_IMPLEMENTATION.md — Explanation & Copilot Guidance

Purpose
- Summarizes the Arabic/RTL implementation and subscription enhancements.

Key points
- RTL styles are in `resources/views/filament/rtl-styles.blade.php` and POS-specific RTL fixes in `resources/views/filament/tenant/pages/pos/index.blade.php`.
- Middleware `app/Http/Middleware/CheckTenantStatus.php` allows billing page access on expired subscriptions.
- Billing page `app/Filament/Tenant/Pages/Billing.php` includes expiry detection and translated UI.
- `lang/ar.json` contains 45+ new translations.
- Registration flow updated to accept subscription plan selection.

Copilot tasks
- Use this file when changing translations, RTL styles, or subscription flow behavior.
- When adding new tenancy or billing UI, ensure keys appear in `lang/ar.json` and `lang/en.json`.

Verification
- Switch to Arabic locale and verify UI direction and translated strings.
- Confirm `modelLabel()` usage for Filament tables when empty states include `:model`.