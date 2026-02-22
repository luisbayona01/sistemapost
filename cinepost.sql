-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 22-02-2026 a las 21:39:35
-- Versión del servidor: 8.4.7
-- Versión de PHP: 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `cinepost`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `activity_logs`
--

DROP TABLE IF EXISTS `activity_logs`;
CREATE TABLE IF NOT EXISTS `activity_logs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `empresa_id` bigint UNSIGNED DEFAULT NULL,
  `action` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `module` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `data` json DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `activity_logs_user_id_foreign` (`user_id`),
  KEY `idx_activity_logs_empresa_fecha` (`empresa_id`,`created_at`)
) ENGINE=MyISAM AUTO_INCREMENT=98 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `empresa_id`, `action`, `module`, `data`, `ip_address`, `user_agent`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, 'Venta registrada', 'Ventas', '{\"venta_id\": 16}', '127.0.0.1', 'Symfony', '2026-02-09 17:36:40', '2026-02-09 17:36:40'),
(2, 1, NULL, 'Venta física registrada', 'Ventas', '{\"venta_id\": 17}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 01:22:15', '2026-02-10 01:22:15'),
(3, 1, NULL, 'Venta física registrada', 'Ventas', '{\"venta_id\": 18}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 02:21:33', '2026-02-10 02:21:33'),
(4, 1, NULL, 'Venta física registrada', 'Ventas', '{\"venta_id\": 19}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 02:48:01', '2026-02-10 02:48:01'),
(5, 1, NULL, 'Venta física registrada', 'Ventas', '{\"venta_id\": 20}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 02:48:48', '2026-02-10 02:48:48'),
(6, 1, NULL, 'Venta física registrada', 'Ventas', '{\"venta_id\": 21}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 16:50:19', '2026-02-10 16:50:19'),
(7, 1, NULL, 'Venta física registrada', 'Ventas', '{\"venta_id\": 22}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 16:50:31', '2026-02-10 16:50:31'),
(8, 1, NULL, 'Venta física registrada', 'Ventas', '{\"venta_id\": 1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 20:55:16', '2026-02-10 20:55:16'),
(9, 1, NULL, 'Venta física registrada', 'Ventas', '{\"venta_id\": 2}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 20:56:46', '2026-02-10 20:56:46'),
(10, 1, NULL, 'Venta física registrada', 'Ventas', '{\"venta_id\": 3}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 21:00:20', '2026-02-10 21:00:20'),
(11, 1, NULL, 'Venta física registrada', 'Ventas', '{\"venta_id\": 5}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 21:46:53', '2026-02-10 21:46:53'),
(12, 1, NULL, 'Venta física registrada', 'Ventas', '{\"venta_id\": 6}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 21:48:19', '2026-02-10 21:48:19'),
(13, 1, NULL, 'Venta física registrada', 'Ventas', '{\"venta_id\": 7}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 22:19:32', '2026-02-10 22:19:32'),
(14, 1, NULL, 'Venta física registrada', 'Ventas', '{\"venta_id\": 8}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 22:21:00', '2026-02-10 22:21:00'),
(15, 1, NULL, 'Venta física registrada', 'Ventas', '{\"venta_id\": 9}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 22:44:42', '2026-02-10 22:44:42'),
(16, 1, NULL, 'Venta física registrada', 'Ventas', '{\"venta_id\": 10}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-10 23:11:10', '2026-02-10 23:11:10'),
(17, 1, NULL, 'Venta POS Procesada: #21', 'Ventas', '{\"canal\": \"confiteria\", \"total\": \"13860.00\", \"asientos\": 0, \"productos\": 2}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-12 16:01:03', '2026-02-12 16:01:03'),
(18, 1, NULL, 'Venta POS Procesada: #22', 'Ventas', '{\"canal\": \"ventanilla\", \"total\": \"136000.00\", \"asientos\": 4, \"productos\": 0}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-12 16:15:00', '2026-02-12 16:15:00'),
(19, 1, NULL, 'Venta POS Procesada: #23', 'Ventas', '{\"canal\": \"ventanilla\", \"total\": \"68000.00\", \"asientos\": 2, \"productos\": 0}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-12 17:03:10', '2026-02-12 17:03:10'),
(20, 1, NULL, 'Venta POS Procesada: #24', 'Ventas', '{\"canal\": \"confiteria\", \"total\": \"73332.00\", \"asientos\": 0, \"productos\": 5}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-12 17:04:42', '2026-02-12 17:04:42'),
(21, 1, NULL, 'Venta POS Procesada: #25', 'Ventas', '{\"canal\": \"mixta\", \"total\": \"97610.00\", \"asientos\": 2, \"productos\": 4}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-12 17:05:40', '2026-02-12 17:05:40'),
(22, 1, NULL, 'Venta POS Procesada: #26', 'Ventas', '{\"canal\": \"confiteria\", \"total\": \"49000.00\", \"asientos\": 0, \"productos\": 2}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-12 17:19:58', '2026-02-12 17:19:58'),
(23, 1, NULL, 'Baja de Inventario', 'Inventario', '{\"tipo\": \"CORTESIA\", \"motivo\": \"cortesia\", \"cantidad\": \"10\", \"insumo_id\": \"10\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-12 17:25:08', '2026-02-12 17:25:08'),
(24, 1, NULL, 'Venta POS Procesada: #27', 'Ventas', '{\"canal\": \"mixta\", \"total\": \"108964.00\", \"asientos\": 2, \"productos\": 4}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-12 18:58:08', '2026-02-12 18:58:08'),
(25, 1, NULL, 'Venta POS Procesada: #28', 'Ventas', '{\"canal\": \"confiteria\", \"total\": \"35000.00\", \"asientos\": 0, \"productos\": 1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-13 00:06:50', '2026-02-13 00:06:50'),
(26, 1, NULL, 'Venta POS Procesada: #29', 'Ventas', '{\"canal\": \"confiteria\", \"total\": \"8500.00\", \"asientos\": 0, \"productos\": 1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-13 00:44:32', '2026-02-13 00:44:32'),
(27, 1, NULL, 'Venta POS Procesada: #30', 'Ventas', '{\"canal\": \"mixta\", \"total\": \"229500.00\", \"asientos\": 3, \"productos\": 5}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-13 21:05:19', '2026-02-13 21:05:19'),
(28, 1, NULL, 'Venta POS Procesada: #31', 'Ventas', '{\"canal\": \"ventanilla\", \"total\": \"14000.00\", \"asientos\": 1, \"productos\": 0}', '127.0.0.1', 'Symfony', '2026-02-14 18:38:31', '2026-02-14 18:38:31'),
(29, 1, NULL, 'Venta POS Procesada: #32', 'Ventas', '{\"canal\": \"confiteria\", \"total\": \"30000.00\", \"asientos\": 0, \"productos\": 1}', '127.0.0.1', 'Symfony', '2026-02-14 18:39:33', '2026-02-14 18:39:33'),
(30, 1, NULL, 'Venta POS Procesada: #33', 'Ventas', '{\"canal\": \"mixta\", \"total\": \"28000.00\", \"asientos\": 1, \"productos\": 1}', '127.0.0.1', 'Symfony', '2026-02-14 18:39:34', '2026-02-14 18:39:34'),
(31, 1, NULL, 'Venta POS Procesada: #34', 'Ventas', '{\"canal\": \"ventanilla\", \"total\": \"14000.00\", \"asientos\": 1, \"productos\": 0}', '127.0.0.1', 'Symfony', '2026-02-14 18:42:50', '2026-02-14 18:42:50'),
(32, 1, NULL, 'Venta POS Procesada: #35', 'Ventas', '{\"canal\": \"confiteria\", \"total\": \"30000.00\", \"asientos\": 0, \"productos\": 1}', '127.0.0.1', 'Symfony', '2026-02-14 18:42:51', '2026-02-14 18:42:51'),
(33, 1, NULL, 'Venta POS Procesada: #36', 'Ventas', '{\"canal\": \"mixta\", \"total\": \"28000.00\", \"asientos\": 1, \"productos\": 1}', '127.0.0.1', 'Symfony', '2026-02-14 18:42:51', '2026-02-14 18:42:51'),
(34, 1, NULL, 'Venta POS Procesada: #37', 'Ventas', '{\"canal\": \"ventanilla\", \"total\": \"14000.00\", \"asientos\": 1, \"productos\": 0}', '127.0.0.1', 'Symfony', '2026-02-14 18:44:45', '2026-02-14 18:44:45'),
(35, 1, NULL, 'Venta POS Procesada: #38', 'Ventas', '{\"canal\": \"confiteria\", \"total\": \"30000.00\", \"asientos\": 0, \"productos\": 1}', '127.0.0.1', 'Symfony', '2026-02-14 18:44:46', '2026-02-14 18:44:46'),
(36, 1, NULL, 'Venta POS Procesada: #39', 'Ventas', '{\"canal\": \"mixta\", \"total\": \"28000.00\", \"asientos\": 1, \"productos\": 1}', '127.0.0.1', 'Symfony', '2026-02-14 18:44:46', '2026-02-14 18:44:46'),
(37, 1, NULL, 'Venta POS Procesada: #40', 'Ventas', '{\"canal\": \"confiteria\", \"total\": \"10000.00\", \"asientos\": 0, \"productos\": 1}', '127.0.0.1', 'Symfony', '2026-02-14 19:00:52', '2026-02-14 19:00:52'),
(38, 1, NULL, 'Venta POS Procesada: #41', 'Ventas', '{\"canal\": \"confiteria\", \"total\": \"15000.00\", \"asientos\": 0, \"productos\": 1}', '127.0.0.1', 'Symfony', '2026-02-14 19:00:52', '2026-02-14 19:00:52'),
(39, 1, NULL, 'Venta POS Procesada: #42', 'Ventas', '{\"canal\": \"confiteria\", \"total\": \"15000.00\", \"asientos\": 0, \"productos\": 1}', '127.0.0.1', 'Symfony', '2026-02-14 19:02:12', '2026-02-14 19:02:12'),
(40, 1, NULL, 'Venta POS Procesada: #43', 'Ventas', '{\"canal\": \"confiteria\", \"total\": \"15000.00\", \"asientos\": 0, \"productos\": 1}', '127.0.0.1', 'Symfony', '2026-02-14 19:05:11', '2026-02-14 19:05:11'),
(41, 1, NULL, 'Venta POS Procesada: #44', 'Ventas', '{\"canal\": \"ventanilla\", \"total\": \"14000.00\", \"asientos\": 1, \"productos\": 0}', '127.0.0.1', 'Symfony', '2026-02-14 19:05:12', '2026-02-14 19:05:12'),
(42, 1, NULL, 'Venta POS Procesada: #45', 'Ventas', '{\"canal\": \"confiteria\", \"total\": \"0.00\", \"asientos\": 0, \"productos\": 2}', '127.0.0.1', 'Symfony', '2026-02-14 19:20:08', '2026-02-14 19:20:08'),
(43, 1, NULL, 'Venta POS Procesada: #46', 'Ventas', '{\"canal\": \"confiteria\", \"total\": \"0.00\", \"asientos\": 0, \"productos\": 2}', '127.0.0.1', 'Symfony', '2026-02-14 19:26:59', '2026-02-14 19:26:59'),
(44, 1, NULL, 'Venta POS Procesada: #47', 'Ventas', '{\"canal\": \"confiteria\", \"total\": \"0.00\", \"asientos\": 0, \"productos\": 2}', '127.0.0.1', 'Symfony', '2026-02-14 19:34:59', '2026-02-14 19:34:59'),
(45, 1, NULL, 'Venta POS Procesada: #48', 'Ventas', '{\"canal\": \"confiteria\", \"total\": \"0.00\", \"asientos\": 0, \"productos\": 2}', '127.0.0.1', 'Symfony', '2026-02-14 19:36:52', '2026-02-14 19:36:52'),
(46, 1, NULL, 'Venta POS Procesada: #49', 'Ventas', '{\"canal\": \"mixta\", \"total\": \"136000.00\", \"asientos\": 4, \"productos\": 5}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-14 22:50:08', '2026-02-14 22:50:08'),
(47, 1, NULL, 'Creación de producto', 'Productos', '{\"codigo\": \"0002\", \"nombre\": \"PIZZA HAWAIANA\", \"marca_id\": null, \"descripcion\": \"PIZZA\", \"categoria_id\": null, \"presentacione_id\": \"1\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-14 23:55:59', '2026-02-14 23:55:59'),
(48, 1, NULL, 'Creación de producto', 'Productos', '{\"codigo\": \"00055\", \"estado\": \"1\", \"nombre\": \"Pizza Hawaiana1\", \"marca_id\": null, \"descripcion\": \"pizza\", \"categoria_id\": null, \"presentacione_id\": \"1\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-15 00:10:10', '2026-02-15 00:10:10'),
(49, 1, NULL, 'Venta POS Procesada: #50', 'Ventas', '{\"canal\": \"confiteria\", \"total\": \"0.00\", \"asientos\": 0, \"productos\": 5}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-15 00:11:17', '2026-02-15 00:11:17'),
(50, 1, NULL, 'Venta POS Procesada: #51', 'Ventas', '{\"canal\": \"mixta\", \"total\": \"102000.00\", \"asientos\": 3, \"productos\": 5}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-16 15:26:51', '2026-02-16 15:26:51'),
(51, 1, NULL, 'Venta POS Procesada: #52', 'Ventas', '{\"canal\": \"mixta\", \"total\": \"68000.00\", \"asientos\": 2, \"productos\": 5}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-16 15:28:57', '2026-02-16 15:28:57'),
(52, 1, NULL, 'Venta POS Procesada: #1', 'Ventas', '{\"canal\": \"confiteria\", \"total\": \"0.00\", \"asientos\": 0, \"productos\": 2}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-16 16:24:55', '2026-02-16 16:24:55'),
(53, 1, NULL, 'Venta POS Procesada: #2', 'Ventas', '{\"canal\": \"ventanilla\", \"total\": \"28000.00\", \"asientos\": 2, \"productos\": 0}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-16 16:37:11', '2026-02-16 16:37:11'),
(54, 1, NULL, 'Venta POS Procesada: #3', 'Ventas', '{\"canal\": \"confiteria\", \"total\": \"0.00\", \"asientos\": 0, \"productos\": 4}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-16 16:38:39', '2026-02-16 16:38:39'),
(55, 6, NULL, 'Venta POS Procesada: #4', 'Ventas', '{\"canal\": \"confiteria\", \"total\": \"0.00\", \"asientos\": 0, \"productos\": 1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-16 19:34:49', '2026-02-16 19:34:49'),
(56, 6, NULL, 'Venta POS completada', 'Ventas', '{\"venta_id\": 14}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-19 19:35:23', '2026-02-19 19:35:23'),
(57, 6, NULL, 'Venta POS completada', 'Ventas', '{\"venta_id\": 15}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-19 19:42:26', '2026-02-19 19:42:26'),
(58, 6, NULL, 'Venta POS completada', 'Ventas', '{\"venta_id\": 16}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-19 19:46:39', '2026-02-19 19:46:39'),
(59, 6, NULL, 'Venta POS completada', 'Ventas', '{\"venta_id\": 17}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-19 20:29:42', '2026-02-19 20:29:42'),
(60, 6, NULL, 'Venta POS completada', 'Ventas', '{\"venta_id\": 18}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-19 20:30:40', '2026-02-19 20:30:40'),
(61, 8, NULL, 'Venta POS completada', 'Ventas', '{\"venta_id\": 19}', '127.0.0.1', 'Symfony', '2026-02-19 20:56:23', '2026-02-19 20:56:23'),
(62, 8, NULL, 'Venta POS completada', 'Ventas', '{\"venta_id\": 20}', '127.0.0.1', 'Symfony', '2026-02-19 20:57:29', '2026-02-19 20:57:29'),
(63, 6, NULL, 'Venta POS completada', 'Ventas', '{\"venta_id\": 21}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-19 20:59:18', '2026-02-19 20:59:18'),
(64, 6, NULL, 'Venta POS completada', 'Ventas', '{\"venta_id\": 22}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-19 21:00:35', '2026-02-19 21:00:35'),
(65, 6, NULL, 'Venta POS completada', 'Ventas', '{\"venta_id\": 23}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-19 21:01:16', '2026-02-19 21:01:16'),
(66, 6, NULL, 'Venta POS completada', 'Ventas', '{\"venta_id\": 24}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-19 21:02:49', '2026-02-19 21:02:49'),
(67, 6, NULL, 'Venta POS completada', 'Ventas', '{\"venta_id\": 25}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-19 22:32:59', '2026-02-19 22:32:59'),
(68, 6, NULL, 'Venta POS completada', 'Ventas', '{\"venta_id\": 26}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-19 23:01:51', '2026-02-19 23:01:51'),
(69, 6, NULL, 'Venta POS completada', 'Ventas', '{\"venta_id\": 27}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-20 01:13:27', '2026-02-20 01:13:27'),
(70, 6, NULL, 'Venta POS completada', 'Ventas', '{\"venta_id\": 28}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-20 01:13:57', '2026-02-20 01:13:57'),
(71, 6, NULL, 'Venta POS completada', 'Ventas', '{\"venta_id\": 29}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-20 01:15:19', '2026-02-20 01:15:19'),
(72, 6, NULL, 'Venta POS completada', 'Ventas', '{\"venta_id\": 30}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', '2026-02-20 01:15:44', '2026-02-20 01:15:44'),
(73, 6, NULL, 'Venta POS completada', 'Ventas', '{\"venta_id\": 31}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-20 14:56:19', '2026-02-20 14:56:19'),
(74, 6, NULL, 'Venta POS completada', 'Ventas', '{\"venta_id\": 1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-20 16:14:22', '2026-02-20 16:14:22'),
(75, 6, NULL, 'Venta POS completada', 'Ventas', '{\"venta_id\": 2}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-20 17:32:32', '2026-02-20 17:32:32'),
(76, 6, NULL, 'Venta POS completada', 'Ventas', '{\"venta_id\": 3}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-20 17:33:34', '2026-02-20 17:33:34'),
(77, 6, NULL, 'Venta POS completada', 'Ventas', '{\"venta_id\": 4}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-20 18:11:01', '2026-02-20 18:11:01'),
(78, 6, NULL, 'Creación de producto', 'Productos', '{\"codigo\": null, \"estado\": \"1\", \"nombre\": \"Pizza hawaiana\", \"marca_id\": null, \"descripcion\": \"pizza\", \"categoria_id\": null, \"presentacione_id\": \"1\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-20 23:33:00', '2026-02-20 23:33:00'),
(79, 6, NULL, 'Venta POS completada', 'Ventas', '{\"venta_id\": 5}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-21 00:01:16', '2026-02-21 00:01:16'),
(80, 6, NULL, 'Venta POS completada', 'Ventas', '{\"venta_id\": 6}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-21 01:36:38', '2026-02-21 01:36:38'),
(81, 6, NULL, 'Venta POS completada', 'Ventas', '{\"venta_id\": 7}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-21 01:57:48', '2026-02-21 01:57:48'),
(82, 6, NULL, 'Venta POS completada', 'Ventas', '{\"venta_id\": 8}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-21 02:00:47', '2026-02-21 02:00:47'),
(83, 6, NULL, 'Venta POS completada', 'Ventas', '{\"venta_id\": 9}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-21 02:09:07', '2026-02-21 02:09:07'),
(84, 6, NULL, 'Venta POS completada', 'Ventas', '{\"venta_id\": 10}', '127.0.0.1', 'Symfony', '2026-02-21 16:00:36', '2026-02-21 16:00:36'),
(85, 6, NULL, 'Venta POS completada', 'Ventas', '{\"venta_id\": 11}', '127.0.0.1', 'Symfony', '2026-02-21 16:00:38', '2026-02-21 16:00:38'),
(86, 6, NULL, 'Venta POS completada', 'Ventas', '{\"venta_id\": 12}', '127.0.0.1', 'Symfony', '2026-02-21 16:00:38', '2026-02-21 16:00:38'),
(87, 6, NULL, 'Venta POS completada', 'Ventas', '{\"venta_id\": 13}', '127.0.0.1', 'Symfony', '2026-02-21 16:00:39', '2026-02-21 16:00:39'),
(88, 6, NULL, 'Venta POS completada', 'Ventas', '{\"venta_id\": 14}', '127.0.0.1', 'Symfony', '2026-02-21 16:00:39', '2026-02-21 16:00:39'),
(89, 6, NULL, 'Venta POS completada', 'Ventas', '{\"venta_id\": 15}', '127.0.0.1', 'Symfony', '2026-02-21 16:00:39', '2026-02-21 16:00:39'),
(90, 6, NULL, 'Venta POS completada', 'Ventas', '{\"venta_id\": 16}', '127.0.0.1', 'Symfony', '2026-02-21 16:00:40', '2026-02-21 16:00:40'),
(91, 6, NULL, 'Venta POS completada', 'Ventas', '{\"venta_id\": 17}', '127.0.0.1', 'Symfony', '2026-02-21 16:00:40', '2026-02-21 16:00:40'),
(92, 6, NULL, 'Venta POS completada', 'Ventas', '{\"venta_id\": 18}', '127.0.0.1', 'Symfony', '2026-02-21 16:00:40', '2026-02-21 16:00:40'),
(93, 6, NULL, 'Venta POS completada', 'Ventas', '{\"venta_id\": 19}', '127.0.0.1', 'Symfony', '2026-02-21 16:01:14', '2026-02-21 16:01:14'),
(94, 6, NULL, 'Venta POS completada', 'Ventas', '{\"venta_id\": 20}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-21 17:37:39', '2026-02-21 17:37:39'),
(95, 6, NULL, 'Venta POS completada', 'Ventas', '{\"venta_id\": 21}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-21 19:09:36', '2026-02-21 19:09:36'),
(96, 6, NULL, 'Venta POS completada', 'Ventas', '{\"venta_id\": 22}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-21 19:11:14', '2026-02-21 19:11:14'),
(97, 6, NULL, 'Venta POS completada', 'Ventas', '{\"venta_id\": 23}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '2026-02-21 22:02:06', '2026-02-21 22:02:06');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alertas`
--

DROP TABLE IF EXISTS `alertas`;
CREATE TABLE IF NOT EXISTS `alertas` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint UNSIGNED NOT NULL,
  `tipo` enum('CRITICA','ADVERTENCIA','INFO') COLLATE utf8mb4_unicode_ci NOT NULL,
  `categoria` enum('INVENTARIO','OCUPACION','CAJA','PRECIO','GENERAL') COLLATE utf8mb4_unicode_ci NOT NULL,
  `titulo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mensaje` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `datos` json DEFAULT NULL COMMENT 'Datos adicionales en JSON',
  `vista` tinyint(1) NOT NULL DEFAULT '0',
  `resuelta` tinyint(1) NOT NULL DEFAULT '0',
  `resuelta_at` timestamp NULL DEFAULT NULL,
  `resuelta_por` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `alertas_resuelta_por_foreign` (`resuelta_por`),
  KEY `alertas_empresa_id_vista_resuelta_index` (`empresa_id`,`vista`,`resuelta`),
  KEY `alertas_tipo_created_at_index` (`tipo`,`created_at`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auditorias_inventario`
--

DROP TABLE IF EXISTS `auditorias_inventario`;
CREATE TABLE IF NOT EXISTS `auditorias_inventario` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `fecha_auditoria` datetime NOT NULL,
  `estado` enum('abierta','finalizada') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'abierta',
  `total_diferencia_valor` decimal(15,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `auditorias_inventario_empresa_id_foreign` (`empresa_id`),
  KEY `auditorias_inventario_user_id_foreign` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `auditorias_inventario`
--

INSERT INTO `auditorias_inventario` (`id`, `empresa_id`, `user_id`, `fecha_auditoria`, `estado`, `total_diferencia_valor`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2026-02-05 21:50:50', 'finalizada', 0.00, '2026-02-06 02:50:50', '2026-02-06 21:38:02'),
(2, 1, 1, '2026-02-06 16:38:08', 'abierta', 0.00, '2026-02-06 21:38:08', '2026-02-06 21:38:08'),
(3, 1, 1, '2026-02-09 21:47:37', 'abierta', 0.00, '2026-02-10 02:47:37', '2026-02-10 02:47:37'),
(4, 1, 1, '2026-02-14 11:38:10', 'abierta', 0.00, '2026-02-14 16:38:10', '2026-02-14 16:38:10');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `auditoria_detalles`
--

DROP TABLE IF EXISTS `auditoria_detalles`;
CREATE TABLE IF NOT EXISTS `auditoria_detalles` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `auditoria_id` bigint UNSIGNED NOT NULL,
  `insumo_id` bigint UNSIGNED NOT NULL,
  `stock_teorico` decimal(15,3) NOT NULL,
  `stock_fisico` decimal(15,3) DEFAULT NULL,
  `diferencia` decimal(15,3) DEFAULT NULL,
  `valor_diferencia` decimal(15,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `auditoria_detalles_auditoria_id_foreign` (`auditoria_id`),
  KEY `auditoria_detalles_insumo_id_foreign` (`insumo_id`)
) ENGINE=MyISAM AUTO_INCREMENT=52 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `auditoria_detalles`
--

INSERT INTO `auditoria_detalles` (`id`, `auditoria_id`, `insumo_id`, `stock_teorico`, `stock_fisico`, `diferencia`, `valor_diferencia`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 0.000, 2000.000, 2000.000, 100000000.00, '2026-02-06 02:50:50', '2026-02-06 21:38:02'),
(2, 2, 1, 7199.999, NULL, NULL, 0.00, '2026-02-06 21:38:08', '2026-02-06 21:38:08'),
(3, 2, 2, 100.000, NULL, NULL, 0.00, '2026-02-06 21:38:08', '2026-02-06 21:38:08'),
(4, 2, 3, 100.000, NULL, NULL, 0.00, '2026-02-06 21:38:08', '2026-02-06 21:38:08'),
(5, 2, 4, 20.000, NULL, NULL, 0.00, '2026-02-06 21:38:08', '2026-02-06 21:38:08'),
(6, 2, 5, 5.000, NULL, NULL, 0.00, '2026-02-06 21:38:08', '2026-02-06 21:38:08'),
(7, 2, 6, 100.000, NULL, NULL, 0.00, '2026-02-06 21:38:08', '2026-02-06 21:38:08'),
(8, 2, 7, 100.000, NULL, NULL, 0.00, '2026-02-06 21:38:08', '2026-02-06 21:38:08'),
(9, 2, 8, 100.000, NULL, NULL, 0.00, '2026-02-06 21:38:08', '2026-02-06 21:38:08'),
(10, 2, 9, 50.000, NULL, NULL, 0.00, '2026-02-06 21:38:08', '2026-02-06 21:38:08'),
(11, 2, 10, 10.000, NULL, NULL, 0.00, '2026-02-06 21:38:08', '2026-02-06 21:38:08'),
(12, 2, 11, 100.000, NULL, NULL, 0.00, '2026-02-06 21:38:08', '2026-02-06 21:38:08'),
(13, 2, 12, 48.000, NULL, NULL, 0.00, '2026-02-06 21:38:08', '2026-02-06 21:38:08'),
(14, 2, 13, 60.000, NULL, NULL, 0.00, '2026-02-06 21:38:08', '2026-02-06 21:38:08'),
(15, 2, 14, 30.000, NULL, NULL, 0.00, '2026-02-06 21:38:08', '2026-02-06 21:38:08'),
(16, 2, 15, 200.000, NULL, NULL, 0.00, '2026-02-06 21:38:08', '2026-02-06 21:38:08'),
(17, 2, 16, 500.000, NULL, NULL, 0.00, '2026-02-06 21:38:08', '2026-02-06 21:38:08'),
(18, 3, 1, 7199.999, NULL, NULL, 0.00, '2026-02-10 02:47:38', '2026-02-10 02:47:38'),
(19, 3, 2, 99.850, NULL, NULL, 0.00, '2026-02-10 02:47:38', '2026-02-10 02:47:38'),
(20, 3, 3, 100.000, NULL, NULL, 0.00, '2026-02-10 02:47:38', '2026-02-10 02:47:38'),
(21, 3, 4, 0.000, NULL, NULL, 0.00, '2026-02-10 02:47:38', '2026-02-10 02:47:38'),
(22, 3, 5, 5.000, NULL, NULL, 0.00, '2026-02-10 02:47:38', '2026-02-10 02:47:38'),
(23, 3, 6, 99.000, NULL, NULL, 0.00, '2026-02-10 02:47:38', '2026-02-10 02:47:38'),
(24, 3, 7, 99.000, NULL, NULL, 0.00, '2026-02-10 02:47:38', '2026-02-10 02:47:38'),
(25, 3, 8, 100.000, NULL, NULL, 0.00, '2026-02-10 02:47:38', '2026-02-10 02:47:38'),
(26, 3, 9, 50.000, NULL, NULL, 0.00, '2026-02-10 02:47:38', '2026-02-10 02:47:38'),
(27, 3, 10, 10.000, NULL, NULL, 0.00, '2026-02-10 02:47:38', '2026-02-10 02:47:38'),
(28, 3, 11, 100.000, NULL, NULL, 0.00, '2026-02-10 02:47:38', '2026-02-10 02:47:38'),
(29, 3, 12, 48.000, NULL, NULL, 0.00, '2026-02-10 02:47:38', '2026-02-10 02:47:38'),
(30, 3, 13, 60.000, NULL, NULL, 0.00, '2026-02-10 02:47:38', '2026-02-10 02:47:38'),
(31, 3, 14, 30.000, NULL, NULL, 0.00, '2026-02-10 02:47:38', '2026-02-10 02:47:38'),
(32, 3, 15, 200.000, NULL, NULL, 0.00, '2026-02-10 02:47:38', '2026-02-10 02:47:38'),
(33, 3, 16, 500.000, NULL, NULL, 0.00, '2026-02-10 02:47:38', '2026-02-10 02:47:38'),
(34, 4, 1, 53.000, NULL, NULL, 0.00, '2026-02-14 16:38:10', '2026-02-14 16:38:10'),
(35, 4, 2, 53.000, NULL, NULL, 0.00, '2026-02-14 16:38:10', '2026-02-14 16:38:10'),
(36, 4, 3, 2632.000, NULL, NULL, 0.00, '2026-02-14 16:38:10', '2026-02-14 16:38:10'),
(37, 4, 4, 102.841, NULL, NULL, 0.00, '2026-02-14 16:38:10', '2026-02-14 16:38:10'),
(38, 4, 5, 15316.315, NULL, NULL, 0.00, '2026-02-14 16:38:10', '2026-02-14 16:38:10'),
(39, 4, 6, 5158.737, NULL, NULL, 0.00, '2026-02-14 16:38:10', '2026-02-14 16:38:10'),
(40, 4, 7, 10212.211, NULL, NULL, 0.00, '2026-02-14 16:38:10', '2026-02-14 16:38:10'),
(41, 4, 8, 46.683, NULL, NULL, 0.00, '2026-02-14 16:38:10', '2026-02-14 16:38:10'),
(42, 4, 9, 10000.000, NULL, NULL, 0.00, '2026-02-14 16:38:10', '2026-02-14 16:38:10'),
(43, 4, 10, 137.735, NULL, NULL, 0.00, '2026-02-14 16:38:10', '2026-02-14 16:38:10'),
(44, 4, 11, 7421.315, NULL, NULL, 0.00, '2026-02-14 16:38:10', '2026-02-14 16:38:10'),
(45, 4, 12, 6947.631, NULL, NULL, 0.00, '2026-02-14 16:38:10', '2026-02-14 16:38:10'),
(46, 4, 13, 3031.684, NULL, NULL, 0.00, '2026-02-14 16:38:10', '2026-02-14 16:38:10'),
(47, 4, 14, 3685.053, NULL, NULL, 0.00, '2026-02-14 16:38:10', '2026-02-14 16:38:10'),
(48, 4, 15, 737.211, NULL, NULL, 0.00, '2026-02-14 16:38:10', '2026-02-14 16:38:10'),
(49, 4, 16, 7895.000, NULL, NULL, 0.00, '2026-02-14 16:38:10', '2026-02-14 16:38:10'),
(50, 4, 17, 10.000, NULL, NULL, 0.00, '2026-02-14 16:38:10', '2026-02-14 16:38:10'),
(51, 4, 18, 3.000, NULL, NULL, 0.00, '2026-02-14 16:38:10', '2026-02-14 16:38:10');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `business_configurations`
--

DROP TABLE IF EXISTS `business_configurations`;
CREATE TABLE IF NOT EXISTS `business_configurations` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint UNSIGNED NOT NULL,
  `business_type` enum('cinema','restaurant','bakery','bar','retail','generic') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'generic',
  `modules_enabled` json NOT NULL COMMENT 'Módulos activos: cinema, pos, inventory, reports, api',
  `settings` json DEFAULT NULL COMMENT 'Configuraciones específicas del negocio',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `business_configurations_empresa_id_unique` (`empresa_id`)
) ENGINE=MyISAM AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `business_configurations`
--

INSERT INTO `business_configurations` (`id`, `empresa_id`, `business_type`, `modules_enabled`, `settings`, `created_at`, `updated_at`) VALUES
(1, 1, 'cinema', '{\"api\": false, \"pos\": true, \"cinema\": true, \"reports\": true, \"inventory\": true}', '{\"currency\": \"COP\", \"timezone\": \"America/Bogota\", \"tax_included\": false}', '2026-02-09 16:44:45', '2026-02-09 22:10:14'),
(2, 2, 'cinema', '{\"api\": false, \"pos\": true, \"cinema\": true, \"reports\": true, \"inventory\": true}', '{\"currency\": \"COP\", \"timezone\": \"America/Bogota\", \"tax_included\": false}', '2026-02-09 16:44:45', '2026-02-09 22:10:14'),
(3, 3, 'cinema', '{\"api\": false, \"pos\": true, \"cinema\": true, \"reports\": true, \"inventory\": true}', '{\"currency\": \"COP\", \"timezone\": \"America/Bogota\", \"tax_included\": false}', '2026-02-09 16:44:45', '2026-02-09 22:10:14'),
(4, 4, 'cinema', '{\"api\": false, \"pos\": true, \"cinema\": true, \"reports\": true, \"inventory\": true}', '{\"currency\": \"COP\", \"timezone\": \"America/Bogota\", \"tax_included\": false}', '2026-02-09 16:44:45', '2026-02-09 22:10:14'),
(5, 5, 'cinema', '{\"api\": false, \"pos\": true, \"cinema\": true, \"reports\": true, \"inventory\": true}', '{\"currency\": \"COP\", \"timezone\": \"America/Bogota\", \"tax_included\": false}', '2026-02-09 16:44:45', '2026-02-09 22:10:14'),
(6, 6, 'cinema', '{\"api\": false, \"pos\": true, \"cinema\": true, \"reports\": true, \"inventory\": true}', '{\"currency\": \"COP\", \"timezone\": \"America/Bogota\", \"tax_included\": false}', '2026-02-09 16:44:45', '2026-02-09 22:10:14'),
(7, 7, 'cinema', '{\"api\": false, \"pos\": true, \"cinema\": true, \"reports\": true, \"inventory\": true}', '{\"currency\": \"COP\", \"timezone\": \"America/Bogota\", \"tax_included\": false}', '2026-02-09 16:44:45', '2026-02-09 22:10:14'),
(8, 8, 'cinema', '{\"api\": false, \"pos\": true, \"cinema\": true, \"reports\": true, \"inventory\": true}', '{\"currency\": \"COP\", \"timezone\": \"America/Bogota\", \"tax_included\": false}', '2026-02-09 16:44:45', '2026-02-09 22:10:14'),
(9, 9, 'cinema', '{\"api\": false, \"pos\": true, \"cinema\": true, \"reports\": true, \"inventory\": true}', '{\"currency\": \"COP\", \"timezone\": \"America/Bogota\", \"tax_included\": false}', '2026-02-09 16:44:45', '2026-02-09 22:10:14'),
(10, 10, 'cinema', '{\"api\": false, \"pos\": true, \"cinema\": true, \"reports\": true, \"inventory\": true}', '{\"currency\": \"COP\", \"timezone\": \"America/Bogota\", \"tax_included\": false}', '2026-02-09 16:44:45', '2026-02-09 22:10:14'),
(11, 11, 'cinema', '{\"api\": false, \"pos\": true, \"cinema\": true, \"reports\": true, \"inventory\": true}', '{\"currency\": \"COP\", \"timezone\": \"America/Bogota\", \"tax_included\": false}', '2026-02-09 16:44:45', '2026-02-09 22:10:15'),
(12, 12, 'cinema', '{\"api\": false, \"pos\": true, \"cinema\": true, \"reports\": true, \"inventory\": true}', '{\"currency\": \"COP\", \"timezone\": \"America/Bogota\", \"tax_included\": false}', '2026-02-09 16:44:45', '2026-02-09 22:10:15'),
(13, 13, 'cinema', '{\"api\": false, \"pos\": true, \"cinema\": true, \"reports\": true, \"inventory\": true}', '{\"currency\": \"COP\", \"timezone\": \"America/Bogota\", \"tax_included\": false}', '2026-02-09 16:44:45', '2026-02-09 22:10:15'),
(14, 14, 'cinema', '{\"api\": false, \"pos\": true, \"cinema\": true, \"reports\": true, \"inventory\": true}', '{\"currency\": \"COP\", \"timezone\": \"America/Bogota\", \"tax_included\": false}', '2026-02-09 16:44:45', '2026-02-09 22:10:15'),
(15, 15, 'cinema', '{\"api\": false, \"pos\": true, \"cinema\": true, \"reports\": true, \"inventory\": true}', '{\"currency\": \"COP\", \"timezone\": \"America/Bogota\", \"tax_included\": false}', '2026-02-09 16:44:45', '2026-02-09 22:10:15'),
(16, 16, 'cinema', '{\"api\": false, \"pos\": true, \"cinema\": true, \"reports\": true, \"inventory\": true}', '{\"currency\": \"COP\", \"timezone\": \"America/Bogota\", \"tax_included\": false}', '2026-02-09 16:44:45', '2026-02-09 22:10:15'),
(17, 17, 'cinema', '{\"api\": false, \"pos\": true, \"cinema\": true, \"reports\": true, \"inventory\": true}', '{\"currency\": \"COP\", \"timezone\": \"America/Bogota\", \"tax_included\": false}', '2026-02-09 16:44:45', '2026-02-09 22:10:15'),
(18, 18, 'cinema', '{\"api\": false, \"pos\": true, \"cinema\": true, \"reports\": true, \"inventory\": true}', '{\"currency\": \"COP\", \"timezone\": \"America/Bogota\", \"tax_included\": false}', '2026-02-09 16:44:45', '2026-02-09 22:10:15'),
(19, 19, 'cinema', '{\"api\": false, \"pos\": true, \"cinema\": true, \"reports\": true, \"inventory\": true}', '{\"currency\": \"COP\", \"timezone\": \"America/Bogota\", \"tax_included\": false}', '2026-02-09 16:44:45', '2026-02-09 22:10:15'),
(20, 20, 'cinema', '{\"api\": false, \"pos\": true, \"cinema\": true, \"reports\": true, \"inventory\": true}', '{\"currency\": \"COP\", \"timezone\": \"America/Bogota\", \"tax_included\": false}', '2026-02-09 16:44:45', '2026-02-09 22:10:15'),
(21, 21, 'cinema', '{\"api\": false, \"pos\": true, \"cinema\": true, \"reports\": true, \"inventory\": true}', '{\"currency\": \"COP\", \"timezone\": \"America/Bogota\", \"tax_included\": false}', '2026-02-09 16:44:45', '2026-02-09 22:10:15'),
(22, 22, 'cinema', '{\"api\": false, \"pos\": true, \"cinema\": true, \"reports\": true, \"inventory\": true}', '{\"currency\": \"COP\", \"timezone\": \"America/Bogota\", \"tax_included\": false}', '2026-02-09 16:44:45', '2026-02-09 22:10:15'),
(23, 23, 'cinema', '{\"api\": false, \"pos\": true, \"cinema\": true, \"reports\": true, \"inventory\": true}', '{\"currency\": \"COP\", \"timezone\": \"America/Bogota\", \"tax_included\": false}', '2026-02-09 16:44:45', '2026-02-09 22:10:15'),
(24, 24, 'cinema', '{\"api\": false, \"pos\": true, \"cinema\": true, \"reports\": true, \"inventory\": true}', '{\"currency\": \"COP\", \"timezone\": \"America/Bogota\", \"tax_included\": false}', '2026-02-09 16:44:45', '2026-02-09 22:10:15'),
(25, 25, 'cinema', '{\"api\": false, \"pos\": true, \"cinema\": true, \"reports\": true, \"inventory\": true}', '{\"currency\": \"COP\", \"timezone\": \"America/Bogota\", \"tax_included\": false}', '2026-02-09 16:44:45', '2026-02-09 22:10:15'),
(26, 26, 'cinema', '{\"api\": false, \"pos\": true, \"cinema\": true, \"reports\": true, \"inventory\": true}', '{\"currency\": \"COP\", \"timezone\": \"America/Bogota\", \"tax_included\": false}', '2026-02-09 16:44:45', '2026-02-09 22:10:15'),
(27, 27, 'cinema', '{\"api\": false, \"pos\": true, \"cinema\": true, \"reports\": true, \"inventory\": true}', '{\"currency\": \"COP\", \"timezone\": \"America/Bogota\", \"tax_included\": false}', '2026-02-09 16:44:45', '2026-02-09 22:10:15'),
(28, 28, 'cinema', '{\"api\": false, \"pos\": true, \"cinema\": true, \"reports\": true, \"inventory\": true}', '{\"currency\": \"COP\", \"timezone\": \"America/Bogota\", \"tax_included\": false}', '2026-02-09 16:44:45', '2026-02-09 22:10:15');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cajas`
--

DROP TABLE IF EXISTS `cajas`;
CREATE TABLE IF NOT EXISTS `cajas` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint UNSIGNED NOT NULL,
  `nombre` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_apertura` datetime NOT NULL,
  `fecha_cierre` timestamp NULL DEFAULT NULL,
  `fecha_hora_cierre` datetime DEFAULT NULL,
  `cierre_at` timestamp NULL DEFAULT NULL,
  `monto_inicial` decimal(8,2) NOT NULL,
  `monto_final_declarado` decimal(10,2) DEFAULT NULL,
  `efectivo_declarado` decimal(10,2) DEFAULT NULL,
  `tarjeta_declarado` decimal(10,2) DEFAULT NULL,
  `otros_declarado` decimal(10,2) DEFAULT NULL,
  `tarjeta_declarada` decimal(10,2) DEFAULT NULL COMMENT 'Total de vouchers/datáfono declarado por el cajero',
  `tarjeta_esperada` decimal(10,2) DEFAULT NULL COMMENT 'Total de ventas con tarjeta según el sistema',
  `monto_final_esperado` decimal(10,2) DEFAULT NULL,
  `saldo_final` decimal(8,2) DEFAULT NULL,
  `conteo_efectivo` json DEFAULT NULL,
  `motivo_diferencia` text COLLATE utf8mb4_unicode_ci,
  `monto_esperado` decimal(10,2) DEFAULT NULL,
  `diferencia` decimal(10,2) DEFAULT NULL,
  `diferencia_tarjeta` decimal(10,2) DEFAULT NULL COMMENT 'Diferencia entre tarjeta declarada y esperada',
  `notas_cierre` text COLLATE utf8mb4_unicode_ci,
  `estado` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ABIERTA',
  `estado_cierre` enum('normal','reabierto_admin') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'normal',
  `user_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `cerrado_por` bigint UNSIGNED DEFAULT NULL,
  `reabierto_por_user_id` bigint UNSIGNED DEFAULT NULL,
  `reabierto_at` timestamp NULL DEFAULT NULL,
  `motivo_reapertura` text COLLATE utf8mb4_unicode_ci,
  `cierre_version` int NOT NULL DEFAULT '1',
  `cierre_original_id` bigint UNSIGNED DEFAULT NULL,
  `cierre_user_id` bigint UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cajas_user_id_foreign` (`user_id`),
  KEY `cajas_empresa_id_estado_index` (`empresa_id`,`estado`),
  KEY `cajas_cerrado_por_foreign` (`cerrado_por`),
  KEY `idx_cajas_empresa_user_estado` (`empresa_id`,`user_id`,`estado`),
  KEY `idx_cajas_empresa_fecha` (`empresa_id`,`created_at`),
  KEY `cajas_reabierto_por_user_id_foreign` (`reabierto_por_user_id`),
  KEY `cajas_cierre_original_id_foreign` (`cierre_original_id`),
  KEY `cajas_cierre_user_id_foreign` (`cierre_user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `cajas`
--

INSERT INTO `cajas` (`id`, `empresa_id`, `nombre`, `fecha_apertura`, `fecha_cierre`, `fecha_hora_cierre`, `cierre_at`, `monto_inicial`, `monto_final_declarado`, `efectivo_declarado`, `tarjeta_declarado`, `otros_declarado`, `tarjeta_declarada`, `tarjeta_esperada`, `monto_final_esperado`, `saldo_final`, `conteo_efectivo`, `motivo_diferencia`, `monto_esperado`, `diferencia`, `diferencia_tarjeta`, `notas_cierre`, `estado`, `estado_cierre`, `user_id`, `created_at`, `updated_at`, `cerrado_por`, `reabierto_por_user_id`, `reabierto_at`, `motivo_reapertura`, `cierre_version`, `cierre_original_id`, `cierre_user_id`) VALUES
(1, 1, 'Caja de Super Administrador', '2026-02-20 10:49:42', '2026-02-21 18:10:28', NULL, NULL, 0.00, 2700538.00, 700538.00, 2000000.00, 0.00, NULL, NULL, 2700538.00, 0.00, NULL, NULL, NULL, 0.00, NULL, NULL, 'CERRADA', 'normal', 6, '2026-02-20 15:49:42', '2026-02-21 18:10:28', 6, NULL, NULL, NULL, 1, NULL, NULL),
(2, 1, 'Caja de Super Administrador', '2026-02-21 14:00:31', NULL, NULL, NULL, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'ABIERTA', 'normal', 6, '2026-02-21 19:00:31', '2026-02-21 19:00:31', NULL, NULL, NULL, NULL, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `caracteristicas`
--

DROP TABLE IF EXISTS `caracteristicas`;
CREATE TABLE IF NOT EXISTS `caracteristicas` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint UNSIGNED DEFAULT NULL,
  `nombre` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `estado` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `caracteristicas_empresa_id_foreign` (`empresa_id`)
) ENGINE=MyISAM AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `caracteristicas`
--

INSERT INTO `caracteristicas` (`id`, `empresa_id`, `nombre`, `descripcion`, `estado`, `created_at`, `updated_at`) VALUES
(1, 1, 'Bebidas', NULL, 1, '2026-02-06 03:05:33', '2026-02-06 03:05:33'),
(2, 1, 'Snacks', NULL, 1, '2026-02-06 03:05:33', '2026-02-06 03:05:33'),
(3, 1, 'Unidad', NULL, 1, '2026-02-06 03:05:33', '2026-02-06 03:05:33'),
(4, 1, 'Confitería', NULL, 1, '2026-02-06 03:11:44', '2026-02-06 03:11:44'),
(5, 1, 'comida', 'Categoría de POS: comida', 1, '2026-02-07 16:51:15', '2026-02-07 16:51:15'),
(6, 1, 'bebidas calientes', 'Categoría de POS: bebidas calientes', 1, '2026-02-07 16:51:15', '2026-02-07 16:51:15'),
(7, 1, 'licores y vinos', 'Categoría de POS: licores y vinos', 1, '2026-02-07 16:51:15', '2026-02-07 16:51:15'),
(8, 1, 'postres', 'Categoría de POS: postres', 1, '2026-02-07 16:51:15', '2026-02-07 16:51:15'),
(9, 1, 'Entradas', NULL, 1, '2026-02-09 17:34:31', '2026-02-09 17:34:31'),
(10, 1, 'Cine Paraíso', NULL, 1, '2026-02-09 17:34:31', '2026-02-09 17:34:31'),
(11, NULL, 'Comida', 'Alimentos preparados', 1, '2026-02-10 18:54:04', '2026-02-10 18:54:04'),
(12, NULL, 'Bebidas', 'Bebidas frías y calientes', 1, '2026-02-10 18:54:05', '2026-02-10 18:54:05'),
(13, NULL, 'Tragos', 'Bebidas alcohólicas', 1, '2026-02-10 18:54:05', '2026-02-10 18:54:05'),
(14, NULL, 'Casa', 'Productos de la casa', 1, '2026-02-10 18:54:05', '2026-02-10 18:54:05'),
(15, NULL, 'Unidad', 'Unidad individual', 1, '2026-02-10 18:54:05', '2026-02-10 18:54:05'),
(16, NULL, 'Comida', 'Alimentos preparados', 1, '2026-02-10 18:55:08', '2026-02-10 18:55:08'),
(17, NULL, 'Bebidas', 'Bebidas frías y calientes', 1, '2026-02-10 18:55:08', '2026-02-10 18:55:08'),
(18, NULL, 'Tragos', 'Bebidas alcohólicas', 1, '2026-02-10 18:55:08', '2026-02-10 18:55:08'),
(19, NULL, 'Casa', 'Productos de la casa', 1, '2026-02-10 18:55:08', '2026-02-10 18:55:08'),
(20, NULL, 'Unidad', 'Unidad individual', 1, '2026-02-10 18:55:08', '2026-02-10 18:55:08'),
(21, NULL, 'Comida', 'Alimentos preparados', 1, '2026-02-10 18:58:58', '2026-02-10 18:58:58'),
(22, NULL, 'Bebidas', 'Bebidas frías y calientes', 1, '2026-02-10 18:58:58', '2026-02-10 18:58:58'),
(23, NULL, 'Tragos', 'Bebidas alcohólicas', 1, '2026-02-10 18:58:58', '2026-02-10 18:58:58'),
(24, NULL, 'Casa', 'Productos de la casa', 1, '2026-02-10 18:58:58', '2026-02-10 18:58:58'),
(25, NULL, 'Unidad', 'Unidad individual', 1, '2026-02-10 18:58:58', '2026-02-10 18:58:58'),
(26, NULL, 'Comida', 'Alimentos preparados', 1, '2026-02-10 19:16:29', '2026-02-10 19:16:29'),
(27, NULL, 'Bebidas', 'Bebidas frías y calientes', 1, '2026-02-10 19:16:29', '2026-02-10 19:16:29'),
(28, NULL, 'Tragos', 'Bebidas alcohólicas', 1, '2026-02-10 19:16:29', '2026-02-10 19:16:29'),
(29, NULL, 'Casa', 'Productos de la casa', 1, '2026-02-10 19:16:29', '2026-02-10 19:16:29'),
(30, NULL, 'Unidad', 'Unidad individual', 1, '2026-02-10 19:16:30', '2026-02-10 19:16:30'),
(31, 1, 'comidas', 'Alimentos preparados', 1, '2026-02-10 20:40:21', '2026-02-14 18:28:57'),
(32, 1, 'bebidas', 'Bebidas frías y calientes', 1, '2026-02-10 20:40:21', '2026-02-14 18:28:57'),
(33, 1, 'tragos o cocteles', 'Bebidas alcohólicas', 1, '2026-02-10 20:40:21', '2026-02-14 18:28:57'),
(34, NULL, 'Casa', 'Productos de la casa', 1, '2026-02-10 20:40:21', '2026-02-10 20:40:21'),
(35, NULL, 'Unidad', 'Unidad individual', 1, '2026-02-10 20:40:21', '2026-02-10 20:40:21'),
(36, 1, 'Generica', NULL, 1, '2026-02-10 21:37:01', '2026-02-10 21:37:01'),
(37, NULL, 'Comida', 'Alimentos preparados', 1, '2026-02-16 16:01:31', '2026-02-16 16:01:31'),
(38, NULL, 'Bebidas', 'Bebidas frías y calientes', 1, '2026-02-16 16:01:31', '2026-02-16 16:01:31'),
(39, NULL, 'Tragos', 'Bebidas alcohólicas', 1, '2026-02-16 16:01:31', '2026-02-16 16:01:31'),
(40, NULL, 'Casa', 'Productos de la casa', 1, '2026-02-16 16:01:31', '2026-02-16 16:01:31'),
(41, NULL, 'Unidad', 'Unidad individual', 1, '2026-02-16 16:01:31', '2026-02-16 16:01:31'),
(42, 1, 'Tragos', NULL, 1, '2026-02-16 22:40:50', '2026-02-16 22:40:50');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

DROP TABLE IF EXISTS `categorias`;
CREATE TABLE IF NOT EXISTS `categorias` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint UNSIGNED DEFAULT NULL,
  `caracteristica_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categorias_caracteristica_id_unique` (`caracteristica_id`),
  KEY `categorias_empresa_id_foreign` (`empresa_id`)
) ENGINE=MyISAM AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id`, `empresa_id`, `caracteristica_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2026-02-06 03:05:33', '2026-02-06 03:05:33'),
(2, 1, 2, '2026-02-06 03:05:33', '2026-02-06 03:05:33'),
(3, 1, 4, '2026-02-06 03:11:44', '2026-02-06 03:11:44'),
(4, 1, 5, '2026-02-07 16:51:15', '2026-02-07 16:51:15'),
(5, 1, 6, '2026-02-07 16:51:15', '2026-02-07 16:51:15'),
(6, 1, 7, '2026-02-07 16:51:15', '2026-02-07 16:51:15'),
(7, 1, 8, '2026-02-07 16:51:15', '2026-02-07 16:51:15'),
(8, 1, 9, '2026-02-09 17:34:31', '2026-02-09 17:34:31'),
(11, 1, 13, '2026-02-10 18:54:05', '2026-02-10 18:54:05'),
(21, 1, 31, '2026-02-10 20:40:21', '2026-02-10 20:40:21'),
(23, 1, 33, '2026-02-10 20:40:21', '2026-02-10 20:40:21');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

DROP TABLE IF EXISTS `clientes`;
CREATE TABLE IF NOT EXISTS `clientes` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint UNSIGNED NOT NULL,
  `persona_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `clientes_persona_id_unique` (`persona_id`),
  KEY `clientes_empresa_id_index` (`empresa_id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id`, `empresa_id`, `persona_id`, `created_at`, `updated_at`) VALUES
(1, 3, 1, '2026-02-04 03:24:40', '2026-02-04 03:24:40'),
(2, 5, 2, '2026-02-04 03:24:40', '2026-02-04 03:24:40'),
(3, 11, 3, '2026-02-04 03:24:41', '2026-02-04 03:24:41'),
(4, 13, 4, '2026-02-04 03:24:41', '2026-02-04 03:24:41'),
(5, 15, 5, '2026-02-04 03:24:41', '2026-02-04 03:24:41'),
(6, 17, 6, '2026-02-04 03:24:41', '2026-02-04 03:24:41'),
(7, 19, 7, '2026-02-04 03:24:41', '2026-02-04 03:24:41'),
(8, 21, 8, '2026-02-04 03:24:41', '2026-02-04 03:24:41'),
(9, 23, 9, '2026-02-04 03:24:41', '2026-02-04 03:24:41'),
(10, 25, 10, '2026-02-04 03:24:41', '2026-02-04 03:24:41'),
(11, 1, 11, '2026-02-10 02:48:00', '2026-02-10 02:48:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compras`
--

DROP TABLE IF EXISTS `compras`;
CREATE TABLE IF NOT EXISTS `compras` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `comprobante_id` bigint UNSIGNED NOT NULL,
  `proveedore_id` bigint UNSIGNED NOT NULL,
  `numero_comprobante` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comprobante_path` varchar(2048) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `metodo_pago` enum('EFECTIVO','TARJETA') COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_hora` datetime NOT NULL,
  `impuesto` decimal(8,2) UNSIGNED NOT NULL,
  `subtotal` decimal(8,2) UNSIGNED NOT NULL,
  `total` decimal(8,2) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `compras_numero_comprobante_unique` (`numero_comprobante`),
  KEY `compras_user_id_foreign` (`user_id`),
  KEY `compras_comprobante_id_foreign` (`comprobante_id`),
  KEY `compras_proveedore_id_foreign` (`proveedore_id`),
  KEY `compras_empresa_id_fecha_hora_index` (`empresa_id`,`fecha_hora`),
  KEY `idx_compras_empresa_fecha` (`empresa_id`,`created_at`),
  KEY `idx_compras_empresa_proveedor` (`empresa_id`,`proveedore_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compra_producto`
--

DROP TABLE IF EXISTS `compra_producto`;
CREATE TABLE IF NOT EXISTS `compra_producto` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `compra_id` bigint UNSIGNED NOT NULL,
  `producto_id` bigint UNSIGNED NOT NULL,
  `cantidad` int UNSIGNED NOT NULL,
  `precio_compra` decimal(10,2) NOT NULL,
  `fecha_vencimiento` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `compra_producto_compra_id_foreign` (`compra_id`),
  KEY `compra_producto_producto_id_foreign` (`producto_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comprobantes`
--

DROP TABLE IF EXISTS `comprobantes`;
CREATE TABLE IF NOT EXISTS `comprobantes` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `comprobantes`
--

INSERT INTO `comprobantes` (`id`, `nombre`, `created_at`, `updated_at`) VALUES
(1, 'Factura', '2026-02-04 03:24:40', '2026-02-04 03:24:40'),
(2, 'Factura', '2026-02-04 03:24:40', '2026-02-04 03:24:40'),
(3, 'Factura', '2026-02-04 03:24:41', '2026-02-04 03:24:41'),
(4, 'Factura', '2026-02-04 03:24:41', '2026-02-04 03:24:41'),
(5, 'Factura', '2026-02-04 03:24:41', '2026-02-04 03:24:41'),
(6, 'Factura', '2026-02-04 03:24:41', '2026-02-04 03:24:41'),
(7, 'Factura', '2026-02-04 03:24:41', '2026-02-04 03:24:41'),
(8, 'Factura', '2026-02-04 03:24:41', '2026-02-04 03:24:41'),
(9, 'Factura', '2026-02-04 03:24:41', '2026-02-04 03:24:41'),
(10, 'Factura', '2026-02-04 03:24:41', '2026-02-04 03:24:41');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion_numeracion`
--

DROP TABLE IF EXISTS `configuracion_numeracion`;
CREATE TABLE IF NOT EXISTS `configuracion_numeracion` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint UNSIGNED NOT NULL,
  `tipo` enum('DE','FE') COLLATE utf8mb4_unicode_ci NOT NULL,
  `prefijo` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `consecutivo_actual` int NOT NULL DEFAULT '0',
  `consecutivo_inicial` int NOT NULL DEFAULT '1',
  `consecutivo_final` int DEFAULT NULL COMMENT 'Solo para FE con resolución DIAN',
  `resolucion_numero` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `resolucion_fecha` date DEFAULT NULL,
  `resolucion_fecha_inicio` date DEFAULT NULL,
  `resolucion_fecha_fin` date DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `notas` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `configuracion_numeracion_empresa_id_tipo_prefijo_unique` (`empresa_id`,`tipo`,`prefijo`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `configuracion_numeracion`
--

INSERT INTO `configuracion_numeracion` (`id`, `empresa_id`, `tipo`, `prefijo`, `consecutivo_actual`, `consecutivo_inicial`, `consecutivo_final`, `resolucion_numero`, `resolucion_fecha`, `resolucion_fecha_inicio`, `resolucion_fecha_fin`, `activo`, `notas`, `created_at`, `updated_at`) VALUES
(1, 1, 'DE', 'POS001', 9, 1, NULL, NULL, NULL, NULL, NULL, 1, 'Numeración para Documentos Equivalentes (POS)', '2026-02-19 18:11:38', '2026-02-19 21:02:49'),
(2, 1, 'FE', 'FE01', 2, 1, 5000, '18764123456789', '2025-01-15', '2025-01-15', '2026-01-15', 1, 'Resolución DIAN para Facturación Electrónica', '2026-02-19 18:11:39', '2026-02-19 20:56:26');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `devoluciones`
--

DROP TABLE IF EXISTS `devoluciones`;
CREATE TABLE IF NOT EXISTS `devoluciones` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint UNSIGNED NOT NULL,
  `venta_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `tipo` enum('BOLETERIA','CONFITERIA','MIXTA') COLLATE utf8mb4_unicode_ci NOT NULL,
  `monto_devuelto` decimal(10,2) NOT NULL,
  `motivo` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `es_excepcional` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'true = devolución de días pasados, solo Root',
  `autorizacion_nota` text COLLATE utf8mb4_unicode_ci COMMENT 'Nota de autorización para casos excepcionales',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `devoluciones_venta_id_foreign` (`venta_id`),
  KEY `devoluciones_user_id_foreign` (`user_id`),
  KEY `devoluciones_empresa_id_venta_id_index` (`empresa_id`,`venta_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `devolucion_items`
--

DROP TABLE IF EXISTS `devolucion_items`;
CREATE TABLE IF NOT EXISTS `devolucion_items` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `devolucion_id` bigint UNSIGNED NOT NULL,
  `tipo_item` enum('BOLETO','PRODUCTO') COLLATE utf8mb4_unicode_ci NOT NULL,
  `funcion_asiento_id` bigint UNSIGNED DEFAULT NULL,
  `producto_id` bigint UNSIGNED DEFAULT NULL,
  `cantidad` decimal(10,2) NOT NULL DEFAULT '1.00',
  `monto` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `devolucion_items_devolucion_id_foreign` (`devolucion_id`),
  KEY `devolucion_items_funcion_asiento_id_foreign` (`funcion_asiento_id`),
  KEY `devolucion_items_producto_id_foreign` (`producto_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `distribuidores`
--

DROP TABLE IF EXISTS `distribuidores`;
CREATE TABLE IF NOT EXISTS `distribuidores` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint UNSIGNED NOT NULL,
  `nombre` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contacto` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notas` text COLLATE utf8mb4_unicode_ci,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `distribuidores_empresa_id_foreign` (`empresa_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `distribuidores`
--

INSERT INTO `distribuidores` (`id`, `empresa_id`, `nombre`, `contacto`, `telefono`, `email`, `notas`, `activo`, `created_at`, `updated_at`) VALUES
(1, 1, 'Warner Bros Test', 'Test Contact', NULL, NULL, NULL, 1, '2026-02-07 16:27:28', '2026-02-07 16:27:28'),
(2, 1, 'BABILLA CINE SAS', 'XXX', '31522222', NULL, NULL, 1, '2026-02-17 13:31:32', '2026-02-17 13:31:32');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `documentos`
--

DROP TABLE IF EXISTS `documentos`;
CREATE TABLE IF NOT EXISTS `documentos` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `documentos`
--

INSERT INTO `documentos` (`id`, `nombre`, `created_at`, `updated_at`) VALUES
(1, 'DNI', '2026-02-04 03:24:40', '2026-02-04 03:24:40'),
(2, 'DNI', '2026-02-04 03:24:40', '2026-02-04 03:24:40'),
(3, 'DNI', '2026-02-04 03:24:41', '2026-02-04 03:24:41'),
(4, 'DNI', '2026-02-04 03:24:41', '2026-02-04 03:24:41'),
(5, 'DNI', '2026-02-04 03:24:41', '2026-02-04 03:24:41'),
(6, 'DNI', '2026-02-04 03:24:41', '2026-02-04 03:24:41'),
(7, 'DNI', '2026-02-04 03:24:41', '2026-02-04 03:24:41'),
(8, 'DNI', '2026-02-04 03:24:41', '2026-02-04 03:24:41'),
(9, 'DNI', '2026-02-04 03:24:41', '2026-02-04 03:24:41'),
(10, 'DNI', '2026-02-04 03:24:41', '2026-02-04 03:24:41');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `documentos_fiscales`
--

DROP TABLE IF EXISTS `documentos_fiscales`;
CREATE TABLE IF NOT EXISTS `documentos_fiscales` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint UNSIGNED NOT NULL,
  `venta_id` bigint UNSIGNED DEFAULT NULL,
  `tipo_documento` enum('DE','FE') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'DE=Documento Equivalente, FE=Factura Electrónica',
  `prefijo` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `numero` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `numero_completo` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` enum('borrador','pendiente_emision','emitido','enviado','aceptado','rechazado','contingencia','contingencia_permanente','enviado_posterior') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'borrador',
  `cufe` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Código Único de Factura Electrónica',
  `cude` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Código Único de Documento Equivalente',
  `qr_code` text COLLATE utf8mb4_unicode_ci,
  `xml_path` text COLLATE utf8mb4_unicode_ci,
  `pdf_path` text COLLATE utf8mb4_unicode_ci,
  `cliente_tipo_documento` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'CC',
  `cliente_documento` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cliente_nombre` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Consumidor Final',
  `cliente_email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cliente_telefono` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cliente_direccion` text COLLATE utf8mb4_unicode_ci,
  `subtotal` decimal(10,2) NOT NULL DEFAULT '0.00',
  `impuesto_inc` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Impuesto Nacional al Consumo 8%',
  `otros_impuestos` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total` decimal(10,2) NOT NULL DEFAULT '0.00',
  `respuesta_proveedor` text COLLATE utf8mb4_unicode_ci,
  `mensaje_error` text COLLATE utf8mb4_unicode_ci,
  `intentos_envio` int NOT NULL DEFAULT '0',
  `fecha_emision` timestamp NULL DEFAULT NULL,
  `fecha_aceptacion_dian` timestamp NULL DEFAULT NULL,
  `es_contingencia` tinyint(1) NOT NULL DEFAULT '0',
  `motivo_contingencia` text COLLATE utf8mb4_unicode_ci,
  `fecha_contingencia` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `documentos_fiscales_numero_completo_unique` (`numero_completo`),
  KEY `documentos_fiscales_venta_id_foreign` (`venta_id`),
  KEY `documentos_fiscales_empresa_id_tipo_documento_estado_index` (`empresa_id`,`tipo_documento`,`estado`),
  KEY `documentos_fiscales_numero_completo_index` (`numero_completo`),
  KEY `documentos_fiscales_created_at_index` (`created_at`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `documentos_fiscales`
--

INSERT INTO `documentos_fiscales` (`id`, `empresa_id`, `venta_id`, `tipo_documento`, `prefijo`, `numero`, `numero_completo`, `estado`, `cufe`, `cude`, `qr_code`, `xml_path`, `pdf_path`, `cliente_tipo_documento`, `cliente_documento`, `cliente_nombre`, `cliente_email`, `cliente_telefono`, `cliente_direccion`, `subtotal`, `impuesto_inc`, `otros_impuestos`, `total`, `respuesta_proveedor`, `mensaje_error`, `intentos_envio`, `fecha_emision`, `fecha_aceptacion_dian`, `es_contingencia`, `motivo_contingencia`, `fecha_contingencia`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'DE', 'REC', '00001', 'REC-20260220-00001', 'pendiente_emision', NULL, NULL, NULL, NULL, NULL, 'CC', NULL, 'Consumidor Final', NULL, NULL, NULL, 213518.52, 7481.48, 0.00, 221000.00, NULL, NULL, 0, NULL, NULL, 0, NULL, NULL, '2026-02-20 16:14:23', '2026-02-20 16:14:23'),
(2, 1, 2, 'DE', 'REC', '00002', 'REC-20260220-00002', 'pendiente_emision', NULL, NULL, NULL, NULL, NULL, 'CC', NULL, 'Consumidor Final', NULL, NULL, NULL, 84092.59, 1927.41, 0.00, 86020.00, NULL, NULL, 0, NULL, NULL, 0, NULL, NULL, '2026-02-20 17:32:35', '2026-02-20 17:32:35'),
(3, 1, 3, 'DE', 'REC', '00003', 'REC-20260220-00003', 'pendiente_emision', NULL, NULL, NULL, NULL, NULL, 'CC', NULL, 'Consumidor Final', NULL, NULL, NULL, 43607.41, 3488.59, 0.00, 47096.00, NULL, NULL, 0, NULL, NULL, 0, NULL, NULL, '2026-02-20 17:33:35', '2026-02-20 17:33:35'),
(4, 1, 4, 'DE', 'REC', '00004', 'REC-20260220-00004', 'pendiente_emision', NULL, NULL, NULL, NULL, NULL, 'CC', NULL, 'Consumidor Final', NULL, NULL, NULL, 30000.00, 0.00, 0.00, 30000.00, NULL, NULL, 0, NULL, NULL, 0, NULL, NULL, '2026-02-20 18:11:02', '2026-02-20 18:11:02'),
(5, 1, 5, 'DE', 'REC', '00005', 'REC-20260220-00005', 'pendiente_emision', NULL, NULL, NULL, NULL, NULL, 'CC', NULL, 'Consumidor Final', NULL, NULL, NULL, 3500.00, 280.00, 0.00, 3780.00, NULL, NULL, 0, NULL, NULL, 0, NULL, NULL, '2026-02-21 00:01:22', '2026-02-21 00:01:22'),
(6, 1, 6, 'DE', 'REC', '00006', 'REC-20260220-00006', 'pendiente_emision', NULL, NULL, NULL, NULL, NULL, 'CC', NULL, 'Consumidor Final', NULL, NULL, NULL, 139629.63, 6370.37, 0.00, 146000.00, NULL, NULL, 0, NULL, NULL, 0, NULL, NULL, '2026-02-21 01:36:39', '2026-02-21 01:36:39'),
(7, 1, 7, 'FE', 'REC', '00007', 'REC-20260220-00007', 'pendiente_emision', NULL, NULL, NULL, NULL, NULL, 'CC', NULL, 'Consumidor Final', NULL, NULL, NULL, 472685.19, 37814.81, 0.00, 510500.00, NULL, NULL, 0, NULL, NULL, 0, NULL, NULL, '2026-02-21 01:57:49', '2026-02-21 01:57:49'),
(8, 1, 8, 'DE', 'REC', '00008', 'REC-20260220-00008', 'pendiente_emision', NULL, NULL, NULL, NULL, NULL, 'CC', NULL, 'Consumidor Final', NULL, NULL, NULL, 34259.26, 2740.74, 0.00, 37000.00, NULL, NULL, 0, NULL, NULL, 0, NULL, NULL, '2026-02-21 02:00:48', '2026-02-21 02:00:48'),
(9, 1, 9, 'FE', 'REC', '00009', 'REC-20260220-00009', 'pendiente_emision', NULL, NULL, NULL, NULL, NULL, 'CC', NULL, 'Consumidor Final', NULL, NULL, NULL, 472685.19, 37814.81, 0.00, 510500.00, NULL, NULL, 0, NULL, NULL, 0, NULL, NULL, '2026-02-21 02:09:07', '2026-02-21 02:09:07'),
(10, 1, 20, 'DE', 'REC', '00020', 'REC-20260221-00020', 'pendiente_emision', NULL, NULL, NULL, NULL, NULL, 'CC', NULL, 'Consumidor Final', NULL, NULL, NULL, 34259.00, 2741.00, 0.00, 37000.00, NULL, NULL, 0, NULL, NULL, 0, NULL, NULL, '2026-02-21 17:37:43', '2026-02-21 17:37:43'),
(11, 1, 21, 'DE', 'REC', '00021', 'REC-20260221-00021', 'pendiente_emision', NULL, NULL, NULL, NULL, NULL, 'CC', NULL, 'Consumidor Final', NULL, NULL, NULL, 95907.00, 2873.00, 0.00, 98780.00, NULL, NULL, 0, NULL, NULL, 0, NULL, NULL, '2026-02-21 19:09:36', '2026-02-21 19:09:36'),
(12, 1, 22, 'DE', 'REC', '00022', 'REC-20260221-00022', 'pendiente_emision', NULL, NULL, NULL, NULL, NULL, 'CC', NULL, 'Consumidor Final', NULL, NULL, NULL, 34259.00, 2741.00, 0.00, 37000.00, NULL, NULL, 0, NULL, NULL, 0, NULL, NULL, '2026-02-21 19:11:14', '2026-02-21 19:11:14'),
(13, 1, 23, 'DE', 'REC', '00023', 'REC-20260221-00023', 'pendiente_emision', NULL, NULL, NULL, NULL, NULL, 'CC', NULL, 'Consumidor Final', NULL, NULL, NULL, 7870.00, 630.00, 0.00, 8500.00, NULL, NULL, 0, NULL, NULL, 0, NULL, NULL, '2026-02-21 22:02:06', '2026-02-21 22:02:06');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `documento_fiscal_lineas`
--

DROP TABLE IF EXISTS `documento_fiscal_lineas`;
CREATE TABLE IF NOT EXISTS `documento_fiscal_lineas` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `documento_fiscal_id` bigint UNSIGNED NOT NULL,
  `linea` int NOT NULL DEFAULT '1',
  `tipo_item` enum('BOLETO','PRODUCTO','SERVICIO') COLLATE utf8mb4_unicode_ci NOT NULL,
  `codigo` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `descripcion` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cantidad` decimal(10,2) NOT NULL DEFAULT '1.00',
  `precio_unitario` decimal(10,2) NOT NULL,
  `subtotal_linea` decimal(10,2) NOT NULL,
  `aplica_inc` tinyint(1) NOT NULL DEFAULT '0',
  `valor_inc` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_linea` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `documento_fiscal_lineas_documento_fiscal_id_foreign` (`documento_fiscal_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empleados`
--

DROP TABLE IF EXISTS `empleados`;
CREATE TABLE IF NOT EXISTS `empleados` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint UNSIGNED NOT NULL,
  `razon_social` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cargo` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `img_path` varchar(2048) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `empleados_empresa_id_foreign` (`empresa_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empresa`
--

DROP TABLE IF EXISTS `empresa`;
CREATE TABLE IF NOT EXISTS `empresa` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `plan_id` bigint UNSIGNED DEFAULT NULL,
  `stripe_subscription_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `stripe_customer_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado_suscripcion` enum('active','cancelled','past_due','trial') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `fecha_proximo_pago` timestamp NULL DEFAULT NULL,
  `fecha_vencimiento_suscripcion` timestamp NULL DEFAULT NULL,
  `tarifa_servicio_porcentaje` decimal(5,2) NOT NULL DEFAULT '2.50',
  `tarifa_servicio_monto` decimal(15,2) NOT NULL DEFAULT '0.00',
  `estado` enum('activa','suspendida') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'activa',
  `fecha_onboarding_completado` timestamp NULL DEFAULT NULL,
  `nombre` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `propietario` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ruc` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `stripe_account_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Stripe Connected Account ID (acct_...) para Split Payments',
  `stripe_connect_status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'NOT_STARTED' COMMENT 'Estado del onboarding: NOT_STARTED|PENDING|ACTIVE|REJECTED|UNDER_REVIEW',
  `stripe_onboarding_url` text COLLATE utf8mb4_unicode_ci COMMENT 'URL para que el usuario complete el onboarding de Stripe Connect',
  `stripe_connect_updated_at` timestamp NULL DEFAULT NULL COMMENT 'Última fecha de actualización del estado de Stripe Connect',
  `porcentaje_impuesto` int UNSIGNED NOT NULL,
  `gastos_indirectos_porcentaje` decimal(5,2) NOT NULL DEFAULT '0.00',
  `merma_esperada_porcentaje` decimal(5,2) NOT NULL DEFAULT '0.00',
  `abreviatura_impuesto` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `direccion` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `correo` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ubicacion` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `moneda_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `empresa_stripe_account_id_unique` (`stripe_account_id`),
  UNIQUE KEY `empresa_stripe_subscription_id_unique` (`stripe_subscription_id`),
  KEY `empresa_stripe_account_id_index` (`stripe_account_id`),
  KEY `empresa_stripe_connect_status_index` (`stripe_connect_status`),
  KEY `empresa_plan_id_index` (`plan_id`),
  KEY `empresa_estado_suscripcion_index` (`estado_suscripcion`),
  KEY `empresa_estado_index` (`estado`)
) ENGINE=MyISAM AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `empresa`
--

INSERT INTO `empresa` (`id`, `plan_id`, `stripe_subscription_id`, `stripe_customer_id`, `estado_suscripcion`, `fecha_proximo_pago`, `fecha_vencimiento_suscripcion`, `tarifa_servicio_porcentaje`, `tarifa_servicio_monto`, `estado`, `fecha_onboarding_completado`, `nombre`, `propietario`, `ruc`, `stripe_account_id`, `stripe_connect_status`, `stripe_onboarding_url`, `stripe_connect_updated_at`, `porcentaje_impuesto`, `gastos_indirectos_porcentaje`, `merma_esperada_porcentaje`, `abreviatura_impuesto`, `direccion`, `correo`, `telefono`, `ubicacion`, `moneda_id`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, NULL, 'active', NULL, NULL, 2.50, 0.00, 'activa', NULL, 'Cinema Paraíso', 'Dr. Emory Jast', '7823263467', NULL, 'NOT_STARTED', NULL, NULL, 19, 0.00, 0.00, 'IVA', '4148 Kunde Village\nPowlowskiborough, WV 04175', 'steuber.earl@kuphal.com', '+1-941-834-8322', NULL, 1, '2026-02-04 03:24:40', '2026-02-06 02:39:26'),
(2, 1, NULL, NULL, 'trial', NULL, NULL, 2.50, 0.00, 'activa', NULL, 'Cine B', 'Mrs. Nyah Kshlerin', '5374943138', NULL, 'NOT_STARTED', NULL, NULL, 19, 0.00, 0.00, 'IVA', '387 Gracie Manor\nBennyberg, NV 46096', 'gdonnelly@ortiz.org', '507-770-8527', NULL, 1, '2026-02-04 03:24:40', '2026-02-04 03:24:40'),
(3, 1, NULL, NULL, 'trial', NULL, NULL, 2.50, 0.00, 'activa', NULL, 'Lemke LLC', 'Mrs. Earline Goodwin', '5504388440', NULL, 'NOT_STARTED', NULL, NULL, 19, 0.00, 0.00, 'IVA', '7811 Kunze Coves Suite 089\nShanahanhaven, AK 42373-1919', 'elwyn.batz@oreilly.com', '629.813.6718', NULL, 1, '2026-02-04 03:24:40', '2026-02-04 03:24:40'),
(4, 1, NULL, NULL, 'trial', NULL, NULL, 2.50, 0.00, 'activa', NULL, 'Schimmel Inc', 'Eulah Bailey I', '6350645190', NULL, 'NOT_STARTED', NULL, NULL, 19, 0.00, 0.00, 'IVA', '44057 Wilderman Junctions Apt. 684\nNorth Sigrid, DC 99633-7754', 'elda38@braun.org', '+1 (559) 599-1030', NULL, 1, '2026-02-04 03:24:40', '2026-02-04 03:24:40'),
(5, 1, NULL, NULL, 'trial', NULL, NULL, 2.50, 0.00, 'activa', NULL, 'Erdman LLC', 'Melvin Feest MD', '2103872963', NULL, 'NOT_STARTED', NULL, NULL, 19, 0.00, 0.00, 'IVA', '55466 Kiehn Manor\nLake Marjory, MN 21340', 'dlangworth@wyman.com', '1-567-406-8915', NULL, 1, '2026-02-04 03:24:40', '2026-02-04 03:24:40'),
(6, 1, NULL, NULL, 'trial', NULL, NULL, 2.50, 0.00, 'activa', NULL, 'Hand Group', 'Ettie Glover', '5718490860', NULL, 'NOT_STARTED', NULL, NULL, 19, 0.00, 0.00, 'IVA', '12126 Jayda Expressway\nPaultown, MS 72590-9208', 'cwunsch@maggio.info', '+1-609-819-5593', NULL, 1, '2026-02-04 03:24:40', '2026-02-04 03:24:40'),
(7, 1, NULL, NULL, 'trial', NULL, NULL, 2.50, 0.00, 'activa', NULL, 'Goldner-Price', 'Miss Maddison Koss III', '5819243928', NULL, 'NOT_STARTED', NULL, NULL, 19, 0.00, 0.00, 'IVA', '320 Alfonzo Underpass Apt. 114\nIsaiahberg, WV 44979', 'williamson.marjory@fadel.com', '1-917-962-9278', NULL, 1, '2026-02-04 03:24:41', '2026-02-04 03:24:41'),
(8, 1, NULL, NULL, 'trial', NULL, NULL, 2.50, 0.00, 'activa', NULL, 'Waters Group', 'Olin Rutherford', '4492740636', NULL, 'NOT_STARTED', NULL, NULL, 19, 0.00, 0.00, 'IVA', '8862 Goldner Estates\nSouth Jared, MT 19306', 'homenick.alexandrea@wintheiser.com', '435.681.1065', NULL, 1, '2026-02-04 03:24:41', '2026-02-04 03:24:41'),
(9, 1, NULL, NULL, 'trial', NULL, NULL, 2.50, 0.00, 'activa', NULL, 'Herzog, Barrows and Harvey', 'Josh Heidenreich', '2610457262', NULL, 'NOT_STARTED', NULL, NULL, 19, 0.00, 0.00, 'IVA', '80417 Mohr Landing\nCielomouth, AZ 75374', 'rherzog@wunsch.com', '1-231-289-3463', NULL, 1, '2026-02-04 03:24:41', '2026-02-04 03:24:41'),
(10, 1, NULL, NULL, 'trial', NULL, NULL, 2.50, 0.00, 'activa', NULL, 'Rodriguez PLC', 'Asia Weimann', '6138264508', NULL, 'NOT_STARTED', NULL, NULL, 19, 0.00, 0.00, 'IVA', '3736 Madeline Creek\nWest Earline, UT 35392', 'bayer.charlene@pagac.com', '380.956.0702', NULL, 1, '2026-02-04 03:24:41', '2026-02-04 03:24:41'),
(11, 1, NULL, NULL, 'trial', NULL, NULL, 2.50, 0.00, 'activa', NULL, 'Lehner, Barton and Ullrich', 'Micheal Keeling', '2424991298', NULL, 'NOT_STARTED', NULL, NULL, 19, 0.00, 0.00, 'IVA', '54884 Nitzsche Well\nNew Demarco, NY 37300', 'lonny73@ledner.com', '1-762-884-1510', NULL, 1, '2026-02-04 03:24:41', '2026-02-04 03:24:41'),
(12, 1, NULL, NULL, 'trial', NULL, NULL, 2.50, 0.00, 'activa', NULL, 'Olson, Sporer and Bechtelar', 'Twila Torp II', '0085454090', NULL, 'NOT_STARTED', NULL, NULL, 19, 0.00, 0.00, 'IVA', '205 Spencer Drive Suite 648\nNorth Nakiaview, VT 92061-4328', 'marquardt.jeanie@flatley.com', '+1-651-212-1985', NULL, 1, '2026-02-04 03:24:41', '2026-02-04 03:24:41'),
(13, 1, NULL, NULL, 'trial', NULL, NULL, 2.50, 0.00, 'activa', NULL, 'Walsh, Pfeffer and Daniel', 'Merlin Hamill', '4953668036', NULL, 'NOT_STARTED', NULL, NULL, 19, 0.00, 0.00, 'IVA', '6011 Vernice Rapids Apt. 883\nAlysonborough, PA 19364-1034', 'ernest66@terry.com', '(458) 664-6317', NULL, 1, '2026-02-04 03:24:41', '2026-02-04 03:24:41'),
(14, 1, NULL, NULL, 'trial', NULL, NULL, 2.50, 0.00, 'activa', NULL, 'Dietrich-Schmitt', 'Mrs. Leda Kuvalis', '8690429839', NULL, 'NOT_STARTED', NULL, NULL, 19, 0.00, 0.00, 'IVA', '63081 Kuphal Extension Apt. 119\nNorth Verliefurt, MI 12281', 'sadie75@lemke.com', '817-585-8658', NULL, 1, '2026-02-04 03:24:41', '2026-02-04 03:24:41'),
(15, 1, NULL, NULL, 'trial', NULL, NULL, 2.50, 0.00, 'activa', NULL, 'Torphy, Morissette and Larkin', 'Pierce Smitham', '6494491779', NULL, 'NOT_STARTED', NULL, NULL, 19, 0.00, 0.00, 'IVA', '401 Lowe Pine\nCharliestad, IL 61306', 'nkassulke@dietrich.info', '865-442-7480', NULL, 1, '2026-02-04 03:24:41', '2026-02-04 03:24:41'),
(16, 1, NULL, NULL, 'trial', NULL, NULL, 2.50, 0.00, 'activa', NULL, 'Wintheiser, Balistreri and Daugherty', 'Mr. Merl Kulas', '4915313647', NULL, 'NOT_STARTED', NULL, NULL, 19, 0.00, 0.00, 'IVA', '25377 August Crossroad\nErickaland, CO 45798', 'ukeebler@barton.com', '(954) 354-3863', NULL, 1, '2026-02-04 03:24:41', '2026-02-04 03:24:41'),
(17, 1, NULL, NULL, 'trial', NULL, NULL, 2.50, 0.00, 'activa', NULL, 'McCullough Inc', 'Violette Erdman', '6223042322', NULL, 'NOT_STARTED', NULL, NULL, 19, 0.00, 0.00, 'IVA', '46380 Morissette Divide Suite 633\nVonRuedenchester, WY 62436-0612', 'millie21@robel.biz', '+1 (351) 467-5400', NULL, 1, '2026-02-04 03:24:41', '2026-02-04 03:24:41'),
(18, 1, NULL, NULL, 'trial', NULL, NULL, 2.50, 0.00, 'activa', NULL, 'Hills LLC', 'Dr. Taylor Wehner III', '8947342248', NULL, 'NOT_STARTED', NULL, NULL, 19, 0.00, 0.00, 'IVA', '56014 Reilly Locks Suite 893\nOkunevatown, KS 04038', 'tom.hansen@hegmann.com', '(628) 498-2312', NULL, 1, '2026-02-04 03:24:41', '2026-02-04 03:24:41'),
(19, 1, NULL, NULL, 'trial', NULL, NULL, 2.50, 0.00, 'activa', NULL, 'Quigley and Sons', 'Joshua Braun', '9722182500', NULL, 'NOT_STARTED', NULL, NULL, 19, 0.00, 0.00, 'IVA', '713 Hahn Circles Apt. 765\nYeseniashire, NY 91946', 'carleton.streich@conroy.com', '704.224.4620', NULL, 1, '2026-02-04 03:24:41', '2026-02-04 03:24:41'),
(20, 1, NULL, NULL, 'trial', NULL, NULL, 2.50, 0.00, 'activa', NULL, 'Cummings, Watsica and Casper', 'Dortha Herman', '0494837465', NULL, 'NOT_STARTED', NULL, NULL, 19, 0.00, 0.00, 'IVA', '9093 Barbara Mountains Apt. 824\nEast Jaysonbury, WY 43194', 'gcorkery@fadel.info', '1-914-967-6939', NULL, 1, '2026-02-04 03:24:41', '2026-02-04 03:24:41'),
(21, 1, NULL, NULL, 'trial', NULL, NULL, 2.50, 0.00, 'activa', NULL, 'Hackett LLC', 'Athena Bashirian', '2135700622', NULL, 'NOT_STARTED', NULL, NULL, 19, 0.00, 0.00, 'IVA', '869 Arden Shore Apt. 151\nWest Anahi, RI 73249', 'bogisich.corrine@schulist.com', '972-305-7892', NULL, 1, '2026-02-04 03:24:41', '2026-02-04 03:24:41'),
(22, 1, NULL, NULL, 'trial', NULL, NULL, 2.50, 0.00, 'activa', NULL, 'Stoltenberg, Jerde and Hand', 'Mr. Nicolas Schaefer PhD', '5880671311', NULL, 'NOT_STARTED', NULL, NULL, 19, 0.00, 0.00, 'IVA', '985 Reilly Ford Apt. 990\nHaagshire, WI 96659-5166', 'monahan.jules@parisian.biz', '+1-972-557-1539', NULL, 1, '2026-02-04 03:24:41', '2026-02-04 03:24:41'),
(23, 1, NULL, NULL, 'trial', NULL, NULL, 2.50, 0.00, 'activa', NULL, 'Wiegand Group', 'Juana Franecki', '2042838247', NULL, 'NOT_STARTED', NULL, NULL, 19, 0.00, 0.00, 'IVA', '5074 Candido Causeway\nSelenaview, KY 29418', 'grant.jettie@mraz.com', '(516) 603-7177', NULL, 1, '2026-02-04 03:24:41', '2026-02-04 03:24:41'),
(24, 1, NULL, NULL, 'trial', NULL, NULL, 2.50, 0.00, 'activa', NULL, 'Bogan, Kilback and Homenick', 'Dejah Shanahan Sr.', '3506786319', NULL, 'NOT_STARTED', NULL, NULL, 19, 0.00, 0.00, 'IVA', '62816 Melisa Neck\nBrowntown, ME 34437', 'bfeil@pollich.com', '442-947-3259', NULL, 1, '2026-02-04 03:24:41', '2026-02-04 03:24:41'),
(25, 1, NULL, NULL, 'trial', NULL, NULL, 2.50, 0.00, 'activa', NULL, 'Altenwerth, Heathcote and Denesik', 'Bradford Spinka', '3166722980', NULL, 'NOT_STARTED', NULL, NULL, 19, 0.00, 0.00, 'IVA', '55938 Dominique Ridges Apt. 989\nDaijaland, MS 15909-6777', 'chackett@toy.com', '1-661-378-3375', NULL, 1, '2026-02-04 03:24:41', '2026-02-04 03:24:41'),
(26, 1, NULL, NULL, 'trial', NULL, NULL, 2.50, 0.00, 'activa', NULL, 'Runte, Abshire and DuBuque', 'Ariel Gerlach DDS', '1296608202', NULL, 'NOT_STARTED', NULL, NULL, 19, 0.00, 0.00, 'IVA', '8336 Schiller Summit\nNorth Kalliemouth, GA 79093-8497', 'glover.deven@stroman.com', '(272) 677-5328', NULL, 1, '2026-02-04 03:24:41', '2026-02-04 03:24:41'),
(27, 1, NULL, NULL, 'trial', NULL, NULL, 2.50, 0.00, 'activa', NULL, 'Adams Group', 'Jennings Schumm', '0735875111', NULL, 'NOT_STARTED', NULL, NULL, 19, 0.00, 0.00, 'IVA', '107 Verner Extensions Suite 279\nWest Elinore, SD 70877-0724', 'jose84@metz.net', '(763) 454-2917', NULL, 1, '2026-02-04 03:24:41', '2026-02-04 03:24:41'),
(28, 1, NULL, NULL, 'trial', NULL, NULL, 2.50, 0.00, 'activa', NULL, 'Hyatt PLC', 'Pattie Abshire', '5656205997', NULL, 'NOT_STARTED', NULL, NULL, 19, 0.00, 0.00, 'IVA', '125 Schimmel Crossing\nMiguelville, ME 26973-5845', 'qbins@bartell.org', '1-325-958-1278', NULL, 1, '2026-02-04 03:24:41', '2026-02-04 03:24:41');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `facturas_compra`
--

DROP TABLE IF EXISTS `facturas_compra`;
CREATE TABLE IF NOT EXISTS `facturas_compra` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `proveedor_id` bigint UNSIGNED DEFAULT NULL,
  `numero_factura` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_compra` date NOT NULL,
  `total_pagado` decimal(12,2) NOT NULL,
  `impuesto_tipo` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `impuesto_porcentaje` decimal(5,2) DEFAULT NULL,
  `impuesto_valor` decimal(12,2) NOT NULL DEFAULT '0.00',
  `subtotal_calculado` decimal(12,2) NOT NULL,
  `notas` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `facturas_compra_empresa_id_foreign` (`empresa_id`),
  KEY `facturas_compra_user_id_foreign` (`user_id`),
  KEY `facturas_compra_proveedor_id_foreign` (`proveedor_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `funciones`
--

DROP TABLE IF EXISTS `funciones`;
CREATE TABLE IF NOT EXISTS `funciones` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint UNSIGNED NOT NULL,
  `sala_id` bigint UNSIGNED NOT NULL,
  `pelicula_id` bigint UNSIGNED DEFAULT NULL,
  `fecha_hora` datetime NOT NULL,
  `precio` decimal(15,2) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `tarifa_id` bigint UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `funciones_sala_id_foreign` (`sala_id`),
  KEY `funciones_pelicula_id_foreign` (`pelicula_id`),
  KEY `idx_funciones_empresa_fecha` (`empresa_id`,`fecha_hora`),
  KEY `idx_funciones_perf_empresa_fecha` (`empresa_id`,`fecha_hora`),
  KEY `funciones_tarifa_id_foreign` (`tarifa_id`)
) ENGINE=MyISAM AUTO_INCREMENT=68 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `funciones`
--

INSERT INTO `funciones` (`id`, `empresa_id`, `sala_id`, `pelicula_id`, `fecha_hora`, `precio`, `activo`, `created_at`, `updated_at`, `tarifa_id`) VALUES
(40, 1, 1, 1, '2026-02-16 13:02:23', 30000.00, 1, '2026-02-16 16:02:23', '2026-02-16 16:02:23', NULL),
(41, 1, 1, 1, '2026-02-16 18:40:00', 30000.00, 1, '2026-02-16 19:37:03', '2026-02-16 19:37:03', NULL),
(42, 1, 2, 1, '2026-02-16 20:45:00', 30000.00, 1, '2026-02-16 19:37:47', '2026-02-16 19:37:47', NULL),
(43, 1, 1, 2, '2026-02-17 19:00:00', 30000.00, 1, '2026-02-17 13:36:14', '2026-02-17 13:36:14', NULL),
(44, 1, 1, 2, '2026-02-18 19:00:00', 30000.00, 1, '2026-02-17 13:36:15', '2026-02-17 13:36:15', NULL),
(45, 1, 1, 2, '2026-02-19 19:00:00', 30000.00, 1, '2026-02-17 13:36:15', '2026-02-17 13:36:15', NULL),
(46, 1, 1, 2, '2026-02-20 19:00:00', 30000.00, 1, '2026-02-17 13:36:15', '2026-02-17 13:36:15', NULL),
(47, 1, 1, 2, '2026-02-21 19:00:00', 30000.00, 1, '2026-02-17 13:36:16', '2026-02-17 13:36:16', NULL),
(48, 1, 1, 2, '2026-02-22 19:00:00', 30000.00, 1, '2026-02-17 13:36:16', '2026-02-17 13:36:16', NULL),
(49, 1, 1, 2, '2026-02-23 19:00:00', 30000.00, 1, '2026-02-17 13:36:17', '2026-02-17 13:36:17', NULL),
(50, 1, 1, 2, '2026-02-24 19:30:00', 30000.00, 1, '2026-02-17 13:36:17', '2026-02-17 13:56:05', NULL),
(52, 1, 2, 3, '2026-02-17 17:00:00', 30000.00, 1, '2026-02-17 16:22:56', '2026-02-17 16:22:56', NULL),
(53, 1, 2, 3, '2026-02-17 21:15:00', 30000.00, 1, '2026-02-17 16:22:56', '2026-02-17 16:22:56', NULL),
(54, 1, 2, 3, '2026-02-18 17:00:00', 30000.00, 1, '2026-02-17 16:22:57', '2026-02-17 16:22:57', NULL),
(55, 1, 2, 3, '2026-02-18 21:15:00', 30000.00, 1, '2026-02-17 16:22:57', '2026-02-17 16:22:57', NULL),
(56, 1, 2, 3, '2026-02-19 17:00:00', 30000.00, 1, '2026-02-17 16:22:57', '2026-02-17 16:22:57', NULL),
(57, 1, 2, 3, '2026-02-19 21:15:00', 30000.00, 1, '2026-02-17 16:22:57', '2026-02-17 16:22:57', NULL),
(58, 1, 2, 3, '2026-02-20 17:00:00', 30000.00, 1, '2026-02-17 16:22:57', '2026-02-17 16:22:57', NULL),
(59, 1, 2, 3, '2026-02-20 21:15:00', 30000.00, 1, '2026-02-17 16:22:58', '2026-02-17 16:22:58', NULL),
(60, 1, 2, 3, '2026-02-21 17:00:00', 30000.00, 1, '2026-02-17 16:22:58', '2026-02-17 16:22:58', NULL),
(61, 1, 2, 3, '2026-02-21 21:15:00', 30000.00, 1, '2026-02-17 16:22:58', '2026-02-17 16:22:58', NULL),
(62, 1, 2, 3, '2026-02-22 17:00:00', 30000.00, 1, '2026-02-17 16:22:58', '2026-02-17 16:22:58', NULL),
(63, 1, 2, 3, '2026-02-22 21:15:00', 30000.00, 1, '2026-02-17 16:22:58', '2026-02-17 16:22:58', NULL),
(64, 1, 2, 3, '2026-02-23 17:00:00', 30000.00, 1, '2026-02-17 16:22:59', '2026-02-17 16:22:59', NULL),
(65, 1, 2, 3, '2026-02-23 21:15:00', 30000.00, 1, '2026-02-17 16:22:59', '2026-02-17 16:22:59', NULL),
(66, 1, 2, 3, '2026-02-24 17:00:00', 30000.00, 1, '2026-02-17 16:22:59', '2026-02-17 16:22:59', NULL),
(67, 1, 2, 3, '2026-02-24 21:15:00', 30000.00, 1, '2026-02-17 16:22:59', '2026-02-17 16:22:59', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `funcion_asientos`
--

DROP TABLE IF EXISTS `funcion_asientos`;
CREATE TABLE IF NOT EXISTS `funcion_asientos` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `funcion_id` bigint UNSIGNED NOT NULL,
  `codigo_asiento` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` enum('DISPONIBLE','RESERVADO','VENDIDO') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'DISPONIBLE',
  `reservado_hasta` timestamp NULL DEFAULT NULL,
  `session_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `venta_id` bigint UNSIGNED DEFAULT NULL,
  `reservado_por` bigint UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `funcion_asientos_venta_id_foreign` (`venta_id`),
  KEY `funcion_asientos_reservado_por_foreign` (`reservado_por`),
  KEY `idx_funcion_estado` (`funcion_id`,`estado`),
  KEY `idx_reservado_hasta` (`reservado_hasta`)
) ENGINE=MyISAM AUTO_INCREMENT=2127 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `funcion_asientos`
--

INSERT INTO `funcion_asientos` (`id`, `funcion_id`, `codigo_asiento`, `estado`, `reservado_hasta`, `session_id`, `created_at`, `updated_at`, `venta_id`, `reservado_por`) VALUES
(1093, 40, 'A1', 'DISPONIBLE', NULL, NULL, '2026-02-16 16:02:23', '2026-02-20 15:46:44', NULL, NULL),
(1094, 40, 'A2', 'DISPONIBLE', NULL, NULL, '2026-02-16 16:02:23', '2026-02-20 15:46:44', NULL, NULL),
(1095, 40, 'A3', 'DISPONIBLE', NULL, NULL, '2026-02-16 16:02:23', '2026-02-20 15:46:44', NULL, NULL),
(1096, 40, 'A4', 'DISPONIBLE', NULL, NULL, '2026-02-16 16:02:23', '2026-02-20 15:46:44', NULL, NULL),
(1097, 40, 'A5', 'DISPONIBLE', NULL, NULL, '2026-02-16 16:02:23', '2026-02-20 15:46:44', NULL, NULL),
(1098, 40, 'A6', 'DISPONIBLE', NULL, NULL, '2026-02-16 16:02:23', '2026-02-20 15:46:44', NULL, NULL),
(1099, 40, 'A7', 'DISPONIBLE', NULL, NULL, '2026-02-16 16:02:23', '2026-02-20 15:46:44', NULL, NULL),
(1100, 40, 'A8', 'DISPONIBLE', NULL, NULL, '2026-02-16 16:02:23', '2026-02-20 15:46:44', NULL, NULL),
(1101, 40, 'A9', 'DISPONIBLE', NULL, NULL, '2026-02-16 16:02:23', '2026-02-20 15:46:44', NULL, NULL),
(1102, 40, 'A10', 'DISPONIBLE', NULL, NULL, '2026-02-16 16:02:23', '2026-02-20 15:46:44', NULL, NULL),
(1103, 41, 'A1', 'DISPONIBLE', NULL, NULL, '2026-02-16 19:37:03', '2026-02-20 15:46:44', NULL, NULL),
(1104, 41, 'A2', 'DISPONIBLE', NULL, NULL, '2026-02-16 19:37:03', '2026-02-20 15:46:44', NULL, NULL),
(1105, 41, 'A3', 'DISPONIBLE', NULL, NULL, '2026-02-16 19:37:03', '2026-02-20 15:46:44', NULL, NULL),
(1106, 41, 'A4', 'DISPONIBLE', NULL, NULL, '2026-02-16 19:37:03', '2026-02-20 15:46:44', NULL, NULL),
(1107, 41, 'A5', 'DISPONIBLE', NULL, NULL, '2026-02-16 19:37:03', '2026-02-20 15:46:44', NULL, NULL),
(1108, 41, 'A6', 'DISPONIBLE', NULL, NULL, '2026-02-16 19:37:03', '2026-02-20 15:46:44', NULL, NULL),
(1109, 41, 'A7', 'DISPONIBLE', NULL, NULL, '2026-02-16 19:37:03', '2026-02-20 15:46:44', NULL, NULL),
(1110, 41, 'A8', 'DISPONIBLE', NULL, NULL, '2026-02-16 19:37:03', '2026-02-20 15:46:44', NULL, NULL),
(1111, 41, 'B1', 'DISPONIBLE', NULL, NULL, '2026-02-16 19:37:03', '2026-02-20 15:46:44', NULL, NULL),
(1112, 41, 'B2', 'DISPONIBLE', NULL, NULL, '2026-02-16 19:37:03', '2026-02-20 15:46:44', NULL, NULL),
(1113, 41, 'B3', 'DISPONIBLE', NULL, NULL, '2026-02-16 19:37:03', '2026-02-20 15:46:44', NULL, NULL),
(1114, 41, 'B4', 'DISPONIBLE', NULL, NULL, '2026-02-16 19:37:03', '2026-02-20 15:46:44', NULL, NULL),
(1115, 41, 'B5', 'DISPONIBLE', NULL, NULL, '2026-02-16 19:37:03', '2026-02-20 15:46:44', NULL, NULL),
(1116, 41, 'B6', 'DISPONIBLE', NULL, NULL, '2026-02-16 19:37:03', '2026-02-20 15:46:44', NULL, NULL),
(1117, 41, 'B7', 'DISPONIBLE', NULL, NULL, '2026-02-16 19:37:03', '2026-02-20 15:46:44', NULL, NULL),
(1118, 41, 'B8', 'DISPONIBLE', NULL, NULL, '2026-02-16 19:37:03', '2026-02-20 15:46:44', NULL, NULL),
(1119, 41, 'C1', 'DISPONIBLE', NULL, NULL, '2026-02-16 19:37:03', '2026-02-20 15:46:44', NULL, NULL),
(1120, 41, 'C2', 'DISPONIBLE', NULL, NULL, '2026-02-16 19:37:03', '2026-02-20 15:46:44', NULL, NULL),
(1121, 41, 'C3', 'DISPONIBLE', NULL, NULL, '2026-02-16 19:37:03', '2026-02-20 15:46:44', NULL, NULL),
(1122, 41, 'C4', 'DISPONIBLE', NULL, NULL, '2026-02-16 19:37:03', '2026-02-20 15:46:44', NULL, NULL),
(1123, 41, 'C5', 'DISPONIBLE', NULL, NULL, '2026-02-16 19:37:03', '2026-02-20 15:46:44', NULL, NULL),
(1124, 41, 'C6', 'DISPONIBLE', NULL, NULL, '2026-02-16 19:37:03', '2026-02-20 15:46:44', NULL, NULL),
(1125, 41, 'C7', 'DISPONIBLE', NULL, NULL, '2026-02-16 19:37:03', '2026-02-20 15:46:44', NULL, NULL),
(1126, 41, 'C8', 'DISPONIBLE', NULL, NULL, '2026-02-16 19:37:03', '2026-02-20 15:46:44', NULL, NULL),
(1127, 41, 'D1', 'DISPONIBLE', NULL, NULL, '2026-02-16 19:37:03', '2026-02-20 15:46:44', NULL, NULL),
(1128, 41, 'D2', 'DISPONIBLE', NULL, NULL, '2026-02-16 19:37:03', '2026-02-20 15:46:44', NULL, NULL),
(1129, 41, 'D3', 'DISPONIBLE', NULL, NULL, '2026-02-16 19:37:03', '2026-02-20 15:46:44', NULL, NULL),
(1130, 41, 'D4', 'DISPONIBLE', NULL, NULL, '2026-02-16 19:37:03', '2026-02-20 15:46:44', NULL, NULL),
(1131, 41, 'D5', 'DISPONIBLE', NULL, NULL, '2026-02-16 19:37:03', '2026-02-20 15:46:44', NULL, NULL),
(1132, 41, 'D6', 'DISPONIBLE', NULL, NULL, '2026-02-16 19:37:03', '2026-02-20 15:46:44', NULL, NULL),
(1133, 41, 'D7', 'DISPONIBLE', NULL, NULL, '2026-02-16 19:37:03', '2026-02-20 15:46:44', NULL, NULL),
(1134, 41, 'D8', 'DISPONIBLE', NULL, NULL, '2026-02-16 19:37:03', '2026-02-20 15:46:44', NULL, NULL),
(1135, 41, 'E1', 'DISPONIBLE', NULL, NULL, '2026-02-16 19:37:03', '2026-02-20 15:46:44', NULL, NULL),
(1136, 41, 'E2', 'DISPONIBLE', NULL, NULL, '2026-02-16 19:37:03', '2026-02-20 15:46:44', NULL, NULL),
(1137, 41, 'E3', 'DISPONIBLE', NULL, NULL, '2026-02-16 19:37:03', '2026-02-20 15:46:44', NULL, NULL),
(1138, 41, 'E4', 'DISPONIBLE', NULL, NULL, '2026-02-16 19:37:03', '2026-02-20 15:46:44', NULL, NULL),
(1139, 41, 'E5', 'DISPONIBLE', NULL, NULL, '2026-02-16 19:37:03', '2026-02-20 15:46:44', NULL, NULL),
(1140, 41, 'E6', 'DISPONIBLE', NULL, NULL, '2026-02-16 19:37:03', '2026-02-20 15:46:44', NULL, NULL),
(1141, 41, 'E7', 'DISPONIBLE', NULL, NULL, '2026-02-16 19:37:03', '2026-02-20 15:46:44', NULL, NULL),
(1142, 41, 'E8', 'DISPONIBLE', NULL, NULL, '2026-02-16 19:37:03', '2026-02-20 15:46:44', NULL, NULL),
(1143, 41, 'F1', 'DISPONIBLE', NULL, NULL, '2026-02-16 19:37:03', '2026-02-20 15:46:44', NULL, NULL),
(1144, 41, 'F2', 'DISPONIBLE', NULL, NULL, '2026-02-16 19:37:03', '2026-02-20 15:46:44', NULL, NULL),
(1145, 41, 'F3', 'DISPONIBLE', NULL, NULL, '2026-02-16 19:37:03', '2026-02-20 15:46:44', NULL, NULL),
(1146, 41, 'F4', 'DISPONIBLE', NULL, NULL, '2026-02-16 19:37:03', '2026-02-20 15:46:44', NULL, NULL),
(1147, 41, 'F5', 'DISPONIBLE', NULL, NULL, '2026-02-16 19:37:03', '2026-02-20 15:46:44', NULL, NULL),
(1148, 41, 'F6', 'DISPONIBLE', NULL, NULL, '2026-02-16 19:37:03', '2026-02-20 15:46:44', NULL, NULL),
(1149, 41, 'F7', 'DISPONIBLE', NULL, NULL, '2026-02-16 19:37:03', '2026-02-20 15:46:44', NULL, NULL),
(1150, 41, 'F8', 'DISPONIBLE', NULL, NULL, '2026-02-16 19:37:03', '2026-02-20 15:46:44', NULL, NULL),
(1151, 41, 'G1', 'DISPONIBLE', NULL, NULL, '2026-02-16 19:37:03', '2026-02-20 15:46:44', NULL, NULL),
(1152, 41, 'G2', 'DISPONIBLE', NULL, NULL, '2026-02-16 19:37:03', '2026-02-20 15:46:44', NULL, NULL),
(1153, 41, 'G3', 'DISPONIBLE', NULL, NULL, '2026-02-16 19:37:03', '2026-02-20 15:46:44', NULL, NULL),
(1154, 41, 'G4', 'DISPONIBLE', NULL, NULL, '2026-02-16 19:37:03', '2026-02-20 15:46:44', NULL, NULL),
(1155, 41, 'G5', 'DISPONIBLE', NULL, NULL, '2026-02-16 19:37:03', '2026-02-20 15:46:44', NULL, NULL),
(1156, 41, 'G6', 'DISPONIBLE', NULL, NULL, '2026-02-16 19:37:03', '2026-02-20 15:46:44', NULL, NULL),
(1157, 41, 'G7', 'DISPONIBLE', NULL, NULL, '2026-02-16 19:37:03', '2026-02-20 15:46:44', NULL, NULL),
(1158, 41, 'G8', 'DISPONIBLE', NULL, NULL, '2026-02-16 19:37:03', '2026-02-20 15:46:44', NULL, NULL),
(1159, 41, 'H1', 'DISPONIBLE', NULL, NULL, '2026-02-16 19:37:03', '2026-02-20 15:46:44', NULL, NULL),
(1160, 41, 'H2', 'DISPONIBLE', NULL, NULL, '2026-02-16 19:37:03', '2026-02-20 15:46:44', NULL, NULL),
(1161, 41, 'H3', 'DISPONIBLE', NULL, NULL, '2026-02-16 19:37:03', '2026-02-20 15:46:44', NULL, NULL),
(1162, 41, 'H4', 'DISPONIBLE', NULL, NULL, '2026-02-16 19:37:03', '2026-02-20 15:46:44', NULL, NULL),
(1163, 41, 'H5', 'DISPONIBLE', NULL, NULL, '2026-02-16 19:37:03', '2026-02-20 15:46:44', NULL, NULL),
(1164, 41, 'H6', 'DISPONIBLE', NULL, NULL, '2026-02-16 19:37:03', '2026-02-20 15:46:44', NULL, NULL),
(1165, 41, 'H7', 'DISPONIBLE', NULL, NULL, '2026-02-16 19:37:03', '2026-02-20 15:46:44', NULL, NULL),
(1166, 41, 'H8', 'DISPONIBLE', NULL, NULL, '2026-02-16 19:37:03', '2026-02-20 15:46:44', NULL, NULL),
(1167, 41, 'I1', 'DISPONIBLE', NULL, NULL, '2026-02-16 19:37:03', '2026-02-20 15:46:44', NULL, NULL),
(1168, 41, 'I2', 'DISPONIBLE', NULL, NULL, '2026-02-16 19:37:03', '2026-02-20 15:46:44', NULL, NULL),
(1169, 41, 'I3', 'DISPONIBLE', NULL, NULL, '2026-02-16 19:37:03', '2026-02-20 15:46:44', NULL, NULL),
(1170, 41, 'I4', 'DISPONIBLE', NULL, NULL, '2026-02-16 19:37:03', '2026-02-20 15:46:44', NULL, NULL),
(1171, 41, 'I5', 'DISPONIBLE', NULL, NULL, '2026-02-16 19:37:03', '2026-02-20 15:46:44', NULL, NULL),
(1172, 41, 'I6', 'DISPONIBLE', NULL, NULL, '2026-02-16 19:37:03', '2026-02-20 15:46:44', NULL, NULL),
(1173, 41, 'I7', 'DISPONIBLE', NULL, NULL, '2026-02-16 19:37:03', '2026-02-20 15:46:44', NULL, NULL),
(1174, 41, 'I8', 'DISPONIBLE', NULL, NULL, '2026-02-16 19:37:03', '2026-02-20 15:46:44', NULL, NULL),
(1175, 41, 'J5', 'DISPONIBLE', NULL, NULL, '2026-02-16 19:37:03', '2026-02-20 15:46:44', NULL, NULL),
(1176, 41, 'J6', 'DISPONIBLE', NULL, NULL, '2026-02-16 19:37:03', '2026-02-20 15:46:44', NULL, NULL),
(1177, 41, 'J7', 'DISPONIBLE', NULL, NULL, '2026-02-16 19:37:03', '2026-02-20 15:46:44', NULL, NULL),
(1178, 41, 'J8', 'DISPONIBLE', NULL, NULL, '2026-02-16 19:37:03', '2026-02-20 15:46:44', NULL, NULL),
(1179, 42, 'A1', 'DISPONIBLE', NULL, NULL, '2026-02-17 00:09:32', '2026-02-20 15:46:44', NULL, NULL),
(1180, 42, 'A2', 'DISPONIBLE', NULL, NULL, '2026-02-17 00:09:32', '2026-02-20 15:46:44', NULL, NULL),
(1181, 42, 'A3', 'DISPONIBLE', NULL, NULL, '2026-02-17 00:09:32', '2026-02-20 15:46:44', NULL, NULL),
(1182, 42, 'A4', 'DISPONIBLE', NULL, NULL, '2026-02-17 00:09:32', '2026-02-20 15:46:44', NULL, NULL),
(1183, 42, 'B1', 'DISPONIBLE', NULL, NULL, '2026-02-17 00:09:32', '2026-02-20 15:46:44', NULL, NULL),
(1184, 42, 'B2', 'DISPONIBLE', NULL, NULL, '2026-02-17 00:09:32', '2026-02-20 15:46:44', NULL, NULL),
(1185, 42, 'B3', 'DISPONIBLE', NULL, NULL, '2026-02-17 00:09:32', '2026-02-20 15:46:44', NULL, NULL),
(1186, 42, 'B4', 'DISPONIBLE', NULL, NULL, '2026-02-17 00:09:32', '2026-02-20 15:46:44', NULL, NULL),
(1187, 42, 'C1', 'DISPONIBLE', NULL, NULL, '2026-02-17 00:09:32', '2026-02-20 15:46:44', NULL, NULL),
(1188, 42, 'C2', 'DISPONIBLE', NULL, NULL, '2026-02-17 00:09:32', '2026-02-20 15:46:44', NULL, NULL),
(1189, 42, 'C3', 'DISPONIBLE', NULL, NULL, '2026-02-17 00:09:32', '2026-02-20 15:46:44', NULL, NULL),
(1190, 42, 'C4', 'DISPONIBLE', NULL, NULL, '2026-02-17 00:09:32', '2026-02-20 15:46:44', NULL, NULL),
(1191, 42, 'D1', 'DISPONIBLE', NULL, NULL, '2026-02-17 00:09:32', '2026-02-20 15:46:44', NULL, NULL),
(1192, 42, 'D2', 'DISPONIBLE', NULL, NULL, '2026-02-17 00:09:32', '2026-02-20 15:46:44', NULL, NULL),
(1193, 42, 'D3', 'DISPONIBLE', NULL, NULL, '2026-02-17 00:09:32', '2026-02-20 15:46:44', NULL, NULL),
(1194, 42, 'D4', 'DISPONIBLE', NULL, NULL, '2026-02-17 00:09:32', '2026-02-20 15:46:44', NULL, NULL),
(1195, 42, 'E1', 'DISPONIBLE', NULL, NULL, '2026-02-17 00:09:32', '2026-02-20 15:46:44', NULL, NULL),
(1196, 42, 'E2', 'DISPONIBLE', NULL, NULL, '2026-02-17 00:09:32', '2026-02-20 15:46:44', NULL, NULL),
(1197, 42, 'E3', 'DISPONIBLE', NULL, NULL, '2026-02-17 00:09:32', '2026-02-20 15:46:44', NULL, NULL),
(1198, 42, 'E4', 'DISPONIBLE', NULL, NULL, '2026-02-17 00:09:32', '2026-02-20 15:46:44', NULL, NULL),
(1199, 43, 'A1', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:14', '2026-02-20 15:46:44', NULL, NULL),
(1200, 43, 'A2', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:14', '2026-02-20 15:46:44', NULL, NULL),
(1201, 43, 'A3', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:14', '2026-02-20 15:46:44', NULL, NULL),
(1202, 43, 'A4', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:14', '2026-02-20 15:46:44', NULL, NULL),
(1203, 43, 'A5', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:14', '2026-02-20 15:46:44', NULL, NULL),
(1204, 43, 'A6', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:14', '2026-02-20 15:46:44', NULL, NULL),
(1205, 43, 'A7', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:14', '2026-02-20 15:46:44', NULL, NULL),
(1206, 43, 'A8', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:14', '2026-02-20 15:46:44', NULL, NULL),
(1207, 43, 'B1', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:14', '2026-02-20 15:46:44', NULL, NULL),
(1208, 43, 'B2', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:14', '2026-02-20 15:46:44', NULL, NULL),
(1209, 43, 'B3', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:14', '2026-02-20 15:46:44', NULL, NULL),
(1210, 43, 'B4', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:14', '2026-02-20 15:46:44', NULL, NULL),
(1211, 43, 'B5', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:14', '2026-02-20 15:46:44', NULL, NULL),
(1212, 43, 'B6', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:14', '2026-02-20 15:46:44', NULL, NULL),
(1213, 43, 'B7', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:14', '2026-02-20 15:46:44', NULL, NULL),
(1214, 43, 'B8', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:14', '2026-02-20 15:46:44', NULL, NULL),
(1215, 43, 'C1', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:14', '2026-02-20 15:46:44', NULL, NULL),
(1216, 43, 'C2', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:14', '2026-02-20 15:46:44', NULL, NULL),
(1217, 43, 'C3', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:14', '2026-02-20 15:46:44', NULL, NULL),
(1218, 43, 'C4', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:14', '2026-02-20 15:46:44', NULL, NULL),
(1219, 43, 'C5', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:14', '2026-02-20 15:46:44', NULL, NULL),
(1220, 43, 'C6', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:14', '2026-02-20 15:46:44', NULL, NULL),
(1221, 43, 'C7', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:14', '2026-02-20 15:46:44', NULL, NULL),
(1222, 43, 'C8', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:14', '2026-02-20 15:46:44', NULL, NULL),
(1223, 43, 'D1', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:14', '2026-02-20 15:46:44', NULL, NULL),
(1224, 43, 'D2', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:14', '2026-02-20 15:46:44', NULL, NULL),
(1225, 43, 'D3', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:14', '2026-02-20 15:46:44', NULL, NULL),
(1226, 43, 'D4', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:14', '2026-02-20 15:46:44', NULL, NULL),
(1227, 43, 'D5', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:14', '2026-02-20 15:46:44', NULL, NULL),
(1228, 43, 'D6', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:14', '2026-02-20 15:46:44', NULL, NULL),
(1229, 43, 'D7', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:14', '2026-02-20 15:46:44', NULL, NULL),
(1230, 43, 'D8', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:14', '2026-02-20 15:46:44', NULL, NULL),
(1231, 43, 'E1', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:14', '2026-02-20 15:46:44', NULL, NULL),
(1232, 43, 'E2', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:14', '2026-02-20 15:46:44', NULL, NULL),
(1233, 43, 'E3', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:14', '2026-02-20 15:46:44', NULL, NULL),
(1234, 43, 'E4', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:14', '2026-02-20 15:46:44', NULL, NULL),
(1235, 43, 'E5', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:14', '2026-02-20 15:46:44', NULL, NULL),
(1236, 43, 'E6', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:14', '2026-02-20 15:46:44', NULL, NULL),
(1237, 43, 'E7', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:14', '2026-02-20 15:46:44', NULL, NULL),
(1238, 43, 'E8', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:14', '2026-02-20 15:46:44', NULL, NULL),
(1239, 43, 'F1', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:14', '2026-02-20 15:46:44', NULL, NULL),
(1240, 43, 'F2', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:14', '2026-02-20 15:46:44', NULL, NULL),
(1241, 43, 'F3', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:14', '2026-02-20 15:46:44', NULL, NULL),
(1242, 43, 'F4', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:14', '2026-02-20 15:46:44', NULL, NULL),
(1243, 43, 'F5', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:14', '2026-02-20 15:46:44', NULL, NULL),
(1244, 43, 'F6', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:14', '2026-02-20 15:46:44', NULL, NULL),
(1245, 43, 'F7', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:14', '2026-02-20 15:46:44', NULL, NULL),
(1246, 43, 'F8', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:14', '2026-02-20 15:46:44', NULL, NULL),
(1247, 43, 'G1', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:14', '2026-02-20 15:46:44', NULL, NULL),
(1248, 43, 'G2', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:14', '2026-02-20 15:46:44', NULL, NULL),
(1249, 43, 'G3', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:14', '2026-02-20 15:46:44', NULL, NULL),
(1250, 43, 'G4', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:14', '2026-02-20 15:46:44', NULL, NULL),
(1251, 43, 'G5', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:14', '2026-02-20 15:46:44', NULL, NULL),
(1252, 43, 'G6', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:14', '2026-02-20 15:46:44', NULL, NULL),
(1253, 43, 'G7', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:14', '2026-02-20 15:46:44', NULL, NULL),
(1254, 43, 'G8', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:14', '2026-02-20 15:46:44', NULL, NULL),
(1255, 43, 'H1', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:14', '2026-02-20 15:46:44', NULL, NULL),
(1256, 43, 'H2', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:14', '2026-02-20 15:46:44', NULL, NULL),
(1257, 43, 'H3', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:14', '2026-02-20 15:46:44', NULL, NULL),
(1258, 43, 'H4', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:14', '2026-02-20 15:46:44', NULL, NULL),
(1259, 43, 'H5', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:14', '2026-02-20 15:46:44', NULL, NULL),
(1260, 43, 'H6', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:14', '2026-02-20 15:46:44', NULL, NULL),
(1261, 43, 'H7', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:14', '2026-02-20 15:46:44', NULL, NULL),
(1262, 43, 'H8', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:14', '2026-02-20 15:46:44', NULL, NULL),
(1263, 43, 'I1', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:14', '2026-02-20 15:46:44', NULL, NULL),
(1264, 43, 'I2', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:14', '2026-02-20 15:46:44', NULL, NULL),
(1265, 43, 'I3', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:14', '2026-02-20 15:46:44', NULL, NULL),
(1266, 43, 'I4', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:14', '2026-02-20 15:46:44', NULL, NULL),
(1267, 43, 'I5', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:14', '2026-02-20 15:46:44', NULL, NULL),
(1268, 43, 'I6', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:14', '2026-02-20 15:46:44', NULL, NULL),
(1269, 43, 'I7', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:14', '2026-02-20 15:46:44', NULL, NULL),
(1270, 43, 'I8', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1271, 43, 'J5', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1272, 43, 'J6', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1273, 43, 'J7', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1274, 43, 'J8', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1275, 44, 'A1', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1276, 44, 'A2', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1277, 44, 'A3', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1278, 44, 'A4', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1279, 44, 'A5', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1280, 44, 'A6', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1281, 44, 'A7', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1282, 44, 'A8', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1283, 44, 'B1', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1284, 44, 'B2', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1285, 44, 'B3', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1286, 44, 'B4', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1287, 44, 'B5', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1288, 44, 'B6', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1289, 44, 'B7', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1290, 44, 'B8', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1291, 44, 'C1', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1292, 44, 'C2', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1293, 44, 'C3', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1294, 44, 'C4', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1295, 44, 'C5', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1296, 44, 'C6', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1297, 44, 'C7', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1298, 44, 'C8', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1299, 44, 'D1', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1300, 44, 'D2', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1301, 44, 'D3', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1302, 44, 'D4', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1303, 44, 'D5', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1304, 44, 'D6', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1305, 44, 'D7', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1306, 44, 'D8', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1307, 44, 'E1', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1308, 44, 'E2', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1309, 44, 'E3', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1310, 44, 'E4', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1311, 44, 'E5', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1312, 44, 'E6', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1313, 44, 'E7', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1314, 44, 'E8', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1315, 44, 'F1', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1316, 44, 'F2', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1317, 44, 'F3', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1318, 44, 'F4', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1319, 44, 'F5', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1320, 44, 'F6', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1321, 44, 'F7', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1322, 44, 'F8', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1323, 44, 'G1', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1324, 44, 'G2', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1325, 44, 'G3', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1326, 44, 'G4', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1327, 44, 'G5', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1328, 44, 'G6', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1329, 44, 'G7', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1330, 44, 'G8', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1331, 44, 'H1', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1332, 44, 'H2', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1333, 44, 'H3', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1334, 44, 'H4', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1335, 44, 'H5', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1336, 44, 'H6', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1337, 44, 'H7', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1338, 44, 'H8', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1339, 44, 'I1', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1340, 44, 'I2', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1341, 44, 'I3', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1342, 44, 'I4', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1343, 44, 'I5', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1344, 44, 'I6', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1345, 44, 'I7', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1346, 44, 'I8', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1347, 44, 'J5', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1348, 44, 'J6', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1349, 44, 'J7', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1350, 44, 'J8', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1351, 45, 'A1', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1352, 45, 'A2', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1353, 45, 'A3', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1354, 45, 'A4', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1355, 45, 'A5', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1356, 45, 'A6', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1357, 45, 'A7', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1358, 45, 'A8', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1359, 45, 'B1', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1360, 45, 'B2', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1361, 45, 'B3', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1362, 45, 'B4', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1363, 45, 'B5', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1364, 45, 'B6', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1365, 45, 'B7', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1366, 45, 'B8', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1367, 45, 'C1', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1368, 45, 'C2', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1369, 45, 'C3', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1370, 45, 'C4', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1371, 45, 'C5', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1372, 45, 'C6', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1373, 45, 'C7', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1374, 45, 'C8', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1375, 45, 'D1', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1376, 45, 'D2', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1377, 45, 'D3', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1378, 45, 'D4', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1379, 45, 'D5', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1380, 45, 'D6', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1381, 45, 'D7', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1382, 45, 'D8', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1383, 45, 'E1', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1384, 45, 'E2', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1385, 45, 'E3', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1386, 45, 'E4', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1387, 45, 'E5', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1388, 45, 'E6', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1389, 45, 'E7', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1390, 45, 'E8', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1391, 45, 'F1', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1392, 45, 'F2', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1393, 45, 'F3', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1394, 45, 'F4', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1395, 45, 'F5', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1396, 45, 'F6', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1397, 45, 'F7', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1398, 45, 'F8', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1399, 45, 'G1', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1400, 45, 'G2', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1401, 45, 'G3', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1402, 45, 'G4', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1403, 45, 'G5', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1404, 45, 'G6', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1405, 45, 'G7', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1406, 45, 'G8', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1407, 45, 'H1', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1408, 45, 'H2', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1409, 45, 'H3', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1410, 45, 'H4', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1411, 45, 'H5', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1412, 45, 'H6', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1413, 45, 'H7', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1414, 45, 'H8', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1415, 45, 'I1', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1416, 45, 'I2', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1417, 45, 'I3', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1418, 45, 'I4', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1419, 45, 'I5', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1420, 45, 'I6', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1421, 45, 'I7', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1422, 45, 'I8', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1423, 45, 'J5', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1424, 45, 'J6', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1425, 45, 'J7', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1426, 45, 'J8', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1427, 46, 'A1', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1428, 46, 'A2', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1429, 46, 'A3', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1430, 46, 'A4', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1431, 46, 'A5', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1432, 46, 'A6', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:15', '2026-02-20 15:46:44', NULL, NULL),
(1433, 46, 'A7', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1434, 46, 'A8', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1435, 46, 'B1', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1436, 46, 'B2', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1437, 46, 'B3', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1438, 46, 'B4', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1439, 46, 'B5', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1440, 46, 'B6', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1441, 46, 'B7', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1442, 46, 'B8', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1443, 46, 'C1', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1444, 46, 'C2', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1445, 46, 'C3', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1446, 46, 'C4', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1447, 46, 'C5', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1448, 46, 'C6', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1449, 46, 'C7', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1450, 46, 'C8', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1451, 46, 'D1', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1452, 46, 'D2', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1453, 46, 'D3', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1454, 46, 'D4', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1455, 46, 'D5', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1456, 46, 'D6', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1457, 46, 'D7', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1458, 46, 'D8', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1459, 46, 'E1', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1460, 46, 'E2', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1461, 46, 'E3', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1462, 46, 'E4', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1463, 46, 'E5', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1464, 46, 'E6', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1465, 46, 'E7', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1466, 46, 'E8', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1467, 46, 'F1', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1468, 46, 'F2', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1469, 46, 'F3', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1470, 46, 'F4', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1471, 46, 'F5', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1472, 46, 'F6', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1473, 46, 'F7', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1474, 46, 'F8', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1475, 46, 'G1', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1476, 46, 'G2', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1477, 46, 'G3', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1478, 46, 'G4', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1479, 46, 'G5', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1480, 46, 'G6', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1481, 46, 'G7', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1482, 46, 'G8', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1483, 46, 'H1', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1484, 46, 'H2', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1485, 46, 'H3', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1486, 46, 'H4', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1487, 46, 'H5', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1488, 46, 'H6', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1489, 46, 'H7', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1490, 46, 'H8', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1491, 46, 'I1', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1492, 46, 'I2', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1493, 46, 'I3', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1494, 46, 'I4', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1495, 46, 'I5', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1496, 46, 'I6', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1497, 46, 'I7', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1498, 46, 'I8', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1499, 46, 'J5', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1500, 46, 'J6', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1501, 46, 'J7', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1502, 46, 'J8', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1503, 47, 'A1', 'VENDIDO', NULL, NULL, '2026-02-17 13:36:16', '2026-02-21 16:00:36', 10, NULL),
(1504, 47, 'A2', 'VENDIDO', NULL, NULL, '2026-02-17 13:36:16', '2026-02-21 16:00:36', 10, NULL),
(1505, 47, 'A3', 'VENDIDO', NULL, NULL, '2026-02-17 13:36:16', '2026-02-21 16:00:38', 11, NULL),
(1506, 47, 'A4', 'VENDIDO', NULL, NULL, '2026-02-17 13:36:16', '2026-02-21 16:00:38', 11, NULL),
(1507, 47, 'A5', 'VENDIDO', NULL, NULL, '2026-02-17 13:36:16', '2026-02-21 16:00:38', 11, NULL),
(1508, 47, 'A6', 'VENDIDO', NULL, NULL, '2026-02-17 13:36:16', '2026-02-21 16:00:38', 11, NULL),
(1509, 47, 'A7', 'VENDIDO', NULL, NULL, '2026-02-17 13:36:16', '2026-02-21 16:00:38', 11, NULL),
(1510, 47, 'A8', 'VENDIDO', NULL, NULL, '2026-02-17 13:36:16', '2026-02-21 16:00:38', 12, NULL),
(1511, 47, 'B1', 'VENDIDO', NULL, NULL, '2026-02-17 13:36:16', '2026-02-21 16:00:38', 12, NULL),
(1512, 47, 'B2', 'VENDIDO', NULL, NULL, '2026-02-17 13:36:16', '2026-02-21 16:00:38', 13, NULL),
(1513, 47, 'B3', 'VENDIDO', NULL, NULL, '2026-02-17 13:36:16', '2026-02-21 16:00:38', 13, NULL),
(1514, 47, 'B4', 'VENDIDO', NULL, NULL, '2026-02-17 13:36:16', '2026-02-21 16:00:38', 13, NULL),
(1515, 47, 'B5', 'VENDIDO', NULL, NULL, '2026-02-17 13:36:16', '2026-02-21 16:00:38', 13, NULL),
(1516, 47, 'B6', 'VENDIDO', NULL, NULL, '2026-02-17 13:36:16', '2026-02-21 16:00:39', 14, NULL),
(1517, 47, 'B7', 'VENDIDO', NULL, NULL, '2026-02-17 13:36:16', '2026-02-21 16:00:39', 14, NULL),
(1518, 47, 'B8', 'VENDIDO', NULL, NULL, '2026-02-17 13:36:16', '2026-02-21 16:00:39', 14, NULL),
(1519, 47, 'C1', 'VENDIDO', NULL, NULL, '2026-02-17 13:36:16', '2026-02-21 16:00:39', 15, NULL),
(1520, 47, 'C2', 'VENDIDO', NULL, NULL, '2026-02-17 13:36:16', '2026-02-21 16:00:39', 16, NULL),
(1521, 47, 'C3', 'VENDIDO', NULL, NULL, '2026-02-17 13:36:16', '2026-02-21 16:00:39', 16, NULL),
(1522, 47, 'C4', 'VENDIDO', NULL, NULL, '2026-02-17 13:36:16', '2026-02-21 16:00:40', 17, NULL),
(1523, 47, 'C5', 'VENDIDO', NULL, NULL, '2026-02-17 13:36:16', '2026-02-21 16:00:40', 17, NULL),
(1524, 47, 'C6', 'VENDIDO', NULL, NULL, '2026-02-17 13:36:16', '2026-02-21 16:00:40', 17, NULL),
(1525, 47, 'C7', 'VENDIDO', NULL, NULL, '2026-02-17 13:36:16', '2026-02-21 16:00:40', 18, NULL),
(1526, 47, 'C8', 'VENDIDO', NULL, NULL, '2026-02-17 13:36:16', '2026-02-21 16:00:40', 18, NULL),
(1527, 47, 'D1', 'VENDIDO', NULL, NULL, '2026-02-17 13:36:16', '2026-02-21 16:00:40', 18, NULL),
(1528, 47, 'D2', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1529, 47, 'D3', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1530, 47, 'D4', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1531, 47, 'D5', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1532, 47, 'D6', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1533, 47, 'D7', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1534, 47, 'D8', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1535, 47, 'E1', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1536, 47, 'E2', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1537, 47, 'E3', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1538, 47, 'E4', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1539, 47, 'E5', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1540, 47, 'E6', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1541, 47, 'E7', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1542, 47, 'E8', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1543, 47, 'F1', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1544, 47, 'F2', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1545, 47, 'F3', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1546, 47, 'F4', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1547, 47, 'F5', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1548, 47, 'F6', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1549, 47, 'F7', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1550, 47, 'F8', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1551, 47, 'G1', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1552, 47, 'G2', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1553, 47, 'G3', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1554, 47, 'G4', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1555, 47, 'G5', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1556, 47, 'G6', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1557, 47, 'G7', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1558, 47, 'G8', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1559, 47, 'H1', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1560, 47, 'H2', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1561, 47, 'H3', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1562, 47, 'H4', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1563, 47, 'H5', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1564, 47, 'H6', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1565, 47, 'H7', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1566, 47, 'H8', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1567, 47, 'I1', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1568, 47, 'I2', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1569, 47, 'I3', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1570, 47, 'I4', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1571, 47, 'I5', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1572, 47, 'I6', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1573, 47, 'I7', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1574, 47, 'I8', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1575, 47, 'J5', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1576, 47, 'J6', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1577, 47, 'J7', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1578, 47, 'J8', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1579, 48, 'A1', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1580, 48, 'A2', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1581, 48, 'A3', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1582, 48, 'A4', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1583, 48, 'A5', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1584, 48, 'A6', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1585, 48, 'A7', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1586, 48, 'A8', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1587, 48, 'B1', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1588, 48, 'B2', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1589, 48, 'B3', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1590, 48, 'B4', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1591, 48, 'B5', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL);
INSERT INTO `funcion_asientos` (`id`, `funcion_id`, `codigo_asiento`, `estado`, `reservado_hasta`, `session_id`, `created_at`, `updated_at`, `venta_id`, `reservado_por`) VALUES
(1592, 48, 'B6', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1593, 48, 'B7', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1594, 48, 'B8', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1595, 48, 'C1', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1596, 48, 'C2', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1597, 48, 'C3', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1598, 48, 'C4', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1599, 48, 'C5', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1600, 48, 'C6', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1601, 48, 'C7', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1602, 48, 'C8', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1603, 48, 'D1', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:16', '2026-02-20 15:46:44', NULL, NULL),
(1604, 48, 'D2', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1605, 48, 'D3', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1606, 48, 'D4', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1607, 48, 'D5', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1608, 48, 'D6', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1609, 48, 'D7', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1610, 48, 'D8', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1611, 48, 'E1', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1612, 48, 'E2', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1613, 48, 'E3', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1614, 48, 'E4', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1615, 48, 'E5', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1616, 48, 'E6', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1617, 48, 'E7', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1618, 48, 'E8', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1619, 48, 'F1', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1620, 48, 'F2', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1621, 48, 'F3', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1622, 48, 'F4', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1623, 48, 'F5', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1624, 48, 'F6', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1625, 48, 'F7', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1626, 48, 'F8', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1627, 48, 'G1', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1628, 48, 'G2', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1629, 48, 'G3', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1630, 48, 'G4', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1631, 48, 'G5', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1632, 48, 'G6', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1633, 48, 'G7', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1634, 48, 'G8', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1635, 48, 'H1', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1636, 48, 'H2', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1637, 48, 'H3', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1638, 48, 'H4', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1639, 48, 'H5', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1640, 48, 'H6', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1641, 48, 'H7', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1642, 48, 'H8', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1643, 48, 'I1', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1644, 48, 'I2', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1645, 48, 'I3', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1646, 48, 'I4', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1647, 48, 'I5', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1648, 48, 'I6', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1649, 48, 'I7', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1650, 48, 'I8', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1651, 48, 'J5', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1652, 48, 'J6', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1653, 48, 'J7', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1654, 48, 'J8', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1655, 49, 'A1', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1656, 49, 'A2', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1657, 49, 'A3', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1658, 49, 'A4', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1659, 49, 'A5', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1660, 49, 'A6', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1661, 49, 'A7', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1662, 49, 'A8', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1663, 49, 'B1', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1664, 49, 'B2', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1665, 49, 'B3', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1666, 49, 'B4', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1667, 49, 'B5', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1668, 49, 'B6', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1669, 49, 'B7', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1670, 49, 'B8', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1671, 49, 'C1', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1672, 49, 'C2', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1673, 49, 'C3', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1674, 49, 'C4', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1675, 49, 'C5', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1676, 49, 'C6', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1677, 49, 'C7', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1678, 49, 'C8', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1679, 49, 'D1', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1680, 49, 'D2', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1681, 49, 'D3', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1682, 49, 'D4', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1683, 49, 'D5', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1684, 49, 'D6', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1685, 49, 'D7', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1686, 49, 'D8', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1687, 49, 'E1', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1688, 49, 'E2', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1689, 49, 'E3', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1690, 49, 'E4', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1691, 49, 'E5', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1692, 49, 'E6', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1693, 49, 'E7', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1694, 49, 'E8', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1695, 49, 'F1', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1696, 49, 'F2', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1697, 49, 'F3', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1698, 49, 'F4', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1699, 49, 'F5', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1700, 49, 'F6', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1701, 49, 'F7', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1702, 49, 'F8', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1703, 49, 'G1', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1704, 49, 'G2', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1705, 49, 'G3', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1706, 49, 'G4', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1707, 49, 'G5', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1708, 49, 'G6', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1709, 49, 'G7', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1710, 49, 'G8', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1711, 49, 'H1', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1712, 49, 'H2', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1713, 49, 'H3', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1714, 49, 'H4', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1715, 49, 'H5', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1716, 49, 'H6', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1717, 49, 'H7', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1718, 49, 'H8', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1719, 49, 'I1', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1720, 49, 'I2', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1721, 49, 'I3', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1722, 49, 'I4', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1723, 49, 'I5', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1724, 49, 'I6', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1725, 49, 'I7', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1726, 49, 'I8', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1727, 49, 'J5', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1728, 49, 'J6', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1729, 49, 'J7', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1730, 49, 'J8', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1731, 50, 'A1', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1732, 50, 'A2', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1733, 50, 'A3', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1734, 50, 'A4', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1735, 50, 'A5', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1736, 50, 'A6', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1737, 50, 'A7', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1738, 50, 'A8', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1739, 50, 'B1', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1740, 50, 'B2', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1741, 50, 'B3', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1742, 50, 'B4', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1743, 50, 'B5', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1744, 50, 'B6', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1745, 50, 'B7', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1746, 50, 'B8', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1747, 50, 'C1', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1748, 50, 'C2', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1749, 50, 'C3', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1750, 50, 'C4', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1751, 50, 'C5', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1752, 50, 'C6', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1753, 50, 'C7', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1754, 50, 'C8', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1755, 50, 'D1', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1756, 50, 'D2', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1757, 50, 'D3', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1758, 50, 'D4', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1759, 50, 'D5', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1760, 50, 'D6', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1761, 50, 'D7', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1762, 50, 'D8', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1763, 50, 'E1', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1764, 50, 'E2', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1765, 50, 'E3', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1766, 50, 'E4', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1767, 50, 'E5', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1768, 50, 'E6', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1769, 50, 'E7', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1770, 50, 'E8', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1771, 50, 'F1', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1772, 50, 'F2', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1773, 50, 'F3', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1774, 50, 'F4', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1775, 50, 'F5', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1776, 50, 'F6', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1777, 50, 'F7', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1778, 50, 'F8', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1779, 50, 'G1', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1780, 50, 'G2', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1781, 50, 'G3', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1782, 50, 'G4', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1783, 50, 'G5', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:17', '2026-02-20 15:46:44', NULL, NULL),
(1784, 50, 'G6', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:18', '2026-02-20 15:46:44', NULL, NULL),
(1785, 50, 'G7', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:18', '2026-02-20 15:46:44', NULL, NULL),
(1786, 50, 'G8', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:18', '2026-02-20 15:46:44', NULL, NULL),
(1787, 50, 'H1', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:18', '2026-02-20 15:46:44', NULL, NULL),
(1788, 50, 'H2', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:18', '2026-02-20 15:46:44', NULL, NULL),
(1789, 50, 'H3', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:18', '2026-02-20 15:46:44', NULL, NULL),
(1790, 50, 'H4', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:18', '2026-02-20 15:46:44', NULL, NULL),
(1791, 50, 'H5', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:18', '2026-02-20 15:46:44', NULL, NULL),
(1792, 50, 'H6', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:18', '2026-02-20 15:46:44', NULL, NULL),
(1793, 50, 'H7', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:18', '2026-02-20 15:46:44', NULL, NULL),
(1794, 50, 'H8', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:18', '2026-02-20 15:46:44', NULL, NULL),
(1795, 50, 'I1', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:18', '2026-02-20 15:46:44', NULL, NULL),
(1796, 50, 'I2', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:18', '2026-02-20 15:46:44', NULL, NULL),
(1797, 50, 'I3', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:18', '2026-02-20 15:46:44', NULL, NULL),
(1798, 50, 'I4', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:18', '2026-02-20 15:46:44', NULL, NULL),
(1799, 50, 'I5', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:18', '2026-02-20 15:46:44', NULL, NULL),
(1800, 50, 'I6', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:18', '2026-02-20 15:46:44', NULL, NULL),
(1801, 50, 'I7', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:18', '2026-02-20 15:46:44', NULL, NULL),
(1802, 50, 'I8', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:18', '2026-02-20 15:46:44', NULL, NULL),
(1803, 50, 'J5', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:18', '2026-02-20 15:46:44', NULL, NULL),
(1804, 50, 'J6', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:18', '2026-02-20 15:46:44', NULL, NULL),
(1805, 50, 'J7', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:18', '2026-02-20 15:46:44', NULL, NULL),
(1806, 50, 'J8', 'DISPONIBLE', NULL, NULL, '2026-02-17 13:36:18', '2026-02-20 15:46:44', NULL, NULL),
(1807, 52, 'A1', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:56', '2026-02-20 15:46:44', NULL, NULL),
(1808, 52, 'A2', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:56', '2026-02-20 15:46:44', NULL, NULL),
(1809, 52, 'A3', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:56', '2026-02-20 15:46:44', NULL, NULL),
(1810, 52, 'A4', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:56', '2026-02-20 15:46:44', NULL, NULL),
(1811, 52, 'B1', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:56', '2026-02-20 15:46:44', NULL, NULL),
(1812, 52, 'B2', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:56', '2026-02-20 15:46:44', NULL, NULL),
(1813, 52, 'B3', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:56', '2026-02-20 15:46:44', NULL, NULL),
(1814, 52, 'B4', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:56', '2026-02-20 15:46:44', NULL, NULL),
(1815, 52, 'C1', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:56', '2026-02-20 15:46:44', NULL, NULL),
(1816, 52, 'C2', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:56', '2026-02-20 15:46:44', NULL, NULL),
(1817, 52, 'C3', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:56', '2026-02-20 15:46:44', NULL, NULL),
(1818, 52, 'C4', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:56', '2026-02-20 15:46:44', NULL, NULL),
(1819, 52, 'D1', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:56', '2026-02-20 15:46:44', NULL, NULL),
(1820, 52, 'D2', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:56', '2026-02-20 15:46:44', NULL, NULL),
(1821, 52, 'D3', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:56', '2026-02-20 15:46:44', NULL, NULL),
(1822, 52, 'D4', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:56', '2026-02-20 15:46:44', NULL, NULL),
(1823, 52, 'E1', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:56', '2026-02-20 15:46:44', NULL, NULL),
(1824, 52, 'E2', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:56', '2026-02-20 15:46:44', NULL, NULL),
(1825, 52, 'E3', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:56', '2026-02-20 15:46:44', NULL, NULL),
(1826, 52, 'E4', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:56', '2026-02-20 15:46:44', NULL, NULL),
(1827, 53, 'A1', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:56', '2026-02-20 15:46:44', NULL, NULL),
(1828, 53, 'A2', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:56', '2026-02-20 15:46:44', NULL, NULL),
(1829, 53, 'A3', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:56', '2026-02-20 15:46:44', NULL, NULL),
(1830, 53, 'A4', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:56', '2026-02-20 15:46:44', NULL, NULL),
(1831, 53, 'B1', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:56', '2026-02-20 15:46:44', NULL, NULL),
(1832, 53, 'B2', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:56', '2026-02-20 15:46:44', NULL, NULL),
(1833, 53, 'B3', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:56', '2026-02-20 15:46:44', NULL, NULL),
(1834, 53, 'B4', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:56', '2026-02-20 15:46:44', NULL, NULL),
(1835, 53, 'C1', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:56', '2026-02-20 15:46:44', NULL, NULL),
(1836, 53, 'C2', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:56', '2026-02-20 15:46:44', NULL, NULL),
(1837, 53, 'C3', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:56', '2026-02-20 15:46:44', NULL, NULL),
(1838, 53, 'C4', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:56', '2026-02-20 15:46:44', NULL, NULL),
(1839, 53, 'D1', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:56', '2026-02-20 15:46:44', NULL, NULL),
(1840, 53, 'D2', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:57', '2026-02-20 15:46:44', NULL, NULL),
(1841, 53, 'D3', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:57', '2026-02-20 15:46:44', NULL, NULL),
(1842, 53, 'D4', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:57', '2026-02-20 15:46:44', NULL, NULL),
(1843, 53, 'E1', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:57', '2026-02-20 15:46:44', NULL, NULL),
(1844, 53, 'E2', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:57', '2026-02-20 15:46:44', NULL, NULL),
(1845, 53, 'E3', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:57', '2026-02-20 15:46:44', NULL, NULL),
(1846, 53, 'E4', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:57', '2026-02-20 15:46:44', NULL, NULL),
(1847, 54, 'A1', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:57', '2026-02-20 15:46:44', NULL, NULL),
(1848, 54, 'A2', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:57', '2026-02-20 15:46:44', NULL, NULL),
(1849, 54, 'A3', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:57', '2026-02-20 15:46:44', NULL, NULL),
(1850, 54, 'A4', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:57', '2026-02-20 15:46:44', NULL, NULL),
(1851, 54, 'B1', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:57', '2026-02-20 15:46:44', NULL, NULL),
(1852, 54, 'B2', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:57', '2026-02-20 15:46:44', NULL, NULL),
(1853, 54, 'B3', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:57', '2026-02-20 15:46:44', NULL, NULL),
(1854, 54, 'B4', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:57', '2026-02-20 15:46:44', NULL, NULL),
(1855, 54, 'C1', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:57', '2026-02-20 15:46:44', NULL, NULL),
(1856, 54, 'C2', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:57', '2026-02-20 15:46:44', NULL, NULL),
(1857, 54, 'C3', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:57', '2026-02-20 15:46:44', NULL, NULL),
(1858, 54, 'C4', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:57', '2026-02-20 15:46:44', NULL, NULL),
(1859, 54, 'D1', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:57', '2026-02-20 15:46:44', NULL, NULL),
(1860, 54, 'D2', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:57', '2026-02-20 15:46:44', NULL, NULL),
(1861, 54, 'D3', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:57', '2026-02-20 15:46:44', NULL, NULL),
(1862, 54, 'D4', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:57', '2026-02-20 15:46:44', NULL, NULL),
(1863, 54, 'E1', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:57', '2026-02-20 15:46:44', NULL, NULL),
(1864, 54, 'E2', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:57', '2026-02-20 15:46:44', NULL, NULL),
(1865, 54, 'E3', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:57', '2026-02-20 15:46:44', NULL, NULL),
(1866, 54, 'E4', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:57', '2026-02-20 15:46:44', NULL, NULL),
(1867, 55, 'A1', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:57', '2026-02-20 15:46:44', NULL, NULL),
(1868, 55, 'A2', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:57', '2026-02-20 15:46:44', NULL, NULL),
(1869, 55, 'A3', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:57', '2026-02-20 15:46:44', NULL, NULL),
(1870, 55, 'A4', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:57', '2026-02-20 15:46:44', NULL, NULL),
(1871, 55, 'B1', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:57', '2026-02-20 15:46:44', NULL, NULL),
(1872, 55, 'B2', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:57', '2026-02-20 15:46:44', NULL, NULL),
(1873, 55, 'B3', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:57', '2026-02-20 15:46:44', NULL, NULL),
(1874, 55, 'B4', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:57', '2026-02-20 15:46:44', NULL, NULL),
(1875, 55, 'C1', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:57', '2026-02-20 15:46:44', NULL, NULL),
(1876, 55, 'C2', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:57', '2026-02-20 15:46:44', NULL, NULL),
(1877, 55, 'C3', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:57', '2026-02-20 15:46:44', NULL, NULL),
(1878, 55, 'C4', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:57', '2026-02-20 15:46:44', NULL, NULL),
(1879, 55, 'D1', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:57', '2026-02-20 15:46:44', NULL, NULL),
(1880, 55, 'D2', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:57', '2026-02-20 15:46:44', NULL, NULL),
(1881, 55, 'D3', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:57', '2026-02-20 15:46:44', NULL, NULL),
(1882, 55, 'D4', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:57', '2026-02-20 15:46:44', NULL, NULL),
(1883, 55, 'E1', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:57', '2026-02-20 15:46:44', NULL, NULL),
(1884, 55, 'E2', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:57', '2026-02-20 15:46:44', NULL, NULL),
(1885, 55, 'E3', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:57', '2026-02-20 15:46:44', NULL, NULL),
(1886, 55, 'E4', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:57', '2026-02-20 15:46:44', NULL, NULL),
(1887, 56, 'A1', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:57', '2026-02-20 15:46:44', NULL, NULL),
(1888, 56, 'A2', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:57', '2026-02-20 15:46:44', NULL, NULL),
(1889, 56, 'A3', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:57', '2026-02-20 15:46:44', NULL, NULL),
(1890, 56, 'A4', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:57', '2026-02-20 15:46:44', NULL, NULL),
(1891, 56, 'B1', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:57', '2026-02-20 15:46:44', NULL, NULL),
(1892, 56, 'B2', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:57', '2026-02-20 15:46:44', NULL, NULL),
(1893, 56, 'B3', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:57', '2026-02-20 15:46:44', NULL, NULL),
(1894, 56, 'B4', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:57', '2026-02-20 15:46:44', NULL, NULL),
(1895, 56, 'C1', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:57', '2026-02-20 15:46:44', NULL, NULL),
(1896, 56, 'C2', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:57', '2026-02-20 15:46:44', NULL, NULL),
(1897, 56, 'C3', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:57', '2026-02-20 15:46:44', NULL, NULL),
(1898, 56, 'C4', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:57', '2026-02-20 15:46:44', NULL, NULL),
(1899, 56, 'D1', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:57', '2026-02-20 15:46:44', NULL, NULL),
(1900, 56, 'D2', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:57', '2026-02-20 15:46:44', NULL, NULL),
(1901, 56, 'D3', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:57', '2026-02-20 15:46:44', NULL, NULL),
(1902, 56, 'D4', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:57', '2026-02-20 15:46:44', NULL, NULL),
(1903, 56, 'E1', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:57', '2026-02-20 15:46:44', NULL, NULL),
(1904, 56, 'E2', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:57', '2026-02-20 15:46:44', NULL, NULL),
(1905, 56, 'E3', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:57', '2026-02-20 15:46:44', NULL, NULL),
(1906, 56, 'E4', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:57', '2026-02-20 15:46:44', NULL, NULL),
(1907, 57, 'A1', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:57', '2026-02-20 15:46:44', NULL, NULL),
(1908, 57, 'A2', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:57', '2026-02-20 15:46:44', NULL, NULL),
(1909, 57, 'A3', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:57', '2026-02-20 15:46:44', NULL, NULL),
(1910, 57, 'A4', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:57', '2026-02-20 15:46:44', NULL, NULL),
(1911, 57, 'B1', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:57', '2026-02-20 15:46:44', NULL, NULL),
(1912, 57, 'B2', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:57', '2026-02-20 15:46:44', NULL, NULL),
(1913, 57, 'B3', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:57', '2026-02-20 15:46:44', NULL, NULL),
(1914, 57, 'B4', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:57', '2026-02-20 15:46:44', NULL, NULL),
(1915, 57, 'C1', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:57', '2026-02-20 15:46:44', NULL, NULL),
(1916, 57, 'C2', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:57', '2026-02-20 15:46:44', NULL, NULL),
(1917, 57, 'C3', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:57', '2026-02-20 15:46:44', NULL, NULL),
(1918, 57, 'C4', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:57', '2026-02-20 15:46:44', NULL, NULL),
(1919, 57, 'D1', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:57', '2026-02-20 15:46:44', NULL, NULL),
(1920, 57, 'D2', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:57', '2026-02-20 15:46:44', NULL, NULL),
(1921, 57, 'D3', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:57', '2026-02-20 15:46:44', NULL, NULL),
(1922, 57, 'D4', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:57', '2026-02-20 15:46:44', NULL, NULL),
(1923, 57, 'E1', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:57', '2026-02-20 15:46:44', NULL, NULL),
(1924, 57, 'E2', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:57', '2026-02-20 15:46:44', NULL, NULL),
(1925, 57, 'E3', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:57', '2026-02-20 15:46:44', NULL, NULL),
(1926, 57, 'E4', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:57', '2026-02-20 15:46:44', NULL, NULL),
(1927, 58, 'A1', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:57', '2026-02-20 15:46:44', NULL, NULL),
(1928, 58, 'A2', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:57', '2026-02-20 15:46:44', NULL, NULL),
(1929, 58, 'A3', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:57', '2026-02-20 15:46:44', NULL, NULL),
(1930, 58, 'A4', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:57', '2026-02-20 15:46:44', NULL, NULL),
(1931, 58, 'B1', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:57', '2026-02-20 15:46:44', NULL, NULL),
(1932, 58, 'B2', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 15:46:44', NULL, NULL),
(1933, 58, 'B3', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 15:46:44', NULL, NULL),
(1934, 58, 'B4', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 15:46:44', NULL, NULL),
(1935, 58, 'C1', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 15:46:44', NULL, NULL),
(1936, 58, 'C2', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 15:46:44', NULL, NULL),
(1937, 58, 'C3', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 15:46:44', NULL, NULL),
(1938, 58, 'C4', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 15:46:44', NULL, NULL),
(1939, 58, 'D1', 'VENDIDO', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 17:32:32', 2, NULL),
(1940, 58, 'D2', 'VENDIDO', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 17:32:32', 2, NULL),
(1941, 58, 'D3', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 15:46:44', NULL, NULL),
(1942, 58, 'D4', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 15:46:44', NULL, NULL),
(1943, 58, 'E1', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 15:46:44', NULL, NULL),
(1944, 58, 'E2', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 15:46:44', NULL, NULL),
(1945, 58, 'E3', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 15:46:44', NULL, NULL),
(1946, 58, 'E4', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 15:46:44', NULL, NULL),
(1947, 59, 'A1', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 15:46:44', NULL, NULL),
(1948, 59, 'A2', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 15:46:44', NULL, NULL),
(1949, 59, 'A3', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 15:46:44', NULL, NULL),
(1950, 59, 'A4', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 15:46:44', NULL, NULL),
(1951, 59, 'B1', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 15:46:44', NULL, NULL),
(1952, 59, 'B2', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 15:46:44', NULL, NULL),
(1953, 59, 'B3', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 15:46:44', NULL, NULL),
(1954, 59, 'B4', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 15:46:44', NULL, NULL),
(1955, 59, 'C1', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 15:46:44', NULL, NULL),
(1956, 59, 'C2', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 15:46:44', NULL, NULL),
(1957, 59, 'C3', 'VENDIDO', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 16:14:22', 1, NULL),
(1958, 59, 'C4', 'VENDIDO', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 16:14:22', 1, NULL),
(1959, 59, 'D1', 'VENDIDO', NULL, NULL, '2026-02-17 16:22:58', '2026-02-21 01:36:38', 6, NULL),
(1960, 59, 'D2', 'VENDIDO', NULL, NULL, '2026-02-17 16:22:58', '2026-02-21 01:36:38', 6, NULL),
(1961, 59, 'D3', 'VENDIDO', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 16:14:22', 1, NULL),
(1962, 59, 'D4', 'VENDIDO', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 16:14:22', 1, NULL),
(1963, 59, 'E1', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 15:46:44', NULL, NULL),
(1964, 59, 'E2', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 15:46:44', NULL, NULL),
(1965, 59, 'E3', 'VENDIDO', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 18:11:01', 4, NULL),
(1966, 59, 'E4', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 15:46:44', NULL, NULL),
(1967, 60, 'A1', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 15:46:44', NULL, NULL),
(1968, 60, 'A2', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 15:46:44', NULL, NULL),
(1969, 60, 'A3', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 15:46:44', NULL, NULL),
(1970, 60, 'A4', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 15:46:44', NULL, NULL),
(1971, 60, 'B1', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 15:46:44', NULL, NULL),
(1972, 60, 'B2', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 15:46:44', NULL, NULL),
(1973, 60, 'B3', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 15:46:44', NULL, NULL),
(1974, 60, 'B4', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 15:46:44', NULL, NULL),
(1975, 60, 'C1', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 15:46:44', NULL, NULL),
(1976, 60, 'C2', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 15:46:44', NULL, NULL),
(1977, 60, 'C3', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 15:46:44', NULL, NULL),
(1978, 60, 'C4', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 15:46:44', NULL, NULL),
(1979, 60, 'D1', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 15:46:44', NULL, NULL),
(1980, 60, 'D2', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 15:46:44', NULL, NULL),
(1981, 60, 'D3', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 15:46:44', NULL, NULL),
(1982, 60, 'D4', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 15:46:44', NULL, NULL),
(1983, 60, 'E1', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 15:46:44', NULL, NULL),
(1984, 60, 'E2', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 15:46:44', NULL, NULL),
(1985, 60, 'E3', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 15:46:44', NULL, NULL),
(1986, 60, 'E4', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 15:46:44', NULL, NULL),
(1987, 61, 'A1', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 15:46:44', NULL, NULL),
(1988, 61, 'A2', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 15:46:44', NULL, NULL),
(1989, 61, 'A3', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 15:46:44', NULL, NULL),
(1990, 61, 'A4', 'VENDIDO', NULL, NULL, '2026-02-17 16:22:58', '2026-02-21 19:09:36', 21, NULL),
(1991, 61, 'B1', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 15:46:44', NULL, NULL),
(1992, 61, 'B2', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 15:46:44', NULL, NULL),
(1993, 61, 'B3', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 15:46:44', NULL, NULL),
(1994, 61, 'B4', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 15:46:44', NULL, NULL),
(1995, 61, 'C1', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 15:46:44', NULL, NULL),
(1996, 61, 'C2', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 15:46:44', NULL, NULL),
(1997, 61, 'C3', 'VENDIDO', NULL, NULL, '2026-02-17 16:22:58', '2026-02-21 19:09:36', 21, NULL),
(1998, 61, 'C4', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 15:46:44', NULL, NULL),
(1999, 61, 'D1', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 15:46:44', NULL, NULL),
(2000, 61, 'D2', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 15:46:44', NULL, NULL),
(2001, 61, 'D3', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 15:46:44', NULL, NULL),
(2002, 61, 'D4', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 15:46:44', NULL, NULL),
(2003, 61, 'E1', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 15:46:44', NULL, NULL),
(2004, 61, 'E2', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 15:46:44', NULL, NULL),
(2005, 61, 'E3', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 15:46:44', NULL, NULL),
(2006, 61, 'E4', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 15:46:44', NULL, NULL),
(2007, 62, 'A1', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 15:46:44', NULL, NULL),
(2008, 62, 'A2', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 15:46:44', NULL, NULL),
(2009, 62, 'A3', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 15:46:44', NULL, NULL),
(2010, 62, 'A4', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 15:46:44', NULL, NULL),
(2011, 62, 'B1', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 15:46:44', NULL, NULL),
(2012, 62, 'B2', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 15:46:44', NULL, NULL),
(2013, 62, 'B3', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 15:46:44', NULL, NULL),
(2014, 62, 'B4', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 15:46:44', NULL, NULL),
(2015, 62, 'C1', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 15:46:44', NULL, NULL),
(2016, 62, 'C2', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 15:46:44', NULL, NULL),
(2017, 62, 'C3', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 15:46:44', NULL, NULL),
(2018, 62, 'C4', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 15:46:44', NULL, NULL),
(2019, 62, 'D1', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 15:46:44', NULL, NULL),
(2020, 62, 'D2', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 15:46:44', NULL, NULL),
(2021, 62, 'D3', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 15:46:44', NULL, NULL),
(2022, 62, 'D4', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 15:46:44', NULL, NULL),
(2023, 62, 'E1', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 15:46:44', NULL, NULL),
(2024, 62, 'E2', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 15:46:44', NULL, NULL),
(2025, 62, 'E3', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 15:46:44', NULL, NULL),
(2026, 62, 'E4', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 15:46:44', NULL, NULL),
(2027, 63, 'A1', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 15:46:44', NULL, NULL),
(2028, 63, 'A2', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 15:46:44', NULL, NULL),
(2029, 63, 'A3', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 15:46:44', NULL, NULL),
(2030, 63, 'A4', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 15:46:44', NULL, NULL),
(2031, 63, 'B1', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 15:46:44', NULL, NULL),
(2032, 63, 'B2', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 15:46:44', NULL, NULL),
(2033, 63, 'B3', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 15:46:44', NULL, NULL),
(2034, 63, 'B4', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 15:46:44', NULL, NULL),
(2035, 63, 'C1', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:58', '2026-02-20 15:46:44', NULL, NULL),
(2036, 63, 'C2', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:59', '2026-02-20 15:46:44', NULL, NULL),
(2037, 63, 'C3', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:59', '2026-02-20 15:46:44', NULL, NULL),
(2038, 63, 'C4', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:59', '2026-02-20 15:46:44', NULL, NULL),
(2039, 63, 'D1', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:59', '2026-02-20 15:46:44', NULL, NULL),
(2040, 63, 'D2', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:59', '2026-02-20 15:46:44', NULL, NULL),
(2041, 63, 'D3', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:59', '2026-02-20 15:46:44', NULL, NULL),
(2042, 63, 'D4', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:59', '2026-02-20 15:46:44', NULL, NULL),
(2043, 63, 'E1', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:59', '2026-02-20 15:46:44', NULL, NULL),
(2044, 63, 'E2', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:59', '2026-02-20 15:46:44', NULL, NULL),
(2045, 63, 'E3', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:59', '2026-02-20 15:46:44', NULL, NULL),
(2046, 63, 'E4', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:59', '2026-02-20 15:46:44', NULL, NULL),
(2047, 64, 'A1', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:59', '2026-02-20 15:46:44', NULL, NULL),
(2048, 64, 'A2', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:59', '2026-02-20 15:46:44', NULL, NULL),
(2049, 64, 'A3', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:59', '2026-02-20 15:46:44', NULL, NULL),
(2050, 64, 'A4', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:59', '2026-02-20 15:46:44', NULL, NULL),
(2051, 64, 'B1', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:59', '2026-02-20 15:46:44', NULL, NULL),
(2052, 64, 'B2', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:59', '2026-02-20 15:46:44', NULL, NULL),
(2053, 64, 'B3', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:59', '2026-02-20 15:46:44', NULL, NULL),
(2054, 64, 'B4', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:59', '2026-02-20 15:46:44', NULL, NULL),
(2055, 64, 'C1', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:59', '2026-02-20 15:46:44', NULL, NULL),
(2056, 64, 'C2', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:59', '2026-02-20 15:46:44', NULL, NULL),
(2057, 64, 'C3', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:59', '2026-02-20 15:46:44', NULL, NULL),
(2058, 64, 'C4', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:59', '2026-02-20 15:46:44', NULL, NULL),
(2059, 64, 'D1', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:59', '2026-02-20 15:46:44', NULL, NULL),
(2060, 64, 'D2', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:59', '2026-02-20 15:46:44', NULL, NULL),
(2061, 64, 'D3', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:59', '2026-02-20 15:46:44', NULL, NULL),
(2062, 64, 'D4', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:59', '2026-02-20 15:46:44', NULL, NULL),
(2063, 64, 'E1', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:59', '2026-02-20 15:46:44', NULL, NULL),
(2064, 64, 'E2', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:59', '2026-02-20 15:46:44', NULL, NULL),
(2065, 64, 'E3', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:59', '2026-02-20 15:46:44', NULL, NULL),
(2066, 64, 'E4', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:59', '2026-02-20 15:46:44', NULL, NULL),
(2067, 65, 'A1', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:59', '2026-02-20 15:46:44', NULL, NULL),
(2068, 65, 'A2', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:59', '2026-02-20 15:46:44', NULL, NULL),
(2069, 65, 'A3', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:59', '2026-02-20 15:46:44', NULL, NULL),
(2070, 65, 'A4', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:59', '2026-02-20 15:46:44', NULL, NULL),
(2071, 65, 'B1', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:59', '2026-02-20 15:46:44', NULL, NULL),
(2072, 65, 'B2', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:59', '2026-02-20 15:46:44', NULL, NULL),
(2073, 65, 'B3', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:59', '2026-02-20 15:46:44', NULL, NULL),
(2074, 65, 'B4', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:59', '2026-02-20 15:46:44', NULL, NULL),
(2075, 65, 'C1', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:59', '2026-02-20 15:46:44', NULL, NULL),
(2076, 65, 'C2', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:59', '2026-02-20 15:46:44', NULL, NULL),
(2077, 65, 'C3', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:59', '2026-02-20 15:46:44', NULL, NULL),
(2078, 65, 'C4', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:59', '2026-02-20 15:46:44', NULL, NULL),
(2079, 65, 'D1', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:59', '2026-02-20 15:46:44', NULL, NULL),
(2080, 65, 'D2', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:59', '2026-02-20 15:46:44', NULL, NULL),
(2081, 65, 'D3', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:59', '2026-02-20 15:46:44', NULL, NULL),
(2082, 65, 'D4', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:59', '2026-02-20 15:46:44', NULL, NULL),
(2083, 65, 'E1', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:59', '2026-02-20 15:46:44', NULL, NULL),
(2084, 65, 'E2', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:59', '2026-02-20 15:46:44', NULL, NULL),
(2085, 65, 'E3', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:59', '2026-02-20 15:46:44', NULL, NULL),
(2086, 65, 'E4', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:59', '2026-02-20 15:46:44', NULL, NULL),
(2087, 66, 'A1', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:59', '2026-02-20 15:46:44', NULL, NULL),
(2088, 66, 'A2', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:59', '2026-02-20 15:46:44', NULL, NULL),
(2089, 66, 'A3', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:59', '2026-02-20 15:46:44', NULL, NULL);
INSERT INTO `funcion_asientos` (`id`, `funcion_id`, `codigo_asiento`, `estado`, `reservado_hasta`, `session_id`, `created_at`, `updated_at`, `venta_id`, `reservado_por`) VALUES
(2090, 66, 'A4', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:59', '2026-02-20 15:46:44', NULL, NULL),
(2091, 66, 'B1', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:59', '2026-02-20 15:46:44', NULL, NULL),
(2092, 66, 'B2', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:59', '2026-02-20 15:46:44', NULL, NULL),
(2093, 66, 'B3', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:59', '2026-02-20 15:46:44', NULL, NULL),
(2094, 66, 'B4', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:59', '2026-02-20 15:46:44', NULL, NULL),
(2095, 66, 'C1', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:59', '2026-02-20 15:46:44', NULL, NULL),
(2096, 66, 'C2', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:59', '2026-02-20 15:46:44', NULL, NULL),
(2097, 66, 'C3', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:59', '2026-02-20 15:46:44', NULL, NULL),
(2098, 66, 'C4', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:59', '2026-02-20 15:46:44', NULL, NULL),
(2099, 66, 'D1', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:59', '2026-02-20 15:46:44', NULL, NULL),
(2100, 66, 'D2', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:59', '2026-02-20 15:46:44', NULL, NULL),
(2101, 66, 'D3', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:59', '2026-02-20 15:46:44', NULL, NULL),
(2102, 66, 'D4', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:59', '2026-02-20 15:46:44', NULL, NULL),
(2103, 66, 'E1', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:59', '2026-02-20 15:46:44', NULL, NULL),
(2104, 66, 'E2', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:59', '2026-02-20 15:46:44', NULL, NULL),
(2105, 66, 'E3', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:59', '2026-02-20 15:46:44', NULL, NULL),
(2106, 66, 'E4', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:59', '2026-02-20 15:46:44', NULL, NULL),
(2107, 67, 'A1', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:59', '2026-02-20 15:46:44', NULL, NULL),
(2108, 67, 'A2', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:59', '2026-02-20 15:46:44', NULL, NULL),
(2109, 67, 'A3', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:59', '2026-02-20 15:46:44', NULL, NULL),
(2110, 67, 'A4', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:59', '2026-02-20 15:46:44', NULL, NULL),
(2111, 67, 'B1', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:59', '2026-02-20 15:46:44', NULL, NULL),
(2112, 67, 'B2', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:59', '2026-02-20 15:46:44', NULL, NULL),
(2113, 67, 'B3', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:59', '2026-02-20 15:46:44', NULL, NULL),
(2114, 67, 'B4', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:59', '2026-02-20 15:46:44', NULL, NULL),
(2115, 67, 'C1', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:59', '2026-02-20 15:46:44', NULL, NULL),
(2116, 67, 'C2', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:59', '2026-02-20 15:46:44', NULL, NULL),
(2117, 67, 'C3', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:59', '2026-02-20 15:46:44', NULL, NULL),
(2118, 67, 'C4', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:59', '2026-02-20 15:46:44', NULL, NULL),
(2119, 67, 'D1', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:59', '2026-02-20 15:46:44', NULL, NULL),
(2120, 67, 'D2', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:59', '2026-02-20 15:46:44', NULL, NULL),
(2121, 67, 'D3', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:59', '2026-02-20 15:46:44', NULL, NULL),
(2122, 67, 'D4', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:59', '2026-02-20 15:46:44', NULL, NULL),
(2123, 67, 'E1', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:59', '2026-02-20 15:46:44', NULL, NULL),
(2124, 67, 'E2', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:59', '2026-02-20 15:46:44', NULL, NULL),
(2125, 67, 'E3', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:59', '2026-02-20 15:46:44', NULL, NULL),
(2126, 67, 'E4', 'DISPONIBLE', NULL, NULL, '2026-02-17 16:22:59', '2026-02-20 15:46:44', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `funcion_precio`
--

DROP TABLE IF EXISTS `funcion_precio`;
CREATE TABLE IF NOT EXISTS `funcion_precio` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `funcion_id` bigint UNSIGNED NOT NULL,
  `precio_entrada_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `funcion_precio_funcion_id_foreign` (`funcion_id`),
  KEY `funcion_precio_precio_entrada_id_foreign` (`precio_entrada_id`)
) ENGINE=MyISAM AUTO_INCREMENT=76 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `funcion_precio`
--

INSERT INTO `funcion_precio` (`id`, `funcion_id`, `precio_entrada_id`, `created_at`, `updated_at`) VALUES
(1, 1, 2, NULL, NULL),
(2, 1, 3, NULL, NULL),
(3, 1, 4, NULL, NULL),
(4, 2, 2, NULL, NULL),
(5, 2, 3, NULL, NULL),
(6, 2, 4, NULL, NULL),
(7, 3, 2, NULL, NULL),
(8, 3, 3, NULL, NULL),
(9, 3, 4, NULL, NULL),
(10, 4, 2, NULL, NULL),
(11, 4, 3, NULL, NULL),
(12, 4, 4, NULL, NULL),
(13, 5, 2, NULL, NULL),
(14, 5, 3, NULL, NULL),
(15, 5, 4, NULL, NULL),
(16, 6, 2, NULL, NULL),
(17, 6, 3, NULL, NULL),
(18, 6, 4, NULL, NULL),
(19, 7, 2, NULL, NULL),
(20, 7, 3, NULL, NULL),
(21, 7, 4, NULL, NULL),
(22, 8, 2, NULL, NULL),
(23, 8, 3, NULL, NULL),
(24, 8, 4, NULL, NULL),
(25, 9, 2, NULL, NULL),
(26, 9, 3, NULL, NULL),
(27, 9, 4, NULL, NULL),
(28, 10, 2, NULL, NULL),
(29, 10, 3, NULL, NULL),
(30, 10, 4, NULL, NULL),
(31, 11, 2, NULL, NULL),
(32, 11, 3, NULL, NULL),
(33, 11, 4, NULL, NULL),
(34, 12, 2, NULL, NULL),
(35, 12, 3, NULL, NULL),
(36, 12, 4, NULL, NULL),
(37, 13, 1, NULL, NULL),
(38, 14, 1, NULL, NULL),
(39, 15, 1, NULL, NULL),
(40, 16, 1, NULL, NULL),
(41, 17, 1, NULL, NULL),
(42, 18, 1, NULL, NULL),
(43, 19, 1, NULL, NULL),
(44, 20, 1, NULL, NULL),
(45, 21, 1, NULL, NULL),
(46, 22, 1, NULL, NULL),
(47, 23, 1, NULL, NULL),
(48, 24, 1, NULL, NULL),
(49, 41, 1, NULL, NULL),
(50, 42, 1, NULL, NULL),
(51, 43, 1, NULL, NULL),
(52, 44, 1, NULL, NULL),
(53, 45, 1, NULL, NULL),
(54, 46, 1, NULL, NULL),
(55, 47, 1, NULL, NULL),
(56, 48, 1, NULL, NULL),
(57, 49, 1, NULL, NULL),
(58, 50, 1, NULL, NULL),
(59, 51, 1, NULL, NULL),
(60, 52, 1, NULL, NULL),
(61, 53, 1, NULL, NULL),
(62, 54, 1, NULL, NULL),
(63, 55, 1, NULL, NULL),
(64, 56, 1, NULL, NULL),
(65, 57, 1, NULL, NULL),
(66, 58, 1, NULL, NULL),
(67, 59, 1, NULL, NULL),
(68, 60, 1, NULL, NULL),
(69, 61, 1, NULL, NULL),
(70, 62, 1, NULL, NULL),
(71, 63, 1, NULL, NULL),
(72, 64, 1, NULL, NULL),
(73, 65, 1, NULL, NULL),
(74, 66, 1, NULL, NULL),
(75, 67, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `gastos_operacionales`
--

DROP TABLE IF EXISTS `gastos_operacionales`;
CREATE TABLE IF NOT EXISTS `gastos_operacionales` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint UNSIGNED NOT NULL,
  `nombre` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `monto` decimal(15,2) NOT NULL,
  `periodo` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_pago` date DEFAULT NULL,
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `gastos_operacionales_empresa_id_foreign` (`empresa_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `insumos`
--

DROP TABLE IF EXISTS `insumos`;
CREATE TABLE IF NOT EXISTS `insumos` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint UNSIGNED NOT NULL,
  `nombre` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `codigo` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unidad_medida` enum('kg','g','l','ml','und','oz','lb') COLLATE utf8mb4_unicode_ci DEFAULT 'und',
  `costo_unitario` decimal(10,2) NOT NULL DEFAULT '0.00',
  `stock_actual` decimal(15,3) NOT NULL,
  `stock_minimo` decimal(12,3) NOT NULL DEFAULT '0.000',
  `stock_seguridad` decimal(15,3) NOT NULL DEFAULT '0.000',
  `rendimiento` decimal(5,2) NOT NULL DEFAULT '100.00' COMMENT 'Porcentaje de aprovechamiento neto',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `insumos_empresa_id_foreign` (`empresa_id`)
) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `insumos`
--

INSERT INTO `insumos` (`id`, `empresa_id`, `nombre`, `codigo`, `unidad_medida`, `costo_unitario`, `stock_actual`, `stock_minimo`, `stock_seguridad`, `rendimiento`, `created_at`, `updated_at`) VALUES
(3, 1, 'Salsas y aderezos', NULL, 'g', 15.00, 2395.155, 50.000, 0.000, 100.00, '2026-02-16 16:01:32', '2026-02-21 19:09:36'),
(21, 1, 'Limón Fresco', NULL, 'und', 200.00, -9.000, 20.000, 0.000, 100.00, '2026-02-20 23:38:42', '2026-02-21 02:09:07'),
(20, 1, 'Piña Trozos', NULL, 'g', 5.00, -600.000, 500.000, 0.000, 100.00, '2026-02-20 23:38:42', '2026-02-21 19:11:14'),
(19, 1, 'Masa Pizza', NULL, 'und', 2000.00, -6.000, 10.000, 0.000, 100.00, '2026-02-20 23:37:53', '2026-02-21 19:11:14'),
(17, 1, 'Maíz Pira', NULL, 'kg', 5000.00, -3.200, 20.000, 0.000, 100.00, '2026-02-16 16:02:23', '2026-02-20 17:32:32'),
(18, 1, 'Aceite', NULL, 'ml', 8000.00, 1.650, 5000.000, 0.000, 100.00, '2026-02-16 16:02:23', '2026-02-20 17:32:33');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `insumo_lotes`
--

DROP TABLE IF EXISTS `insumo_lotes`;
CREATE TABLE IF NOT EXISTS `insumo_lotes` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `insumo_id` bigint UNSIGNED NOT NULL,
  `numero_lote` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cantidad_inicial` decimal(15,3) NOT NULL,
  `cantidad_actual` decimal(15,3) NOT NULL,
  `costo_unitario` decimal(15,2) NOT NULL,
  `fecha_vencimiento` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `insumo_lotes_insumo_id_foreign` (`insumo_id`)
) ENGINE=MyISAM AUTO_INCREMENT=167 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `insumo_lotes`
--

INSERT INTO `insumo_lotes` (`id`, `insumo_id`, `numero_lote`, `cantidad_inicial`, `cantidad_actual`, `costo_unitario`, `fecha_vencimiento`, `created_at`, `updated_at`) VALUES
(120, 17, 'ESTAB-20260211', 10.000, 0.000, 5000.00, '2027-02-11', '2026-02-11 18:34:40', '2026-02-14 19:05:10'),
(121, 18, 'ESTAB-20260211', 3.000, 1.550, 8000.00, '2027-02-11', '2026-02-11 18:34:40', '2026-02-20 17:32:33'),
(122, 13, 'ESTAB-20260211', 3158.000, 2336.946, 80.00, '2027-02-11', '2026-02-11 18:34:41', '2026-02-19 21:00:35'),
(123, 10, 'ESTAB-20260211', 53.000, 10.365, 3500.00, '2027-02-11', '2026-02-11 18:34:41', '2026-02-19 21:01:16'),
(124, 12, 'ESTAB-20260211', 7895.000, 6631.841, 60.00, '2027-02-11', '2026-02-11 18:34:41', '2026-02-16 15:26:49'),
(125, 11, 'ESTAB-20260211', 7895.000, 5052.894, 60.00, '2027-02-11', '2026-02-11 18:34:41', '2026-02-19 21:01:16'),
(126, 16, 'ESTAB-20260211', 7895.000, 7895.000, 20.00, '2027-02-11', '2026-02-11 18:34:41', '2026-02-11 18:34:41'),
(127, 8, 'ESTAB-20260211', 53.000, 0.000, 2000.00, '2027-02-11', '2026-02-11 18:34:41', '2026-02-19 19:46:40'),
(128, 14, 'ESTAB-20260211', 5264.000, 3264.001, 10.00, '2027-02-11', '2026-02-11 18:34:41', '2026-02-16 15:28:56'),
(129, 15, 'ESTAB-20260211', 1053.000, 652.999, 8.00, '2027-02-11', '2026-02-11 18:34:41', '2026-02-16 15:28:57'),
(130, 1, 'ESTAB-20260211', 53.000, 49.841, 1500.00, '2027-02-11', '2026-02-11 18:34:41', '2026-02-16 16:38:39'),
(131, 2, 'ESTAB-20260211', 53.000, 49.841, 2000.00, '2027-02-11', '2026-02-11 18:34:41', '2026-02-16 16:38:39'),
(132, 3, 'ESTAB-20260211', 2632.000, 1895.155, 15.00, '2027-02-11', '2026-02-11 18:34:41', '2026-02-21 19:09:36'),
(133, 4, 'ESTAB-20260211', 53.000, 45.629, 3000.00, '2027-02-11', '2026-02-11 18:34:42', '2026-02-16 15:28:56'),
(134, 5, 'ESTAB-20260211', 7895.000, 6789.735, 28.00, '2027-02-11', '2026-02-11 18:34:42', '2026-02-16 15:28:56'),
(135, 6, 'ESTAB-20260211', 5264.000, 4948.211, 25.00, '2027-02-11', '2026-02-11 18:34:42', '2026-02-16 15:26:50'),
(136, 7, 'ESTAB-20260211', 5264.000, 4527.159, 12.00, '2027-02-11', '2026-02-11 18:34:42', '2026-02-16 15:28:56'),
(137, 4, 'ESTAB-20260211', 53.000, 53.000, 3000.00, '2027-02-11', '2026-02-11 18:34:42', '2026-02-11 18:34:42'),
(138, 5, 'ESTAB-20260211', 7895.000, 7895.000, 28.00, '2027-02-11', '2026-02-11 18:34:42', '2026-02-11 18:34:42'),
(139, 7, 'ESTAB-20260211', 5264.000, 5264.000, 12.00, '2027-02-11', '2026-02-11 18:34:42', '2026-02-11 18:34:42'),
(140, 10, NULL, 100.000, 100.000, 3100.00, '2026-09-24', '2026-02-12 17:21:37', '2026-02-12 17:21:37'),
(141, 17, 'DEVOLUCIÓN VENTA #42', 0.200, 0.000, 5000.00, NULL, '2026-02-14 19:02:13', '2026-02-14 19:02:13'),
(142, 18, 'DEVOLUCIÓN VENTA #42', 0.050, 0.050, 8000.00, NULL, '2026-02-14 19:02:13', '2026-02-14 19:02:13'),
(143, 17, 'DEVOLUCIÓN VENTA #43', 0.200, -3.400, 5000.00, NULL, '2026-02-14 19:05:11', '2026-02-20 17:32:32'),
(144, 18, 'DEVOLUCIÓN VENTA #43', 0.050, 0.050, 8000.00, NULL, '2026-02-14 19:05:11', '2026-02-14 19:05:11'),
(145, 1, 'LOTE-SIM-1', 500.000, 500.000, 1500.00, '2027-02-16', '2026-02-16 16:01:31', '2026-02-16 16:01:31'),
(146, 2, 'LOTE-SIM-2', 500.000, 500.000, 2000.00, '2027-02-16', '2026-02-16 16:01:32', '2026-02-16 16:01:32'),
(147, 3, 'LOTE-SIM-3', 500.000, 500.000, 15.00, '2027-02-16', '2026-02-16 16:01:32', '2026-02-16 16:01:32'),
(148, 4, 'LOTE-SIM-4', 500.000, 500.000, 3000.00, '2027-02-16', '2026-02-16 16:01:32', '2026-02-16 16:01:32'),
(149, 5, 'LOTE-SIM-5', 500.000, 500.000, 28.00, '2027-02-16', '2026-02-16 16:01:32', '2026-02-16 16:01:32'),
(150, 6, 'LOTE-SIM-6', 500.000, 500.000, 25.00, '2027-02-16', '2026-02-16 16:01:32', '2026-02-16 16:01:32'),
(151, 7, 'LOTE-SIM-7', 500.000, 500.000, 12.00, '2027-02-16', '2026-02-16 16:01:32', '2026-02-16 16:01:32'),
(152, 8, 'LOTE-SIM-8', 500.000, 491.945, 2000.00, '2027-02-16', '2026-02-16 16:01:32', '2026-02-19 19:46:40'),
(153, 9, 'LOTE-SIM-9', 500.000, 500.000, 1500.00, '2027-02-16', '2026-02-16 16:01:32', '2026-02-16 16:01:32'),
(154, 10, 'LOTE-SIM-10', 500.000, 500.000, 3500.00, '2027-02-16', '2026-02-16 16:01:32', '2026-02-16 16:01:32'),
(155, 11, 'LOTE-SIM-11', 500.000, 500.000, 60.00, '2027-02-16', '2026-02-16 16:01:32', '2026-02-16 16:01:32'),
(156, 12, 'LOTE-SIM-12', 500.000, 500.000, 60.00, '2027-02-16', '2026-02-16 16:01:32', '2026-02-16 16:01:32'),
(157, 13, 'LOTE-SIM-13', 500.000, 500.000, 80.00, '2027-02-16', '2026-02-16 16:01:32', '2026-02-16 16:01:32'),
(158, 14, 'LOTE-SIM-14', 500.000, 500.000, 10.00, '2027-02-16', '2026-02-16 16:01:32', '2026-02-16 16:01:32'),
(159, 15, 'LOTE-SIM-15', 500.000, 500.000, 8.00, '2027-02-16', '2026-02-16 16:01:32', '2026-02-16 16:01:32'),
(160, 16, 'LOTE-SIM-16', 500.000, 500.000, 20.00, '2027-02-16', '2026-02-16 16:01:32', '2026-02-16 16:01:32'),
(161, 8, '125', 100.000, 100.000, 3500.00, '2026-02-28', '2026-02-19 19:48:21', '2026-02-19 19:48:21'),
(162, 1, '125', 150.000, 150.000, 3500.00, '2026-02-26', '2026-02-19 20:25:52', '2026-02-19 20:25:52'),
(163, 1, '125', 150.000, 150.000, 3500.00, '2026-02-26', '2026-02-19 20:25:55', '2026-02-19 20:25:55'),
(164, 19, 'AJUSTE-NEGATIVO-INICIAL', 0.000, -7.000, 2000.00, NULL, '2026-02-21 01:36:38', '2026-02-21 19:11:14'),
(165, 20, 'AJUSTE-NEGATIVO-INICIAL', 0.000, -700.000, 5.00, NULL, '2026-02-21 01:36:39', '2026-02-21 19:11:14'),
(166, 21, 'AJUSTE-NEGATIVO-INICIAL', 0.000, -18.000, 200.00, NULL, '2026-02-21 01:57:49', '2026-02-21 02:09:07');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `insumo_salidas`
--

DROP TABLE IF EXISTS `insumo_salidas`;
CREATE TABLE IF NOT EXISTS `insumo_salidas` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `insumo_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `cantidad` decimal(15,3) NOT NULL,
  `tipo` enum('baja','cortesia','merma','ajuste_inventario') COLLATE utf8mb4_unicode_ci NOT NULL,
  `motivo` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `costo_estimado` decimal(15,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `insumo_salidas_insumo_id_foreign` (`insumo_id`),
  KEY `insumo_salidas_user_id_foreign` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `insumo_salidas`
--

INSERT INTO `insumo_salidas` (`id`, `insumo_id`, `user_id`, `cantidad`, `tipo`, `motivo`, `costo_estimado`, `created_at`, `updated_at`) VALUES
(1, 4, 1, 1000.000, 'merma', 'merma', 12500000.00, '2026-02-07 00:48:16', '2026-02-07 00:48:16'),
(2, 10, 1, 10.000, 'cortesia', 'cortesia', 31000.00, '2026-02-12 17:25:08', '2026-02-12 17:25:08');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inventario`
--

DROP TABLE IF EXISTS `inventario`;
CREATE TABLE IF NOT EXISTS `inventario` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint UNSIGNED NOT NULL,
  `producto_id` bigint UNSIGNED NOT NULL,
  `ubicacione_id` bigint UNSIGNED NOT NULL,
  `cantidad` int NOT NULL,
  `cantidad_minima` int UNSIGNED DEFAULT NULL,
  `cantidad_maxima` int UNSIGNED DEFAULT NULL,
  `fecha_vencimiento` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `inventario_producto_id_unique` (`producto_id`),
  KEY `inventario_ubicacione_id_foreign` (`ubicacione_id`),
  KEY `inventario_empresa_id_index` (`empresa_id`),
  KEY `idx_inventario_producto_empresa` (`producto_id`,`empresa_id`),
  KEY `idx_inventario_empresa_cantidad` (`empresa_id`,`cantidad`)
) ENGINE=MyISAM AUTO_INCREMENT=118 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `inventario`
--

INSERT INTO `inventario` (`id`, `empresa_id`, `producto_id`, `ubicacione_id`, `cantidad`, `cantidad_minima`, `cantidad_maxima`, `fecha_vencimiento`, `created_at`, `updated_at`) VALUES
(116, 1, 85, 1, 50, 5, NULL, NULL, '2026-02-20 23:37:53', '2026-02-20 23:37:53'),
(115, 1, 82, 1, 48, 10, NULL, NULL, '2026-02-16 16:01:34', '2026-02-21 22:03:33'),
(114, 1, 81, 1, 49, 10, NULL, NULL, '2026-02-16 16:01:34', '2026-02-20 17:33:34'),
(113, 1, 80, 1, 45, 10, NULL, NULL, '2026-02-16 16:01:34', '2026-02-21 16:00:40'),
(112, 1, 79, 1, 45, 10, NULL, NULL, '2026-02-16 16:01:34', '2026-02-21 16:00:40'),
(111, 1, 78, 1, 38, 10, NULL, NULL, '2026-02-16 16:01:34', '2026-02-21 16:00:40'),
(110, 1, 77, 1, 44, 10, NULL, NULL, '2026-02-16 16:01:34', '2026-02-21 16:01:14'),
(109, 1, 76, 1, 44, 10, NULL, NULL, '2026-02-16 16:01:34', '2026-02-21 19:09:36'),
(108, 1, 75, 1, 31, 10, NULL, NULL, '2026-02-16 16:01:33', '2026-02-21 02:09:07'),
(107, 1, 74, 1, 40, 10, NULL, NULL, '2026-02-16 16:01:33', '2026-02-21 16:00:39'),
(106, 1, 73, 1, 50, 10, NULL, NULL, '2026-02-16 16:01:33', '2026-02-16 16:01:33'),
(117, 1, 86, 1, 100, 10, NULL, NULL, '2026-02-20 23:38:42', '2026-02-20 23:38:42');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inventario_ajustes`
--

DROP TABLE IF EXISTS `inventario_ajustes`;
CREATE TABLE IF NOT EXISTS `inventario_ajustes` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `insumo_id` bigint UNSIGNED DEFAULT NULL,
  `producto_id` bigint UNSIGNED DEFAULT NULL,
  `cantidad` decimal(12,3) NOT NULL,
  `tipo` enum('INCREMENTO','DECREMENTO') COLLATE utf8mb4_unicode_ci NOT NULL,
  `motivo` enum('merma','daño','error conteo','vencimiento','otro') COLLATE utf8mb4_unicode_ci NOT NULL,
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `inventario_ajustes_empresa_id_foreign` (`empresa_id`),
  KEY `inventario_ajustes_user_id_foreign` (`user_id`),
  KEY `inventario_ajustes_insumo_id_foreign` (`insumo_id`),
  KEY `inventario_ajustes_producto_id_foreign` (`producto_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inventario_movimientos`
--

DROP TABLE IF EXISTS `inventario_movimientos`;
CREATE TABLE IF NOT EXISTS `inventario_movimientos` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint UNSIGNED NOT NULL,
  `producto_id` bigint UNSIGNED NOT NULL,
  `factura_id` bigint UNSIGNED NOT NULL,
  `cantidad` decimal(12,2) NOT NULL,
  `costo_unitario` decimal(12,4) NOT NULL,
  `origen` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'FACTURA_COMPRA',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `inventario_movimientos_empresa_id_foreign` (`empresa_id`),
  KEY `inventario_movimientos_producto_id_foreign` (`producto_id`),
  KEY `inventario_movimientos_factura_id_foreign` (`factura_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `jobs`
--

DROP TABLE IF EXISTS `jobs`;
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `queue` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=MyISAM AUTO_INCREMENT=79 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `jobs`
--

INSERT INTO `jobs` (`id`, `queue`, `payload`, `attempts`, `reserved_at`, `available_at`, `created_at`) VALUES
(1, 'default', '{\"uuid\":\"bdf9db8f-be16-46d7-b329-f1696ec0fbc7\",\"displayName\":\"App\\\\Listeners\\\\EmitirDocumentoFiscalListener\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Events\\\\CallQueuedListener\",\"command\":\"O:36:\\\"Illuminate\\\\Events\\\\CallQueuedListener\\\":20:{s:5:\\\"class\\\";s:43:\\\"App\\\\Listeners\\\\EmitirDocumentoFiscalListener\\\";s:6:\\\"method\\\";s:6:\\\"handle\\\";s:4:\\\"data\\\";a:1:{i:0;O:27:\\\"App\\\\Events\\\\CreateVentaEvent\\\":1:{s:5:\\\"venta\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:16:\\\"App\\\\Models\\\\Venta\\\";s:2:\\\"id\\\";i:25;s:9:\\\"relations\\\";a:2:{i:0;s:9:\\\"productos\\\";i:1;s:17:\\\"productos.insumos\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}}}s:5:\\\"tries\\\";N;s:13:\\\"maxExceptions\\\";N;s:7:\\\"backoff\\\";N;s:10:\\\"retryUntil\\\";N;s:7:\\\"timeout\\\";N;s:13:\\\"failOnTimeout\\\";b:0;s:17:\\\"shouldBeEncrypted\\\";b:0;s:3:\\\"job\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\"},\"createdAt\":1771540380,\"delay\":null}', 0, NULL, 1771540380, 1771540380),
(2, 'default', '{\"uuid\":\"a826dd4a-b6b7-4c80-a58b-7e13c873345a\",\"displayName\":\"App\\\\Jobs\\\\EnviarComprobanteVentaJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\EnviarComprobanteVentaJob\",\"command\":\"O:34:\\\"App\\\\Jobs\\\\EnviarComprobanteVentaJob\\\":2:{s:10:\\\"\\u0000*\\u0000ventaId\\\";i:25;s:11:\\\"afterCommit\\\";b:1;}\"},\"createdAt\":1771540380,\"delay\":null}', 0, NULL, 1771540381, 1771540381),
(3, 'default', '{\"uuid\":\"b65ca9ec-ac4d-4d26-84f1-9b64717f3a2c\",\"displayName\":\"App\\\\Listeners\\\\EmitirDocumentoFiscalListener\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Events\\\\CallQueuedListener\",\"command\":\"O:36:\\\"Illuminate\\\\Events\\\\CallQueuedListener\\\":20:{s:5:\\\"class\\\";s:43:\\\"App\\\\Listeners\\\\EmitirDocumentoFiscalListener\\\";s:6:\\\"method\\\";s:6:\\\"handle\\\";s:4:\\\"data\\\";a:1:{i:0;O:27:\\\"App\\\\Events\\\\CreateVentaEvent\\\":1:{s:5:\\\"venta\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:16:\\\"App\\\\Models\\\\Venta\\\";s:2:\\\"id\\\";i:26;s:9:\\\"relations\\\";a:2:{i:0;s:9:\\\"productos\\\";i:1;s:17:\\\"productos.insumos\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}}}s:5:\\\"tries\\\";N;s:13:\\\"maxExceptions\\\";N;s:7:\\\"backoff\\\";N;s:10:\\\"retryUntil\\\";N;s:7:\\\"timeout\\\";N;s:13:\\\"failOnTimeout\\\";b:0;s:17:\\\"shouldBeEncrypted\\\";b:0;s:3:\\\"job\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\"},\"createdAt\":1771542111,\"delay\":null}', 0, NULL, 1771542111, 1771542111),
(4, 'default', '{\"uuid\":\"e14178a2-53cf-4d6a-acd9-565cb7adb632\",\"displayName\":\"App\\\\Jobs\\\\EnviarComprobanteVentaJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\EnviarComprobanteVentaJob\",\"command\":\"O:34:\\\"App\\\\Jobs\\\\EnviarComprobanteVentaJob\\\":2:{s:10:\\\"\\u0000*\\u0000ventaId\\\";i:26;s:11:\\\"afterCommit\\\";b:1;}\"},\"createdAt\":1771542111,\"delay\":null}', 0, NULL, 1771542111, 1771542111),
(5, 'default', '{\"uuid\":\"fcd7f90e-72aa-48bf-9361-5ffd61269ec4\",\"displayName\":\"App\\\\Listeners\\\\EmitirDocumentoFiscalListener\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Events\\\\CallQueuedListener\",\"command\":\"O:36:\\\"Illuminate\\\\Events\\\\CallQueuedListener\\\":20:{s:5:\\\"class\\\";s:43:\\\"App\\\\Listeners\\\\EmitirDocumentoFiscalListener\\\";s:6:\\\"method\\\";s:6:\\\"handle\\\";s:4:\\\"data\\\";a:1:{i:0;O:27:\\\"App\\\\Events\\\\CreateVentaEvent\\\":1:{s:5:\\\"venta\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:16:\\\"App\\\\Models\\\\Venta\\\";s:2:\\\"id\\\";i:27;s:9:\\\"relations\\\";a:2:{i:0;s:9:\\\"productos\\\";i:1;s:17:\\\"productos.insumos\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}}}s:5:\\\"tries\\\";N;s:13:\\\"maxExceptions\\\";N;s:7:\\\"backoff\\\";N;s:10:\\\"retryUntil\\\";N;s:7:\\\"timeout\\\";N;s:13:\\\"failOnTimeout\\\";b:0;s:17:\\\"shouldBeEncrypted\\\";b:0;s:3:\\\"job\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\"},\"createdAt\":1771550008,\"delay\":null}', 0, NULL, 1771550008, 1771550008),
(6, 'default', '{\"uuid\":\"62697c5c-db84-4ce4-8b9c-8a98b7fc960b\",\"displayName\":\"App\\\\Jobs\\\\EnviarComprobanteVentaJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\EnviarComprobanteVentaJob\",\"command\":\"O:34:\\\"App\\\\Jobs\\\\EnviarComprobanteVentaJob\\\":2:{s:10:\\\"\\u0000*\\u0000ventaId\\\";i:27;s:11:\\\"afterCommit\\\";b:1;}\"},\"createdAt\":1771550008,\"delay\":null}', 0, NULL, 1771550008, 1771550008),
(7, 'fiscal', '{\"uuid\":\"4410a3e2-b299-479b-93c2-8c2798bb974f\",\"displayName\":\"App\\\\Jobs\\\\EmitirDocumentoFiscalJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":5,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":\"10,30,60,120\",\"timeout\":120,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\EmitirDocumentoFiscalJob\",\"command\":\"O:33:\\\"App\\\\Jobs\\\\EmitirDocumentoFiscalJob\\\":3:{s:10:\\\"\\u0000*\\u0000ventaId\\\";i:27;s:5:\\\"queue\\\";s:6:\\\"fiscal\\\";s:5:\\\"delay\\\";O:25:\\\"Illuminate\\\\Support\\\\Carbon\\\":3:{s:4:\\\"date\\\";s:26:\\\"2026-02-19 20:13:31.196597\\\";s:13:\\\"timezone_type\\\";i:3;s:8:\\\"timezone\\\";s:12:\\\"America\\/Lima\\\";}}\"},\"createdAt\":1771550008,\"delay\":3}', 0, NULL, 1771550011, 1771550008),
(8, 'default', '{\"uuid\":\"00defd65-fd13-45cf-83d2-6755db129e7c\",\"displayName\":\"App\\\\Listeners\\\\EmitirDocumentoFiscalListener\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Events\\\\CallQueuedListener\",\"command\":\"O:36:\\\"Illuminate\\\\Events\\\\CallQueuedListener\\\":20:{s:5:\\\"class\\\";s:43:\\\"App\\\\Listeners\\\\EmitirDocumentoFiscalListener\\\";s:6:\\\"method\\\";s:6:\\\"handle\\\";s:4:\\\"data\\\";a:1:{i:0;O:27:\\\"App\\\\Events\\\\CreateVentaEvent\\\":1:{s:5:\\\"venta\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:16:\\\"App\\\\Models\\\\Venta\\\";s:2:\\\"id\\\";i:28;s:9:\\\"relations\\\";a:1:{i:0;s:9:\\\"productos\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}}}s:5:\\\"tries\\\";N;s:13:\\\"maxExceptions\\\";N;s:7:\\\"backoff\\\";N;s:10:\\\"retryUntil\\\";N;s:7:\\\"timeout\\\";N;s:13:\\\"failOnTimeout\\\";b:0;s:17:\\\"shouldBeEncrypted\\\";b:0;s:3:\\\"job\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\"},\"createdAt\":1771550037,\"delay\":null}', 0, NULL, 1771550037, 1771550037),
(9, 'default', '{\"uuid\":\"dbee4789-7c4e-4552-be9b-ff332240982d\",\"displayName\":\"App\\\\Jobs\\\\EnviarComprobanteVentaJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\EnviarComprobanteVentaJob\",\"command\":\"O:34:\\\"App\\\\Jobs\\\\EnviarComprobanteVentaJob\\\":2:{s:10:\\\"\\u0000*\\u0000ventaId\\\";i:28;s:11:\\\"afterCommit\\\";b:1;}\"},\"createdAt\":1771550037,\"delay\":null}', 0, NULL, 1771550037, 1771550037),
(10, 'fiscal', '{\"uuid\":\"b50bd4fb-133c-4e57-bb66-24ff460d9700\",\"displayName\":\"App\\\\Jobs\\\\EmitirDocumentoFiscalJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":5,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":\"10,30,60,120\",\"timeout\":120,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\EmitirDocumentoFiscalJob\",\"command\":\"O:33:\\\"App\\\\Jobs\\\\EmitirDocumentoFiscalJob\\\":3:{s:10:\\\"\\u0000*\\u0000ventaId\\\";i:28;s:5:\\\"queue\\\";s:6:\\\"fiscal\\\";s:5:\\\"delay\\\";O:25:\\\"Illuminate\\\\Support\\\\Carbon\\\":3:{s:4:\\\"date\\\";s:26:\\\"2026-02-19 20:14:00.368599\\\";s:13:\\\"timezone_type\\\";i:3;s:8:\\\"timezone\\\";s:12:\\\"America\\/Lima\\\";}}\"},\"createdAt\":1771550037,\"delay\":3}', 0, NULL, 1771550040, 1771550037),
(11, 'default', '{\"uuid\":\"a4443f0e-35a3-42e6-9e63-72a8cff9aed3\",\"displayName\":\"App\\\\Listeners\\\\EmitirDocumentoFiscalListener\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Events\\\\CallQueuedListener\",\"command\":\"O:36:\\\"Illuminate\\\\Events\\\\CallQueuedListener\\\":20:{s:5:\\\"class\\\";s:43:\\\"App\\\\Listeners\\\\EmitirDocumentoFiscalListener\\\";s:6:\\\"method\\\";s:6:\\\"handle\\\";s:4:\\\"data\\\";a:1:{i:0;O:27:\\\"App\\\\Events\\\\CreateVentaEvent\\\":1:{s:5:\\\"venta\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:16:\\\"App\\\\Models\\\\Venta\\\";s:2:\\\"id\\\";i:29;s:9:\\\"relations\\\";a:2:{i:0;s:9:\\\"productos\\\";i:1;s:17:\\\"productos.insumos\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}}}s:5:\\\"tries\\\";N;s:13:\\\"maxExceptions\\\";N;s:7:\\\"backoff\\\";N;s:10:\\\"retryUntil\\\";N;s:7:\\\"timeout\\\";N;s:13:\\\"failOnTimeout\\\";b:0;s:17:\\\"shouldBeEncrypted\\\";b:0;s:3:\\\"job\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\"},\"createdAt\":1771550120,\"delay\":null}', 0, NULL, 1771550120, 1771550120),
(12, 'default', '{\"uuid\":\"0382fb50-6781-40bb-9a6a-dd96a398fc87\",\"displayName\":\"App\\\\Jobs\\\\EnviarComprobanteVentaJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\EnviarComprobanteVentaJob\",\"command\":\"O:34:\\\"App\\\\Jobs\\\\EnviarComprobanteVentaJob\\\":2:{s:10:\\\"\\u0000*\\u0000ventaId\\\";i:29;s:11:\\\"afterCommit\\\";b:1;}\"},\"createdAt\":1771550120,\"delay\":null}', 0, NULL, 1771550120, 1771550120),
(13, 'fiscal', '{\"uuid\":\"3b0ed4bf-8db7-4778-aaee-625e526d8a76\",\"displayName\":\"App\\\\Jobs\\\\EmitirDocumentoFiscalJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":5,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":\"10,30,60,120\",\"timeout\":120,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\EmitirDocumentoFiscalJob\",\"command\":\"O:33:\\\"App\\\\Jobs\\\\EmitirDocumentoFiscalJob\\\":3:{s:10:\\\"\\u0000*\\u0000ventaId\\\";i:29;s:5:\\\"queue\\\";s:6:\\\"fiscal\\\";s:5:\\\"delay\\\";O:25:\\\"Illuminate\\\\Support\\\\Carbon\\\":3:{s:4:\\\"date\\\";s:26:\\\"2026-02-19 20:15:23.059223\\\";s:13:\\\"timezone_type\\\";i:3;s:8:\\\"timezone\\\";s:12:\\\"America\\/Lima\\\";}}\"},\"createdAt\":1771550120,\"delay\":3}', 0, NULL, 1771550123, 1771550120),
(14, 'default', '{\"uuid\":\"321a129c-efc4-4e72-921a-fed334867ef9\",\"displayName\":\"App\\\\Listeners\\\\EmitirDocumentoFiscalListener\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Events\\\\CallQueuedListener\",\"command\":\"O:36:\\\"Illuminate\\\\Events\\\\CallQueuedListener\\\":20:{s:5:\\\"class\\\";s:43:\\\"App\\\\Listeners\\\\EmitirDocumentoFiscalListener\\\";s:6:\\\"method\\\";s:6:\\\"handle\\\";s:4:\\\"data\\\";a:1:{i:0;O:27:\\\"App\\\\Events\\\\CreateVentaEvent\\\":1:{s:5:\\\"venta\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:16:\\\"App\\\\Models\\\\Venta\\\";s:2:\\\"id\\\";i:30;s:9:\\\"relations\\\";a:2:{i:0;s:9:\\\"productos\\\";i:1;s:17:\\\"productos.insumos\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}}}s:5:\\\"tries\\\";N;s:13:\\\"maxExceptions\\\";N;s:7:\\\"backoff\\\";N;s:10:\\\"retryUntil\\\";N;s:7:\\\"timeout\\\";N;s:13:\\\"failOnTimeout\\\";b:0;s:17:\\\"shouldBeEncrypted\\\";b:0;s:3:\\\"job\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\"},\"createdAt\":1771550144,\"delay\":null}', 0, NULL, 1771550144, 1771550144),
(15, 'default', '{\"uuid\":\"184f3d1d-8d96-48e9-8531-113b67632e43\",\"displayName\":\"App\\\\Jobs\\\\EnviarComprobanteVentaJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\EnviarComprobanteVentaJob\",\"command\":\"O:34:\\\"App\\\\Jobs\\\\EnviarComprobanteVentaJob\\\":2:{s:10:\\\"\\u0000*\\u0000ventaId\\\";i:30;s:11:\\\"afterCommit\\\";b:1;}\"},\"createdAt\":1771550144,\"delay\":null}', 0, NULL, 1771550144, 1771550144),
(16, 'fiscal', '{\"uuid\":\"3de93f5b-86c6-456a-aea1-ae06a3b4cabf\",\"displayName\":\"App\\\\Jobs\\\\EmitirDocumentoFiscalJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":5,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":\"10,30,60,120\",\"timeout\":120,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\EmitirDocumentoFiscalJob\",\"command\":\"O:33:\\\"App\\\\Jobs\\\\EmitirDocumentoFiscalJob\\\":3:{s:10:\\\"\\u0000*\\u0000ventaId\\\";i:30;s:5:\\\"queue\\\";s:6:\\\"fiscal\\\";s:5:\\\"delay\\\";O:25:\\\"Illuminate\\\\Support\\\\Carbon\\\":3:{s:4:\\\"date\\\";s:26:\\\"2026-02-19 20:15:47.458929\\\";s:13:\\\"timezone_type\\\";i:3;s:8:\\\"timezone\\\";s:12:\\\"America\\/Lima\\\";}}\"},\"createdAt\":1771550144,\"delay\":3}', 0, NULL, 1771550147, 1771550144),
(17, 'default', '{\"uuid\":\"5a7f0538-e526-4221-91ed-f87adec2bc94\",\"displayName\":\"App\\\\Listeners\\\\EmitirDocumentoFiscalListener\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Events\\\\CallQueuedListener\",\"command\":\"O:36:\\\"Illuminate\\\\Events\\\\CallQueuedListener\\\":20:{s:5:\\\"class\\\";s:43:\\\"App\\\\Listeners\\\\EmitirDocumentoFiscalListener\\\";s:6:\\\"method\\\";s:6:\\\"handle\\\";s:4:\\\"data\\\";a:1:{i:0;O:27:\\\"App\\\\Events\\\\CreateVentaEvent\\\":1:{s:5:\\\"venta\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:16:\\\"App\\\\Models\\\\Venta\\\";s:2:\\\"id\\\";i:31;s:9:\\\"relations\\\";a:2:{i:0;s:9:\\\"productos\\\";i:1;s:17:\\\"productos.insumos\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}}}s:5:\\\"tries\\\";N;s:13:\\\"maxExceptions\\\";N;s:7:\\\"backoff\\\";N;s:10:\\\"retryUntil\\\";N;s:7:\\\"timeout\\\";N;s:13:\\\"failOnTimeout\\\";b:0;s:17:\\\"shouldBeEncrypted\\\";b:0;s:3:\\\"job\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\"},\"createdAt\":1771599385,\"delay\":null}', 0, NULL, 1771599386, 1771599386),
(18, 'default', '{\"uuid\":\"14e44fd2-b061-45d1-bf79-c14c786246c7\",\"displayName\":\"App\\\\Jobs\\\\EnviarComprobanteVentaJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\EnviarComprobanteVentaJob\",\"command\":\"O:34:\\\"App\\\\Jobs\\\\EnviarComprobanteVentaJob\\\":2:{s:10:\\\"\\u0000*\\u0000ventaId\\\";i:31;s:11:\\\"afterCommit\\\";b:1;}\"},\"createdAt\":1771599385,\"delay\":null}', 0, NULL, 1771599386, 1771599386),
(19, 'fiscal', '{\"uuid\":\"ff4caf0c-4e20-4b25-b056-c824e353e371\",\"displayName\":\"App\\\\Jobs\\\\EmitirDocumentoFiscalJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":5,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":\"10,30,60,120\",\"timeout\":120,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\EmitirDocumentoFiscalJob\",\"command\":\"O:33:\\\"App\\\\Jobs\\\\EmitirDocumentoFiscalJob\\\":3:{s:10:\\\"\\u0000*\\u0000ventaId\\\";i:31;s:5:\\\"queue\\\";s:6:\\\"fiscal\\\";s:5:\\\"delay\\\";O:25:\\\"Illuminate\\\\Support\\\\Carbon\\\":3:{s:4:\\\"date\\\";s:26:\\\"2026-02-20 09:56:29.477549\\\";s:13:\\\"timezone_type\\\";i:3;s:8:\\\"timezone\\\";s:12:\\\"America\\/Lima\\\";}}\"},\"createdAt\":1771599386,\"delay\":3}', 0, NULL, 1771599389, 1771599386),
(20, 'default', '{\"uuid\":\"8d000632-9794-4568-a8b2-ffb300893f99\",\"displayName\":\"App\\\\Listeners\\\\EmitirDocumentoFiscalListener\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Events\\\\CallQueuedListener\",\"command\":\"O:36:\\\"Illuminate\\\\Events\\\\CallQueuedListener\\\":20:{s:5:\\\"class\\\";s:43:\\\"App\\\\Listeners\\\\EmitirDocumentoFiscalListener\\\";s:6:\\\"method\\\";s:6:\\\"handle\\\";s:4:\\\"data\\\";a:1:{i:0;O:27:\\\"App\\\\Events\\\\CreateVentaEvent\\\":1:{s:5:\\\"venta\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:16:\\\"App\\\\Models\\\\Venta\\\";s:2:\\\"id\\\";i:1;s:9:\\\"relations\\\";a:2:{i:0;s:9:\\\"productos\\\";i:1;s:17:\\\"productos.insumos\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}}}s:5:\\\"tries\\\";N;s:13:\\\"maxExceptions\\\";N;s:7:\\\"backoff\\\";N;s:10:\\\"retryUntil\\\";N;s:7:\\\"timeout\\\";N;s:13:\\\"failOnTimeout\\\";b:0;s:17:\\\"shouldBeEncrypted\\\";b:0;s:3:\\\"job\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\"},\"createdAt\":1771604063,\"delay\":null}', 0, NULL, 1771604063, 1771604063),
(21, 'default', '{\"uuid\":\"581e7191-c96e-419d-abc9-654cdf622ff0\",\"displayName\":\"App\\\\Jobs\\\\EnviarComprobanteVentaJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\EnviarComprobanteVentaJob\",\"command\":\"O:34:\\\"App\\\\Jobs\\\\EnviarComprobanteVentaJob\\\":2:{s:10:\\\"\\u0000*\\u0000ventaId\\\";i:1;s:11:\\\"afterCommit\\\";b:1;}\"},\"createdAt\":1771604063,\"delay\":null}', 0, NULL, 1771604063, 1771604063),
(22, 'fiscal', '{\"uuid\":\"176cfa91-04be-4ab8-8673-bfa657156410\",\"displayName\":\"App\\\\Jobs\\\\EmitirDocumentoFiscalJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":5,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":\"10,30,60,120\",\"timeout\":120,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\EmitirDocumentoFiscalJob\",\"command\":\"O:33:\\\"App\\\\Jobs\\\\EmitirDocumentoFiscalJob\\\":3:{s:10:\\\"\\u0000*\\u0000ventaId\\\";i:1;s:5:\\\"queue\\\";s:6:\\\"fiscal\\\";s:5:\\\"delay\\\";O:25:\\\"Illuminate\\\\Support\\\\Carbon\\\":3:{s:4:\\\"date\\\";s:26:\\\"2026-02-20 11:14:26.661376\\\";s:13:\\\"timezone_type\\\";i:3;s:8:\\\"timezone\\\";s:12:\\\"America\\/Lima\\\";}}\"},\"createdAt\":1771604063,\"delay\":3}', 0, NULL, 1771604066, 1771604063),
(23, 'default', '{\"uuid\":\"776888cb-2124-4a47-8a6a-c7adc4c07380\",\"displayName\":\"App\\\\Listeners\\\\EmitirDocumentoFiscalListener\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Events\\\\CallQueuedListener\",\"command\":\"O:36:\\\"Illuminate\\\\Events\\\\CallQueuedListener\\\":20:{s:5:\\\"class\\\";s:43:\\\"App\\\\Listeners\\\\EmitirDocumentoFiscalListener\\\";s:6:\\\"method\\\";s:6:\\\"handle\\\";s:4:\\\"data\\\";a:1:{i:0;O:27:\\\"App\\\\Events\\\\CreateVentaEvent\\\":1:{s:5:\\\"venta\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:16:\\\"App\\\\Models\\\\Venta\\\";s:2:\\\"id\\\";i:2;s:9:\\\"relations\\\";a:2:{i:0;s:9:\\\"productos\\\";i:1;s:17:\\\"productos.insumos\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}}}s:5:\\\"tries\\\";N;s:13:\\\"maxExceptions\\\";N;s:7:\\\"backoff\\\";N;s:10:\\\"retryUntil\\\";N;s:7:\\\"timeout\\\";N;s:13:\\\"failOnTimeout\\\";b:0;s:17:\\\"shouldBeEncrypted\\\";b:0;s:3:\\\"job\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\"},\"createdAt\":1771608755,\"delay\":null}', 0, NULL, 1771608755, 1771608755),
(24, 'default', '{\"uuid\":\"fbe1c14c-f362-4aa8-9971-c846fa6eeec8\",\"displayName\":\"App\\\\Jobs\\\\EnviarComprobanteVentaJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\EnviarComprobanteVentaJob\",\"command\":\"O:34:\\\"App\\\\Jobs\\\\EnviarComprobanteVentaJob\\\":2:{s:10:\\\"\\u0000*\\u0000ventaId\\\";i:2;s:11:\\\"afterCommit\\\";b:1;}\"},\"createdAt\":1771608755,\"delay\":null}', 0, NULL, 1771608755, 1771608755),
(25, 'fiscal', '{\"uuid\":\"c0821bf5-130f-40f9-9cf3-772f0c6029ae\",\"displayName\":\"App\\\\Jobs\\\\EmitirDocumentoFiscalJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":5,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":\"10,30,60,120\",\"timeout\":120,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\EmitirDocumentoFiscalJob\",\"command\":\"O:33:\\\"App\\\\Jobs\\\\EmitirDocumentoFiscalJob\\\":3:{s:10:\\\"\\u0000*\\u0000ventaId\\\";i:2;s:5:\\\"queue\\\";s:6:\\\"fiscal\\\";s:5:\\\"delay\\\";O:25:\\\"Illuminate\\\\Support\\\\Carbon\\\":3:{s:4:\\\"date\\\";s:26:\\\"2026-02-20 12:32:38.856980\\\";s:13:\\\"timezone_type\\\";i:3;s:8:\\\"timezone\\\";s:12:\\\"America\\/Lima\\\";}}\"},\"createdAt\":1771608755,\"delay\":2}', 0, NULL, 1771608758, 1771608756),
(26, 'default', '{\"uuid\":\"5a3e3c90-6219-4820-84db-bef8cf92ddf4\",\"displayName\":\"App\\\\Listeners\\\\EmitirDocumentoFiscalListener\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Events\\\\CallQueuedListener\",\"command\":\"O:36:\\\"Illuminate\\\\Events\\\\CallQueuedListener\\\":20:{s:5:\\\"class\\\";s:43:\\\"App\\\\Listeners\\\\EmitirDocumentoFiscalListener\\\";s:6:\\\"method\\\";s:6:\\\"handle\\\";s:4:\\\"data\\\";a:1:{i:0;O:27:\\\"App\\\\Events\\\\CreateVentaEvent\\\":1:{s:5:\\\"venta\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:16:\\\"App\\\\Models\\\\Venta\\\";s:2:\\\"id\\\";i:3;s:9:\\\"relations\\\";a:2:{i:0;s:9:\\\"productos\\\";i:1;s:17:\\\"productos.insumos\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}}}s:5:\\\"tries\\\";N;s:13:\\\"maxExceptions\\\";N;s:7:\\\"backoff\\\";N;s:10:\\\"retryUntil\\\";N;s:7:\\\"timeout\\\";N;s:13:\\\"failOnTimeout\\\";b:0;s:17:\\\"shouldBeEncrypted\\\";b:0;s:3:\\\"job\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\"},\"createdAt\":1771608815,\"delay\":null}', 0, NULL, 1771608815, 1771608815),
(27, 'default', '{\"uuid\":\"dab2bdeb-7f08-4d48-ba84-8648ae2597a2\",\"displayName\":\"App\\\\Jobs\\\\EnviarComprobanteVentaJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\EnviarComprobanteVentaJob\",\"command\":\"O:34:\\\"App\\\\Jobs\\\\EnviarComprobanteVentaJob\\\":2:{s:10:\\\"\\u0000*\\u0000ventaId\\\";i:3;s:11:\\\"afterCommit\\\";b:1;}\"},\"createdAt\":1771608815,\"delay\":null}', 0, NULL, 1771608815, 1771608815),
(28, 'fiscal', '{\"uuid\":\"7df19543-6361-4cf7-8e2a-c200af775864\",\"displayName\":\"App\\\\Jobs\\\\EmitirDocumentoFiscalJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":5,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":\"10,30,60,120\",\"timeout\":120,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\EmitirDocumentoFiscalJob\",\"command\":\"O:33:\\\"App\\\\Jobs\\\\EmitirDocumentoFiscalJob\\\":3:{s:10:\\\"\\u0000*\\u0000ventaId\\\";i:3;s:5:\\\"queue\\\";s:6:\\\"fiscal\\\";s:5:\\\"delay\\\";O:25:\\\"Illuminate\\\\Support\\\\Carbon\\\":3:{s:4:\\\"date\\\";s:26:\\\"2026-02-20 12:33:38.279754\\\";s:13:\\\"timezone_type\\\";i:3;s:8:\\\"timezone\\\";s:12:\\\"America\\/Lima\\\";}}\"},\"createdAt\":1771608815,\"delay\":3}', 0, NULL, 1771608818, 1771608815),
(29, 'default', '{\"uuid\":\"f37a42a9-eebc-4384-a52a-d5c012c1effa\",\"displayName\":\"App\\\\Listeners\\\\EmitirDocumentoFiscalListener\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Events\\\\CallQueuedListener\",\"command\":\"O:36:\\\"Illuminate\\\\Events\\\\CallQueuedListener\\\":20:{s:5:\\\"class\\\";s:43:\\\"App\\\\Listeners\\\\EmitirDocumentoFiscalListener\\\";s:6:\\\"method\\\";s:6:\\\"handle\\\";s:4:\\\"data\\\";a:1:{i:0;O:27:\\\"App\\\\Events\\\\CreateVentaEvent\\\":1:{s:5:\\\"venta\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:16:\\\"App\\\\Models\\\\Venta\\\";s:2:\\\"id\\\";i:4;s:9:\\\"relations\\\";a:1:{i:0;s:9:\\\"productos\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}}}s:5:\\\"tries\\\";N;s:13:\\\"maxExceptions\\\";N;s:7:\\\"backoff\\\";N;s:10:\\\"retryUntil\\\";N;s:7:\\\"timeout\\\";N;s:13:\\\"failOnTimeout\\\";b:0;s:17:\\\"shouldBeEncrypted\\\";b:0;s:3:\\\"job\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\"},\"createdAt\":1771611061,\"delay\":null}', 0, NULL, 1771611061, 1771611061),
(30, 'default', '{\"uuid\":\"6184b099-e8a0-49df-bb27-09be3d648aec\",\"displayName\":\"App\\\\Jobs\\\\EnviarComprobanteVentaJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\EnviarComprobanteVentaJob\",\"command\":\"O:34:\\\"App\\\\Jobs\\\\EnviarComprobanteVentaJob\\\":2:{s:10:\\\"\\u0000*\\u0000ventaId\\\";i:4;s:11:\\\"afterCommit\\\";b:1;}\"},\"createdAt\":1771611061,\"delay\":null}', 0, NULL, 1771611061, 1771611061),
(31, 'fiscal', '{\"uuid\":\"5bf21503-af67-4e5b-ad05-e6e6abcb08f9\",\"displayName\":\"App\\\\Jobs\\\\EmitirDocumentoFiscalJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":5,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":\"10,30,60,120\",\"timeout\":120,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\EmitirDocumentoFiscalJob\",\"command\":\"O:33:\\\"App\\\\Jobs\\\\EmitirDocumentoFiscalJob\\\":3:{s:10:\\\"\\u0000*\\u0000ventaId\\\";i:4;s:5:\\\"queue\\\";s:6:\\\"fiscal\\\";s:5:\\\"delay\\\";O:25:\\\"Illuminate\\\\Support\\\\Carbon\\\":3:{s:4:\\\"date\\\";s:26:\\\"2026-02-20 13:11:05.036558\\\";s:13:\\\"timezone_type\\\";i:3;s:8:\\\"timezone\\\";s:12:\\\"America\\/Lima\\\";}}\"},\"createdAt\":1771611062,\"delay\":3}', 0, NULL, 1771611065, 1771611062),
(32, 'default', '{\"uuid\":\"e3622813-837a-4881-9a84-0d240e62088b\",\"displayName\":\"App\\\\Listeners\\\\EmitirDocumentoFiscalListener\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Events\\\\CallQueuedListener\",\"command\":\"O:36:\\\"Illuminate\\\\Events\\\\CallQueuedListener\\\":20:{s:5:\\\"class\\\";s:43:\\\"App\\\\Listeners\\\\EmitirDocumentoFiscalListener\\\";s:6:\\\"method\\\";s:6:\\\"handle\\\";s:4:\\\"data\\\";a:1:{i:0;O:27:\\\"App\\\\Events\\\\CreateVentaEvent\\\":1:{s:5:\\\"venta\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:16:\\\"App\\\\Models\\\\Venta\\\";s:2:\\\"id\\\";i:5;s:9:\\\"relations\\\";a:2:{i:0;s:9:\\\"productos\\\";i:1;s:17:\\\"productos.insumos\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}}}s:5:\\\"tries\\\";N;s:13:\\\"maxExceptions\\\";N;s:7:\\\"backoff\\\";N;s:10:\\\"retryUntil\\\";N;s:7:\\\"timeout\\\";N;s:13:\\\"failOnTimeout\\\";b:0;s:17:\\\"shouldBeEncrypted\\\";b:0;s:3:\\\"job\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\"},\"createdAt\":1771632081,\"delay\":null}', 0, NULL, 1771632082, 1771632082),
(33, 'default', '{\"uuid\":\"3afb170c-f6ca-4acb-a788-72ea117f4f31\",\"displayName\":\"App\\\\Jobs\\\\EnviarComprobanteVentaJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\EnviarComprobanteVentaJob\",\"command\":\"O:34:\\\"App\\\\Jobs\\\\EnviarComprobanteVentaJob\\\":2:{s:10:\\\"\\u0000*\\u0000ventaId\\\";i:5;s:11:\\\"afterCommit\\\";b:1;}\"},\"createdAt\":1771632081,\"delay\":null}', 0, NULL, 1771632082, 1771632082),
(34, 'fiscal', '{\"uuid\":\"72f253e3-cea0-4144-b1f7-5a4d2624968f\",\"displayName\":\"App\\\\Jobs\\\\EmitirDocumentoFiscalJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":5,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":\"10,30,60,120\",\"timeout\":120,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\EmitirDocumentoFiscalJob\",\"command\":\"O:33:\\\"App\\\\Jobs\\\\EmitirDocumentoFiscalJob\\\":3:{s:10:\\\"\\u0000*\\u0000ventaId\\\";i:5;s:5:\\\"queue\\\";s:6:\\\"fiscal\\\";s:5:\\\"delay\\\";O:25:\\\"Illuminate\\\\Support\\\\Carbon\\\":3:{s:4:\\\"date\\\";s:26:\\\"2026-02-20 19:01:25.204667\\\";s:13:\\\"timezone_type\\\";i:3;s:8:\\\"timezone\\\";s:12:\\\"America\\/Lima\\\";}}\"},\"createdAt\":1771632082,\"delay\":3}', 0, NULL, 1771632085, 1771632082),
(35, 'default', '{\"uuid\":\"0f967028-8d96-40ed-ab5f-733c3fd4fd37\",\"displayName\":\"App\\\\Listeners\\\\EmitirDocumentoFiscalListener\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Events\\\\CallQueuedListener\",\"command\":\"O:36:\\\"Illuminate\\\\Events\\\\CallQueuedListener\\\":20:{s:5:\\\"class\\\";s:43:\\\"App\\\\Listeners\\\\EmitirDocumentoFiscalListener\\\";s:6:\\\"method\\\";s:6:\\\"handle\\\";s:4:\\\"data\\\";a:1:{i:0;O:27:\\\"App\\\\Events\\\\CreateVentaEvent\\\":1:{s:5:\\\"venta\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:16:\\\"App\\\\Models\\\\Venta\\\";s:2:\\\"id\\\";i:6;s:9:\\\"relations\\\";a:2:{i:0;s:9:\\\"productos\\\";i:1;s:17:\\\"productos.insumos\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}}}s:5:\\\"tries\\\";N;s:13:\\\"maxExceptions\\\";N;s:7:\\\"backoff\\\";N;s:10:\\\"retryUntil\\\";N;s:7:\\\"timeout\\\";N;s:13:\\\"failOnTimeout\\\";b:0;s:17:\\\"shouldBeEncrypted\\\";b:0;s:3:\\\"job\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\"},\"createdAt\":1771637799,\"delay\":null}', 0, NULL, 1771637799, 1771637799),
(36, 'default', '{\"uuid\":\"a10632c4-6279-472b-808c-96f4606a76e2\",\"displayName\":\"App\\\\Jobs\\\\EnviarComprobanteVentaJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\EnviarComprobanteVentaJob\",\"command\":\"O:34:\\\"App\\\\Jobs\\\\EnviarComprobanteVentaJob\\\":2:{s:10:\\\"\\u0000*\\u0000ventaId\\\";i:6;s:11:\\\"afterCommit\\\";b:1;}\"},\"createdAt\":1771637799,\"delay\":null}', 0, NULL, 1771637799, 1771637799),
(37, 'fiscal', '{\"uuid\":\"edc73666-5ddf-449a-b3b7-bb22f963cf29\",\"displayName\":\"App\\\\Jobs\\\\EmitirDocumentoFiscalJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":5,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":\"10,30,60,120\",\"timeout\":120,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\EmitirDocumentoFiscalJob\",\"command\":\"O:33:\\\"App\\\\Jobs\\\\EmitirDocumentoFiscalJob\\\":3:{s:10:\\\"\\u0000*\\u0000ventaId\\\";i:6;s:5:\\\"queue\\\";s:6:\\\"fiscal\\\";s:5:\\\"delay\\\";O:25:\\\"Illuminate\\\\Support\\\\Carbon\\\":3:{s:4:\\\"date\\\";s:26:\\\"2026-02-20 20:36:42.305313\\\";s:13:\\\"timezone_type\\\";i:3;s:8:\\\"timezone\\\";s:12:\\\"America\\/Lima\\\";}}\"},\"createdAt\":1771637799,\"delay\":3}', 0, NULL, 1771637802, 1771637799),
(38, 'default', '{\"uuid\":\"769bc6d3-b8fc-47a7-9e67-2597262aff99\",\"displayName\":\"App\\\\Listeners\\\\EmitirDocumentoFiscalListener\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Events\\\\CallQueuedListener\",\"command\":\"O:36:\\\"Illuminate\\\\Events\\\\CallQueuedListener\\\":20:{s:5:\\\"class\\\";s:43:\\\"App\\\\Listeners\\\\EmitirDocumentoFiscalListener\\\";s:6:\\\"method\\\";s:6:\\\"handle\\\";s:4:\\\"data\\\";a:1:{i:0;O:27:\\\"App\\\\Events\\\\CreateVentaEvent\\\":1:{s:5:\\\"venta\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:16:\\\"App\\\\Models\\\\Venta\\\";s:2:\\\"id\\\";i:7;s:9:\\\"relations\\\";a:2:{i:0;s:9:\\\"productos\\\";i:1;s:17:\\\"productos.insumos\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}}}s:5:\\\"tries\\\";N;s:13:\\\"maxExceptions\\\";N;s:7:\\\"backoff\\\";N;s:10:\\\"retryUntil\\\";N;s:7:\\\"timeout\\\";N;s:13:\\\"failOnTimeout\\\";b:0;s:17:\\\"shouldBeEncrypted\\\";b:0;s:3:\\\"job\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\"},\"createdAt\":1771639069,\"delay\":null}', 0, NULL, 1771639069, 1771639069),
(39, 'default', '{\"uuid\":\"eca7a3b9-106d-4c29-8f11-6bd6d6b2f6b2\",\"displayName\":\"App\\\\Jobs\\\\EnviarComprobanteVentaJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\EnviarComprobanteVentaJob\",\"command\":\"O:34:\\\"App\\\\Jobs\\\\EnviarComprobanteVentaJob\\\":2:{s:10:\\\"\\u0000*\\u0000ventaId\\\";i:7;s:11:\\\"afterCommit\\\";b:1;}\"},\"createdAt\":1771639069,\"delay\":null}', 0, NULL, 1771639069, 1771639069),
(40, 'fiscal', '{\"uuid\":\"7e47a9f1-a8d4-42f3-8820-38bfbb479700\",\"displayName\":\"App\\\\Jobs\\\\EmitirDocumentoFiscalJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":5,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":\"10,30,60,120\",\"timeout\":120,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\EmitirDocumentoFiscalJob\",\"command\":\"O:33:\\\"App\\\\Jobs\\\\EmitirDocumentoFiscalJob\\\":3:{s:10:\\\"\\u0000*\\u0000ventaId\\\";i:7;s:5:\\\"queue\\\";s:6:\\\"fiscal\\\";s:5:\\\"delay\\\";O:25:\\\"Illuminate\\\\Support\\\\Carbon\\\":3:{s:4:\\\"date\\\";s:26:\\\"2026-02-20 20:57:52.583293\\\";s:13:\\\"timezone_type\\\";i:3;s:8:\\\"timezone\\\";s:12:\\\"America\\/Lima\\\";}}\"},\"createdAt\":1771639069,\"delay\":3}', 0, NULL, 1771639072, 1771639069),
(41, 'default', '{\"uuid\":\"32ec8766-d0b6-46e6-b4db-663da6a12b8f\",\"displayName\":\"App\\\\Listeners\\\\EmitirDocumentoFiscalListener\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Events\\\\CallQueuedListener\",\"command\":\"O:36:\\\"Illuminate\\\\Events\\\\CallQueuedListener\\\":20:{s:5:\\\"class\\\";s:43:\\\"App\\\\Listeners\\\\EmitirDocumentoFiscalListener\\\";s:6:\\\"method\\\";s:6:\\\"handle\\\";s:4:\\\"data\\\";a:1:{i:0;O:27:\\\"App\\\\Events\\\\CreateVentaEvent\\\":1:{s:5:\\\"venta\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:16:\\\"App\\\\Models\\\\Venta\\\";s:2:\\\"id\\\";i:8;s:9:\\\"relations\\\";a:2:{i:0;s:9:\\\"productos\\\";i:1;s:17:\\\"productos.insumos\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}}}s:5:\\\"tries\\\";N;s:13:\\\"maxExceptions\\\";N;s:7:\\\"backoff\\\";N;s:10:\\\"retryUntil\\\";N;s:7:\\\"timeout\\\";N;s:13:\\\"failOnTimeout\\\";b:0;s:17:\\\"shouldBeEncrypted\\\";b:0;s:3:\\\"job\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\"},\"createdAt\":1771639248,\"delay\":null}', 0, NULL, 1771639248, 1771639248),
(42, 'default', '{\"uuid\":\"99303124-2817-4e0c-bbf6-0b4a27673877\",\"displayName\":\"App\\\\Jobs\\\\EnviarComprobanteVentaJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\EnviarComprobanteVentaJob\",\"command\":\"O:34:\\\"App\\\\Jobs\\\\EnviarComprobanteVentaJob\\\":2:{s:10:\\\"\\u0000*\\u0000ventaId\\\";i:8;s:11:\\\"afterCommit\\\";b:1;}\"},\"createdAt\":1771639248,\"delay\":null}', 0, NULL, 1771639248, 1771639248),
(43, 'fiscal', '{\"uuid\":\"fb66b867-a67e-47d4-ac77-c7a6bfe8923f\",\"displayName\":\"App\\\\Jobs\\\\EmitirDocumentoFiscalJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":5,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":\"10,30,60,120\",\"timeout\":120,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\EmitirDocumentoFiscalJob\",\"command\":\"O:33:\\\"App\\\\Jobs\\\\EmitirDocumentoFiscalJob\\\":3:{s:10:\\\"\\u0000*\\u0000ventaId\\\";i:8;s:5:\\\"queue\\\";s:6:\\\"fiscal\\\";s:5:\\\"delay\\\";O:25:\\\"Illuminate\\\\Support\\\\Carbon\\\":3:{s:4:\\\"date\\\";s:26:\\\"2026-02-20 21:00:51.854199\\\";s:13:\\\"timezone_type\\\";i:3;s:8:\\\"timezone\\\";s:12:\\\"America\\/Lima\\\";}}\"},\"createdAt\":1771639248,\"delay\":3}', 0, NULL, 1771639251, 1771639248),
(44, 'default', '{\"uuid\":\"31d13557-a1da-4643-975b-2313496a128d\",\"displayName\":\"App\\\\Listeners\\\\EmitirDocumentoFiscalListener\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Events\\\\CallQueuedListener\",\"command\":\"O:36:\\\"Illuminate\\\\Events\\\\CallQueuedListener\\\":20:{s:5:\\\"class\\\";s:43:\\\"App\\\\Listeners\\\\EmitirDocumentoFiscalListener\\\";s:6:\\\"method\\\";s:6:\\\"handle\\\";s:4:\\\"data\\\";a:1:{i:0;O:27:\\\"App\\\\Events\\\\CreateVentaEvent\\\":1:{s:5:\\\"venta\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:16:\\\"App\\\\Models\\\\Venta\\\";s:2:\\\"id\\\";i:9;s:9:\\\"relations\\\";a:2:{i:0;s:9:\\\"productos\\\";i:1;s:17:\\\"productos.insumos\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}}}s:5:\\\"tries\\\";N;s:13:\\\"maxExceptions\\\";N;s:7:\\\"backoff\\\";N;s:10:\\\"retryUntil\\\";N;s:7:\\\"timeout\\\";N;s:13:\\\"failOnTimeout\\\";b:0;s:17:\\\"shouldBeEncrypted\\\";b:0;s:3:\\\"job\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\"},\"createdAt\":1771639747,\"delay\":null}', 0, NULL, 1771639747, 1771639747),
(45, 'default', '{\"uuid\":\"8c1b3e4b-c600-472f-97dd-927c8922297d\",\"displayName\":\"App\\\\Jobs\\\\EnviarComprobanteVentaJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\EnviarComprobanteVentaJob\",\"command\":\"O:34:\\\"App\\\\Jobs\\\\EnviarComprobanteVentaJob\\\":2:{s:10:\\\"\\u0000*\\u0000ventaId\\\";i:9;s:11:\\\"afterCommit\\\";b:1;}\"},\"createdAt\":1771639747,\"delay\":null}', 0, NULL, 1771639747, 1771639747),
(46, 'fiscal', '{\"uuid\":\"12db6125-a5a4-479e-950b-59ebd6193b82\",\"displayName\":\"App\\\\Jobs\\\\EmitirDocumentoFiscalJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":5,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":\"10,30,60,120\",\"timeout\":120,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\EmitirDocumentoFiscalJob\",\"command\":\"O:33:\\\"App\\\\Jobs\\\\EmitirDocumentoFiscalJob\\\":3:{s:10:\\\"\\u0000*\\u0000ventaId\\\";i:9;s:5:\\\"queue\\\";s:6:\\\"fiscal\\\";s:5:\\\"delay\\\";O:25:\\\"Illuminate\\\\Support\\\\Carbon\\\":3:{s:4:\\\"date\\\";s:26:\\\"2026-02-20 21:09:10.849140\\\";s:13:\\\"timezone_type\\\";i:3;s:8:\\\"timezone\\\";s:12:\\\"America\\/Lima\\\";}}\"},\"createdAt\":1771639747,\"delay\":3}', 0, NULL, 1771639750, 1771639747),
(47, 'default', '{\"uuid\":\"11d9ed4c-8192-425c-87bd-bdec9c0ba80a\",\"displayName\":\"App\\\\Listeners\\\\EmitirDocumentoFiscalListener\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Events\\\\CallQueuedListener\",\"command\":\"O:36:\\\"Illuminate\\\\Events\\\\CallQueuedListener\\\":20:{s:5:\\\"class\\\";s:43:\\\"App\\\\Listeners\\\\EmitirDocumentoFiscalListener\\\";s:6:\\\"method\\\";s:6:\\\"handle\\\";s:4:\\\"data\\\";a:1:{i:0;O:27:\\\"App\\\\Events\\\\CreateVentaEvent\\\":1:{s:5:\\\"venta\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:16:\\\"App\\\\Models\\\\Venta\\\";s:2:\\\"id\\\";i:10;s:9:\\\"relations\\\";a:2:{i:0;s:9:\\\"productos\\\";i:1;s:17:\\\"productos.insumos\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}}}s:5:\\\"tries\\\";N;s:13:\\\"maxExceptions\\\";N;s:7:\\\"backoff\\\";N;s:10:\\\"retryUntil\\\";N;s:7:\\\"timeout\\\";N;s:13:\\\"failOnTimeout\\\";b:0;s:17:\\\"shouldBeEncrypted\\\";b:0;s:3:\\\"job\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\"},\"createdAt\":1771689638,\"delay\":null}', 0, NULL, 1771689638, 1771689638),
(48, 'default', '{\"uuid\":\"c6ec878b-9233-4487-bcc0-3e4316cedfba\",\"displayName\":\"App\\\\Jobs\\\\EnviarComprobanteVentaJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\EnviarComprobanteVentaJob\",\"command\":\"O:34:\\\"App\\\\Jobs\\\\EnviarComprobanteVentaJob\\\":2:{s:10:\\\"\\u0000*\\u0000ventaId\\\";i:10;s:11:\\\"afterCommit\\\";b:1;}\"},\"createdAt\":1771689637,\"delay\":null}', 0, NULL, 1771689638, 1771689638),
(49, 'default', '{\"uuid\":\"aec71cc8-5112-4e51-a583-50fac17de2fe\",\"displayName\":\"App\\\\Listeners\\\\EmitirDocumentoFiscalListener\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Events\\\\CallQueuedListener\",\"command\":\"O:36:\\\"Illuminate\\\\Events\\\\CallQueuedListener\\\":20:{s:5:\\\"class\\\";s:43:\\\"App\\\\Listeners\\\\EmitirDocumentoFiscalListener\\\";s:6:\\\"method\\\";s:6:\\\"handle\\\";s:4:\\\"data\\\";a:1:{i:0;O:27:\\\"App\\\\Events\\\\CreateVentaEvent\\\":1:{s:5:\\\"venta\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:16:\\\"App\\\\Models\\\\Venta\\\";s:2:\\\"id\\\";i:11;s:9:\\\"relations\\\";a:2:{i:0;s:9:\\\"productos\\\";i:1;s:17:\\\"productos.insumos\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}}}s:5:\\\"tries\\\";N;s:13:\\\"maxExceptions\\\";N;s:7:\\\"backoff\\\";N;s:10:\\\"retryUntil\\\";N;s:7:\\\"timeout\\\";N;s:13:\\\"failOnTimeout\\\";b:0;s:17:\\\"shouldBeEncrypted\\\";b:0;s:3:\\\"job\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\"},\"createdAt\":1771689638,\"delay\":null}', 0, NULL, 1771689638, 1771689638),
(50, 'default', '{\"uuid\":\"4c5d0be9-c257-43ae-baae-9af9e5435d89\",\"displayName\":\"App\\\\Jobs\\\\EnviarComprobanteVentaJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\EnviarComprobanteVentaJob\",\"command\":\"O:34:\\\"App\\\\Jobs\\\\EnviarComprobanteVentaJob\\\":2:{s:10:\\\"\\u0000*\\u0000ventaId\\\";i:11;s:11:\\\"afterCommit\\\";b:1;}\"},\"createdAt\":1771689638,\"delay\":null}', 0, NULL, 1771689638, 1771689638);
INSERT INTO `jobs` (`id`, `queue`, `payload`, `attempts`, `reserved_at`, `available_at`, `created_at`) VALUES
(51, 'default', '{\"uuid\":\"3368bb1a-e8be-487c-9d1a-d9846e8338ab\",\"displayName\":\"App\\\\Listeners\\\\EmitirDocumentoFiscalListener\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Events\\\\CallQueuedListener\",\"command\":\"O:36:\\\"Illuminate\\\\Events\\\\CallQueuedListener\\\":20:{s:5:\\\"class\\\";s:43:\\\"App\\\\Listeners\\\\EmitirDocumentoFiscalListener\\\";s:6:\\\"method\\\";s:6:\\\"handle\\\";s:4:\\\"data\\\";a:1:{i:0;O:27:\\\"App\\\\Events\\\\CreateVentaEvent\\\":1:{s:5:\\\"venta\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:16:\\\"App\\\\Models\\\\Venta\\\";s:2:\\\"id\\\";i:12;s:9:\\\"relations\\\";a:2:{i:0;s:9:\\\"productos\\\";i:1;s:17:\\\"productos.insumos\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}}}s:5:\\\"tries\\\";N;s:13:\\\"maxExceptions\\\";N;s:7:\\\"backoff\\\";N;s:10:\\\"retryUntil\\\";N;s:7:\\\"timeout\\\";N;s:13:\\\"failOnTimeout\\\";b:0;s:17:\\\"shouldBeEncrypted\\\";b:0;s:3:\\\"job\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\"},\"createdAt\":1771689638,\"delay\":null}', 0, NULL, 1771689638, 1771689638),
(52, 'default', '{\"uuid\":\"b147844e-7a41-47f9-bd64-22f5aebcd867\",\"displayName\":\"App\\\\Jobs\\\\EnviarComprobanteVentaJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\EnviarComprobanteVentaJob\",\"command\":\"O:34:\\\"App\\\\Jobs\\\\EnviarComprobanteVentaJob\\\":2:{s:10:\\\"\\u0000*\\u0000ventaId\\\";i:12;s:11:\\\"afterCommit\\\";b:1;}\"},\"createdAt\":1771689638,\"delay\":null}', 0, NULL, 1771689638, 1771689638),
(53, 'default', '{\"uuid\":\"2b05395b-0ea0-4f2e-99bb-6e7149c7586e\",\"displayName\":\"App\\\\Listeners\\\\EmitirDocumentoFiscalListener\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Events\\\\CallQueuedListener\",\"command\":\"O:36:\\\"Illuminate\\\\Events\\\\CallQueuedListener\\\":20:{s:5:\\\"class\\\";s:43:\\\"App\\\\Listeners\\\\EmitirDocumentoFiscalListener\\\";s:6:\\\"method\\\";s:6:\\\"handle\\\";s:4:\\\"data\\\";a:1:{i:0;O:27:\\\"App\\\\Events\\\\CreateVentaEvent\\\":1:{s:5:\\\"venta\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:16:\\\"App\\\\Models\\\\Venta\\\";s:2:\\\"id\\\";i:13;s:9:\\\"relations\\\";a:2:{i:0;s:9:\\\"productos\\\";i:1;s:17:\\\"productos.insumos\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}}}s:5:\\\"tries\\\";N;s:13:\\\"maxExceptions\\\";N;s:7:\\\"backoff\\\";N;s:10:\\\"retryUntil\\\";N;s:7:\\\"timeout\\\";N;s:13:\\\"failOnTimeout\\\";b:0;s:17:\\\"shouldBeEncrypted\\\";b:0;s:3:\\\"job\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\"},\"createdAt\":1771689639,\"delay\":null}', 0, NULL, 1771689639, 1771689639),
(54, 'default', '{\"uuid\":\"11949cb9-438d-4f05-afb2-535a6f0d8da4\",\"displayName\":\"App\\\\Jobs\\\\EnviarComprobanteVentaJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\EnviarComprobanteVentaJob\",\"command\":\"O:34:\\\"App\\\\Jobs\\\\EnviarComprobanteVentaJob\\\":2:{s:10:\\\"\\u0000*\\u0000ventaId\\\";i:13;s:11:\\\"afterCommit\\\";b:1;}\"},\"createdAt\":1771689639,\"delay\":null}', 0, NULL, 1771689639, 1771689639),
(55, 'default', '{\"uuid\":\"78d7c54e-8678-44c6-b9c8-d8d5d5804279\",\"displayName\":\"App\\\\Listeners\\\\EmitirDocumentoFiscalListener\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Events\\\\CallQueuedListener\",\"command\":\"O:36:\\\"Illuminate\\\\Events\\\\CallQueuedListener\\\":20:{s:5:\\\"class\\\";s:43:\\\"App\\\\Listeners\\\\EmitirDocumentoFiscalListener\\\";s:6:\\\"method\\\";s:6:\\\"handle\\\";s:4:\\\"data\\\";a:1:{i:0;O:27:\\\"App\\\\Events\\\\CreateVentaEvent\\\":1:{s:5:\\\"venta\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:16:\\\"App\\\\Models\\\\Venta\\\";s:2:\\\"id\\\";i:14;s:9:\\\"relations\\\";a:2:{i:0;s:9:\\\"productos\\\";i:1;s:17:\\\"productos.insumos\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}}}s:5:\\\"tries\\\";N;s:13:\\\"maxExceptions\\\";N;s:7:\\\"backoff\\\";N;s:10:\\\"retryUntil\\\";N;s:7:\\\"timeout\\\";N;s:13:\\\"failOnTimeout\\\";b:0;s:17:\\\"shouldBeEncrypted\\\";b:0;s:3:\\\"job\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\"},\"createdAt\":1771689639,\"delay\":null}', 0, NULL, 1771689639, 1771689639),
(56, 'default', '{\"uuid\":\"fa8dd60f-0671-48d6-9363-823cc5982a40\",\"displayName\":\"App\\\\Jobs\\\\EnviarComprobanteVentaJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\EnviarComprobanteVentaJob\",\"command\":\"O:34:\\\"App\\\\Jobs\\\\EnviarComprobanteVentaJob\\\":2:{s:10:\\\"\\u0000*\\u0000ventaId\\\";i:14;s:11:\\\"afterCommit\\\";b:1;}\"},\"createdAt\":1771689639,\"delay\":null}', 0, NULL, 1771689639, 1771689639),
(57, 'default', '{\"uuid\":\"689ea707-65fd-4b2d-9bc0-7eac8725de79\",\"displayName\":\"App\\\\Listeners\\\\EmitirDocumentoFiscalListener\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Events\\\\CallQueuedListener\",\"command\":\"O:36:\\\"Illuminate\\\\Events\\\\CallQueuedListener\\\":20:{s:5:\\\"class\\\";s:43:\\\"App\\\\Listeners\\\\EmitirDocumentoFiscalListener\\\";s:6:\\\"method\\\";s:6:\\\"handle\\\";s:4:\\\"data\\\";a:1:{i:0;O:27:\\\"App\\\\Events\\\\CreateVentaEvent\\\":1:{s:5:\\\"venta\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:16:\\\"App\\\\Models\\\\Venta\\\";s:2:\\\"id\\\";i:15;s:9:\\\"relations\\\";a:2:{i:0;s:9:\\\"productos\\\";i:1;s:17:\\\"productos.insumos\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}}}s:5:\\\"tries\\\";N;s:13:\\\"maxExceptions\\\";N;s:7:\\\"backoff\\\";N;s:10:\\\"retryUntil\\\";N;s:7:\\\"timeout\\\";N;s:13:\\\"failOnTimeout\\\";b:0;s:17:\\\"shouldBeEncrypted\\\";b:0;s:3:\\\"job\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\"},\"createdAt\":1771689639,\"delay\":null}', 0, NULL, 1771689639, 1771689639),
(58, 'default', '{\"uuid\":\"0f6ab308-516b-4b2a-99ed-df1775573ac6\",\"displayName\":\"App\\\\Jobs\\\\EnviarComprobanteVentaJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\EnviarComprobanteVentaJob\",\"command\":\"O:34:\\\"App\\\\Jobs\\\\EnviarComprobanteVentaJob\\\":2:{s:10:\\\"\\u0000*\\u0000ventaId\\\";i:15;s:11:\\\"afterCommit\\\";b:1;}\"},\"createdAt\":1771689639,\"delay\":null}', 0, NULL, 1771689639, 1771689639),
(59, 'default', '{\"uuid\":\"c80dd4eb-793c-4b6a-b079-ca33de505a83\",\"displayName\":\"App\\\\Listeners\\\\EmitirDocumentoFiscalListener\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Events\\\\CallQueuedListener\",\"command\":\"O:36:\\\"Illuminate\\\\Events\\\\CallQueuedListener\\\":20:{s:5:\\\"class\\\";s:43:\\\"App\\\\Listeners\\\\EmitirDocumentoFiscalListener\\\";s:6:\\\"method\\\";s:6:\\\"handle\\\";s:4:\\\"data\\\";a:1:{i:0;O:27:\\\"App\\\\Events\\\\CreateVentaEvent\\\":1:{s:5:\\\"venta\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:16:\\\"App\\\\Models\\\\Venta\\\";s:2:\\\"id\\\";i:16;s:9:\\\"relations\\\";a:2:{i:0;s:9:\\\"productos\\\";i:1;s:17:\\\"productos.insumos\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}}}s:5:\\\"tries\\\";N;s:13:\\\"maxExceptions\\\";N;s:7:\\\"backoff\\\";N;s:10:\\\"retryUntil\\\";N;s:7:\\\"timeout\\\";N;s:13:\\\"failOnTimeout\\\";b:0;s:17:\\\"shouldBeEncrypted\\\";b:0;s:3:\\\"job\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\"},\"createdAt\":1771689640,\"delay\":null}', 0, NULL, 1771689640, 1771689640),
(60, 'default', '{\"uuid\":\"40918863-16c8-4324-92c2-9ccf51f2ef15\",\"displayName\":\"App\\\\Jobs\\\\EnviarComprobanteVentaJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\EnviarComprobanteVentaJob\",\"command\":\"O:34:\\\"App\\\\Jobs\\\\EnviarComprobanteVentaJob\\\":2:{s:10:\\\"\\u0000*\\u0000ventaId\\\";i:16;s:11:\\\"afterCommit\\\";b:1;}\"},\"createdAt\":1771689640,\"delay\":null}', 0, NULL, 1771689640, 1771689640),
(61, 'default', '{\"uuid\":\"0d3840e6-f0ac-474c-8c6b-6d9a8e0a924d\",\"displayName\":\"App\\\\Listeners\\\\EmitirDocumentoFiscalListener\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Events\\\\CallQueuedListener\",\"command\":\"O:36:\\\"Illuminate\\\\Events\\\\CallQueuedListener\\\":20:{s:5:\\\"class\\\";s:43:\\\"App\\\\Listeners\\\\EmitirDocumentoFiscalListener\\\";s:6:\\\"method\\\";s:6:\\\"handle\\\";s:4:\\\"data\\\";a:1:{i:0;O:27:\\\"App\\\\Events\\\\CreateVentaEvent\\\":1:{s:5:\\\"venta\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:16:\\\"App\\\\Models\\\\Venta\\\";s:2:\\\"id\\\";i:17;s:9:\\\"relations\\\";a:2:{i:0;s:9:\\\"productos\\\";i:1;s:17:\\\"productos.insumos\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}}}s:5:\\\"tries\\\";N;s:13:\\\"maxExceptions\\\";N;s:7:\\\"backoff\\\";N;s:10:\\\"retryUntil\\\";N;s:7:\\\"timeout\\\";N;s:13:\\\"failOnTimeout\\\";b:0;s:17:\\\"shouldBeEncrypted\\\";b:0;s:3:\\\"job\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\"},\"createdAt\":1771689640,\"delay\":null}', 0, NULL, 1771689640, 1771689640),
(62, 'default', '{\"uuid\":\"e67a5337-8ad3-4bb8-9deb-186491214326\",\"displayName\":\"App\\\\Jobs\\\\EnviarComprobanteVentaJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\EnviarComprobanteVentaJob\",\"command\":\"O:34:\\\"App\\\\Jobs\\\\EnviarComprobanteVentaJob\\\":2:{s:10:\\\"\\u0000*\\u0000ventaId\\\";i:17;s:11:\\\"afterCommit\\\";b:1;}\"},\"createdAt\":1771689640,\"delay\":null}', 0, NULL, 1771689640, 1771689640),
(63, 'default', '{\"uuid\":\"88e0a844-860a-4431-8d3f-0aac684fdbd0\",\"displayName\":\"App\\\\Listeners\\\\EmitirDocumentoFiscalListener\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Events\\\\CallQueuedListener\",\"command\":\"O:36:\\\"Illuminate\\\\Events\\\\CallQueuedListener\\\":20:{s:5:\\\"class\\\";s:43:\\\"App\\\\Listeners\\\\EmitirDocumentoFiscalListener\\\";s:6:\\\"method\\\";s:6:\\\"handle\\\";s:4:\\\"data\\\";a:1:{i:0;O:27:\\\"App\\\\Events\\\\CreateVentaEvent\\\":1:{s:5:\\\"venta\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:16:\\\"App\\\\Models\\\\Venta\\\";s:2:\\\"id\\\";i:18;s:9:\\\"relations\\\";a:2:{i:0;s:9:\\\"productos\\\";i:1;s:17:\\\"productos.insumos\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}}}s:5:\\\"tries\\\";N;s:13:\\\"maxExceptions\\\";N;s:7:\\\"backoff\\\";N;s:10:\\\"retryUntil\\\";N;s:7:\\\"timeout\\\";N;s:13:\\\"failOnTimeout\\\";b:0;s:17:\\\"shouldBeEncrypted\\\";b:0;s:3:\\\"job\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\"},\"createdAt\":1771689641,\"delay\":null}', 0, NULL, 1771689641, 1771689641),
(64, 'default', '{\"uuid\":\"8155c749-afb9-4c7c-8f81-b59cb129ba28\",\"displayName\":\"App\\\\Jobs\\\\EnviarComprobanteVentaJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\EnviarComprobanteVentaJob\",\"command\":\"O:34:\\\"App\\\\Jobs\\\\EnviarComprobanteVentaJob\\\":2:{s:10:\\\"\\u0000*\\u0000ventaId\\\";i:18;s:11:\\\"afterCommit\\\";b:1;}\"},\"createdAt\":1771689641,\"delay\":null}', 0, NULL, 1771689641, 1771689641),
(65, 'default', '{\"uuid\":\"5a7df61e-a1ac-425d-8fb3-097af4599c82\",\"displayName\":\"App\\\\Listeners\\\\EmitirDocumentoFiscalListener\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Events\\\\CallQueuedListener\",\"command\":\"O:36:\\\"Illuminate\\\\Events\\\\CallQueuedListener\\\":20:{s:5:\\\"class\\\";s:43:\\\"App\\\\Listeners\\\\EmitirDocumentoFiscalListener\\\";s:6:\\\"method\\\";s:6:\\\"handle\\\";s:4:\\\"data\\\";a:1:{i:0;O:27:\\\"App\\\\Events\\\\CreateVentaEvent\\\":1:{s:5:\\\"venta\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:16:\\\"App\\\\Models\\\\Venta\\\";s:2:\\\"id\\\";i:19;s:9:\\\"relations\\\";a:2:{i:0;s:9:\\\"productos\\\";i:1;s:17:\\\"productos.insumos\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}}}s:5:\\\"tries\\\";N;s:13:\\\"maxExceptions\\\";N;s:7:\\\"backoff\\\";N;s:10:\\\"retryUntil\\\";N;s:7:\\\"timeout\\\";N;s:13:\\\"failOnTimeout\\\";b:0;s:17:\\\"shouldBeEncrypted\\\";b:0;s:3:\\\"job\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\"},\"createdAt\":1771689675,\"delay\":null}', 0, NULL, 1771689675, 1771689675),
(66, 'default', '{\"uuid\":\"088c141b-9b00-4cd1-9079-449914d719df\",\"displayName\":\"App\\\\Jobs\\\\EnviarComprobanteVentaJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\EnviarComprobanteVentaJob\",\"command\":\"O:34:\\\"App\\\\Jobs\\\\EnviarComprobanteVentaJob\\\":2:{s:10:\\\"\\u0000*\\u0000ventaId\\\";i:19;s:11:\\\"afterCommit\\\";b:1;}\"},\"createdAt\":1771689675,\"delay\":null}', 0, NULL, 1771689675, 1771689675),
(67, 'default', '{\"uuid\":\"dba903b0-9fa4-45db-a786-385bd3a30d31\",\"displayName\":\"App\\\\Listeners\\\\EmitirDocumentoFiscalListener\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Events\\\\CallQueuedListener\",\"command\":\"O:36:\\\"Illuminate\\\\Events\\\\CallQueuedListener\\\":20:{s:5:\\\"class\\\";s:43:\\\"App\\\\Listeners\\\\EmitirDocumentoFiscalListener\\\";s:6:\\\"method\\\";s:6:\\\"handle\\\";s:4:\\\"data\\\";a:1:{i:0;O:27:\\\"App\\\\Events\\\\CreateVentaEvent\\\":1:{s:5:\\\"venta\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:16:\\\"App\\\\Models\\\\Venta\\\";s:2:\\\"id\\\";i:20;s:9:\\\"relations\\\";a:2:{i:0;s:9:\\\"productos\\\";i:1;s:17:\\\"productos.insumos\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}}}s:5:\\\"tries\\\";N;s:13:\\\"maxExceptions\\\";N;s:7:\\\"backoff\\\";N;s:10:\\\"retryUntil\\\";N;s:7:\\\"timeout\\\";N;s:13:\\\"failOnTimeout\\\";b:0;s:17:\\\"shouldBeEncrypted\\\";b:0;s:3:\\\"job\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\"},\"createdAt\":1771695463,\"delay\":null}', 0, NULL, 1771695463, 1771695463),
(68, 'default', '{\"uuid\":\"c028c66d-d883-442c-ae9e-34d751a84224\",\"displayName\":\"App\\\\Jobs\\\\EnviarComprobanteVentaJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\EnviarComprobanteVentaJob\",\"command\":\"O:34:\\\"App\\\\Jobs\\\\EnviarComprobanteVentaJob\\\":2:{s:10:\\\"\\u0000*\\u0000ventaId\\\";i:20;s:11:\\\"afterCommit\\\";b:1;}\"},\"createdAt\":1771695463,\"delay\":null}', 0, NULL, 1771695463, 1771695463),
(69, 'fiscal', '{\"uuid\":\"2d35d5f0-68cb-4cec-932d-94c0c198007a\",\"displayName\":\"App\\\\Jobs\\\\EmitirDocumentoFiscalJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":5,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":\"10,30,60,120\",\"timeout\":120,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\EmitirDocumentoFiscalJob\",\"command\":\"O:33:\\\"App\\\\Jobs\\\\EmitirDocumentoFiscalJob\\\":3:{s:10:\\\"\\u0000*\\u0000ventaId\\\";i:20;s:5:\\\"queue\\\";s:6:\\\"fiscal\\\";s:5:\\\"delay\\\";O:25:\\\"Illuminate\\\\Support\\\\Carbon\\\":3:{s:4:\\\"date\\\";s:26:\\\"2026-02-21 12:37:47.001566\\\";s:13:\\\"timezone_type\\\";i:3;s:8:\\\"timezone\\\";s:12:\\\"America\\/Lima\\\";}}\"},\"createdAt\":1771695464,\"delay\":3}', 0, NULL, 1771695467, 1771695464),
(70, 'default', '{\"uuid\":\"70e21a6a-2407-479e-9fcc-f2334a9be35c\",\"displayName\":\"App\\\\Listeners\\\\EmitirDocumentoFiscalListener\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Events\\\\CallQueuedListener\",\"command\":\"O:36:\\\"Illuminate\\\\Events\\\\CallQueuedListener\\\":20:{s:5:\\\"class\\\";s:43:\\\"App\\\\Listeners\\\\EmitirDocumentoFiscalListener\\\";s:6:\\\"method\\\";s:6:\\\"handle\\\";s:4:\\\"data\\\";a:1:{i:0;O:27:\\\"App\\\\Events\\\\CreateVentaEvent\\\":1:{s:5:\\\"venta\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:16:\\\"App\\\\Models\\\\Venta\\\";s:2:\\\"id\\\";i:21;s:9:\\\"relations\\\";a:2:{i:0;s:9:\\\"productos\\\";i:1;s:17:\\\"productos.insumos\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}}}s:5:\\\"tries\\\";N;s:13:\\\"maxExceptions\\\";N;s:7:\\\"backoff\\\";N;s:10:\\\"retryUntil\\\";N;s:7:\\\"timeout\\\";N;s:13:\\\"failOnTimeout\\\";b:0;s:17:\\\"shouldBeEncrypted\\\";b:0;s:3:\\\"job\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\"},\"createdAt\":1771700976,\"delay\":null}', 0, NULL, 1771700976, 1771700976),
(71, 'default', '{\"uuid\":\"ee7844e8-52d8-4c46-beba-03e8778a8910\",\"displayName\":\"App\\\\Jobs\\\\EnviarComprobanteVentaJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\EnviarComprobanteVentaJob\",\"command\":\"O:34:\\\"App\\\\Jobs\\\\EnviarComprobanteVentaJob\\\":2:{s:10:\\\"\\u0000*\\u0000ventaId\\\";i:21;s:11:\\\"afterCommit\\\";b:1;}\"},\"createdAt\":1771700976,\"delay\":null}', 0, NULL, 1771700976, 1771700976),
(72, 'fiscal', '{\"uuid\":\"db1902b5-2866-4372-805c-d2be0a98bb1c\",\"displayName\":\"App\\\\Jobs\\\\EmitirDocumentoFiscalJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":5,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":\"10,30,60,120\",\"timeout\":120,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\EmitirDocumentoFiscalJob\",\"command\":\"O:33:\\\"App\\\\Jobs\\\\EmitirDocumentoFiscalJob\\\":3:{s:10:\\\"\\u0000*\\u0000ventaId\\\";i:21;s:5:\\\"queue\\\";s:6:\\\"fiscal\\\";s:5:\\\"delay\\\";O:25:\\\"Illuminate\\\\Support\\\\Carbon\\\":3:{s:4:\\\"date\\\";s:26:\\\"2026-02-21 14:09:39.979015\\\";s:13:\\\"timezone_type\\\";i:3;s:8:\\\"timezone\\\";s:12:\\\"America\\/Lima\\\";}}\"},\"createdAt\":1771700976,\"delay\":3}', 0, NULL, 1771700979, 1771700976),
(73, 'default', '{\"uuid\":\"80892ac1-b3d2-4ebb-b57c-29f2146c0201\",\"displayName\":\"App\\\\Listeners\\\\EmitirDocumentoFiscalListener\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Events\\\\CallQueuedListener\",\"command\":\"O:36:\\\"Illuminate\\\\Events\\\\CallQueuedListener\\\":20:{s:5:\\\"class\\\";s:43:\\\"App\\\\Listeners\\\\EmitirDocumentoFiscalListener\\\";s:6:\\\"method\\\";s:6:\\\"handle\\\";s:4:\\\"data\\\";a:1:{i:0;O:27:\\\"App\\\\Events\\\\CreateVentaEvent\\\":1:{s:5:\\\"venta\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:16:\\\"App\\\\Models\\\\Venta\\\";s:2:\\\"id\\\";i:22;s:9:\\\"relations\\\";a:2:{i:0;s:9:\\\"productos\\\";i:1;s:17:\\\"productos.insumos\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}}}s:5:\\\"tries\\\";N;s:13:\\\"maxExceptions\\\";N;s:7:\\\"backoff\\\";N;s:10:\\\"retryUntil\\\";N;s:7:\\\"timeout\\\";N;s:13:\\\"failOnTimeout\\\";b:0;s:17:\\\"shouldBeEncrypted\\\";b:0;s:3:\\\"job\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\"},\"createdAt\":1771701074,\"delay\":null}', 0, NULL, 1771701074, 1771701074),
(74, 'default', '{\"uuid\":\"4748213f-8e9c-43d9-87c5-e35ef1cb5926\",\"displayName\":\"App\\\\Jobs\\\\EnviarComprobanteVentaJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\EnviarComprobanteVentaJob\",\"command\":\"O:34:\\\"App\\\\Jobs\\\\EnviarComprobanteVentaJob\\\":2:{s:10:\\\"\\u0000*\\u0000ventaId\\\";i:22;s:11:\\\"afterCommit\\\";b:1;}\"},\"createdAt\":1771701074,\"delay\":null}', 0, NULL, 1771701074, 1771701074),
(75, 'fiscal', '{\"uuid\":\"2082cb23-6d97-4a1e-a8ef-cb8b519ae84c\",\"displayName\":\"App\\\\Jobs\\\\EmitirDocumentoFiscalJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":5,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":\"10,30,60,120\",\"timeout\":120,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\EmitirDocumentoFiscalJob\",\"command\":\"O:33:\\\"App\\\\Jobs\\\\EmitirDocumentoFiscalJob\\\":3:{s:10:\\\"\\u0000*\\u0000ventaId\\\";i:22;s:5:\\\"queue\\\";s:6:\\\"fiscal\\\";s:5:\\\"delay\\\";O:25:\\\"Illuminate\\\\Support\\\\Carbon\\\":3:{s:4:\\\"date\\\";s:26:\\\"2026-02-21 14:11:17.531733\\\";s:13:\\\"timezone_type\\\";i:3;s:8:\\\"timezone\\\";s:12:\\\"America\\/Lima\\\";}}\"},\"createdAt\":1771701074,\"delay\":3}', 0, NULL, 1771701077, 1771701074),
(76, 'default', '{\"uuid\":\"4446e973-bce6-4b12-9711-e7515a10e612\",\"displayName\":\"App\\\\Listeners\\\\EmitirDocumentoFiscalListener\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Events\\\\CallQueuedListener\",\"command\":\"O:36:\\\"Illuminate\\\\Events\\\\CallQueuedListener\\\":20:{s:5:\\\"class\\\";s:43:\\\"App\\\\Listeners\\\\EmitirDocumentoFiscalListener\\\";s:6:\\\"method\\\";s:6:\\\"handle\\\";s:4:\\\"data\\\";a:1:{i:0;O:27:\\\"App\\\\Events\\\\CreateVentaEvent\\\":1:{s:5:\\\"venta\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:16:\\\"App\\\\Models\\\\Venta\\\";s:2:\\\"id\\\";i:23;s:9:\\\"relations\\\";a:2:{i:0;s:9:\\\"productos\\\";i:1;s:17:\\\"productos.insumos\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}}}s:5:\\\"tries\\\";N;s:13:\\\"maxExceptions\\\";N;s:7:\\\"backoff\\\";N;s:10:\\\"retryUntil\\\";N;s:7:\\\"timeout\\\";N;s:13:\\\"failOnTimeout\\\";b:0;s:17:\\\"shouldBeEncrypted\\\";b:0;s:3:\\\"job\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\"},\"createdAt\":1771711326,\"delay\":null}', 0, NULL, 1771711326, 1771711326),
(77, 'default', '{\"uuid\":\"95e606a9-c8f2-4926-bc39-b73657243ee9\",\"displayName\":\"App\\\\Jobs\\\\EnviarComprobanteVentaJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\EnviarComprobanteVentaJob\",\"command\":\"O:34:\\\"App\\\\Jobs\\\\EnviarComprobanteVentaJob\\\":2:{s:10:\\\"\\u0000*\\u0000ventaId\\\";i:23;s:11:\\\"afterCommit\\\";b:1;}\"},\"createdAt\":1771711326,\"delay\":null}', 0, NULL, 1771711326, 1771711326),
(78, 'fiscal', '{\"uuid\":\"30d8da2b-1302-49d8-94f5-225303638dfe\",\"displayName\":\"App\\\\Jobs\\\\EmitirDocumentoFiscalJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":5,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":\"10,30,60,120\",\"timeout\":120,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\EmitirDocumentoFiscalJob\",\"command\":\"O:33:\\\"App\\\\Jobs\\\\EmitirDocumentoFiscalJob\\\":3:{s:10:\\\"\\u0000*\\u0000ventaId\\\";i:23;s:5:\\\"queue\\\";s:6:\\\"fiscal\\\";s:5:\\\"delay\\\";O:25:\\\"Illuminate\\\\Support\\\\Carbon\\\":3:{s:4:\\\"date\\\";s:26:\\\"2026-02-21 17:02:09.874942\\\";s:13:\\\"timezone_type\\\";i:3;s:8:\\\"timezone\\\";s:12:\\\"America\\/Lima\\\";}}\"},\"createdAt\":1771711326,\"delay\":3}', 0, NULL, 1771711329, 1771711326);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `kardex`
--

DROP TABLE IF EXISTS `kardex`;
CREATE TABLE IF NOT EXISTS `kardex` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint UNSIGNED NOT NULL,
  `producto_id` bigint UNSIGNED DEFAULT NULL,
  `insumo_id` bigint UNSIGNED DEFAULT NULL,
  `tipo_transaccion` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion_transaccion` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `entrada` int DEFAULT NULL,
  `salida` int DEFAULT NULL,
  `saldo` int NOT NULL,
  `costo_unitario` decimal(8,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `kardex_empresa_id_index` (`empresa_id`),
  KEY `kardex_insumo_id_foreign` (`insumo_id`),
  KEY `idx_kardex_producto_fecha` (`producto_id`,`created_at`),
  KEY `idx_kardex_producto_tipo` (`producto_id`,`tipo_transaccion`)
) ENGINE=MyISAM AUTO_INCREMENT=380 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `kardex`
--

INSERT INTO `kardex` (`id`, `empresa_id`, `producto_id`, `insumo_id`, `tipo_transaccion`, `descripcion_transaccion`, `entrada`, `salida`, `saldo`, `costo_unitario`, `created_at`, `updated_at`) VALUES
(379, 1, 82, NULL, 'COMPRA', 'Ajuste manual: compra', 1, NULL, 49, 35000.00, '2026-02-21 22:03:34', '2026-02-21 22:03:34'),
(378, 1, NULL, 20, 'VENTA', 'Venta #22 - Item: Pizza hawaiana', NULL, 100, -700, 5.00, '2026-02-21 19:11:14', '2026-02-21 19:11:14'),
(377, 1, NULL, 19, 'VENTA', 'Venta #22 - Item: Pizza hawaiana', NULL, 1, -7, 2000.00, '2026-02-21 19:11:14', '2026-02-21 19:11:14'),
(376, 1, 76, NULL, 'VENTA', 'Venta (Legacy) #21', NULL, 1, 46, 3150.00, '2026-02-21 19:09:36', '2026-02-21 19:09:36'),
(365, 1, 80, NULL, 'VENTA', 'Venta (Legacy) #15', NULL, 1, 48, 9450.00, '2026-02-21 16:00:39', '2026-02-21 16:00:39'),
(366, 1, 77, NULL, 'VENTA', 'Venta (Legacy) #16', NULL, 2, 45, 1218.00, '2026-02-21 16:00:40', '2026-02-21 16:00:40'),
(367, 1, 78, NULL, 'VENTA', 'Venta (Legacy) #16', NULL, 1, 40, 2100.00, '2026-02-21 16:00:40', '2026-02-21 16:00:40'),
(368, 1, 76, NULL, 'VENTA', 'Venta (Legacy) #17', NULL, 1, 47, 3150.00, '2026-02-21 16:00:40', '2026-02-21 16:00:40'),
(369, 1, 79, NULL, 'VENTA', 'Venta (Legacy) #17', NULL, 1, 46, 3675.00, '2026-02-21 16:00:40', '2026-02-21 16:00:40'),
(370, 1, 80, NULL, 'VENTA', 'Venta (Legacy) #18', NULL, 2, 46, 9450.00, '2026-02-21 16:00:40', '2026-02-21 16:00:40'),
(371, 1, 78, NULL, 'VENTA', 'Venta (Legacy) #18', NULL, 1, 39, 2100.00, '2026-02-21 16:00:40', '2026-02-21 16:00:40'),
(372, 1, 77, NULL, 'VENTA', 'Venta (Legacy) #19', NULL, 1, 44, 1218.00, '2026-02-21 16:01:14', '2026-02-21 16:01:14'),
(373, 1, NULL, 19, 'VENTA', 'Venta #20 - Item: Pizza hawaiana', NULL, 1, -6, 2000.00, '2026-02-21 17:37:39', '2026-02-21 17:37:39'),
(374, 1, NULL, 20, 'VENTA', 'Venta #20 - Item: Pizza hawaiana', NULL, 100, -600, 5.00, '2026-02-21 17:37:39', '2026-02-21 17:37:39'),
(375, 1, NULL, 3, 'VENTA', 'Venta #21 - Item: Perro caliente', NULL, 53, 78, 15.00, '2026-02-21 19:09:36', '2026-02-21 19:09:36'),
(364, 1, 74, NULL, 'VENTA', 'Venta (Legacy) #15', NULL, 1, 40, 8820.00, '2026-02-21 16:00:39', '2026-02-21 16:00:39'),
(363, 1, 78, NULL, 'VENTA', 'Venta (Legacy) #14', NULL, 1, 41, 2100.00, '2026-02-21 16:00:39', '2026-02-21 16:00:39'),
(362, 1, 77, NULL, 'VENTA', 'Venta (Legacy) #14', NULL, 2, 47, 1218.00, '2026-02-21 16:00:39', '2026-02-21 16:00:39'),
(361, 1, NULL, 3, 'VENTA', 'Venta #14 - Item: Perro caliente', NULL, 53, 131, 15.00, '2026-02-21 16:00:39', '2026-02-21 16:00:39'),
(360, 1, 79, NULL, 'VENTA', 'Venta (Legacy) #13', NULL, 2, 47, 3675.00, '2026-02-21 16:00:39', '2026-02-21 16:00:39'),
(359, 1, NULL, 20, 'VENTA', 'Venta #13 - Item: Pizza hawaiana', NULL, 100, -500, 5.00, '2026-02-21 16:00:39', '2026-02-21 16:00:39'),
(358, 1, NULL, 19, 'VENTA', 'Venta #13 - Item: Pizza hawaiana', NULL, 1, -5, 2000.00, '2026-02-21 16:00:39', '2026-02-21 16:00:39'),
(357, 1, 76, NULL, 'VENTA', 'Venta (Legacy) #12', NULL, 1, 48, 3150.00, '2026-02-21 16:00:38', '2026-02-21 16:00:38'),
(356, 1, 74, NULL, 'VENTA', 'Venta (Legacy) #12', NULL, 1, 41, 8820.00, '2026-02-21 16:00:38', '2026-02-21 16:00:38'),
(355, 1, 77, NULL, 'VENTA', 'Venta (Legacy) #11', NULL, 1, 49, 1218.00, '2026-02-21 16:00:38', '2026-02-21 16:00:38'),
(353, 1, 78, NULL, 'VENTA', 'Venta (Legacy) #10', NULL, 1, 42, 2100.00, '2026-02-21 16:00:37', '2026-02-21 16:00:37'),
(354, 1, NULL, 3, 'VENTA', 'Venta #11 - Item: Perro caliente', NULL, 53, 184, 15.00, '2026-02-21 16:00:38', '2026-02-21 16:00:38'),
(352, 1, 74, NULL, 'VENTA', 'Venta (Legacy) #10', NULL, 1, 42, 8820.00, '2026-02-21 16:00:37', '2026-02-21 16:00:37'),
(350, 1, NULL, 20, 'VENTA', 'Venta #9 - Item: Pizza hawaiana', NULL, 100, -400, 5.00, '2026-02-21 02:09:07', '2026-02-21 02:09:07'),
(351, 1, NULL, 19, 'VENTA', 'Venta #9 - Item: Pizza hawaiana', NULL, 1, -4, 2000.00, '2026-02-21 02:09:07', '2026-02-21 02:09:07'),
(349, 1, NULL, 3, 'VENTA', 'Venta #9 - Item: Perro caliente', NULL, 105, 237, 15.00, '2026-02-21 02:09:07', '2026-02-21 02:09:07'),
(348, 1, 74, NULL, 'VENTA', 'Venta (Legacy) #9', NULL, 3, 43, 8820.00, '2026-02-21 02:09:07', '2026-02-21 02:09:07'),
(347, 1, 75, NULL, 'VENTA', 'Venta (Legacy) #9', NULL, 6, 38, 11445.00, '2026-02-21 02:09:07', '2026-02-21 02:09:07'),
(345, 1, 78, NULL, 'VENTA', 'Venta (Legacy) #9', NULL, 3, 43, 2100.00, '2026-02-21 02:09:07', '2026-02-21 02:09:07'),
(346, 1, NULL, 21, 'VENTA', 'Venta #9 - Item: Zumo de Limón', NULL, 9, -18, 200.00, '2026-02-21 02:09:07', '2026-02-21 02:09:07'),
(343, 1, NULL, 19, 'VENTA', 'Venta #8 - Item: Pizza hawaiana', NULL, 1, -3, 2000.00, '2026-02-21 02:00:47', '2026-02-21 02:00:47'),
(344, 1, NULL, 20, 'VENTA', 'Venta #8 - Item: Pizza hawaiana', NULL, 100, -300, 5.00, '2026-02-21 02:00:47', '2026-02-21 02:00:47'),
(342, 1, NULL, 3, 'VENTA', 'Venta #7 - Item: Perro caliente', NULL, 105, 342, 15.00, '2026-02-21 01:57:49', '2026-02-21 01:57:49'),
(340, 1, NULL, 21, 'VENTA', 'Venta #7 - Item: Zumo de Limón', NULL, 9, -9, 200.00, '2026-02-21 01:57:49', '2026-02-21 01:57:49'),
(341, 1, 74, NULL, 'VENTA', 'Venta (Legacy) #7', NULL, 3, 46, 8820.00, '2026-02-21 01:57:49', '2026-02-21 01:57:49'),
(296, 1, NULL, 1, 'COMPRA', 'Entrada de stock: 125', 150, NULL, 799, 3500.00, '2026-02-19 20:25:55', '2026-02-19 20:25:55'),
(295, 1, NULL, 1, 'COMPRA', 'Entrada de stock: 125', 150, NULL, 649, 3500.00, '2026-02-19 20:25:52', '2026-02-19 20:25:52'),
(339, 1, 75, NULL, 'VENTA', 'Venta (Legacy) #7', NULL, 6, 44, 11445.00, '2026-02-21 01:57:49', '2026-02-21 01:57:49'),
(294, 1, NULL, 8, 'COMPRA', 'Entrada de stock: 125', 100, NULL, 547, 3500.00, '2026-02-19 19:48:21', '2026-02-19 19:48:21'),
(337, 1, NULL, 19, 'VENTA', 'Venta #7 - Item: Pizza hawaiana', NULL, 1, -2, 2000.00, '2026-02-21 01:57:48', '2026-02-21 01:57:48'),
(338, 1, 78, NULL, 'VENTA', 'Venta (Legacy) #7', NULL, 3, 46, 2100.00, '2026-02-21 01:57:49', '2026-02-21 01:57:49'),
(336, 1, NULL, 20, 'VENTA', 'Venta #7 - Item: Pizza hawaiana', NULL, 100, -200, 5.00, '2026-02-21 01:57:48', '2026-02-21 01:57:48'),
(335, 1, 80, NULL, 'VENTA', 'Venta (Legacy) #6', NULL, 1, 49, 9450.00, '2026-02-21 01:36:39', '2026-02-21 01:36:39'),
(334, 1, 79, NULL, 'VENTA', 'Venta (Legacy) #6', NULL, 1, 49, 3675.00, '2026-02-21 01:36:39', '2026-02-21 01:36:39'),
(333, 1, NULL, 20, 'VENTA', 'Venta #6 - Item: Pizza hawaiana', NULL, 100, -100, 5.00, '2026-02-21 01:36:39', '2026-02-21 01:36:39'),
(332, 1, NULL, 19, 'VENTA', 'Venta #6 - Item: Pizza hawaiana', NULL, 1, -1, 2000.00, '2026-02-21 01:36:38', '2026-02-21 01:36:38'),
(330, 1, 81, NULL, 'VENTA', 'Venta (Legacy) #3', NULL, 1, 49, 9450.00, '2026-02-20 17:33:35', '2026-02-20 17:33:35'),
(331, 1, 76, NULL, 'VENTA', 'Venta (Legacy) #5', NULL, 1, 49, 3150.00, '2026-02-21 00:01:16', '2026-02-21 00:01:16'),
(329, 1, 82, NULL, 'VENTA', 'Venta (Legacy) #3', NULL, 2, 48, 5040.00, '2026-02-20 17:33:34', '2026-02-20 17:33:34'),
(328, 1, 78, NULL, 'VENTA', 'Venta (Legacy) #2', NULL, 1, 49, 2100.00, '2026-02-20 17:32:33', '2026-02-20 17:32:33'),
(327, 1, NULL, 18, 'VENTA', 'Venta #2 - Item: Palomitas Grandes', NULL, 0, 0, 8000.00, '2026-02-20 17:32:33', '2026-02-20 17:32:33'),
(326, 1, NULL, 17, 'VENTA', 'Venta #2 - Item: Palomitas Grandes', NULL, 0, 0, 5000.00, '2026-02-20 17:32:33', '2026-02-20 17:32:33'),
(325, 1, 74, NULL, 'VENTA', 'Venta (Legacy) #1', NULL, 1, 49, 8820.00, '2026-02-20 16:14:23', '2026-02-20 16:14:23'),
(324, 1, NULL, 3, 'VENTA', 'Venta #1 - Item: Perro caliente', NULL, 53, 447, 15.00, '2026-02-20 16:14:23', '2026-02-20 16:14:23'),
(322, 1, NULL, 17, 'VENTA', 'Venta #1 - Item: Palomitas Grandes', NULL, 0, 0, 5000.00, '2026-02-20 16:14:23', '2026-02-20 16:14:23'),
(323, 1, NULL, 18, 'VENTA', 'Venta #1 - Item: Palomitas Grandes', NULL, 0, 0, 8000.00, '2026-02-20 16:14:23', '2026-02-20 16:14:23'),
(277, 1, 82, NULL, 'APERTURA', 'Apertura del producto', 50, NULL, 50, 5040.00, '2026-02-16 16:01:34', '2026-02-16 16:01:34'),
(276, 1, 81, NULL, 'APERTURA', 'Apertura del producto', 50, NULL, 50, 9450.00, '2026-02-16 16:01:34', '2026-02-16 16:01:34'),
(275, 1, 80, NULL, 'APERTURA', 'Apertura del producto', 50, NULL, 50, 9450.00, '2026-02-16 16:01:34', '2026-02-16 16:01:34'),
(274, 1, 79, NULL, 'APERTURA', 'Apertura del producto', 50, NULL, 50, 3675.00, '2026-02-16 16:01:34', '2026-02-16 16:01:34'),
(273, 1, 78, NULL, 'APERTURA', 'Apertura del producto', 50, NULL, 50, 2100.00, '2026-02-16 16:01:34', '2026-02-16 16:01:34'),
(272, 1, 77, NULL, 'APERTURA', 'Apertura del producto', 50, NULL, 50, 1218.00, '2026-02-16 16:01:34', '2026-02-16 16:01:34'),
(271, 1, 76, NULL, 'APERTURA', 'Apertura del producto', 50, NULL, 50, 3150.00, '2026-02-16 16:01:34', '2026-02-16 16:01:34'),
(270, 1, 75, NULL, 'APERTURA', 'Apertura del producto', 50, NULL, 50, 11445.00, '2026-02-16 16:01:34', '2026-02-16 16:01:34'),
(269, 1, 74, NULL, 'APERTURA', 'Apertura del producto', 50, NULL, 50, 8820.00, '2026-02-16 16:01:33', '2026-02-16 16:01:33'),
(268, 1, 73, NULL, 'APERTURA', 'Apertura del producto', 50, NULL, 50, 4462.50, '2026-02-16 16:01:33', '2026-02-16 16:01:33'),
(267, 1, NULL, 16, 'APERTURA', 'Apertura del producto', 500, NULL, 500, 20.00, '2026-02-16 16:01:32', '2026-02-16 16:01:32'),
(266, 1, NULL, 15, 'APERTURA', 'Apertura del producto', 500, NULL, 500, 8.00, '2026-02-16 16:01:32', '2026-02-16 16:01:32'),
(265, 1, NULL, 14, 'APERTURA', 'Apertura del producto', 500, NULL, 500, 10.00, '2026-02-16 16:01:32', '2026-02-16 16:01:32'),
(264, 1, NULL, 13, 'APERTURA', 'Apertura del producto', 500, NULL, 500, 80.00, '2026-02-16 16:01:32', '2026-02-16 16:01:32'),
(263, 1, NULL, 12, 'APERTURA', 'Apertura del producto', 500, NULL, 500, 60.00, '2026-02-16 16:01:32', '2026-02-16 16:01:32'),
(262, 1, NULL, 11, 'APERTURA', 'Apertura del producto', 500, NULL, 500, 60.00, '2026-02-16 16:01:32', '2026-02-16 16:01:32'),
(261, 1, NULL, 10, 'APERTURA', 'Apertura del producto', 500, NULL, 500, 3500.00, '2026-02-16 16:01:32', '2026-02-16 16:01:32'),
(260, 1, NULL, 9, 'APERTURA', 'Apertura del producto', 500, NULL, 500, 1500.00, '2026-02-16 16:01:32', '2026-02-16 16:01:32'),
(259, 1, NULL, 8, 'APERTURA', 'Apertura del producto', 500, NULL, 500, 2000.00, '2026-02-16 16:01:32', '2026-02-16 16:01:32'),
(258, 1, NULL, 7, 'APERTURA', 'Apertura del producto', 500, NULL, 500, 12.00, '2026-02-16 16:01:32', '2026-02-16 16:01:32'),
(257, 1, NULL, 6, 'APERTURA', 'Apertura del producto', 500, NULL, 500, 25.00, '2026-02-16 16:01:32', '2026-02-16 16:01:32'),
(256, 1, NULL, 5, 'APERTURA', 'Apertura del producto', 500, NULL, 500, 28.00, '2026-02-16 16:01:32', '2026-02-16 16:01:32'),
(255, 1, NULL, 4, 'APERTURA', 'Apertura del producto', 500, NULL, 500, 3000.00, '2026-02-16 16:01:32', '2026-02-16 16:01:32'),
(254, 1, NULL, 3, 'APERTURA', 'Apertura del producto', 500, NULL, 500, 15.00, '2026-02-16 16:01:32', '2026-02-16 16:01:32'),
(253, 1, NULL, 2, 'APERTURA', 'Apertura del producto', 500, NULL, 500, 2000.00, '2026-02-16 16:01:32', '2026-02-16 16:01:32'),
(252, 1, NULL, 1, 'APERTURA', 'Apertura del producto', 500, NULL, 500, 1500.00, '2026-02-16 16:01:31', '2026-02-16 16:01:32');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `marcas`
--

DROP TABLE IF EXISTS `marcas`;
CREATE TABLE IF NOT EXISTS `marcas` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint UNSIGNED DEFAULT NULL,
  `caracteristica_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `marcas_caracteristica_id_unique` (`caracteristica_id`),
  KEY `marcas_empresa_id_foreign` (`empresa_id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `marcas`
--

INSERT INTO `marcas` (`id`, `empresa_id`, `caracteristica_id`, `created_at`, `updated_at`) VALUES
(1, 1, 10, '2026-02-09 17:34:31', '2026-02-09 17:34:31'),
(2, 1, 14, '2026-02-10 18:54:05', '2026-02-10 18:54:05'),
(3, 1, 19, '2026-02-10 18:55:08', '2026-02-10 18:55:08'),
(4, 1, 24, '2026-02-10 18:58:58', '2026-02-10 18:58:58'),
(5, 1, 29, '2026-02-10 19:16:29', '2026-02-10 19:16:29'),
(6, 1, 34, '2026-02-10 20:40:21', '2026-02-10 20:40:21'),
(7, 1, 36, '2026-02-10 21:37:01', '2026-02-10 21:37:01'),
(8, 1, 40, '2026-02-16 16:01:31', '2026-02-16 16:01:31');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=128 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2023_03_10_011515_create_documentos_table', 1),
(6, '2023_03_10_012149_create_personas_table', 1),
(7, '2023_03_10_015030_create_proveedores_table', 1),
(8, '2023_03_10_015806_create_clientes_table', 1),
(9, '2023_03_10_020010_create_comprobantes_table', 1),
(10, '2023_03_10_020257_create_compras_table', 1),
(11, '2023_03_10_022517_create_ventas_table', 1),
(12, '2023_03_10_023329_create_caracteristicas_table', 1),
(13, '2023_03_10_023555_create_categorias_table', 1),
(14, '2023_03_10_023818_create_marcas_table', 1),
(15, '2023_03_10_023953_create_presentaciones_table', 1),
(16, '2023_03_10_024112_create_productos_table', 1),
(17, '2023_03_10_025748_create_compra_producto_table', 1),
(18, '2023_03_10_030137_create_producto_venta_table', 1),
(19, '2025_01_22_114613_create_permission_tables', 1),
(20, '2025_01_23_113358_create_monedas_table', 1),
(21, '2025_01_23_113626_create_empresas_table', 1),
(22, '2025_01_23_114215_create_empleados_table', 1),
(23, '2025_01_23_114438_update_columns_to_users_table', 1),
(24, '2025_01_23_115036_create_cajas_table', 1),
(25, '2025_01_23_115425_create_movimientos_table', 1),
(26, '2025_01_23_115923_update_columns_to_ventas_table', 1),
(27, '2025_01_23_120147_create_ubicaciones_table', 1),
(28, '2025_01_23_121110_create_inventarios_table', 1),
(29, '2025_01_23_121449_create_kardexes_table', 1),
(30, '2025_02_03_102442_create_activity_logs_table', 1),
(31, '2025_05_20_213434_create_jobs_table', 1),
(32, '2025_05_24_210954_create_notifications_table', 1),
(33, '2026_01_30_114320_add_empresa_id_to_users_table', 1),
(34, '2026_01_30_114325_add_empresa_id_to_empleados_table', 1),
(35, '2026_01_30_114330_add_empresa_id_to_cajas_table', 1),
(36, '2026_01_30_114335_update_movimientos_table', 1),
(37, '2026_01_30_114340_add_fields_to_ventas_table', 1),
(38, '2026_01_30_114345_add_empresa_id_to_productos_table', 1),
(39, '2026_01_30_114350_add_empresa_id_to_compras_table', 1),
(40, '2026_01_30_114355_add_empresa_id_to_clientes_table', 1),
(41, '2026_01_30_114400_add_empresa_id_to_proveedores_table', 1),
(42, '2026_01_30_114405_add_empresa_id_to_inventarios_table', 1),
(43, '2026_01_30_114410_add_empresa_id_to_kardexes_table', 1),
(44, '2026_01_30_114415_add_tarifa_unitaria_to_producto_venta_table', 1),
(45, '2026_01_30_114420_create_stripe_configs_table', 1),
(46, '2026_01_30_114425_create_payment_transactions_table', 1),
(47, '2026_01_30_115000_add_estado_pago_to_ventas_table', 1),
(48, '2026_01_31_000001_add_stripe_connect_to_empresas_table', 1),
(49, '2026_01_31_000001_create_saas_plans_table', 1),
(50, '2026_01_31_000002_add_subscription_fields_to_empresa_table', 1),
(51, '2026_01_31_134802_create_planes_table', 1),
(52, '2026_02_03_203000_remove_unique_constraint_from_empresa_moneda', 1),
(53, '2026_02_03_222603_add_user_agent_to_activity_logs_table', 2),
(54, '2026_02_03_222848_create_venta_pagos_table', 3),
(55, '2026_02_03_225120_modify_metodo_pago_in_ventas_table', 4),
(56, '2026_02_03_230713_create_cinema_tables', 5),
(58, '2026_02_04_113500_create_precios_entradas_table', 6),
(59, '2026_02_04_120724_add_venta_id_to_funcion_asientos_table', 7),
(60, '2026_02_04_121000_add_venta_id_to_funcion_asientos_table', 7),
(61, '2026_02_04_140001_create_insumos_table', 8),
(62, '2026_02_04_140002_create_recetas_table', 8),
(63, '2026_02_04_210001_add_cinema_fields_to_productos', 9),
(64, '2026_02_05_114826_create_distribuidores_table', 10),
(65, '2026_02_05_114901_add_cinema_admin_fields_to_productos', 10),
(66, '2026_02_05_122323_add_advanced_fields_to_cinema_tables', 11),
(67, '2026_02_05_135232_update_inventory_system_core', 12),
(68, '2026_02_05_135328_adjust_insumos_units_enum', 12),
(69, '2026_02_05_153551_add_username_to_users_table', 13),
(70, '2026_02_05_160349_add_canal_to_ventas_table', 14),
(71, '2026_02_05_171000_add_merma_to_recetas_table', 15),
(72, '2026_02_05_221806_add_advanced_inventory_fields', 16),
(73, '2026_02_06_165137_add_business_logic_fields_to_productos_table', 17),
(74, '2026_02_06_192723_add_profitability_fields_to_productos_table', 18),
(75, '2026_02_06_195419_modify_canal_in_ventas_table', 19),
(76, '2026_02_07_105002_add_margen_objetivo_to_productos_table', 20),
(77, '2026_02_07_153500_create_peliculas_table', 21),
(78, '2026_02_07_153400_update_funciones_table_add_pelicula_id', 22),
(79, '2026_02_07_153501_migrate_data_products_to_movies', 22),
(80, '2026_02_07_153502_cleanup_productos_table', 22),
(81, '2026_02_09_114000_create_business_configurations_table', 23),
(82, '2026_02_09_181101_update_payment_transactions_enum_values', 24),
(83, '2026_02_09_183029_add_tipo_and_origen_to_ventas_table', 24),
(84, '2026_02_09_184516_modify_kardex_to_support_insumos', 25),
(85, '2026_02_09_184633_create_inventario_ajustes_table', 26),
(86, '2026_02_09_184654_create_gasto_operacionals_table', 26),
(87, '2026_02_09_190524_add_expired_state_to_ventas', 27),
(88, '2026_02_09_190824_add_inventory_tracking_to_ventas', 28),
(89, '2026_02_09_200155_add_integrity_fields_to_productos_table', 29),
(90, '2026_02_09_205625_add_empresa_id_to_catalogs', 30),
(91, '2026_02_10_161523_add_temporal_reservation_to_funcion_asientos', 31),
(92, '2026_02_10_163054_adjust_caja_and_create_asiento_link_tables', 32),
(93, '2026_02_11_103743_normalize_funcion_asientos_estados', 33),
(94, '2026_02_11_153500_normalize_funcion_asientos_estados', 34),
(95, '2026_02_11_160000_update_ventas_canal_enum_add_mixta', 35),
(96, '2026_02_11_170000_fix_movimientos_enum_types', 36),
(97, '2026_02_12_133102_create_devoluciones_table', 37),
(98, '2026_02_12_133216_add_devuelta_to_ventas_status', 38),
(99, '2026_02_13_212242_add_empresa_id_to_activity_logs_table', 39),
(100, '2026_02_13_212310_add_critical_indexes_for_performance', 40),
(101, '2026_02_13_212346_add_movimiento_creado_at_to_ventas_table', 40),
(102, '2026_02_13_213600_add_cierre_fields_to_cajas_table', 40),
(103, '2026_02_13_222130_create_alertas_table', 40),
(104, '2026_02_14_122830_add_pin_code_to_users_table', 41),
(105, '2026_02_14_214400_add_tarjeta_fields_to_cajas_table', 42),
(106, '2026_02_16_094037_add_details_to_cajas_table', 43),
(107, '2026_02_16_095449_add_reopening_fields_to_cajas_table', 44),
(108, '2026_02_16_100923_add_closing_audit_fields_to_cajas_table', 45),
(109, '2026_02_16_145751_add_indexes_for_performance', 46),
(110, '2026_02_16_173348_add_desglose_metodos_pago_to_cajas', 47),
(111, '2026_02_16_175813_add_all_missing_fields_to_cajas_table', 48),
(112, '2026_02_16_180030_rename_cajas_old_fields', 49),
(113, '2026_02_16_180154_add_missing_fields_to_ventas_table', 50),
(114, '2026_02_16_181352_estandarizar_nombres_columnas_cajas', 51),
(115, '2026_02_17_100343_create_tarifas_table', 52),
(117, '2026_02_17_102228_create_facturas_compra_table', 53),
(118, '2026_02_17_124845_create_devoluciones_table_v2', 54),
(119, '2026_02_17_135722_add_tax_discrimination_to_ventas_table', 55),
(120, '2026_02_18_102301_create_periodos_operativos_table_and_add_fecha_to_ventas', 56),
(121, '2026_02_18_115957_prepare_ventas_table_for_phase_4_2', 57),
(122, '2026_02_19_130655_create_documentos_fiscales_table', 58),
(123, '2026_02_19_130952_create_configuracion_numeracion_table', 59),
(124, '2026_02_19_153103_add_fiscal_fields_to_ventas_table', 60),
(125, '2026_02_19_164556_create_vertical_configs_table', 61),
(126, '2026_02_19_230300_extend_estado_enum_documentos_fiscales', 62),
(127, '2026_02_20_170000_create_rules_tables', 63);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `model_has_permissions`
--

DROP TABLE IF EXISTS `model_has_permissions`;
CREATE TABLE IF NOT EXISTS `model_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `model_has_roles`
--

DROP TABLE IF EXISTS `model_has_roles`;
CREATE TABLE IF NOT EXISTS `model_has_roles` (
  `role_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 1),
(1, 'App\\Models\\User', 2),
(1, 'App\\Models\\User', 3),
(1, 'App\\Models\\User', 4),
(1, 'App\\Models\\User', 5),
(1, 'App\\Models\\User', 6),
(1, 'App\\Models\\User', 7),
(1, 'App\\Models\\User', 8),
(1, 'App\\Models\\User', 9),
(1, 'App\\Models\\User', 10),
(1, 'App\\Models\\User', 11),
(1, 'App\\Models\\User', 12),
(1, 'App\\Models\\User', 13),
(1, 'App\\Models\\User', 14),
(1, 'App\\Models\\User', 15),
(1, 'App\\Models\\User', 16),
(1, 'App\\Models\\User', 17),
(1, 'App\\Models\\User', 18),
(1, 'App\\Models\\User', 19),
(2, 'App\\Models\\User', 3),
(2, 'App\\Models\\User', 4),
(2, 'App\\Models\\User', 6),
(3, 'App\\Models\\User', 2),
(3, 'App\\Models\\User', 6),
(4, 'App\\Models\\User', 6);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `monedas`
--

DROP TABLE IF EXISTS `monedas`;
CREATE TABLE IF NOT EXISTS `monedas` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `estandar_iso` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre_completo` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `simbolo` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movimientos`
--

DROP TABLE IF EXISTS `movimientos`;
CREATE TABLE IF NOT EXISTS `movimientos` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint UNSIGNED NOT NULL,
  `tipo` enum('VENTA','RETIRO','INGRESO','EGRESO','CORTESIA','BAJA','DEVOLUCION') COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `monto` decimal(8,2) NOT NULL,
  `metodo_pago` enum('EFECTIVO','TARJETA','TRANSFERENCIA','QR','MIXTO','STRIPE') COLLATE utf8mb4_unicode_ci NOT NULL,
  `caja_id` bigint UNSIGNED NOT NULL,
  `venta_id` bigint UNSIGNED DEFAULT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `movimientos_venta_id_foreign` (`venta_id`),
  KEY `movimientos_empresa_id_caja_id_created_at_index` (`empresa_id`,`caja_id`,`created_at`),
  KEY `movimientos_user_id_foreign` (`user_id`),
  KEY `idx_movimientos_caja_tipo_fecha` (`caja_id`,`tipo`,`created_at`),
  KEY `idx_movimientos_caja_fecha` (`caja_id`,`created_at`)
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `movimientos`
--

INSERT INTO `movimientos` (`id`, `empresa_id`, `tipo`, `descripcion`, `monto`, `metodo_pago`, `caja_id`, `venta_id`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 1, 'INGRESO', 'Venta POS #1 - Canal: mixta', 221000.00, 'EFECTIVO', 1, 1, 6, '2026-02-20 16:14:22', '2026-02-20 16:14:22'),
(2, 1, 'INGRESO', 'Venta POS #2 - Canal: mixta', 86020.00, 'EFECTIVO', 1, 2, 6, '2026-02-20 17:32:32', '2026-02-20 17:32:32'),
(3, 1, 'INGRESO', 'Venta POS #3 - Canal: confiteria', 47096.00, 'EFECTIVO', 1, 3, 6, '2026-02-20 17:33:34', '2026-02-20 17:33:34'),
(4, 1, 'INGRESO', 'Venta POS #4 - Canal: ventanilla', 30000.00, 'EFECTIVO', 1, 4, 6, '2026-02-20 18:11:01', '2026-02-20 18:11:01'),
(5, 1, 'INGRESO', 'Venta POS #5 - Canal: confiteria', 3780.00, 'EFECTIVO', 1, 5, 6, '2026-02-21 00:01:16', '2026-02-21 00:01:16'),
(6, 1, 'INGRESO', 'Venta POS #6 - Canal: mixta', 146000.00, 'EFECTIVO', 1, 6, 6, '2026-02-21 01:36:38', '2026-02-21 01:36:38'),
(7, 1, 'INGRESO', 'Venta POS #7 - Canal: confiteria', 510500.00, 'EFECTIVO', 1, 7, 6, '2026-02-21 01:57:48', '2026-02-21 01:57:48'),
(8, 1, 'INGRESO', 'Venta POS #8 - Canal: confiteria', 37000.00, 'EFECTIVO', 1, 8, 6, '2026-02-21 02:00:47', '2026-02-21 02:00:47'),
(9, 1, 'INGRESO', 'Venta POS #9 - Canal: confiteria', 510500.00, 'EFECTIVO', 1, 9, 6, '2026-02-21 02:09:07', '2026-02-21 02:09:07'),
(10, 1, 'INGRESO', 'Venta POS #10 - Canal: ventanilla', 73104.00, 'EFECTIVO', 1, 10, 6, '2026-02-21 16:00:36', '2026-02-21 16:00:36'),
(11, 1, 'INGRESO', 'Venta POS #11 - Canal: ventanilla', 199000.00, 'EFECTIVO', 1, 11, 6, '2026-02-21 16:00:38', '2026-02-21 16:00:38'),
(12, 1, 'INGRESO', 'Venta POS #12 - Canal: ventanilla', 82864.00, 'EFECTIVO', 1, 12, 6, '2026-02-21 16:00:38', '2026-02-21 16:00:38'),
(13, 1, 'INGRESO', 'Venta POS #13 - Canal: ventanilla', 165820.00, 'EFECTIVO', 1, 13, 6, '2026-02-21 16:00:38', '2026-02-21 16:00:38'),
(14, 1, 'INGRESO', 'Venta POS #14 - Canal: ventanilla', 155520.00, 'EFECTIVO', 1, 14, 6, '2026-02-21 16:00:39', '2026-02-21 16:00:39'),
(15, 1, 'INGRESO', 'Venta POS #15 - Canal: ventanilla', 51924.00, 'EFECTIVO', 1, 15, 6, '2026-02-21 16:00:39', '2026-02-21 16:00:39'),
(16, 1, 'INGRESO', 'Venta POS #16 - Canal: ventanilla', 99020.00, 'EFECTIVO', 1, 16, 6, '2026-02-21 16:00:40', '2026-02-21 16:00:40'),
(17, 1, 'INGRESO', 'Venta POS #17 - Canal: ventanilla', 106690.00, 'EFECTIVO', 1, 17, 6, '2026-02-21 16:00:40', '2026-02-21 16:00:40'),
(18, 1, 'INGRESO', 'Venta POS #18 - Canal: ventanilla', 123700.00, 'EFECTIVO', 1, 18, 6, '2026-02-21 16:00:40', '2026-02-21 16:00:40'),
(19, 1, 'INGRESO', 'Venta POS #19 - Canal: ventanilla', 14000.00, 'EFECTIVO', 1, 19, 6, '2026-02-21 16:01:14', '2026-02-21 16:01:14'),
(20, 1, 'INGRESO', 'Venta POS #20 - Canal: confiteria', 37000.00, 'EFECTIVO', 1, 20, 6, '2026-02-21 17:37:39', '2026-02-21 17:37:39'),
(21, 1, 'INGRESO', 'Venta POS #21 - Canal: mixta', 98780.00, 'EFECTIVO', 2, 21, 6, '2026-02-21 19:09:36', '2026-02-21 19:09:36'),
(22, 1, 'INGRESO', 'Venta POS #22 - Canal: confiteria', 37000.00, 'EFECTIVO', 2, 22, 6, '2026-02-21 19:11:14', '2026-02-21 19:11:14'),
(23, 1, 'INGRESO', 'Venta POS #23 - Canal: confiteria', 8500.00, 'EFECTIVO', 2, 23, 6, '2026-02-21 22:02:06', '2026-02-21 22:02:06');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notifications`
--

DROP TABLE IF EXISTS `notifications`;
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_id` bigint UNSIGNED NOT NULL,
  `data` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE IF NOT EXISTS `password_resets` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `payment_transactions`
--

DROP TABLE IF EXISTS `payment_transactions`;
CREATE TABLE IF NOT EXISTS `payment_transactions` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint UNSIGNED NOT NULL,
  `venta_id` bigint UNSIGNED NOT NULL,
  `payment_method` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `stripe_payment_intent_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'ID del PaymentIntent de Stripe (si aplica)',
  `stripe_charge_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'ID de la carga de Stripe después de confirmada',
  `amount_paid` decimal(10,2) NOT NULL COMMENT 'Monto pagado en esta transacción',
  `currency` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'USD' COMMENT 'Moneda del pago',
  `status` enum('PENDING','SUCCESS','FAILED','REFUNDED','CANCELLED') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PENDING' COMMENT 'Estado del pago',
  `metadata` json DEFAULT NULL COMMENT 'Datos adicionales (referencia externa, notas, etc)',
  `error_message` text COLLATE utf8mb4_unicode_ci COMMENT 'Si falló, guardar mensaje de error',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payment_transactions_venta_id_foreign` (`venta_id`),
  KEY `payment_transactions_empresa_id_venta_id_index` (`empresa_id`,`venta_id`),
  KEY `payment_transactions_empresa_id_status_index` (`empresa_id`,`status`),
  KEY `payment_transactions_stripe_payment_intent_id_index` (`stripe_payment_intent_id`),
  KEY `payment_transactions_created_at_index` (`created_at`)
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `payment_transactions`
--

INSERT INTO `payment_transactions` (`id`, `empresa_id`, `venta_id`, `payment_method`, `stripe_payment_intent_id`, `stripe_charge_id`, `amount_paid`, `currency`, `status`, `metadata`, `error_message`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'EFECTIVO', NULL, NULL, 221000.00, 'USD', 'SUCCESS', NULL, NULL, '2026-02-20 16:14:22', '2026-02-20 16:14:22'),
(2, 1, 2, 'EFECTIVO', NULL, NULL, 86020.00, 'USD', 'SUCCESS', NULL, NULL, '2026-02-20 17:32:32', '2026-02-20 17:32:32'),
(3, 1, 3, 'EFECTIVO', NULL, NULL, 47096.00, 'USD', 'SUCCESS', NULL, NULL, '2026-02-20 17:33:34', '2026-02-20 17:33:34'),
(4, 1, 4, 'EFECTIVO', NULL, NULL, 30000.00, 'USD', 'SUCCESS', NULL, NULL, '2026-02-20 18:11:01', '2026-02-20 18:11:01'),
(5, 1, 5, 'EFECTIVO', NULL, NULL, 3780.00, 'USD', 'SUCCESS', NULL, NULL, '2026-02-21 00:01:16', '2026-02-21 00:01:16'),
(6, 1, 6, 'EFECTIVO', NULL, NULL, 146000.00, 'USD', 'SUCCESS', NULL, NULL, '2026-02-21 01:36:38', '2026-02-21 01:36:38'),
(7, 1, 7, 'EFECTIVO', NULL, NULL, 510500.00, 'USD', 'SUCCESS', NULL, NULL, '2026-02-21 01:57:48', '2026-02-21 01:57:48'),
(8, 1, 8, 'EFECTIVO', NULL, NULL, 37000.00, 'USD', 'SUCCESS', NULL, NULL, '2026-02-21 02:00:47', '2026-02-21 02:00:47'),
(9, 1, 9, 'EFECTIVO', NULL, NULL, 510500.00, 'USD', 'SUCCESS', NULL, NULL, '2026-02-21 02:09:07', '2026-02-21 02:09:07'),
(10, 1, 10, 'EFECTIVO', NULL, NULL, 73104.00, 'USD', 'SUCCESS', NULL, NULL, '2026-02-21 16:00:36', '2026-02-21 16:00:36'),
(11, 1, 11, 'EFECTIVO', NULL, NULL, 199000.00, 'USD', 'SUCCESS', NULL, NULL, '2026-02-21 16:00:38', '2026-02-21 16:00:38'),
(12, 1, 12, 'EFECTIVO', NULL, NULL, 82864.00, 'USD', 'SUCCESS', NULL, NULL, '2026-02-21 16:00:38', '2026-02-21 16:00:38'),
(13, 1, 13, 'EFECTIVO', NULL, NULL, 165820.00, 'USD', 'SUCCESS', NULL, NULL, '2026-02-21 16:00:38', '2026-02-21 16:00:38'),
(14, 1, 14, 'EFECTIVO', NULL, NULL, 155520.00, 'USD', 'SUCCESS', NULL, NULL, '2026-02-21 16:00:39', '2026-02-21 16:00:39'),
(15, 1, 15, 'EFECTIVO', NULL, NULL, 51924.00, 'USD', 'SUCCESS', NULL, NULL, '2026-02-21 16:00:39', '2026-02-21 16:00:39'),
(16, 1, 16, 'EFECTIVO', NULL, NULL, 99020.00, 'USD', 'SUCCESS', NULL, NULL, '2026-02-21 16:00:40', '2026-02-21 16:00:40'),
(17, 1, 17, 'EFECTIVO', NULL, NULL, 106690.00, 'USD', 'SUCCESS', NULL, NULL, '2026-02-21 16:00:40', '2026-02-21 16:00:40'),
(18, 1, 18, 'EFECTIVO', NULL, NULL, 123700.00, 'USD', 'SUCCESS', NULL, NULL, '2026-02-21 16:00:40', '2026-02-21 16:00:40'),
(19, 1, 19, 'EFECTIVO', NULL, NULL, 14000.00, 'USD', 'SUCCESS', NULL, NULL, '2026-02-21 16:01:14', '2026-02-21 16:01:14'),
(20, 1, 20, 'EFECTIVO', NULL, NULL, 37000.00, 'USD', 'SUCCESS', NULL, NULL, '2026-02-21 17:37:39', '2026-02-21 17:37:39'),
(21, 1, 21, 'EFECTIVO', NULL, NULL, 98780.00, 'USD', 'SUCCESS', NULL, NULL, '2026-02-21 19:09:36', '2026-02-21 19:09:36'),
(22, 1, 22, 'EFECTIVO', NULL, NULL, 37000.00, 'USD', 'SUCCESS', NULL, NULL, '2026-02-21 19:11:14', '2026-02-21 19:11:14'),
(23, 1, 23, 'EFECTIVO', NULL, NULL, 8500.00, 'USD', 'SUCCESS', NULL, NULL, '2026-02-21 22:02:06', '2026-02-21 22:02:06');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `peliculas`
--

DROP TABLE IF EXISTS `peliculas`;
CREATE TABLE IF NOT EXISTS `peliculas` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint UNSIGNED NOT NULL,
  `titulo` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sinopsis` text COLLATE utf8mb4_unicode_ci,
  `duracion` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'En minutos o formato horas:minutos',
  `clasificacion` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `genero` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `afiche` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `trailer_url` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `distribuidor_id` bigint UNSIGNED DEFAULT NULL,
  `fecha_estreno` date DEFAULT NULL,
  `fecha_fin_exhibicion` date DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `peliculas_empresa_id_foreign` (`empresa_id`),
  KEY `peliculas_distribuidor_id_foreign` (`distribuidor_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `peliculas`
--

INSERT INTO `peliculas` (`id`, `empresa_id`, `titulo`, `sinopsis`, `duracion`, `clasificacion`, `genero`, `afiche`, `trailer_url`, `distribuidor_id`, `fecha_estreno`, `fecha_fin_exhibicion`, `activo`, `created_at`, `updated_at`) VALUES
(1, 1, 'Película de Prueba', NULL, '120', '+13', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-02-16 16:02:23', '2026-02-16 16:02:23'),
(2, 1, 'LAS CATADORAS DE HITLER', '123', '98', '+13', 'Drama', NULL, NULL, 2, '2026-02-17', '2026-02-27', 1, '2026-02-17 13:32:24', '2026-02-17 13:32:24'),
(3, 1, 'AZNAVOUR', 'WER', '112', '+18', 'Romance', 'peliculas/JdEUIMczxjGyaHzvfjT0rzyudQNBELyaMmvnxO73.jpg', NULL, 2, '2026-02-17', '2026-02-26', 1, '2026-02-17 13:34:44', '2026-02-17 13:34:44');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `periodos_operativos`
--

DROP TABLE IF EXISTS `periodos_operativos`;
CREATE TABLE IF NOT EXISTS `periodos_operativos` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint UNSIGNED NOT NULL,
  `fecha_operativa` date NOT NULL,
  `estado` enum('ABIERTO','CERRADO') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ABIERTO',
  `fecha_cierre` timestamp NULL DEFAULT NULL,
  `cerrado_por` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `periodos_operativos_empresa_id_fecha_operativa_unique` (`empresa_id`,`fecha_operativa`),
  KEY `periodos_operativos_cerrado_por_foreign` (`cerrado_por`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `periodos_operativos`
--

INSERT INTO `periodos_operativos` (`id`, `empresa_id`, `fecha_operativa`, `estado`, `fecha_cierre`, `cerrado_por`, `created_at`, `updated_at`) VALUES
(1, 1, '2026-02-20', 'ABIERTO', NULL, NULL, '2026-02-20 15:48:26', '2026-02-20 15:48:26');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permissions`
--

DROP TABLE IF EXISTS `permissions`;
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=MyISAM AUTO_INCREMENT=72 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'ver-pelicula', 'web', '2026-02-05 17:53:44', '2026-02-05 17:53:44'),
(2, 'crear-pelicula', 'web', '2026-02-05 17:53:47', '2026-02-05 17:53:47'),
(3, 'editar-pelicula', 'web', '2026-02-05 17:53:51', '2026-02-05 17:53:51'),
(4, 'eliminar-pelicula', 'web', '2026-02-05 17:53:54', '2026-02-05 17:53:54'),
(5, 'ver-registro-actividad', 'web', '2026-02-05 18:30:40', '2026-02-05 18:30:40'),
(6, 'ver-caja', 'web', '2026-02-05 18:30:40', '2026-02-05 18:30:40'),
(7, 'aperturar-caja', 'web', '2026-02-05 18:30:40', '2026-02-05 18:30:40'),
(8, 'cerrar-caja', 'web', '2026-02-05 18:30:40', '2026-02-05 18:30:40'),
(9, 'ver-kardex', 'web', '2026-02-05 18:30:40', '2026-02-05 18:30:40'),
(10, 'ver-categoria', 'web', '2026-02-05 18:30:40', '2026-02-05 18:30:40'),
(11, 'crear-categoria', 'web', '2026-02-05 18:30:40', '2026-02-05 18:30:40'),
(12, 'editar-categoria', 'web', '2026-02-05 18:30:40', '2026-02-05 18:30:40'),
(13, 'eliminar-categoria', 'web', '2026-02-05 18:30:40', '2026-02-05 18:30:40'),
(14, 'ver-cliente', 'web', '2026-02-05 18:30:40', '2026-02-05 18:30:40'),
(15, 'crear-cliente', 'web', '2026-02-05 18:30:40', '2026-02-05 18:30:40'),
(16, 'editar-cliente', 'web', '2026-02-05 18:30:40', '2026-02-05 18:30:40'),
(17, 'eliminar-cliente', 'web', '2026-02-05 18:30:40', '2026-02-05 18:30:40'),
(18, 'ver-compra', 'web', '2026-02-05 18:30:40', '2026-02-05 18:30:40'),
(19, 'crear-compra', 'web', '2026-02-05 18:30:40', '2026-02-05 18:30:40'),
(20, 'mostrar-compra', 'web', '2026-02-05 18:30:40', '2026-02-05 18:30:40'),
(21, 'ver-empleado', 'web', '2026-02-05 18:30:40', '2026-02-05 18:30:40'),
(22, 'crear-empleado', 'web', '2026-02-05 18:30:40', '2026-02-05 18:30:40'),
(23, 'editar-empleado', 'web', '2026-02-05 18:30:40', '2026-02-05 18:30:40'),
(24, 'eliminar-empleado', 'web', '2026-02-05 18:30:40', '2026-02-05 18:30:40'),
(25, 'ver-empresa', 'web', '2026-02-05 18:30:40', '2026-02-05 18:30:40'),
(26, 'update-empresa', 'web', '2026-02-05 18:30:40', '2026-02-05 18:30:40'),
(27, 'ver-inventario', 'web', '2026-02-05 18:30:40', '2026-02-05 18:30:40'),
(28, 'crear-inventario', 'web', '2026-02-05 18:30:40', '2026-02-05 18:30:40'),
(29, 'ver-marca', 'web', '2026-02-05 18:30:41', '2026-02-05 18:30:41'),
(30, 'crear-marca', 'web', '2026-02-05 18:30:41', '2026-02-05 18:30:41'),
(31, 'editar-marca', 'web', '2026-02-05 18:30:41', '2026-02-05 18:30:41'),
(32, 'eliminar-marca', 'web', '2026-02-05 18:30:41', '2026-02-05 18:30:41'),
(33, 'ver-movimiento', 'web', '2026-02-05 18:30:41', '2026-02-05 18:30:41'),
(34, 'crear-movimiento', 'web', '2026-02-05 18:30:41', '2026-02-05 18:30:41'),
(35, 'ver-presentacione', 'web', '2026-02-05 18:30:41', '2026-02-05 18:30:41'),
(36, 'crear-presentacione', 'web', '2026-02-05 18:30:41', '2026-02-05 18:30:41'),
(37, 'editar-presentacione', 'web', '2026-02-05 18:30:41', '2026-02-05 18:30:41'),
(38, 'eliminar-presentacione', 'web', '2026-02-05 18:30:41', '2026-02-05 18:30:41'),
(39, 'ver-producto', 'web', '2026-02-05 18:30:41', '2026-02-05 18:30:41'),
(40, 'crear-producto', 'web', '2026-02-05 18:30:41', '2026-02-05 18:30:41'),
(41, 'editar-producto', 'web', '2026-02-05 18:30:41', '2026-02-05 18:30:41'),
(42, 'ver-perfil', 'web', '2026-02-05 18:30:41', '2026-02-05 18:30:41'),
(43, 'editar-perfil', 'web', '2026-02-05 18:30:41', '2026-02-05 18:30:41'),
(44, 'ver-proveedore', 'web', '2026-02-05 18:30:41', '2026-02-05 18:30:41'),
(45, 'crear-proveedore', 'web', '2026-02-05 18:30:41', '2026-02-05 18:30:41'),
(46, 'editar-proveedore', 'web', '2026-02-05 18:30:41', '2026-02-05 18:30:41'),
(47, 'eliminar-proveedore', 'web', '2026-02-05 18:30:41', '2026-02-05 18:30:41'),
(48, 'ver-venta', 'web', '2026-02-05 18:30:41', '2026-02-05 18:30:41'),
(49, 'crear-venta', 'web', '2026-02-05 18:30:41', '2026-02-05 18:30:41'),
(50, 'mostrar-venta', 'web', '2026-02-05 18:30:41', '2026-02-05 18:30:41'),
(51, 'ver-role', 'web', '2026-02-05 18:30:41', '2026-02-05 18:30:41'),
(52, 'crear-role', 'web', '2026-02-05 18:30:41', '2026-02-05 18:30:41'),
(53, 'editar-role', 'web', '2026-02-05 18:30:41', '2026-02-05 18:30:41'),
(54, 'eliminar-role', 'web', '2026-02-05 18:30:41', '2026-02-05 18:30:41'),
(55, 'ver-user', 'web', '2026-02-05 18:30:41', '2026-02-05 18:30:41'),
(56, 'crear-user', 'web', '2026-02-05 18:30:41', '2026-02-05 18:30:41'),
(57, 'editar-user', 'web', '2026-02-05 18:30:41', '2026-02-05 18:30:41'),
(58, 'eliminar-user', 'web', '2026-02-05 18:30:41', '2026-02-05 18:30:41'),
(59, 'crear-empresa-saas', 'web', '2026-02-05 18:30:41', '2026-02-05 18:30:41'),
(60, 'editar-empresa-saas', 'web', '2026-02-05 18:30:41', '2026-02-05 18:30:41'),
(61, 'ver-empresa-saas', 'web', '2026-02-05 18:30:41', '2026-02-05 18:30:41'),
(62, 'suspender-empresa', 'web', '2026-02-05 18:30:41', '2026-02-05 18:30:41'),
(63, 'activar-empresa', 'web', '2026-02-05 18:30:41', '2026-02-05 18:30:41'),
(64, 'eliminar-empresa', 'web', '2026-02-05 18:30:41', '2026-02-05 18:30:41'),
(65, 'ver-suscripciones-todas', 'web', '2026-02-05 18:30:41', '2026-02-05 18:30:41'),
(66, 'ver-metricas-globales', 'web', '2026-02-05 18:30:41', '2026-02-05 18:30:41'),
(67, 'ver-reportes-globales', 'web', '2026-02-05 18:30:41', '2026-02-05 18:30:41'),
(68, 'administrar-planes-saas', 'web', '2026-02-05 18:30:41', '2026-02-05 18:30:41'),
(69, 'crear-plan-saas', 'web', '2026-02-05 18:30:41', '2026-02-05 18:30:41'),
(70, 'editar-plan-saas', 'web', '2026-02-05 18:30:41', '2026-02-05 18:30:41'),
(71, 'eliminar-plan-saas', 'web', '2026-02-05 18:30:41', '2026-02-05 18:30:41');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `personas`
--

DROP TABLE IF EXISTS `personas`;
CREATE TABLE IF NOT EXISTS `personas` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `razon_social` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `direccion` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipo` enum('NATURAL','JURIDICA') COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` tinyint NOT NULL DEFAULT '1',
  `documento_id` bigint UNSIGNED NOT NULL,
  `numero_documento` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `personas_documento_id_foreign` (`documento_id`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `personas`
--

INSERT INTO `personas` (`id`, `razon_social`, `direccion`, `telefono`, `tipo`, `email`, `estado`, `documento_id`, `numero_documento`, `created_at`, `updated_at`) VALUES
(1, 'Miss Bette Willms', NULL, NULL, 'NATURAL', NULL, 1, 1, '89912737', '2026-02-04 03:24:40', '2026-02-04 03:24:40'),
(2, 'Toni Hessel', NULL, NULL, 'NATURAL', NULL, 1, 2, '30563616', '2026-02-04 03:24:40', '2026-02-04 03:24:40'),
(3, 'Dr. Sterling Dietrich', NULL, NULL, 'NATURAL', NULL, 1, 3, '28546087', '2026-02-04 03:24:41', '2026-02-04 03:24:41'),
(4, 'Woodrow Bogisich', NULL, NULL, 'NATURAL', NULL, 1, 4, '37524076', '2026-02-04 03:24:41', '2026-02-04 03:24:41'),
(5, 'Ms. Matilde Ullrich', NULL, NULL, 'NATURAL', NULL, 1, 5, '78804863', '2026-02-04 03:24:41', '2026-02-04 03:24:41'),
(6, 'Kaleb Tillman', NULL, NULL, 'NATURAL', NULL, 1, 6, '12759462', '2026-02-04 03:24:41', '2026-02-04 03:24:41'),
(7, 'Tamara Casper', NULL, NULL, 'NATURAL', NULL, 1, 7, '07437768', '2026-02-04 03:24:41', '2026-02-04 03:24:41'),
(8, 'Tyrell Gleichner', NULL, NULL, 'NATURAL', NULL, 1, 8, '75368709', '2026-02-04 03:24:41', '2026-02-04 03:24:41'),
(9, 'Humberto Legros PhD', NULL, NULL, 'NATURAL', NULL, 1, 9, '07502276', '2026-02-04 03:24:41', '2026-02-04 03:24:41'),
(10, 'Norval Gibson', NULL, NULL, 'NATURAL', NULL, 1, 10, '18665515', '2026-02-04 03:24:41', '2026-02-04 03:24:41'),
(11, 'PUBLICO GENERAL', NULL, NULL, 'NATURAL', NULL, 1, 1, '0', '2026-02-10 02:48:00', '2026-02-10 02:48:00'),
(12, 'babilla cien', 'calle 696-20', '1111', 'JURIDICA', '1@1', 1, 6, '123', '2026-02-18 16:17:06', '2026-02-18 16:17:06'),
(13, 'cinema', 'Chapinero', '31062222222', 'JURIDICA', 'publi.jvc.marketing@gmail.com', 1, 1, '102123566', '2026-02-18 16:17:56', '2026-02-18 16:17:56');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `planes`
--

DROP TABLE IF EXISTS `planes`;
CREATE TABLE IF NOT EXISTS `planes` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `precios_entradas`
--

DROP TABLE IF EXISTS `precios_entradas`;
CREATE TABLE IF NOT EXISTS `precios_entradas` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint UNSIGNED NOT NULL,
  `nombre` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `precio` decimal(15,2) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `precios_entradas_empresa_id_foreign` (`empresa_id`)
) ENGINE=MyISAM AUTO_INCREMENT=86 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `precios_entradas`
--

INSERT INTO `precios_entradas` (`id`, `empresa_id`, `nombre`, `precio`, `activo`, `created_at`, `updated_at`) VALUES
(1, 1, 'Entrada General', 30000.00, 1, '2026-02-04 23:12:05', '2026-02-04 23:12:05'),
(2, 1, 'General', 30000.00, 1, '2026-02-09 17:18:10', '2026-02-16 16:02:03'),
(3, 1, 'Niños/Adulto Mayor', 22000.00, 1, '2026-02-09 17:18:10', '2026-02-16 16:02:03'),
(4, 1, 'Promoción Miércoles', 15000.00, 1, '2026-02-09 17:18:10', '2026-02-16 16:02:03'),
(5, 2, 'General', 30000.00, 1, '2026-02-09 17:18:12', '2026-02-16 16:02:03'),
(6, 2, 'Niños/Adulto Mayor', 22000.00, 1, '2026-02-09 17:18:12', '2026-02-16 16:02:03'),
(7, 2, 'Promoción Miércoles', 15000.00, 1, '2026-02-09 17:18:12', '2026-02-16 16:02:03'),
(8, 3, 'General', 30000.00, 1, '2026-02-09 17:18:12', '2026-02-16 16:02:03'),
(9, 3, 'Niños/Adulto Mayor', 22000.00, 1, '2026-02-09 17:18:12', '2026-02-16 16:02:03'),
(10, 3, 'Promoción Miércoles', 15000.00, 1, '2026-02-09 17:18:12', '2026-02-16 16:02:03'),
(11, 4, 'General', 30000.00, 1, '2026-02-09 17:18:12', '2026-02-16 16:02:03'),
(12, 4, 'Niños/Adulto Mayor', 22000.00, 1, '2026-02-09 17:18:13', '2026-02-16 16:02:03'),
(13, 4, 'Promoción Miércoles', 15000.00, 1, '2026-02-09 17:18:13', '2026-02-16 16:02:03'),
(14, 5, 'General', 30000.00, 1, '2026-02-09 17:18:13', '2026-02-16 16:02:03'),
(15, 5, 'Niños/Adulto Mayor', 22000.00, 1, '2026-02-09 17:18:13', '2026-02-16 16:02:03'),
(16, 5, 'Promoción Miércoles', 15000.00, 1, '2026-02-09 17:18:13', '2026-02-16 16:02:03'),
(17, 6, 'General', 30000.00, 1, '2026-02-09 17:18:13', '2026-02-16 16:02:04'),
(18, 6, 'Niños/Adulto Mayor', 22000.00, 1, '2026-02-09 17:18:13', '2026-02-16 16:02:04'),
(19, 6, 'Promoción Miércoles', 15000.00, 1, '2026-02-09 17:18:13', '2026-02-16 16:02:04'),
(20, 7, 'General', 30000.00, 1, '2026-02-09 17:18:13', '2026-02-16 16:02:04'),
(21, 7, 'Niños/Adulto Mayor', 22000.00, 1, '2026-02-09 17:18:13', '2026-02-16 16:02:04'),
(22, 7, 'Promoción Miércoles', 15000.00, 1, '2026-02-09 17:18:13', '2026-02-16 16:02:04'),
(23, 8, 'General', 30000.00, 1, '2026-02-09 17:18:14', '2026-02-16 16:02:04'),
(24, 8, 'Niños/Adulto Mayor', 22000.00, 1, '2026-02-09 17:18:14', '2026-02-16 16:02:04'),
(25, 8, 'Promoción Miércoles', 15000.00, 1, '2026-02-09 17:18:14', '2026-02-16 16:02:04'),
(26, 9, 'General', 30000.00, 1, '2026-02-09 17:18:14', '2026-02-16 16:02:04'),
(27, 9, 'Niños/Adulto Mayor', 22000.00, 1, '2026-02-09 17:18:14', '2026-02-16 16:02:04'),
(28, 9, 'Promoción Miércoles', 15000.00, 1, '2026-02-09 17:18:14', '2026-02-16 16:02:04'),
(29, 10, 'General', 30000.00, 1, '2026-02-09 17:18:14', '2026-02-16 16:02:04'),
(30, 10, 'Niños/Adulto Mayor', 22000.00, 1, '2026-02-09 17:18:14', '2026-02-16 16:02:04'),
(31, 10, 'Promoción Miércoles', 15000.00, 1, '2026-02-09 17:18:14', '2026-02-16 16:02:04'),
(32, 11, 'General', 30000.00, 1, '2026-02-09 17:18:14', '2026-02-16 16:02:04'),
(33, 11, 'Niños/Adulto Mayor', 22000.00, 1, '2026-02-09 17:18:14', '2026-02-16 16:02:04'),
(34, 11, 'Promoción Miércoles', 15000.00, 1, '2026-02-09 17:18:14', '2026-02-16 16:02:04'),
(35, 12, 'General', 30000.00, 1, '2026-02-09 17:18:15', '2026-02-16 16:02:04'),
(36, 12, 'Niños/Adulto Mayor', 22000.00, 1, '2026-02-09 17:18:15', '2026-02-16 16:02:04'),
(37, 12, 'Promoción Miércoles', 15000.00, 1, '2026-02-09 17:18:15', '2026-02-16 16:02:04'),
(38, 13, 'General', 30000.00, 1, '2026-02-09 17:18:15', '2026-02-16 16:02:04'),
(39, 13, 'Niños/Adulto Mayor', 22000.00, 1, '2026-02-09 17:18:15', '2026-02-16 16:02:04'),
(40, 13, 'Promoción Miércoles', 15000.00, 1, '2026-02-09 17:18:15', '2026-02-16 16:02:04'),
(41, 14, 'General', 30000.00, 1, '2026-02-09 17:18:15', '2026-02-16 16:02:04'),
(42, 14, 'Niños/Adulto Mayor', 22000.00, 1, '2026-02-09 17:18:16', '2026-02-16 16:02:04'),
(43, 14, 'Promoción Miércoles', 15000.00, 1, '2026-02-09 17:18:16', '2026-02-16 16:02:04'),
(44, 15, 'General', 30000.00, 1, '2026-02-09 17:18:16', '2026-02-16 16:02:04'),
(45, 15, 'Niños/Adulto Mayor', 22000.00, 1, '2026-02-09 17:18:16', '2026-02-16 16:02:04'),
(46, 15, 'Promoción Miércoles', 15000.00, 1, '2026-02-09 17:18:16', '2026-02-16 16:02:04'),
(47, 16, 'General', 30000.00, 1, '2026-02-09 17:18:16', '2026-02-16 16:02:04'),
(48, 16, 'Niños/Adulto Mayor', 22000.00, 1, '2026-02-09 17:18:16', '2026-02-16 16:02:04'),
(49, 16, 'Promoción Miércoles', 15000.00, 1, '2026-02-09 17:18:16', '2026-02-16 16:02:04'),
(50, 17, 'General', 30000.00, 1, '2026-02-09 17:18:16', '2026-02-16 16:02:04'),
(51, 17, 'Niños/Adulto Mayor', 22000.00, 1, '2026-02-09 17:18:16', '2026-02-16 16:02:04'),
(52, 17, 'Promoción Miércoles', 15000.00, 1, '2026-02-09 17:18:16', '2026-02-16 16:02:04'),
(53, 18, 'General', 30000.00, 1, '2026-02-09 17:18:16', '2026-02-16 16:02:04'),
(54, 18, 'Niños/Adulto Mayor', 22000.00, 1, '2026-02-09 17:18:16', '2026-02-16 16:02:04'),
(55, 18, 'Promoción Miércoles', 15000.00, 1, '2026-02-09 17:18:16', '2026-02-16 16:02:04'),
(56, 19, 'General', 30000.00, 1, '2026-02-09 17:18:16', '2026-02-16 16:02:04'),
(57, 19, 'Niños/Adulto Mayor', 22000.00, 1, '2026-02-09 17:18:16', '2026-02-16 16:02:04'),
(58, 19, 'Promoción Miércoles', 15000.00, 1, '2026-02-09 17:18:16', '2026-02-16 16:02:04'),
(59, 20, 'General', 30000.00, 1, '2026-02-09 17:18:16', '2026-02-16 16:02:04'),
(60, 20, 'Niños/Adulto Mayor', 22000.00, 1, '2026-02-09 17:18:16', '2026-02-16 16:02:04'),
(61, 20, 'Promoción Miércoles', 15000.00, 1, '2026-02-09 17:18:16', '2026-02-16 16:02:04'),
(62, 21, 'General', 30000.00, 1, '2026-02-09 17:18:17', '2026-02-16 16:02:04'),
(63, 21, 'Niños/Adulto Mayor', 22000.00, 1, '2026-02-09 17:18:17', '2026-02-16 16:02:04'),
(64, 21, 'Promoción Miércoles', 15000.00, 1, '2026-02-09 17:18:17', '2026-02-16 16:02:04'),
(65, 22, 'General', 30000.00, 1, '2026-02-09 17:18:17', '2026-02-16 16:02:04'),
(66, 22, 'Niños/Adulto Mayor', 22000.00, 1, '2026-02-09 17:18:17', '2026-02-16 16:02:04'),
(67, 22, 'Promoción Miércoles', 15000.00, 1, '2026-02-09 17:18:17', '2026-02-16 16:02:04'),
(68, 23, 'General', 30000.00, 1, '2026-02-09 17:18:17', '2026-02-16 16:02:04'),
(69, 23, 'Niños/Adulto Mayor', 22000.00, 1, '2026-02-09 17:18:17', '2026-02-16 16:02:04'),
(70, 23, 'Promoción Miércoles', 15000.00, 1, '2026-02-09 17:18:17', '2026-02-16 16:02:04'),
(71, 24, 'General', 30000.00, 1, '2026-02-09 17:18:17', '2026-02-16 16:02:04'),
(72, 24, 'Niños/Adulto Mayor', 22000.00, 1, '2026-02-09 17:18:17', '2026-02-16 16:02:04'),
(73, 24, 'Promoción Miércoles', 15000.00, 1, '2026-02-09 17:18:17', '2026-02-16 16:02:04'),
(74, 25, 'General', 30000.00, 1, '2026-02-09 17:18:17', '2026-02-16 16:02:04'),
(75, 25, 'Niños/Adulto Mayor', 22000.00, 1, '2026-02-09 17:18:17', '2026-02-16 16:02:04'),
(76, 25, 'Promoción Miércoles', 15000.00, 1, '2026-02-09 17:18:17', '2026-02-16 16:02:04'),
(77, 26, 'General', 30000.00, 1, '2026-02-09 17:18:17', '2026-02-16 16:02:04'),
(78, 26, 'Niños/Adulto Mayor', 22000.00, 1, '2026-02-09 17:18:17', '2026-02-16 16:02:04'),
(79, 26, 'Promoción Miércoles', 15000.00, 1, '2026-02-09 17:18:17', '2026-02-16 16:02:04'),
(80, 27, 'General', 30000.00, 1, '2026-02-09 17:18:17', '2026-02-16 16:02:04'),
(81, 27, 'Niños/Adulto Mayor', 22000.00, 1, '2026-02-09 17:18:17', '2026-02-16 16:02:04'),
(82, 27, 'Promoción Miércoles', 15000.00, 1, '2026-02-09 17:18:18', '2026-02-16 16:02:04'),
(83, 28, 'General', 30000.00, 1, '2026-02-09 17:18:18', '2026-02-16 16:02:04'),
(84, 28, 'Niños/Adulto Mayor', 22000.00, 1, '2026-02-09 17:18:18', '2026-02-16 16:02:04'),
(85, 28, 'Promoción Miércoles', 15000.00, 1, '2026-02-09 17:18:18', '2026-02-16 16:02:04');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `presentaciones`
--

DROP TABLE IF EXISTS `presentaciones`;
CREATE TABLE IF NOT EXISTS `presentaciones` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint UNSIGNED DEFAULT NULL,
  `caracteristica_id` bigint UNSIGNED NOT NULL,
  `sigla` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `presentaciones_caracteristica_id_unique` (`caracteristica_id`),
  KEY `presentaciones_empresa_id_foreign` (`empresa_id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `presentaciones`
--

INSERT INTO `presentaciones` (`id`, `empresa_id`, `caracteristica_id`, `sigla`, `created_at`, `updated_at`) VALUES
(1, 1, 3, 'UND', '2026-02-06 03:05:33', '2026-02-06 03:05:33'),
(2, 1, 15, 'UND', '2026-02-10 18:54:05', '2026-02-10 18:54:05'),
(3, 1, 20, 'UND', '2026-02-10 18:55:08', '2026-02-10 18:55:08'),
(4, 1, 25, 'UND', '2026-02-10 18:58:58', '2026-02-10 18:58:58'),
(5, 1, 30, 'UND', '2026-02-10 19:16:30', '2026-02-10 19:16:30'),
(6, 1, 35, 'UND', '2026-02-10 20:40:21', '2026-02-10 20:40:21'),
(7, 1, 41, 'UND', '2026-02-16 16:01:31', '2026-02-16 16:01:31');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

DROP TABLE IF EXISTS `productos`;
CREATE TABLE IF NOT EXISTS `productos` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint UNSIGNED NOT NULL,
  `codigo` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `img_path` varchar(2048) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` tinyint NOT NULL DEFAULT '0',
  `disponible_venta` tinyint(1) NOT NULL DEFAULT '1',
  `es_venta_retail` tinyint(1) NOT NULL DEFAULT '1',
  `stock_actual` decimal(15,3) DEFAULT NULL,
  `precio` decimal(8,2) DEFAULT NULL,
  `margen_ganancia_absoluta` decimal(10,2) NOT NULL DEFAULT '0.00',
  `margen_ganancia_porcentual` decimal(8,2) NOT NULL DEFAULT '0.00',
  `roi` decimal(8,2) NOT NULL DEFAULT '0.00',
  `costos_calculados_at` timestamp NULL DEFAULT NULL,
  `gasto_operativo_fijo` decimal(12,2) NOT NULL DEFAULT '0.00',
  `costo_insumos_total` decimal(10,2) NOT NULL DEFAULT '0.00',
  `costo_merma` decimal(10,2) NOT NULL DEFAULT '0.00',
  `costo_indirectos` decimal(10,2) NOT NULL DEFAULT '0.00',
  `costo_total_unitario` decimal(10,2) NOT NULL DEFAULT '0.00',
  `precio_sugerido` decimal(10,2) DEFAULT NULL,
  `margen_objetivo` decimal(5,2) NOT NULL DEFAULT '40.00',
  `tipo_impuesto` enum('IVA','IMPOCONSUMO','EXENTO') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'EXENTO',
  `porcentaje_impuesto` decimal(5,2) NOT NULL DEFAULT '0.00',
  `marca_id` bigint UNSIGNED DEFAULT NULL,
  `presentacione_id` bigint UNSIGNED NOT NULL,
  `categoria_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `estado_pelicula` enum('cartelera','proximamente','archivada') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `es_preventa` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `productos_codigo_unique` (`codigo`),
  KEY `productos_marca_id_foreign` (`marca_id`),
  KEY `productos_presentacione_id_foreign` (`presentacione_id`),
  KEY `productos_categoria_id_foreign` (`categoria_id`),
  KEY `productos_empresa_id_estado_index` (`empresa_id`,`estado`),
  KEY `idx_productos_perf_retail_disp` (`empresa_id`,`es_venta_retail`,`disponible_venta`)
) ENGINE=MyISAM AUTO_INCREMENT=87 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `empresa_id`, `codigo`, `nombre`, `slug`, `descripcion`, `img_path`, `estado`, `disponible_venta`, `es_venta_retail`, `stock_actual`, `precio`, `margen_ganancia_absoluta`, `margen_ganancia_porcentual`, `roi`, `costos_calculados_at`, `gasto_operativo_fijo`, `costo_insumos_total`, `costo_merma`, `costo_indirectos`, `costo_total_unitario`, `precio_sugerido`, `margen_objetivo`, `tipo_impuesto`, `porcentaje_impuesto`, `marca_id`, `presentacione_id`, `categoria_id`, `created_at`, `updated_at`, `estado_pelicula`, `es_preventa`) VALUES
(83, 1, '009649669779', 'Palomitas Grandes', NULL, NULL, NULL, 0, 1, 1, 100.000, 15000.00, 13600.00, 90.67, 971.43, '2026-02-21 23:03:44', 0.00, 1400.00, 0.00, 0.00, 1400.00, 2333.33, 40.00, 'EXENTO', 0.00, 7, 1, 2, '2026-02-16 16:02:23', '2026-02-21 23:03:44', NULL, 0),
(81, 1, '006767020630', 'Copa de vino blanco', NULL, 'Producto de simulación POS', NULL, 1, 1, 1, 49.000, 35000.00, 25550.00, 73.00, 270.37, '2026-02-16 16:01:34', 0.00, 9450.00, 0.00, 0.00, 9450.00, 15750.00, 40.00, 'IMPOCONSUMO', 8.00, 8, 7, 11, '2026-02-16 16:01:32', '2026-02-21 00:39:48', NULL, 0),
(82, 1, '006387467334', 'Coctel', NULL, 'Producto de simulación POS', NULL, 1, 1, 1, 48.000, 35000.00, 24960.00, 71.31, 248.61, '2026-02-17 00:17:48', 5000.00, 5040.00, 0.00, 5000.00, 10040.00, 16733.33, 40.00, 'EXENTO', 0.00, 8, 7, 11, '2026-02-16 16:01:32', '2026-02-21 22:05:32', NULL, 0),
(80, 1, '009431297386', 'Copa de vino tinto', NULL, 'Producto de simulación POS', NULL, 1, 1, 1, 45.000, 35000.00, 25550.00, 73.00, 270.37, '2026-02-16 16:01:34', 3000.00, 9450.00, 0.00, 0.00, 9450.00, 15750.00, 40.00, 'IMPOCONSUMO', 8.00, 8, 7, 11, '2026-02-16 16:01:32', '2026-02-21 22:06:05', NULL, 0),
(78, 1, '005356019173', 'Gaseosa 350ml', NULL, 'Producto de simulación POS', NULL, 1, 1, 1, 38.000, 8500.00, 6400.00, 75.29, 304.76, '2026-02-19 20:05:21', 3000.00, 2100.00, 0.00, 0.00, 2100.00, 3500.00, 40.00, 'IMPOCONSUMO', 8.00, 8, 7, 1, '2026-02-16 16:01:32', '2026-02-21 22:42:06', NULL, 0),
(79, 1, '005876937630', 'Cerveza', NULL, 'Producto de simulación POS', NULL, 1, 1, 1, 45.000, 14000.00, 10325.00, 73.75, 280.95, '2026-02-16 16:01:34', 3200.00, 3675.00, 0.00, 0.00, 3675.00, 6125.00, 40.00, 'IMPOCONSUMO', 8.00, 8, 7, 11, '2026-02-16 16:01:32', '2026-02-21 23:37:25', NULL, 0),
(77, 1, '003627189109', 'Crispetas', NULL, 'Producto de simulación POS', NULL, 1, 1, 1, 44.000, 14000.00, 12782.00, 91.30, 1049.43, '2026-02-16 16:01:34', 3000.00, 1218.00, 0.00, 0.00, 1218.00, 2030.00, 40.00, 'IMPOCONSUMO', 8.00, 8, 7, 4, '2026-02-16 16:01:32', '2026-02-21 22:54:48', NULL, 0),
(75, 1, '009176398157', 'Pizza de jamón', NULL, 'Producto de simulación POS', NULL, 1, 1, 1, 31.000, 36000.00, 24555.00, 68.21, 214.55, '2026-02-16 16:01:33', 6000.00, 11445.00, 0.00, 0.00, 11445.00, 19075.00, 40.00, 'IMPOCONSUMO', 8.00, 8, 7, 4, '2026-02-16 16:01:32', '2026-02-21 22:56:04', NULL, 0),
(76, 1, '004748342152', 'Brownie', NULL, 'Producto de simulación POS', NULL, 1, 1, 1, 44.000, 16000.00, 12850.00, 80.31, 407.94, '2026-02-16 16:01:34', 8000.00, 3150.00, 0.00, 0.00, 3150.00, 5250.00, 40.00, 'IMPOCONSUMO', 8.00, 8, 7, 4, '2026-02-16 16:01:32', '2026-02-21 22:55:36', NULL, 0),
(73, 1, '009716085432', 'Perro caliente', NULL, 'Producto de simulación POS', NULL, 1, 1, 1, 50.000, 35000.00, 34212.50, 97.75, 4344.44, '2026-02-21 23:03:44', 0.00, 787.50, 0.00, 0.00, 787.50, 1312.50, 40.00, 'EXENTO', 0.00, 8, 7, 4, '2026-02-16 16:01:32', '2026-02-21 23:03:44', NULL, 0),
(74, 1, '007404001055', 'Pizza margarita', NULL, 'Producto de simulación POS', NULL, 1, 1, 1, 40.000, 34000.00, 25180.00, 74.06, 285.49, '2026-02-16 16:01:33', 6000.00, 8820.00, 0.00, 0.00, 8820.00, 14700.00, 40.00, 'IMPOCONSUMO', 8.00, 8, 7, 4, '2026-02-16 16:01:32', '2026-02-21 22:57:16', NULL, 0),
(39, 1, 'TICKET_CINEMA_1', 'Entrada de Cine General', NULL, NULL, NULL, 1, 0, 0, 10000.000, 0.00, 0.00, 0.00, 0.00, NULL, 0.00, 0.00, 0.00, 0.00, 0.00, NULL, 40.00, 'EXENTO', 0.00, 1, 1, 9, '2026-02-09 17:36:06', '2026-02-21 19:05:33', NULL, 0),
(84, 1, '005356019174', 'Agua Mineral 500ml', NULL, 'Producto de simulación POS', NULL, 1, 1, 1, 50.000, 8500.00, 6400.00, 75.29, 304.76, '2026-02-19 20:05:21', 0.00, 2100.00, 0.00, 0.00, 2100.00, 3500.00, 40.00, 'EXENTO', 0.00, 8, 7, 1, '2026-02-19 20:05:21', '2026-02-19 20:05:21', NULL, 0),
(85, 1, 'PRO-0001', 'Pizza hawaiana', 'pizza-hawaiana', 'pizza', NULL, 1, 1, 1, 50.000, 37000.00, 34500.00, 93.24, 1380.00, '2026-02-21 23:03:44', 0.00, 2500.00, 0.00, 0.00, 2500.00, 4166.67, 40.00, 'EXENTO', 0.00, NULL, 1, 2, '2026-02-20 23:33:00', '2026-02-21 23:03:44', NULL, 0),
(86, 1, 'PRO-0002', 'Zumo de Limón', NULL, NULL, NULL, 1, 1, 1, 100.000, 3000.00, 2400.00, 80.00, 400.00, '2026-02-21 23:03:44', 0.00, 600.00, 0.00, 0.00, 600.00, 1000.00, 40.00, 'EXENTO', 0.00, NULL, 1, 1, '2026-02-20 23:38:42', '2026-02-21 23:03:44', NULL, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto_venta`
--

DROP TABLE IF EXISTS `producto_venta`;
CREATE TABLE IF NOT EXISTS `producto_venta` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `venta_id` bigint UNSIGNED NOT NULL,
  `producto_id` bigint UNSIGNED NOT NULL,
  `cantidad` int UNSIGNED NOT NULL,
  `precio_venta` decimal(10,2) NOT NULL,
  `tarifa_unitaria` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Tarifa aplicada a este producto en esta venta',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `producto_venta_venta_id_foreign` (`venta_id`),
  KEY `producto_venta_producto_id_foreign` (`producto_id`)
) ENGINE=MyISAM AUTO_INCREMENT=58 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `producto_venta`
--

INSERT INTO `producto_venta` (`id`, `venta_id`, `producto_id`, `cantidad`, `precio_venta`, `tarifa_unitaria`, `created_at`, `updated_at`) VALUES
(1, 1, 83, 1, 15000.00, 0.00, '2026-02-20 16:14:22', '2026-02-20 16:14:22'),
(2, 1, 84, 2, 8500.00, 0.00, '2026-02-20 16:14:22', '2026-02-20 16:14:22'),
(3, 1, 73, 1, 35000.00, 0.00, '2026-02-20 16:14:22', '2026-02-20 16:14:22'),
(4, 1, 74, 1, 34000.00, 0.00, '2026-02-20 16:14:22', '2026-02-20 16:14:22'),
(5, 2, 83, 1, 15000.00, 0.00, '2026-02-20 17:32:32', '2026-02-20 17:32:32'),
(6, 2, 84, 1, 8500.00, 0.00, '2026-02-20 17:32:32', '2026-02-20 17:32:32'),
(7, 2, 78, 1, 2520.00, 0.00, '2026-02-20 17:32:32', '2026-02-20 17:32:32'),
(8, 3, 82, 2, 6048.00, 0.00, '2026-02-20 17:33:33', '2026-02-20 17:33:33'),
(9, 3, 81, 1, 35000.00, 0.00, '2026-02-20 17:33:33', '2026-02-20 17:33:33'),
(10, 5, 76, 1, 3780.00, 0.00, '2026-02-21 00:01:15', '2026-02-21 00:01:15'),
(11, 6, 85, 1, 37000.00, 0.00, '2026-02-21 01:36:38', '2026-02-21 01:36:38'),
(12, 6, 79, 1, 14000.00, 0.00, '2026-02-21 01:36:38', '2026-02-21 01:36:38'),
(13, 6, 80, 1, 35000.00, 0.00, '2026-02-21 01:36:38', '2026-02-21 01:36:38'),
(14, 7, 85, 1, 37000.00, 0.00, '2026-02-21 01:57:48', '2026-02-21 01:57:48'),
(15, 7, 84, 6, 8500.00, 0.00, '2026-02-21 01:57:48', '2026-02-21 01:57:48'),
(16, 7, 78, 3, 8500.00, 0.00, '2026-02-21 01:57:48', '2026-02-21 01:57:48'),
(17, 7, 75, 6, 36000.00, 0.00, '2026-02-21 01:57:48', '2026-02-21 01:57:48'),
(18, 7, 86, 3, 3000.00, 0.00, '2026-02-21 01:57:48', '2026-02-21 01:57:48'),
(19, 7, 74, 3, 34000.00, 0.00, '2026-02-21 01:57:48', '2026-02-21 01:57:48'),
(20, 7, 73, 2, 35000.00, 0.00, '2026-02-21 01:57:48', '2026-02-21 01:57:48'),
(21, 8, 85, 1, 37000.00, 0.00, '2026-02-21 02:00:47', '2026-02-21 02:00:47'),
(22, 9, 84, 6, 8500.00, 0.00, '2026-02-21 02:09:07', '2026-02-21 02:09:07'),
(23, 9, 78, 3, 8500.00, 0.00, '2026-02-21 02:09:07', '2026-02-21 02:09:07'),
(24, 9, 86, 3, 3000.00, 0.00, '2026-02-21 02:09:07', '2026-02-21 02:09:07'),
(25, 9, 75, 6, 36000.00, 0.00, '2026-02-21 02:09:07', '2026-02-21 02:09:07'),
(26, 9, 74, 3, 34000.00, 0.00, '2026-02-21 02:09:07', '2026-02-21 02:09:07'),
(27, 9, 73, 2, 35000.00, 0.00, '2026-02-21 02:09:07', '2026-02-21 02:09:07'),
(28, 9, 85, 1, 37000.00, 0.00, '2026-02-21 02:09:07', '2026-02-21 02:09:07'),
(29, 10, 74, 1, 10584.00, 0.00, '2026-02-21 16:00:36', '2026-02-21 16:00:36'),
(30, 10, 78, 1, 2520.00, 0.00, '2026-02-21 16:00:36', '2026-02-21 16:00:36'),
(31, 11, 73, 1, 35000.00, 0.00, '2026-02-21 16:00:38', '2026-02-21 16:00:38'),
(32, 11, 77, 1, 14000.00, 0.00, '2026-02-21 16:00:38', '2026-02-21 16:00:38'),
(33, 12, 74, 1, 10584.00, 0.00, '2026-02-21 16:00:38', '2026-02-21 16:00:38'),
(34, 12, 76, 1, 3780.00, 0.00, '2026-02-21 16:00:38', '2026-02-21 16:00:38'),
(35, 12, 84, 1, 8500.00, 0.00, '2026-02-21 16:00:38', '2026-02-21 16:00:38'),
(36, 13, 85, 1, 37000.00, 0.00, '2026-02-21 16:00:38', '2026-02-21 16:00:38'),
(37, 13, 79, 2, 4410.00, 0.00, '2026-02-21 16:00:38', '2026-02-21 16:00:38'),
(38, 14, 73, 1, 35000.00, 0.00, '2026-02-21 16:00:39', '2026-02-21 16:00:39'),
(39, 14, 77, 2, 14000.00, 0.00, '2026-02-21 16:00:39', '2026-02-21 16:00:39'),
(40, 14, 78, 1, 2520.00, 0.00, '2026-02-21 16:00:39', '2026-02-21 16:00:39'),
(41, 15, 74, 1, 10584.00, 0.00, '2026-02-21 16:00:39', '2026-02-21 16:00:39'),
(42, 15, 80, 1, 11340.00, 0.00, '2026-02-21 16:00:39', '2026-02-21 16:00:39'),
(43, 16, 77, 2, 14000.00, 0.00, '2026-02-21 16:00:40', '2026-02-21 16:00:40'),
(44, 16, 84, 1, 8500.00, 0.00, '2026-02-21 16:00:40', '2026-02-21 16:00:40'),
(45, 16, 78, 1, 2520.00, 0.00, '2026-02-21 16:00:40', '2026-02-21 16:00:40'),
(46, 17, 76, 1, 3780.00, 0.00, '2026-02-21 16:00:40', '2026-02-21 16:00:40'),
(47, 17, 84, 1, 8500.00, 0.00, '2026-02-21 16:00:40', '2026-02-21 16:00:40'),
(48, 17, 79, 1, 4410.00, 0.00, '2026-02-21 16:00:40', '2026-02-21 16:00:40'),
(49, 18, 80, 2, 11340.00, 0.00, '2026-02-21 16:00:40', '2026-02-21 16:00:40'),
(50, 18, 78, 1, 2520.00, 0.00, '2026-02-21 16:00:40', '2026-02-21 16:00:40'),
(51, 18, 84, 1, 8500.00, 0.00, '2026-02-21 16:00:40', '2026-02-21 16:00:40'),
(52, 19, 77, 1, 14000.00, 0.00, '2026-02-21 16:01:14', '2026-02-21 16:01:14'),
(53, 20, 85, 1, 37000.00, 0.00, '2026-02-21 17:37:38', '2026-02-21 17:37:38'),
(54, 21, 73, 1, 35000.00, 0.00, '2026-02-21 19:09:36', '2026-02-21 19:09:36'),
(55, 21, 76, 1, 3780.00, 0.00, '2026-02-21 19:09:36', '2026-02-21 19:09:36'),
(56, 22, 85, 1, 37000.00, 0.00, '2026-02-21 19:11:14', '2026-02-21 19:11:14'),
(57, 23, 84, 1, 8500.00, 0.00, '2026-02-21 22:02:06', '2026-02-21 22:02:06');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedores`
--

DROP TABLE IF EXISTS `proveedores`;
CREATE TABLE IF NOT EXISTS `proveedores` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint UNSIGNED NOT NULL,
  `persona_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `proveedores_persona_id_unique` (`persona_id`),
  KEY `proveedores_empresa_id_index` (`empresa_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recetas`
--

DROP TABLE IF EXISTS `recetas`;
CREATE TABLE IF NOT EXISTS `recetas` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `producto_id` bigint UNSIGNED NOT NULL,
  `insumo_id` bigint UNSIGNED NOT NULL,
  `cantidad` decimal(12,3) NOT NULL,
  `merma_esperada` decimal(5,2) NOT NULL DEFAULT '0.00',
  `unidad_medida` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `recetas_producto_id_insumo_id_unique` (`producto_id`,`insumo_id`),
  KEY `recetas_insumo_id_foreign` (`insumo_id`)
) ENGINE=MyISAM AUTO_INCREMENT=95 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `recetas`
--

INSERT INTO `recetas` (`id`, `producto_id`, `insumo_id`, `cantidad`, `merma_esperada`, `unidad_medida`, `created_at`, `updated_at`) VALUES
(91, 83, 18, 0.050, 0.00, 'KG', '2026-02-16 16:02:23', '2026-02-16 16:02:23'),
(89, 82, 13, 60.000, 5.00, 'ml', '2026-02-16 16:01:33', '2026-02-16 16:01:33'),
(90, 83, 17, 0.200, 0.00, 'KG', '2026-02-16 16:02:23', '2026-02-16 16:02:23'),
(88, 81, 12, 150.000, 5.00, 'ml', '2026-02-16 16:01:33', '2026-02-16 16:01:33'),
(87, 80, 11, 150.000, 5.00, 'ml', '2026-02-16 16:01:33', '2026-02-16 16:01:33'),
(86, 79, 10, 1.000, 5.00, 'und', '2026-02-16 16:01:33', '2026-02-16 16:01:33'),
(85, 78, 8, 1.000, 5.00, 'und', '2026-02-16 16:01:33', '2026-02-16 16:01:33'),
(84, 77, 15, 20.000, 5.00, 'g', '2026-02-16 16:01:33', '2026-02-16 16:01:33'),
(83, 77, 14, 100.000, 5.00, 'g', '2026-02-16 16:01:33', '2026-02-16 16:01:33'),
(82, 76, 16, 150.000, 5.00, 'g', '2026-02-16 16:01:33', '2026-02-16 16:01:33'),
(81, 75, 7, 100.000, 5.00, 'g', '2026-02-16 16:01:33', '2026-02-16 16:01:33'),
(80, 75, 6, 100.000, 5.00, 'g', '2026-02-16 16:01:33', '2026-02-16 16:01:33'),
(79, 75, 5, 150.000, 5.00, 'g', '2026-02-16 16:01:33', '2026-02-16 16:01:33'),
(78, 75, 4, 1.000, 5.00, 'und', '2026-02-16 16:01:33', '2026-02-16 16:01:33'),
(77, 74, 7, 100.000, 5.00, 'g', '2026-02-16 16:01:33', '2026-02-16 16:01:33'),
(76, 74, 5, 150.000, 5.00, 'g', '2026-02-16 16:01:32', '2026-02-16 16:01:32'),
(75, 74, 4, 1.000, 5.00, 'und', '2026-02-16 16:01:32', '2026-02-16 16:01:32'),
(74, 73, 3, 50.000, 5.00, 'g', '2026-02-16 16:01:32', '2026-02-16 16:01:32'),
(73, 73, 2, 1.000, 5.00, 'und', '2026-02-16 16:01:32', '2026-02-16 16:01:32'),
(72, 73, 1, 1.000, 5.00, 'und', '2026-02-16 16:01:32', '2026-02-16 16:01:32'),
(92, 85, 19, 1.000, 0.00, 'und', '2026-02-20 23:38:42', '2026-02-20 23:54:06'),
(93, 85, 20, 100.000, 0.00, 'g', '2026-02-20 23:38:42', '2026-02-20 23:54:06'),
(94, 86, 21, 3.000, 0.00, 'und', '2026-02-20 23:38:42', '2026-02-20 23:54:06');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'administrador', 'web', '2026-02-05 17:54:22', '2026-02-05 17:54:22'),
(2, 'cajero', 'web', '2026-02-05 17:54:25', '2026-02-05 17:54:25'),
(3, 'Root', 'web', '2026-02-06 19:13:07', '2026-02-06 19:13:07'),
(4, 'Gerente', 'web', '2026-02-06 19:13:10', '2026-02-06 19:13:10'),
(5, 'Operador', 'web', '2026-02-06 19:13:11', '2026-02-06 19:13:11');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `role_has_permissions`
--

DROP TABLE IF EXISTS `role_has_permissions`;
CREATE TABLE IF NOT EXISTS `role_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `role_id` bigint UNSIGNED NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 1),
(1, 3),
(2, 1),
(2, 3),
(3, 1),
(3, 3),
(4, 1),
(4, 3),
(5, 1),
(5, 3),
(5, 4),
(6, 1),
(6, 3),
(6, 4),
(6, 5),
(7, 1),
(7, 3),
(7, 4),
(7, 5),
(8, 1),
(8, 3),
(8, 4),
(8, 5),
(9, 1),
(9, 3),
(9, 4),
(10, 1),
(10, 3),
(11, 1),
(11, 3),
(12, 1),
(12, 3),
(13, 1),
(13, 3),
(14, 1),
(14, 3),
(14, 4),
(14, 5),
(15, 1),
(15, 3),
(15, 4),
(15, 5),
(16, 1),
(16, 3),
(16, 4),
(17, 1),
(17, 3),
(18, 1),
(18, 3),
(18, 4),
(19, 1),
(19, 3),
(19, 4),
(20, 1),
(20, 3),
(20, 4),
(21, 1),
(21, 3),
(22, 1),
(22, 3),
(23, 1),
(23, 3),
(24, 1),
(24, 3),
(25, 1),
(25, 3),
(26, 1),
(26, 3),
(27, 1),
(27, 3),
(27, 4),
(27, 5),
(28, 1),
(28, 3),
(28, 4),
(29, 1),
(29, 3),
(30, 1),
(30, 3),
(31, 1),
(31, 3),
(32, 1),
(32, 3),
(33, 1),
(33, 3),
(33, 4),
(34, 1),
(34, 3),
(34, 4),
(35, 1),
(35, 3),
(36, 1),
(36, 3),
(37, 1),
(37, 3),
(38, 1),
(38, 3),
(39, 1),
(39, 3),
(39, 4),
(40, 1),
(40, 3),
(40, 4),
(41, 1),
(41, 3),
(41, 4),
(42, 1),
(42, 3),
(43, 1),
(43, 3),
(44, 1),
(44, 3),
(44, 4),
(45, 1),
(45, 3),
(45, 4),
(46, 1),
(46, 3),
(46, 4),
(47, 1),
(47, 3),
(48, 1),
(48, 3),
(48, 4),
(48, 5),
(49, 1),
(49, 3),
(49, 4),
(49, 5),
(50, 1),
(50, 3),
(50, 4),
(50, 5),
(51, 1),
(51, 3),
(52, 1),
(52, 3),
(53, 1),
(53, 3),
(54, 1),
(54, 3),
(55, 1),
(55, 3),
(56, 1),
(56, 3),
(57, 1),
(57, 3),
(58, 1),
(58, 3),
(59, 1),
(59, 3),
(60, 1),
(60, 3),
(61, 1),
(61, 3),
(62, 1),
(62, 3),
(63, 1),
(63, 3),
(64, 1),
(64, 3),
(65, 1),
(65, 3),
(66, 1),
(66, 3),
(67, 1),
(67, 3),
(67, 4),
(68, 1),
(68, 3),
(69, 1),
(69, 3),
(70, 1),
(70, 3),
(71, 1),
(71, 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rules`
--

DROP TABLE IF EXISTS `rules`;
CREATE TABLE IF NOT EXISTS `rules` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `event_type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `logical_operator` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'AND',
  `priority` tinyint UNSIGNED NOT NULL DEFAULT '50',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `stop_on_match` tinyint(1) NOT NULL DEFAULT '0',
  `execution_count` int UNSIGNED NOT NULL DEFAULT '0',
  `last_executed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `rules_empresa_id_event_type_active_index` (`empresa_id`,`event_type`,`active`),
  KEY `rules_empresa_id_index` (`empresa_id`),
  KEY `rules_event_type_index` (`event_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rule_actions`
--

DROP TABLE IF EXISTS `rule_actions`;
CREATE TABLE IF NOT EXISTS `rule_actions` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `rule_id` bigint UNSIGNED NOT NULL,
  `action_type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `parameters` json DEFAULT NULL,
  `sort_order` tinyint UNSIGNED NOT NULL DEFAULT '0',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `rule_actions_rule_id_foreign` (`rule_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rule_conditions`
--

DROP TABLE IF EXISTS `rule_conditions`;
CREATE TABLE IF NOT EXISTS `rule_conditions` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `rule_id` bigint UNSIGNED NOT NULL,
  `field` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `operator` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `data_type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'numeric',
  `sort_order` tinyint UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `rule_conditions_rule_id_foreign` (`rule_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rule_executions`
--

DROP TABLE IF EXISTS `rule_executions`;
CREATE TABLE IF NOT EXISTS `rule_executions` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `rule_id` bigint UNSIGNED NOT NULL,
  `empresa_id` bigint UNSIGNED NOT NULL,
  `event_type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `context` json DEFAULT NULL,
  `conditions_result` json DEFAULT NULL,
  `actions_result` json DEFAULT NULL,
  `matched` tinyint(1) NOT NULL DEFAULT '0',
  `executed` tinyint(1) NOT NULL DEFAULT '0',
  `error_message` text COLLATE utf8mb4_unicode_ci,
  `execution_time_ms` smallint UNSIGNED DEFAULT NULL,
  `executed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `rule_executions_rule_id_executed_at_index` (`rule_id`,`executed_at`),
  KEY `rule_executions_empresa_id_event_type_executed_at_index` (`empresa_id`,`event_type`,`executed_at`),
  KEY `rule_executions_empresa_id_index` (`empresa_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `saas_plans`
--

DROP TABLE IF EXISTS `saas_plans`;
CREATE TABLE IF NOT EXISTS `saas_plans` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `stripe_price_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `precio_mensual_cop` decimal(15,2) NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `caracteristicas` json DEFAULT NULL,
  `dias_trial` int NOT NULL DEFAULT '14',
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `saas_plans_nombre_unique` (`nombre`),
  KEY `saas_plans_activo_index` (`activo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `salas`
--

DROP TABLE IF EXISTS `salas`;
CREATE TABLE IF NOT EXISTS `salas` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint UNSIGNED NOT NULL,
  `nombre` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `configuracion_json` json NOT NULL,
  `capacidad` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `salas_empresa_id_foreign` (`empresa_id`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `salas`
--

INSERT INTO `salas` (`id`, `empresa_id`, `nombre`, `configuracion_json`, `capacidad`, `created_at`, `updated_at`) VALUES
(1, 1, 'Sala 1', '[{\"col\": 1, \"num\": 1, \"fila\": \"A\", \"tipo\": \"asiento\"}, {\"col\": 2, \"num\": 2, \"fila\": \"A\", \"tipo\": \"asiento\"}, {\"col\": 3, \"num\": 3, \"fila\": \"A\", \"tipo\": \"asiento\"}, {\"col\": 4, \"num\": 4, \"fila\": \"A\", \"tipo\": \"asiento\"}, {\"col\": 5, \"num\": null, \"fila\": \"A\", \"tipo\": \"pasillo\"}, {\"col\": 6, \"num\": 5, \"fila\": \"A\", \"tipo\": \"asiento\"}, {\"col\": 7, \"num\": 6, \"fila\": \"A\", \"tipo\": \"asiento\"}, {\"col\": 8, \"num\": 7, \"fila\": \"A\", \"tipo\": \"asiento\"}, {\"col\": 9, \"num\": 8, \"fila\": \"A\", \"tipo\": \"asiento\"}, {\"col\": 1, \"num\": 1, \"fila\": \"B\", \"tipo\": \"asiento\"}, {\"col\": 2, \"num\": 2, \"fila\": \"B\", \"tipo\": \"asiento\"}, {\"col\": 3, \"num\": 3, \"fila\": \"B\", \"tipo\": \"asiento\"}, {\"col\": 4, \"num\": 4, \"fila\": \"B\", \"tipo\": \"asiento\"}, {\"col\": 5, \"num\": null, \"fila\": \"B\", \"tipo\": \"pasillo\"}, {\"col\": 6, \"num\": 5, \"fila\": \"B\", \"tipo\": \"asiento\"}, {\"col\": 7, \"num\": 6, \"fila\": \"B\", \"tipo\": \"asiento\"}, {\"col\": 8, \"num\": 7, \"fila\": \"B\", \"tipo\": \"asiento\"}, {\"col\": 9, \"num\": 8, \"fila\": \"B\", \"tipo\": \"asiento\"}, {\"col\": 1, \"num\": 1, \"fila\": \"C\", \"tipo\": \"asiento\"}, {\"col\": 2, \"num\": 2, \"fila\": \"C\", \"tipo\": \"asiento\"}, {\"col\": 3, \"num\": 3, \"fila\": \"C\", \"tipo\": \"asiento\"}, {\"col\": 4, \"num\": 4, \"fila\": \"C\", \"tipo\": \"asiento\"}, {\"col\": 5, \"num\": null, \"fila\": \"C\", \"tipo\": \"pasillo\"}, {\"col\": 6, \"num\": 5, \"fila\": \"C\", \"tipo\": \"asiento\"}, {\"col\": 7, \"num\": 6, \"fila\": \"C\", \"tipo\": \"asiento\"}, {\"col\": 8, \"num\": 7, \"fila\": \"C\", \"tipo\": \"asiento\"}, {\"col\": 9, \"num\": 8, \"fila\": \"C\", \"tipo\": \"asiento\"}, {\"col\": 1, \"num\": 1, \"fila\": \"D\", \"tipo\": \"asiento\"}, {\"col\": 2, \"num\": 2, \"fila\": \"D\", \"tipo\": \"asiento\"}, {\"col\": 3, \"num\": 3, \"fila\": \"D\", \"tipo\": \"asiento\"}, {\"col\": 4, \"num\": 4, \"fila\": \"D\", \"tipo\": \"asiento\"}, {\"col\": 5, \"num\": null, \"fila\": \"D\", \"tipo\": \"pasillo\"}, {\"col\": 6, \"num\": 5, \"fila\": \"D\", \"tipo\": \"asiento\"}, {\"col\": 7, \"num\": 6, \"fila\": \"D\", \"tipo\": \"asiento\"}, {\"col\": 8, \"num\": 7, \"fila\": \"D\", \"tipo\": \"asiento\"}, {\"col\": 9, \"num\": 8, \"fila\": \"D\", \"tipo\": \"asiento\"}, {\"col\": 1, \"num\": 1, \"fila\": \"E\", \"tipo\": \"asiento\"}, {\"col\": 2, \"num\": 2, \"fila\": \"E\", \"tipo\": \"asiento\"}, {\"col\": 3, \"num\": 3, \"fila\": \"E\", \"tipo\": \"asiento\"}, {\"col\": 4, \"num\": 4, \"fila\": \"E\", \"tipo\": \"asiento\"}, {\"col\": 5, \"num\": null, \"fila\": \"E\", \"tipo\": \"pasillo\"}, {\"col\": 6, \"num\": 5, \"fila\": \"E\", \"tipo\": \"asiento\"}, {\"col\": 7, \"num\": 6, \"fila\": \"E\", \"tipo\": \"asiento\"}, {\"col\": 8, \"num\": 7, \"fila\": \"E\", \"tipo\": \"asiento\"}, {\"col\": 9, \"num\": 8, \"fila\": \"E\", \"tipo\": \"asiento\"}, {\"col\": 1, \"num\": 1, \"fila\": \"F\", \"tipo\": \"asiento\"}, {\"col\": 2, \"num\": 2, \"fila\": \"F\", \"tipo\": \"asiento\"}, {\"col\": 3, \"num\": 3, \"fila\": \"F\", \"tipo\": \"asiento\"}, {\"col\": 4, \"num\": 4, \"fila\": \"F\", \"tipo\": \"asiento\"}, {\"col\": 5, \"num\": null, \"fila\": \"F\", \"tipo\": \"pasillo\"}, {\"col\": 6, \"num\": 5, \"fila\": \"F\", \"tipo\": \"asiento\"}, {\"col\": 7, \"num\": 6, \"fila\": \"F\", \"tipo\": \"asiento\"}, {\"col\": 8, \"num\": 7, \"fila\": \"F\", \"tipo\": \"asiento\"}, {\"col\": 9, \"num\": 8, \"fila\": \"F\", \"tipo\": \"asiento\"}, {\"col\": 1, \"num\": 1, \"fila\": \"G\", \"tipo\": \"asiento\"}, {\"col\": 2, \"num\": 2, \"fila\": \"G\", \"tipo\": \"asiento\"}, {\"col\": 3, \"num\": 3, \"fila\": \"G\", \"tipo\": \"asiento\"}, {\"col\": 4, \"num\": 4, \"fila\": \"G\", \"tipo\": \"asiento\"}, {\"col\": 5, \"num\": null, \"fila\": \"G\", \"tipo\": \"pasillo\"}, {\"col\": 6, \"num\": 5, \"fila\": \"G\", \"tipo\": \"asiento\"}, {\"col\": 7, \"num\": 6, \"fila\": \"G\", \"tipo\": \"asiento\"}, {\"col\": 8, \"num\": 7, \"fila\": \"G\", \"tipo\": \"asiento\"}, {\"col\": 9, \"num\": 8, \"fila\": \"G\", \"tipo\": \"asiento\"}, {\"col\": 1, \"num\": 1, \"fila\": \"H\", \"tipo\": \"asiento\"}, {\"col\": 2, \"num\": 2, \"fila\": \"H\", \"tipo\": \"asiento\"}, {\"col\": 3, \"num\": 3, \"fila\": \"H\", \"tipo\": \"asiento\"}, {\"col\": 4, \"num\": 4, \"fila\": \"H\", \"tipo\": \"asiento\"}, {\"col\": 5, \"num\": null, \"fila\": \"H\", \"tipo\": \"pasillo\"}, {\"col\": 6, \"num\": 5, \"fila\": \"H\", \"tipo\": \"asiento\"}, {\"col\": 7, \"num\": 6, \"fila\": \"H\", \"tipo\": \"asiento\"}, {\"col\": 8, \"num\": 7, \"fila\": \"H\", \"tipo\": \"asiento\"}, {\"col\": 9, \"num\": 8, \"fila\": \"H\", \"tipo\": \"asiento\"}, {\"col\": 1, \"num\": 1, \"fila\": \"I\", \"tipo\": \"asiento\"}, {\"col\": 2, \"num\": 2, \"fila\": \"I\", \"tipo\": \"asiento\"}, {\"col\": 3, \"num\": 3, \"fila\": \"I\", \"tipo\": \"asiento\"}, {\"col\": 4, \"num\": 4, \"fila\": \"I\", \"tipo\": \"asiento\"}, {\"col\": 5, \"num\": null, \"fila\": \"I\", \"tipo\": \"pasillo\"}, {\"col\": 6, \"num\": 5, \"fila\": \"I\", \"tipo\": \"asiento\"}, {\"col\": 7, \"num\": 6, \"fila\": \"I\", \"tipo\": \"asiento\"}, {\"col\": 8, \"num\": 7, \"fila\": \"I\", \"tipo\": \"asiento\"}, {\"col\": 9, \"num\": 8, \"fila\": \"I\", \"tipo\": \"asiento\"}, {\"col\": 5, \"num\": null, \"fila\": \"J\", \"tipo\": \"pasillo\"}, {\"col\": 6, \"num\": 5, \"fila\": \"J\", \"tipo\": \"asiento\"}, {\"col\": 7, \"num\": 6, \"fila\": \"J\", \"tipo\": \"asiento\"}, {\"col\": 8, \"num\": 7, \"fila\": \"J\", \"tipo\": \"asiento\"}, {\"col\": 9, \"num\": 8, \"fila\": \"J\", \"tipo\": \"asiento\"}]', 76, '2026-02-04 23:12:05', '2026-02-21 17:26:19'),
(2, 1, 'Sala 2', '\"[{\\\"fila\\\":\\\"A\\\",\\\"num\\\":\\\"1\\\",\\\"col\\\":1,\\\"tipo\\\":\\\"asiento\\\"},{\\\"fila\\\":\\\"A\\\",\\\"num\\\":\\\"2\\\",\\\"col\\\":2,\\\"tipo\\\":\\\"asiento\\\"},{\\\"fila\\\":\\\"A\\\",\\\"num\\\":null,\\\"col\\\":3,\\\"tipo\\\":\\\"pasillo\\\"},{\\\"fila\\\":\\\"A\\\",\\\"num\\\":\\\"3\\\",\\\"col\\\":4,\\\"tipo\\\":\\\"asiento\\\"},{\\\"fila\\\":\\\"A\\\",\\\"num\\\":\\\"4\\\",\\\"col\\\":5,\\\"tipo\\\":\\\"asiento\\\"},{\\\"fila\\\":\\\"A\\\",\\\"num\\\":null,\\\"col\\\":6,\\\"tipo\\\":\\\"pasillo\\\"},{\\\"fila\\\":\\\"B\\\",\\\"num\\\":\\\"1\\\",\\\"col\\\":1,\\\"tipo\\\":\\\"asiento\\\"},{\\\"fila\\\":\\\"B\\\",\\\"num\\\":\\\"2\\\",\\\"col\\\":2,\\\"tipo\\\":\\\"asiento\\\"},{\\\"fila\\\":\\\"B\\\",\\\"num\\\":null,\\\"col\\\":3,\\\"tipo\\\":\\\"pasillo\\\"},{\\\"fila\\\":\\\"B\\\",\\\"num\\\":\\\"3\\\",\\\"col\\\":4,\\\"tipo\\\":\\\"asiento\\\"},{\\\"fila\\\":\\\"B\\\",\\\"num\\\":\\\"4\\\",\\\"col\\\":5,\\\"tipo\\\":\\\"asiento\\\"},{\\\"fila\\\":\\\"B\\\",\\\"num\\\":null,\\\"col\\\":6,\\\"tipo\\\":\\\"pasillo\\\"},{\\\"fila\\\":\\\"C\\\",\\\"num\\\":\\\"1\\\",\\\"col\\\":1,\\\"tipo\\\":\\\"asiento\\\"},{\\\"fila\\\":\\\"C\\\",\\\"num\\\":\\\"2\\\",\\\"col\\\":2,\\\"tipo\\\":\\\"asiento\\\"},{\\\"fila\\\":\\\"C\\\",\\\"num\\\":null,\\\"col\\\":3,\\\"tipo\\\":\\\"pasillo\\\"},{\\\"fila\\\":\\\"C\\\",\\\"num\\\":\\\"3\\\",\\\"col\\\":4,\\\"tipo\\\":\\\"asiento\\\"},{\\\"fila\\\":\\\"C\\\",\\\"num\\\":\\\"4\\\",\\\"col\\\":5,\\\"tipo\\\":\\\"asiento\\\"},{\\\"fila\\\":\\\"C\\\",\\\"num\\\":null,\\\"col\\\":6,\\\"tipo\\\":\\\"pasillo\\\"},{\\\"fila\\\":\\\"D\\\",\\\"num\\\":\\\"1\\\",\\\"col\\\":1,\\\"tipo\\\":\\\"asiento\\\"},{\\\"fila\\\":\\\"D\\\",\\\"num\\\":\\\"2\\\",\\\"col\\\":2,\\\"tipo\\\":\\\"asiento\\\"},{\\\"fila\\\":\\\"D\\\",\\\"num\\\":null,\\\"col\\\":3,\\\"tipo\\\":\\\"pasillo\\\"},{\\\"fila\\\":\\\"D\\\",\\\"num\\\":\\\"3\\\",\\\"col\\\":4,\\\"tipo\\\":\\\"asiento\\\"},{\\\"fila\\\":\\\"D\\\",\\\"num\\\":\\\"4\\\",\\\"col\\\":5,\\\"tipo\\\":\\\"asiento\\\"},{\\\"fila\\\":\\\"D\\\",\\\"num\\\":null,\\\"col\\\":6,\\\"tipo\\\":\\\"pasillo\\\"},{\\\"fila\\\":\\\"E\\\",\\\"num\\\":\\\"1\\\",\\\"col\\\":1,\\\"tipo\\\":\\\"asiento\\\"},{\\\"fila\\\":\\\"E\\\",\\\"num\\\":\\\"2\\\",\\\"col\\\":2,\\\"tipo\\\":\\\"asiento\\\"},{\\\"fila\\\":\\\"E\\\",\\\"num\\\":null,\\\"col\\\":3,\\\"tipo\\\":\\\"pasillo\\\"},{\\\"fila\\\":\\\"E\\\",\\\"num\\\":\\\"3\\\",\\\"col\\\":4,\\\"tipo\\\":\\\"asiento\\\"},{\\\"fila\\\":\\\"E\\\",\\\"num\\\":\\\"4\\\",\\\"col\\\":5,\\\"tipo\\\":\\\"asiento\\\"},{\\\"fila\\\":\\\"E\\\",\\\"num\\\":null,\\\"col\\\":6,\\\"tipo\\\":\\\"pasillo\\\"}]\"', 20, '2026-02-04 23:12:05', '2026-02-21 17:26:19');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `stripe_configs`
--

DROP TABLE IF EXISTS `stripe_configs`;
CREATE TABLE IF NOT EXISTS `stripe_configs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint UNSIGNED NOT NULL,
  `public_key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Stripe Publishable Key (públicamente segura)',
  `secret_key` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Stripe Secret Key (ENCRIPTADA en valores)',
  `webhook_secret` text COLLATE utf8mb4_unicode_ci COMMENT 'Stripe Webhook Signing Secret (ENCRIPTADA)',
  `test_mode` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'true = test keys, false = live keys',
  `enabled` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Si está habilitada la integración Stripe',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `stripe_configs_empresa_id_unique` (`empresa_id`),
  KEY `stripe_configs_empresa_id_enabled_index` (`empresa_id`,`enabled`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tarifas`
--

DROP TABLE IF EXISTS `tarifas`;
CREATE TABLE IF NOT EXISTS `tarifas` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint UNSIGNED NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `dias_semana` json DEFAULT NULL COMMENT '0=Dom,1=Lun,2=Mar,3=Mie,4=Jue,5=Vie,6=Sab',
  `aplica_festivos` tinyint(1) NOT NULL DEFAULT '0',
  `es_default` tinyint(1) NOT NULL DEFAULT '0',
  `activa` tinyint(1) NOT NULL DEFAULT '1',
  `color` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#3B82F6',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tarifas_empresa_id_foreign` (`empresa_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `tarifas`
--

INSERT INTO `tarifas` (`id`, `empresa_id`, `nombre`, `precio`, `dias_semana`, `aplica_festivos`, `es_default`, `activa`, `color`, `created_at`, `updated_at`) VALUES
(1, 1, 'Entre Semana', 24000.00, '[1, 2, 3, 4, 5]', 0, 1, 1, '#3B82F6', '2026-02-17 15:10:06', '2026-02-17 15:10:06'),
(2, 1, 'Fin de Semana y Festivos', 30000.00, '[0, 6]', 1, 0, 1, '#10B981', '2026-02-17 15:10:06', '2026-02-17 15:10:06'),
(3, 1, 'Días Especiales', 18000.00, '[]', 0, 0, 1, '#F59E0B', '2026-02-17 15:10:06', '2026-02-17 15:10:06');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ubicaciones`
--

DROP TABLE IF EXISTS `ubicaciones`;
CREATE TABLE IF NOT EXISTS `ubicaciones` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint UNSIGNED DEFAULT NULL,
  `nombre` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ubicaciones_empresa_id_foreign` (`empresa_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `ubicaciones`
--

INSERT INTO `ubicaciones` (`id`, `empresa_id`, `nombre`, `created_at`, `updated_at`) VALUES
(1, 1, 'Bodega Central', '2026-02-07 16:32:21', '2026-02-07 16:32:21');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `empresa_id` bigint UNSIGNED DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pin_code` varchar(4) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `empleado_id` bigint UNSIGNED DEFAULT NULL,
  `estado` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_empleado_id_unique` (`empleado_id`),
  UNIQUE KEY `users_username_unique` (`username`),
  KEY `users_empresa_id_foreign` (`empresa_id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `email`, `empresa_id`, `email_verified_at`, `password`, `pin_code`, `remember_token`, `empleado_id`, `estado`, `created_at`, `updated_at`) VALUES
(8, 'Cajero B', NULL, 'cajeroB@test.com', 1, NULL, '$2y$10$vRF0i2BbOfPGSgxbrGHsz.tssKHDo9YGwLvs7S9eAbAqbvD0aWWJa', NULL, NULL, NULL, 1, '2026-02-16 17:27:09', '2026-02-16 17:27:09'),
(7, 'Cajero A', NULL, 'cajeroA@test.com', 1, NULL, '$2y$10$vRF0i2BbOfPGSgxbrGHsz.tssKHDo9YGwLvs7S9eAbAqbvD0aWWJa', NULL, NULL, NULL, 1, '2026-02-16 17:27:09', '2026-02-16 17:27:09'),
(6, 'Super Administrador', 'admin@admin.com', 'admin@admin.com', 1, NULL, '$2y$10$eoDLZDXTXhcVt35xltKGIuEL0nG95fO4GHw29R6e.TiklNfjOXBMG', NULL, NULL, NULL, 1, '2026-02-16 16:59:50', '2026-02-16 17:48:52');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

DROP TABLE IF EXISTS `ventas`;
CREATE TABLE IF NOT EXISTS `ventas` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `empresa_id` bigint UNSIGNED NOT NULL,
  `cliente_id` bigint UNSIGNED NOT NULL,
  `cliente_tipo_doc` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'CC',
  `cliente_documento` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cliente_nombre` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cliente_email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cliente_telefono` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `solicita_factura` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'true = Cliente pidió factura explícitamente',
  `preferencia_fiscal` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'fe_todo',
  `user_id` bigint UNSIGNED NOT NULL,
  `caja_id` bigint UNSIGNED NOT NULL,
  `comprobante_id` bigint UNSIGNED NOT NULL,
  `numero_comprobante` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `consecutivo_pos` bigint DEFAULT NULL,
  `metodo_pago` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `canal` enum('ventanilla','confiteria','web','mixta') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'confiteria',
  `tipo_venta` enum('FISICA','WEB') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'FISICA',
  `origen` enum('POS','WEB') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'POS',
  `fecha_hora` datetime NOT NULL,
  `fecha_operativa` date DEFAULT NULL,
  `subtotal` decimal(8,2) NOT NULL,
  `subtotal_cine` decimal(12,2) NOT NULL DEFAULT '0.00',
  `subtotal_confiteria` decimal(12,2) NOT NULL DEFAULT '0.00',
  `impuesto` decimal(8,2) NOT NULL,
  `inc_total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total` decimal(8,2) NOT NULL,
  `total_final` decimal(12,2) NOT NULL DEFAULT '0.00',
  `tarifa_servicio` decimal(5,2) NOT NULL DEFAULT '0.00' COMMENT 'Porcentaje de tarifa por servicio',
  `monto_tarifa` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Monto de la tarifa calculada',
  `stripe_payment_intent_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'ID del payment intent de Stripe',
  `estado_pago` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado_fiscal` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'NORMAL',
  `en_contingencia` tinyint(1) NOT NULL DEFAULT '0',
  `cufe` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numero_factura_dian` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_factura_dian` timestamp NULL DEFAULT NULL,
  `qrcode_url` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `xml_factura_url` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pdf_factura_url` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `error_dian` text COLLATE utf8mb4_unicode_ci,
  `inventario_descontado_at` timestamp NULL DEFAULT NULL,
  `movimiento_creado_at` timestamp NULL DEFAULT NULL,
  `monto_recibido` decimal(10,2) DEFAULT NULL,
  `cambio` decimal(8,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ventas_numero_comprobante_unique` (`numero_comprobante`),
  KEY `ventas_cliente_id_foreign` (`cliente_id`),
  KEY `ventas_user_id_foreign` (`user_id`),
  KEY `ventas_comprobante_id_foreign` (`comprobante_id`),
  KEY `ventas_caja_id_foreign` (`caja_id`),
  KEY `ventas_empresa_id_fecha_hora_index` (`empresa_id`,`fecha_hora`),
  KEY `ventas_empresa_id_estado_pago_index` (`empresa_id`,`estado_pago`),
  KEY `ventas_estado_pago_created_at_index` (`estado_pago`,`created_at`),
  KEY `ventas_inventario_descontado_at_index` (`inventario_descontado_at`),
  KEY `idx_ventas_empresa_user_fecha` (`empresa_id`,`user_id`,`created_at`),
  KEY `idx_ventas_empresa_fecha` (`empresa_id`,`fecha_hora`),
  KEY `idx_ventas_empresa_estado` (`empresa_id`,`estado_pago`),
  KEY `idx_ventas_empresa_canal` (`empresa_id`,`canal`),
  KEY `idx_ventas_perf_caja_estado` (`empresa_id`,`caja_id`,`estado_pago`),
  KEY `ventas_fecha_operativa_index` (`fecha_operativa`),
  KEY `ventas_consecutivo_pos_caja_id_index` (`consecutivo_pos`,`caja_id`),
  KEY `ventas_estado_fiscal_index` (`estado_fiscal`)
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `ventas`
--

INSERT INTO `ventas` (`id`, `empresa_id`, `cliente_id`, `cliente_tipo_doc`, `cliente_documento`, `cliente_nombre`, `cliente_email`, `cliente_telefono`, `solicita_factura`, `preferencia_fiscal`, `user_id`, `caja_id`, `comprobante_id`, `numero_comprobante`, `consecutivo_pos`, `metodo_pago`, `canal`, `tipo_venta`, `origen`, `fecha_hora`, `fecha_operativa`, `subtotal`, `subtotal_cine`, `subtotal_confiteria`, `impuesto`, `inc_total`, `total`, `total_final`, `tarifa_servicio`, `monto_tarifa`, `stripe_payment_intent_id`, `estado_pago`, `estado_fiscal`, `en_contingencia`, `cufe`, `numero_factura_dian`, `fecha_factura_dian`, `qrcode_url`, `xml_factura_url`, `pdf_factura_url`, `error_dian`, `inventario_descontado_at`, `movimiento_creado_at`, `monto_recibido`, `cambio`, `created_at`, `updated_at`) VALUES
(1, 1, 11, 'CC', NULL, 'CONSUMIDOR FINAL', NULL, NULL, 0, 'fe_todo', 6, 1, 1, 'POS001-0000001', 1, 'EFECTIVO', 'mixta', 'FISICA', 'POS', '2026-02-20 11:14:21', '2026-02-20', 221000.00, 120000.00, 101000.00, 0.00, 7481.48, 221000.00, 221000.00, 0.00, 0.00, NULL, 'PAGADA', 'NORMAL', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-20 16:14:23', NULL, 221000.00, 0.00, '2026-02-20 16:14:21', '2026-02-20 16:14:23'),
(2, 1, 11, 'CC', NULL, 'CONSUMIDOR FINAL', NULL, NULL, 0, 'fe_todo', 6, 1, 1, 'POS001-0000002', 2, 'EFECTIVO', 'mixta', 'FISICA', 'POS', '2026-02-20 12:32:31', '2026-02-20', 86020.00, 60000.00, 26020.00, 0.00, 1927.41, 86020.00, 86020.00, 0.00, 0.00, NULL, 'PAGADA', 'NORMAL', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-20 17:32:33', NULL, 86020.00, 0.00, '2026-02-20 17:32:32', '2026-02-20 17:32:33'),
(3, 1, 11, 'CC', NULL, 'CONSUMIDOR FINAL', NULL, NULL, 0, 'fe_todo', 6, 1, 1, 'POS001-0000003', 3, 'EFECTIVO', 'confiteria', 'FISICA', 'POS', '2026-02-20 12:33:31', '2026-02-20', 47096.00, 0.00, 47096.00, 0.00, 3488.59, 47096.00, 47096.00, 0.00, 0.00, NULL, 'PAGADA', 'NORMAL', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-20 17:33:35', NULL, 47096.00, 0.00, '2026-02-20 17:33:32', '2026-02-20 17:33:35'),
(4, 1, 11, 'CC', NULL, 'CONSUMIDOR FINAL', NULL, NULL, 0, 'fe_todo', 6, 1, 1, 'POS001-0000004', 4, 'EFECTIVO', 'ventanilla', 'FISICA', 'POS', '2026-02-20 13:11:01', '2026-02-20', 30000.00, 30000.00, 0.00, 0.00, 0.00, 30000.00, 30000.00, 0.00, 0.00, NULL, 'PAGADA', 'NORMAL', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-20 18:11:01', NULL, 30000.00, 0.00, '2026-02-20 18:11:01', '2026-02-20 18:11:01'),
(5, 1, 11, 'CC', NULL, 'CONSUMIDOR FINAL', NULL, NULL, 0, 'fe_todo', 6, 1, 1, 'POS001-0000005', 5, 'EFECTIVO', 'confiteria', 'FISICA', 'POS', '2026-02-20 19:01:15', '2026-02-20', 3780.00, 0.00, 3780.00, 0.00, 280.00, 3780.00, 3780.00, 0.00, 0.00, NULL, 'PAGADA', 'NORMAL', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-21 00:01:16', NULL, 3780.00, 0.00, '2026-02-21 00:01:15', '2026-02-21 00:01:16'),
(6, 1, 11, 'CC', NULL, 'CONSUMIDOR FINAL', NULL, NULL, 0, 'fe_todo', 6, 1, 1, 'POS001-0000006', 6, 'EFECTIVO', 'mixta', 'FISICA', 'POS', '2026-02-20 20:36:38', '2026-02-20', 146000.00, 60000.00, 86000.00, 0.00, 6370.37, 146000.00, 146000.00, 0.00, 0.00, NULL, 'PAGADA', 'NORMAL', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-21 01:36:39', NULL, 146000.00, 0.00, '2026-02-21 01:36:38', '2026-02-21 01:36:39'),
(7, 1, 11, 'CC', NULL, 'CONSUMIDOR FINAL', NULL, NULL, 0, 'fe_todo', 6, 1, 1, 'POS001-0000007', 7, 'EFECTIVO', 'confiteria', 'FISICA', 'POS', '2026-02-20 20:57:48', '2026-02-20', 510500.00, 0.00, 510500.00, 0.00, 37814.81, 510500.00, 510500.00, 0.00, 0.00, NULL, 'PAGADA', 'NORMAL', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-21 01:57:49', NULL, 510500.00, 0.00, '2026-02-21 01:57:48', '2026-02-21 01:57:49'),
(8, 1, 11, 'CC', NULL, 'CONSUMIDOR FINAL', NULL, NULL, 0, 'fe_todo', 6, 1, 1, 'POS001-0000008', 8, 'EFECTIVO', 'confiteria', 'FISICA', 'POS', '2026-02-20 21:00:47', '2026-02-20', 37000.00, 0.00, 37000.00, 0.00, 2740.74, 37000.00, 37000.00, 0.00, 0.00, NULL, 'PAGADA', 'NORMAL', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-21 02:00:47', NULL, 37000.00, 0.00, '2026-02-21 02:00:47', '2026-02-21 02:00:47'),
(9, 1, 11, 'CC', NULL, 'CONSUMIDOR FINAL', NULL, NULL, 0, 'fe_todo', 6, 1, 1, 'POS001-0000009', 9, 'EFECTIVO', 'confiteria', 'FISICA', 'POS', '2026-02-20 21:09:06', '2026-02-20', 510500.00, 0.00, 510500.00, 0.00, 37814.81, 510500.00, 510500.00, 0.00, 0.00, NULL, 'PAGADA', 'NORMAL', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-21 02:09:07', NULL, 510500.00, 0.00, '2026-02-21 02:09:07', '2026-02-21 02:09:07'),
(10, 1, 11, 'CC', NULL, 'CONSUMIDOR FINAL', NULL, NULL, 0, 'fe_todo', 6, 1, 1, 'POS001-0000010', 10, 'EFECTIVO', 'ventanilla', 'FISICA', 'POS', '2026-02-21 11:00:36', '2026-02-20', 73104.00, 60000.00, 13104.00, 0.00, 970.67, 73104.00, 73104.00, 0.00, 0.00, NULL, 'PAGADA', 'NORMAL', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-21 16:00:37', NULL, 73104.00, 0.00, '2026-02-21 16:00:36', '2026-02-21 16:00:37'),
(11, 1, 11, 'CC', NULL, 'CONSUMIDOR FINAL', NULL, NULL, 0, 'fe_todo', 6, 1, 1, 'POS001-0000011', 11, 'EFECTIVO', 'ventanilla', 'FISICA', 'POS', '2026-02-21 11:00:38', '2026-02-20', 199000.00, 150000.00, 49000.00, 0.00, 3629.63, 199000.00, 199000.00, 0.00, 0.00, NULL, 'PAGADA', 'NORMAL', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-21 16:00:38', NULL, 199000.00, 0.00, '2026-02-21 16:00:38', '2026-02-21 16:00:38'),
(12, 1, 11, 'CC', NULL, 'CONSUMIDOR FINAL', NULL, NULL, 0, 'fe_todo', 6, 1, 1, 'POS001-0000012', 12, 'EFECTIVO', 'ventanilla', 'FISICA', 'POS', '2026-02-21 11:00:38', '2026-02-20', 82864.00, 60000.00, 22864.00, 0.00, 1693.63, 82864.00, 82864.00, 0.00, 0.00, NULL, 'PAGADA', 'NORMAL', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-21 16:00:38', NULL, 82864.00, 0.00, '2026-02-21 16:00:38', '2026-02-21 16:00:38'),
(13, 1, 11, 'CC', NULL, 'CONSUMIDOR FINAL', NULL, NULL, 0, 'fe_todo', 6, 1, 1, 'POS001-0000013', 13, 'EFECTIVO', 'ventanilla', 'FISICA', 'POS', '2026-02-21 11:00:38', '2026-02-20', 165820.00, 120000.00, 45820.00, 0.00, 3394.07, 165820.00, 165820.00, 0.00, 0.00, NULL, 'PAGADA', 'NORMAL', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-21 16:00:39', NULL, 165820.00, 0.00, '2026-02-21 16:00:38', '2026-02-21 16:00:39'),
(14, 1, 11, 'CC', NULL, 'CONSUMIDOR FINAL', NULL, NULL, 0, 'fe_todo', 6, 1, 1, 'POS001-0000014', 14, 'EFECTIVO', 'ventanilla', 'FISICA', 'POS', '2026-02-21 11:00:39', '2026-02-20', 155520.00, 90000.00, 65520.00, 0.00, 4853.33, 155520.00, 155520.00, 0.00, 0.00, NULL, 'PAGADA', 'NORMAL', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-21 16:00:39', NULL, 155520.00, 0.00, '2026-02-21 16:00:39', '2026-02-21 16:00:39'),
(15, 1, 11, 'CC', NULL, 'CONSUMIDOR FINAL', NULL, NULL, 0, 'fe_todo', 6, 1, 1, 'POS001-0000015', 15, 'EFECTIVO', 'ventanilla', 'FISICA', 'POS', '2026-02-21 11:00:39', '2026-02-20', 51924.00, 30000.00, 21924.00, 0.00, 1624.00, 51924.00, 51924.00, 0.00, 0.00, NULL, 'PAGADA', 'NORMAL', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-21 16:00:39', NULL, 51924.00, 0.00, '2026-02-21 16:00:39', '2026-02-21 16:00:39'),
(16, 1, 11, 'CC', NULL, 'CONSUMIDOR FINAL', NULL, NULL, 0, 'fe_todo', 6, 1, 1, 'POS001-0000016', 16, 'EFECTIVO', 'ventanilla', 'FISICA', 'POS', '2026-02-21 11:00:39', '2026-02-20', 99020.00, 60000.00, 39020.00, 0.00, 2890.37, 99020.00, 99020.00, 0.00, 0.00, NULL, 'PAGADA', 'NORMAL', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-21 16:00:40', NULL, 99020.00, 0.00, '2026-02-21 16:00:39', '2026-02-21 16:00:40'),
(17, 1, 11, 'CC', NULL, 'CONSUMIDOR FINAL', NULL, NULL, 0, 'fe_todo', 6, 1, 1, 'POS001-0000017', 17, 'EFECTIVO', 'ventanilla', 'FISICA', 'POS', '2026-02-21 11:00:40', '2026-02-20', 106690.00, 90000.00, 16690.00, 0.00, 1236.30, 106690.00, 106690.00, 0.00, 0.00, NULL, 'PAGADA', 'NORMAL', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-21 16:00:40', NULL, 106690.00, 0.00, '2026-02-21 16:00:40', '2026-02-21 16:00:40'),
(18, 1, 11, 'CC', NULL, 'CONSUMIDOR FINAL', NULL, NULL, 0, 'fe_todo', 6, 1, 1, 'POS001-0000018', 18, 'EFECTIVO', 'ventanilla', 'FISICA', 'POS', '2026-02-21 11:00:40', '2026-02-20', 123700.00, 90000.00, 33700.00, 0.00, 2496.30, 123700.00, 123700.00, 0.00, 0.00, NULL, 'PAGADA', 'NORMAL', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-21 16:00:41', NULL, 123700.00, 0.00, '2026-02-21 16:00:40', '2026-02-21 16:00:41'),
(19, 1, 11, 'CC', NULL, 'CONSUMIDOR FINAL', NULL, NULL, 0, 'fe_todo', 6, 1, 1, 'POS001-0000019', 19, 'EFECTIVO', 'ventanilla', 'FISICA', 'POS', '2026-02-21 11:01:14', '2026-02-20', 14000.00, 0.00, 14000.00, 0.00, 1037.04, 14000.00, 14000.00, 0.00, 0.00, NULL, 'PAGADA', 'NORMAL', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-21 16:01:14', NULL, 14000.00, 0.00, '2026-02-21 16:01:14', '2026-02-21 16:01:14'),
(20, 1, 11, 'CC', NULL, 'CONSUMIDOR FINAL', NULL, NULL, 0, 'fe_todo', 6, 1, 1, 'POS001-0000020', 20, 'EFECTIVO', 'confiteria', 'FISICA', 'POS', '2026-02-21 12:37:38', '2026-02-20', 37000.00, 0.00, 37000.00, 0.00, 2741.00, 37000.00, 37000.00, 0.00, 0.00, NULL, 'PAGADA', 'NORMAL', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-21 17:37:39', NULL, 37000.00, 0.00, '2026-02-21 17:37:38', '2026-02-21 17:37:39'),
(21, 1, 11, 'CC', NULL, 'CONSUMIDOR FINAL', NULL, NULL, 0, 'fe_todo', 6, 2, 1, 'POS002-0000001', 1, 'EFECTIVO', 'mixta', 'FISICA', 'POS', '2026-02-21 14:09:36', '2026-02-20', 98780.00, 60000.00, 38780.00, 0.00, 2873.00, 98780.00, 98780.00, 0.00, 0.00, NULL, 'PAGADA', 'NORMAL', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-21 19:09:36', NULL, 98780.00, 0.00, '2026-02-21 19:09:36', '2026-02-21 19:09:36'),
(22, 1, 11, 'CC', NULL, 'CONSUMIDOR FINAL', NULL, NULL, 0, 'fe_todo', 6, 2, 1, 'POS002-0000002', 2, 'EFECTIVO', 'confiteria', 'FISICA', 'POS', '2026-02-21 14:11:14', '2026-02-20', 37000.00, 0.00, 37000.00, 0.00, 2741.00, 37000.00, 37000.00, 0.00, 0.00, NULL, 'PAGADA', 'NORMAL', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-21 19:11:14', NULL, 37000.00, 0.00, '2026-02-21 19:11:14', '2026-02-21 19:11:14'),
(23, 1, 11, 'CC', NULL, 'CONSUMIDOR FINAL', NULL, NULL, 0, 'fe_todo', 6, 2, 1, 'POS002-0000003', 3, 'EFECTIVO', 'confiteria', 'FISICA', 'POS', '2026-02-21 17:02:06', '2026-02-20', 8500.00, 0.00, 8500.00, 0.00, 630.00, 8500.00, 8500.00, 0.00, 0.00, NULL, 'PAGADA', 'NORMAL', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-21 22:02:06', NULL, 8500.00, 0.00, '2026-02-21 22:02:06', '2026-02-21 22:02:06');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `venta_funcion_asientos`
--

DROP TABLE IF EXISTS `venta_funcion_asientos`;
CREATE TABLE IF NOT EXISTS `venta_funcion_asientos` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `venta_id` bigint UNSIGNED NOT NULL,
  `funcion_asiento_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `venta_funcion_asientos_venta_id_foreign` (`venta_id`),
  KEY `venta_funcion_asientos_funcion_asiento_id_foreign` (`funcion_asiento_id`)
) ENGINE=MyISAM AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `venta_funcion_asientos`
--

INSERT INTO `venta_funcion_asientos` (`id`, `venta_id`, `funcion_asiento_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1957, '2026-02-20 16:14:22', '2026-02-20 16:14:22'),
(2, 1, 1958, '2026-02-20 16:14:22', '2026-02-20 16:14:22'),
(3, 1, 1962, '2026-02-20 16:14:22', '2026-02-20 16:14:22'),
(4, 1, 1961, '2026-02-20 16:14:22', '2026-02-20 16:14:22'),
(5, 2, 1940, '2026-02-20 17:32:32', '2026-02-20 17:32:32'),
(6, 2, 1939, '2026-02-20 17:32:32', '2026-02-20 17:32:32'),
(7, 4, 1965, '2026-02-20 18:11:01', '2026-02-20 18:11:01'),
(8, 6, 1960, '2026-02-21 01:36:38', '2026-02-21 01:36:38'),
(9, 6, 1959, '2026-02-21 01:36:38', '2026-02-21 01:36:38'),
(10, 10, 1503, '2026-02-21 16:00:36', '2026-02-21 16:00:36'),
(11, 10, 1504, '2026-02-21 16:00:36', '2026-02-21 16:00:36'),
(12, 11, 1505, '2026-02-21 16:00:38', '2026-02-21 16:00:38'),
(13, 11, 1506, '2026-02-21 16:00:38', '2026-02-21 16:00:38'),
(14, 11, 1507, '2026-02-21 16:00:38', '2026-02-21 16:00:38'),
(15, 11, 1508, '2026-02-21 16:00:38', '2026-02-21 16:00:38'),
(16, 11, 1509, '2026-02-21 16:00:38', '2026-02-21 16:00:38'),
(17, 12, 1510, '2026-02-21 16:00:38', '2026-02-21 16:00:38'),
(18, 12, 1511, '2026-02-21 16:00:38', '2026-02-21 16:00:38'),
(19, 13, 1512, '2026-02-21 16:00:38', '2026-02-21 16:00:38'),
(20, 13, 1513, '2026-02-21 16:00:38', '2026-02-21 16:00:38'),
(21, 13, 1514, '2026-02-21 16:00:38', '2026-02-21 16:00:38'),
(22, 13, 1515, '2026-02-21 16:00:38', '2026-02-21 16:00:38'),
(23, 14, 1516, '2026-02-21 16:00:39', '2026-02-21 16:00:39'),
(24, 14, 1517, '2026-02-21 16:00:39', '2026-02-21 16:00:39'),
(25, 14, 1518, '2026-02-21 16:00:39', '2026-02-21 16:00:39'),
(26, 15, 1519, '2026-02-21 16:00:39', '2026-02-21 16:00:39'),
(27, 16, 1520, '2026-02-21 16:00:39', '2026-02-21 16:00:39'),
(28, 16, 1521, '2026-02-21 16:00:40', '2026-02-21 16:00:40'),
(29, 17, 1522, '2026-02-21 16:00:40', '2026-02-21 16:00:40'),
(30, 17, 1523, '2026-02-21 16:00:40', '2026-02-21 16:00:40'),
(31, 17, 1524, '2026-02-21 16:00:40', '2026-02-21 16:00:40'),
(32, 18, 1525, '2026-02-21 16:00:40', '2026-02-21 16:00:40'),
(33, 18, 1526, '2026-02-21 16:00:40', '2026-02-21 16:00:40'),
(34, 18, 1527, '2026-02-21 16:00:40', '2026-02-21 16:00:40'),
(35, 21, 1997, '2026-02-21 19:09:36', '2026-02-21 19:09:36'),
(36, 21, 1990, '2026-02-21 19:09:36', '2026-02-21 19:09:36');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vertical_configs`
--

DROP TABLE IF EXISTS `vertical_configs`;
CREATE TABLE IF NOT EXISTS `vertical_configs` (
  `empresa_id` bigint UNSIGNED NOT NULL,
  `vertical` enum('cine','restaurante','estadio','retail','evento') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cine',
  `features` json NOT NULL,
  `fiscal_provider` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`empresa_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
