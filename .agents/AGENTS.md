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
