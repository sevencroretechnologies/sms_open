# Smart School Management System - Quick Start Guide

## Overview

This guide provides step-by-step instructions to quickly set up and start developing the Smart School Management System using PHP/Laravel.

## Prerequisites

### Required Software
- **PHP**: 8.2 or higher
- **Composer**: Latest version
- **MySQL**: 8.0+ or PostgreSQL 14+
- **Redis**: Latest version
- **Node.js**: 18+ and npm
- **Git**: Latest version

### Optional but Recommended
- **Laravel Valet** (macOS) or **Laravel Sail** (Docker)
- **Postman** or **Insomnia** (for API testing)
- **VS Code** with Laravel extensions

## Step 1: Project Setup

### 1.1 Create New Laravel Project

```bash
# Create new Laravel project
composer create-project laravel/laravel smart-school

# Navigate to project directory
cd smart-school
```

### 1.2 Configure Environment

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 1.3 Update `.env` File

```env
APP_NAME="Smart School"
APP_ENV=local
APP_KEY=base64:your-generated-key
APP_DEBUG=true
APP_URL=http://localhost:8000

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=smart_school
DB_USERNAME=root
DB_PASSWORD=

BROADCAST_DRIVER=log
CACHE_DRIVER=redis
FILESYSTEM_DISK=local
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
SESSION_LIFETIME=120

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"
```

## Step 2: Install Core Dependencies

### 2.1 Authentication & Authorization

```bash
# Install Laravel Breeze
composer require laravel/breeze --dev

# Install Breeze scaffolding
php artisan breeze:install blade

# Install Spatie Permission
composer require spatie/laravel-permission

# Publish and run migrations
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan migrate
```

### 2.2 Additional Packages

```bash
# Install Laravel Sanctum for API
composer require laravel/sanctum
php artisan sanctum:install

# Install Laravel Backup
composer require spatie/laravel-backup
php artisan vendor:publish --provider="Spatie\Backup\BackupServiceProvider"

# Install Laravel Excel
composer require maatwebsite/excel
php artisan vendor:publish --provider="Maatwebsite\Excel\ExcelServiceProvider" --tag=config

# Install DomPDF
composer require barryvdh/laravel-dompdf
php artisan vendor:publish --provider="Barryvdh\DomPDF\ServiceProvider"

# Install Intervention Image
composer require intervention/image
php artisan vendor:publish --provider="Intervention\Image\ImageServiceProviderLaravelRecent"
```

### 2.3 Frontend Dependencies

```bash
# Install npm packages
npm install

# Install Chart.js
npm install chart.js

# Install Alpine.js
npm install alpinejs

# Install additional packages
npm install axios sweetalert2 select2
```

## Step 3: Database Setup

### 3.1 Create Database

```sql
-- Create MySQL database
CREATE DATABASE smart_school CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 3.2 Run Migrations

```bash
# Run all migrations
php artisan migrate

# Run seeders
php artisan db:seed
```

## Step 4: Project Structure Setup

### 4.1 Create Modular Structure

```bash
# Create modules directory
mkdir -p app/Modules/{Student,Academic,Attendance,Examination,Fees,Library,Transport,Hostel,Communication,Accounting}

# Create services directory
mkdir -p app/Services

# Create repositories directory
mkdir -p app/Repositories

# Create interfaces directory
mkdir -p app/Interfaces
```

### 4.2 Create Base Classes

```bash
# Create base controller
php artisan make:controller Controller/BaseController

# Create base model
php artisan make:model Model/BaseModel
```

## Step 5: Initial Configuration

### 5.1 Configure Authentication

```bash
# Create roles and permissions seeder
php artisan make:seeder RoleSeeder
php artisan make:seeder PermissionSeeder
php artisan make:seeder AdminUserSeeder
```

### 5.2 Run Seeders

```bash
# Run all seeders
php artisan db:seed --class=RoleSeeder
php artisan db:seed --class=PermissionSeeder
php artisan db:seed --class=AdminUserSeeder
```

## Step 6: Create Basic Models

### 6.1 Create Core Models

```bash
# Create user model with relationships
php artisan make:model User

# Create role and permission models
php artisan make:model Role
php artisan make:model Permission

# Create academic models
php artisan make:model AcademicSession
php artisan make:model Class
php artisan make:model Section
php artisan make:model Subject

# Create student model
php artisan make:model Student
```

## Step 7: Create Migrations

### 7.1 Create Migration Files

```bash
# Create users and roles migrations
php artisan make:migration create_users_table
php artisan make:migration create_roles_table
php artisan make:migration create_permissions_table

# Create academic structure migrations
php artisan make:migration create_academic_sessions_table
php artisan make:migration create_classes_table
php artisan make:migration create_sections_table
php artisan make:migration create_subjects_table

# Create student management migrations
php artisan make:migration create_students_table
php artisan make:migration create_student_siblings_table
php artisan make:migration create_student_documents_table
```

### 7.2 Run Migrations

```bash
# Run all migrations
php artisan migrate
```

## Step 8: Create Controllers

### 8.1 Create Controllers

```bash
# Create authentication controllers
php artisan make:controller AuthController

# Create admin controllers
php artisan make:controller Admin/DashboardController
php artisan make:controller Admin/UserController
php artisan make:controller Admin/SettingController

# Create student controllers
php artisan make:controller Student/StudentController
php artisan make:controller Student/AdmissionController
```

## Step 9: Create Views

### 9.1 Create Layout

```bash
# Create views directory structure
mkdir -p resources/views/layouts
mkdir -p resources/views/auth
mkdir -p resources/views/admin
mkdir -p resources/views/student
mkdir -p resources/views/components
```

### 9.2 Create Base Layout

Create `resources/views/layouts/app.blade.php`:
```blade
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Smart School') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">
    @include('layouts.navigation')
    
    <main class="py-6">
        @yield('content')
    </main>
    
    @include('layouts.footer')
