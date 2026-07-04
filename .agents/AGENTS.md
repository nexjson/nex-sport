# NEX-Sport — Agent Rules

## The Four Rules of Coding Practice

These rules apply to every task in this project without exception. Before writing any code, confirm you understand and are following all four.

---

### 1. Think Before Coding

State your assumptions out loud before starting.
If the request is ambiguous, ask — do not guess.
If a simpler approach exists, push back and explain why.
Stop when confused, name what is unclear, and wait for clarification. Do **not** pick one interpretation and run with it.

> **Before writing code, always answer:** What exactly is being asked? What assumptions am I making? Is there a simpler way?

---

### 2. Simplicity First

Write the **minimum** code that solves the problem.
No speculative abstractions. No added flexibility nobody asked for.
No premature generalization or "just in case" layers.

> **The test:** Would a senior engineer look at this and call it overcomplicated? If yes, simplify it.

---

### 3. Surgical Changes

Touch **only** what the task requires.
Do not improve neighboring code. Do not refactor what is not broken.
Do not clean up unrelated formatting, rename unrelated variables, or restructure files that were not part of the request.

> **Every changed line must trace directly back to the original request.** If you cannot explain why a line changed, revert it.

---

### 4. Goal-Driven Execution

Turn vague instructions into **verifiable targets** before writing a line of code.

| Vague Instruction | Verifiable Target |
|-------------------|-------------------|
| "Add validation" | Write tests for invalid inputs, then make them pass |
| "Make it faster" | Define what metric proves it is faster, then optimize |
| "Fix the bug" | Write a test that reproduces the bug, then fix the code |
| "Add a feature" | Define the acceptance criteria, then implement |

> Do not start coding until you can state: *"This task is done when [specific, measurable outcome]."*

---

## Project Context

**NEX-Sport** adalah platform manajemen turnamen olahraga dan e-sports yang memungkinkan Organizer membuat event turnamen, Squad mendaftar dan bertanding, serta sistem escrow yang memproses payout hadiah secara otomatis.

### Tech Stack
- **Backend:** Laravel 13, PHP 8.5
- **Frontend:** Inertia.js v3 + Svelte 5
- **Database:** PostgreSQL
- **Auth:** Laravel Fortify
- **Payment:** Xendit / Midtrans (Payment Gateway)
- **CSS:** Tailwind CSS v4
- **Testing:** Pest v4

### Dokumen Referensi Desain
Selalu baca dokumen berikut sebelum memulai pekerjaan pada fitur yang relevan:

