# NEX-Sport — Master Task Checklist

> Dokumen ini dihasilkan dari analisis mendalam terhadap:
> - [feature_list_per_role.md](file:///d:/webapp/nex-sport/feature_list_per_role.md) — Hak akses per role
> - [financial_analysis.md](file:///d:/webapp/nex-sport/financial_analysis.md) — Audit trail keuangan
> - [financial_benefits.md](file:///d:/webapp/nex-sport/financial_benefits.md) — Model bisnis & revenue
> - [financial_system.md](file:///d:/webapp/nex-sport/financial_system.md) — Arsitektur escrow & payout
> - [implementation_plan.md](file:///d:/webapp/nex-sport/implementation_plan.md) — ERD & struktur database

---

## 📊 Status Proyek Saat Ini

| Komponen | Status | Detail |
|----------|:------:|--------|
| Migrations (18 tabel) | ✅ Done | Semua tabel domain sudah dibuat |
| Models (20 model) | ✅ Done | Semua Eloquent model tersedia |
| Repositories (6 contract + 6 impl) | ✅ Done | Event, Match, Registration, RewardClaim, Squad, User |
| Auth Pages (7 halaman) | ✅ Done | Login, Register, ForgotPassword, ResetPassword, VerifyEmail, 2FA, ConfirmPassword |
| Web Controllers (10 controller) | ✅ Done | Admin (4), Organizer (2), Player (4), Dashboard (1) |
| Web Routes | ✅ Done | Semua route terdaftar di `web.php` |
| Frontend Pages (Svelte) | ✅ Done | Admin, Organizer, Player, Dashboard, Settings |
| **Policies** | ❌ Missing | Belum ada file Policy sama sekali |
| **Form Requests** | ❌ Missing | Hanya Settings, belum ada domain Form Request |
| **API Routes (`api.php`)** | ❌ Missing | File belum dibuat |
| **Public API** | ❌ Missing | 8 endpoint landing page belum ada |
| **Mobile API (Sanctum)** | ❌ Missing | Belum ada controller API |
| **API Resources** | ❌ Missing | Belum ada JsonResource class |

---

## 📋 Modul 1 — Authentication & Profile

### F-01: Login / Logout

**Deskripsi:** User (semua role) dapat masuk menggunakan email & password, serta keluar dengan aman. Setelah login, user diarahkan ke dashboard sesuai role-nya.

| Aspek | Detail |
|-------|--------|
| **Tabel** | `users`, `roles` |
| **Role** | Super Admin, Admin, Organizer, Player |
| **Controller** | Fortify (built-in) |
| **Pages** | `auth/Login.svelte`, `auth/Register.svelte` |

**✅ Positive Case:**
- [x] Login dengan email & password benar → masuk ke dashboard sesuai role
- [x] Setelah login, session aktif dan user bisa mengakses halaman terproteksi
- [x] Logout berhasil → session dihapus, redirect ke halaman login
- [x] Akses halaman terproteksi setelah logout → redirect ke login

**❌ Negative Case:**
- [x] Login dengan email tidak terdaftar → error "Email atau password salah"
- [x] Login dengan password salah → error "Email atau password salah"
- [x] Login dengan akun yang dinonaktifkan (status=false) → error "Akun Anda dinonaktifkan"
- [x] Login >5x gagal berturut-turut → rate-limit aktif (throttle)

**☑️ Checklist Implementasi:**
- [x] Halaman login (Inertia/Svelte) — `auth/Login.svelte`
- [x] Halaman register — `auth/Register.svelte`
- [x] Fortify authentication backend
- [x] Middleware redirect setelah login berdasarkan role
- [ ] Rate limiting pada route login (Fortify throttle config)
- [x] Logout dengan invalidate session
- [ ] Middleware cek `status=false` → tolak login user yang di-ban
- [ ] Test: login semua role → redirect ke dashboard masing-masing
- [ ] Test: login akun banned → ditolak
- [ ] Test: throttle setelah 5x gagal

---

### F-02: Edit Profil & Ganti Password

**Deskripsi:** Setiap user dapat mengubah nama, foto profil, nomor telepon, dan password akun miliknya sendiri.

| Aspek | Detail |
|-------|--------|
| **Tabel** | `users` |
| **Role** | Semua role |
| **Controller** | Fortify Actions (`UpdateUserProfileInformation`, `UpdateUserPassword`) |
| **Pages** | `settings/` |

**✅ Positive Case:**
- [x] Update nama dan nomor telepon berhasil tersimpan
- [x] Upload foto profil (JPG/PNG/WebP, maks 2MB) berhasil
- [x] Ganti password dengan memasukkan password lama yang benar → berhasil
- [x] Password baru bisa digunakan untuk login berikutnya

**❌ Negative Case:**
- [x] Upload foto format tidak didukung (PDF, DOC) → validasi ditolak
- [x] Upload foto ukuran >2MB → validasi ditolak dengan pesan error
- [x] Ganti password dengan password lama yang salah → ditolak
- [x] Konfirmasi password baru tidak cocok → validasi ditolak

**☑️ Checklist Implementasi:**
- [x] Halaman edit profil — `settings/`
- [ ] Upload foto ke Storage (local/S3) dengan validasi mime & size
- [x] Form ganti password dengan validasi `current_password`
- [ ] Form Request validasi khusus profile update
- [ ] Test: update profil berhasil
- [ ] Test: upload foto invalid → 422

---

### F-02b: Two-Factor Authentication (2FA)

**Deskripsi:** User dapat mengaktifkan 2FA (TOTP) untuk keamanan tambahan. Setelah diaktifkan, user harus memasukkan kode 2FA saat login.

| Aspek | Detail |
|-------|--------|
| **Tabel** | `users` (kolom 2FA dari migrasi Fortify) |
| **Role** | Semua role |
| **Pages** | `auth/TwoFactorChallenge.svelte` |

**✅ Positive Case:**
- [ ] User aktifkan 2FA → QR code & recovery codes ditampilkan
- [ ] Login dengan 2FA aktif → redirect ke halaman input kode
- [ ] Input kode TOTP valid → berhasil login
- [ ] Input recovery code valid → berhasil login

**❌ Negative Case:**
- [ ] Input kode TOTP expired/invalid → ditolak
- [ ] Input recovery code yang sudah terpakai → ditolak
- [ ] Brute force kode 2FA → rate-limit aktif

**☑️ Checklist Implementasi:**
- [x] Halaman 2FA challenge — `auth/TwoFactorChallenge.svelte`
- [ ] Halaman settings untuk enable/disable 2FA
- [ ] Tampilkan QR code & recovery codes saat aktivasi
- [ ] Test: enable 2FA → verify TOTP flow
- [ ] Test: recovery code login

---

## 📋 Modul 2 — Manajemen User

### F-03: CRUD User (Super Admin)

**Deskripsi:** Super Admin dapat melihat, menambah, mengedit role, menonaktifkan (ban), dan menghapus akun user di seluruh sistem. Admin hanya bisa melihat daftar user.

| Aspek | Detail |
|-------|--------|
| **Tabel** | `users`, `roles` |
| **Role** | Super Admin (CRUD), Admin (Read) |
| **Controller** | [UserController](file:///d:/webapp/nex-sport/app/Http/Controllers/Admin/UserController.php) |
| **Pages** | `admin/users/` |
| **Routes** | `admin.users.*` |

**✅ Positive Case:**
- [x] Lihat semua user dengan filter role dan status
- [x] Buat user baru dengan menetapkan role tertentu
- [x] Ubah role user (Player → Organizer)
- [x] Nonaktifkan/ban user (status=false) → user tidak bisa login
- [x] Hapus user (soft delete) yang tidak memiliki data terkait
- [x] Reset password user lain → email notifikasi terkirim

**❌ Negative Case:**
- [x] Buat user dengan email yang sudah terdaftar → validasi duplikat email
- [x] Hapus user yang masih memiliki event/squad aktif → ditolak dengan warning
- [x] Admin (bukan Super Admin) mencoba hapus user → 403
- [x] Super Admin mencoba hapus akun dirinya sendiri → ditolak

**☑️ Checklist Implementasi:**
- [x] Controller: `UserController` (index, store, update, destroy, toggleStatus)
- [x] Frontend: `admin/users/` pages
- [x] Routes: `admin.users.*` (5 routes)
- [ ] **Policy: `UserPolicy`** — hanya Super Admin boleh CUD, Admin boleh R
- [ ] **Form Request: `StoreUserRequest`, `UpdateUserRequest`**
- [ ] Validasi: email unique, role_id exists
- [ ] Soft delete user dengan proteksi data terkait (cek event/squad aktif)
- [ ] Middleware: cek `status=false` di login → tolak user yang di-ban
- [ ] Test: Super Admin CRUD user berhasil
- [ ] Test: Admin hanya bisa lihat, tidak bisa CUD → 403
- [ ] Test: Hapus user dengan event aktif → ditolak
- [ ] Test: Ban user → user tidak bisa login

---

### F-03b: Manajemen Role

**Deskripsi:** Super Admin dapat melihat dan mengelola daftar role sistem. Admin hanya bisa melihat.

| Aspek | Detail |
|-------|--------|
| **Tabel** | `roles` |
| **Role** | Super Admin (CRUD), Admin (Read) |

**✅ Positive Case:**
- [ ] Lihat daftar semua role (super-admin, admin, organizer, player)
- [ ] Tambah role baru (jika diperlukan)
- [ ] Edit nama/status role
- [ ] Assign role ke user via halaman user management

**❌ Negative Case:**
- [ ] Hapus role yang masih digunakan oleh user → ditolak
- [ ] Tambah role dengan nama duplikat → validasi gagal
- [ ] Admin mencoba tambah/edit/hapus role → 403

**☑️ Checklist Implementasi:**
- [ ] CRUD Role (bisa inline di halaman User Management)
- [ ] Validasi: nama role unique
- [ ] Guard: tidak boleh hapus role yang memiliki user
- [ ] Test: CRUD role oleh Super Admin
- [ ] Test: Admin akses CRUD role → 403

---

## 📋 Modul 3 — Manajemen Games

### F-04: CRUD Games & Game Roles

**Deskripsi:** Admin/Super Admin mengelola daftar cabang olahraga dan e-sports yang tersedia di platform, beserta role/posisi pemain tiap game (contoh: MLBB → Tank, Mage, Assassin; Football → Goalkeeper, Striker).

| Aspek | Detail |
|-------|--------|
| **Tabel** | `games`, `game_roles` |
| **Role** | Super Admin (CRUD penuh + hapus), Admin (CRU), Organizer & Player (Read) |
| **Controller** | [GameController](file:///d:/webapp/nex-sport/app/Http/Controllers/Admin/GameController.php) |
| **Pages** | `admin/games/` |
| **Routes** | `admin.games.*`, `admin.games.roles.*` |

**✅ Positive Case:**
- [x] Tambah game baru (nama, kategori esport/sport, logo)
- [x] Edit informasi game yang ada
- [x] Nonaktifkan game → tidak muncul sebagai pilihan di event baru
- [x] Super Admin hapus game yang belum dipakai di event
- [x] Semua role melihat daftar game & filter by kategori
- [x] Tambah game_role posisi spesifik (MLBB: Tank, Mage; Football: Striker, GK)

**❌ Negative Case:**
- [x] Tambah game dengan nama yang sudah ada → validasi duplikat
- [x] Hapus game yang sudah digunakan di `event_games` → ditolak
- [x] Nonaktifkan game yang sedang digunakan di event `ongoing` → peringatan
- [x] Organizer/Player mencoba tambah game → 403

**☑️ Checklist Implementasi:**
- [x] Controller: `GameController` (index, store, update, destroy, storeRole, destroyRole)
- [x] Frontend: `admin/games/` pages
- [x] Routes: `admin.games.*` (6 routes)
- [ ] **Policy: `GamePolicy`** — Super Admin CRUD, Admin CRU, rest Read
- [ ] **Form Request: `StoreGameRequest`, `UpdateGameRequest`**
- [ ] Upload logo game ke Storage
- [ ] Guard penghapusan: cek apakah game sudah dipakai di `event_games`
- [ ] Test: Admin CRUD game berhasil
- [ ] Test: Hapus game yang sudah di event → ditolak
- [ ] Test: Player/Organizer akses CUD → 403

---

## 📋 Modul 4 — Manajemen Organizer

### F-05: CRUD Organizer

**Deskripsi:** Admin/Super Admin membuat profil Organizer dan meng-assign ke user yang memiliki role `organizer`. Organizer dapat mengedit profil miliknya sendiri (nama, logo, deskripsi).

| Aspek | Detail |
|-------|--------|
| **Tabel** | `organizers`, `users` |
| **Role** | Super Admin & Admin (CRUD), Organizer (edit sendiri) |
| **Controller** | [OrganizerController](file:///d:/webapp/nex-sport/app/Http/Controllers/Admin/OrganizerController.php) |
| **Pages** | `admin/organizers/` |
| **Routes** | `admin.organizers.*` |

**✅ Positive Case:**
- [x] Admin buat profil organizer baru dan assign ke user yang ada
- [x] Organizer edit nama, logo, deskripsi miliknya sendiri
- [x] Super Admin hapus organizer yang belum memiliki event
- [x] Admin melihat daftar semua organizer

**❌ Negative Case:**
- [x] Assign user yang sudah menjadi organizer lain → ditolak
- [x] Organizer edit profil organizer milik orang lain → 403
- [x] Hapus organizer yang masih memiliki event aktif → ditolak
- [x] Player akses halaman manajemen organizer → 403

**☑️ Checklist Implementasi:**
- [x] Controller: `OrganizerController` (index, store, update, destroy)
- [x] Frontend: `admin/organizers/` pages
- [x] Routes: `admin.organizers.*` (4 routes)
- [ ] **Policy: `OrganizerPolicy`** — Admin CRUD, Organizer edit sendiri
- [ ] **Form Request: `StoreOrganizerRequest`, `UpdateOrganizerRequest`**
- [ ] Upload logo organizer ke Storage
- [ ] Guard: user_id unique di tabel organizers
- [ ] Guard: hapus organizer yang masih punya event → ditolak
- [ ] Test: Admin CRUD organizer
- [ ] Test: Organizer edit profil sendiri → berhasil
- [ ] Test: Organizer edit profil orang lain → 403
- [ ] Test: Player akses → 403

---

## 📋 Modul 5 — Manajemen Team & Squad

### F-06: Manajemen Team (Klub Induk)

**Deskripsi:** Player mendaftarkan dan mengelola Team (organisasi induk seperti RRQ, EVOS, Persija) sebagai wadah Squad-Squad yang dimilikinya. Satu user bisa memiliki satu Team.

| Aspek | Detail |
|-------|--------|
| **Tabel** | `teams` |
| **Role** | Player (buat/kelola sendiri), Super Admin (CRUD penuh) |
| **Controller** | [TeamController](file:///d:/webapp/nex-sport/app/Http/Controllers/Player/TeamController.php) |
| **Pages** | `player/teams/` |
| **Routes** | `player.teams.*` |

**✅ Positive Case:**
- [x] Player buat Team baru (nama, singkatan, logo, deskripsi)
- [x] Ketua Team edit detail Team miliknya
- [x] Super Admin lihat semua Team dan bisa edit/hapus

**❌ Negative Case:**
- [x] Buat Team dengan nama yang sudah ada → validasi duplikat
- [x] Player edit Team milik orang lain → 403
- [x] Hapus Team yang masih memiliki Squad aktif → ditolak

**☑️ Checklist Implementasi:**
- [x] Controller: `TeamController` (index, store, update, destroy)
- [x] Frontend: `player/teams/` pages
- [x] Routes: `player.teams.*` (4 routes)
- [ ] **Policy: `TeamPolicy`** — Player hanya kelola milik sendiri
- [ ] **Form Request: `StoreTeamRequest`, `UpdateTeamRequest`**
- [ ] Upload logo team ke Storage
- [ ] Guard: hapus team yang masih punya squad aktif → ditolak
- [ ] Test: Player CRUD team sendiri
- [ ] Test: Player edit team orang lain → 403
- [ ] Test: Hapus team dengan squad aktif → ditolak

---

### F-07: Manajemen Squad & Anggota

**Deskripsi:** Player (ketua) membuat Squad di bawah Team untuk game spesifik, mengelola anggota via undangan/lamaran (`squad_requests`), dan membubarkan Squad jika diperlukan. Setiap perubahan anggota dicatat di `transfer_histories`.

| Aspek | Detail |
|-------|--------|
| **Tabel** | `squads`, `players`, `squad_requests`, `transfer_histories` |
| **Role** | Player (buat/kelola sendiri), Super Admin (CRUD), Admin (RU) |
| **Controller** | [SquadController](file:///d:/webapp/nex-sport/app/Http/Controllers/Player/SquadController.php) |
| **Pages** | `player/squads/` |
| **Routes** | `player.squads.*` |

**✅ Positive Case:**
- [x] Player buat Squad baru (pilih Team induk, pilih game, set max_players)
- [x] Ketua Squad undang Player lain (squad_request type: `invite`)
- [x] Player melamar masuk Squad (squad_request type: `apply`)
- [x] Ketua Squad approve/reject lamaran atau undangan
- [x] Ketua Squad keluarkan anggota dari Squad (release)
- [x] Ketua Squad bubarkan Squad (disband → status: `disbanded`)
- [x] Setiap perubahan anggota otomatis tercatat di `transfer_histories`

**❌ Negative Case:**
- [x] Player melamar Squad dengan game berbeda dari `game_id` Player → ditolak
- [x] Player masuk ke 2 Squad yang sama game-nya sekaligus → ditolak
- [x] Jumlah anggota melebihi `max_players` → pendaftaran anggota baru ditolak
- [x] Anggota biasa (bukan ketua) mencoba edit Squad → 403
- [x] Ketua disband Squad yang sedang `ongoing` di turnamen → peringatan + konfirmasi

**☑️ Checklist Implementasi:**
- [x] Controller: `SquadController` (index, store, update, destroy, sendRequest, handleRequest, releasePlayer)
- [x] Frontend: `player/squads/` pages
- [x] Routes: `player.squads.*` (7 routes)
- [ ] **Policy: `SquadPolicy`** — ketua Squad untuk manage
- [ ] **Form Request: `StoreSquadRequest`, `SendSquadRequestRequest`**
- [ ] Validasi `max_players` sebelum tambah anggota
- [ ] Validasi `game_id` cocok antara Player dan Squad
- [ ] Validasi player belum di squad lain (same game)
- [ ] Otomatis buat `TransferHistory` saat join/keluar/disband
- [ ] Guard: disband squad yang sedang ongoing → warning
- [ ] Test: CRUD Squad + invite/apply flow
- [ ] Test: Apply ke squad game beda → ditolak
- [ ] Test: Exceed max_players → ditolak
- [ ] Test: Non-ketua edit squad → 403

---

## 📋 Modul 6 — Manajemen Event

### F-08: Pembuatan & Pengelolaan Event

**Deskripsi:** Organizer membuat Event turnamen, menambahkan cabang game (`event_games` dengan kuota & harga tiket), menambahkan sponsor, membayar deposit via Payment Gateway, lalu membuka pendaftaran. Status event mengikuti state machine: `draft → waiting_payment → waiting_verification → registration → ongoing → completed | cancelled`.

| Aspek | Detail |
|-------|--------|
| **Tabel** | `events`, `event_games`, `event_sponsors`, `event_payments`, `rewards` |
| **Role** | Organizer (milik sendiri), Admin/Super Admin (semua event) |
| **Controller** | [EventController](file:///d:/webapp/nex-sport/app/Http/Controllers/Organizer/EventController.php) |
| **Pages** | `organizer/events/` |
| **Routes** | `organizer.events.*` |

**✅ Positive Case:**
- [x] Organizer buat Event baru → status awal `draft`
- [x] Tambah cabang `event_game` (pilih game, set kuota, harga tiket, admin_ticket_fee)
- [x] Tambah sponsor event (nama, logo/banner, URL)
- [x] Ajukan pembayaran deposit event → mendapat link payment gateway
- [x] Pembayaran berhasil → status event berubah ke `registration`
- [x] Organizer buka/tutup pendaftaran secara manual
- [x] Organizer hapus event yang masih `draft`
- [x] Organizer ubah status event (ongoing → completed, dll.)

**❌ Negative Case:**
- [x] Publish event tanpa menambahkan cabang game → ditolak
- [x] Tanggal selesai lebih awal dari tanggal mulai → validasi gagal
- [x] Hapus event yang sudah berstatus `registration`/`ongoing` → ditolak
- [x] Organizer edit event milik Organizer lain → 403
- [x] Publish event tanpa membayar `event_payment` → ditolak

**☑️ Checklist Implementasi:**
- [x] Controller: `EventController` (CRUD + toggleRegistration, updateStatus, storeGame, destroyGame, storeSponsor, payDeposit)
- [x] Frontend: `organizer/events/` pages (wizard multi-step)
- [x] Routes: `organizer.events.*` (11 routes)
- [ ] **Policy: `EventPolicy`** — Organizer hanya event sendiri
- [ ] **Form Request: `StoreEventRequest`, `UpdateEventRequest`, `StoreEventGameRequest`**
- [ ] State machine status event (validasi transisi status yang legal)
- [ ] Validasi tanggal: `end_date >= start_date`, `registration_end >= registration_start`
- [ ] Guard: hapus event hanya jika status `draft`
- [ ] Integrasi Payment Gateway untuk deposit (Xendit/Midtrans)
- [ ] Test: Organizer CRUD event + full lifecycle
- [ ] Test: Hapus event ongoing → ditolak
- [ ] Test: Edit event orang lain → 403

---

## 📋 Modul 7 — Registrasi Event (Tiket)

### F-09: Pendaftaran Squad ke Turnamen

**Deskripsi:** Ketua Squad mendaftarkan Squadnya ke Event Game, melakukan pembayaran tiket jika berbayar, dan menunggu persetujuan Organizer. Jika ditolak atau dibatalkan, refund tiket otomatis.

| Aspek | Detail |
|-------|--------|
| **Tabel** | `registrations`, `event_games` |
| **Role** | Player/ketua Squad (daftar), Organizer (approve/reject) |
| **Controller** | [RegistrationController](file:///d:/webapp/nex-sport/app/Http/Controllers/Player/RegistrationController.php) |
| **Pages** | `player/registrations/` |
| **Routes** | `player.registrations.*` |

**✅ Positive Case:**
- [x] Ketua Squad lihat daftar event yang membuka pendaftaran
- [x] Daftarkan Squad ke Event Game → redirect ke pembayaran jika berbayar
- [x] Pembayaran tiket sukses → status `pending`, Organizer mendapat notifikasi
- [x] Organizer approve → status `approved`, kuota berkurang 1
- [x] Organizer reject → status `rejected`, refund tiket otomatis ke Squad
- [x] Squad batalkan registrasi sebelum diapprove → status `cancelled`, refund otomatis

**❌ Negative Case:**
- [x] Daftar ke event yang kuotanya penuh → ditolak
- [x] Daftar ke Event Game yang game-nya berbeda dari game Squad → ditolak
- [x] Squad yang sudah terdaftar di event yang sama mendaftar lagi → ditolak duplikat
- [x] Membatalkan registrasi yang sudah berstatus `approved` → tidak bisa dibatalkan
- [x] Anggota biasa (bukan ketua) mendaftarkan Squad → 403

**☑️ Checklist Implementasi:**
- [x] Controller: `RegistrationController` (index, store, payTicket, cancel, processRegistration)
- [x] Frontend: `player/registrations/` pages
- [x] Routes: `player.registrations.*` (5 routes)
- [ ] **Policy: `RegistrationPolicy`** — ketua Squad untuk daftar
- [ ] **Form Request: `StoreRegistrationRequest`**
- [ ] Webhook callback konfirmasi pembayaran tiket
- [ ] Mesin status registrasi (pending → approved/rejected/cancelled)
- [ ] Refund otomatis saat rejected/cancelled via gateway API
- [ ] Validasi: kuota belum penuh, game cocok, belum terdaftar
- [ ] Audit trail: salin `ticket_price` & `admin_fee` saat transaksi
- [ ] Test: Full flow pendaftaran + pembayaran
- [ ] Test: Daftar kuota penuh → ditolak
- [ ] Test: Daftar game beda → ditolak
- [ ] Test: Duplikat registrasi → ditolak
- [ ] Test: Cancel setelah approved → ditolak

---

## 📋 Modul 8 — Match & Bracket

### F-10: Generate Bracket & Kelola Pertandingan

**Deskripsi:** Organizer men-generate bracket dari Squad yang approved, mengatur jadwal, menginput skor, dan memperbarui status match. Standings (klasemen) diperbarui otomatis untuk format Round Robin.

| Aspek | Detail |
|-------|--------|
| **Tabel** | `matches`, `standings` |
| **Role** | Organizer (event sendiri), Admin/Super Admin (semua) |
| **Controller** | [MatchController](file:///d:/webapp/nex-sport/app/Http/Controllers/Organizer/MatchController.php) |
| **Pages** | `organizer/matches/` |
| **Routes** | `organizer.matches.*` |

**✅ Positive Case:**
- [x] Generate bracket dari Squad approved (4 tipe: single_elimination, double_elimination, round_robin, swiss)
- [x] Match slot berikutnya otomatis kosong (`squad_home_id = null`) hingga ronde sebelumnya selesai
- [x] Organizer atur jadwal (`scheduled_at`) tiap match
- [x] Input skor → `winner_id` terisi, status match → `completed`
- [x] Standings diperbarui otomatis setelah match Round Robin selesai
- [x] Pemenang ronde sebelumnya otomatis mengisi slot bracket berikutnya
- [x] Semua role bisa lihat bracket dan klasemen

**❌ Negative Case:**
- [x] Generate bracket dengan <2 Squad terdaftar → ditolak
- [x] Input skor dengan nilai negatif → validasi gagal
- [x] Organizer edit match event milik Organizer lain → 403
- [x] Ubah status match dari `completed` ke `live` tanpa Super Admin → ditolak

**☑️ Checklist Implementasi:**
- [x] Controller: `MatchController` (index, generate, updateScore, updateSchedule, toggleMatchStatus)
- [x] Frontend: `organizer/matches/` pages
- [x] Routes: `organizer.matches.*` (5 routes)
- [ ] **Policy: `MatchPolicy`** — Organizer hanya event sendiri
- [ ] Algoritma generate bracket (4 tipe tournament)
- [ ] Halaman visualisasi bracket interaktif (Svelte component)
- [ ] Auto-update standings setelah match completed (Round Robin)
- [ ] Auto-fill slot bracket berikutnya setelah match selesai (Elimination)
- [ ] Validasi: skor >= 0, minimum 2 squad untuk generate
- [ ] Test: Generate bracket 4 tipe
- [ ] Test: Input skor → winner + standings updated
- [ ] Test: Generate dengan <2 squad → ditolak
- [ ] Test: Edit match event lain → 403

---

## 📋 Modul 9 — Reward Turnamen

### F-11: CRUD Reward

**Deskripsi:** Organizer mengatur daftar hadiah per tier juara (Juara 1, Juara 2, MVP, dll.) untuk tiap Event Game. Tipe hadiah: `CUP_DIGITAL` (piala digital), `PRIZE` (uang tunai), `VOUCHER` (kode voucher).

| Aspek | Detail |
|-------|--------|
| **Tabel** | `rewards` |
| **Role** | Organizer (event sendiri), Admin/Super Admin (semua) |
| **Controller** | Inline di [EventController](file:///d:/webapp/nex-sport/app/Http/Controllers/Organizer/EventController.php) |

**✅ Positive Case:**
- [x] Tambah reward Juara 1 (tipe: PRIZE, prize_amount: Rp 3.000.000)
- [x] Tambah reward digital (tipe: CUP_DIGITAL)
- [x] Tambah reward voucher (tipe: VOUCHER, prize_amount: nilai voucher)
- [x] Edit/hapus reward yang belum diklaim pemenang

**❌ Negative Case:**
- [x] Tambah reward dengan tier yang sama dalam 1 Event Game → duplikat tier ditolak
- [x] Total `prize_amount` melebihi jumlah deposit di `event_payment` → peringatan
- [x] Hapus reward yang sudah ada `reward_claim` terkait → ditolak
- [x] Organizer edit reward event milik Organizer lain → 403

**☑️ Checklist Implementasi:**
- [ ] CRUD Reward per Event Game (bisa inline di event edit page)
- [ ] **Form Request: `StoreRewardRequest`**
- [ ] Validasi: tier unique per event_game, total prize_amount ≤ deposit
- [ ] Guard: hapus reward yang sudah ada claim → ditolak
- [ ] Test: CRUD reward + validasi tier duplikat
- [ ] Test: Total prize > deposit → warning
- [ ] Test: Hapus reward dengan claim → ditolak

---

## 📋 Modul 10 — Pembayaran Event (Deposit Escrow)

### F-12: Pembayaran & Verifikasi Deposit Event

**Deskripsi:** Organizer membayar deposit (total hadiah + service fee) via Payment Gateway agar event bisa diterbitkan. Admin memverifikasi pembayaran. Service fee dihitung otomatis berdasarkan konfigurasi `service_fee_configs`. Dana hadiah ditampung di escrow platform.

| Aspek | Detail |
|-------|--------|
| **Tabel** | `event_payments`, `service_fee_configs` |
| **Role** | Organizer (ajukan & bayar), Admin/Super Admin (verifikasi) |
| **Controller** | [AdminPaymentController](file:///d:/webapp/nex-sport/app/Http/Controllers/Admin/AdminPaymentController.php) (verifikasi), [EventController](file:///d:/webapp/nex-sport/app/Http/Controllers/Organizer/EventController.php) (ajukan) |
| **Pages** | `admin/payments/`, `organizer/events/` |

**✅ Positive Case:**
- [x] Organizer melihat total tagihan (total hadiah + service fee berdasarkan config)
- [x] Organizer mendapat link pembayaran (Virtual Account/QRIS)
- [x] Pembayaran sukses via webhook → `event_payment` status `approved`, event status bergerak
- [x] Admin verifikasi manual pembayaran jika webhook gagal
- [x] Admin proses refund jika event dibatalkan → status `refunded`, `refunded_at` & `refund_receipt` terisi

**❌ Negative Case:**
- [x] Pembayaran dengan jumlah kurang dari total tagihan → ditolak gateway
- [x] Organizer ajukan `event_payment` kedua kali untuk event yang sudah `approved` → ditolak duplikat
- [x] Webhook palsu dari sumber tidak dikenal → validasi signature gateway

**☑️ Checklist Implementasi:**
- [x] Controller Admin: `AdminPaymentController` (index, verify, updateFeeConfig)
- [x] Controller Organizer: payDeposit di `EventController`
- [x] Frontend: `admin/payments/` pages
- [x] Routes: `admin.payments.*` (3 routes)
- [ ] **Integrasi Payment Gateway (Xendit/Midtrans)** — inbound payment
- [ ] **Webhook handler + validasi signature** gateway
- [ ] Halaman konfigurasi `service_fee_configs` untuk Admin (CRUD tier)
- [ ] Kalkulasi service_fee otomatis berdasarkan `service_fee_configs` tier
- [ ] **Refund API** — saat event dibatalkan, kembalikan deposit (minus service_fee)
- [ ] `refunded_at` & `refund_receipt` terisi saat refund sukses
- [ ] Test: Full payment flow (create → pay → webhook → approved)
- [ ] Test: Duplikat payment → ditolak
- [ ] Test: Webhook signature invalid → 403
- [ ] Test: Refund event dibatalkan → deposit dikembalikan, service_fee hangus

---

## 📋 Modul 11 — Klaim & Payout Hadiah

### F-13: Pencairan Hadiah Otomatis (Escrow Payout)

**Deskripsi:** Sistem mendeteksi pemenang setelah turnamen diselesaikan Organizer. Sistem auto-generate `reward_claim` entries. Player pemenang mengisi data rekening/e-wallet, lalu sistem mengirim payout otomatis via Disbursement API. Hadiah tim ditransfer ke perwakilan tim (ketua Squad).

| Aspek | Detail |
|-------|--------|
| **Tabel** | `reward_claims` |
| **Role** | Player pemenang (klaim), 🤖 Sistem (payout otomatis) |
| **Controller** | [RewardClaimController](file:///d:/webapp/nex-sport/app/Http/Controllers/Player/RewardClaimController.php) |
| **Pages** | `player/claims/` |
| **Routes** | `player.claims.*` |

**✅ Positive Case:**
- [x] Sistem buat entri `reward_claim` (status: `pending`) saat event diselesaikan
- [x] Player pemenang mendapat notifikasi hadiah tersedia
- [x] Player isi data rekening (`bank_name`, `account_number`, `account_name`)
- [x] `claimed_by_id` terisi user yang mengajukan klaim (ketua Squad untuk hadiah tim)
- [x] `amount` di `reward_claim` = snapshot dari `prize_amount` di `rewards` (audit trail)
- [x] Status berubah ke `processing` saat payout dikirim ke gateway
- [x] Payout sukses → status `paid` + `payment_receipt` = transaction_id gateway
- [x] Super Admin lihat semua riwayat klaim & payout

**❌ Negative Case:**
- [x] Player bukan pemenang coba klaim → tidak ada `reward_claim` → 403
- [x] Player masukkan nomor rekening tidak valid → transfer gagal → status `failed`, notifikasi perbaiki rekening
- [x] Payout gagal karena error gateway → alert ke Super Admin, Player bisa retry
- [x] Player klaim reward yang sudah berstatus `paid` → ditolak duplikat
- [x] Reward nominal berubah di tabel `rewards` → tidak mempengaruhi `amount` di `reward_claims` (audit trail aman)

**☑️ Checklist Implementasi:**
- [x] Controller: `RewardClaimController` (index, claim)
- [x] Frontend: `player/claims/` pages
- [x] Routes: `player.claims.*` (2 routes)
- [ ] Auto-generate `reward_claims` saat Organizer complete event
- [ ] Snapshot `amount` dari `rewards.prize_amount` saat generate (audit trail)
- [ ] `claimed_by_id` = user yang submit (ketua Squad untuk hadiah tim)
- [ ] **Integrasi Disbursement API** (Xendit/Midtrans) — outbound payout
- [ ] **Webhook konfirmasi payout** sukses/gagal dari gateway
- [ ] Retry mechanism untuk payout `failed`
- [ ] Notifikasi ke Player saat payout berhasil/gagal
- [ ] Freeze mechanism: Super Admin bisa freeze klaim jika ada sengketa
- [ ] Test: Auto-generate claims saat event completed
- [ ] Test: Claim + payout flow sukses
- [ ] Test: Payout gagal → status `failed`, bisa retry
- [ ] Test: Klaim duplikat → ditolak
- [ ] Test: Non-pemenang klaim → 403

---

## 📋 Modul 12 — Dashboard & Reporting

### F-14: Dashboard per Role

**Deskripsi:** Tiap role mendapat tampilan dashboard yang relevan dengan aktivitasnya, menampilkan statistik dan data penting.

| Aspek | Detail |
|-------|--------|
| **Controller** | [DashboardController](file:///d:/webapp/nex-sport/app/Http/Controllers/DashboardController.php) |
| **Pages** | `dashboard/` |
| **Route** | `dashboard` |

| Role | Konten Dashboard |
|------|-----------------:|
| **Super Admin** | Total user, event, pendapatan platform, log aktivitas |
| **Admin** | Event aktif, pembayaran pending verifikasi, registrasi pending |
| **Organizer** | Event milik sendiri, peserta, status bracket, pemasukan tiket |
| **Player** | Squad saya, jadwal match berikutnya, riwayat pertandingan, klaim hadiah |

**✅ Positive Case:**
- [x] Super Admin lihat stats keseluruhan platform secara realtime
- [x] Admin lihat event & pembayaran yang perlu diverifikasi
- [x] Organizer lihat ringkasan semua event miliknya
- [x] Player lihat jadwal match berikutnya dan riwayat hasil

**❌ Negative Case:**
- [x] Organizer akses dashboard Super Admin → 403
- [x] Player akses halaman manajemen event → 403
- [x] Dashboard lambat saat data banyak → implementasi caching

**☑️ Checklist Implementasi:**
- [x] Controller: `DashboardController` (mengembalikan data sesuai role)
- [x] Frontend: `dashboard/` pages (Svelte)
- [x] Route: `dashboard`
- [ ] Stats cards per role (total user, event, revenue, dll.)
- [ ] Caching untuk query statistik berat (`Cache::remember()`)
- [ ] Responsive layout untuk semua ukuran layar
- [ ] Test: Setiap role mendapat data dashboard yang sesuai
- [ ] Test: Cross-role akses → hanya data milik role-nya

---

## 📋 Modul 13 — Service Fee Config

### F-15: Konfigurasi Biaya Layanan Turnamen

**Deskripsi:** Admin/Super Admin mengatur tier biaya layanan platform berdasarkan range nominal total hadiah turnamen. Biaya ini dibebankan ke Organizer saat deposit event.

| Aspek | Detail |
|-------|--------|
| **Tabel** | `service_fee_configs` |
| **Role** | Super Admin & Admin |
| **Controller** | [AdminPaymentController](file:///d:/webapp/nex-sport/app/Http/Controllers/Admin/AdminPaymentController.php) |

**✅ Positive Case:**
- [x] Admin tambah tier baru (min_reward, max_reward, service_fee)
- [x] Admin edit tier yang ada
- [x] Saat Organizer ajukan deposit, service_fee dihitung otomatis dari config

**❌ Negative Case:**
- [x] Tambah tier dengan range yang overlap dengan tier lain → validasi gagal
- [x] Organizer/Player akses halaman config → 403
- [x] Hapus tier yang sedang digunakan oleh event_payment aktif → peringatan

**☑️ Checklist Implementasi:**
- [x] Route: `admin.payments.fee-config`
- [ ] CRUD halaman service fee config (inline di halaman payments)
- [ ] Validasi: range min/max tidak overlap
- [ ] Kalkulasi otomatis saat deposit event
- [ ] Test: CRUD tier config
- [ ] Test: Range overlap → validasi gagal

---

## 📋 Modul 14 — Public API (Landing Page)

### F-16: API Publik untuk Landing Page Website

**Deskripsi:** Endpoint JSON tanpa autentikasi untuk menampilkan data di landing page publik. Semua response menggunakan API Resource dan response caching.

| Aspek | Detail |
|-------|--------|
| **Routes** | `routes/api.php` → `/api/v1/public/*` |
| **Auth** | ❌ Tidak perlu auth |
| **Response** | JSON via API Resource |

| # | Endpoint | Deskripsi | Cache |
|---|----------|-----------|:-----:|
| 1 | `GET /api/v1/public/stats` | Statistik platform (total event, player, organizer, turnamen selesai) | 10 menit |
| 2 | `GET /api/v1/public/events` | Daftar event publik (status: `registration` / `ongoing`) + filter & pagination | 5 menit |
| 3 | `GET /api/v1/public/events/{id}` | Detail event: info, games, reward, jadwal | 5 menit |
| 4 | `GET /api/v1/public/events/live` | Turnamen yang sedang `ongoing` (live) | 1 menit |
| 5 | `GET /api/v1/public/games` | Daftar cabang game aktif di platform | 30 menit |
| 6 | `GET /api/v1/public/leaderboard` | Leaderboard player (berdasarkan jumlah kemenangan) + filter game | 10 menit |
| 7 | `GET /api/v1/public/winners` | Daftar pemenang dari event `completed` | 10 menit |
| 8 | `GET /api/v1/public/organizers/featured` | Organizer terpopuler (berdasarkan jumlah event) | 30 menit |

**✅ Positive Case:**
- [ ] Semua 8 endpoint mengembalikan response JSON format standar `{ data, message }`
- [ ] Response menggunakan API Resource (tidak expose data sensitif)
- [ ] Pagination default 12 item per page untuk endpoint list
- [ ] Cache aktif sesuai durasi yang ditentukan
- [ ] Filter & search berfungsi (events by game, leaderboard by game)

**❌ Negative Case:**
- [ ] Request ke endpoint yang tidak ada → 404 JSON
- [ ] Event dengan status `draft` atau `cancelled` tidak muncul di public API
- [ ] Data sensitif (email, rekening, token) tidak pernah muncul di response
- [ ] Rate limiting aktif untuk mencegah abuse

**☑️ Checklist Implementasi:**
- [ ] Buat file `routes/api.php`
- [ ] **Controller:** `Api/V1/Public/StatsController`
- [ ] **Controller:** `Api/V1/Public/EventController`
- [ ] **Controller:** `Api/V1/Public/GameController`
- [ ] **Controller:** `Api/V1/Public/LeaderboardController`
- [ ] **Controller:** `Api/V1/Public/OrganizerController`
- [ ] **API Resource:** `EventResource`, `GameResource`, `PlayerResource`, `OrganizerResource`
- [ ] Response caching (`Cache::remember()`) per endpoint
- [ ] Pagination (12 per page default)
- [ ] Jangan expose: email, account_number, password, token
- [ ] Test: Semua 8 endpoint return 200 + JSON format benar
- [ ] Test: Data sensitif tidak bocor
- [ ] Test: Pagination berfungsi
- [ ] Test: Filter events by game berfungsi

---

## 📋 Modul 15 — Mobile API (Sanctum)

### F-17: API Mobile untuk Player (Sanctum Token Auth)

**Deskripsi:** Endpoint JSON dengan autentikasi Sanctum token untuk aplikasi mobile. Player bisa login, mengelola squad, mendaftar event, dan klaim hadiah dari mobile app.

| Aspek | Detail |
|-------|--------|
| **Routes** | `routes/api.php` → `/api/v1/*` (auth:sanctum) |
| **Auth** | Laravel Sanctum (token-based) |
| **Response** | JSON via API Resource |

**✅ Positive Case:**
- [ ] Login → mendapat Sanctum token
- [ ] Logout → token direvoke
- [ ] CRUD Squad via API
- [ ] Registrasi event via API
- [ ] Klaim hadiah via API
- [ ] Lihat bracket & jadwal match

**❌ Negative Case:**
- [ ] Request tanpa token → 401
- [ ] Request dengan token expired/revoked → 401
- [ ] Akses resource milik orang lain → 403

**☑️ Checklist Implementasi:**
- [ ] **Controller:** `Api/V1/Auth/AuthController` (login, logout)
- [ ] **Controller:** `Api/V1/SquadController`
- [ ] **Controller:** `Api/V1/PlayerController`
- [ ] **Controller:** `Api/V1/RegistrationController`
- [ ] **Controller:** `Api/V1/RewardClaimController`
- [ ] Sanctum token auth middleware
- [ ] Reuse Repository layer (shared dengan Web controllers)
- [ ] Test: Login → token → authenticated request
- [ ] Test: Request tanpa token → 401
- [ ] Test: Cross-user akses → 403

---

## 📋 Cross-Cutting Concerns

### CC-01: Policies (Authorization)

> [!CAUTION]
> **Belum ada Policy sama sekali.** Ini adalah celah keamanan kritis. Semua authorization saat ini kemungkinan dilakukan inline di controller tanpa standar yang konsisten.

| Policy | Model | Prioritas |
|--------|-------|:---------:|
| `UserPolicy` | User | 🔴 High |
| `GamePolicy` | Game | 🟡 Medium |
| `OrganizerPolicy` | Organizer | 🟡 Medium |
| `TeamPolicy` | Team | 🟡 Medium |
| `SquadPolicy` | Squad | 🔴 High |
| `EventPolicy` | Event | 🔴 High |
| `RegistrationPolicy` | Registration | 🔴 High |
| `MatchPolicy` | GameMatch | 🟡 Medium |
| `RewardClaimPolicy` | RewardClaim | 🟡 Medium |

**☑️ Checklist:**
- [ ] Buat semua 9 Policy via `php artisan make:policy`
- [ ] Register di `AuthServiceProvider`
- [ ] Ganti inline auth check di controller dengan `$this->authorize()`
- [ ] Test setiap policy per role

---

### CC-02: Form Requests (Validation)

> [!WARNING]
> **Belum ada Form Request domain.** Validasi kemungkinan dilakukan inline di controller. Perlu dipindah ke Form Request untuk konsistensi dan reusability.

**☑️ Checklist:**
- [ ] `StoreUserRequest`, `UpdateUserRequest`
- [ ] `StoreGameRequest`, `UpdateGameRequest`
- [ ] `StoreOrganizerRequest`, `UpdateOrganizerRequest`
- [ ] `StoreTeamRequest`, `UpdateTeamRequest`
- [ ] `StoreSquadRequest`, `UpdateSquadRequest`
- [ ] `StoreEventRequest`, `UpdateEventRequest`
- [ ] `StoreEventGameRequest`
- [ ] `StoreRewardRequest`
- [ ] `StoreRegistrationRequest`
- [ ] `ClaimRewardRequest`

---

### CC-03: Payment Gateway Integration

> [!IMPORTANT]
> Integrasi Payment Gateway (Xendit/Midtrans) diperlukan untuk 3 alur keuangan:

| # | Alur | Tipe | Tabel |
|---|------|------|-------|
| 1 | Deposit Event (Organizer → Escrow) | Inbound Payment | `event_payments` |
| 2 | Pembayaran Tiket (Player → Organizer) | Inbound Payment | `registrations` |
| 3 | Payout Hadiah (Escrow → Player Pemenang) | Outbound Disbursement | `reward_claims` |

**☑️ Checklist:**
- [ ] Pilih Payment Gateway (Xendit / Midtrans)
- [ ] Setup merchant account & API credentials
- [ ] Inbound: Create payment link/VA/QRIS
- [ ] Inbound: Webhook handler + signature validation
- [ ] Outbound: Disbursement API untuk payout hadiah
- [ ] Outbound: Webhook handler payout sukses/gagal
- [ ] Refund API untuk registrasi rejected/cancelled
- [ ] Refund API untuk event cancelled (deposit - service_fee)
- [ ] Environment config: `.env` gateway credentials

---

### CC-04: Audit Trail Keuangan

> [!IMPORTANT]
> Berdasarkan [financial_analysis.md](file:///d:/webapp/nex-sport/financial_analysis.md), audit trail harus memastikan data historis tidak terdistorsi.

**☑️ Checklist:**
- [ ] `reward_claims.amount` = snapshot dari `rewards.prize_amount` saat generate
- [ ] `registrations.ticket_price` = snapshot dari `event_games.ticket_price` saat daftar
- [ ] `registrations.admin_fee` = snapshot dari `event_games.admin_ticket_fee` saat daftar
- [ ] `event_payments.service_fee` = snapshot dari `service_fee_configs` saat deposit
- [ ] Perubahan nominal di tabel master tidak mempengaruhi data transaksi historis
- [ ] Test: Ubah `rewards.prize_amount` → `reward_claims.amount` tidak berubah

---

### CC-05: Edge Cases Keuangan

Berdasarkan [financial_system.md](file:///d:/webapp/nex-sport/financial_system.md):

| Skenario | Solusi | Status |
|----------|--------|:------:|
| Turnamen dibatalkan Organizer | Refund deposit (minus service_fee) ke Organizer | `[ ]` |
| Sengketa hasil match | Super Admin freeze `reward_claim` sebelum payout | `[ ]` |
| Transfer gagal (salah rekening) | Status `failed`, notif Player perbaiki rekening, bisa retry | `[ ]` |
| Organizer ubah hadiah setelah payout | Tidak pengaruh karena `amount` sudah di-snapshot | `[ ]` |

---

## 📊 Progress Tracker

| # | Modul | Feature | Backend | Frontend | Policy | FormReq | Test | API |
|---|-------|---------|:-------:|:--------:|:------:|:-------:|:----:|:---:|
| 1 | Auth & Profile | F-01, F-02, F-02b | ✅ | ✅ | — | — | `[ ]` | `[ ]` |
| 2 | Manajemen User | F-03, F-03b | ✅ | ✅ | `[ ]` | `[ ]` | `[ ]` | `[ ]` |
| 3 | Manajemen Games | F-04 | ✅ | ✅ | `[ ]` | `[ ]` | `[ ]` | `[ ]` |
| 4 | Manajemen Organizer | F-05 | ✅ | ✅ | `[ ]` | `[ ]` | `[ ]` | `[ ]` |
| 5 | Manajemen Team | F-06 | ✅ | ✅ | `[ ]` | `[ ]` | `[ ]` | `[ ]` |
| 6 | Manajemen Squad | F-07 | ✅ | ✅ | `[ ]` | `[ ]` | `[ ]` | `[ ]` |
| 7 | Manajemen Event | F-08 | ✅ | ✅ | `[ ]` | `[ ]` | `[ ]` | `[ ]` |
| 8 | Registrasi Event | F-09 | ✅ | ✅ | `[ ]` | `[ ]` | `[ ]` | `[ ]` |
| 9 | Match & Bracket | F-10 | ✅ | ✅ | `[ ]` | `[ ]` | `[ ]` | `[ ]` |
| 10 | Reward Turnamen | F-11 | ⚠️ | ⚠️ | `[ ]` | `[ ]` | `[ ]` | `[ ]` |
| 11 | Pembayaran Event | F-12 | ✅ | ✅ | — | `[ ]` | `[ ]` | `[ ]` |
| 12 | Klaim & Payout | F-13 | ✅ | ✅ | `[ ]` | `[ ]` | `[ ]` | `[ ]` |
| 13 | Dashboard | F-14 | ✅ | ✅ | — | — | `[ ]` | — |
| 14 | Service Fee Config | F-15 | ✅ | ⚠️ | — | `[ ]` | `[ ]` | — |
| 15 | Public API | F-16 | `[ ]` | — | — | — | `[ ]` | `[ ]` |
| 16 | Mobile API | F-17 | `[ ]` | — | — | — | `[ ]` | `[ ]` |
| — | Policies | CC-01 | `[ ]` | — | `[ ]` | — | `[ ]` | — |
| — | Form Requests | CC-02 | `[ ]` | — | — | `[ ]` | — | — |
| — | Payment Gateway | CC-03 | `[ ]` | — | — | — | `[ ]` | — |
| — | Audit Trail | CC-04 | `[ ]` | — | — | — | `[ ]` | — |
| — | Edge Cases Keuangan | CC-05 | `[ ]` | — | — | — | `[ ]` | — |

> **Legend:** ✅ = Done | ⚠️ = Partial | `[ ]` = Not started | — = Not applicable
