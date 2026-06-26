# QuizLab Station — Dashboard VR Pharmaceutical Lab

Panel admin (login → dashboard) untuk melihat & mengelola hasil quiz dari game VR (Unity)
SMKN 5 Pangkalpinang. PHP murni + MySQL (Laragon).

## Struktur folder

```
quizlab-station/
├── index.php            # pengalih -> login / dashboard
├── login.php            # halaman login
├── logout.php
├── dashboard.php        # ringkasan (jumlah data per lokasi)
├── data_quiz.php        # tabel hasil + filter lokasi & tanggal + hapus
├── simpan_quiz.php      # ENDPOINT yang dipanggil Unity (POST)
├── hapus.php            # proses hapus data (POST + token CSRF)
├── config/
│   └── koneksi.php      # koneksi MySQL  ........ (KONEKSI)
├── includes/
│   ├── auth.php         # penjaga login + token CSRF
│   ├── fungsi.php       # fungsi bantu (label lokasi, csrf)
│   ├── header.php       # kerangka atas + sidebar  (TAMPILAN)
│   └── footer.php       # kerangka bawah           (TAMPILAN)
├── assets/css/
│   ├── style.css        # tampilan panel           (TAMPILAN)
│   └── login.css        # tampilan login           (TAMPILAN)
├── database/
│   ├── database.sql     # skema tabel + ALTER
│   └── setup_admin.php  # buat akun admin (hapus setelah dipakai)
└── README.md
```

## Database (`quizlab_station`)

**Tabel `admin`** — akun login. Kolom `password` disimpan **ter-hash (bcrypt `$2y$...`)**, bukan teks asli.
Lihat di phpMyAdmin: `quizlab_station` → `admin` → Browse. Default: `admin` / `admin123`.

**Tabel `hasil_quiz`** — diisi otomatis oleh Unity:

| Kolom | Keterangan |
|-------|------------|
| id | nomor urut |
| nama, kelas | identitas siswa |
| score | skor 0–100 (sudah dinormalisasi di Unity) |
| benar, total | jumlah benar & total soal (tampil `8/10`) |
| lokasi | nama scene: VRLab / VRLabSimulation / VRLabSimulation_Padat |
| waktu | waktu kirim (otomatis) |

## Field POST dari Unity (`simpan_quiz.php`)

| Field | Tipe | Contoh |
|-------|------|--------|
| nama | string | `naza` |
| kelas | string | `7A` |
| score | int | `100` |
| benar | int | `8` |
| total | int | `30` |
| lokasi | string | `VRLab` |

## Setup (Laragon)

1. **Start All** (Apache + MySQL).
2. Import `database/database.sql` lewat phpMyAdmin (kalau dari nol).
   - Kalau tabel `hasil_quiz` lama belum punya kolom `benar`/`total`, jalankan ALTER di bagian
     bawah `database.sql` (tab SQL phpMyAdmin), sekali saja.
3. Buka `http://localhost/quizlab-station/database/setup_admin.php` (sekali) → buat akun admin → lalu **hapus file itu**.
4. Login di `http://localhost/quizlab-station/` → `admin` / `admin123`.

## Fitur panel
- **Dashboard**: jumlah data per lokasi + 8 data terbaru.
- **Data Quiz**: tabel dengan kolom Score (0–100) & Benar (`benar/total`).
  - Filter **lokasi** (Semua / VRLab / VRLabSimulation / VRLabSimulation_Padat).
  - Filter **tanggal** (kanan atas, default **hari ini**, ada "Semua tanggal").
  - **Hapus**: centang baris (atau "pilih semua") → tombol **Hapus Terpilih** → konfirmasi "Apakah Anda yakin?".

## Tes dari Unity (lokal)
Di `SummaryUI.cs`, untuk tes lokal ganti URL:
```csharp
UnityWebRequest.Post("http://localhost/quizlab-station/simpan_quiz.php", form)
```
Play → kerjakan quiz → buka **Data Quiz** di web (default tampil hari ini).

> **Quest:** ganti `localhost` dengan IP PC (`ipconfig` → IPv4), PC & Quest satu WiFi,
> dan izinkan *cleartext traffic* di Android.

## Keamanan
- Semua query: prepared statement. Output: `htmlspecialchars`.
- Password admin: `password_hash` (bcrypt). Hapus `database/setup_admin.php` setelah dipakai.
- Hapus data: wajib login + POST + token CSRF.
