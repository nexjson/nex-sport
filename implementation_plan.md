# Analisis & Revisi Rancangan NEX-Sport

Hasil review mendalam terhadap struktur data di [nex-sport.xlsx](file:///d:/webapp/nex-sport/nex-sport.xlsx).

---

## 🔴 Masalah yang Perlu Diperbaiki

### 1. Typo: "orgnaizer" → "organizer"
Muncul di 3 tempat: tabel Roles (baris 4), nama tabel Organizer (baris 20), dan tabel User roles (baris 10).

```diff
- orgnaizer
+ organizer
```

### 2. Duplicate ID di Tabel Games
Game `Football` dan `PUBG Mobile` sama-sama menggunakan **id = 2**. `Football` seharusnya **id = 3**.

| id | name | category | status |
|----|------|----------|--------|
| 1 | Mobile Legends | esport | ✅ |
| 2 | PUBG Mobile | esport | ✅ |
| ~~2~~ → **3** | Football | sport | ✅ |
| ~~3~~ → **4** | Volleyball | sport | ❌ |

### 3. Tabel Event — Kolom Ambigu
Data event terlihat membingungkan:

| No | name | description | banner | organizer_id |
|----|------|-------------|--------|--------------|
| 1 | Tournament 17 Agustus | **Sport** | **Football** | 1 |
| | | **Esport** | **ML** | 1 |

> [!WARNING]
> Kolom `description` diisi "Sport"/"Esport" (seharusnya deskripsi event), dan `banner` diisi "Football"/"ML" (seharusnya path gambar banner). Ini terlihat seperti sub-kategori game, bukan description & banner yang sebenarnya. Relasi game ke event sudah ditangani oleh tabel **Event Games**.

### 4. Tabel Squad — Baris Data Rusak
Baris data contoh memiliki literal text `"id"` di kolom id, bukan angka:

```diff
- id, "req regum qeon", "RRQ", ...
+ 1, "Rex Regum Qeon", "RRQ", ...
```

### 5. Tabel Organizer — `user_id` Kosong
Kolom `user_id` didefinisikan tapi tidak diisi di sample data, padahal ini relasi penting untuk menentukan siapa yang mengelola organizer.

### 6. Tabel Player — Tidak Ada Sample Data
Hanya header tanpa data contoh, sehingga sulit memvalidasi struktur.

### 7. Tabel Roles — Redundan dengan Kolom User
Tabel Roles ada, tapi di tabel User kolom `roles` langsung menyimpan **nama role** (`"super-admin"`) bukan **role_id** (FK). Ini inkonsisten — seharusnya menggunakan foreign key.

```diff
  USER table:
- roles: "super-admin"  (string literal)
+ role_id: 1             (FK → Roles.id)
```

---

## 🟡 Elemen yang Perlu Ditambahkan

### A. Tabel yang Hilang (Kritis)

#### 1. 🏢 `Team` — Organisasi Induk Tim
Menampung divisi-divisi squad di bawah satu bendera organisasi (misalnya RRQ memiliki RRQ Hoshi, RRQ Sena, dll).

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | int PK | |
| name | string | Nama tim (e.g., "Rex Regum Qeon") |
| short_name | string | Singkatan (e.g., "RRQ") |
| logo | string | Path file logo |
| description | string | Deskripsi tim |
| user_id | int FK | Pemilik/Manager tim (FK to User) |
| status | boolean | Status aktif tim |

#### 2. 🛡️ `GameRole` — Role/Posisi dalam Game
Menampung role spesifik untuk setiap game (e.g., MLBB: Tank, Mage, Assassin; Football: Goalkeeper, Striker).

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | int PK | |
| game_id | int FK | Menghubungkan ke Game spesifik |
| name | string | Nama role (e.g., "Tank", "Striker") |
| description | string (null) | Deskripsi opsional |

#### 3. 🔄 `TransferHistory` — Bursa & Riwayat Transfer Player
Mencatat setiap perpindahan pemain antar squad untuk mendukung history bursa transfer.

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | int PK | |
| player_id | int FK | Pemain yang ditransfer |
| from_squad_id | int FK (null) | Squad asal (null jika sebelumnya free agent) |
| to_squad_id | int FK (null) | Squad tujuan (null jika dilepas jadi free agent) |
| transfer_type | string | Jenis: "join", "transfer", "release", "disband" |
| transfer_fee | int (null) | Biaya transfer jika ada |
| transfer_date | datetime | Tanggal efektif transfer |

#### 4. 📨 `SquadRequest` — Request Join & Invite Player
Mengatur proses lamaran dari Player ke Squad, atau undangan dari Squad ke Player.

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | int PK | |
| squad_id | int FK | Squad yang dilamar/mengundang |
| player_id | int FK | Player yang melamar/diundang |
| type | string | `"apply"` (lamaran player) atau `"invite"` (undangan squad) |
| status | enum | `"pending"`, `"approved"`, `"rejected"`, `"cancelled"` |
| notes | string (null) | Catatan opsional |
| created_at | datetime | Waktu dibuat |
| updated_at | datetime | Waktu di-update |

#### 5. 🏆 `Match` — Pertandingan
Inti dari tournament management! Tanpa ini, tidak bisa melacak pertandingan.

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | int PK | |
| event_games_id | int FK | Game dalam event mana |
| round | int | Ronde ke-berapa |
| match_order | int | Urutan match dalam ronde |
| squad_home_id | int FK | Tim tuan rumah |
| squad_away_id | int FK | Tim tamu |
| score_home | int | Skor tim home |
| score_away | int | Skor tim away |
| winner_id | int FK | Squad pemenang |
| status | enum | `scheduled`, `live`, `completed`, `cancelled` |
| scheduled_at | datetime | Jadwal pertandingan |

#### 6. 📝 `Registration` — Pendaftaran Squad ke Event
Menghubungkan squad dengan event games (many-to-many).

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | int PK | |
| squad_id | int FK | Tim yang mendaftar |
| event_games_id | int FK | Game dalam event |
| status | enum | `pending`, `approved`, `rejected` |
| payment_status | enum | `free`, `unpaid`, `paid`, `refunded` |
| ticket_price | int | Harga tiket dasar yang berlaku saat transaksi |
| admin_fee | int | Biaya layanan admin yang berlaku saat transaksi |
| amount_paid | int | Total nominal yang dibayarkan (ticket_price + admin_fee) |
| payment_method | string (null) | Metode pembayaran (Bank, E-Wallet, dll) |
| payment_receipt | string (null) | ID Transaksi / Bukti Bayar pendaftaran |
| paid_at | datetime (null) | Waktu pembayaran pendaftaran |
| refunded_at | datetime (null) | Waktu refund jika pendaftaran ditolak |
| refund_receipt | string (null) | Bukti transfer / ID Transaksi Refund pendaftaran |
| registered_at | datetime | Waktu pendaftaran |

#### 7. 📊 `Standing` — Klasemen (untuk Round Robin)
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | int PK | |
| event_games_id | int FK | |
| squad_id | int FK | |
| wins | int | Jumlah menang |
| losses | int | Jumlah kalah |
| draws | int | Jumlah seri |
| points | int | Total poin |

#### 8. 💳 `EventPayment` — Verifikasi Penerbitan Event
Mencatat pembayaran organizer untuk penerbitan event dengan reward berbayar (PRICE/VOUCHER).

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | int PK | |
| event_id | int FK | Event yang dimohonkan verifikasi |
| amount | int | Nominal pembayaran (Total reward + biaya layanan) |
| service_fee | int | Biaya layanan platform |
| payment_receipt | string (null) | Foto bukti pembayaran |
| voucher_code | string (null) | Kode voucher diskon/gratis jika digunakan |
| status | enum | `"pending"`, `"approved"`, `"rejected"`, `"refunded"` |
| verified_by_id | int FK (null) | Admin yang memverifikasi |
| verified_at | datetime (null) | Tanggal verifikasi |
| refunded_at | datetime (null) | Tanggal pengembalian dana (refund) |
| refund_receipt | string (null) | Bukti transfer / ID Transaksi Refund |
| created_at | datetime | Waktu request pembayaran |

#### 9. 🎁 `RewardClaim` — Klaim & Payout Hadiah
Mencatat detail penyerahan reward kepada pemenang setelah turnamen selesai.

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | int PK | |
| reward_id | int FK | Hadiah yang didapatkan |
| amount | int | Nominal hadiah yang dibayarkan (Audit Trail) |
| squad_id | int FK (null) | Tim pemenang (jika reward tim) |
| player_id | int FK (null) | Player pemenang (jika reward individu/MVP) |
| claimed_by_id | int FK | User yang mengajukan penarikan/klaim |
| status | enum | `"pending"`, `"processing"`, `"paid"`, `"failed"` |
| payment_method | string (null) | Metode pembayaran (Bank, E-Wallet, dll) |
| bank_name | string (null) | Nama Bank/E-Wallet tujuan (e.g. BCA, GOPAY) |
| account_number | string (null) | Nomor Rekening / No. HP E-Wallet |
| account_name | string (null) | Nama Pemilik Rekening |
| payment_receipt | string (null) | Bukti transfer / ID Transaksi Payout Gateway otomatis |
| claimed_at | datetime | Waktu klaim tercatat |
| paid_at | datetime (null) | Waktu dana ditransfer |

#### 10. ⚙️ `ServiceFeeConfig` — Konfigurasi Biaya Layanan Turnamen
Menyimpan range nominal total reward dan besaran biaya layanan yang diatur oleh Admin.

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | int PK | |
| min_reward | int | Batas bawah total reward |
| max_reward | int | Batas atas total reward |
| service_fee | int | Nominal biaya layanan yang dikenakan |
| created_at | datetime | Waktu dibuat |
| updated_at | datetime | Waktu di-update |

#### 11. 🤝 `EventSponsor` — Sponsor Event Turnamen
Mencatat daftar sponsor yang ditambahkan oleh Organizer untuk sebuah event turnamen.

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | int PK | |
| event_id | int FK | Menghubungkan ke Event terkait |
| name | string | Nama sponsor |
| banner | string | Path file gambar banner/logo sponsor |
| url | string (null) | URL website/sosmed sponsor |
| created_at | datetime | Waktu dibuat |
| updated_at | datetime | Waktu di-update |

---

### B. Kolom yang Hilang di Tabel Existing

#### 12. 🔐 User — Authentication Fields
```
+ password        (string)  — Hash password
+ email_verified   (boolean) — Status verifikasi email
+ last_login       (datetime)
```

#### 13. 📅 Event — Waktu & Status
```
+ start_date      (datetime) — Tanggal mulai
+ end_date        (datetime) — Tanggal selesai
+ registration_start (datetime) — Buka pendaftaran
+ registration_end   (datetime) — Tutup pendaftaran
+ status          (enum: draft, waiting_payment, waiting_verification, registration, ongoing, completed, cancelled) — Status event, termasuk approval berbayar
+ location        (string)   — Lokasi event (untuk sport)
+ tournament_type  (enum: single_elimination, double_elimination, round_robin, swiss)
```

#### 14. 🏅 Reward — Detail Tipe Hadiah
```
+ reward_type     (enum: CUP_DIGITAL | PRIZE | VOUCHER) — Tipe hadiah
+ title           (string)  — "Juara 1", "Juara 2", "MVP"
+ description     (string)  — Detail hadiah
+ prize_amount    (int, null) — Nominal uang jika PRIZE, nilai voucher jika VOUCHER
```

#### 15. 👥 Squad — Menghubungkan ke Team & Game
```
+ team_id         (int FK)  — Menghubungkan squad dengan Team induk
+ game_id         (int FK)  — Menghubungkan dengan Game spesifik (e.g., MLBB)
+ max_players     (int)     — Batas anggota (5 untuk ML, 11 untuk Football)
+ status          (enum: active, inactive, disbanded)
```

#### 16. 🎮 Player — Pembatasan 1 Squad & Detail
```
+ user_id         (int FK)  — Menghubungkan Player dengan User
+ squad_id        (int FK, null) — Foreign Key ke Squad (nullable jika free agent). 1 Player hanya bisa di 1 Squad pada satu waktu.
+ game_id         (int FK)  — Game utama/divisi game yang dipilih saat registrasi mandiri (e.g., Mobile Legends). Player hanya bisa masuk/melamar ke Squad dengan game_id yang sama.
+ game_role_id    (int FK)  — Role game yang dimiliki (FK to GameRole, e.g. Tank, Striker, Mage). Harus sesuai dengan game_id yang dipilih.
+ jersey_number   (int)     — Nomor punggung (sport)
```

#### 17. ⏱️ Semua Tabel — Timestamps
```
+ created_at      (datetime)
+ updated_at      (datetime)
```

---

### C. Fitur Tambahan yang Disarankan

| # | Fitur | Keterangan |
|---|-------|------------|
| 12 | **Bracket Generator** | Auto-generate bracket dari peserta terdaftar |
| 13 | **Bursa Transfer UI** | Halaman khusus melihat riwayat perpindahan player dan form transfer pemain |
| 14 | **Notification/Log** | Tabel activity log untuk tracking perubahan |
| 15 | **Media/Gallery** | Upload foto/dokumentasi event |

---

## Revisi ERD Lengkap

```mermaid
erDiagram
    roles ||--o{ users : "has"
    users ||--o{ organizers : "manages"
    users ||--o{ teams : "owns"
    users ||--o{ players : "has profile"
    users ||--o{ reward_claims : "claims"
    teams ||--o{ squads : "has divisions"
    games ||--o{ squads : "played_in"
    games ||--o{ game_roles : "has roles"
    game_roles ||--o{ players : "assigned to"
    squads ||--o{ players : "contains"
    squads ||--o{ transfer_histories : "from/to"
    squads ||--o{ squad_requests : "receives/sends"
    players ||--o{ transfer_histories : "transferred"
    players ||--o{ squad_requests : "sends/receives"
    organizers ||--o{ events : "creates"
    events ||--o{ event_games : "includes"
    events ||--o{ event_payments : "verifies publishing"
    events ||--o{ event_sponsors : "sponsored by"
    games ||--o{ event_games : "played_in"
    event_games ||--o{ rewards : "prizes"
    rewards ||--o{ reward_claims : "awarded"
    squads ||--o{ reward_claims : "receives"
    players ||--o{ reward_claims : "receives"
    event_games ||--o{ matches : "scheduled_in"
    event_games ||--o{ registrations : "accepts"
    event_games ||--o{ standings : "ranks"
    squads ||--o{ registrations : "registers"
    squads ||--o{ standings : "ranked_in"
    squads ||--o{ matches : "plays_home"
    squads ||--o{ matches : "plays_away"

    roles {
        int id PK
        string name
        boolean status
        datetime created_at
        datetime updated_at
    }
    users {
        int id PK
        string username UK
        string name
        string email UK
        string password
        string phone
        string images
        int role_id FK
        boolean status
        boolean email_verified
        datetime last_login
        datetime created_at
        datetime updated_at
    }
    games {
        int id PK
        string name
        enum category "esport | sport"
        boolean status
        datetime created_at
        datetime updated_at
    }
    game_roles {
        int id PK
        int game_id FK
        string name
        string description
    }
    organizers {
        int id PK
        string name
        string logo
        string description
        boolean status
        int user_id FK
        datetime created_at
        datetime updated_at
    }
    teams {
        int id PK
        string name
        string short_name
        string logo
        string description
        int user_id FK
        boolean status
        datetime created_at
        datetime updated_at
    }
    squads {
        int id PK
        int team_id FK
        int game_id FK
        string name
        string short_name
        string logo
        int max_players
        enum status "active | inactive | disbanded"
        datetime created_at
        datetime updated_at
    }
    players {
        int id PK
        int user_id FK
        string name
        string nickname
        string photo
        int game_role_id FK
        int squad_id FK "Nullable"
        int game_id FK
        int jersey_number
        datetime created_at
        datetime updated_at
    }
    transfer_histories {
        int id PK
        int player_id FK
        int from_squad_id FK "Nullable"
        int to_squad_id FK "Nullable"
        string transfer_type
        int transfer_fee "Nullable"
        datetime transfer_date
        datetime created_at
    }
    squad_requests {
        int id PK
        int squad_id FK
        int player_id FK
        string type "apply | invite"
        enum status "pending | approved | rejected | cancelled"
        string notes "Nullable"
        datetime created_at
        datetime updated_at
    }
    events {
        int id PK
        string name
        string description
        string banner
        int organizer_id FK
        enum tournament_type "single_elimination | double_elimination | round_robin | swiss"
        string location
        datetime start_date
        datetime end_date
        datetime registration_start
        datetime registration_end
        enum status "draft | waiting_payment | waiting_verification | registration | ongoing | completed | cancelled"
        datetime created_at
        datetime updated_at
    }
    event_payments {
        int id PK
        int event_id FK
        int amount
        int service_fee
        string payment_receipt "Nullable"
        string voucher_code "Nullable"
        enum status "pending | approved | rejected | refunded"
        int verified_by_id FK "Nullable"
        datetime verified_at "Nullable"
        datetime refunded_at "Nullable"
        string refund_receipt "Nullable"
        datetime created_at
    }
    event_sponsors {
        int id PK
        int event_id FK
        string name
        string banner
        string url "Nullable"
        datetime created_at
        datetime updated_at
    }
    event_games {
        int id PK
        int games_id FK
        int event_id FK
        int ticket_price
        int max_participants
        int admin_ticket_fee
        datetime created_at
    }
    rewards {
        int id PK
        int event_games_id FK
        string reward_type "CUP_DIGITAL | PRIZE | VOUCHER"
        int tier
        string title
        string description
        int prize_amount "Nullable"
        datetime created_at
        datetime updated_at
    }
    reward_claims {
        int id PK
        int reward_id FK
        int amount
        int squad_id FK "Nullable"
        int player_id FK "Nullable"
        int claimed_by_id FK
        enum status "pending | processing | paid | failed"
        string payment_method "Nullable"
        string bank_name "Nullable"
        string account_number "Nullable"
        string account_name "Nullable"
        string payment_receipt "Nullable"
        datetime claimed_at
        datetime paid_at "Nullable"
    }
    matches {
        int id PK
        int event_games_id FK
        int round
        int match_order
        int squad_home_id FK "Nullable"
        int squad_away_id FK "Nullable"
        int score_home
        int score_away
        int winner_id FK "Nullable"
        enum status "scheduled | live | completed | cancelled"
        datetime scheduled_at
        datetime created_at
        datetime updated_at
    }
    registrations {
        int id PK
        int squad_id FK
        int event_games_id FK
        enum status "pending | approved | rejected"
        enum payment_status "free | unpaid | paid | refunded"
        int ticket_price
        int admin_fee
        int amount_paid
        string payment_method "Nullable"
        string payment_receipt "Nullable"
        datetime paid_at "Nullable"
        datetime refunded_at "Nullable"
        string refund_receipt "Nullable"
        datetime registered_at
        datetime created_at
    }
    standings {
        int id PK
        int event_games_id FK
        int squad_id FK
        int wins
        int losses
        int draws
        int points
        datetime updated_at
    }
    service_fee_configs {
        int id PK
        int min_reward
        int max_reward
        int service_fee
        datetime created_at
        datetime updated_at
    }
```

---

## Ringkasan

| Kategori | Jumlah |
|----------|--------|
| 🔴 Bug / Error data | **7** masalah |
| 🟡 Tabel baru yang kritis | **3** tabel (Match, Registration, Standing) |
| 🟡 Kolom baru yang penting | **~15** kolom tersebar di 6 tabel |
| 🟢 Fitur tambahan (opsional) | **3** fitur |

> [!IMPORTANT]
> **Rekomendasi**: Setujui revisi ini sebagai blueprint final, lalu saya akan langsung mulai membangun aplikasinya dengan struktur database yang sudah diperbaiki. Apakah ada yang ingin ditambah/ubah lagi?
