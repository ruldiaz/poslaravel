FROM richarvey/nginx-php-fpm:latest

# Instalar Node.js y npm (necesario para compilar assets)
RUN apk add --update nodejs npm

# Copiar solo los archivos necesarios para composer primero (optimización de caché de Docker)
COPY composer.json composer.lock ./

# Instalar dependencias de Composer
RUN composer install --no-dev --optimize-autoloader

# Copiar el resto de los archivos
COPY . .

# Instalar dependencias de NPM y compilar assets (si usas Vite o Mix)
RUN if [ -f "package.json" ]; then \
    npm install && \
    npm run build; \
    fi

# Mover los assets del backend a la carpeta public (si existe backend/assets)
RUN if [ -d "backend/assets" ]; then \
    mkdir -p public/backend && \
    cp -r backend/assets public/backend/; \
    fi

# Limpiar cache y optimizar Laravel
RUN php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache

# Configuración de la imagen
ENV SKIP_COMPOSER 1
ENV WEBROOT /var/www/html/public
ENV PHP_ERRORS_STDERR 1
ENV RUN_SCRIPTS 1
ENV REAL_IP_HEADER 1

# Configuración de Laravel
ENV APP_ENV production
ENV APP_DEBUG false
ENV LOG_CHANNEL stderr

# Permisos para composer
ENV COMPOSER_ALLOW_SUPERUSER 1

# Limpiar cache de APK y NPM para reducir tamaño de imagen
RUN rm -rf /var/cache/apk/* && \
    if [ -d "node_modules" ]; then rm -rf node_modules; fi

CMD ["/start.sh"]