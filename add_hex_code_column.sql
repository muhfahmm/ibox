-- Migration: Add hex_code column to all product image tables
-- Date: 2026-01-07
-- Description: Menambahkan kolom hex_code untuk menyimpan kode warna hex dari setiap warna produk

-- ========================================
-- iPhone
-- ========================================
ALTER TABLE `admin_produk_iphone_gambar` 
ADD COLUMN `hex_code` VARCHAR(7) NULL DEFAULT NULL AFTER `warna`;

-- ========================================
-- iPad
-- ========================================
ALTER TABLE `admin_produk_ipad_gambar` 
ADD COLUMN `hex_code` VARCHAR(7) NULL DEFAULT NULL AFTER `warna`;

-- ========================================
-- Mac
-- ========================================
ALTER TABLE `admin_produk_mac_gambar` 
ADD COLUMN `hex_code` VARCHAR(7) NULL DEFAULT NULL AFTER `warna`;

-- ========================================
-- Music (AirPods, HomePod, dll)
-- ========================================
ALTER TABLE `admin_produk_music_gambar` 
ADD COLUMN `hex_code` VARCHAR(7) NULL DEFAULT NULL AFTER `warna`;

-- ========================================
-- Watch
-- ========================================
ALTER TABLE `admin_produk_watch_gambar` 
ADD COLUMN `hex_code` VARCHAR(7) NULL DEFAULT NULL AFTER `warna_case`;

-- ========================================
-- AirTag
-- ========================================
ALTER TABLE `admin_produk_airtag_gambar` 
ADD COLUMN `hex_code` VARCHAR(7) NULL DEFAULT NULL AFTER `warna`;

-- ========================================
-- Aksesoris
-- ========================================
ALTER TABLE `admin_produk_aksesoris_gambar` 
ADD COLUMN `hex_code` VARCHAR(7) NULL DEFAULT NULL AFTER `warna`;

-- ========================================
-- Optional: Update existing records with default gray color
-- Uncomment jika ingin set default value untuk data yang sudah ada
-- ========================================

-- UPDATE `admin_produk_iphone_gambar` SET `hex_code` = '#CCCCCC' WHERE `hex_code` IS NULL;
-- UPDATE `admin_produk_ipad_gambar` SET `hex_code` = '#CCCCCC' WHERE `hex_code` IS NULL;
-- UPDATE `admin_produk_mac_gambar` SET `hex_code` = '#CCCCCC' WHERE `hex_code` IS NULL;
-- UPDATE `admin_produk_music_gambar` SET `hex_code` = '#CCCCCC' WHERE `hex_code` IS NULL;
-- UPDATE `admin_produk_watch_gambar` SET `hex_code` = '#CCCCCC' WHERE `hex_code` IS NULL;
-- UPDATE `admin_produk_airtag_gambar` SET `hex_code` = '#CCCCCC' WHERE `hex_code` IS NULL;
-- UPDATE `admin_produk_aksesoris_gambar` SET `hex_code` = '#CCCCCC' WHERE `hex_code` IS NULL;

-- ========================================
-- Verification Queries
-- Uncomment untuk verifikasi perubahan
-- ========================================

-- SHOW COLUMNS FROM `admin_produk_iphone_gambar` LIKE 'hex_code';
-- SHOW COLUMNS FROM `admin_produk_ipad_gambar` LIKE 'hex_code';
-- SHOW COLUMNS FROM `admin_produk_mac_gambar` LIKE 'hex_code';
-- SHOW COLUMNS FROM `admin_produk_music_gambar` LIKE 'hex_code';
-- SHOW COLUMNS FROM `admin_produk_watch_gambar` LIKE 'hex_code';
-- SHOW COLUMNS FROM `admin_produk_airtag_gambar` LIKE 'hex_code';
-- SHOW COLUMNS FROM `admin_produk_aksesoris_gambar` LIKE 'hex_code';