</body>
</html>
```

## Step 10: Configure Routes

### 10.1 Update Routes File

Update `routes/web.php`:
```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardController;

// Authentication routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Admin routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

// Home route
Route::get('/', function () {
    return redirect()->route('login');
});
```

## Step 11: Development Server

### 11.1 Start Development Server

```bash
# Start Laravel development server
php artisan serve

# Access application at http://localhost:8000
```

### 11.2 Start Redis (if not running)

```bash
# Start Redis server
redis-server
```

### 11.3 Start Queue Worker

```bash
# Start queue worker in separate terminal
php artisan queue:work
```

## Step 12: Testing

### 12.1 Create Test User

```bash
# Create admin user using tinker
php artisan tinker

>>> $user = \App\Models\User::create([
...     'first_name' => 'Admin',
...     'last_name' => 'User',
...     'email' => 'admin@example.com',
...     'password' => bcrypt('password'),
... ]);
>>> $user->assignRole('admin');
```

### 12.2 Login and Test

1. Visit `http://localhost:8000/login`
2. Login with credentials:
   - Email: `admin@example.com`
   - Password: `password`
3. Verify dashboard loads correctly

## Step 13: Next Steps

### 13.1 Implement Core Modules

Follow the implementation roadmap to build:

1. **Student Management Module** (Week 7-8)
2. **Academic Management Module** (Week 9)
3. **Attendance Management Module** (Week 10)
4. **Examination Management Module** (Week 11-12)
5. **Fees Management Module** (Week 13-14)

### 13.2 Configure Third-Party Services

- **Payment Gateways**: Razorpay, PayPal
- **SMS Gateway**: Twilio, Clickatell
- **Email Services**: SMTP, SendGrid
- **Cloud Storage**: AWS S3, DigitalOcean Spaces

### 13.3 Implement Features

Refer to the implementation roadmap for detailed steps:
- `plans/school-management-implementation-roadmap.md`

## Common Commands

### Development Commands

```bash
# Run migrations
php artisan migrate

# Rollback migrations
php artisan migrate:rollback

# Fresh migration with seeding
php artisan migrate:fresh --seed

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Create controller
php artisan make:controller ControllerName

# Create model
php artisan make:model ModelName

# Create migration
php artisan make:migration migration_name

# Create seeder
php artisan make:seeder SeederName

# Run specific seeder
php artisan db:seed --class=SeederName

# Start development server
php artisan serve

# Start queue worker
php artisan queue:work

# Run tests
php artisan test

# Generate IDE helper
php artisan ide-helper:generate
```

### NPM Commands

```bash
# Install dependencies
npm install

# Run development server
npm run dev

# Build for production
npm run build

# Watch for changes
npm run watch
```

## Troubleshooting

### Common Issues

#### 1. Database Connection Error
```bash
# Check database credentials in .env
# Ensure MySQL is running
# Verify database exists
```

#### 2. Redis Connection Error
```bash
# Ensure Redis is running
redis-cli ping  # Should return PONG

# Check Redis configuration in .env
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
```

#### 3. Permission Issues
```bash
# Set proper permissions
chmod -R 775 storage bootstrap/cache
```

#### 4. Composer Issues
```bash
# Clear composer cache
composer clear-cache

# Update composer
composer self-update
```

## Development Workflow

### 1. Feature Development

```bash
# Create feature branch
git checkout -b feature/feature-name

# Make changes
# ...

# Run tests
php artisan test

# Commit changes
git add .
git commit -m "Add feature description"

# Push to remote
git push origin feature/feature-name
```

### 2. Code Quality

```bash
# Run PHP CS Fixer
./vendor/bin/php-cs-fixer fix

# Run PHPStan
./vendor/bin/phpstan analyse

# Run tests
php artisan test
```

## Useful Resources

### Documentation
- [Laravel Documentation](https://laravel.com/docs)
- [Laravel Breeze](https://laravel.com/docs/starter-kits)
- [Spatie Permission](https://spatie.be/docs/laravel-permission)
- [Laravel Excel](https://laravel-excel.com)
- [DomPDF](https://github.com/barryvdh/laravel-dompdf)

### Community
- [Laravel Forums](https://laracasts.com/discuss)
- [Stack Overflow](https://stackoverflow.com/questions/tagged/laravel)
- [Laravel Discord](https://discord.gg/laravel)

## Deployment Checklist

### Pre-Deployment
- [ ] Set `APP_ENV=production` in `.env`
- [ ] Set `APP_DEBUG=false`
- [ ] Generate application key
- [ ] Set proper database credentials
- [ ] Configure mail settings
- [ ] Configure file storage
- [ ] Set up SSL certificate
- [ ] Configure backup schedule
- [ ] Set up monitoring
- [ ] Test all features

### Production Deployment
- [ ] Deploy code to server
- [ ] Run migrations
- [ ] Run seeders (if needed)
- [ ] Clear cache
- [ ] Optimize application
- [ ] Set up queue workers
- [ ] Configure cron jobs
- [ ] Test application
- [ ] Monitor for issues

## Support

For issues or questions:
1. Check the documentation
2. Review the implementation roadmap
3. Check the database schema
4. Refer to Laravel documentation
5. Ask in community forums

## Conclusion

This quick start guide provides the foundation for building the Smart School Management System. Follow the implementation roadmap for detailed steps to build each module. The modular design allows for independent development of different features while maintaining a cohesive system architecture.

Happy coding! 🚀
