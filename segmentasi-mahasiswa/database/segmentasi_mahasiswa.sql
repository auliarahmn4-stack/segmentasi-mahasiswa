-- SIMA Kompetensi | database untuk simulasi penelitian
-- Data mahasiswa di bawah adalah sintetis/dummy, bukan data akademik asli.
CREATE DATABASE IF NOT EXISTS segmentasi_mahasiswa CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE segmentasi_mahasiswa;
SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS log_aktivitas, centroid_clustering, hasil_clustering, riwayat_clustering, nilai_mahasiswa, mata_kuliah, mahasiswa, users;
SET FOREIGN_KEY_CHECKS=1;
CREATE TABLE users (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, nama VARCHAR(120) NOT NULL, username VARCHAR(60) NOT NULL UNIQUE,
 email VARCHAR(190) NOT NULL UNIQUE, password VARCHAR(255) NOT NULL, role ENUM('admin') NOT NULL DEFAULT 'admin',
 created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;
CREATE TABLE mahasiswa (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, kode_mahasiswa VARCHAR(20) NOT NULL UNIQUE, angkatan SMALLINT UNSIGNED NOT NULL,
 created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 INDEX idx_mahasiswa_angkatan(angkatan)
) ENGINE=InnoDB;
CREATE TABLE mata_kuliah (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, kode_mata_kuliah VARCHAR(12) NOT NULL UNIQUE, nama_mata_kuliah VARCHAR(150) NOT NULL UNIQUE,
 sks TINYINT UNSIGNED NOT NULL DEFAULT 3, created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;
CREATE TABLE nilai_mahasiswa (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, mahasiswa_id BIGINT UNSIGNED NOT NULL, mata_kuliah_id BIGINT UNSIGNED NOT NULL,
 nilai DECIMAL(5,2) NOT NULL, created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 UNIQUE KEY uq_nilai_mahasiswa_mk(mahasiswa_id,mata_kuliah_id), INDEX idx_nilai_mk(mata_kuliah_id),
 CONSTRAINT fk_nilai_mahasiswa FOREIGN KEY(mahasiswa_id) REFERENCES mahasiswa(id) ON DELETE CASCADE,
 CONSTRAINT fk_nilai_mk FOREIGN KEY(mata_kuliah_id) REFERENCES mata_kuliah(id) ON DELETE RESTRICT,
 CONSTRAINT chk_nilai_range CHECK(nilai>=0 AND nilai<=100)
) ENGINE=InnoDB;
CREATE TABLE riwayat_clustering (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, nama_proses VARCHAR(150) NOT NULL, algoritma VARCHAR(40) NOT NULL DEFAULT 'K-Means',
 jumlah_cluster TINYINT UNSIGNED NOT NULL, metode_standardisasi VARCHAR(40) NOT NULL, jumlah_data INT UNSIGNED NOT NULL,
 sse DOUBLE NULL, silhouette DOUBLE NULL, status ENUM('berhasil','gagal') NOT NULL DEFAULT 'berhasil', parameter_json JSON NULL,
 created_by BIGINT UNSIGNED NULL, created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 CONSTRAINT fk_run_user FOREIGN KEY(created_by) REFERENCES users(id) ON DELETE SET NULL, INDEX idx_run_created(created_at)
) ENGINE=InnoDB;
CREATE TABLE hasil_clustering (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, riwayat_clustering_id BIGINT UNSIGNED NOT NULL, mahasiswa_id BIGINT UNSIGNED NOT NULL,
 nomor_cluster TINYINT UNSIGNED NOT NULL, label_kompetensi VARCHAR(80) NOT NULL, created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 UNIQUE KEY uq_run_student(riwayat_clustering_id,mahasiswa_id), INDEX idx_result_label(label_kompetensi),
 CONSTRAINT fk_hasil_run FOREIGN KEY(riwayat_clustering_id) REFERENCES riwayat_clustering(id) ON DELETE CASCADE,
 CONSTRAINT fk_hasil_mahasiswa FOREIGN KEY(mahasiswa_id) REFERENCES mahasiswa(id) ON DELETE CASCADE
) ENGINE=InnoDB;
CREATE TABLE centroid_clustering (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, riwayat_clustering_id BIGINT UNSIGNED NOT NULL, nomor_cluster TINYINT UNSIGNED NOT NULL,
 mata_kuliah_id BIGINT UNSIGNED NOT NULL, nilai_centroid DECIMAL(8,4) NOT NULL,
 UNIQUE KEY uq_centroid_attr(riwayat_clustering_id,nomor_cluster,mata_kuliah_id),
 CONSTRAINT fk_centroid_run FOREIGN KEY(riwayat_clustering_id) REFERENCES riwayat_clustering(id) ON DELETE CASCADE,
 CONSTRAINT fk_centroid_mk FOREIGN KEY(mata_kuliah_id) REFERENCES mata_kuliah(id) ON DELETE RESTRICT
) ENGINE=InnoDB;
CREATE TABLE log_aktivitas (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, user_id BIGINT UNSIGNED NULL, aksi VARCHAR(80) NOT NULL, detail TEXT NULL,
 ip_address VARCHAR(45) NULL, created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 CONSTRAINT fk_log_user FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE SET NULL, INDEX idx_log_created(created_at)
) ENGINE=InnoDB;

-- Akun pengembangan: admin / password
-- Ganti password setelah login pertama sebelum memasukkan data sensitif.
INSERT INTO users(nama,username,email,password,role) VALUES
('Administrator SIMA','admin','admin@example.local','$2y$12$CrFYLOwZ2jTB8DAwG9/rouFJqexcOIVLncXPh8xPvTGHdpnX8/DqG','admin');

INSERT INTO mata_kuliah (kode_mata_kuliah, nama_mata_kuliah, sks) VALUES
('MK01', "Sistem Informasi Manajemen", 3),
('MK02', "Analisis Proses Bisnis", 3),
('MK03', "Analisis Perancangan Sistem Informasi", 3),
('MK04', "Manajemen Proyek Sistem Informasi", 3),
('MK05', "Pemrograman Web", 3),
('MK06', "Sistem Basis Data", 3),
('MK07', "Desain UI/UX", 3),
('MK08', "Sistem Operasi", 3),
('MK09', "Rekayasa Perangkat Lunak", 3),
('MK10', "Software Testing dan Quality Assurance", 3),
('MK11', "Interaksi Manusia dan Komputer", 3),
('MK12', "Manajemen Infrastruktur TI", 3),
('MK13', "Sistem Enterprise", 3);

INSERT INTO mahasiswa(kode_mahasiswa,angkatan) VALUES
('DM001',2024),
('DM002',2024),
('DM003',2024),
('DM004',2024),
('DM005',2024);

INSERT INTO nilai_mahasiswa(mahasiswa_id,mata_kuliah_id,nilai) VALUES
(1,1,50.80),
(1,2,42.74),
(1,3,47.90),
(1,4,50.81),
(1,5,33.94),
(1,6,54.25),
(1,7,57.54),
(1,8,51.20),
(1,9,62.09),
(1,10,62.61),
(1,11,59.95),
(1,12,36.97),
(1,13,54.65),
(2,1,67.38),
(2,2,66.77),
(2,3,72.19),
(2,4,87.66),
(2,5,83.74),
(2,6,73.98),
(2,7,81.90),
(2,8,73.33),
(2,9,69.49),
(2,10,77.61),
(2,11,78.17),
(2,12,88.77),
(2,13,68.86),
(3,1,96.82),
(3,2,89.17),
(3,3,85.67),
(3,4,99.00),
(3,5,86.32),
(3,6,69.29),
(3,7,95.49),
(3,8,82.27),
(3,9,98.84),
(3,10,94.93),
(3,11,85.44),
(3,12,99.00),
(3,13,85.42),
(4,1,42.24),
(4,2,55.73),
(4,3,65.15),
(4,4,58.36),
(4,5,52.85),
(4,6,52.48),
(4,7,47.25),
(4,8,62.78),
(4,9,44.46),
(4,10,56.53),
(4,11,50.17),
(4,12,39.69),
(4,13,44.63),
(5,1,79.58),
(5,2,88.41),
(5,3,74.34),
(5,4,77.00);
