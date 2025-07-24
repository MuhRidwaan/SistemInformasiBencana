-- Set the database to use
USE `db_disasterinfromationsystem`;

-- Drop existing tables if they exist to allow for a clean recreation
-- This is useful for development/testing, but be cautious in production
DROP TABLE IF EXISTS `t_laporan_masyarakat`;
DROP TABLE IF EXISTS `m_upaya_penanganan`;
DROP TABLE IF EXISTS `m_relawan`;
DROP TABLE IF EXISTS `m_lokasi_posko`;
DROP TABLE IF EXISTS `m_korban`;
DROP TABLE IF EXISTS `m_kerusakan`;
DROP TABLE IF EXISTS `m_kebutuhan_logistik`;
DROP TABLE IF EXISTS `m_bencana`;
DROP TABLE IF EXISTS `m_dis_kelurahan`;
DROP TABLE IF EXISTS `m_dis_kecamatan`;
DROP TABLE IF EXISTS `m_dis_kota`;
DROP TABLE IF EXISTS `m_dis_provinsi`;
DROP TABLE IF EXISTS `m_jenis_bencana`;
DROP TABLE IF EXISTS `m_roles`;
DROP TABLE IF EXISTS `m_users`;


-- 1. Create all tables first, respecting basic dependencies (e.g., m_users first)

-- Table for Users (referenced by almost all other tables for create_who/change_who)
CREATE TABLE `m_users`(
    `user_id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(255) NOT NULL,
    `password_hash` VARCHAR(255) NOT NULL,
    `nama_lengkap` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NULL DEFAULT NULL,
    `kontak` VARCHAR(255) NULL DEFAULT NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `create_who` BIGINT(20) UNSIGNED NULL DEFAULT NULL,
    `create_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
    `change_who` BIGINT(20) UNSIGNED NULL DEFAULT NULL,
    `change_date` DATETIME NULL DEFAULT NULL
);

-- Table for Roles (references m_users)
CREATE TABLE `m_roles`(
    `role_id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `nama_role` VARCHAR(100) NOT NULL,
    `deskripsi_role` TEXT NULL,
    `create_who` BIGINT(20) UNSIGNED NULL DEFAULT NULL,
    `create_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
    `change_who` BIGINT(20) UNSIGNED NULL DEFAULT NULL,
    `change_date` DATETIME NULL DEFAULT NULL
);

-- Table for Jenis Bencana (no external FKs)
CREATE TABLE `m_jenis_bencana`(
    `jenis_bencana_id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `nama_jenis` VARCHAR(255) NOT NULL,
    `deskripsi_jenis` TEXT NULL,
    `create_who` BIGINT(20) UNSIGNED NOT NULL,
    `create_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
    `change_who` BIGINT(20) UNSIGNED NULL DEFAULT NULL,
    `change_date` DATETIME NULL DEFAULT NULL
);

-- Table for Provinsi (no external FKs)
CREATE TABLE `m_dis_provinsi`(
    `provinsi_id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `kode_wilayah` VARCHAR(255) NOT NULL,
    `nama_provinsi` VARCHAR(255) NOT NULL,
    `create_who` BIGINT(20) UNSIGNED NOT NULL,
    `create_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
    `change_who` BIGINT(20) UNSIGNED NULL DEFAULT NULL,
    `change_date` DATETIME NULL DEFAULT NULL
);

-- Table for Kota (references m_dis_provinsi)
CREATE TABLE `m_dis_kota`(
    `kota_id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `provinsi_id` BIGINT(20) UNSIGNED NOT NULL,
    `kode_wilayah` VARCHAR(255) NOT NULL,
    `nama_kota` VARCHAR(255) NOT NULL,
    `create_who` BIGINT(20) UNSIGNED NOT NULL,
    `create_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
    `change_who` BIGINT(20) UNSIGNED NULL DEFAULT NULL,
    `change_date` DATETIME NULL DEFAULT NULL
);

