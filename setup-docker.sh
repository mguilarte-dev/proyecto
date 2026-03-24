#!/bin/bash

# Script robusto para levantar el proyecto con Docker
echo "--------------------------------------------------------"
echo "🚀 CONFIGURACIÓN DOCKER - ELEARNING-GUILALVA"
echo "--------------------------------------------------------"

# Verificar si docker está instalado
if ! command -v docker &> /dev/null; then
    echo "❌ Error: 'docker' no está instalado. Por favor instálalo para continuar."
    exit 1
fi

# Verificar si docker-compose está instalado
if ! command -v docker-compose &> /dev/null; then
    echo "❌ Error: 'docker-compose' no está instalado."
    exit 1
fi

echo "📦 Levantando contenedores..."
docker-compose up -d

echo "⏳ Esperando a que los servicios estén listos (10s)..."
sleep 10

echo "⚙️ Ejecutando configuración interna (Composer, Migraciones, NPM)..."
# Asegurar DB_HOST=db para el entorno Docker
sed -i '' 's/DB_HOST=127.0.0.1/DB_HOST=db/g' .env 2>/dev/null || sed -i '' 's/DB_HOST=localhost/DB_HOST=db/g' .env 2>/dev/null

# Limpiar posibles restos de arquitectura local que den conflicto en Docker
docker exec lms_app rm -rf node_modules package-lock.json

# Ejecutar el setup completo
docker exec lms_app composer run setup

echo ""
echo "--------------------------------------------------------"
echo "✅ ¡Configuración completada!"
echo "--------------------------------------------------------"
echo "🌐 App: http://localhost:8000"
echo "🛠️ phpMyAdmin: http://localhost:8080"
echo "--------------------------------------------------------"
echo "👤 Administrador por defecto:"
echo "   📧 Email: admin@example.com"
echo "   🔑 Pass:  admin123"
echo "--------------------------------------------------------"
