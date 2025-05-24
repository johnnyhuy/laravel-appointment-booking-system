## Laravel Appointment Booking System — Agent Context

#### Main Features
- Register as a business owner or customer
- Customer booking, viewing, and management
- Business owner management of business info, roster, employees, activities, and bookings
- Admin views for summary, activity, booking history, and more
- Employee assignment and working times

#### Core Tech & Stack
- Laravel 5.4 (PHP framework)
- PHP >= 5.6.4
- Composer for PHP dependency management
- MySQL (production), SQLite (local dev)
- [Laravel Dusk](https://laravel.com/docs/5.4/dusk) for browser/UI testing

#### Key Codebase Structure
```
├── app/                 # App logic: models, controllers, helpers
│   ├── Http/Controllers/ # Main controllers: Booking, BusinessOwner, Auth, etc
│   ├── Helpers/         # Utility/helper functions (Auth, DateTime, String)
│   └── ...
├── resources/views/     # Blade templates for admin, customer, layout, shared
├── routes/              # Route files, notably web.php (main site)
├── database/            # Migrations, seeds, SQLite files
├── public/              # Entry (index.php), public assets (CSS, JS, etc)
├── tests/               # Browser, Integration, and Unit tests
├── config/              # Laravel config files
├── composer.json        # PHP/Laravel dependency manifest
└── README.md
```

#### Run/Setup Instructions
See documentation in `README.md` for step-by-step info; highlights:
1. Copy `.env.example` to `.env` and set up DB (SQLite for local, MySQL for production)
2. Run `composer install`
3. Generate app key: `php artisan key:generate`
4. Run: `php artisan serve` to start the dev webserver

#### Common Extension Points
- To add logic/UI for new booking features: edit controllers in `app/Http/Controllers` & Blade templates in `resources/views`
- For DB changes: add/modify migrations under `database/migrations`
- For custom helpers: see `app/Helpers`

#### Testing
Test suites are separated into:
- Feature/UI (Dusk): `tests/Browser`
- Integration: `tests/Integration`
- Unit: `tests/Unit`
Use `php artisan dusk`, or `phpunit`, as appropriate.

#### Maintainers/Ownership
See `README.md` for developer credits. Current owner: johnnyhuy

---
This `AGENT.md` is for agents and new contributors who need a mental model of the repo layout, core technology, and where to add or modify features in the Laravel Appointment Booking System.