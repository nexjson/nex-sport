# NEX-Sport — Feature List per Role

Daftar lengkap fitur dan hak akses untuk setiap role dalam aplikasi.

---

## Ringkasan Role

| Role | Deskripsi | Scope Akses |
|------|-----------|-------------|
| 🔴 **Super Admin** | Pengelola sistem keseluruhan | Full access — semua fitur & data |
| 🟠 **Admin** | Pengelola operasional | Manajemen data games, event, dan user |
| 🟡 **Organizer** | Penyelenggara tournament | Hanya event milik sendiri |
| 🟢 **Player** | Peserta tournament | Profil, squad, dan registrasi event |

---

## 1. 🔐 Authentication & Profile

| Fitur | Super Admin | Admin | Organizer | Player |
|-------|:-----------:|:-----:|:---------:|:------:|
| Login / Logout | ✅ | ✅ | ✅ | ✅ |
| Edit profil sendiri | ✅ | ✅ | ✅ | ✅ |
| Ganti password | ✅ | ✅ | ✅ | ✅ |
| Upload foto profil | ✅ | ✅ | ✅ | ✅ |

---

## 2. 👥 Manajemen User

| Fitur | Super Admin | Admin | Organizer | Player |
|-------|:-----------:|:-----:|:---------:|:------:|
| Lihat semua user | ✅ | ✅ | ❌ | ❌ |
| Tambah user baru | ✅ | ❌ | ❌ | ❌ |
| Edit user (termasuk role) | ✅ | ❌ | ❌ | ❌ |
| Nonaktifkan / ban user | ✅ | ❌ | ❌ | ❌ |
| Hapus user | ✅ | ❌ | ❌ | ❌ |
| Reset password user lain | ✅ | ❌ | ❌ | ❌ |

---

## 3. 🛡️ Manajemen Role

| Fitur | Super Admin | Admin | Organizer | Player |
|-------|:-----------:|:-----:|:---------:|:------:|
| Lihat daftar role | ✅ | ✅ | ❌ | ❌ |
| Tambah role baru | ✅ | ❌ | ❌ | ❌ |
| Edit role | ✅ | ❌ | ❌ | ❌ |
| Hapus role | ✅ | ❌ | ❌ | ❌ |
| Assign role ke user | ✅ | ❌ | ❌ | ❌ |

---

## 4. 🎮 Manajemen Games

| Fitur | Super Admin | Admin | Organizer | Player |
|-------|:-----------:|:-----:|:---------:|:------:|
| Lihat daftar games | ✅ | ✅ | ✅ | ✅ |
| Tambah game baru | ✅ | ✅ | ❌ | ❌ |
| Edit game | ✅ | ✅ | ❌ | ❌ |
| Nonaktifkan game | ✅ | ✅ | ❌ | ❌ |
| Hapus game | ✅ | ❌ | ❌ | ❌ |
| Filter game by kategori | ✅ | ✅ | ✅ | ✅ |

---

## 5. 🏢 Manajemen Organizer

| Fitur | Super Admin | Admin | Organizer | Player |
|-------|:-----------:|:-----:|:---------:|:------:|
| Lihat semua organizer | ✅ | ✅ | ❌ | ❌ |
| Lihat profil organizer sendiri | — | — | ✅ | ❌ |
| Tambah organizer baru | ✅ | ✅ | ❌ | ❌ |
| Edit organizer manapun | ✅ | ✅ | ❌ | ❌ |
| Edit organizer sendiri | — | — | ✅ | ❌ |
| Hapus organizer | ✅ | ❌ | ❌ | ❌ |
| Assign user ke organizer | ✅ | ✅ | ❌ | ❌ |

---

## 6. 📅 Manajemen Event

