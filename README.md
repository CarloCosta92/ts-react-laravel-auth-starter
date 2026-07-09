# React + Laravel Sanctum Auth Demo

Progetto di esplorazione full-stack: autenticazione token-based con **Laravel 11 + Sanctum** sul backend e **React + TypeScript** sul frontend.

L'obiettivo è didattico — capire come funziona un flusso di autenticazione API-based (register, login, logout, route protette) disaccoppiando completamente FE e BE.

---

## 📦 Stack

**Backend**
- PHP 8.3
- Laravel 13.8
- Laravel Sanctum (autenticazione via token)
- SQLite (zero-config, un singolo file)

**Frontend**
- React 19.2
- TypeScript
- Vite
- React Router DOM

---

## 🏗️ Struttura del progetto

```
auth-demo/
├── backend/          # API Laravel
│   ├── app/
│   │   └── Models/
│   │       └── User.php
│   ├── database/
│   │   └── database.sqlite
│   ├── routes/
│   │   └── api.php   # register, login, logout, user
│   └── .env
│
└── frontend/          # React + TS
    ├── src/
    │   ├── api.ts             # helper per le chiamate HTTP
    │   ├── AuthContext.tsx    # stato globale del token
    │   ├── App.tsx            # routing + route protette
    │   ├── components/
    │   │   └── Header.tsx
    │   └── pages/
    │       ├── Login.tsx
    │       └── Home.tsx
    └── package.json
```

---

## 🔐 Come funziona l'autenticazione

Il progetto usa **token Bearer** (non sessioni/cookie), l'approccio standard quando FE e BE sono disaccoppiati.

```
┌─────────┐                                    ┌──────────┐
│  React  │                                    │ Laravel  │
└────┬────┘                                    └────┬─────┘
     │                                              │
     │  POST /api/register  {name,email,password}   │
     ├─────────────────────────────────────────────►│
     │                                              │ crea utente
     │                                              │ hash password
     │                                              │ genera token
     │  { status, message, data:{user, token} }      │
     │◄─────────────────────────────────────────────┤
     │                                              │
     │  React salva il token (localStorage)          │
     │                                              │
     │  GET /api/user   Header: Authorization: Bearer <token>
     ├─────────────────────────────────────────────►│
     │                                              │ middleware auth:sanctum
     │                                              │ verifica il token nel DB
     │  { status, message, data:{user} }              │
     │◄─────────────────────────────────────────────┤
     │                                              │
     │  POST /api/logout  Header: Authorization: Bearer <token>
     ├─────────────────────────────────────────────►│
     │                                              │ elimina il token dal DB
     │  { status, message, data: null }               │
     │◄─────────────────────────────────────────────┤
```

### Struttura di risposta uniforme

Tutte le API rispondono con lo stesso formato:

```json
{
  "status": "success",
  "message": "Login effettuato con successo",
  "data": {
    "user": { "id": 1, "name": "Carlo", "email": "carlo@test.com" },
    "token": "5|SOpYeVPbfCjfKSjKC9DfWuCiCHLKLQ5P9naVCK2518af769e"
  }
}
```

In caso di errore, `status` diventa `"error"`, `data` è `null` e viene incluso un `message` esplicativo.

---

## 🚀 Setup — Backend (Laravel)

### Prerequisiti
- PHP ≥ 8.1 (consigliato installare tramite [Laravel Herd](https://herd.laravel.com/windows) su Windows — include PHP + Composer preconfigurati)
- Composer

### Installazione

```bash
cd backend
composer install
```

### Configurazione ambiente

Copia `.env.example` in `.env` (se non già presente) e verifica che punti a SQLite:

```
DB_CONNECTION=sqlite
```

Crea il file del database (se non esiste già):

```bash
# PowerShell
New-Item -Path "database\database.sqlite" -ItemType File
```

### Migration

```bash
php artisan migrate
```

Questo crea le tabelle `users`, `cache`, `jobs` e `personal_access_tokens` (quest'ultima usata da Sanctum per salvare i token).

### Avvio del server

```bash
php artisan serve
```

Il backend sarà disponibile su `http://127.0.0.1:8000`.

---

## 🎨 Setup — Frontend (React)

### Prerequisiti
- Node.js ≥ 18
- npm

### Installazione

```bash
cd frontend
npm install
```

### Avvio in sviluppo

```bash
npm run dev
```

Il frontend sarà disponibile su `http://localhost:5173` (o porta indicata a terminale).

---

## 📡 Endpoint API

| Metodo | Endpoint         | Auth richiesta | Descrizione                              |
|--------|------------------|:--------------:|-------------------------------------------|
| POST   | `/api/register`  | ❌              | Crea un nuovo utente, ritorna user + token |
| POST   | `/api/login`     | ❌              | Verifica credenziali, ritorna user + token |
| GET    | `/api/user`      | ✅ (Bearer)     | Ritorna l'utente autenticato               |
| POST   | `/api/logout`    | ✅ (Bearer)     | Invalida il token corrente                 |

### Esempio: Register

```http
POST /api/register
Content-Type: application/json

{
  "name": "Carlo",
  "email": "carlo@test.com",
  "password": "password123"
}
```

### Esempio: Login

```http
POST /api/login
Content-Type: application/json

{
  "email": "carlo@test.com",
  "password": "password123"
}
```

### Esempio: Richiesta autenticata

```http
GET /api/user
Authorization: Bearer 5|SOpYeVPbfCjfKSjKC9DfWuCiCHLKLQ5P9naVCK2518af769e
```

---

## 🧪 Testare le API

Puoi testare gli endpoint con **Postman** (o qualsiasi client REST):

1. Crea una richiesta `POST` verso `http://127.0.0.1:8000/api/register` con body JSON
2. Copia il `token` ricevuto nella risposta
3. Per le route protette, vai su **Authorization → Bearer Token** e incolla il token

---

## 🧠 Concetti chiave esplorati in questo progetto

- **Autenticazione token-based** vs session-based: perché ha senso per API disaccoppiate da un FE SPA
- **Sanctum**: come genera, salva e verifica i token (tabella `personal_access_tokens`)
- **Middleware** in Laravel (`auth:sanctum`) come "buttafuori" delle route protette
- **Hashing password** (`Hash::make` / `Hash::check`) — mai salvare password in chiaro
- **Validazione request** lato Laravel (`$request->validate([...])`)
- **Context API** in React per gestire lo stato di autenticazione globale
- **Route protette** in React Router, con redirect automatico se non autenticati
- Struttura di risposta API uniforme (`status` / `message` / `data`) per semplificare la gestione lato client

---

## 📝 Note

Questo è un progetto didattico, **non pensato per la produzione**. Alcune semplificazioni volute:

- Il token viene salvato in `localStorage` (per un'app di produzione andrebbero valutate alternative più sicure contro XSS, es. httpOnly cookie)
- Nessuna gestione di refresh token o scadenza automatica
- CORS configurato in modo permissivo per lo sviluppo locale
- SQLite usato per semplicità di setup, non per performance

---

## 📄 Licenza

MIT — fai pure quello che vuoi con questo codice.