-- Table for Kecamatan (references m_dis_kota)
CREATE TABLE `m_dis_kecamatan`(
    `kecamatan_id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `kota_id` BIGINT(20) UNSIGNED NOT NULL,
    `kode_wilayah` VARCHAR(255) NOT NULL,
    `nama_kecamatan` VARCHAR(255) NOT NULL,
    `create_who` BIGINT(20) UNSIGNED NOT NULL,
    `create_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
    `change_who` BIGINT(20) UNSIGNED NULL DEFAULT NULL,
    `change_date` DATETIME NULL DEFAULT NULL
);

-- Table for Kelurahan (references m_dis_kecamatan)
CREATE TABLE `m_dis_kelurahan`(
    `kelurahan_id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `kecamatan_id` BIGINT(20) UNSIGNED NOT NULL,
    `kode_wilayah` VARCHAR(255) NOT NULL,
    `nama_kelurahan` VARCHAR(255) NOT NULL,
    `create_who` BIGINT(20) UNSIGNED NOT NULL,
    `create_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
    `change_who` BIGINT(20) UNSIGNED NULL DEFAULT NULL,
    `change_date` DATETIME NULL DEFAULT NULL
);

-- Table for Bencana (references multiple location tables, jenis_bencana, and users)
CREATE TABLE `m_bencana`(
    `bencana_id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `jenis_bencana_id` BIGINT NOT NULL,
    `nama_bencana` VARCHAR(255) NOT NULL,
    `kronologis` TEXT NOT NULL,
    `deskripsi` TEXT NOT NULL,
    `tanggal_kejadian` DATETIME NOT NULL,
    `latitude` DECIMAL(10, 8) NULL DEFAULT NULL,
    `longitude` DECIMAL(11, 8) NULL DEFAULT NULL,
    `provinsi_id` BIGINT(20) UNSIGNED NOT NULL,
    `kota_id` BIGINT(20) UNSIGNED NOT NULL,
    `kecamatan_id` BIGINT(20) UNSIGNED NOT NULL,
    `kelurahan_id` BIGINT(20) UNSIGNED NOT NULL,
    `create_who` BIGINT(20) UNSIGNED NOT NULL,
    `create_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
    `change_who` BIGINT(20) UNSIGNED NULL DEFAULT NULL,
    `change_date` DATETIME NULL DEFAULT NULL
);

-- Table for Kebutuhan Logistik (references m_bencana and m_users)
CREATE TABLE `m_kebutuhan_logistik`(
    `kebutuhan_id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `bencana_id` BIGINT(20) UNSIGNED NOT NULL,
    `jenis_kebutuhan` VARCHAR(255) NOT NULL,
    `jumlah_dibutuhkan` BIGINT(20) NOT NULL,
    `satuan` VARCHAR(255) NOT NULL,
    `jumlah_tersedia` BIGINT(20) NOT NULL,
    `tanggal_update` DATETIME NOT NULL,
    `deskripsi` TEXT NULL,
    `create_who` BIGINT(20) UNSIGNED NOT NULL,
    `create_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
    `change_who` BIGINT(20) UNSIGNED NULL DEFAULT NULL,
    `change_date` DATETIME NULL DEFAULT NULL
);

-- Table for Kerusakan (references m_bencana and m_users)
CREATE TABLE `m_kerusakan`(
    `kerusakan_id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `bencana_id` BIGINT(20) UNSIGNED NOT NULL,
    `objek` VARCHAR(255) NOT NULL,
    `tingkat_kerusakan` VARCHAR(255) NOT NULL,
    `jumlah` BIGINT(20) NOT NULL,
    `satuan` VARCHAR(255) NOT NULL,
    `deskripsi` TEXT NOT NULL,
    `tanggal_input` DATETIME NOT NULL,
    `create_who` BIGINT(20) UNSIGNED NOT NULL,
    `create_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
    `change_who` BIGINT(20) UNSIGNED NULL DEFAULT NULL,
    `change_date` DATETIME NULL DEFAULT NULL
);