| Dokumen | Tujuan |
|---------|--------|
| [implementation_plan.md](file:///d:/webapp/nex-sport/implementation_plan.md) | ERD lengkap, struktur tabel, dan kolom database |
| [feature_list_per_role.md](file:///d:/webapp/nex-sport/feature_list_per_role.md) | Hak akses setiap fitur per role |
| [feature_tasks.md](file:///C:/Users/LENOVO/.gemini/antigravity-ide/brain/8f03e63a-5601-4822-8672-2a665791bb9f/feature_tasks.md) | Deskripsi fitur, positive case, negative case, dan checklist implementasi |
| [role_entity_glossary.md](file:///C:/Users/LENOVO/.gemini/antigravity-ide/brain/8f03e63a-5601-4822-8672-2a665791bb9f/role_entity_glossary.md) | Kamus istilah entitas dan role aplikasi |

---

## Domain Glossary

Gunakan istilah baku berikut dalam setiap instruksi dan implementasi kode:

| Istilah | Tabel | Penjelasan |
|---------|-------|------------|
| **User** | `users` | Akun yang terdaftar di aplikasi, memiliki salah satu role |
| **Organizer** | `organizers` | Penyelenggara event/turnamen. Satu user bisa menjadi satu organizer |
| **Team** | `teams` | Klub/organisasi induk (contoh: RRQ, EVOS) yang memiliki banyak Squad |
| **Squad** | `squads` | Tim spesifik per game di bawah Team yang **mendaftar ke turnamen** |
| **Player** | `players` | Profil pemain individu yang terhubung ke User, dan tergabung dalam Squad |
| **Event** | `events` | Kegiatan turnamen yang dibuat Organizer (bisa multi-game) |
| **Event Game** | `event_games` | Cabang game spesifik dalam Event, memiliki kuota, tiket, dan bracket sendiri |
| **Match** | `matches` | Pertandingan antar Squad dalam sebuah Event Game |
| **Registration** | `registrations` | Pendaftaran Squad ke Event Game + status pembayaran tiket |
| **Event Payment** | `event_payments` | Deposit dana hadiah dari Organizer ke platform (escrow) |
| **Reward Claim** | `reward_claims` | Klaim & pencairan hadiah otomatis ke pemenang via payment gateway |

---

## Database Conventions

Aturan wajib untuk semua tabel dan kolom database di proyek ini:

- **Nama tabel:** lowercase, plural, snake_case — contoh: `event_games`, `reward_claims`, `transfer_histories`
- **Primary key:** selalu `id` (integer, auto-increment)
- **Foreign key:** nama kolom mengacu ke tabel singular + `_id` — contoh: `user_id`, `squad_id`, `event_games_id`
- **Timestamps:** setiap tabel wajib memiliki `created_at` dan `updated_at` (kecuali tabel pivot murni)
- **Soft delete:** hanya digunakan pada tabel `users` (kolom `deleted_at`)
- **Enum:** gunakan Laravel `enum` cast di Model, bukan PHP native enum, agar kompatibel dengan PostgreSQL
- **Nullable:** semua FK opsional harus eksplisit dideklarasikan `->nullable()` di migrasi
- **Audit Trail:** kolom keuangan seperti `amount_paid`, `ticket_price`, `admin_fee` wajib disalin saat transaksi (tidak hanya referensi FK)

---

## Feature Task Reference

Setiap kali diminta membuat atau mengerjakan sebuah fitur, **wajib mengacu** ke [feature_tasks.md](file:///C:/Users/LENOVO/.gemini/antigravity-ide/brain/8f03e63a-5601-4822-8672-2a665791bb9f/feature_tasks.md) untuk:

1. Membaca **deskripsi fitur** agar memahami scope yang benar
2. Mengidentifikasi **positive case** yang harus berjalan dengan baik
3. Mengidentifikasi **negative case** yang harus ditangani (validasi, 403, dll.)
4. Mencentang **checklist implementasi** saat item tersebut selesai dikerjakan

Pekerjaan pada sebuah fitur dianggap **selesai** hanya jika seluruh positive case dan negative case yang relevan sudah ditangani dan dibuktikan dengan test Pest yang passing.

---

## Repository Pattern

Proyek ini menggunakan Repository Pattern untuk entity yang memiliki query kompleks atau diakses dari banyak tempat. ALWAYS activate `repository-pattern` skill saat membuat atau memodifikasi repository.

### Entity yang Wajib Pakai Repository

| Entity | Repository Interface | Implementasi |
|--------|---------------------|--------------|
| User | `UserRepositoryInterface` | `Eloquent/UserRepository` |
| Event | `EventRepositoryInterface` | `Eloquent/EventRepository` |
| Squad | `SquadRepositoryInterface` | `Eloquent/SquadRepository` |
| Registration | `RegistrationRepositoryInterface` | `Eloquent/RegistrationRepository` |
| Match | `MatchRepositoryInterface` | `Eloquent/MatchRepository` |
| RewardClaim | `RewardClaimRepositoryInterface` | `Eloquent/RewardClaimRepository` |

Entity sederhana (Game, Organizer, Team, Reward) **tidak** perlu repository — gunakan Eloquent langsung di controller/service.

### Struktur Direktori

```
app/
├── Repositories/
│   ├── Contracts/               ← Interface (contract)
│   │   ├── EventRepositoryInterface.php
│   │   └── ...
│   └── Eloquent/                ← Implementasi konkret
│       ├── EventRepository.php
│       └── ...
└── Providers/
    └── RepositoryServiceProvider.php
```

### Aturan Repository

1. **Interface wajib ada** sebelum implementasi — binding di `RepositoryServiceProvider`
2. **Return type:** Eloquent Model atau Collection — bukan DTO
3. **Exception:** Query agregasi (statistik dashboard) boleh return `array`
4. **Controller tidak boleh** memanggil Eloquent query secara langsung untuk entity yang punya repository
5. **Inject via constructor** menggunakan interface, bukan konkret class

---

## API Architecture (Triple-Consumer)

Aplikasi ini melayani tiga consumer: **Web** (Inertia/Svelte), **Mobile** (JSON API, Sanctum), dan **Public** (JSON API, no auth — untuk landing page website). Selalu activate `repository-pattern` skill saat membuat API endpoint baru.

### Scope API

| Consumer | Auth | Role / Akses |
|----------|:----:|--------------|
| **Web** (Inertia) | Session | Super Admin, Admin, Organizer, Player |
| **Mobile API** | Sanctum token | Player & Squad/Team |
| **Public API** | ❌ No auth | Siapa saja (landing page) |

Role & endpoint per consumer:

| Role | Web (Inertia) | API Mobile | API Public |
|------|:---:|:---:|:---:|
| Super Admin | ✅ | ❌ | — |
| Admin | ✅ | ❌ | — |
| Organizer | ✅ | ❌ | — |
| **Player** | ✅ | ✅ | — |
| **Squad / Team** | ✅ | ✅ | — |
| **Guest (publik)** | — | — | ✅ |

### Autentikasi API

- **Mobile API:** gunakan **Laravel Sanctum** (token-based)
- **Public API:** tidak perlu auth — route tanpa middleware auth
- Jangan gunakan Passport
- Middleware: `auth:sanctum` hanya pada route mobile yang terproteksi

### Routing Convention

```
routes/web.php   → semua halaman Inertia (semua role)
routes/api.php   → /api/v1/public/*   (no auth — landing page)
                   /api/v1/*           (auth:sanctum — mobile app)
```

### Struktur Controller

```
app/Http/Controllers/
├── Web/                         ← Web controllers (Inertia)
│   ├── EventController.php
│   ├── SquadController.php
│   └── ...
└── Api/
    └── V1/                      ← API controllers (JSON)
        ├── Public/              ← No auth (landing page)
        │   ├── EventController.php
        │   ├── GameController.php
        │   ├── LeaderboardController.php
        │   └── StatsController.php
        ├── Auth/                ← Sanctum auth
        │   └── AuthController.php
        ├── PlayerController.php ← auth:sanctum
        ├── SquadController.php  ← auth:sanctum
        └── ...
```

### API Response Convention

- **Selalu** gunakan **API Resource** (`php artisan make:resource`) untuk response JSON
- Jangan return Model atau array mentah dari API controller
- Format response standar:

```json
{
  "data": { ... },
  "message": "Success"
}
```

- Error response mengikuti HTTP status code yang tepat (401, 403, 404, 422, 500)
- Gunakan `JsonResource` untuk single object, `ResourceCollection` untuk list

### API Versioning

- Semua route API menggunakan prefix `/api/v1/`
- Jika ada breaking change di masa depan, buat `/api/v2/` tanpa mengubah v1
- Jangan pernah ubah response structure v1 setelah mobile app rilis

---

## Public API — Landing Page

Semua endpoint berikut adalah **public** (tidak perlu login). Gunakan **response caching** untuk endpoint yang datanya jarang berubah.

### Daftar Endpoint

| Method | Endpoint | Deskripsi | Cache |
|--------|----------|-----------|:-----:|
| `GET` | `/api/v1/public/stats` | Statistik platform (total event, player, organizer, turnamen selesai) | ✅ 10 menit |
| `GET` | `/api/v1/public/events` | Daftar event publik (status: `registration` atau `ongoing`) dengan filter & pagination | ✅ 5 menit |
| `GET` | `/api/v1/public/events/{id}` | Detail event: info, games, reward, jadwal | ✅ 5 menit |
| `GET` | `/api/v1/public/events/live` | Turnamen yang sedang `ongoing` (live) | ✅ 1 menit |
| `GET` | `/api/v1/public/games` | Daftar cabang game yang aktif di platform | ✅ 30 menit |
| `GET` | `/api/v1/public/leaderboard` | Leaderboard player (berdasarkan jumlah kemenangan) dengan filter game | ✅ 10 menit |
| `GET` | `/api/v1/public/winners` | Daftar pemenang/juara dari event yang sudah `completed` | ✅ 10 menit |
| `GET` | `/api/v1/public/organizers/featured` | Organizer terfeatured/terpopuler (berdasarkan jumlah event) | ✅ 30 menit |

### Aturan Public API

1. **Tidak ada middleware auth** pada route `/public/*`
2. **Selalu gunakan cache** (`Cache::remember()`) untuk semua endpoint public — data ini dibaca banyak orang sekaligus
3. **Tidak boleh expose** data sensitif: email, nomor rekening, token, data keuangan internal
4. **Pagination wajib** untuk semua endpoint yang return list (default: 12 item per page)
5. Response tetap mengikuti format standar API Resource

### Contoh Routing

```php
// routes/api.php

Route::prefix('v1')->group(function () {

    // Public — no auth
    Route::prefix('public')->group(function () {
        Route::get('stats', [StatsController::class, 'index']);
        Route::get('events', [Public\EventController::class, 'index']);
        Route::get('events/live', [Public\EventController::class, 'live']);
        Route::get('events/{id}', [Public\EventController::class, 'show']);
        Route::get('games', [Public\GameController::class, 'index']);
        Route::get('leaderboard', [Public\LeaderboardController::class, 'index']);
        Route::get('winners', [Public\LeaderboardController::class, 'winners']);
        Route::get('organizers/featured', [Public\OrganizerController::class, 'featured']);
    });

    // Mobile — auth required
    Route::post('login', [Auth\AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [Auth\AuthController::class, 'logout']);
        Route::apiResource('squads', SquadController::class);
        Route::apiResource('players', PlayerController::class);
        // ... more mobile routes
    });

});
```

### Contoh Response: Stats

```json
{
  "data": {
    "total_events": 128,
    "total_players": 4502,
    "total_organizers": 37,
    "completed_tournaments": 95,
    "live_tournaments": 3
  },
  "message": "Success"
}
```
