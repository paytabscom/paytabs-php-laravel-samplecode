-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               5.7.24 - MySQL Community Server (GPL)
-- Server OS:                    Win32
-- HeidiSQL Version:             11.2.0.6213
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Dumping structure for table merchant_db.carts_clickpay_live
CREATE TABLE IF NOT EXISTS `carts_clickpay_live` (
  `cart_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `products` varchar(100) DEFAULT NULL,
  `total` int(10) unsigned DEFAULT NULL,
  `tran_type` enum('sale','auth') DEFAULT NULL,
  `payment_resp_status` enum('A','E') DEFAULT NULL,
  `payment_resp_code` varchar(20) DEFAULT NULL,
  `payment_resp_msg` varchar(50) DEFAULT NULL,
  `capture_resp_status` enum('A','E','D','V') DEFAULT NULL,
  `capture_resp_code` varchar(20) DEFAULT NULL,
  `capture_resp_msg` varchar(50) DEFAULT NULL,
  `payment_tran_ref` varchar(50) DEFAULT NULL,
  `payment_updated_at` datetime DEFAULT NULL,
  `capture_updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`cart_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- Dumping data for table merchant_db.carts_clickpay_live: ~0 rows (approximately)
/*!40000 ALTER TABLE `carts_clickpay_live` DISABLE KEYS */;
/*!40000 ALTER TABLE `carts_clickpay_live` ENABLE KEYS */;

-- Dumping structure for table merchant_db.carts_clickpay_live_hamad
CREATE TABLE IF NOT EXISTS `carts_clickpay_live_hamad` (
  `cart_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `products` varchar(100) DEFAULT NULL,
  `total` int(10) unsigned DEFAULT NULL,
  `tran_type` enum('sale','auth') DEFAULT NULL,
  `payment_resp_status` enum('A','E') DEFAULT NULL,
  `payment_resp_code` varchar(20) DEFAULT NULL,
  `payment_resp_msg` varchar(50) DEFAULT NULL,
  `capture_resp_status` enum('A','E','D','V') DEFAULT NULL,
  `capture_resp_code` varchar(20) DEFAULT NULL,
  `capture_resp_msg` varchar(50) DEFAULT NULL,
  `payment_tran_ref` varchar(50) DEFAULT NULL,
  `payment_updated_at` datetime DEFAULT NULL,
  `capture_updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`cart_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- Dumping data for table merchant_db.carts_clickpay_live_hamad: ~0 rows (approximately)
/*!40000 ALTER TABLE `carts_clickpay_live_hamad` DISABLE KEYS */;
/*!40000 ALTER TABLE `carts_clickpay_live_hamad` ENABLE KEYS */;

-- Dumping structure for table merchant_db.carts_clickpay_test
CREATE TABLE IF NOT EXISTS `carts_clickpay_test` (
  `cart_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `products` varchar(100) DEFAULT NULL,
  `total` int(10) unsigned DEFAULT NULL,
  `tran_type` enum('sale','auth') DEFAULT NULL,
  `payment_resp_status` enum('A','E') DEFAULT NULL,
  `payment_resp_code` varchar(20) DEFAULT NULL,
  `payment_resp_msg` varchar(50) DEFAULT NULL,
  `capture_resp_status` enum('A','E','D','V') DEFAULT NULL,
  `capture_resp_code` varchar(20) DEFAULT NULL,
  `capture_resp_msg` varchar(50) DEFAULT NULL,
  `payment_tran_ref` varchar(50) DEFAULT NULL,
  `payment_updated_at` datetime DEFAULT NULL,
  `capture_updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`cart_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- Dumping data for table merchant_db.carts_clickpay_test: ~0 rows (approximately)
/*!40000 ALTER TABLE `carts_clickpay_test` DISABLE KEYS */;
/*!40000 ALTER TABLE `carts_clickpay_test` ENABLE KEYS */;

-- Dumping structure for table merchant_db.carts_paytabs
CREATE TABLE IF NOT EXISTS `carts_paytabs` (
  `cart_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `products` varchar(100) DEFAULT NULL,
  `total` int(10) unsigned DEFAULT NULL,
  `tran_type` enum('sale','auth') DEFAULT NULL,
  `payment_resp_status` enum('A','E') DEFAULT NULL,
  `payment_resp_code` varchar(20) DEFAULT NULL,
  `payment_resp_msg` varchar(50) DEFAULT NULL,
  `capture_resp_status` enum('A','E','D','V') DEFAULT NULL,
  `capture_resp_code` varchar(20) DEFAULT NULL,
  `capture_resp_msg` varchar(50) DEFAULT NULL,
  `payment_tran_ref` varchar(50) DEFAULT NULL,
  `payment_updated_at` datetime DEFAULT NULL,
  `capture_updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`cart_id`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8;

-- Dumping data for table merchant_db.carts_paytabs: ~1 rows (approximately)
/*!40000 ALTER TABLE `carts_paytabs` DISABLE KEYS */;
INSERT INTO `carts_paytabs` (`cart_id`, `products`, `total`, `tran_type`, `payment_resp_status`, `payment_resp_code`, `payment_resp_msg`, `capture_resp_status`, `capture_resp_code`, `capture_resp_msg`, `payment_tran_ref`, `payment_updated_at`, `capture_updated_at`) VALUES
	(35, 'a:1:{i:0;i:1;}', 1, 'sale', 'A', 'G12798', 'Authorised', 'A', 'G13484', 'Authorised', 'TST2116100195416', '2021-06-13 13:03:51', '2021-06-10 11:24:43'),
	(36, 'a:1:{i:0;i:0;}', 1, 'sale', 'A', 'G79386', 'Authorised', NULL, NULL, NULL, 'TST2116400197587', '2021-06-15 11:43:17', NULL);
/*!40000 ALTER TABLE `carts_paytabs` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