-- Table for Korban (references m_bencana and m_users)
CREATE TABLE `m_korban`(
    `korban_id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `bencana_id` BIGINT(20) UNSIGNED NOT NULL,
    `meninggal` BIGINT(20) NOT NULL,
    `luka_berat` BIGINT(20) NOT NULL,
    `luka_ringan` BIGINT(20) NOT NULL,
    `hilang` BIGINT(20) NOT NULL,
    `mengungsi` BIGINT(20) NOT NULL,
    `terdampak` BIGINT(20) NOT NULL,
    `tanggal_input` DATETIME NOT NULL,
    `create_who` BIGINT(20) UNSIGNED NOT NULL,
    `create_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
    `change_who` BIGINT(20) UNSIGNED NULL DEFAULT NULL,
    `change_date` DATETIME NULL DEFAULT NULL
);

-- Table for Lokasi Posko (references m_bencana and m_users)
CREATE TABLE `m_lokasi_posko`(
    `posko_id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `bencana_id` BIGINT(20) UNSIGNED NOT NULL,
    `nama_posko` VARCHAR(255) NOT NULL,
    `alamat_posko` TEXT NOT NULL,
    `latitude` DECIMAL(10, 8) NULL DEFAULT NULL,
    `longitude` DECIMAL(11, 8) NULL DEFAULT NULL,
    `kapasitas` BIGINT(20) NULL DEFAULT NULL,
    `kontak_person` VARCHAR(255) NULL DEFAULT NULL,
    `create_who` BIGINT(20) UNSIGNED NOT NULL,
    `create_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
    `change_who` BIGINT(20) UNSIGNED NULL DEFAULT NULL,
    `change_date` DATETIME NULL DEFAULT NULL
);

-- Table for Relawan (references m_users)
CREATE TABLE `m_relawan`(
    `relawan_id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `keahlian` VARCHAR(255) NULL DEFAULT NULL,
    `organisasi` VARCHAR(255) NULL DEFAULT NULL,
    `create_who` BIGINT(20) UNSIGNED NOT NULL,
    `create_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
    `change_who` BIGINT(20) UNSIGNED NULL DEFAULT NULL,
    `change_date` DATETIME NULL DEFAULT NULL
);

-- Table for Upaya Penanganan (references m_bencana and m_users)
CREATE TABLE `m_upaya_penanganan`(
    `upaya_id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `bencana_id` BIGINT(20) UNSIGNED NOT NULL,
    `instansi` VARCHAR(255) NOT NULL,
    `jenis_upaya` VARCHAR(255) NOT NULL,
    `deskripsi` TEXT NOT NULL,
    `tanggal_penanganan` DATETIME NOT NULL,
    `create_who` BIGINT(20) UNSIGNED NOT NULL,
    `create_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
    `change_who` BIGINT(20) UNSIGNED NULL DEFAULT NULL,
    `change_date` DATETIME NULL DEFAULT NULL
);

-- Table for Laporan Masyarakat (references m_bencana and m_users)
CREATE TABLE `t_laporan_masyarakat`(
    `laporan_id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `jenis_laporan` VARCHAR(50) NOT NULL,
    `judul_laporan` VARCHAR(255) NOT NULL,
    `deskripsi_laporan` TEXT NOT NULL,
    `tanggal_laporan` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
    `nama_pelapor` VARCHAR(255) NULL DEFAULT NULL,
    `kontak_pelapor` VARCHAR(255) NULL DEFAULT NULL,
    `latitude` DECIMAL(10, 8) NULL DEFAULT NULL,
    `longitude` DECIMAL(11, 8) NULL DEFAULT NULL,
    `path_foto` TEXT NULL,
    `status_laporan` VARCHAR(50) NOT NULL DEFAULT 'Pending',
    `bencana_id` BIGINT(20) UNSIGNED NULL DEFAULT NULL,
    `create_who` BIGINT(20) UNSIGNED NOT NULL,
    `create_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
    `change_who` BIGINT(20) UNSIGNED NULL DEFAULT NULL,
    `change_date` DATETIME NULL DEFAULT NULL
);


-- 2. Add all indexes

