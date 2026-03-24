#!/bin/bash

# ========================================================
# Script de Arranque Local para Elearning-Guilalva
# ========================================================

echo "--------------------------------------------------------"
echo "🚀 ARRANQUE LOCAL - ELEARNING-GUILALVA"
echo "--------------------------------------------------------"

# 1. DOCKER
if command -v docker > /dev/null 2>&1 && docker info > /dev/null 2>&1; then
    if ! docker ps | grep -q "lms_db"; then
        echo "🐘 Iniciando DB en Docker..."
        docker-compose up -d db phpmyadmin
        sleep 3
    fi
fi

# 2. CONFIGURACION
[ ! -d vendor ] && composer install
[ ! -f .env ] && cp .env.example .env && php artisan key:generate

# Fix .env
sed -i '' 's/DB_HOST=db/DB_HOST=127.0.0.1/g' .env 2>/dev/null || sed -i 's/DB_HOST=db/DB_HOST=127.0.0.1/g' .env
sed -i '' 's/APP_LOCALE=en/APP_LOCALE=es/g' .env 2>/dev/null || sed -i 's/APP_LOCALE=en/APP_LOCALE=es/g' .env

# 3. PREPARAR
php artisan storage:link --force > /dev/null 2>&1
php artisan migrate --force > /dev/null 2>&1

[ ! -d node_modules ] && npm install --force

# 4. CHEQUEO DE PUERTO
if lsof -Pi :8000 -sTCP:LISTEN -t >/dev/null ; then
    echo "⚠️ ADVERTENCIA: Puerto 8000 ocupado. Deten el servidor anterior."
    exit 1
fi

echo "--------------------------------------------------------"
echo "🌐 URL: http://localhost:8000"
echo "👤 ADMIN: admin@example.com / admin123"
echo "--------------------------------------------------------"

# 5. ARRANQUE
# Sin limites (0) y desde /public con ../server.php
# Usamos variables simples para evitar errores de sintaxis
export PHP_OPTS="-d upload_max_filesize=0 -d post_max_size=0 -d memory_limit=-1"

if [ -f node_modules/.bin/concurrently ]; then
    ./node_modules/.bin/concurrently -n "SERVER,VITE" -c "blue,magenta" "cd public && php $PHP_OPTS -S localhost:8000 ../server.php" "npm run dev" --kill-others
else
    (cd public && php $PHP_OPTS -S localhost:8000 ../server.php) & npm run dev
fi
