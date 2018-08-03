# Keyword Finder

### Server Requirements

* PHP >= 5.6.4
* OpenSSL PHP Extension
* PDO PHP Extension // apt-get install php5.6-mysql
* Mbstring PHP Extension
* Tokenizer PHP Extension
* XML PHP Extension // apt-get install php5.6-dom
* MySQL >= 5.6
* PHP-FPM // apt-get install php5.6-fpm

### Configuration

1. Rename `.env.example` to `.env`
```
cp .env.example .env
```
2. Config the `.env` file
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=homestead
DB_USERNAME=homestead
DB_PASSWORD=secret
```
3. nginx config file
```
server {
    listen 80;
    server_name localhost; # Your domain
    root /app/public; # The public folder in this application root path

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-XSS-Protection "1; mode=block";
    add_header X-Content-Type-Options "nosniff";

    index index.html index.htm index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    location ~ \.php$ {
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass unix:/var/run/php/php5.6-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

4. Migration
``` bash
php artisan migrate
```
5. Cronjob
```
* * * * * /usr/bin/php /app/artisan schedule:run
```