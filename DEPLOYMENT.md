# FixMyStreet.cm - Deployment Guide

This guide covers deploying FixMyStreet.cm to various hosting platforms.

## Table of Contents

- [General Requirements](#general-requirements)
- [Shared Hosting](#shared-hosting)
- [VPS / Cloud Server](#vps--cloud-server)
- [Platform-Specific Guides](#platform-specific-guides)
- [Post-Deployment](#post-deployment)

## General Requirements

### Server Requirements
- PHP 8.2 or higher
- MySQL 8.0 or PostgreSQL 13+
- Composer
- Node.js 18+ & NPM
- Web server (Apache/Nginx)

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
- GD or Imagick (for image processing)

## Shared Hosting

### 1. Prepare Your Files

```bash
# Build production assets locally
npm run build

# Create a deployment package
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 2. Upload Files

Upload all files except:
- `.env` (create on server)
- `node_modules/`
- `.git/`

### 3. Configure Environment

Create `.env` file on server with production settings:

```env
APP_NAME="FixMyStreet.cm"
APP_ENV=production
APP_KEY=base64:YOUR_KEY_HERE
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

FILESYSTEM_DISK=public
```

### 4. Set Permissions

```bash
chmod -R 755 storage bootstrap/cache
```

### 5. Run Migrations

```bash
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link
```

## VPS / Cloud Server

### Using Ubuntu 22.04 LTS

#### 1. Install Dependencies

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install PHP 8.2
sudo apt install -y software-properties-common
sudo add-apt-repository ppa:ondrej/php
sudo apt update
sudo apt install -y php8.2 php8.2-fpm php8.2-mysql php8.2-xml php8.2-mbstring \
    php8.2-curl php8.2-zip php8.2-gd php8.2-bcmath php8.2-intl

# Install MySQL
sudo apt install -y mysql-server

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Node.js
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs

# Install Nginx
sudo apt install -y nginx
```

#### 2. Configure MySQL

```bash
sudo mysql
```

```sql
CREATE DATABASE fixmystreet CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'fixmystreet'@'localhost' IDENTIFIED BY 'secure_password';
GRANT ALL PRIVILEGES ON fixmystreet.* TO 'fixmystreet'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

#### 3. Clone and Setup Application

```bash
cd /var/www
sudo git clone <your-repo-url> fixmystreet
cd fixmystreet

# Install dependencies
composer install --optimize-autoloader --no-dev
npm install
npm run build

# Setup environment
cp .env.example .env
php artisan key:generate

# Configure .env with your database credentials

# Run migrations
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link

# Set permissions
sudo chown -R www-data:www-data /var/www/fixmystreet
sudo chmod -R 755 /var/www/fixmystreet/storage
sudo chmod -R 755 /var/www/fixmystreet/bootstrap/cache
```

#### 4. Configure Nginx

Create `/etc/nginx/sites-available/fixmystreet`:

```nginx
server {
    listen 80;
    server_name yourdomain.com www.yourdomain.com;
    root /var/www/fixmystreet/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Enable the site:

```bash
sudo ln -s /etc/nginx/sites-available/fixmystreet /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

#### 5. Setup SSL with Let's Encrypt

```bash
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com
```

## Platform-Specific Guides

### Deploy to Railway

1. Create a new project on [Railway](https://railway.app)
2. Connect your GitHub repository
3. Add MySQL database service
4. Set environment variables:
   ```
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://your-app.railway.app
   ```
5. Railway will auto-deploy on push

### Deploy to Render

1. Create a new Web Service on [Render](https://render.com)
2. Connect your GitHub repository
3. Set build command:
   ```bash
   composer install --optimize-autoloader --no-dev && npm install && npm run build
   ```
4. Set start command:
   ```bash
   php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=$PORT
   ```
5. Add MySQL database and configure environment variables

### Deploy to DigitalOcean App Platform

1. Create a new App on [DigitalOcean](https://cloud.digitalocean.com/apps)
2. Connect your GitHub repository
3. Add managed MySQL database
4. Configure build and run commands
5. Set environment variables

## Post-Deployment

### 1. Create Admin User

```bash
php artisan make:filament-user
```

### 2. Optimize Application

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

### 3. Setup Cron Jobs (Optional)

Add to crontab:

```bash
* * * * * cd /var/www/fixmystreet && php artisan schedule:run >> /dev/null 2>&1
```

### 4. Setup Queue Worker (Optional)

Create systemd service `/etc/systemd/system/fixmystreet-worker.service`:

```ini
[Unit]
Description=FixMyStreet Queue Worker
After=network.target

[Service]
User=www-data
Group=www-data
Restart=always
ExecStart=/usr/bin/php /var/www/fixmystreet/artisan queue:work --sleep=3 --tries=3

[Install]
WantedBy=multi-user.target
```

Enable and start:

```bash
sudo systemctl enable fixmystreet-worker
sudo systemctl start fixmystreet-worker
```

### 5. Setup Monitoring

Consider setting up:
- Application monitoring (e.g., Laravel Telescope in dev)
- Error tracking (e.g., Sentry)
- Uptime monitoring
- Log aggregation

### 6. Backup Strategy

Setup automated backups for:
- Database (daily)
- Uploaded images (`storage/app/public/issues`)
- Environment configuration

Example backup script:

```bash
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
mysqldump -u username -p password fixmystreet > backup_$DATE.sql
tar -czf storage_$DATE.tar.gz storage/app/public/issues
```

## Security Checklist

- [ ] Set `APP_DEBUG=false` in production
- [ ] Use strong database passwords
- [ ] Enable HTTPS/SSL
- [ ] Configure firewall (UFW)
- [ ] Keep dependencies updated
- [ ] Set proper file permissions
- [ ] Configure CORS if needed
- [ ] Enable rate limiting
- [ ] Regular security updates

## Troubleshooting

### 500 Internal Server Error

```bash
# Check logs
tail -f storage/logs/laravel.log

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Permission Issues

```bash
sudo chown -R www-data:www-data /var/www/fixmystreet
sudo chmod -R 755 storage bootstrap/cache
```

### Database Connection Error

- Verify database credentials in `.env`
- Check if MySQL is running: `sudo systemctl status mysql`
- Test connection: `php artisan db:show`

## Performance Optimization

### Enable OPcache

Edit `/etc/php/8.2/fpm/php.ini`:

```ini
opcache.enable=1
opcache.memory_consumption=256
opcache.max_accelerated_files=20000
opcache.validate_timestamps=0
```

### Configure Redis (Optional)

```bash
sudo apt install redis-server
composer require predis/predis
```

Update `.env`:

```env
CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

## Support

For deployment issues:
- Check Laravel logs: `storage/logs/laravel.log`
- Check web server logs: `/var/log/nginx/error.log`
- Review [Laravel Deployment Documentation](https://laravel.com/docs/deployment)
