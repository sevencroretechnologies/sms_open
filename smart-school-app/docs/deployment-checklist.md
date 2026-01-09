# Deployment Checklist

**Prompt 510: Create Deployment Checklist**

This checklist ensures a smooth deployment of the Smart School Management System.

## Pre-Deployment

### Environment Setup
- [ ] Verify server meets minimum requirements (PHP 8.2+, MySQL 8.0+/SQLite)
- [ ] Configure web server (Apache/Nginx)
- [ ] Set up SSL certificate for HTTPS
- [ ] Configure DNS records

### Application Configuration
- [ ] Copy `.env.example` to `.env`
- [ ] Generate application key: `php artisan key:generate`
- [ ] Configure database connection
- [ ] Set `APP_ENV=production`
- [ ] Set `APP_DEBUG=false`
- [ ] Configure mail settings
- [ ] Configure queue driver
- [ ] Configure cache driver
- [ ] Configure session driver

### Database
- [ ] Create production database
- [ ] Run migrations: `php artisan migrate --force`
- [ ] Run seeders (if needed): `php artisan db:seed --force`
- [ ] Verify database indexes

### Assets
- [ ] Install Node.js dependencies: `npm install`
- [ ] Build production assets: `npm run build`
- [ ] Verify assets are compiled correctly

### Storage
- [ ] Create storage link: `php artisan storage:link`
- [ ] Set proper permissions on storage directory
- [ ] Set proper permissions on bootstrap/cache directory

## Deployment Steps

### 1. Maintenance Mode
```bash
php artisan down --message="Upgrading system" --retry=60
```

### 2. Pull Latest Code
```bash
git pull origin main
```

### 3. Install Dependencies
```bash
composer install --no-dev --optimize-autoloader
npm install
npm run build
```

### 4. Run Migrations
```bash
php artisan migrate --force
```

### 5. Clear and Rebuild Caches
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

### 6. Restart Queue Workers
```bash
php artisan queue:restart
```

### 7. Warm Cache
```bash
php artisan cache:warm
```

### 8. Exit Maintenance Mode
```bash
php artisan up
```

## Post-Deployment

### Verification
- [ ] Verify application is accessible
- [ ] Test login functionality
- [ ] Test critical features (dashboard, student management)
- [ ] Check error logs for issues
- [ ] Verify queue workers are running
- [ ] Test email notifications
- [ ] Verify file uploads work

### Monitoring
- [ ] Check application health endpoint: `/api/health`
- [ ] Monitor error rates
- [ ] Monitor response times
- [ ] Check disk space usage
- [ ] Verify backup jobs are running

### Security
- [ ] Verify HTTPS is enforced
- [ ] Check security headers are present
- [ ] Verify CSRF protection is active
- [ ] Test rate limiting
- [ ] Review access logs

## Rollback Procedure

If issues are detected after deployment:

### 1. Enable Maintenance Mode
```bash
php artisan down
```

### 2. Rollback Code
```bash
git checkout <previous-commit>
```

### 3. Rollback Database (if needed)
```bash
php artisan migrate:rollback --step=1
```

### 4. Rebuild Caches
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 5. Exit Maintenance Mode
```bash
php artisan up
```

## Environment Variables Reference

| Variable | Description | Example |
|----------|-------------|---------|
| APP_NAME | Application name | Smart School |
| APP_ENV | Environment | production |
| APP_DEBUG | Debug mode | false |
| APP_URL | Application URL | https://school.example.com |
| DB_CONNECTION | Database driver | mysql |
| DB_HOST | Database host | localhost |
| DB_DATABASE | Database name | smart_school |
| CACHE_DRIVER | Cache driver | redis |
| QUEUE_CONNECTION | Queue driver | redis |
| SESSION_DRIVER | Session driver | database |
| MAIL_MAILER | Mail driver | smtp |

## Server Requirements

- PHP >= 8.2
- MySQL >= 8.0 or SQLite
- Node.js >= 18.x
- Composer >= 2.x
- Redis (optional, for caching/queues)

### PHP Extensions
- BCMath
- Ctype
- Fileinfo
- JSON
- Mbstring
- OpenSSL
- PDO
- Tokenizer
- XML
- cURL
- GD or Imagick

## Support

For deployment issues, contact the development team or refer to the documentation at `/docs`.