-- Indexes for m_users
ALTER TABLE `m_users` ADD UNIQUE `m_users_username_unique`(`username`);
ALTER TABLE `m_users` ADD UNIQUE `m_users_email_unique`(`email`);
ALTER TABLE `m_users` ADD INDEX `m_users_create_who_index`(`create_who`);
ALTER TABLE `m_users` ADD INDEX `m_users_change_who_index`(`change_who`);

-- Indexes for m_roles
ALTER TABLE `m_roles` ADD UNIQUE `m_roles_nama_role_unique`(`nama_role`);
ALTER TABLE `m_roles` ADD INDEX `m_roles_create_who_index`(`create_who`);
ALTER TABLE `m_roles` ADD INDEX `m_roles_change_who_index`(`change_who`);

-- Indexes for m_jenis_bencana
ALTER TABLE `m_jenis_bencana` ADD UNIQUE `m_jenis_bencana_nama_jenis_unique`(`nama_jenis`);

-- Indexes for m_dis_provinsi
ALTER TABLE `m_dis_provinsi` ADD UNIQUE `m_dis_provinsi_kode_wilayah_unique`(`kode_wilayah`);

-- Indexes for m_dis_kota
ALTER TABLE `m_dis_kota` ADD INDEX `m_dis_kota_provinsi_id_index`(`provinsi_id`);
ALTER TABLE `m_dis_kota` ADD UNIQUE `m_dis_kota_kode_wilayah_unique`(`kode_wilayah`);

-- Indexes for m_dis_kecamatan
ALTER TABLE `m_dis_kecamatan` ADD INDEX `m_dis_kecamatan_kota_id_index`(`kota_id`);
ALTER TABLE `m_dis_kecamatan` ADD UNIQUE `m_dis_kecamatan_kode_wilayah_unique`(`kode_wilayah`);

-- Indexes for m_dis_kelurahan
ALTER TABLE `m_dis_kelurahan` ADD INDEX `m_dis_kelurahan_kecamatan_id_index`(`kecamatan_id`);
ALTER TABLE `m_dis_kelurahan` ADD UNIQUE `m_dis_kelurahan_kode_wilayah_unique`(`kode_wilayah`);

-- Indexes for m_bencana
ALTER TABLE `m_bencana` ADD INDEX `m_bencana_jenis_bencana_id_index`(`jenis_bencana_id`);
ALTER TABLE `m_bencana` ADD INDEX `m_bencana_provinsi_id_index`(`provinsi_id`);
ALTER TABLE `m_bencana` ADD INDEX `m_bencana_kota_id_index`(`kota_id`);
ALTER TABLE `m_bencana` ADD INDEX `m_bencana_kecamatan_id_index`(`kecamatan_id`);
ALTER TABLE `m_bencana` ADD INDEX `m_bencana_kelurahan_id_index`(`kelurahan_id`);
ALTER TABLE `m_bencana` ADD INDEX `m_bencana_create_who_index`(`create_who`);
ALTER TABLE `m_bencana` ADD INDEX `m_bencana_change_who_index`(`change_who`);

-- Indexes for m_kebutuhan_logistik
ALTER TABLE `m_kebutuhan_logistik` ADD INDEX `m_kebutuhan_logistik_bencana_id_index`(`bencana_id`);
ALTER TABLE `m_kebutuhan_logistik` ADD INDEX `m_kebutuhan_logistik_create_who_index`(`create_who`);
ALTER TABLE `m_kebutuhan_logistik` ADD INDEX `m_kebutuhan_logistik_change_who_index`(`change_who`);

-- Indexes for m_kerusakan
ALTER TABLE `m_kerusakan` ADD INDEX `m_kerusakan_bencana_id_index`(`bencana_id`);
ALTER TABLE `m_kerusakan` ADD INDEX `m_kerusakan_create_who_index`(`create_who`);
ALTER TABLE `m_kerusakan` ADD INDEX `m_kerusakan_change_who_index`(`change_who`);

