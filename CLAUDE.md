# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Development Commands

### Setup
- `composer install` - Install PHP dependencies
- `cp .env.example .env` - Copy environment configuration
- `php artisan key:generate` - Generate application key
- Update `.env` file with `DB_DATABASE` path to `{PROJECT_ROOT}/database/dev.database.sqlite`

### Development Server
- `php artisan serve` - Start development server (runs on localhost:8000)

### Testing
- `vendor/bin/phpunit` or `./unit.bat` - Run unit and integration tests
- `php artisan dusk` - Run browser tests (Laravel Dusk)

### Asset Compilation
- `npm run dev` - Compile assets for development
- `npm run watch` - Watch files and recompile on changes
- `npm run production` - Compile assets for production

## Architecture Overview

This is a Laravel 5.4 appointment booking system for businesses to manage employee schedules and customer bookings.

### Core Models & Relationships
- **BusinessOwner**: Manages business and employees
- **Employee**: Works for business, has working times and bookings
- **Customer**: Creates bookings for activities
- **Booking**: Links customer, employee, activity with date/time
- **Activity**: Services offered by business (has duration)
- **WorkingTime**: Employee availability schedules
- **BusinessTime**: Business operating hours

### Authentication System
- Dual authentication for BusinessOwner and Customer models
- Custom session handling in `Auth\SessionController`
- Both models implement `Authenticatable` interface

### Key Business Logic
- Booking time calculations in `Booking::calcEndTime()` 
- Employee availability matching in `Booking::getWorkableBookingsForEmployee()`
- Calendar-based views for roster and booking management
- Month/year routing pattern for time-based views

### Database
- Uses SQLite for development (`database/dev.database.sqlite`)
- Migrations in `database/migrations/`
- Timezone set to Australia/Melbourne

### Helper System
- Custom helpers registered via `HelperServiceProvider`
- Located in `app/Helpers/` (Auth, DateTime, String utilities)
- Global helper functions available throughout application

### View Structure
- Admin views: `/admin/*` - Business owner dashboard, employee management, bookings
- Customer views: `/bookings/*` - Customer booking interface
- Shared components in `resources/views/shared/`