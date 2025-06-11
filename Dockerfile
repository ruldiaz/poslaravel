FROM richarvey/nginx-php-fpm:latest

# 1. Instalar dependencias del sistema
RUN apk add --update nodejs npm

# 2. Copiar TODOS los archivos primero
COPY . .

# 3. Instalar dependencias de Composer
RUN composer install --no-dev --optimize-autoloader

# 4. Instalar dependencias NPM y compilar assets (si es necesario)
RUN if [ -f "package.json" ]; then \
    npm install && \
    npm run build; \
    fi

# 5. Mover assets del backend si existen
RUN if [ -d "backend/assets" ]; then \
    mkdir -p public/backend && \
    cp -r backend/assets public/backend/; \
    fi

# 6. Configuración de Laravel para producción
RUN php artisan config:clear && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache

# 7. Configuración de entorno
ENV SKIP_COMPOSER=1
ENV WEBROOT=/var/www/html/public
ENV PHP_ERRORS_STDERR=1
ENV RUN_SCRIPTS=1
ENV REAL_IP_HEADER=1
ENV APP_ENV=production
ENV APP_DEBUG=false
ENV LOG_CHANNEL=stderr
ENV COMPOSER_ALLOW_SUPERUSER=1

# 8. Limpieza para reducir tamaño de imagen
RUN rm -rf /var/cache/apk/* && \
    if [ -d "node_modules" ]; then rm -rf node_modules; fi

CMD ["/start.sh"]