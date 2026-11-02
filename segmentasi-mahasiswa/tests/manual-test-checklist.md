# Checklist Pengujian Manual

Gunakan lingkungan lokal dengan database sudah diimpor. Tandai hasil setelah benar-benar diuji.

- [ ] Login valid berhasil dan diarahkan ke dashboard.
- [ ] Login dengan password salah ditolak.
- [ ] Akses `index.php?page=mahasiswa` tanpa login dialihkan ke login.
- [ ] Logout mengakhiri sesi.
- [ ] Tambah mahasiswa dengan 13 nilai valid.
- [ ] Kode mahasiswa duplikat ditolak.
- [ ] Nilai di bawah 0 atau di atas 100 ditolak.
- [ ] Edit dan hapus mahasiswa bekerja.
- [ ] Impor CSV valid berhasil.
- [ ] CSV dengan kolom atau nilai tidak valid dilaporkan.
- [ ] K-Means dijalankan dengan K=3.
- [ ] Riwayat, centroid, SSE, dan Silhouette tersimpan.
- [ ] Halaman hasil membuka riwayat yang dipilih.
- [ ] Evaluasi K=2 sampai K=6 berjalan jika data mencukupi.
- [ ] Laporan dapat dicetak.
- [ ] Ekspor CSV dapat dibuka.
- [ ] Perubahan profil bekerja dan password baru tervalidasi.
