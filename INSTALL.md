# LAV-SMS Installation and Deployment Guide for Linux Web Server

This guide covers the complete process of installing and deploying the LAV-SMS application on a Linux web server.

## Prerequisites

- A Linux server (Ubuntu/Debian recommended)
- Root or sudo access
- Domain name pointed to your server (optional but recommended)

## Step 1: Server Setup

### Update System
```bash
sudo apt update
sudo apt upgrade -y
```

### Install Required Packages
```bash
sudo apt install -y curl git unzip nginx software-properties-common
```

### Install PHP and Required Extensions
```bash
# Add PHP repository
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update

# Install PHP 8.1 and extensions
sudo apt install -y php8.1 php8.1-cli php8.1-fpm php8.1-common php8.1-mysql php8.1-zip php8.1-gd php8.1-mbstring php8.1-curl php8.1-xml php8.1-bcmath php8.1-intl
```

### Install Composer
```bash
curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer
```

### Install MySQL
```bash
sudo apt install -y mysql-server

# Secure MySQL installation
sudo mysql_secure_installation
```

### Install Node.js and NPM
```bash
curl -fsSL https://deb.nodesource.com/setup_16.x | sudo -E bash -
sudo apt install -y nodejs
```

## Step 2: Database Setup

### Create Database and User
```bash
sudo mysql -u root -p
```

Inside the MySQL prompt:
```sql
CREATE DATABASE lav_sms;
CREATE USER 'lav_sms_user'@'localhost' IDENTIFIED BY 'your_strong_password';
GRANT ALL PRIVILEGES ON lav_sms.* TO 'lav_sms_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

## Step 3: Application Deployment

### Clone the Repository
```bash
cd /var/www
sudo git clone https://github.com/4jean/lav_sms.git
sudo chown -R www-data:www-data lav_sms
cd lav_sms
```

### Install Dependencies
```bash
sudo -u www-data composer install --no-dev --optimize-autoloader
sudo -u www-data npm install
```

### Configure Environment
```bash
sudo -u www-data cp .env.example .env
sudo -u www-data php artisan key:generate
```

Edit the `.env` file:
```bash
sudo nano .env
```

Update the following variables:
```
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_DATABASE=lav_sms
DB_USERNAME=lav_sms_user
DB_PASSWORD=your_strong_password

# Add any SMS API credentials or other configuration here
```

### Build Assets
```bash
sudo -u www-data npm run build
```

### Run Migrations
```bash
sudo -u www-data php artisan migrate --seed
```

### Set Directory Permissions
```bash
sudo chown -R www-data:www-data /var/www/lav_sms
sudo find /var/www/lav_sms -type f -exec chmod 644 {} \;
sudo find /var/www/lav_sms -type d -exec chmod 755 {} \;
sudo chmod -R 775 /var/www/lav_sms/storage
sudo chmod -R 775 /var/www/lav_sms/bootstrap/cache
```

## Step 4: Web Server Configuration

### Configure Nginx
Create a new Nginx site configuration:
```bash
sudo nano /etc/nginx/sites-available/lav_sms
```

Add the following content:
```nginx
server {
    listen 80;
    server_name your-domain.com www.your-domain.com;
    root /var/www/lav_sms/public;

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
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Enable the site and restart Nginx:
```bash
sudo ln -s /etc/nginx/sites-available/lav_sms /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

## Step 5: SSL Configuration (Recommended)

### Install Certbot
```bash
sudo apt install -y certbot python3-certbot-nginx
```

### Obtain SSL Certificate
```bash
sudo certbot --nginx -d your-domain.com -d www.your-domain.com
```

Follow the prompts to complete the SSL setup.

## Step 6: Final Configuration

### Configure Scheduler
Add Laravel scheduler to crontab:
```bash
sudo -u www-data crontab -e
```

Add the following line:
```
* * * * * cd /var/www/lav_sms && php artisan schedule:run >> /dev/null 2>&1
```

### Set Up Queue Worker (if needed)
Create a systemd service file:
```bash
sudo nano /etc/systemd/system/laravel-worker.service
```

Add the following content:
```
[Unit]
Description=Laravel Queue Worker
After=network.target

[Service]
User=www-data
Group=www-data
WorkingDirectory=/var/www/lav_sms
ExecStart=/usr/bin/php /var/www/lav_sms/artisan queue:work --sleep=3 --tries=3
Restart=always

[Install]
WantedBy=multi-user.target
```

Enable and start the service:
```bash
sudo systemctl enable laravel-worker
sudo systemctl start laravel-worker
```

## Step 7: Application Maintenance

### Update Application
To update the application in the future:
```bash
cd /var/www/lav_sms
sudo -u www-data git pull
sudo -u www-data composer install --no-dev --optimize-autoloader
sudo -u www-data npm install
sudo -u www-data npm run build
sudo -u www-data php artisan migrate
sudo -u www-data php artisan cache:clear
sudo -u www-data php artisan config:clear
sudo -u www-data php artisan view:clear
```

### Backup Database
Set up a regular backup schedule:
```bash
sudo nano /etc/cron.daily/backup-lav-sms-db
```

Add:
```bash
#!/bin/bash
BACKUP_DIR="/var/backups/lav_sms"
mkdir -p $BACKUP_DIR
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
mysqldump -u lav_sms_user -p'your_strong_password' lav_sms > $BACKUP_DIR/lav_sms_$TIMESTAMP.sql
find $BACKUP_DIR -type f -name "*.sql" -mtime +7 -delete
```

Make the script executable:
```bash
sudo chmod +x /etc/cron.daily/backup-lav-sms-db
```

## Troubleshooting

### Check Logs
Application logs:
```bash
sudo tail -f /var/www/lav_sms/storage/logs/laravel.log
```

Nginx logs:
```bash
sudo tail -f /var/log/nginx/error.log
```

PHP-FPM logs:
```bash
sudo tail -f /var/log/php8.1-fpm.log
```

### Common Issues
1. **Permission errors**: Ensure proper ownership and permissions for storage and bootstrap/cache directories
2. **Database connection issues**: Verify credentials in .env file
3. **500 errors**: Check Laravel and Nginx logs for specifics
4. **Blank page**: Enable APP_DEBUG=true temporarily to see errors
