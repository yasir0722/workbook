# Deployment

## Local development: Docker Compose only

Docker is used only to provide a consistent local PHP, MySQL 8, and Node/Vite
development environment. It is not part of production deployment.

1. Create a local environment file:

   ```sh
   cp .env.example .env
   ```

2. Generate an application key after Composer dependencies are installed:

   ```sh
   docker compose up -d
   docker compose exec app composer install
   docker compose exec app php artisan key:generate
   docker compose exec app php artisan migrate --seed
   ```

3. Open the application at <http://localhost:8000>. Vite runs at
   <http://localhost:5173> and is exposed for hot module reload.

The `node` service installs dependencies automatically the first time it starts. To
run it explicitly:

```sh
docker compose exec node npm install
docker compose exec node npm run dev -- --host 0.0.0.0
```

Useful commands:

```sh
# Run backend tests
docker compose exec app php artisan test

# Build and type-check the Vue application
docker compose exec node npm run type-check
docker compose exec node npm run build

# Stop the local stack, retaining MySQL data
docker compose down

# Rebuild PHP after changing Dockerfile
docker compose up -d --build

# Remove local containers and the MySQL development volume
docker compose down -v
```

`mysql_data` is a persistent local Docker volume. `DB_HOST=mysql` is required
inside the PHP container; do not change it to `localhost`.

## Production: native Ubuntu on a DigitalOcean Droplet

Production must use a native Linux stack, not Docker:

- Ubuntu
- Nginx
- PHP-FPM with PHP 8.4 and required extensions (`bcmath`, `mbstring`,
  `pdo_mysql`, `zip`)
- MySQL 8
- Composer
- Node.js/npm for the Vite build

Keep a production-only `.env` on the Droplet. It must use the Droplet's native MySQL
host, credentials, and production `APP_KEY`; never copy or commit the local Docker
environment file. Configure Nginx to serve `public/` and forward PHP requests to
PHP-FPM.

After a normal `git pull`, run from the application directory:

```sh
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
npm install
npm run build
```

Restart PHP-FPM after deployment if required by the server's process manager.
Provision HTTPS at Nginx. No Meta credentials, access tokens, or API integration
are part of this deployment stage.
