# Riniger – Phoenix + Symfony (REST API)

Projekt składający się z dwóch współpracujących aplikacji:

- **Backend**: Elixir + Phoenix  
  Odpowiedzialny za bazę danych, import użytkowników oraz REST API
- **Frontend / Panel administracyjny**: PHP + Symfony  
  Interfejs webowy do zarządzania użytkownikami przez API Phoenix

Całość uruchamiana jest za pomocą **Docker Compose**.

---

## 📦 Struktura projektu

/project-root
│
├── backend/
│ └── phoenix/ # Aplikacja Phoenix (API)
| └── Dockerfile
│
├── frontend/
│ └── symfony/ # Aplikacja Symfony (panel admina)
| └── Dockerfile
│
├── docker-compose.yml
└── README.md

---

## ⚙️ Wymagania

- Docker >= 24
- Docker Compose >= 2
- Git

Nie jest wymagane lokalne PHP, Elixir ani Node – wszystko działa w Dockerze.

---

## 🚀 Uruchomienie projektu

### 1 Klonowanie repozytorium

```bash
git clone https://github.com/kwegielski/rinigier.git
cd rinigier
```

### 2 Uruchomienie konterów

```bash
docker compose up -d --build
```

Uruchomione zostaną:
- PostgreSQL
- Phoenix API (http://localhost:4000)
- Symfony frontend (http://localhost:8080)


### 3 Backend – Phoenix (API)

Migracje bazy danych

```bash
docker compose exec phoenix mix ecto.setup
docker compose exec phoenix mix ecto.migrate
docker compose exec phoenix mix phx.server
```
Co robi import:
- pobiera listy imion i nazwisk (100 dla każdej płci)
- generuje 100 losowych użytkowników
- losowa data urodzenia: 1970-01-01 → 2024-12-31
- zapis do PostgreSQL
- import odpalany z poziomu Symfony admin panel

Dostępne endpointy API:

```bash
GET    /api/users
GET    /api/users/:id
POST   /api/users
PUT    /api/users/:id
DELETE /api/users/:id
```

Obsługiwane funkcje:
- filtrowanie (imię, nazwisko, płeć, zakres dat)
- sortowanie po każdej kolumnie
- paginacja

### 4 FRONTEND – Symfony

Panel administracyjny dostępny pod:

```bash
http://localhost:8080
```

Funkcjonalności:
- lista użytkowników
- filtrowanie i sortowanie
- paginacja
- dodawanie użytkownika
- edycja użytkownika
- usuwanie użytkownika
- komunikacja wyłącznie przez REST API Phoenix

Symfony nie posiada własnej bazy danych – działa jako klient API.