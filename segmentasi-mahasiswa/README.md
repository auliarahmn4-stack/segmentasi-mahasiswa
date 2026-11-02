# SIMA Kompetensi — Website Segmentasi Mahasiswa

Aplikasi penelitian lokal berbasis PHP Native dan MySQL untuk segmentasi pola nilai mata kuliah menggunakan K-Means. Data awal bersifat sintetis/dummy, bukan data akademik asli UIN Imam Bonjol Padang.

## Teknologi
- PHP 8+ dengan PDO
- MySQL/MariaDB dan Apache melalui XAMPP
- HTML, CSS, JavaScript
- Bootstrap 5 dan Chart.js (CDN)

## Instalasi di Windows
1. Ekstrak folder `segmentasi-mahasiswa` ke `C:\xampp\htdocs\segmentasi-mahasiswa`.
2. Buka XAMPP Control Panel, jalankan Apache dan MySQL.
3. Buka `http://localhost/phpmyadmin`.
4. Pilih **Import**, lalu pilih `database/segmentasi_mahasiswa.sql` dan jalankan.
5. Buka `config/database.php`. Konfigurasi bawaan menggunakan host `127.0.0.1`, database `segmentasi_mahasiswa`, user `root`, dan password kosong. Sesuaikan jika konfigurasi XAMPP berbeda.
6. Buka `http://localhost/segmentasi-mahasiswa/`.

## Akun pengembangan
- Username: `admin`
- Password: `password`
- Email: `admin@example.local`

Akun ini hanya untuk pengembangan lokal. Segera ubah password melalui menu **Profil Akun** sebelum menggunakan data sensitif.

## Fitur yang disediakan
- Login dan logout berbasis session, password hashing, CSRF token, serta query PDO prepared statements.
- Dashboard dengan statistik database dan ringkasan proses terakhir.
- Pengelolaan data mahasiswa beserta 13 atribut nilai (tambah, edit, hapus, filter, pencarian).
- Impor CSV dengan validasi dasar dan unduh template CSV.
- Implementasi K-Means PHP: standardisasi Z-score, Euclidean distance, centroid, iterasi, SSE, Silhouette, dan penyimpanan riwayat secara transaksional.
- Hasil clustering dan profil centroid per riwayat.
- Evaluasi Elbow K=2 sampai K=6 dan Silhouette.
- Laporan berdasarkan riwayat, siap dicetak melalui dialog cetak browser (pilih Save as PDF).
- Ekspor hasil ke CSV, profil akun, pengaturan, dan checklist uji manual.

## Atribut nilai
Sistem Informasi Manajemen; Analisis Proses Bisnis; Analisis Perancangan Sistem Informasi; Manajemen Proyek Sistem Informasi; Pemrograman Web; Sistem Basis Data; Desain UI/UX; Sistem Operasi; Rekayasa Perangkat Lunak; Software Testing dan Quality Assurance; Interaksi Manusia dan Komputer; Manajemen Infrastruktur TI; Sistem Enterprise. Semua atribut bernilai 3 SKS (total 39 SKS) dan skala nilai 0–100.

## Impor CSV
Unduh template dari halaman Data Mahasiswa. Kolom harus terdiri dari `kode_mahasiswa`, `angkatan`, lalu 13 kolom nilai mengikuti urutan pada template. Nilai harus berupa angka 0–100. Baris invalid akan dilewati dan dirangkum pada notifikasi.

## Menjalankan clustering
1. Pastikan ada data dengan 13 nilai lengkap.
2. Buka menu **Proses Clustering**.
3. Pilih K, maksimum iterasi, toleransi, dan random seed.
4. Klik **Jalankan K-Means**.
5. Buka hasil untuk melihat anggota cluster, distribusi, dan centroid. Riwayat proses sebelumnya tetap tersimpan.
6. Buka **Evaluasi Model** untuk membandingkan SSE dan Silhouette pada K=2 sampai K=6.

Label kompetensi ditetapkan berdasarkan rata-rata centroid pada skala nilai asli: terendah = Kompetensi Perlu Ditingkatkan, tertinggi = Kompetensi Tinggi, dan di antaranya = Kompetensi Menengah. Label adalah interpretasi relatif terhadap dataset, bukan penilaian mutlak kemampuan individu.

## Struktur folder
- `config/`: konfigurasi aplikasi dan database
- `includes/`: autentikasi, fungsi umum, dan komponen layout
- `process/`: endpoint form, autentikasi, CRUD, impor, dan clustering
- `pages/`: halaman aplikasi
- `services/KMeans.php`: algoritma clustering dan metrik evaluasi
- `database/`: skema SQL dan data dummy
- `exports/`: endpoint ekspor CSV
- `tests/`: checklist pengujian manual

## Catatan keamanan dan keterbatasan
- Gunakan data akademik asli hanya setelah memperoleh izin dan mengikuti prosedur perlindungan data yang sesuai.
- Chart.js dimuat dari CDN, sehingga grafik membutuhkan koneksi internet; fungsi inti PHP dan database tetap berjalan secara lokal.
- `APP_BASE` di `config/app.php` disetel ke `/segmentasi-mahasiswa`. Ubah jika nama folder berbeda.
- SQL membuat ulang tabel yang ada di database `segmentasi_mahasiswa`. Jangan impor ulang jika ada data yang ingin dipertahankan.
- Checklist pengujian manual ada di `tests/manual-test-checklist.md`. Aplikasi belum diuji melalui browser XAMPP di komputer pengguna; lakukan pemeriksaan integrasi lokal sebelum digunakan untuk penelitian.
