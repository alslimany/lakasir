# Multitenancy SaaS Architecture

## System Architecture Overview

```
┌─────────────────────────────────────────────────────────────────┐
│                         LAKASIR POS SAAS                         │
└─────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│                          USER ACCESS                             │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  ┌──────────────────┐      ┌──────────────────┐                │
│  │  Admin Domain    │      │  Tenant Domain   │                │
│  │ admin.lakasir.com│      │ tenant1.lakasir  │                │
│  │                  │      │ .com             │                │
│  └────────┬─────────┘      └────────┬─────────┘                │
│           │                         │                           │
└───────────┼─────────────────────────┼───────────────────────────┘
            │                         │
            ▼                         ▼
┌───────────────────────┐   ┌───────────────────────┐
│   Admin Panel         │   │   Tenant Panel        │
│   (Super Admin)       │   │   (Tenant Users)      │
├───────────────────────┤   ├───────────────────────┤
│ - Tenant Management   │   │ - POS Operations      │
│ - Plan Management     │   │ - Product Management  │
│ - Analytics           │   │ - Sales Reports       │
│ - Suspend/Activate    │   │ - Billing Portal      │
└───────────┬───────────┘   └───────────┬───────────┘
            │                           │
            └───────────┬───────────────┘
                        │
                        ▼
        ┌───────────────────────────────┐
        │   Application Layer            │
        ├───────────────────────────────┤
        │                                │
        │  ┌──────────────────────────┐ │
        │  │  Tenancy Middleware      │ │
        │  │  - Domain Resolution     │ │
        │  │  - Tenant Identification │ │
        │  │  - Context Switching     │ │
        │  └──────────────────────────┘ │
        │                                │
        │  ┌──────────────────────────┐ │
        │  │  Subscription Middleware │ │
        │  │  - Status Check          │ │
        │  │  - Trial Verification    │ │
        │  │  - Feature Gating        │ │
        │  └──────────────────────────┘ │
        │                                │
        └────────────────┬───────────────┘
                         │
                         ▼
        ┌───────────────────────────────┐
        │   Business Logic Layer        │
        ├───────────────────────────────┤
        │                                │
        │  ┌────────────────┐            │
        │  │ Tenant Model   │            │
        │  │ - Billable     │            │
        │  │ - Subscription │            │
        │  │ - Usage Limits │            │
        │  └────────────────┘            │
        │                                │
        │  ┌────────────────┐            │
        │  │ Subscription   │            │
        │  │ Service        │            │
        │  │ - Create       │            │
        │  │ - Change Plan  │            │
        │  │ - Cancel       │            │
        │  └────────────────┘            │
        │                                │
        │  ┌────────────────┐            │
        │  │ Usage Tracking │            │
        │  │ - Products     │            │
        │  │ - Users        │            │
        │  │ - Storage      │            │
        │  └────────────────┘            │
        │                                │
        └────────────────┬───────────────┘
                         │
                         ▼
        ┌───────────────────────────────┐
        │   Data Layer                  │
        ├───────────────────────────────┤
        │                                │
        │  ┌──────────────────────────┐ │
        │  │  Central Database        │ │
        │  │  - tenants               │ │
        │  │  - domains               │ │
        │  │  - subscription_plans    │ │
        │  │  - tenant_usage          │ │
        │  │  - admins                │ │
        │  │  - subscriptions (Cash.) │ │
        │  └──────────────────────────┘ │
        │                                │
        │  ┌──────────────────────────┐ │
        │  │  Tenant Databases        │ │
        │  │  lakasir_tenant1         │ │
        │  │  - users                 │ │
        │  │  - products              │ │
        │  │  - sales                 │ │
        │  │  - customers             │ │
        │  │  - ... (tenant data)     │ │
        │  └──────────────────────────┘ │
        │                                │
        │  ┌──────────────────────────┐ │
        │  │  lakasir_tenant2         │ │
        │  │  - users                 │ │
        │  │  - products              │ │
        │  │  - ... (tenant data)     │ │
        │  └──────────────────────────┘ │
        │                                │
        └────────────────┬───────────────┘
                         │
                         ▼
        ┌───────────────────────────────┐
        │   External Services           │
        ├───────────────────────────────┤
        │                                │
        │  ┌──────────────────────────┐ │
        │  │  Stripe API              │ │
        │  │  - Subscriptions         │ │
        │  │  - Payments              │ │
        │  │  - Invoices              │ │
        │  │  - Webhooks              │ │
        │  └──────────────────────────┘ │
        │                                │
        └────────────────────────────────┘
```

