<style>
    /* RTL Support */
    html[dir="rtl"], body[dir="rtl"] {
        direction: rtl;
        text-align: right;
    }

    html[dir="rtl"] .fi-sidebar {
        right: 0;
        left: auto;
    }

    html[dir="rtl"] .fi-main {
        margin-right: 16rem;
        margin-left: 0;
    }

    html[dir="rtl"] .fi-topbar {
        padding-right: 1rem;
        padding-left: 0;
    }

    html[dir="rtl"] .fi-sidebar-nav-item {
        text-align: right;
    }

    html[dir="rtl"] .fi-form .fi-fo-field-wrp {
        direction: rtl;
    }

    html[dir="rtl"] .fi-ta-text-item {
        text-align: right;
    }

    html[dir="rtl"] .fi-breadcrumbs {
        direction: rtl;
    }

    html[dir="rtl"] .fi-breadcrumbs-item::after {
        transform: rotate(180deg);
    }

    /* Fix for dropdowns and modals */
    html[dir="rtl"] .fi-dropdown-list {
        direction: rtl;
        text-align: right;
    }

    html[dir="rtl"] .fi-modal {
        direction: rtl;
    }

    /* Fix for tables */
    html[dir="rtl"] table {
        direction: rtl;
    }

    html[dir="rtl"] th, 
    html[dir="rtl"] td {
        text-align: right;
    }

    /* Fix for icons */
    html[dir="rtl"] .fi-sidebar-item-icon {
        margin-left: 0.75rem;
        margin-right: 0;
    }

    /* Fix for forms */
    html[dir="rtl"] .fi-fo-field-wrp-label {
        text-align: right;
    }

    html[dir="rtl"] input[type="text"],
    html[dir="rtl"] input[type="email"],
    html[dir="rtl"] input[type="password"],
    html[dir="rtl"] input[type="number"],
    html[dir="rtl"] textarea,
    html[dir="rtl"] select {
        direction: rtl;
        text-align: right;
    }

    /* Fix for notifications */
    html[dir="rtl"] .fi-no-notification {
        right: auto;
        left: 1rem;
    }

    /* Fix for badges */
    html[dir="rtl"] .fi-badge {
        direction: rtl;
    }

    /* Fix padding and margins for RTL */
    html[dir="rtl"] .fi-header-actions,
    html[dir="rtl"] .fi-header-heading {
        padding-right: 0;
        padding-left: 1rem;
    }

    html[dir="rtl"] .fi-btn {
        text-align: center;
    }

    html[dir="rtl"] .fi-btn-icon {
        margin-right: 0;
        margin-left: 0.5rem;
    }

    html[dir="rtl"] .fi-section-header {
        text-align: right;
    }

    html[dir="rtl"] .fi-card {
        text-align: right;
    }

    /* Fix margins in grid layouts */
    html[dir="rtl"] .grid > * {
        margin-right: 0;
        margin-left: 0;
    }

    /* Fix page header pluralization issue - hide trailing 's' */
    html[dir="rtl"] .fi-header-heading::after {
        content: '';
        display: none;
    }

    /* Sidebar when collapsed on RTL */
    @media (max-width: 1023px) {
        html[dir="rtl"] .fi-sidebar {
            transform: translateX(100%);
        }

        html[dir="rtl"] .fi-sidebar.open {
            transform: translateX(0);
        }
    }
</style>
