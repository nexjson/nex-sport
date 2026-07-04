# Model Bisnis & Perhitungan Keuntungan Entitas (NEX-Sport)

Berikut adalah rincian model bisnis, struktur pendapatan, pengeluaran, serta formulasi perhitungan keuntungan untuk tiga entitas utama di platform NEX-Sport: **Admin (Platform)**, **Organizer (Penyelenggara)**, dan **Squad (Peserta)**.

---

## 1. 🔴 Admin (Pemilik Aplikasi / Platform)

Admin memperoleh keuntungan dari penyediaan infrastruktur turnamen, sistem escrow hadiah, bracket otomatis, dan integrasi pembayaran.

### 💰 Sumber Pendapatan (Revenue Streams)
1. **Service Fee (Biaya Layanan Turnamen):** Diambil dari setiap event turnamen komersial yang dipublikasikan (berdasarkan konfigurasi `SERVICE_FEE_CONFIG`).
2. **Potongan Tiket Pendaftaran (Platform Commision - Opsional):** Potongan persentase kecil (misalnya 2% - 5%) jika turnamen memberlakukan biaya pendaftaran berbayar melalui Payment Gateway platform.
3. **Sponsor Utama & Iklan Platform:** Penempatan banner sponsor global pada halaman utama aplikasi.

### 💸 Pengeluaran (Expenses)
1. **Biaya Payment Gateway (MDR - Merchant Discount Rate):** Biaya per transaksi dari Xendit/Midtrans (contoh: Rp 4.000 untuk Virtual Account, atau 1.5% untuk e-wallet).
2. **Biaya Server & API:** Cloud hosting (AWS/Vercel/Laravel Cloud) dan biaya pengiriman WhatsApp OTP/Email.
3. **Biaya Operasional:** Maintenance software dan pemasaran.

### 📊 Rumus Keuntungan Bersih Admin
$$\text{Keuntungan Bersih Admin} = \sum (\text{Service Fee}) + \sum (\text{Komisi Pendaftaran}) - \text{Biaya Payment Gateway} - \text{Biaya Server/Operasional}$$

---

## 2. 🟠 Organizer (Penyelenggara Turnamen)

Organizer menggunakan platform untuk mengelola jalannya turnamen secara profesional dan menarik sponsor atau biaya pendaftaran.

### 💰 Sumber Pendapatan (Revenue Streams)
1. **Biaya Pendaftaran Squad (Registration Fee):** Tiket masuk yang dibebankan kepada tim/squad peserta (misal: Rp 50.000 per squad).
2. **Sponsor Turnamen Mandiri:** Dana dari brand lokal/sponsor yang dipasang di halaman detail event (`EVENT_SPONSOR`).
3. **Donasi / Saweran (Streaming Caster):** Jika turnamen ditayangkan online dan membuka sistem donasi.

### 💸 Pengeluaran (Expenses)
1. **Total Dana Hadiah (Prize Pool):** Uang tunai yang disetorkan ke escrow platform.
2. **Service Fee Platform:** Biaya layanan yang dibayarkan ke Admin NEX-Sport.
3. **Biaya Operasional Lapangan:** Sewa tempat (untuk olahraga fisik), honor Wasit/Caster, konsumsi, dan dekorasi.

### 📊 Rumus Keuntungan Bersih Organizer
$$\text{Keuntungan Bersih Organizer} = (\text{Total Biaya Pendaftaran} + \text{Dana Sponsor}) - (\text{Total Hadiah} + \text{Service Fee Platform} + \text{Biaya Operasional})$$

---

## 3. 🟢 Squad & Player (Peserta Turnamen)

Squad bertanding untuk memperebutkan hadiah uang tunai, piala digital, serta membangun reputasi tim agar dilirik oleh sponsor pro-scene.