## Data Flow Diagrams

### 1. Tenant Registration Flow

```
User Registration
       │
       ▼
┌────────────────┐
│ Register Form  │
│ - Email        │
│ - Password     │
│ - Domain       │
└───────┬────────┘
        │
        ▼
┌────────────────────┐
│ RegisterTenant     │
│ Service            │
└────────┬───────────┘
         │
         ├──► Create Tenant Record
         │    - id: domain_name
         │    - email
         │    - trial_ends_at: +14 days
         │    - is_active: true
         │
         ├──► Create Domain
         │    - domain: tenant.lakasir.com
         │
         ├──► Create Tenant Database
         │    - lakasir_tenant_name
         │
         ├──► Run Migrations
         │
         ├──► Create Owner User
         │    (in tenant database)
         │
         ├──► Create Usage Record
         │    - product_count: 0
         │    - user_count: 0
         │    - storage_used: 0
         │
         └──► Seed Default Data
              - Permissions
              - Payment Methods
              - Categories
```

### 2. Subscription Creation Flow

```
Tenant Chooses Plan
         │
         ▼
┌────────────────────┐
│ Billing Page       │
│ - Display Plans    │
│ - Select Plan      │
└────────┬───────────┘
         │
         ▼
┌────────────────────┐
│ SubscriptionService│
│ .createSubscription│
└────────┬───────────┘
         │
         ├──► Check/Create Stripe Customer
         │    - createAsStripeCustomer()
         │
         ├──► Create Subscription in Stripe
         │    - newSubscription('default', price_id)
         │
         ├──► Redirect to Stripe Checkout
         │
         ▼
┌────────────────────┐
│ Stripe Checkout    │
│ - Enter Card Info  │
│ - Confirm Payment  │
└────────┬───────────┘
         │
         ▼
┌────────────────────┐
│ Stripe Webhook     │
│ subscription.created│
└────────┬───────────┘
         │
         ├──► Update Tenant
         │    - is_active: true
         │    - subscription_plan_id
         │
         ├──► Calculate Usage
         │
         └──► Send Confirmation Email
```

### 3. Feature Access Check Flow

```
User Action (e.g., Create Product)
         │
         ▼
┌────────────────────┐
│ CheckTenantStatus  │
│ Middleware         │
└────────┬───────────┘
         │
         ├──► Is tenant active?
         │    ├─ No ──► Show Suspended Page
         │    └─ Yes ──► Continue
         │
         ├──► Has active subscription/trial?
         │    ├─ No ──► Redirect to Billing
         │    └─ Yes ──► Continue
         │
         ▼
┌────────────────────┐
│ Check Feature Limit│
│ hasReachedLimit()  │
└────────┬───────────┘
         │
         ├──► Get Plan Limit
         │
         ├──► Get Current Usage
         │
         ├──► Compare
         │    ├─ At Limit ──► Show Upgrade Prompt
         │    └─ Below ──► Allow Action
         │
         ▼
┌────────────────────┐
│ Execute Action     │
└────────┬───────────┘
         │
         ├──► Create Record
         │
         └──► Update Usage Count
              (via Observer)
```

### 4. Usage Tracking Flow

```
Model Event (Create/Delete)
         │
         ▼
┌────────────────────┐
│ Model Observer     │
│ - ProductObserver  │
│ - UserObserver     │
└────────┬───────────┘
         │
         ▼
┌────────────────────┐
│ updateUsageCount() │
└────────┬───────────┘
         │
         ├──► Get Tenant Context
         │
         ├──► Count Records
         │    - Product::count()
         │    - User::count()
         │
         ├──► Update tenant_usage
         │    - product_count
         │    - user_count
         │    - last_calculated_at
         │
         └──► Save Changes
```

### 5. Webhook Processing Flow

```
Stripe Event
         │
         ▼
┌────────────────────┐
│ POST /stripe/      │
│      webhook       │
└────────┬───────────┘
         │
         ├──► Verify Signature
         │
         ▼
┌─────────────────────────┐
│ StripeWebhookController │
└─────────┬───────────────┘
          │
          ├─ subscription.created
          │  └──► Activate Tenant
          │
          ├─ subscription.updated
          │  └──► Update Status
          │
          ├─ subscription.deleted
          │  └──► Deactivate Tenant
          │
          ├─ invoice.payment_succeeded
          │  └──► Send Success Notification
          │
          └─ invoice.payment_failed
             └──► Send Failed Notification
```

