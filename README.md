# MOVIC

Monorepo com backend em Laravel 11 e frontend em Vue 3 + Vite.

## Stack
- Backend: Laravel 11, Sanctum, MySQL, Redis
- Frontend: Vue 3, Vite, Pinia, Tailwind CSS
- Infra local: Docker Compose, Nginx, PHP-FPM, Mailpit
- Documentação: Swagger UI customizado em tema dark

## Requisitos
- WSL2
- Docker + Docker Compose
- Node.js 20+ e npm

## Subindo o projeto

### 1. Backend e serviços
```bash
docker compose up -d --build
```

### 2. Banco de dados
```bash
docker compose exec php php artisan migrate --seed
```

### 3. Frontend
```bash
cd frontend
npm install
npm run dev
```

## URLs locais
- API: `http://localhost:8000/api`
- Swagger UI: `http://localhost:8000/api/documentation`
- OpenAPI JSON: `http://localhost:8000/api/openapi.json`
- Frontend: `http://localhost:5173`
- Mailpit: `http://localhost:8025`
- MySQL: `localhost:3306`
- Redis: `localhost:6379`

## Observações
- Banco MySQL local:
  - database: `movic`
  - user: `movic`
  - password: `movic`
- Mail local via Mailpit:
  - SMTP: `localhost:1025`
  - UI: `http://localhost:8025`
- O frontend roda localmente via Vite e consome a API em `http://localhost:8000/api`.

## Comandos úteis

### Parar o ambiente
```bash
docker compose down
```

### Ver logs do backend
```bash
docker compose logs -f php nginx
```

### Rodar build do frontend
```bash
cd frontend
npm run build
```

## Fluxos principais

### Autenticação
- Cadastro e login com token Bearer
- Recuperação de senha com:
  - `POST /api/forgot-password`
  - `POST /api/reset-password`
- Rotas protegidas usam `Authorization: Bearer <token>`

### Aluno
- Dashboard com treino do dia, progresso semanal e pagamentos
- Persistência dos checks do treino por sessão
- Finalização do treino com `finished_at`
- Tempo médio calculado a partir de sessões concluídas

### Professor
- Dashboard, alunos, treinos, exercícios, pagamentos e histórico
- Swagger UI disponível para inspeção dos endpoints

## Endpoints principais

### Auth
| Método | Rota | Descrição |
| --- | --- | --- |
| POST | `/auth/register` | Registrar usuário |
| POST | `/auth/login` | Login e retorno do token |
| POST | `/auth/logout` | Logout autenticado |
| GET | `/me` | Usuário autenticado |
| POST | `/forgot-password` | Solicitar redefinição de senha |
| POST | `/reset-password` | Redefinir senha |

### Profile
| Método | Rota | Descrição |
| --- | --- | --- |
| PATCH | `/user/profile` | Atualizar perfil |
| PATCH | `/user/password` | Atualizar senha |
| POST | `/user/avatar` | Enviar avatar |

### Teacher
| Método | Rota | Descrição |
| --- | --- | --- |
| GET | `/teacher/dashboard` | Dashboard do professor |
| GET | `/teacher/requests` | Solicitações de alunos |
| POST | `/teacher/requests/{studentId}/approve` | Aprovar solicitação |
| POST | `/teacher/requests/{studentId}/reject` | Rejeitar solicitação |
| GET | `/teacher/students` | Listar alunos |
| GET | `/teacher/students/{id}/overview` | Resumo do aluno |
| PATCH | `/teacher/students/{id}/status` | Atualizar status do aluno |
| POST | `/teacher/students/{id}/reset-password` | Enviar redefinição de senha |
| DELETE | `/teacher/students/{id}` | Remover aluno |
| GET | `/teacher/workouts` | Listar treinos |
| POST | `/teacher/workouts/days` | Criar dia de treino |
| POST | `/teacher/workouts/days/{dayId}/items` | Criar item do treino |
| PUT | `/teacher/workouts/items/{itemId}` | Atualizar item do treino |
| DELETE | `/teacher/workouts/items/{itemId}` | Remover item do treino |
| GET | `/teacher/exercises` | Listar exercícios |
| POST | `/teacher/exercises` | Criar exercício |
| PUT | `/teacher/exercises/{exerciseId}` | Atualizar exercício |
| DELETE | `/teacher/exercises/{exerciseId}` | Remover exercício |
| GET | `/teacher/payments` | Listar pagamentos |
| POST | `/teacher/payments/register` | Registrar pagamento manual |
| GET | `/teacher/payments/{paymentId}/receipt.pdf` | Gerar comprovante PDF |
| POST | `/teacher/payments/{paymentId}/send-receipt` | Enviar comprovante por email |
| GET | `/teacher/history` | Histórico do professor |

### Student
| Método | Rota | Descrição |
| --- | --- | --- |
| GET | `/student/dashboard` | Dashboard do aluno |
| GET | `/student/plan/active` | Plano ativo |
| POST | `/workout-items/{itemId}/toggle` | Alternar conclusão do exercício na sessão atual |
| POST | `/student/sessions/start` | Iniciar sessão de treino |
| POST | `/student/sessions/{session}/check` | Marcar exercício manualmente na sessão |
| POST | `/student/sessions/{session}/finish` | Finalizar sessão |
| GET | `/student/payments` | Listar pagamentos |
| GET | `/student/payments/{id}` | Detalhe do pagamento |
| GET | `/student/payments/{id}/pdf` | Baixar comprovante PDF |
| POST | `/student/payments/{id}/email` | Enviar comprovante por email |
| POST | `/student/payments/manual` | Registrar pagamento manual |

## Documentação da API
- A documentação interativa fica em `http://localhost:8000/api/documentation`
- O OpenAPI JSON fica em `http://localhost:8000/api/openapi.json`
- O Swagger UI usa sobrescrita de CSS customizada, sem alterar o JS da lib

## Testes

### Frontend
```bash
cd frontend
npm run build
```

### Backend
Se houver PHP configurado no host:
```bash
cd backend
php artisan test
```

## Estrutura resumida
```text
backend/   Laravel API
frontend/  Vue 3 + Vite
docker/    Nginx e PHP-FPM
```