| Fitur | Super Admin | Admin | Organizer | Player |
|-------|:-----------:|:-----:|:---------:|:------:|
| Lihat semua event | ✅ | ✅ | ✅ (publik) | ✅ (publik) |
| Buat event baru | ✅ | ✅ | ✅ (milik sendiri) | ❌ |
| Edit event manapun | ✅ | ✅ | ❌ | ❌ |
| Edit event sendiri | — | — | ✅ | ❌ |
| Hapus event manapun | ✅ | ❌ | ❌ | ❌ |
| Hapus event sendiri | — | — | ✅ (jika draft) | ❌ |
| Set tanggal & lokasi event | ✅ | ✅ | ✅ (milik sendiri) | ❌ |
| Buka/tutup registrasi | ✅ | ✅ | ✅ (milik sendiri) | ❌ |
| Ubah status event | ✅ | ✅ | ✅ (milik sendiri) | ❌ |
| Assign game ke event | ✅ | ✅ | ✅ (milik sendiri) | ❌ |
| Pilih tipe tournament | ✅ | ✅ | ✅ (milik sendiri) | ❌ |
| Set max peserta | ✅ | ✅ | ✅ (milik sendiri) | ❌ |
| Kelola sponsor event sendiri | ✅ | ✅ | ✅ (milik sendiri) | ❌ |
| Lihat sponsor event | ✅ | ✅ | ✅ | ✅ |

---

## 7. 🏆 Manajemen Match & Bracket

| Fitur | Super Admin | Admin | Organizer | Player |
|-------|:-----------:|:-----:|:---------:|:------:|
| Lihat bracket/jadwal | ✅ | ✅ | ✅ | ✅ |
| Generate bracket otomatis | ✅ | ✅ | ✅ (event sendiri) | ❌ |
| Atur jadwal match | ✅ | ✅ | ✅ (event sendiri) | ❌ |
| Input skor match | ✅ | ✅ | ✅ (event sendiri) | ❌ |
| Ubah status match | ✅ | ✅ | ✅ (event sendiri) | ❌ |
| Lihat klasemen / standing | ✅ | ✅ | ✅ | ✅ |
| Edit hasil match | ✅ | ✅ | ✅ (event sendiri) | ❌ |

---

## 8. 🎁 Manajemen Reward

| Fitur | Super Admin | Admin | Organizer | Player |
|-------|:-----------:|:-----:|:---------:|:------:|
| Lihat reward event | ✅ | ✅ | ✅ | ✅ (event terdaftar) |
| Tambah reward | ✅ | ✅ | ✅ (event sendiri) | ❌ |
| Edit reward | ✅ | ✅ | ✅ (event sendiri) | ❌ |
| Hapus reward | ✅ | ✅ | ✅ (event sendiri) | ❌ |

---

## 9. 👥 Manajemen Squad & Player

| Fitur | Super Admin | Admin | Organizer | Player |
|-------|:-----------:|:-----:|:---------:|:------:|
| Lihat semua squad | ✅ | ✅ | ✅ (peserta event sendiri) | ❌ |
| Lihat squad sendiri | — | — | — | ✅ |
| Buat squad baru | ✅ | ❌ | ❌ | ✅ |
| Edit squad manapun | ✅ | ✅ | ❌ | ❌ |
| Edit squad sendiri | — | — | — | ✅ |
| Hapus squad manapun | ✅ | ❌ | ❌ | ❌ |
| Disband squad sendiri | — | — | — | ✅ |
| Tambah player ke squad | ✅ | ❌ | ❌ | ✅ (squad sendiri) |
| Edit data player | ✅ | ❌ | ❌ | ✅ (squad sendiri) |
| Hapus player dari squad | ✅ | ❌ | ❌ | ✅ (squad sendiri) |

---

## 10. 📝 Registrasi Event

| Fitur | Super Admin | Admin | Organizer | Player |
|-------|:-----------:|:-----:|:---------:|:------:|
| Lihat semua registrasi | ✅ | ✅ | ✅ (event sendiri) | ❌ |
| Lihat registrasi sendiri | — | — | — | ✅ |
| Daftarkan squad ke event | ✅ | ❌ | ❌ | ✅ |
| Approve registrasi | ✅ | ✅ | ✅ (event sendiri) | ❌ |
| Reject registrasi | ✅ | ✅ | ✅ (event sendiri) | ❌ |
| Batalkan registrasi sendiri | — | — | — | ✅ (jika belum approved) |

---

## 11. 💳 Pembayaran & Penerbitan Event

| Fitur | Super Admin | Admin | Organizer | Player |
|-------|:-----------:|:-----:|:---------:|:------:|
| Lihat riwayat pembayaran | ✅ | ✅ | ✅ (event sendiri) | ❌ |
| Ajukan pembayaran event | ✅ | ❌ | ✅ (event sendiri) | ❌ |
| Upload bukti bayar event | ✅ | ❌ | ✅ (event sendiri) | ❌ |
| Approve / Verifikasi pembayaran | ✅ | ✅ | ❌ | ❌ |
| Reject pembayaran | ✅ | ✅ | ❌ | ❌ |
| Kelola biaya layanan (config) | ✅ | ✅ | ❌ | ❌ |

