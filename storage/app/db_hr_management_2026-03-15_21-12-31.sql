-- MySQL dump 10.13  Distrib 9.2.0, for macos14.7 (arm64)
--
-- Host: 127.0.0.1    Database: db_hr_management
-- ------------------------------------------------------
-- Server version	5.5.5-10.4.27-MariaDB

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
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
INSERT INTO `cache` VALUES ('laravel-cache-f1f70ec40aaa556905d4a030501c0ba4','i:1;',1773607301),('laravel-cache-f1f70ec40aaa556905d4a030501c0ba4:timer','i:1773607301;',1773607301);
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employee_logs`
--

DROP TABLE IF EXISTS `employee_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `employee_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint(20) unsigned DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `meta` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`meta`)),
  `logged_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `employee_logs_employee_id_foreign` (`employee_id`),
  CONSTRAINT `employee_logs_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employee_logs`
--

LOCK TABLES `employee_logs` WRITE;
/*!40000 ALTER TABLE `employee_logs` DISABLE KEYS */;
INSERT INTO `employee_logs` VALUES (4,NULL,'exported','Employee data was exported to CSV.',NULL,'2026-03-15 17:07:48','2026-03-15 17:07:48','2026-03-15 17:07:48'),(5,NULL,'exported','Employee data was exported to CSV.',NULL,'2026-03-15 17:15:23','2026-03-15 17:15:23','2026-03-15 17:15:23'),(6,NULL,'exported','Employee data was exported to CSV.',NULL,'2026-03-15 17:16:20','2026-03-15 17:16:20','2026-03-15 17:16:20'),(7,NULL,'exported','Employee data was exported to CSV.',NULL,'2026-03-15 17:16:21','2026-03-15 17:16:21','2026-03-15 17:16:21');
/*!40000 ALTER TABLE `employee_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employees`
--

DROP TABLE IF EXISTS `employees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `employees` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `salary` decimal(10,2) NOT NULL,
  `manager_id` bigint(20) unsigned DEFAULT NULL,
  `is_founder` tinyint(1) NOT NULL DEFAULT 0,
  `position_id` bigint(20) unsigned DEFAULT NULL,
  `last_salary_changed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `employees_email_unique` (`email`),
  KEY `employees_manager_id_foreign` (`manager_id`),
  KEY `employees_position_id_foreign` (`position_id`),
  CONSTRAINT `employees_manager_id_foreign` FOREIGN KEY (`manager_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL,
  CONSTRAINT `employees_position_id_foreign` FOREIGN KEY (`position_id`) REFERENCES `positions` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employees`
--

LOCK TABLES `employees` WRITE;
/*!40000 ALTER TABLE `employees` DISABLE KEYS */;
INSERT INTO `employees` VALUES (1,'John Founder','founder@test.com',10000.00,NULL,1,NULL,NULL,'2026-03-15 16:19:37','2026-03-15 16:19:37',NULL),(2,'Jane Manager','jane@test.com',9999.00,1,0,1,NULL,'2026-03-15 16:19:37','2026-03-15 16:53:29',NULL),(3,'Bob Senior','bob@test.com',9999.00,2,0,NULL,'2026-03-15 16:34:16','2026-03-15 16:19:37','2026-03-15 16:34:16',NULL),(4,'Alice Junior','alice@test.com',9999.00,3,0,NULL,'2026-03-15 16:36:39','2026-03-15 16:19:37','2026-03-15 16:36:39',NULL),(5,'Updated Name','charlie@test.com',19000.00,2,0,NULL,'2026-03-15 17:00:56','2026-03-15 16:19:37','2026-03-15 17:00:56',NULL),(6,'Diana HR','diana@test.com',4500.00,2,0,NULL,NULL,'2026-03-15 16:19:37','2026-03-15 16:19:37',NULL),(7,'Eve Designer','eve@test.com',3500.00,3,0,NULL,NULL,'2026-03-15 16:19:37','2026-03-15 16:19:37',NULL),(8,'emp One','wmp@test.com',1000.00,4,0,NULL,NULL,'2026-03-15 17:02:38','2026-03-15 17:02:38',NULL),(9,'emp two','wmpj@test.com',1000.00,4,0,NULL,NULL,'2026-03-15 17:06:50','2026-03-15 17:06:50',NULL),(10,'Miracle Boyer','buford00@example.net',3980.00,8,0,NULL,NULL,'2026-03-15 17:24:35','2026-03-15 17:24:35',NULL),(11,'Mr. Terrell Hermiston III','brock.hodkiewicz@example.net',12471.00,7,0,NULL,NULL,'2026-03-15 17:24:35','2026-03-15 17:24:35',NULL),(12,'Cornelius King','fkozey@example.net',13951.00,1,0,NULL,NULL,'2026-03-15 17:24:35','2026-03-15 17:24:35',NULL),(13,'Jasen Swift','jevon44@example.com',16298.00,12,0,NULL,NULL,'2026-03-15 17:24:35','2026-03-15 17:24:35',NULL),(14,'Gay Hills','breichert@example.com',9149.00,11,0,NULL,NULL,'2026-03-15 17:24:35','2026-03-15 17:24:35',NULL),(15,'Germaine Schumm','hagenes.april@example.com',12818.00,11,0,NULL,NULL,'2026-03-15 17:24:35','2026-03-15 17:24:35',NULL),(16,'Cedrick Robel','ines85@example.net',12773.00,6,0,NULL,NULL,'2026-03-15 17:24:35','2026-03-15 17:24:35',NULL),(17,'Earnestine Powlowski','mckenna00@example.com',14955.00,6,0,NULL,NULL,'2026-03-15 17:24:35','2026-03-15 17:24:35',NULL),(18,'Mrs. Ana Jerde MD','gerhard48@example.org',12685.00,2,0,NULL,NULL,'2026-03-15 17:24:35','2026-03-15 17:24:35',NULL),(19,'Rebekah Bernier','bernhard.jayden@example.com',10927.00,4,0,NULL,NULL,'2026-03-15 17:24:35','2026-03-15 17:24:35',NULL),(20,'John Founder','founder@company.com',15000.00,NULL,1,11,NULL,'2026-03-15 18:07:54','2026-03-15 18:07:54',NULL),(21,'Alice Manager','alice@company.com',10000.00,20,0,9,NULL,'2026-03-15 18:07:54','2026-03-15 18:07:54',NULL),(22,'Bob Manager','bob@company.com',9500.00,20,0,10,NULL,'2026-03-15 18:07:54','2026-03-15 18:07:54',NULL),(23,'Carol Manager','carol@company.com',9000.00,20,0,7,NULL,'2026-03-15 18:07:54','2026-03-15 18:07:54',NULL),(24,'David Lee','david@company.com',6000.00,22,0,9,NULL,'2026-03-15 18:07:54','2026-03-15 18:07:54',NULL),(25,'Eva Brown','eva@company.com',5500.00,23,0,6,NULL,'2026-03-15 18:07:54','2026-03-15 18:07:54',NULL),(26,'Frank Wilson','frank@company.com',5000.00,22,0,6,NULL,'2026-03-15 18:07:54','2026-03-15 18:07:54',NULL),(27,'Grace Kim','grace@company.com',4800.00,21,0,1,NULL,'2026-03-15 18:07:54','2026-03-15 18:07:54',NULL),(28,'Henry Davis','henry@company.com',4500.00,21,0,11,NULL,'2026-03-15 18:07:54','2026-03-15 18:07:54',NULL),(29,'Isla Scott','isla@company.com',4200.00,22,0,7,NULL,'2026-03-15 18:07:54','2026-03-15 18:07:54',NULL),(30,'Jack Taylor','jack@company.com',4000.00,22,0,5,NULL,'2026-03-15 18:07:54','2026-03-15 18:07:54',NULL),(31,'Karen White','karen@company.com',3800.00,21,0,1,NULL,'2026-03-15 18:07:54','2026-03-15 18:07:54',NULL),(32,'Liam Harris','liam@company.com',3600.00,22,0,8,NULL,'2026-03-15 18:07:54','2026-03-15 18:07:54',NULL),(33,'Mia Martin','mia@company.com',3500.00,22,0,6,NULL,'2026-03-15 18:07:54','2026-03-15 18:07:54',NULL),(34,'Noah Garcia','noah@company.com',3400.00,23,0,8,NULL,'2026-03-15 18:07:54','2026-03-15 18:07:54',NULL),(35,'Olivia Hall','olivia@company.com',3300.00,23,0,1,NULL,'2026-03-15 18:07:54','2026-03-15 18:07:54',NULL),(36,'Paul Allen','paul@company.com',3200.00,23,0,1,NULL,'2026-03-15 18:07:54','2026-03-15 18:07:54',NULL),(37,'Quinn Young','quinn@company.com',3100.00,23,0,10,NULL,'2026-03-15 18:07:54','2026-03-15 18:07:54',NULL),(38,'Rachel King','rachel@company.com',3000.00,22,0,10,NULL,'2026-03-15 18:07:54','2026-03-15 18:07:54',NULL);
/*!40000 ALTER TABLE `employees` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
INSERT INTO `jobs` VALUES (1,'default','{\"uuid\":\"8e5206e5-c47e-417d-8e02-708e6957e9fb\",\"displayName\":\"App\\\\Events\\\\SalaryChanged\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\",\"command\":\"O:38:\\\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\\\":17:{s:5:\\\"event\\\";O:24:\\\"App\\\\Events\\\\SalaryChanged\\\":3:{s:8:\\\"employee\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:19:\\\"App\\\\Models\\\\Employee\\\";s:2:\\\"id\\\";i:2;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:9:\\\"oldSalary\\\";d:7000;s:9:\\\"newSalary\\\";d:9999;}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:7:\\\"backoff\\\";N;s:13:\\\"maxExceptions\\\";N;s:23:\\\"deleteWhenMissingModels\\\";b:1;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:12:\\\"messageGroup\\\";N;s:12:\\\"deduplicator\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\",\"batchId\":null},\"createdAt\":1773602919,\"delay\":null}',0,NULL,1773602919,1773602919),(2,'default','{\"uuid\":\"b28c57c8-3ca2-4c92-9ff0-2e6966fec456\",\"displayName\":\"App\\\\Events\\\\SalaryChanged\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\",\"command\":\"O:38:\\\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\\\":17:{s:5:\\\"event\\\";O:24:\\\"App\\\\Events\\\\SalaryChanged\\\":3:{s:8:\\\"employee\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:19:\\\"App\\\\Models\\\\Employee\\\";s:2:\\\"id\\\";i:3;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:9:\\\"oldSalary\\\";d:5000;s:9:\\\"newSalary\\\";d:9999;}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:7:\\\"backoff\\\";N;s:13:\\\"maxExceptions\\\";N;s:23:\\\"deleteWhenMissingModels\\\";b:1;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:12:\\\"messageGroup\\\";N;s:12:\\\"deduplicator\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\",\"batchId\":null},\"createdAt\":1773603256,\"delay\":null}',0,NULL,1773603256,1773603256),(3,'default','{\"uuid\":\"f0ca45d5-3dff-4d9a-857e-548754fa73bc\",\"displayName\":\"App\\\\Events\\\\SalaryChanged\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\",\"command\":\"O:38:\\\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\\\":17:{s:5:\\\"event\\\";O:24:\\\"App\\\\Events\\\\SalaryChanged\\\":3:{s:8:\\\"employee\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:19:\\\"App\\\\Models\\\\Employee\\\";s:2:\\\"id\\\";i:4;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:9:\\\"oldSalary\\\";d:3000;s:9:\\\"newSalary\\\";d:9999;}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:7:\\\"backoff\\\";N;s:13:\\\"maxExceptions\\\";N;s:23:\\\"deleteWhenMissingModels\\\";b:1;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:12:\\\"messageGroup\\\";N;s:12:\\\"deduplicator\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\",\"batchId\":null},\"createdAt\":1773603399,\"delay\":null}',0,NULL,1773603399,1773603399),(4,'default','{\"uuid\":\"7e7131dc-86aa-4310-9144-bd6744e33f13\",\"displayName\":\"App\\\\Events\\\\SalaryChanged\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\",\"command\":\"O:38:\\\"Illuminate\\\\Broadcasting\\\\BroadcastEvent\\\":17:{s:5:\\\"event\\\";O:24:\\\"App\\\\Events\\\\SalaryChanged\\\":3:{s:8:\\\"employee\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:19:\\\"App\\\\Models\\\\Employee\\\";s:2:\\\"id\\\";i:5;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:9:\\\"oldSalary\\\";d:4000;s:9:\\\"newSalary\\\";d:19000;}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:7:\\\"backoff\\\";N;s:13:\\\"maxExceptions\\\";N;s:23:\\\"deleteWhenMissingModels\\\";b:1;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:12:\\\"messageGroup\\\";N;s:12:\\\"deduplicator\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\",\"batchId\":null},\"createdAt\":1773604856,\"delay\":null}',0,NULL,1773604856,1773604856);
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_03_14_183844_create_personal_access_tokens_table',1),(5,'2026_03_14_185816_create_employees_table',1),(8,'2026_03_15_175257_add_deleted_at_to_employees_table',2),(9,'2026_03_15_193309_add_last_salary_changed_at_to_employees_table',3),(10,'2026_03_15_193744_create_positions_table',4),(11,'2026_03_15_195420_create_employee_logs_table',5);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  KEY `personal_access_tokens_expires_at_index` (`expires_at`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
INSERT INTO `personal_access_tokens` VALUES (1,'App\\Models\\User',1,'api-token','d7655057c4673dcfa7a4e354065823fcd6dc881f57f88c4933d7619fd96d295a','[\"*\"]',NULL,NULL,'2026-03-15 14:54:01','2026-03-15 14:54:01'),(2,'App\\Models\\User',1,'api-token','f7f7c3b31c7a71e040cf65d65114f5d51191bf0d18ffd6b7c11436e14a4499f7','[\"*\"]','2026-03-15 17:40:41',NULL,'2026-03-15 14:54:03','2026-03-15 17:40:41'),(3,'App\\Models\\User',1,'api-token','578884716eeb2d792e8fdbff1a4c2b8f75571a6fe7b5a15f3ee3e6feebec123b','[\"*\"]',NULL,NULL,'2026-03-15 15:01:48','2026-03-15 15:01:48'),(4,'App\\Models\\User',1,'api-token','8e25e1161143de71e46fddd2da523938b31c80703eee8ab610593ecd63c1709d','[\"*\"]','2026-03-15 17:06:50',NULL,'2026-03-15 15:12:15','2026-03-15 17:06:50'),(5,'App\\Models\\User',1,'api-token','6d4ac8697630b101211d76f0e6a306cbf379d824bee2b02dd630121b8aeb3cb6','[\"*\"]','2026-03-15 17:16:21',NULL,'2026-03-15 15:59:05','2026-03-15 17:16:21');
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `positions`
--

DROP TABLE IF EXISTS `positions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `positions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `positions_title_unique` (`title`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `positions`
--

LOCK TABLES `positions` WRITE;
/*!40000 ALTER TABLE `positions` DISABLE KEYS */;
INSERT INTO `positions` VALUES (1,'Software Engineer','Develops and maintains software applications','2026-03-15 16:53:15','2026-03-15 16:53:15'),(5,'Senior Software Engineer','Consectetur perferendis est ut maiores.','2026-03-15 18:07:54','2026-03-15 18:07:54'),(6,'Product Manager','Sit eum voluptas ea molestiae eos iure.','2026-03-15 18:07:54','2026-03-15 18:07:54'),(7,'HR Manager','Molestiae illo qui ratione earum nesciunt.','2026-03-15 18:07:54','2026-03-15 18:07:54'),(8,'Data Analyst','Accusantium voluptas est minus.','2026-03-15 18:07:54','2026-03-15 18:07:54'),(9,'DevOps Engineer','Sed totam consequuntur quis iste.','2026-03-15 18:07:54','2026-03-15 18:07:54'),(10,'UI/UX Designer','Ullam impedit sed alias excepturi molestiae ut.','2026-03-15 18:07:54','2026-03-15 18:07:54'),(11,'QA Engineer','Ad molestiae est mollitia aperiam dolores.','2026-03-15 18:07:54','2026-03-15 18:07:54');
/*!40000 ALTER TABLE `positions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('S9h7lIwx07De1gnV7HbQRl7SnsUEnoHz6yYfuq6f',NULL,'127.0.0.1','PostmanRuntime/7.51.1','YTozOntzOjY6Il90b2tlbiI7czo0MDoielRBdWVIVUtncmcyWTNpS2dTVWdJem5YSk9aVHBndGdvUVpZbktVbCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1773605198);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Ali','karamsuhil98@gmail.com',NULL,'$2y$12$iSdc3aXdg4mb4IM7nre.6et49wNJlE8LQEEjtY6XwXJfJHAqnyFLy',NULL,'2026-03-15 14:54:01','2026-03-15 14:54:01'),(2,'Admin User','admin@hr.com','2026-03-15 18:07:08','$2y$12$co1MZf5m8tHexWdooqHQd.RbGxMzKboR4pKcEs937nqgjuEQsZ/Vu','JvykHFoYqM','2026-03-15 18:07:09','2026-03-15 18:07:09');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-03-16  0:12:31
