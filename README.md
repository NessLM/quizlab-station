# QuizLab Station — Dashboard VR Pharmaceutical Lab

Panel admin (login → dashboard) untuk melihat hasil quiz yang dikirim dari game VR (Unity)
milik SMKN 5 Pangkalpinang. PHP murni + MySQL, dijalankan lewat Laragon.

## Isi folder

| File | Fungsi |
|------|--------|
| `koneksi.php` | Koneksi MySQL (database `quizlab_station`) |
| `database.sql` | Skema tabel `admin` + `hasil_quiz` |
| `setup_admin.php` | Membuat akun admin default (jalankan sekali, lalu hapus) |
| `auth.php` | Penjaga sesi — halaman panel wajib login |
| `login.php` / `logout.php` | Masuk / keluar |
| `index.php` | Pengalih ke dashboard / login |
| `_header.php` / `_footer.php` | Kerangka tampilan (sidebar + CSS) |
| `dashboard.php` | Ringkasan jumlah data per lokasi |
| `data_quiz.php` | Tabel hasil quiz, **bisa difilter per lokasi** |
| `simpan_quiz.php` | **Endpoint** penerima POST dari Unity |

## Data yang dikirim Unity (POST ke `simpan_quiz.php`)

| Field | Tipe | Contoh | Keterangan |
|-------|------|--------|------------|
| `nama` | string | `Budi` | "-" kalau kosong |
| `kelas` | string | `XII Farmasi 1` | "-" kalau kosong |
| `score` | int | `80` | skor quiz |
| `lokasi` | string | `VRLabSimulation` | nama scene = penanda quiz |

`lokasi` (nama scene) yang dipakai sebagai filter:
- `VRLab` → VR Lab (Umum)
- `VRLabSimulation` → Cair & Semi Padat
- `VRLabSimulation_Padat` → Padat

## Cara setup (lokal, Laragon)

1. **Laragon → Start All** (Apache + MySQL).
2. Database & akun admin sudah disiapkan. *(Kalau perlu dari nol: buka
   `http://localhost/phpmyadmin` → Import `database.sql`, lalu buka
   `http://localhost/quizlab-station/setup_admin.php` sekali.)*
3. Login di **`http://localhost/quizlab-station/`**
   - Username: `admin`
   - Password: `admin123`  *(ganti setelah ini, dan hapus `setup_admin.php`)*

## Cara tes dari Unity

1. Di `SummaryUI.cs`, ganti URL endpoint untuk tes lokal:
   ```csharp
   // dari:
   UnityWebRequest.Post("https://vrlabfarmasismkn5pkp.fun/simpan_quiz.php", form)
   // jadi (tes di Unity Editor / PC yang sama):
   UnityWebRequest.Post("http://localhost/quizlab-station/simpan_quiz.php", form)
   ```
2. Play di Unity, kerjakan quiz sampai layar Summary. Console akan menampilkan:
   `Data berhasil dikirim: {"status":"sukses",...}`
3. Buka **`http://localhost/quizlab-station/dashboard.php`** atau menu
   **Data Quiz** → data muncul, bisa difilter per lokasi.

> **Di headset Quest:** `localhost` menunjuk ke headset sendiri. Ganti dengan IP PC
> (cek `ipconfig` → IPv4), mis. `http://192.168.1.10/quizlab-station/simpan_quiz.php`,
> PC & Quest satu WiFi. Android juga memblokir HTTP polos → perlu izinkan *cleartext traffic*.

## Catatan keamanan
- Semua query memakai prepared statement.
- Output data siswa di-escape dengan `htmlspecialchars`.
- Password admin disimpan ter-hash (`password_hash`).
- Hapus `setup_admin.php` setelah akun admin dibuat.