---

## 12. 🎁 Klaim & Payout Hadiah

| Fitur | Super Admin | Admin | Organizer | Player |
|-------|:-----------:|:-----:|:---------:|:------:|
| Lihat riwayat klaim & payout | ✅ | ✅ | ✅ (event sendiri) | ✅ (terkait sendiri) |
| Isi info rekening/e-wallet untuk klaim | ✅ | ❌ | ❌ | ✅ (player pemenang) |
| Payout/transfer reward otomatis | 🤖 Sistem | 🤖 Sistem | 🤖 Sistem | 🤖 Sistem |

---

## 13. 📊 Dashboard & Reporting

| Fitur | Super Admin | Admin | Organizer | Player |
|-------|:-----------:|:-----:|:---------:|:------:|
| Dashboard overview (semua data) | ✅ | ✅ | ❌ | ❌ |
| Dashboard organizer (event sendiri) | — | — | ✅ | ❌ |
| Dashboard player (squad & match) | — | — | — | ✅ |
| Statistik total user, event, squad | ✅ | ✅ | ❌ | ❌ |
| Statistik event sendiri | — | — | ✅ | ❌ |
| Riwayat match squad sendiri | — | — | — | ✅ |

---

## User Journey per Role

### 🔴 Super Admin
```
Login → Dashboard (full stats)
  ├─ Kelola Users & Roles
  ├─ Kelola Games (CRUD)
  ├─ Kelola Organizers (CRUD)
  ├─ Monitor semua Events & Matches
  ├─ Verifikasi pembayaran & kelola biaya layanan
  └─ Override/edit data apapun
```

### 🟠 Admin
```
Login → Dashboard (operational stats)
  ├─ Kelola Games (tambah/edit)
  ├─ Kelola Organizers (tambah/edit)
  ├─ Monitor Events & approve registrasi
  ├─ Verifikasi pembayaran event organizer
  └─ Input skor & manage matches
```

### 🟡 Organizer
```
Login → Dashboard Organizer
  ├─ Edit profil organizer
  ├─ Buat Event baru
  │    ├─ Pilih games, tipe tournament, jadwal
  │    ├─ Set reward per tier
  │    ├─ Bayar biaya penerbitan event
  │    └─ Buka registrasi
  ├─ Review & approve pendaftaran squad
  ├─ Generate bracket
  ├─ Input skor pertandingan
  ├─ Selesaikan Event (Sistem otomatis memicu payout ke pemenang)
  └─ Lihat klasemen & hasil
```

### 🟢 Player
```
Login → Dashboard Player
  ├─ Edit profil
  ├─ Buat / kelola Squad
  │    ├─ Tambah / hapus anggota
  │    └─ Set posisi & detail player
  ├─ Browse event yang terbuka
  ├─ Daftarkan squad ke event
  ├─ Klaim reward (isi rekening/e-wallet untuk payout otomatis)
  ├─ Lihat jadwal match
  ├─ Lihat bracket & klasemen
  └─ Riwayat pertandingan
```

---

## Matriks Akses Ringkasan

```
                    Super Admin    Admin    Organizer    Player
                    ───────────    ─────    ─────────    ──────
Users & Roles       CRUD           Read     ─            ─
Games               CRUD           CRU      Read         Read
Organizers          CRUD           CRU      Own          ─
Events              CRUD           CRU      Own CRUD     Read
Matches             CRUD           CRU      Own CRU      Read
Rewards             CRUD           CRU      Own CRUD     Read
Squads              CRUD           RU       Read         Own CRUD
Players             CRUD           ─        Read         Own CRUD
Registrations       CRUD           RU       Own RU       Own CR
Payments            CRUD           RU       Own CR       ─
Claims              CRUD           ─        Own RU       Own CR
Dashboard           Full           Full     Own          Own
```

> **Legend**: `CRUD` = Full access | `CRU` = Create/Read/Update | `RU` = Read/Update | `Own` = Hanya data milik sendiri | `Read` = Lihat saja | `─` = Tidak ada akses

> [!IMPORTANT]
> Silakan review apakah pembagian akses ini sudah sesuai kebutuhan. Jika ada fitur yang perlu dipindah ke role lain atau ditambah, beri tahu saya. Setelah disetujui, saya akan lanjut ke implementasi.