-- Indexes for m_korban
ALTER TABLE `m_korban` ADD INDEX `m_korban_bencana_id_index`(`bencana_id`);
ALTER TABLE `m_korban` ADD INDEX `m_korban_create_who_index`(`create_who`);
ALTER TABLE `m_korban` ADD INDEX `m_korban_change_who_index`(`change_who`);

-- Indexes for m_lokasi_posko
ALTER TABLE `m_lokasi_posko` ADD INDEX `m_lokasi_posko_bencana_id_index`(`bencana_id`);
ALTER TABLE `m_lokasi_posko` ADD INDEX `m_lokasi_posko_create_who_index`(`create_who`);
ALTER TABLE `m_lokasi_posko` ADD INDEX `m_lokasi_posko_change_who_index`(`change_who`);

-- Indexes for m_relawan
ALTER TABLE `m_relawan` ADD INDEX `m_relawan_create_who_index`(`create_who`);
ALTER TABLE `m_relawan` ADD INDEX `m_relawan_change_who_index`(`change_who`);

-- Indexes for m_upaya_penanganan
ALTER TABLE `m_upaya_penanganan` ADD INDEX `m_upaya_penanganan_bencana_id_index`(`bencana_id`);
ALTER TABLE `m_upaya_penanganan` ADD INDEX `m_upaya_penanganan_create_who_index`(`create_who`);
ALTER TABLE `m_upaya_penanganan` ADD INDEX `m_upaya_penanganan_change_who_index`(`change_who`);

-- Indexes for t_laporan_masyarakat
ALTER TABLE `t_laporan_masyarakat` ADD INDEX `t_laporan_masyarakat_bencana_id_index`(`bencana_id`);
ALTER TABLE `t_laporan_masyarakat` ADD INDEX `t_laporan_masyarakat_create_who_index`(`create_who`);
ALTER TABLE `t_laporan_masyarakat` ADD INDEX `t_laporan_masyarakat_change_who_index`(`change_who`);


-- 3. Add all foreign key constraints, respecting dependencies

-- Foreign Keys for m_users (self-referencing)
ALTER TABLE `m_users` ADD CONSTRAINT `m_users_create_who_foreign` FOREIGN KEY(`create_who`) REFERENCES `m_users`(`user_id`);
ALTER TABLE `m_users` ADD CONSTRAINT `m_users_change_who_foreign` FOREIGN KEY(`change_who`) REFERENCES `m_users`(`user_id`);

-- Foreign Keys for m_roles
ALTER TABLE `m_roles` ADD CONSTRAINT `m_roles_create_who_foreign` FOREIGN KEY(`create_who`) REFERENCES `m_users`(`user_id`);
ALTER TABLE `m_roles` ADD CONSTRAINT `m_roles_change_who_foreign` FOREIGN KEY(`change_who`) REFERENCES `m_users`(`user_id`);

-- Foreign Keys for m_jenis_bencana
ALTER TABLE `m_jenis_bencana` ADD CONSTRAINT `m_jenis_bencana_create_who_foreign` FOREIGN KEY(`create_who`) REFERENCES `m_users`(`user_id`);
ALTER TABLE `m_jenis_bencana` ADD CONSTRAINT `m_jenis_bencana_change_who_foreign` FOREIGN KEY(`change_who`) REFERENCES `m_users`(`user_id`);

-- Foreign Keys for m_dis_provinsi
ALTER TABLE `m_dis_provinsi` ADD CONSTRAINT `m_dis_provinsi_create_who_foreign` FOREIGN KEY(`create_who`) REFERENCES `m_users`(`user_id`);
ALTER TABLE `m_dis_provinsi` ADD CONSTRAINT `m_dis_provinsi_change_who_foreign` FOREIGN KEY(`change_who`) REFERENCES `m_users`(`user_id`);