### 💰 Sumber Pendapatan (Revenue Streams)
1. **Hadiah Uang Tunai (Prize Money):** Dicairkan otomatis lewat sistem `REWARD_CLAIM` jika meraih juara.
2. **Sponsor Tim Mandiri:** Nilai sponsor yang didapatkan squad karena memiliki prestasi di platform.
3. **Piala/Reward Digital:** Sertifikat dan trofi digital untuk meningkatkan reputasi tim (*exposure*).

### 💸 Pengeluaran (Expenses)
1. **Biaya Pendaftaran Turnamen:** Dibayarkan ke Organizer saat registrasi.
2. **Biaya Latihan & Operasional:** Internet (untuk e-sport), sewa lapangan latihan (untuk sport), jersey, dan perlengkapan tim.

### 📊 Rumus Keuntungan Bersih Squad
$$\text{Keuntungan Bersih Squad} = \text{Hadiah Turnamen yang Didapatkan} - (\text{Biaya Pendaftaran} + \text{Biaya Operasional Tim})$$

---

## 🧮 Simulasi Kasus Konkret (Contoh Angka)

Mari kita simulasikan sebuah Turnamen Mobile Legends dengan kapasitas **32 Squad** yang diadakan oleh Organizer **"Ligagame"** di platform NEX-Sport.

### **Parameter Turnamen:**
* **Total Hadiah (Prize Pool):** Rp 5.000.000 (Juara 1: Rp 3M, Juara 2: Rp 1.5M, Juara 3: Rp 500rb).
* **Biaya Layanan Platform (Service Fee):** Rp 150.000 (ditentukan Admin).
* **Biaya Pendaftaran per Squad:** Rp 200.000.
* **Dana Sponsor Organizer:** Rp 1.500.000 (dari brand minuman).
* **Biaya Operasional Lapangan/Wasit:** Rp 500.000.
* **Biaya Transaksi Payment Gateway:** Rp 4.500 per transaksi pendaftaran & payout.

---

### **1. Keuntungan Admin (Platform)**
* **Pendapatan:**
  * Service Fee: Rp 150.000
* **Pengeluaran:**
  * Biaya transaksi pembayaran masuk (Organizer ke Escrow): Rp 4.500
  * Biaya transaksi payout otomatis ke 3 pemenang ($3 \times \text{Rp } 4.500$): Rp 13.500
* **Keuntungan Bersih Admin:**
  $$\text{Rp } 150.000 - (\text{Rp } 4.500 + \text{Rp } 13.500) = \mathbf{Rp\ 132.000}$$
  *(Belum dipotong biaya server bulanan)*

---

### **2. Keuntungan Organizer (Ligagame)**
* **Pendapatan:**
  * Biaya Pendaftaran ($32 \text{ Squad} \times \text{Rp } 200.000$): Rp 6.400.000
  * Dana Sponsor Event: Rp 1.500.000
  * *Total Pendapatan:* **Rp 7.900.000**
* **Pengeluaran:**
  * Total Hadiah (disetor di awal): Rp 5.000.000
  * Service Fee Platform: Rp 150.000
  * Biaya Operasional Lapangan: Rp 500.000
  * *Total Pengeluaran:* **Rp 5.650.000**
* **Keuntungan Bersih Organizer:**
  $$\text{Rp } 7.900.000 - \text{Rp } 5.650.000 = \mathbf{Rp\ 2.250.000}$$

---

### **3. Keuntungan Squad (Contoh: "RRQ Juara 1")**
* **Pendapatan:**
  * Hadiah Juara 1: Rp 3.000.000
* **Pengeluaran:**
  * Biaya Pendaftaran Turnamen: Rp 200.000
  * Biaya Operasional Tim (Internet & Snack): Rp 150.000
* **Keuntungan Bersih Squad:**
  $$\text{Rp } 3.000.000 - (\text{Rp } 200.000 + \text{Rp } 150.000) = \mathbf{Rp\ 2.650.000}$$
  *(Hadiah bersih ini dapat dibagi rata ke 5 player anggota tim)*
