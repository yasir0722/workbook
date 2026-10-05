# Facebook Reader

A focused, local-first dashboard for manually selected Facebook Pages. This MVP uses
development mock data only. It does not contact Facebook or Meta, use Facebook
Login, scrape content, or store Meta credentials.

## Stack

- Laravel 13 REST API
- MySQL 8
- Vue 3, Vite, and TypeScript
- Docker Compose for local development only

The application has a `ContentSourceInterface` bound to
`MockFacebookContentSource`. A future approved source can replace that binding
without changing the Vue application or normalized REST API.

## Start locally

```sh
cp .env.example .env
docker compose up -d
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
```

Open <http://localhost:8000>. The Vite server is available at
<http://localhost:5173>.

See [Local and production deployment](docs/deployment.md) for Docker operations,
testing, rebuild commands, and the native Ubuntu/DigitalOcean deployment approach.

## API

- `GET /api/pages`
- `POST /api/pages`
- `GET /api/pages/{page}`
- `PUT /api/pages/{page}`
- `DELETE /api/pages/{page}`
- `GET /api/feed`
- `GET /api/pages/{page}/posts`

## Meta integration

Meta integration remains intentionally unimplemented until PPCA approval and a
Graph API proof of concept. Read
[Facebook API feasibility](docs/facebook-api-feasibility.md) before changing the
content source.