-- Foreign Keys for m_dis_kota
ALTER TABLE `m_dis_kota` ADD CONSTRAINT `m_dis_kota_provinsi_id_foreign` FOREIGN KEY(`provinsi_id`) REFERENCES `m_dis_provinsi`(`provinsi_id`);
ALTER TABLE `m_dis_kota` ADD CONSTRAINT `m_dis_kota_create_who_foreign` FOREIGN KEY(`create_who`) REFERENCES `m_users`(`user_id`);
ALTER TABLE `m_dis_kota` ADD CONSTRAINT `m_dis_kota_change_who_foreign` FOREIGN KEY(`change_who`) REFERENCES `m_users`(`user_id`);

-- Foreign Keys for m_dis_kecamatan
ALTER TABLE `m_dis_kecamatan` ADD CONSTRAINT `m_dis_kecamatan_kota_id_foreign` FOREIGN KEY(`kota_id`) REFERENCES `m_dis_kota`(`kota_id`);
ALTER TABLE `m_dis_kecamatan` ADD CONSTRAINT `m_dis_kecamatan_create_who_foreign` FOREIGN KEY(`create_who`) REFERENCES `m_users`(`user_id`);
ALTER TABLE `m_dis_kecamatan` ADD CONSTRAINT `m_dis_kecamatan_change_who_foreign` FOREIGN KEY(`change_who`) REFERENCES `m_users`(`user_id`);

-- Foreign Keys for m_dis_kelurahan
ALTER TABLE `m_dis_kelurahan` ADD CONSTRAINT `m_dis_kelurahan_kecamatan_id_foreign` FOREIGN KEY(`kecamatan_id`) REFERENCES `m_dis_kecamatan`(`kecamatan_id`);
ALTER TABLE `m_dis_kelurahan` ADD CONSTRAINT `m_dis_kelurahan_create_who_foreign` FOREIGN KEY(`create_who`) REFERENCES `m_users`(`user_id`);
ALTER TABLE `m_dis_kelurahan` ADD CONSTRAINT `m_dis_kelurahan_change_who_foreign` FOREIGN KEY(`change_who`) REFERENCES `m_users`(`user_id`);

-- Foreign Keys for m_bencana
ALTER TABLE `m_bencana` ADD CONSTRAINT `m_bencana_jenis_bencana_id_foreign` FOREIGN KEY(`jenis_bencana_id`) REFERENCES `m_jenis_bencana`(`jenis_bencana_id`);
ALTER TABLE `m_bencana` ADD CONSTRAINT `m_bencana_provinsi_id_foreign` FOREIGN KEY(`provinsi_id`) REFERENCES `m_dis_provinsi`(`provinsi_id`);
ALTER TABLE `m_bencana` ADD CONSTRAINT `m_bencana_kota_id_foreign` FOREIGN KEY(`kota_id`) REFERENCES `m_dis_kota`(`kota_id`);
ALTER TABLE `m_bencana` ADD CONSTRAINT `m_bencana_kecamatan_id_foreign` FOREIGN KEY(`kecamatan_id`) REFERENCES `m_dis_kecamatan`(`kecamatan_id`);
ALTER TABLE `m_bencana` ADD CONSTRAINT `m_bencana_kelurahan_id_foreign` FOREIGN KEY(`kelurahan_id`) REFERENCES `m_dis_kelurahan`(`kelurahan_id`);
ALTER TABLE `m_bencana` ADD CONSTRAINT `m_bencana_create_who_foreign` FOREIGN KEY(`create_who`) REFERENCES `m_users`(`user_id`);
ALTER TABLE `m_bencana` ADD CONSTRAINT `m_bencana_change_who_foreign` FOREIGN KEY(`change_who`) REFERENCES `m_users`(`user_id`);

-- Foreign Keys for m_kebutuhan_logistik
ALTER TABLE `m_kebutuhan_logistik` ADD CONSTRAINT `m_kebutuhan_logistik_bencana_id_foreign` FOREIGN KEY(`bencana_id`) REFERENCES `m_bencana`(`bencana_id`);
ALTER TABLE `m_kebutuhan_logistik` ADD CONSTRAINT `m_kebutuhan_logistik_create_who_foreign` FOREIGN KEY(`create_who`) REFERENCES `m_users`(`user_id`);
ALTER TABLE `m_kebutuhan_logistik` ADD CONSTRAINT `m_kebutuhan_logistik_change_who_foreign` FOREIGN KEY(`change_who`) REFERENCES `m_users`(`user_id`);

