# AGENT.md — Diretrizes para IA no projeto MOVIC

Este projeto utiliza um monorepo com separação clara entre backend e frontend.
Qualquer agente de IA (Codex, Copilot, ChatGPT, etc.) DEVE seguir estritamente
as regras abaixo.

## 1. Stack obrigatória
- Backend: Laravel 11 (PHP 8.3+)
- Frontend: Vue 3 + Vite
- State: Pinia
- API: REST (Laravel Sanctum)
- Banco: MySQL
- Infra: Docker + docker-compose + Nginx
- Estilo frontend: TailwindCSS (PrimeVue opcional)

NÃO sugerir nem introduzir:
- Next.js, Nuxt, React, Angular
- Outras linguagens backend
- ORMs fora do Eloquent
- Frameworks CSS fora do Tailwind/PrimeVue

## 2. Estrutura do repositório (obrigatória)
movic/
  backend/   -> Laravel
  frontend/  -> Vue 3
  docker/    -> Infra
  docker-compose.yml
  README.md

Nunca misturar código de frontend dentro do backend.

## 3. Regras de domínio (MVP)
- Usuário possui role: teacher ou student.
- Um aluno pertence a APENAS um professor (teacher_student UNIQUE(student_id)).
- Um aluno pode ter vários planos, mas somente UM plano ativo por vez.
- Apenas um treino por dia por aluno (UNIQUE(student_id, session_date)).
- Professor só pode acessar/modificar recursos que são dele.
- Aluno só acessa seus próprios dados.

## 4. Backend (Laravel)
- Usar Form Requests para validação.
- Usar Services para regras de negócio.
- Usar Policies ou Gates para autorização.
- Não colocar regra de negócio em migrations.
- Seguir padrão REST para controllers.

## 5. Frontend (Vue)
- Usar Composition API.
- Estado global apenas via Pinia.
- Não chamar API diretamente em componentes (usar camada api/).
- Separar pages, layouts e components.

## 6. Banco de dados
- Usar migrations para qualquer alteração.
- Não alterar schema sem atualizar models e relacionamentos.
- Campos ENUM devem ser representados por constantes ou Enums PHP.

## 7. Commits e evolução
- Criar código incremental.
- Não implementar funcionalidades fora do escopo solicitado.
- Priorizar código simples e legível.

Qualquer dúvida, perguntar antes de assumir.
