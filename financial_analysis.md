# Analisis Keandalan Pencatatan Keuangan (Ledgering Review)

Setelah menganalisis kembali skema pencatatan keuangan pada tabel `EVENT_PAYMENT` dan `REWARD_CLAIM`, ditemukan beberapa celah krusial yang bisa merusak integritas data keuangan aplikasi di masa mendatang. Berikut adalah analisis dan usulan perbaikannya:

---

## 🔍 Temuan Masalah & Celah Keuangan

### 1. Masalah Integritas Data Historis (Crucial)
* **Masalah:** Di tabel `REWARD_CLAIM`, nominal hadiah yang dibayarkan tidak disimpan, melainkan hanya merujuk lewat `reward_id` ke tabel `REWARD`.
* **Resiko:** Jika di kemudian hari Organizer mengubah nominal hadiah di tabel `REWARD` (misalnya untuk turnamen musim berikutnya), maka riwayat transfer di `REWARD_CLAIM` pada masa lalu akan ikut berubah secara tidak sah. Laporan keuangan tahunan menjadi tidak akurat (inkonsisten).
* **Solusi:** Salin nominal hadiah ke kolom `amount` di tabel `REWARD_CLAIM` saat transaksi sukses diproses. Ini adalah standar kepatuhan audit (*audit trail*).

### 2. Status Pengembalian Dana (Refund) Belum Terakomodasi
* **Masalah:** Jika turnamen dibatalkan oleh Organizer atau ditolak oleh Admin, sistem harus mengembalikan uang deposit. Namun, status `refunded` dan bukti pengembalian belum tercatat di tabel `EVENT_PAYMENT`.
* **Solusi:**
  * Tambahkan status `"refunded"` pada enum status di `EVENT_PAYMENT`.
  * Tambahkan kolom `refunded_at` (datetime) dan `refund_receipt` (string/bukti transfer balik).

### 3. Ketidakjelasan Penerima Payout untuk Tim (Squad)
* **Masalah:** Jika turnamen berbentuk Tim (Squad), hadiah dikirimkan ke mana? 
  * Apakah ditransfer secara merata ke setiap player anggota squad? (Sangat sulit karena butuh 5-11 rekening aktif yang valid).
  * Atau ditransfer ke Ketua Squad/Manager? (Paling umum & efisien).
* **Solusi/Aturan Bisnis:** Secara default, hadiah tim ditransfer ke perwakilan tim (pemilik/ketua squad). Oleh karena itu, kita perlu mencatat user mana yang melakukan penarikan dana atas nama squad tersebut.

---

## 🛠️ Usulan Perubahan Struktur Database

### A. Tabel `EVENT_PAYMENT`
Menambahkan penanganan refund:
```diff
 EVENT_PAYMENT {
     int id PK
     int event_id FK
     int amount
     int service_fee
     string payment_receipt "Nullable"
     string voucher_code "Nullable"
-    enum status "pending | approved | rejected"
+    enum status "pending | approved | rejected | refunded"
     int verified_by_id FK "Nullable"
     datetime verified_at "Nullable"
+    datetime refunded_at "Nullable"
+    string refund_receipt "Nullable"
     datetime created_at
 }
```

### B. Tabel `REWARD_CLAIM`
Menyimpan nominal historis transaksi:
```diff
 REWARD_CLAIM {
     int id PK
     int reward_id FK
+    int amount "Nominal hadiah yang dibayarkan (Audit Trail)"
     int squad_id FK "Nullable"
     int player_id FK "Nullable"
+    int claimed_by_id FK "User yang mengajukan pencairan dana"
     enum status "pending | processing | paid | failed"
     string payment_method "Nullable"
     string payment_receipt "Nullable"
     datetime claimed_at
     datetime paid_at "Nullable"
 }
```

Dengan perubahan ini, audit keuangan platform NEX-Sport akan 100% aman dan memiliki jejak audit (*audit trail*) yang lengkap tanpa takut data historis terdistorsi oleh pengeditan data master.
