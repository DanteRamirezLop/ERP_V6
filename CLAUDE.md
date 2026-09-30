# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Overview

ERP v6 built on **Ultimate POS** (Laravel 9, PHP ^8.0). Multi-tenant POS/ERP where every record is scoped by `business_id`. UI is server-rendered Blade (AdminLTE) with jQuery + Yajra DataTables; frontend assets are prebuilt in `public/` (there is no root `package.json`). The team works in Spanish — commit messages, UI copy and `lang/es` translations are primarily Spanish.

## Commands

```bash
composer install
php artisan serve
php artisan migrate                                   # core migrations (database/migrations)
php artisan module:migrate Crm                        # a single module's migrations
php artisan migrate --path=Modules/Crm/Database/Migrations/<file>.php   # one specific migration
php artisan module:make-controller FooController Crm  # nwidart/laravel-modules generators
vendor/bin/phpunit                                    # tests (only example tests exist)
vendor/bin/phpunit --filter SomeTest                  # single test
```

Code style: PSR-2 with short arrays and alphabetically ordered, unused-free imports (`.php_cs`); Prettier for JS (4 spaces, single quotes, width 100).

## Architecture

### Core app (`app/`)
- **Models live directly in `app/`** (e.g. `App\Transaction`, `App\Product`), not `app/Models`.
- **Business logic lives in `app/Utils/*Util.php`** (`TransactionUtil`, `ProductUtil`, `BusinessUtil`, `ModuleUtil`, …), injected into controllers via constructor. Prefer extending these over putting logic in controllers.
- `Transaction` is the central table: sells, purchases, expenses, returns, stock transfers/adjustments, etc. are all rows distinguished by `type`.
- Global helpers are in `app/Http/helpers.php` (autoloaded).

### Request pipeline & tenancy
- Authenticated routes in `routes/web.php` use the middleware stack `setData, auth, SetSessionData, language, timezone, AdminSidebarMenu, CheckUserLogin`.
- `SetSessionData` puts `user`, `business`, `currency`, `financial_year` into the session. Controllers get the tenant with `request()->session()->get('user.business_id')` and **must** filter every query by it.
- Authorization uses spatie/laravel-permission: `auth()->user()->can('...')` checks followed by `abort(403, 'Unauthorized action.')`. When the Superadmin module is installed, feature access also depends on the subscription package (`ModuleUtil::hasThePermissionInSubscription`).
- Standard controller convention: wrap writes in try/catch, log with `\Log::emergency('File:'...' Line:'...' Message:'...)`, return `['success' => bool, 'msg' => __(...)]` for AJAX requests (consumed by toastr in the JS), otherwise redirect with `->with('status', $output)`. Create/edit forms are usually loaded as AJAX modals; index pages are Yajra DataTables fed by the same `index` action when `request()->ajax()`.

### Modules (`Modules/`, nwidart/laravel-modules)
- Each module (Crm, Accounting, Essentials, Superadmin, Manufacturing, Repair, Project, …) is a mini-app with `Config`, `Database/Migrations`, `Entities` (models), `Http/Controllers`, `Resources/{views,lang}`, `Routes/web.php`. Views are referenced as `crm::path.view`, translations as `__('crm::lang.key')`.
- Enable/disable state is in `modules_statuses.json`, but a module is only considered **installed** when the `system` table has a `<module>_version` property (`ModuleUtil::isModuleInstalled`). Each module's `InstallController` performs installation; its version is in `Modules/<X>/Config/config.php` (`module_version`).
- **Core ↔ module integration is via hooks**: core code calls `ModuleUtil::getModuleData('hook_name', $args)`, which invokes the method of the same name on each installed module's `Http/Controllers/DataController.php` (e.g. `modifyAdminMenu`, `user_permissions`, `superadmin_package`, `get_contact_view_tabs`, `after_contact_saved`, `calendarEvents`). To add menu entries, permissions or tabs from a module, implement these in its `DataController` rather than editing core.
- Some modules listed in `modules_statuses.json` have no directory in this repo.

### Localization
Core strings are in `lang/<locale>/*.php` (e.g. `lang_v1`, `messages`); module strings in `Modules/<X>/Resources/lang/<locale>/lang.php`. When adding keys, add them to both `en` and `es`.

## Deployment quirk

Production appears to lack shell access: migrations are sometimes run through temporary closure routes at the top of `routes/web.php` that call `Artisan::call('migrate', ['--path' => ..., '--force' => true])`. Be aware these exist (and are unauthenticated) when editing that file; don't add new ones unless asked.