-- Foreign Keys for m_kerusakan
ALTER TABLE `m_kerusakan` ADD CONSTRAINT `m_kerusakan_bencana_id_foreign` FOREIGN KEY(`bencana_id`) REFERENCES `m_bencana`(`bencana_id`);
ALTER TABLE `m_kerusakan` ADD CONSTRAINT `m_kerusakan_create_who_foreign` FOREIGN KEY(`create_who`) REFERENCES `m_users`(`user_id`);
ALTER TABLE `m_kerusakan` ADD CONSTRAINT `m_kerusakan_change_who_foreign` FOREIGN KEY(`change_who`) REFERENCES `m_users`(`user_id`);

-- Foreign Keys for m_korban
ALTER TABLE `m_korban` ADD CONSTRAINT `m_korban_bencana_id_foreign` FOREIGN KEY(`bencana_id`) REFERENCES `m_bencana`(`bencana_id`);
ALTER TABLE `m_korban` ADD CONSTRAINT `m_korban_create_who_foreign` FOREIGN KEY(`create_who`) REFERENCES `m_users`(`user_id`);
ALTER TABLE `m_korban` ADD CONSTRAINT `m_korban_change_who_foreign` FOREIGN KEY(`change_who`) REFERENCES `m_users`(`user_id`);

-- Foreign Keys for m_lokasi_posko
ALTER TABLE `m_lokasi_posko` ADD CONSTRAINT `m_lokasi_posko_bencana_id_foreign` FOREIGN KEY(`bencana_id`) REFERENCES `m_bencana`(`bencana_id`);
ALTER TABLE `m_lokasi_posko` ADD CONSTRAINT `m_lokasi_posko_create_who_foreign` FOREIGN KEY(`create_who`) REFERENCES `m_users`(`user_id`);
ALTER TABLE `m_lokasi_posko` ADD CONSTRAINT `m_lokasi_posko_change_who_foreign` FOREIGN KEY(`change_who`) REFERENCES `m_users`(`user_id`);

-- Foreign Keys for m_relawan
ALTER TABLE `m_relawan` ADD CONSTRAINT `m_relawan_create_who_foreign` FOREIGN KEY(`create_who`) REFERENCES `m_users`(`user_id`);
ALTER TABLE `m_relawan` ADD CONSTRAINT `m_relawan_change_who_foreign` FOREIGN KEY(`change_who`) REFERENCES `m_users`(`user_id`);

-- Foreign Keys for m_upaya_penanganan
ALTER TABLE `m_upaya_penanganan` ADD CONSTRAINT `m_upaya_penanganan_bencana_id_foreign` FOREIGN KEY(`bencana_id`) REFERENCES `m_bencana`(`bencana_id`);
ALTER TABLE `m_upaya_penanganan` ADD CONSTRAINT `m_upaya_penanganan_create_who_foreign` FOREIGN KEY(`create_who`) REFERENCES `m_users`(`user_id`);
ALTER TABLE `m_upaya_penanganan` ADD CONSTRAINT `m_upaya_penanganan_change_who_foreign` FOREIGN KEY(`change_who`) REFERENCES `m_users`(`user_id`);

-- Foreign Keys for t_laporan_masyarakat
ALTER TABLE `t_laporan_masyarakat` ADD CONSTRAINT `t_laporan_masyarakat_bencana_id_foreign` FOREIGN KEY(`bencana_id`) REFERENCES `m_bencana`(`bencana_id`);
ALTER TABLE `t_laporan_masyarakat` ADD CONSTRAINT `t_laporan_masyarakat_create_who_foreign` FOREIGN KEY(`create_who`) REFERENCES `m_users`(`user_id`);
ALTER TABLE `t_laporan_masyarakat` ADD CONSTRAINT `t_laporan_masyarakat_change_who_foreign` FOREIGN KEY(`change_who`) REFERENCES `m_users`(`user_id`);
