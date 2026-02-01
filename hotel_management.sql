-- MySQL dump 10.13  Distrib 8.0.30, for Win64 (x86_64)
--
-- Host: localhost    Database: hotel_management
-- ------------------------------------------------------
-- Server version	8.0.30

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `bookings`
--

DROP TABLE IF EXISTS `bookings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bookings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `guest_id` varchar(255) DEFAULT NULL,
  `room_id` varchar(255) DEFAULT NULL,
  `check_in_date` date NOT NULL,
  `check_out_date` date NOT NULL,
  `status` enum('booked','checked_in','checked_out','cancelled') DEFAULT 'booked',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `judul` varchar(255) DEFAULT NULL,
  `room_type` varchar(20) DEFAULT NULL,
  `nipp` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `guest_id` (`guest_id`),
  KEY `room_id` (`room_id`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bookings`
--

LOCK TABLES `bookings` WRITE;
/*!40000 ALTER TABLE `bookings` DISABLE KEYS */;
INSERT INTO `bookings` VALUES (24,'40','75','2025-12-07','2025-12-12','checked_out','2025-12-07 12:39:56',NULL,'ROOM','99999999'),(25,'35','90','2025-12-07','2025-12-13','booked','2025-12-07 12:41:53',NULL,'ROOM','NIPP2024006'),(26,'41','93','2026-01-19','2026-01-20','booked','2026-01-19 07:18:18',NULL,'ROOM','345345435'),(27,'42','75','2026-01-19','2026-01-21','booked','2026-01-19 07:18:59',NULL,'ROOM','asasasasa'),(28,'43','89','2026-01-19','2026-01-20','booked','2026-01-19 07:26:06',NULL,'ROOM','XXX'),(29,'44','84','2026-01-19','2026-01-20','booked','2026-01-19 07:28:12',NULL,'ROOM','ABHDAHBDWJ'),(30,'45','88','2026-01-19','2026-01-20','booked','2026-01-19 07:30:51',NULL,'ROOM','XXXXCCC'),(31,'47','106','2026-01-19','2026-01-20','booked','2026-01-19 07:33:57',NULL,'ROOM','JDNKAJNDWKJN'),(32,'49','72','2026-02-10','2026-02-12','booked','2026-01-29 14:57:11',NULL,'ROOM','XXXXXXX'),(33,'38','92','2026-02-23','2026-02-25','booked','2026-01-29 15:04:23',NULL,'ROOM','NIPP2024009'),(34,'38','114','2026-02-04','2026-02-06','booked','2026-01-31 10:23:17',NULL,'ROOM','NIPP2024009'),(35,'39','98','2026-02-23','2026-02-27','booked','2026-01-31 10:50:36',NULL,'ROOM','NIPP2024010'),(36,'32','98','2026-03-01','2026-03-03','booked','2026-02-01 13:19:18',NULL,'ROOM','NIPP2024003'),(37,'41','90','2026-02-01','2026-02-05','checked_in','2026-02-01 14:04:56',NULL,'ROOM','345345435'),(38,'37','106','2026-02-01','2026-02-05','booked','2026-02-01 14:17:21',NULL,'ROOM','NIPP2024008');
/*!40000 ALTER TABLE `bookings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cleaning_logs`
--

DROP TABLE IF EXISTS `cleaning_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cleaning_logs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `room_id` int NOT NULL,
  `cleaner_id` int NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime DEFAULT NULL,
  `duration_minutes` int GENERATED ALWAYS AS (timestampdiff(MINUTE,`start_time`,`end_time`)) STORED,
  `notes` text,
  `id_user` int DEFAULT NULL,
  `nama_user` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `room_id` (`room_id`),
  KEY `cleaner_id` (`cleaner_id`),
  CONSTRAINT `cleaning_logs_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cleaning_logs_ibfk_2` FOREIGN KEY (`cleaner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cleaning_logs`
--

LOCK TABLES `cleaning_logs` WRITE;
/*!40000 ALTER TABLE `cleaning_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `cleaning_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cleaning_reports`
--

DROP TABLE IF EXISTS `cleaning_reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cleaning_reports` (
  `id` int NOT NULL AUTO_INCREMENT,
  `floor_number` varchar(10) DEFAULT NULL,
  `begin_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `room_id` varchar(10) DEFAULT NULL,
  `room_type` varchar(20) DEFAULT NULL,
  `user_id` varchar(10) DEFAULT NULL,
  `username` varchar(100) DEFAULT NULL,
  `created_date` date DEFAULT NULL,
  `total_time` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cleaning_reports`
--

LOCK TABLES `cleaning_reports` WRITE;
/*!40000 ALTER TABLE `cleaning_reports` DISABLE KEYS */;
/*!40000 ALTER TABLE `cleaning_reports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `faces`
--

DROP TABLE IF EXISTS `faces`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `faces` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `descriptor` json NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `faces`
--

LOCK TABLES `faces` WRITE;
/*!40000 ALTER TABLE `faces` DISABLE KEYS */;
INSERT INTO `faces` VALUES (2,'Alif Aji','alif@example.com','[-0.014968769624829292, 0.1278800219297409, -0.01311606913805008, -0.02409430779516697, -0.07180506736040115, 0.09615595638751984, -0.06915644556283951, 0.0012152933049947023, 0.06521610170602798, -0.08784658461809158, 0.2947973906993866, -0.07963069528341293, -0.29920682311058044, 0.040419094264507294, 0.05881540849804878, 0.11964358389377594, -0.13982634246349335, -0.10719388723373412, -0.1779988557100296, -0.0743083506822586, -0.07105671614408493, 0.03894810378551483, 0.006609142757952213, 0.05459195747971535, -0.11087705194950104, -0.3065728545188904, -0.03072245791554451, -0.1027032658457756, 0.09931258857250214, -0.2217750996351242, -0.03200579062104225, 0.005147951655089855, -0.191216841340065, -0.0342373289167881, 0.05296855792403221, 0.04077694192528725, -0.08850771188735962, -0.04099802300333977, 0.271287739276886, 0.07230941206216812, -0.13037000596523285, 0.011895159259438516, 0.05178258940577507, 0.29317277669906616, 0.19205646216869351, 0.04124952852725983, -0.0380074717104435, -0.028329016640782356, 0.04633152112364769, -0.2992778420448303, 0.050263334065675735, 0.15060876309871674, 0.2055258601903915, 0.15139544010162354, 0.026320738717913628, -0.11408880352973938, 0.013715658336877825, 0.15983471274375916, -0.1926962584257126, 0.10772835463285446, 0.0355132594704628, -0.07365281879901886, -0.056402526795864105, -0.05598161742091179, 0.27582481503486633, 0.08709853887557983, -0.1522807478904724, -0.18016888201236725, 0.11796144396066666, -0.1292368471622467, -0.12981247901916504, 0.16354157030582428, -0.05955153703689575, -0.16736875474452972, -0.2544843852519989, 0.06930314004421234, 0.33937400579452515, 0.12506595253944397, -0.2441532164812088, -0.03059026226401329, 0.0022538318298757076, -0.14955449104309082, 0.0071409004740417, 0.10338496416807176, -0.0524599514901638, -0.12687690556049347, -0.004838069435209036, 0.039067015051841736, 0.2113565355539322, -0.01161679159849882, -0.03568153828382492, 0.1677924245595932, -0.0341477245092392, 0.04738471284508705, 0.016996566206216812, 0.09135160595178604, -0.09725209325551988, -0.06043366342782974, -0.10058383643627168, -0.03880280256271362, -0.024123288691043857, -0.06418123841285706, -0.05599942430853844, 0.12992936372756958, -0.16395369172096252, 0.2163766622543335, -0.04294707253575325, -0.08470315486192703, 0.04232524707913399, 0.050915513187646866, -0.039647843688726425, -0.02493402734398842, 0.1866977661848068, -0.3717438578605652, 0.1907822787761688, 0.2218083143234253, 0.021689416840672493, 0.02861960791051388, 0.04311344772577286, 0.052008263766765594, 0.056594930589199066, 0.05159038305282593, -0.2743934690952301, -0.1522342711687088, 0.022111181169748303, -0.07704741507768631, -0.029127033427357674, -0.03304362669587135]','faces/alif@example.com.jpg','2025-11-13 09:07:36'),(3,'Alif Prasetyo Aji','aliaji615@gmail.com','[-0.1048269271850586, 0.029242493212223053, 0.15020838379859924, 0.016314972192049026, -0.03718315809965134, -0.07186558842658997, -0.05581219494342804, -0.10264655202627182, 0.09951471537351608, -0.10282406955957411, 0.2611745595932007, -0.050826653838157654, -0.19693277776241305, -0.1058383285999298, 0.010973742231726646, 0.1268288642168045, -0.11366797238588332, -0.10127173364162444, -0.03426562622189522, -0.03498223051428795, 0.045911725610494614, -0.0458923876285553, 0.04538950324058533, 0.04547703266143799, -0.13240885734558103, -0.3409040570259094, -0.09577380865812302, -0.06774202734231949, -0.07145693898200989, -0.033896949142217636, -0.06750225275754929, 0.11119095981121065, -0.15455566346645355, -0.06630273908376694, -0.02471563033759594, 0.1304413676261902, -0.029640423133969307, -0.03919032588601112, 0.1588096022605896, -0.018437860533595085, -0.16819816827774048, -0.02693573571741581, 0.03557058796286583, 0.2538015842437744, 0.2094517946243286, 0.06874264776706696, 0.010481835342943668, -0.008371707983314991, 0.020569689571857452, -0.2123635411262512, -0.03149697184562683, 0.14139629900455475, 0.06757624447345734, 0.03130069002509117, 0.0844297707080841, -0.13765093684196472, 0.049944959580898285, 0.02513815648853779, -0.059801895171403885, -0.00500519061461091, -0.014551863074302672, -0.12140140682458878, 0.045207154005765915, -0.045737799257040024, 0.31333425641059875, 0.06265177577733994, -0.09036733210086824, -0.060398656874895096, 0.12475646287202836, -0.08768283575773239, -0.10759826004505156, 0.038760993629693985, -0.1770544797182083, -0.1748158484697342, -0.33564093708992004, 0.029993228614330292, 0.3842336237430573, 0.10059504956007004, -0.23054474592208865, 0.08375915139913559, -0.07934249192476273, -0.004825556185096502, 0.11548794060945512, 0.10124443471431732, -0.0518939234316349, 0.0724153071641922, -0.15634149312973022, -0.034408632665872574, 0.20490266382694244, -0.027574004605412483, -0.05046357586979866, 0.19152812659740448, -0.02478746697306633, 0.0508962906897068, 0.09109274297952652, -0.05447929725050926, -0.04294338449835777, 0.01956392079591751, -0.21556484699249268, -0.05345144867897034, 0.0997658222913742, -0.06775156408548355, -0.0008868678705766797, 0.11094684898853302, -0.1915288120508194, 0.03339622914791107, 0.0031787408515810966, -0.004299188032746315, 0.015769338235259056, 0.027073819190263748, -0.19343987107276917, -0.04575372859835625, 0.14762356877326965, -0.24289262294769287, 0.13276077806949615, 0.19515420496463776, 0.05467652156949043, 0.12396437674760818, 0.17318829894065857, 0.06887838989496231, -0.04771656915545464, 0.02381032332777977, -0.11555193364620207, -0.020928284153342247, 0.09754183143377304, 0.0017014076001942158, 0.11001979559659958, 0.05040387436747551]','faces/aliaji615@gmail.com.jpg','2025-11-13 09:45:08');
/*!40000 ALTER TABLE `faces` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `floors`
--

DROP TABLE IF EXISTS `floors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `floors` (
  `id` int NOT NULL AUTO_INCREMENT,
  `floor_number` int NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `floors`
--

LOCK TABLES `floors` WRITE;
/*!40000 ALTER TABLE `floors` DISABLE KEYS */;
INSERT INTO `floors` VALUES (1,1,'Lantai 1'),(2,2,'Lantai 2'),(3,3,'Lantai 3'),(4,4,'Lantai 4'),(5,5,'Lantai 5'),(6,7,'Lantai M');
/*!40000 ALTER TABLE `floors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `guests`
--

DROP TABLE IF EXISTS `guests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `guests` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `nik` varchar(20) DEFAULT NULL,
  `telepon` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `alamat` text,
  `foto_wajah` varchar(400) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `user_face_id` varchar(100) DEFAULT NULL,
  `nipp` varchar(20) DEFAULT NULL,
  `jabatan` varchar(200) DEFAULT NULL,
  `unit_induk` varchar(200) DEFAULT NULL,
  `kelamin` varchar(10) DEFAULT NULL,
  `kendaraan` varchar(100) DEFAULT NULL,
  `nomor_polisi` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `guests`
--

LOCK TABLES `guests` WRITE;
/*!40000 ALTER TABLE `guests` DISABLE KEYS */;
INSERT INTO `guests` VALUES (29,'xxx','xxxx','xxx','xxxxx','xxxx',NULL,'2025-11-23 08:26:33',NULL,'xxxx','xxxx','xxxx','L','xxxx','xxxx'),(30,'Budi Santoso','1234567890123451','081234567890','budi.santoso@email.com','Jl. Merdeka No. 123, Jakarta',NULL,'2025-11-24 02:15:22',NULL,'NIPP2024001','Manager IT','Divisi Teknologi Informasi','L','Toyota Avanza','B 1234 AB'),(31,'Sari Dewi','2345678901234562','081234567891','sari.dewi@email.com','Jl. Sudirman No. 456, Bandung',NULL,'2025-11-24 03:30:45',NULL,'NIPP2024002','Supervisor HR','Divisi Sumber Daya Manusia','P','Honda Jazz','D 5678 CD'),(32,'Ahmad Rizki','3456789012345673','081234567892','ahmad.rizki@email.com','Jl. Gatot Subroto No. 789, Surabaya',NULL,'2025-11-24 04:45:18',NULL,'NIPP2024003','Staff Finance','Divisi Keuangan','L','Mitsubishi Xpander','L 9012 EF'),(33,'Maya Sari','4567890123456784','081234567893','maya.sari@email.com','Jl. Thamrin No. 321, Medan',NULL,'2025-11-24 06:20:33',NULL,'NIPP2024004','Analyst Marketing','Divisi Pemasaran','P','Toyota Yaris','BK 3456 GH'),(34,'Rudi Hermawan','5678901234567895','081234567894','rudi.hermawan@email.com','Jl. Asia Afrika No. 654, Bali',NULL,'2025-11-24 07:55:07',NULL,'NIPP2024005','Engineer','Divisi Operasional','L','Daihatsu Terios','DK 7890 IJ'),(35,'Dewi Anggraini','6789012345678906','081234567895','dewi.anggraini@email.com','Jl. Pemuda No. 987, Yogyakarta',NULL,'2025-11-24 09:10:29',NULL,'NIPP2024006','Admin','Divisi Umum','P',NULL,NULL),(36,'Hendra Pratama','7890123456789017','081234567896','hendra.pratama@email.com','Jl. Diponegoro No. 147, Semarang',NULL,'2025-11-24 10:25:44',NULL,'NIPP2024007','Manager Produksi','Divisi Produksi','L','Suzuki Ertiga','H 1234 KL'),(37,'Lina Marlina','8901234567890128','081234567897','lina.marlina@email.com','Jl. Ahmad Yani No. 258, Makassar',NULL,'2025-11-24 11:40:15',NULL,'NIPP2024008','Supervisor QC','Divisi Quality Control','P','Nissan Livina','DD 5678 MN'),(38,'Fajar Nugroho','9012345678901239','081234567898','fajar.nugroho@email.com','Jl. Pahlawan No. 369, Palembang',NULL,'2025-11-24 12:55:38',NULL,'NIPP2024009','Staff Procurement','Divisi Pengadaan','L','Toyota Rush','PG 9012 OP'),(39,'Citra Wulandari','0123456789012340','081234567899','citra.wulandari@email.com','Jl. Hayam Wuruk No. 741, Malang',NULL,'2025-11-24 14:10:52',NULL,'NIPP2024010','Officer Legal','Divisi Hukum','P','Honda Brio','N 3456 QR'),(40,'DEKA PRAMESTA','172617826182621','0895377941531','dekapramesta77@gmail.com','Jalan Agung Tengah V Blok I1 No.D11 Sunter',NULL,'2025-12-07 12:39:56',NULL,'99999999','BOD PLN','PLN HOLDING','L','',''),(41,'DEKA PRAMESTA','2424324234234','0895377941531','dekapramesta77@gmail.com','Jalan Agung Tengah V Blok I1 No.D11 Sunter',NULL,'2026-01-19 07:18:18','vz2TqU9lCxIY','345345435','BOD PLN','PLN HOLDING','L','345345435','453454'),(42,'DEKA PRAMESTA','asasas','0895377941531','dekapramesta77@gmail.com','Jalan Agung Tengah V Blok I1 No.D11 Sunter',NULL,'2026-01-19 07:18:59',NULL,'asasasasa','BOD PLN','asasa','L','asas','asas'),(43,'DEKA PRAMESTA','XXX','0895377941531','dekapramesta77@gmail.com','Jalan Agung Tengah V Blok I1 No.D11 Sunter',NULL,'2026-01-19 07:26:06',NULL,'XXX','BOD PLN','PLN HOLDING','L','XX',''),(44,'JNDAJKDBAJSB','HSBDHABDJH','HBDBASHBDASJ','DEAKSBAJS@GMAIL.COM','HSBDABDJB',NULL,'2026-01-19 07:28:12',NULL,'ABHDAHBDWJ','JHBDBDAWBDJH','HWBADHWJBD','L','HBSBDAJBD','HBABDWBHJBDWA'),(45,'DEKA PRAMESTA','XXXX','0895377941531','dekapramesta77@gmail.com','Jalan Agung Tengah V Blok I1 No.D11 Sunter',NULL,'2026-01-19 07:30:51',NULL,'XXXXCCC','BOD PLN','PLN HOLDING','L','XXX','XXX'),(47,'jnkjsdnkajd','JNKDJNAKJDW','kjanskjNDSKJ','dekapramesta77@gmail.com','JKNSDKJNAJKD',NULL,'2026-01-19 07:33:57',NULL,'JDNKAJNDWKJN','JKANJKANWDKJA','JNAKJNDWKAJDN','L','JANKJADNWKJA','JSDNKAJNDJK'),(49,'DEKA PRAMESTA','2424324234234','0895377941531','dekapramesta77@gmail.com','Jalan Agung Tengah V Blok I1 No.D11 Sunter',NULL,'2026-01-29 14:57:11',NULL,'XXXXXXX','BOD PLN','PLN HOLDING','L','XXX','XXX');
/*!40000 ALTER TABLE `guests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `log_booking`
--

DROP TABLE IF EXISTS `log_booking`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `log_booking` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_kamar` int DEFAULT NULL,
  `id_booking` int DEFAULT NULL,
  `jenis_ruangan` varchar(100) DEFAULT NULL,
  `activity` varchar(255) DEFAULT NULL,
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` varchar(255) DEFAULT NULL,
  `tanggal_activity` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `log_booking`
--

LOCK TABLES `log_booking` WRITE;
/*!40000 ALTER TABLE `log_booking` DISABLE KEYS */;
INSERT INTO `log_booking` VALUES (1,114,34,'ROOM','booked','2026-01-31 17:23:17','2026-01-31 17:23:17','Administrator',NULL),(2,98,35,'ROOM','booked','2026-01-31 17:50:36','2026-01-31 17:50:36','Administrator','2026-02-23 00:00:00'),(3,98,36,'ROOM','booked','2026-02-01 20:19:18','2026-02-01 20:19:18','Administrator','2026-03-01 00:00:00'),(4,90,37,'ROOM','booked','2026-02-01 21:04:56','2026-02-01 21:04:56','Administrator','2026-02-01 00:00:00'),(5,106,38,'ROOM','booked','2026-02-01 21:17:21','2026-02-01 21:17:21','Administrator','2026-02-01 00:00:00');
/*!40000 ALTER TABLE `log_booking` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `booking_id` int NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `method` enum('cash','credit_card','transfer') DEFAULT 'cash',
  PRIMARY KEY (`id`),
  KEY `booking_id` (`booking_id`),
  CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'admin'),(2,'user');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rooms`
--

DROP TABLE IF EXISTS `rooms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rooms` (
  `id` int NOT NULL AUTO_INCREMENT,
  `room_number` varchar(10) NOT NULL,
  `floor_id` int NOT NULL,
  `type` enum('single','double','suite') DEFAULT 'single',
  `price_per_night` decimal(10,2) NOT NULL,
  `status` enum('available','booked','occupied','cleaning') DEFAULT 'available',
  `floor_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `capacity` int DEFAULT NULL,
  `is_cleaning` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `floor_id` (`floor_id`),
  CONSTRAINT `rooms_ibfk_1` FOREIGN KEY (`floor_id`) REFERENCES `floors` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=124 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rooms`
--

LOCK TABLES `rooms` WRITE;
/*!40000 ALTER TABLE `rooms` DISABLE KEYS */;
INSERT INTO `rooms` VALUES (64,'201',2,'double',300000.00,'cleaning','lantai 2',2,'Y'),(65,'202',2,'double',300000.00,'cleaning','lantai 2',2,'Y'),(66,'203',2,'double',300000.00,'cleaning','lantai 2',2,'Y'),(67,'204',2,'double',300000.00,'cleaning','lantai 2',2,'Y'),(68,'205',2,'double',300000.00,'cleaning','lantai 2',2,'Y'),(69,'206',2,'double',300000.00,'cleaning','lantai 2',2,'Y'),(70,'207',2,'double',300000.00,'cleaning','lantai 2',2,'Y'),(71,'208',2,'double',300000.00,'cleaning','lantai 2',2,'Y'),(72,'209',2,'double',300000.00,'cleaning','lantai 2',2,'Y'),(73,'210',2,'double',300000.00,'cleaning','lantai 2',2,'Y'),(74,'211',2,'double',300000.00,'cleaning','lantai 2',2,'Y'),(75,'212',2,'double',300000.00,'cleaning','lantai 2',2,'Y'),(76,'213',2,'double',300000.00,'cleaning','lantai 2',2,'Y'),(77,'214',2,'double',300000.00,'cleaning','lantai 2',2,'Y'),(78,'215',2,'double',300000.00,'cleaning','lantai 2',2,'Y'),(79,'301',3,'double',300000.00,'cleaning','lantai 3',2,'Y'),(80,'302',3,'double',300000.00,'cleaning','lantai 3',2,'Y'),(81,'303',3,'double',300000.00,'cleaning','lantai 3',2,'Y'),(82,'304',3,'double',300000.00,'cleaning','lantai 3',2,'Y'),(83,'305',3,'double',300000.00,'cleaning','lantai 3',2,'Y'),(84,'306',3,'double',300000.00,'cleaning','lantai 3',2,'Y'),(85,'307',3,'double',300000.00,'cleaning','lantai 3',2,'Y'),(86,'308',3,'double',300000.00,'cleaning','lantai 3',2,'Y'),(87,'309',3,'double',300000.00,'cleaning','lantai 3',2,'Y'),(88,'310',3,'double',300000.00,'cleaning','lantai 3',2,'Y'),(89,'311',3,'double',300000.00,'cleaning','lantai 3',2,'Y'),(90,'312',3,'double',300000.00,'cleaning','lantai 3',2,'Y'),(91,'313',3,'double',300000.00,'cleaning','lantai 3',2,'Y'),(92,'314',3,'double',300000.00,'cleaning','lantai 3',2,'Y'),(93,'315',3,'double',300000.00,'cleaning','lantai 3',2,'Y'),(94,'401',4,'double',300000.00,'cleaning','lantai 4',2,'Y'),(95,'402',4,'double',300000.00,'cleaning','lantai 4',2,'Y'),(96,'403',4,'double',300000.00,'cleaning','lantai 4',2,'Y'),(97,'404',4,'double',300000.00,'cleaning','lantai 4',2,'Y'),(98,'405',4,'double',300000.00,'cleaning','lantai 4',2,'Y'),(99,'406',4,'double',300000.00,'cleaning','lantai 4',2,'Y'),(100,'407',4,'double',300000.00,'cleaning','lantai 4',2,'Y'),(101,'408',4,'double',300000.00,'cleaning','lantai 4',2,'Y'),(102,'409',4,'double',300000.00,'cleaning','lantai 4',2,'Y'),(103,'410',4,'double',300000.00,'cleaning','lantai 4',2,'Y'),(104,'411',4,'double',300000.00,'cleaning','lantai 4',2,'Y'),(105,'412',4,'double',300000.00,'cleaning','lantai 4',2,'Y'),(106,'413',4,'double',300000.00,'booked','lantai 4',2,'Y'),(107,'414',4,'double',300000.00,'cleaning','lantai 4',2,'Y'),(108,'415',4,'double',300000.00,'cleaning','lantai 4',2,'Y'),(109,'501',5,'double',300000.00,'cleaning','lantai 5',2,'Y'),(110,'502',5,'double',300000.00,'cleaning','lantai 5',2,'Y'),(111,'503',5,'double',300000.00,'cleaning','lantai 5',2,'Y'),(112,'504',5,'double',300000.00,'cleaning','lantai 5',2,'Y'),(113,'505',5,'double',300000.00,'cleaning','lantai 5',2,'Y'),(114,'506',5,'double',300000.00,'cleaning','lantai 5',2,'Y'),(115,'507',5,'double',300000.00,'cleaning','lantai 5',2,'Y'),(116,'508',5,'double',300000.00,'cleaning','lantai 5',2,'Y'),(117,'509',5,'double',300000.00,'cleaning','lantai 5',2,'Y'),(118,'510',5,'double',300000.00,'cleaning','lantai 5',2,'Y'),(119,'511',5,'double',300000.00,'cleaning','lantai 5',2,'Y'),(120,'512',5,'double',300000.00,'cleaning','lantai 5',2,'Y'),(121,'513',5,'double',300000.00,'cleaning','lantai 5',2,'Y'),(122,'514',5,'double',300000.00,'cleaning','lantai 5',2,'Y'),(123,'515',5,'double',300000.00,'cleaning','lantai 5',2,'Y');
/*!40000 ALTER TABLE `rooms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rooms_meet`
--

DROP TABLE IF EXISTS `rooms_meet`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rooms_meet` (
  `id` int NOT NULL AUTO_INCREMENT,
  `room_name` varchar(255) DEFAULT NULL,
  `room_number` varchar(100) DEFAULT NULL,
  `floor_id` varchar(100) DEFAULT NULL,
  `status` varchar(10) DEFAULT NULL,
  `created` date DEFAULT NULL,
  `updated` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rooms_meet`
--

LOCK TABLES `rooms_meet` WRITE;
/*!40000 ALTER TABLE `rooms_meet` DISABLE KEYS */;
INSERT INTO `rooms_meet` VALUES (1,'Orchid Meeting Room','MR-101','6','cleaning','2024-01-10','2024-03-02'),(2,'Rose Conference Hall','MR-102','6','cleaning','2024-01-12','2024-03-05'),(3,'Jasmine Board Room','MR-201','6','cleaning','2024-02-01','2024-03-08'),(4,'Tulip Meeting Room','MR-202','6','cleaning','2024-01-15','2024-03-04'),(5,'Lavender Conference Hall','MR-301','6','cleaning','2024-01-20','2024-03-10'),(6,'Magnolia Board Room','MR-302','6','cleaning','2024-02-10','2024-03-11'),(7,'Daisy Mini Room','MR-401','6','cleaning','2024-01-05','2024-02-22'),(8,'Sunflower Discussion Room','MR-402','6','cleaning','2024-01-08','2024-03-01'),(9,'Lotus VIP Meeting Room','MR-501','6','cleaning','2024-02-15','2024-03-09'),(10,'Peony Business Room','MR-502','6','cleaning','2024-01-18','2024-03-12');
/*!40000 ALTER TABLE `rooms_meet` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) DEFAULT NULL,
  `role` enum('admin','receptionist','cleaning_service') DEFAULT 'receptionist',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'admin','0192023a7bbd73250516f069df18b500','Administrator','admin','2025-11-13 02:32:18'),(2,'reception','391db9de95524e0b0c952fc77d670853','Receptionist','receptionist','2025-11-13 02:32:18'),(3,'cleaner','733143d386b0cb4dcd485dbc70af9207','Cleaner','cleaning_service','2025-11-13 02:32:18');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'hotel_management'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-02-01 22:16:29
