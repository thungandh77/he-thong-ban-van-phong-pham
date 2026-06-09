FROM php:8.1-apache

# Cài đặt các extension PHP cần thiết cho MySQL
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Bật mod_rewrite của Apache (rất cần thiết cho các framework hoặc rewrite URL)
RUN a2enmod rewrite

# Thiết lập thư mục làm việc mặc định
WORKDIR /var/www/html