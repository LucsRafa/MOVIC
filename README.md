# MOVIC

Monorepo com backend em Laravel 11 e frontend em Vue 3 + Vite.

## Requisitos
- WSL2
- Docker + Docker Compose

## Subir o ambiente
```bash
docker compose up -d
```

## Backend (Laravel)
Entrar no container e rodar migrations:
```bash
docker compose exec php php artisan migrate --seed
```

## Frontend (Vue 3 + Vite)
```bash
cd frontend
npm i
npm run dev
```

## URLs
- API: http://localhost:8000/api
- Frontend: http://localhost:5173

## Observacoes
- Banco MySQL rodando no Docker (usuario: `movic`, senha: `movic`).
- Mailer configurado para `log` por padrao.