## Component Relationships

```
┌────────────────────────────────────────────────┐
│                  Tenant                         │
│  ┌──────────────────────────────────────────┐ │
│  │ - id                                     │ │
│  │ - is_active                              │ │
│  │ - trial_ends_at                          │ │
│  │ - subscription_plan_id (FK)              │ │
│  │ - stripe_id                              │ │
│  └──────────────────────────────────────────┘ │
│                                                 │
│  Relationships:                                 │
│  ├──► domains (HasMany)                        │
│  ├──► subscriptionPlan (BelongsTo)             │
│  └──► usage (HasOne)                           │
│                                                 │
│  Methods:                                       │
│  ├─ onTrial()                                  │
│  ├─ hasActiveSubscription()                    │
│  ├─ canUseFeature(feature)                     │
│  ├─ hasReachedLimit(feature)                   │
│  └─ getRemainingFeatureCount(feature)          │
└────────────────────────────────────────────────┘
                    │
        ┌───────────┼───────────┐
        │           │           │
        ▼           ▼           ▼
┌──────────┐ ┌──────────┐ ┌──────────┐
│  Domain  │ │   Plan   │ │  Usage   │
├──────────┤ ├──────────┤ ├──────────┤
│- domain  │ │- name    │ │- product │
│          │ │- price   │ │  _count  │
│          │ │- features│ │- user    │
│          │ │          │ │  _count  │
│          │ │          │ │- storage │
└──────────┘ └──────────┘ └──────────┘
```

## Security Layers

```
┌────────────────────────────────────────┐
│         Request Processing             │
└────────────────────────────────────────┘
              │
              ▼
┌────────────────────────────────────────┐
│  Layer 1: Domain Resolution            │
│  - Identify tenant from subdomain      │
│  - Load tenant context                 │
└────────────────┬───────────────────────┘
                 │
                 ▼
┌────────────────────────────────────────┐
│  Layer 2: Tenant Status Check          │
│  - Verify tenant is_active             │
│  - Check suspension status             │
└────────────────┬───────────────────────┘
                 │
                 ▼
┌────────────────────────────────────────┐
│  Layer 3: Subscription Verification    │
│  - Check active subscription           │
│  - Verify trial period                 │
└────────────────┬───────────────────────┘
                 │
                 ▼
┌────────────────────────────────────────┐
│  Layer 4: Feature Authorization        │
│  - Verify feature access               │
│  - Check usage limits                  │
└────────────────┬───────────────────────┘
                 │
                 ▼
┌────────────────────────────────────────┐
│  Layer 5: Database Isolation           │
│  - Switch to tenant database           │
│  - Execute query in tenant context     │
└────────────────────────────────────────┘
```

## Deployment Architecture

```
┌─────────────────────────────────────────────┐
│             Load Balancer                    │
└─────────────┬───────────────────────────────┘
              │
    ┌─────────┼─────────┐
    │         │         │
    ▼         ▼         ▼
┌────────┐ ┌────────┐ ┌────────┐
│  Web   │ │  Web   │ │  Web   │
│ Server │ │ Server │ │ Server │
│   1    │ │   2    │ │   3    │
└───┬────┘ └───┬────┘ └───┬────┘
    │          │          │
    └──────────┼──────────┘
               │
               ▼
┌──────────────────────────────────┐
│        Application Layer          │
│  - Laravel App                    │
│  - Tenancy Package                │
│  - Cashier                        │
└──────────┬───────────────────────┘
           │
    ┌──────┼──────┐
    │      │      │
    ▼      ▼      ▼
┌─────┐ ┌─────┐ ┌─────┐
│ DB  │ │Redis│ │Queue│
│Multi│ │Cache│ │     │
└─────┘ └─────┘ └─────┘
    │
    ├─ Central DB
    ├─ Tenant DB 1
    ├─ Tenant DB 2
    └─ Tenant DB N
```

This architecture ensures:
- **Scalability**: Horizontal scaling via load balancer
- **Isolation**: Database-level tenant separation
- **Security**: Multiple middleware layers
- **Performance**: Caching and queue processing
- **Reliability**: Multiple web servers

