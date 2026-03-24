# Elearning-Guilalva - Plataforma de Aprendizaje

Este proyecto es una plataforma de e-learning moderna construida con **Laravel 12**, **Vue 3**, **Inertia.js** y **Tailwind CSS**.

---

## 🌐 Acceso Rápido (URLs)

Si ya tienes los servicios corriendo, puedes acceder aquí:
- **Aplicación:** [http://localhost:8000](http://localhost:8000)
- **📧 Credenciales de Administrador:**
   - **Email:** `admin@example.com`
   - **Pass:** `admin123`
- **Base de Datos (phpMyAdmin):** [http://localhost:8080](http://localhost:8080)
- **Credenciales BD:** Usuario: `root` | Password: `secret`

---

## 🚀 Tecnologías Principales

- **Backend:** Laravel 12 (PHP 8.2+)
- **Frontend:** Vue 3 (Composition API) + Inertia.js 2.0
- **Estilos:** Tailwind CSS
- **Bundler:** Vite
- **Base de Datos:** MySQL
- **Contenedores:** Docker + Docker Compose

## 🔑 Credenciales de la Base de Datos (Docker)

Para el entorno gestionado por Docker, las credenciales predeterminadas (configuradas en `docker-compose.yml`) son:

- **Host:** `db` (interno) o `127.0.0.1` (externo puerto 3306)
- **Base de Datos:** `lms_guilalva`
- **Usuario:** `root`
- **Password:** `secret`

---

## ⚡ Scripts de Automatización

Para facilitar el arranque, se han incluido dos scripts:

- **Configuración Docker:** `./setup-docker.sh` (Levanta todo y configura Laravel).
- **Arranque Local:** `./start-local.sh` (Inicia el servidor PHP y Vite simultáneamente).
> [!NOTE]
> Para instalaciones locales, asegúrate de crear la base de datos `lms_guilalva` y configurar estas credenciales en tu archivo `.env`.

## 🛠️ Requisitos Previos

Antes de comenzar, asegúrate de tener instalado:
- [Docker](https://www.docker.com/products/docker-desktop) y Docker Compose (Recomendado)
- **O de forma local:**
  - PHP 8.2 o superior
  - Node.js 20+ y npm
  - Composer
  - MySQL

## 🐳 Configuración con Docker (Recomendado)

1. **Clonar el repositorio:**
   ```bash
   git clone <url-del-repositorio>
   cd elearning-guilalva
   ```

2. **Levantar los contenedores:**
   ```bash
   docker-compose up -d
   ```

3. **Instalar dependencias y configurar la aplicación (dentro del contenedor):**
   ```bash
   docker exec -it lms_app composer run setup
   ```
   *Este comando ejecutará automáticamente: composer install, copia de .env, generación de key, migraciones, npm install y npm run build.*

4. **Acceder a la aplicación:**
   - **URL App:** [http://localhost:8000](http://localhost:8000)
   - **phpMyAdmin:** [http://localhost:8080](http://localhost:8080) (MySQL en puerto 3306)
   - **Admin Email:** `admin@example.com`
   - **Admin Pass:** `admin123`
   - **Credenciales BD:** Usuario: `root`, Password: `secret`

---

---

## 💻 Configuración Local (Sin Docker)

> [!WARNING]
> Este método requiere que tengas **PHP 8.2+** y **Node.js 20+** instalados directamente en tu sistema. Si no los tienes, usa el método de Docker (recomendado).

1. **Instalar dependencias de PHP:**
   ```bash
   composer install
   ```

2. **Configurar el entorno:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   *Nota: Configura tus credenciales de base de datos en el archivo `.env`.*

3. **Ejecutar migraciones:**
   ```bash
   php artisan migrate
   ```

4. **Instalar dependencias de JS y compilar:**
   ```bash
   npm install
   npm run build
   ```

5. **Iniciar servidores de desarrollo:**
   ```bash
   # En una terminal para el backend
   php artisan serve
   
   # En otra terminal para el frontend (Vite)
   npm run dev
   ```

## 📂 Estructura del Proyecto y Roles

El proyecto maneja tres roles principales:
- **Admin:** Gestión de usuarios y configuración global.
- **Gerente:** Gestión de cursos, lecciones y evaluaciones.
- **Empleado:** Acceso al reproductor de lecciones y realización de evaluaciones.

Las vistas se encuentran en `resources/js/Pages/` y los controladores en `app/Http/Controllers/`.

## 📜 Licencia

Este proyecto está bajo la licencia MIT.

---

## ❓ Solución de Problemas (Troubleshooting)

### Error en Vite / Rollup (Apple Silicon M1/M2/M3)
Si ves un error como `Cannot find module @rollup/rollup-darwin-arm64`, ejecuta:
```bash
rm -rf node_modules package-lock.json
npm install
```

### Versión de Node.js insuficiente
Vite requiere Node.js **20.19+** o **22.12+**. Si tienes una versión anterior, actualiza con:
```bash
brew upgrade node
```
O usa [nvm](https://github.com/nvm-sh/nvm) para cambiar de versión:
```bash
nvm install 22
nvm use 22
```
