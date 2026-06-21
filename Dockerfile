FROM php:8.2-apache

# Sistem paketlerini güncelle ve curl'ü yükle
RUN apt-get update && apt-get install -y \
    libcurl4-openssl-dev \
    && docker-php-ext-install curl

# Dosyaları kopyala
COPY . /var/www/html/

# Port'u aç
EXPOSE 80
