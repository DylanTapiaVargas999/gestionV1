-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Versión del servidor:         10.4.32-MariaDB - mariadb.org binary distribution
-- SO del servidor:              Win64
-- HeidiSQL Versión:             12.11.0.7065
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Volcando estructura de base de datos para sgcs
CREATE DATABASE IF NOT EXISTS `sgcs` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;
USE `sgcs`;

-- Volcando estructura para tabla sgcs.accesos
CREATE TABLE IF NOT EXISTS `accesos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `usuario_id` char(36) NOT NULL,
  `ip` varchar(50) DEFAULT NULL,
  `accion` varchar(100) DEFAULT NULL,
  `recurso` varchar(255) DEFAULT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `accesos_usuario_id_foreign` (`usuario_id`),
  CONSTRAINT `accesos_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla sgcs.accesos: ~0 rows (aproximadamente)

-- Volcando estructura para tabla sgcs.ajustes_cronograma
CREATE TABLE IF NOT EXISTS `ajustes_cronograma` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `proyecto_id` varchar(255) NOT NULL,
  `tipo_ajuste` enum('manual','automatico','solicitud_cambio') NOT NULL DEFAULT 'automatico',
  `estado` enum('propuesto','aprobado','aplicado','rechazado','revertido') NOT NULL DEFAULT 'propuesto',
  `desviaciones_detectadas` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`desviaciones_detectadas`)),
  `ruta_critica` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`ruta_critica`)),
  `recursos_sobrecargados` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`recursos_sobrecargados`)),
  `estrategia` varchar(50) DEFAULT NULL,
  `ajustes_propuestos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`ajustes_propuestos`)),
  `ajustes_aplicados` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`ajustes_aplicados`)),
  `dias_recuperados` int(11) NOT NULL DEFAULT 0,
  `recursos_afectados` int(11) NOT NULL DEFAULT 0,
  `score_solucion` decimal(5,2) DEFAULT NULL,
  `costo_adicional_estimado` decimal(8,2) DEFAULT NULL,
  `aprobado_por` char(36) DEFAULT NULL,
  `aprobado_en` timestamp NULL DEFAULT NULL,
  `motivo_ajuste` text DEFAULT NULL,
  `notas_rechazo` text DEFAULT NULL,
  `creado_por` char(36) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ajustes_cronograma_aprobado_por_foreign` (`aprobado_por`),
  KEY `ajustes_cronograma_creado_por_foreign` (`creado_por`),
  KEY `ajustes_cronograma_proyecto_id_estado_index` (`proyecto_id`,`estado`),
  KEY `ajustes_cronograma_created_at_index` (`created_at`),
  CONSTRAINT `ajustes_cronograma_aprobado_por_foreign` FOREIGN KEY (`aprobado_por`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ajustes_cronograma_creado_por_foreign` FOREIGN KEY (`creado_por`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL,
  CONSTRAINT `ajustes_cronograma_proyecto_id_foreign` FOREIGN KEY (`proyecto_id`) REFERENCES `proyectos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla sgcs.ajustes_cronograma: ~0 rows (aproximadamente)

-- Volcando estructura para tabla sgcs.auditorias
CREATE TABLE IF NOT EXISTS `auditorias` (
  `id` char(36) NOT NULL,
  `tipo_entidad` varchar(100) DEFAULT NULL,
  `entidad_id` char(36) DEFAULT NULL,
  `accion` varchar(100) DEFAULT NULL,
  `usuario_id` char(36) DEFAULT NULL,
  `detalles` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`detalles`)),
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `auditorias_usuario_id_foreign` (`usuario_id`),
  CONSTRAINT `auditorias_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla sgcs.auditorias: ~0 rows (aproximadamente)

-- Volcando estructura para tabla sgcs.comite_cambios
CREATE TABLE IF NOT EXISTS `comite_cambios` (
  `id` char(36) NOT NULL,
  `proyecto_id` char(36) NOT NULL,
  `nombre` varchar(255) DEFAULT NULL,
  `quorum` int(11) DEFAULT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `comite_cambios_proyecto_id_foreign` (`proyecto_id`),
  CONSTRAINT `comite_cambios_proyecto_id_foreign` FOREIGN KEY (`proyecto_id`) REFERENCES `proyectos` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla sgcs.comite_cambios: ~2 rows (aproximadamente)
INSERT INTO `comite_cambios` (`id`, `proyecto_id`, `nombre`, `quorum`, `creado_en`) VALUES
	('2d8c12b8-92ee-4992-a935-31b586269a43', 'c63ea06c-a6f9-41ec-abdd-48f1f2821e86', 'Comité de Control de Cambios E-Commerce', 3, '2025-11-14 05:48:02'),
	('e1b5a0c6-f4a3-4f20-ab02-8547b7266707', '1970240a-41aa-4e90-bab7-8dea24cd3de5', 'Comité de Control de Cambios ERP', 4, '2025-11-14 05:48:03');

-- Volcando estructura para tabla sgcs.commits_repositorio
CREATE TABLE IF NOT EXISTS `commits_repositorio` (
  `id` char(36) NOT NULL,
  `url_repositorio` text NOT NULL,
  `hash_commit` varchar(191) NOT NULL,
  `ec_id` char(36) DEFAULT NULL,
  `autor` text DEFAULT NULL,
  `mensaje` text DEFAULT NULL,
  `fecha_commit` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `commits_repositorio_ec_id_index` (`ec_id`),
  KEY `commits_repositorio_hash_commit_index` (`hash_commit`),
  CONSTRAINT `commits_repositorio_ec_id_foreign` FOREIGN KEY (`ec_id`) REFERENCES `elementos_configuracion` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla sgcs.commits_repositorio: ~0 rows (aproximadamente)

-- Volcando estructura para tabla sgcs.daily_scrums
CREATE TABLE IF NOT EXISTS `daily_scrums` (
  `id_daily` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_sprint` bigint(20) unsigned NOT NULL,
  `id_usuario` char(36) NOT NULL,
  `fecha` date NOT NULL,
  `que_hice_ayer` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Array de tareas completadas ayer' CHECK (json_valid(`que_hice_ayer`)),
  `que_hare_hoy` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Array de tareas planificadas para hoy' CHECK (json_valid(`que_hare_hoy`)),
  `impedimentos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Array de impedimentos reportados' CHECK (json_valid(`impedimentos`)),
  `notas_adicionales` text DEFAULT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `actualizado_en` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_daily`),
  UNIQUE KEY `daily_scrum_unique` (`id_sprint`,`id_usuario`,`fecha`),
  KEY `daily_scrums_id_usuario_foreign` (`id_usuario`),
  KEY `daily_scrums_fecha_index` (`fecha`),
  CONSTRAINT `daily_scrums_id_sprint_foreign` FOREIGN KEY (`id_sprint`) REFERENCES `sprints` (`id_sprint`) ON DELETE CASCADE,
  CONSTRAINT `daily_scrums_id_usuario_foreign` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla sgcs.daily_scrums: ~0 rows (aproximadamente)

-- Volcando estructura para tabla sgcs.elementos_configuracion
CREATE TABLE IF NOT EXISTS `elementos_configuracion` (
  `id` char(36) NOT NULL,
  `codigo_ec` varchar(50) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `proyecto_id` char(36) NOT NULL,
  `tipo` enum('DOCUMENTO','CODIGO','SCRIPT_BD','CONFIGURACION','OTRO') NOT NULL DEFAULT 'OTRO',
  `version_actual_id` char(36) DEFAULT NULL,
  `creado_por` char(36) DEFAULT NULL,
  `estado` enum('PENDIENTE','BORRADOR','EN_REVISION','APROBADO','LIBERADO','OBSOLETO') NOT NULL DEFAULT 'BORRADOR',
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `actualizado_en` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `elementos_configuracion_codigo_ec_unique` (`codigo_ec`),
  KEY `elementos_configuracion_proyecto_id_foreign` (`proyecto_id`),
  KEY `elementos_configuracion_creado_por_foreign` (`creado_por`),
  KEY `elementos_configuracion_version_actual_id_foreign` (`version_actual_id`),
  CONSTRAINT `elementos_configuracion_creado_por_foreign` FOREIGN KEY (`creado_por`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL,
  CONSTRAINT `elementos_configuracion_proyecto_id_foreign` FOREIGN KEY (`proyecto_id`) REFERENCES `proyectos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `elementos_configuracion_version_actual_id_foreign` FOREIGN KEY (`version_actual_id`) REFERENCES `versiones_ec` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla sgcs.elementos_configuracion: ~33 rows (aproximadamente)
INSERT INTO `elementos_configuracion` (`id`, `codigo_ec`, `titulo`, `descripcion`, `proyecto_id`, `tipo`, `version_actual_id`, `creado_por`, `estado`, `creado_en`, `actualizado_en`) VALUES
	('0508cba4-60a0-489c-bf45-06195f756d3e', 'ECOM-CODE-003', 'Módulo de Gestión de Productos', 'CRUD completo de productos, categorías y variantes', 'c63ea06c-a6f9-41ec-abdd-48f1f2821e86', 'CODIGO', NULL, '85ad1d9a-d33c-45c5-a652-ae34e08de3b9', 'APROBADO', '2025-11-14 05:48:02', '2025-11-14 05:48:02'),
	('0e14d478-46e9-4a03-965d-2b85fb5a77fd', 'ERP-DOC-007', 'Manual de Usuario Final', 'Guía completa de uso del sistema con capturas de pantalla', '1970240a-41aa-4e90-bab7-8dea24cd3de5', 'DOCUMENTO', NULL, 'b0ae25ef-b19d-412e-a628-5a49d084b82d', 'PENDIENTE', '2025-11-14 05:48:03', '2025-11-14 05:48:03'),
	('105cd5b5-6255-4067-bd0a-30366b3ec45e', 'ECOM-AUTH-001', 'Módulo de Autenticación', 'Sistema de registro, login y JWT para autenticación de usuarios', 'c63ea06c-a6f9-41ec-abdd-48f1f2821e86', 'CODIGO', NULL, '5d5c77fd-34eb-4fbf-9be6-d9a3014cb337', 'APROBADO', '2025-11-14 05:48:03', '2025-11-14 05:48:03'),
	('123b42d1-98bc-45eb-b43e-7627a8f3dac3', 'ERP-CODE-001', 'Repositorio Git - ERP', 'Código fuente con estructura modular por subsistemas', '1970240a-41aa-4e90-bab7-8dea24cd3de5', 'CODIGO', NULL, 'b0ae25ef-b19d-412e-a628-5a49d084b82d', 'APROBADO', '2025-11-14 05:48:03', '2025-11-14 05:48:03'),
	('1cfef1ba-65f9-47dc-ad1d-bef147fe82b7', 'ERP-DB-001', 'Scripts DDL y Migraciones', 'Scripts SQL para creación de esquema y datos iniciales', '1970240a-41aa-4e90-bab7-8dea24cd3de5', 'SCRIPT_BD', NULL, 'b0ae25ef-b19d-412e-a628-5a49d084b82d', 'APROBADO', '2025-11-14 05:48:03', '2025-11-14 05:48:03'),
	('1d1645aa-cf51-44b6-98fe-12f550422732', 'ERP-CODE-002', 'Módulo de Contabilidad', 'Sistema contable con libro mayor, balance y estados financieros', '1970240a-41aa-4e90-bab7-8dea24cd3de5', 'CODIGO', NULL, 'b0ae25ef-b19d-412e-a628-5a49d084b82d', 'EN_REVISION', '2025-11-14 05:48:03', '2025-11-14 05:48:03'),
	('3135cff2-8ca7-434e-ab27-970c8df4bfb1', 'ERP-DOC-004', 'Modelo Entidad-Relación', 'Diagrama ER normalizado con 85 tablas y relaciones', '1970240a-41aa-4e90-bab7-8dea24cd3de5', 'DOCUMENTO', NULL, 'b0ae25ef-b19d-412e-a628-5a49d084b82d', 'APROBADO', '2025-11-14 05:48:03', '2025-11-14 05:48:03'),
	('3ea4f5f9-7732-4376-9a46-0bc72168b224', 'ECOM-CART-001', 'Módulo de Carrito', 'Carrito de compras con cálculo de totales y sesión persistente', 'c63ea06c-a6f9-41ec-abdd-48f1f2821e86', 'CODIGO', NULL, '5d5c77fd-34eb-4fbf-9be6-d9a3014cb337', 'BORRADOR', '2025-11-14 05:48:03', '2025-11-14 05:48:03'),
	('4163b058-0660-4949-a69d-8f16e1c42b10', 'ERP-DOC-001', 'Especificación de Requisitos del Sistema (SRS)', 'Documento IEEE 830 con requisitos funcionales y no funcionales del ERP', '1970240a-41aa-4e90-bab7-8dea24cd3de5', 'DOCUMENTO', NULL, 'b0ae25ef-b19d-412e-a628-5a49d084b82d', 'APROBADO', '2025-11-14 05:48:03', '2025-11-14 05:48:03'),
	('4630f9cc-961c-42f8-a615-1aef8dd3b5e1', 'ERP-CODE-004', 'Módulo de Inventario', 'Control de stock, kardex y valoración de inventarios', '1970240a-41aa-4e90-bab7-8dea24cd3de5', 'CODIGO', NULL, 'b0ae25ef-b19d-412e-a628-5a49d084b82d', 'LIBERADO', '2025-11-14 05:48:03', '2025-11-14 05:48:03'),
	('4c6cef27-aa5c-4242-b900-6802f62d1c85', 'ECOM-DOC-002', 'Sprint Backlog - Sprint 1', 'Planificación del primer sprint con 8 historias de usuario', 'c63ea06c-a6f9-41ec-abdd-48f1f2821e86', 'DOCUMENTO', NULL, '85ad1d9a-d33c-45c5-a652-ae34e08de3b9', 'EN_REVISION', '2025-11-14 05:48:02', '2025-11-14 05:48:02'),
	('4f0bbaba-6dc8-439e-89c7-f798262c2061', 'ERP-DOC-008', 'Plan de Despliegue a Producción', 'Procedimientos de instalación, configuración y rollback', '1970240a-41aa-4e90-bab7-8dea24cd3de5', 'DOCUMENTO', NULL, 'b0ae25ef-b19d-412e-a628-5a49d084b82d', 'BORRADOR', '2025-11-14 05:48:03', '2025-11-14 05:48:03'),
	('4f53c600-b521-4525-94ee-e77b7dcac4af', 'ECOM-ANALYTICS-001', 'Dashboard de Analytics', 'Reportes y métricas de ventas, productos y usuarios', 'c63ea06c-a6f9-41ec-abdd-48f1f2821e86', 'CODIGO', NULL, '5d5c77fd-34eb-4fbf-9be6-d9a3014cb337', 'PENDIENTE', '2025-11-14 05:48:03', '2025-11-14 05:48:03'),
	('4f6a672f-d873-4ea4-8212-81f7de4f90f5', 'ERP-DOC-002', 'Plan de Gestión del Proyecto', 'Plan maestro según PMBOK con cronograma, presupuesto y recursos', '1970240a-41aa-4e90-bab7-8dea24cd3de5', 'DOCUMENTO', NULL, 'b0ae25ef-b19d-412e-a628-5a49d084b82d', 'APROBADO', '2025-11-14 05:48:03', '2025-11-14 05:48:03'),
	('52b2de14-ab71-40b3-ae51-263653d356a3', 'ERP-DOC-003', 'Documento de Arquitectura de Software (SAD)', 'Arquitectura en capas con patrones MVC, Repository y Factory', '1970240a-41aa-4e90-bab7-8dea24cd3de5', 'DOCUMENTO', NULL, 'b0ae25ef-b19d-412e-a628-5a49d084b82d', 'APROBADO', '2025-11-14 05:48:03', '2025-11-14 05:48:03'),
	('5a89597e-a77b-4634-aa83-a53dce364aba', 'ECOM-CODE-002', 'Módulo de Autenticación JWT', 'Sistema de autenticación con tokens JWT y refresh tokens', 'c63ea06c-a6f9-41ec-abdd-48f1f2821e86', 'CODIGO', NULL, '85ad1d9a-d33c-45c5-a652-ae34e08de3b9', 'LIBERADO', '2025-11-14 05:48:02', '2025-11-14 05:48:02'),
	('697be326-9623-4f6a-a526-a0be2d14e3dc', 'ECOM-DB-001', 'Esquema de Base de Datos', 'Scripts de migración y modelo de datos del e-commerce', 'c63ea06c-a6f9-41ec-abdd-48f1f2821e86', 'SCRIPT_BD', NULL, '85ad1d9a-d33c-45c5-a652-ae34e08de3b9', 'APROBADO', '2025-11-14 05:48:02', '2025-11-14 05:48:02'),
	('7f105513-1722-43a1-9c78-72015ebc1f87', 'ECOM-PAY-001', 'Integración de Pagos', 'Integración con Stripe para procesamiento de pagos', 'c63ea06c-a6f9-41ec-abdd-48f1f2821e86', 'CODIGO', NULL, '5d5c77fd-34eb-4fbf-9be6-d9a3014cb337', 'EN_REVISION', '2025-11-14 05:48:03', '2025-11-14 05:48:03'),
	('825a27e1-82d9-4171-a688-32ac4d1020b9', 'ERP-CODE-006', 'Módulo de Ventas', 'Gestión de clientes, cotizaciones, pedidos y facturación', '1970240a-41aa-4e90-bab7-8dea24cd3de5', 'CODIGO', NULL, 'b0ae25ef-b19d-412e-a628-5a49d084b82d', 'EN_REVISION', '2025-11-14 05:48:03', '2025-11-14 05:48:03'),
	('867e13ea-d618-4780-a9bc-feb5e0688bdf', 'ECOM-ORD-001', 'Módulo de Órdenes', 'Gestión completa de órdenes de compra y estados', 'c63ea06c-a6f9-41ec-abdd-48f1f2821e86', 'CODIGO', NULL, '5d5c77fd-34eb-4fbf-9be6-d9a3014cb337', 'PENDIENTE', '2025-11-14 05:48:03', '2025-11-14 05:48:03'),
	('87eaf3b3-5822-41e3-9c76-439e157b1bad', 'ECOM-CODE-005', 'Integración Pasarela de Pagos', 'Integración con Stripe y PayPal para procesamiento de pagos', 'c63ea06c-a6f9-41ec-abdd-48f1f2821e86', 'CODIGO', NULL, '85ad1d9a-d33c-45c5-a652-ae34e08de3b9', 'PENDIENTE', '2025-11-14 05:48:02', '2025-11-14 05:48:02'),
	('915c2ee7-7d9b-43ac-97ef-847c5f53da42', 'ERP-CODE-005', 'Módulo de Compras', 'Gestión de proveedores, órdenes de compra y recepciones', '1970240a-41aa-4e90-bab7-8dea24cd3de5', 'CODIGO', NULL, 'b0ae25ef-b19d-412e-a628-5a49d084b82d', 'APROBADO', '2025-11-14 05:48:03', '2025-11-14 05:48:03'),
	('a4558278-1560-4845-8a7d-bad028bab3e9', 'ECOM-DOC-001', 'Product Backlog', 'Repositorio central de historias de usuario y features del producto', 'c63ea06c-a6f9-41ec-abdd-48f1f2821e86', 'DOCUMENTO', NULL, '85ad1d9a-d33c-45c5-a652-ae34e08de3b9', 'APROBADO', '2025-11-14 05:48:02', '2025-11-14 05:48:02'),
	('ab4bcd6f-e8d3-4006-9fe8-c79f4311ff1d', 'ERP-DOC-005', 'Plan Maestro de Pruebas', 'Estrategia de testing con casos de prueba funcionales y de integración', '1970240a-41aa-4e90-bab7-8dea24cd3de5', 'DOCUMENTO', NULL, 'b0ae25ef-b19d-412e-a628-5a49d084b82d', 'APROBADO', '2025-11-14 05:48:03', '2025-11-14 05:48:03'),
	('b124fb1e-29d9-4cda-aee2-9e12786bd335', 'ECOM-CONFIG-001', 'Pipeline CI/CD', 'Configuración de GitHub Actions para integración y despliegue continuo', 'c63ea06c-a6f9-41ec-abdd-48f1f2821e86', 'CONFIGURACION', NULL, '85ad1d9a-d33c-45c5-a652-ae34e08de3b9', 'APROBADO', '2025-11-14 05:48:02', '2025-11-14 05:48:02'),
	('bcff756b-3220-4784-8f08-27cbf34b2297', 'ECOM-DOC-004', 'Definition of Done (DoD)', 'Criterios de aceptación y estándares de calidad del equipo', 'c63ea06c-a6f9-41ec-abdd-48f1f2821e86', 'DOCUMENTO', NULL, '85ad1d9a-d33c-45c5-a652-ae34e08de3b9', 'APROBADO', '2025-11-14 05:48:02', '2025-11-14 05:48:02'),
	('c7cd79dd-8930-4d61-aac7-2f74b7423bb9', 'ERP-CODE-003', 'Módulo de Recursos Humanos', 'Gestión de empleados, nómina y evaluaciones de desempeño', '1970240a-41aa-4e90-bab7-8dea24cd3de5', 'CODIGO', NULL, 'b0ae25ef-b19d-412e-a628-5a49d084b82d', 'APROBADO', '2025-11-14 05:48:03', '2025-11-14 05:48:03'),
	('d31784cf-c936-464c-af73-14a675657bd0', 'ECOM-CODE-004', 'Módulo de Carrito de Compras', 'Sistema de carrito con persistencia y cálculo de totales', 'c63ea06c-a6f9-41ec-abdd-48f1f2821e86', 'CODIGO', NULL, '85ad1d9a-d33c-45c5-a652-ae34e08de3b9', 'EN_REVISION', '2025-11-14 05:48:02', '2025-11-14 05:48:02'),
	('d4e57bb3-0ed2-465c-9b92-8b792aa5c209', 'ERP-DOC-006', 'Suite de Casos de Prueba', '350+ casos de prueba categorizados por módulo y prioridad', '1970240a-41aa-4e90-bab7-8dea24cd3de5', 'DOCUMENTO', NULL, 'b0ae25ef-b19d-412e-a628-5a49d084b82d', 'EN_REVISION', '2025-11-14 05:48:03', '2025-11-14 05:48:03'),
	('df043ad7-7d2a-4331-b7a7-e191233915f3', 'ECOM-CODE-001', 'Repositorio Git - E-Commerce', 'Código fuente del proyecto con branches main, develop y features', 'c63ea06c-a6f9-41ec-abdd-48f1f2821e86', 'CODIGO', NULL, '85ad1d9a-d33c-45c5-a652-ae34e08de3b9', 'APROBADO', '2025-11-14 05:48:02', '2025-11-14 05:48:02'),
	('e310fd11-a71a-44db-bd34-d437ae502a49', 'ECOM-DOC-003', 'Documentación API REST', 'Especificación OpenAPI 3.0 de endpoints del backend', 'c63ea06c-a6f9-41ec-abdd-48f1f2821e86', 'DOCUMENTO', NULL, '85ad1d9a-d33c-45c5-a652-ae34e08de3b9', 'EN_REVISION', '2025-11-14 05:48:02', '2025-11-14 05:48:02'),
	('e7e9f9ec-8792-4f1a-8731-3a9dc7a061d8', 'ECOM-PROD-001', 'Módulo de Productos', 'CRUD completo de productos con categorías e inventario', 'c63ea06c-a6f9-41ec-abdd-48f1f2821e86', 'CODIGO', NULL, '5d5c77fd-34eb-4fbf-9be6-d9a3014cb337', 'APROBADO', '2025-11-14 05:48:03', '2025-11-14 05:48:03'),
	('fde3653c-4e37-4a15-bc99-cdca56c47cb3', 'ECOM-CODE-006', 'Suite de Tests Automatizados', 'Tests unitarios y de integración con PHPUnit y Jest', 'c63ea06c-a6f9-41ec-abdd-48f1f2821e86', 'CODIGO', NULL, '85ad1d9a-d33c-45c5-a652-ae34e08de3b9', 'EN_REVISION', '2025-11-14 05:48:02', '2025-11-14 05:48:02');

-- Volcando estructura para tabla sgcs.equipos
CREATE TABLE IF NOT EXISTS `equipos` (
  `id` char(36) NOT NULL,
  `proyecto_id` char(36) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `lider_id` char(36) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `equipos_proyecto_id_foreign` (`proyecto_id`),
  KEY `equipos_lider_id_foreign` (`lider_id`),
  CONSTRAINT `equipos_lider_id_foreign` FOREIGN KEY (`lider_id`) REFERENCES `usuarios` (`id`),
  CONSTRAINT `equipos_proyecto_id_foreign` FOREIGN KEY (`proyecto_id`) REFERENCES `proyectos` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla sgcs.equipos: ~2 rows (aproximadamente)
INSERT INTO `equipos` (`id`, `proyecto_id`, `nombre`, `lider_id`) VALUES
	('95856be5-2993-4304-b78f-8ed8682e5dc3', 'c63ea06c-a6f9-41ec-abdd-48f1f2821e86', 'E-Commerce Development Team', '5d5c77fd-34eb-4fbf-9be6-d9a3014cb337'),
	('97e5807a-2a24-4dfd-9262-f5a93c33fc71', '1970240a-41aa-4e90-bab7-8dea24cd3de5', 'ERP Implementation Team', '5d5c77fd-34eb-4fbf-9be6-d9a3014cb337');

-- Volcando estructura para tabla sgcs.fases_metodologia
CREATE TABLE IF NOT EXISTS `fases_metodologia` (
  `id_fase` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_metodologia` bigint(20) unsigned NOT NULL,
  `nombre_fase` varchar(100) NOT NULL,
  `orden` int(11) DEFAULT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_fase`),
  KEY `fases_metodologia_id_metodologia_foreign` (`id_metodologia`),
  CONSTRAINT `fases_metodologia_id_metodologia_foreign` FOREIGN KEY (`id_metodologia`) REFERENCES `metodologias` (`id_metodologia`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla sgcs.fases_metodologia: ~12 rows (aproximadamente)
INSERT INTO `fases_metodologia` (`id_fase`, `id_metodologia`, `nombre_fase`, `orden`, `descripcion`) VALUES
	(1, 1, 'Product Backlog', 1, 'Repositorio de historias de usuario pendientes'),
	(2, 1, 'Sprint Planning', 2, 'Planificación y selección de historias para el sprint'),
	(3, 1, 'In Progress', 3, 'Tareas en desarrollo activo durante el sprint'),
	(4, 1, 'In Review', 4, 'Revisión de código y validación de criterios de aceptación'),
	(5, 1, 'Done', 5, 'Historias completadas y aceptadas'),
	(6, 2, 'Requisitos', 1, 'Recolección y definición de requerimientos del sistema'),
	(7, 2, 'Análisis', 2, 'Análisis detallado de requisitos y especificaciones'),
	(8, 2, 'Diseño', 3, 'Diseño arquitectónico y detallado del sistema'),
	(9, 2, 'Implementación', 4, 'Codificación y desarrollo del sistema'),
	(10, 2, 'Pruebas', 5, 'Testing, validación y verificación del sistema'),
	(11, 2, 'Despliegue', 6, 'Implementación y puesta en producción'),
	(12, 2, 'Mantenimiento', 7, 'Soporte y mantenimiento post-despliegue');

-- Volcando estructura para tabla sgcs.historial_ajustes_tareas
CREATE TABLE IF NOT EXISTS `historial_ajustes_tareas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ajuste_id` bigint(20) unsigned NOT NULL,
  `tarea_id` bigint(20) unsigned NOT NULL,
  `fecha_inicio_anterior` date DEFAULT NULL,
  `fecha_fin_anterior` date DEFAULT NULL,
  `duracion_anterior` int(11) DEFAULT NULL,
  `responsable_anterior` char(36) DEFAULT NULL,
  `horas_estimadas_anterior` decimal(8,2) DEFAULT NULL,
  `fecha_inicio_nueva` date DEFAULT NULL,
  `fecha_fin_nueva` date DEFAULT NULL,
  `duracion_nueva` int(11) DEFAULT NULL,
  `responsable_nuevo` char(36) DEFAULT NULL,
  `horas_estimadas_nueva` decimal(8,2) DEFAULT NULL,
  `tipo_cambio` varchar(50) NOT NULL,
  `impacto_estimado` text DEFAULT NULL,
  `aplicado` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `historial_ajustes_tareas_responsable_anterior_foreign` (`responsable_anterior`),
  KEY `historial_ajustes_tareas_responsable_nuevo_foreign` (`responsable_nuevo`),
  KEY `historial_ajustes_tareas_ajuste_id_index` (`ajuste_id`),
  KEY `historial_ajustes_tareas_tarea_id_index` (`tarea_id`),
  CONSTRAINT `historial_ajustes_tareas_ajuste_id_foreign` FOREIGN KEY (`ajuste_id`) REFERENCES `ajustes_cronograma` (`id`) ON DELETE CASCADE,
  CONSTRAINT `historial_ajustes_tareas_responsable_anterior_foreign` FOREIGN KEY (`responsable_anterior`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL,
  CONSTRAINT `historial_ajustes_tareas_responsable_nuevo_foreign` FOREIGN KEY (`responsable_nuevo`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL,
  CONSTRAINT `historial_ajustes_tareas_tarea_id_foreign` FOREIGN KEY (`tarea_id`) REFERENCES `tareas_proyecto` (`id_tarea`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla sgcs.historial_ajustes_tareas: ~0 rows (aproximadamente)

-- Volcando estructura para tabla sgcs.impedimentos
CREATE TABLE IF NOT EXISTS `impedimentos` (
  `id_impedimento` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_proyecto` char(36) NOT NULL,
  `id_sprint` bigint(20) unsigned DEFAULT NULL,
  `id_usuario_reporta` char(36) NOT NULL,
  `id_usuario_asignado` char(36) DEFAULT NULL,
  `titulo` varchar(255) NOT NULL,
  `descripcion` text NOT NULL,
  `prioridad` enum('baja','media','alta','critica') NOT NULL DEFAULT 'media',
  `estado` enum('abierto','en_progreso','resuelto','cerrado') NOT NULL DEFAULT 'abierto',
  `fecha_reporte` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_resolucion` timestamp NULL DEFAULT NULL,
  `solucion` text DEFAULT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `actualizado_en` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_impedimento`),
  KEY `impedimentos_id_proyecto_foreign` (`id_proyecto`),
  KEY `impedimentos_id_usuario_reporta_foreign` (`id_usuario_reporta`),
  KEY `impedimentos_id_usuario_asignado_foreign` (`id_usuario_asignado`),
  KEY `impedimentos_id_sprint_foreign` (`id_sprint`),
  CONSTRAINT `impedimentos_id_proyecto_foreign` FOREIGN KEY (`id_proyecto`) REFERENCES `proyectos` (`id`),
  CONSTRAINT `impedimentos_id_sprint_foreign` FOREIGN KEY (`id_sprint`) REFERENCES `sprints` (`id_sprint`) ON DELETE SET NULL,
  CONSTRAINT `impedimentos_id_usuario_asignado_foreign` FOREIGN KEY (`id_usuario_asignado`) REFERENCES `usuarios` (`id`),
  CONSTRAINT `impedimentos_id_usuario_reporta_foreign` FOREIGN KEY (`id_usuario_reporta`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla sgcs.impedimentos: ~0 rows (aproximadamente)

-- Volcando estructura para tabla sgcs.items_cambio
CREATE TABLE IF NOT EXISTS `items_cambio` (
  `id` char(36) NOT NULL,
  `solicitud_cambio_id` char(36) NOT NULL,
  `ec_id` char(36) NOT NULL,
  `version_actual_ec_id` char(36) DEFAULT NULL,
  `version_propuesta` varchar(50) DEFAULT NULL,
  `nota` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `items_cambio_solicitud_cambio_id_foreign` (`solicitud_cambio_id`),
  KEY `items_cambio_ec_id_foreign` (`ec_id`),
  KEY `items_cambio_version_actual_ec_id_foreign` (`version_actual_ec_id`),
  CONSTRAINT `items_cambio_ec_id_foreign` FOREIGN KEY (`ec_id`) REFERENCES `elementos_configuracion` (`id`),
  CONSTRAINT `items_cambio_solicitud_cambio_id_foreign` FOREIGN KEY (`solicitud_cambio_id`) REFERENCES `solicitudes_cambio` (`id`) ON DELETE CASCADE,
  CONSTRAINT `items_cambio_version_actual_ec_id_foreign` FOREIGN KEY (`version_actual_ec_id`) REFERENCES `versiones_ec` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla sgcs.items_cambio: ~0 rows (aproximadamente)

-- Volcando estructura para tabla sgcs.items_liberacion
CREATE TABLE IF NOT EXISTS `items_liberacion` (
  `id` char(36) NOT NULL,
  `liberacion_id` char(36) NOT NULL,
  `ec_id` char(36) DEFAULT NULL,
  `version_ec_id` char(36) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `items_liberacion_liberacion_id_foreign` (`liberacion_id`),
  KEY `items_liberacion_ec_id_foreign` (`ec_id`),
  KEY `items_liberacion_version_ec_id_foreign` (`version_ec_id`),
  CONSTRAINT `items_liberacion_ec_id_foreign` FOREIGN KEY (`ec_id`) REFERENCES `elementos_configuracion` (`id`),
  CONSTRAINT `items_liberacion_liberacion_id_foreign` FOREIGN KEY (`liberacion_id`) REFERENCES `liberaciones` (`id`) ON DELETE CASCADE,
  CONSTRAINT `items_liberacion_version_ec_id_foreign` FOREIGN KEY (`version_ec_id`) REFERENCES `versiones_ec` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla sgcs.items_liberacion: ~0 rows (aproximadamente)

-- Volcando estructura para tabla sgcs.jobs
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla sgcs.jobs: ~0 rows (aproximadamente)

-- Volcando estructura para tabla sgcs.liberaciones
CREATE TABLE IF NOT EXISTS `liberaciones` (
  `id` char(36) NOT NULL,
  `proyecto_id` char(36) NOT NULL,
  `etiqueta` varchar(50) NOT NULL,
  `nombre` varchar(255) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha_liberacion` date DEFAULT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `liberaciones_proyecto_id_foreign` (`proyecto_id`),
  CONSTRAINT `liberaciones_proyecto_id_foreign` FOREIGN KEY (`proyecto_id`) REFERENCES `proyectos` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla sgcs.liberaciones: ~0 rows (aproximadamente)

-- Volcando estructura para tabla sgcs.metodologias
CREATE TABLE IF NOT EXISTS `metodologias` (
  `id_metodologia` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `tipo` varchar(50) DEFAULT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_metodologia`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla sgcs.metodologias: ~2 rows (aproximadamente)
INSERT INTO `metodologias` (`id_metodologia`, `nombre`, `tipo`, `descripcion`) VALUES
	(1, 'Scrum', 'ágil', 'Framework ágil basado en sprints, roles definidos y entregas iterativas'),
	(2, 'Cascada', 'secuencial', 'Metodología tradicional con fases secuenciales y entregables por etapa');

-- Volcando estructura para tabla sgcs.miembros_ccb
CREATE TABLE IF NOT EXISTS `miembros_ccb` (
  `ccb_id` char(36) NOT NULL,
  `usuario_id` char(36) NOT NULL,
  `rol_en_ccb` varchar(100) NOT NULL DEFAULT 'Miembro',
  PRIMARY KEY (`ccb_id`,`usuario_id`),
  KEY `miembros_ccb_usuario_id_foreign` (`usuario_id`),
  CONSTRAINT `miembros_ccb_ccb_id_foreign` FOREIGN KEY (`ccb_id`) REFERENCES `comite_cambios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `miembros_ccb_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla sgcs.miembros_ccb: ~0 rows (aproximadamente)

-- Volcando estructura para tabla sgcs.miembros_equipo
CREATE TABLE IF NOT EXISTS `miembros_equipo` (
  `equipo_id` char(36) NOT NULL,
  `usuario_id` char(36) NOT NULL,
  `rol_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`equipo_id`,`usuario_id`,`rol_id`),
  KEY `miembros_equipo_usuario_id_foreign` (`usuario_id`),
  KEY `miembros_equipo_rol_id_foreign` (`rol_id`),
  CONSTRAINT `miembros_equipo_equipo_id_foreign` FOREIGN KEY (`equipo_id`) REFERENCES `equipos` (`id`),
  CONSTRAINT `miembros_equipo_rol_id_foreign` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`),
  CONSTRAINT `miembros_equipo_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla sgcs.miembros_equipo: ~16 rows (aproximadamente)
INSERT INTO `miembros_equipo` (`equipo_id`, `usuario_id`, `rol_id`) VALUES
	('95856be5-2993-4304-b78f-8ed8682e5dc3', '5d5c77fd-34eb-4fbf-9be6-d9a3014cb337', 5),
	('95856be5-2993-4304-b78f-8ed8682e5dc3', '848b794e-0a54-4dc5-b2fc-7b5618d54140', 13),
	('95856be5-2993-4304-b78f-8ed8682e5dc3', '85ad1d9a-d33c-45c5-a652-ae34e08de3b9', 5),
	('95856be5-2993-4304-b78f-8ed8682e5dc3', '962af55f-6ef5-4f08-8813-df0fd1c29519', 6),
	('95856be5-2993-4304-b78f-8ed8682e5dc3', '9db28213-895e-4590-b696-f59cf29f0832', 12),
	('95856be5-2993-4304-b78f-8ed8682e5dc3', 'b1daa777-d9c5-4831-9b65-5ced9f65b73f', 14),
	('95856be5-2993-4304-b78f-8ed8682e5dc3', 'df805d5f-6f5f-430b-b196-5faec7ca72a9', 13),
	('97e5807a-2a24-4dfd-9262-f5a93c33fc71', '5d5c77fd-34eb-4fbf-9be6-d9a3014cb337', 9),
	('97e5807a-2a24-4dfd-9262-f5a93c33fc71', '848b794e-0a54-4dc5-b2fc-7b5618d54140', 13),
	('97e5807a-2a24-4dfd-9262-f5a93c33fc71', '949be9b3-b065-4e69-899a-6e3f2fac8fc9', 15),
	('97e5807a-2a24-4dfd-9262-f5a93c33fc71', '9db28213-895e-4590-b696-f59cf29f0832', 12),
	('97e5807a-2a24-4dfd-9262-f5a93c33fc71', 'b0ae25ef-b19d-412e-a628-5a49d084b82d', 9),
	('97e5807a-2a24-4dfd-9262-f5a93c33fc71', 'b1daa777-d9c5-4831-9b65-5ced9f65b73f', 14),
	('97e5807a-2a24-4dfd-9262-f5a93c33fc71', 'bb4c897e-6d7f-449c-b81a-7e5feceb9ca3', 5),
	('97e5807a-2a24-4dfd-9262-f5a93c33fc71', 'df805d5f-6f5f-430b-b196-5faec7ca72a9', 13),
	('97e5807a-2a24-4dfd-9262-f5a93c33fc71', 'e0a3c802-dc32-4397-a9dd-1320face8c3a', 10);

-- Volcando estructura para tabla sgcs.migrations
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla sgcs.migrations: ~16 rows (aproximadamente)
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
	(1, '0001_01_01_000000_create_users_table', 1),
	(2, '2024_01_01_000001_create_roles_table', 1),
	(3, '2024_01_01_000002_create_proyectos_table', 1),
	(4, '2024_01_01_000002a_create_roles_table', 1),
	(5, '2024_01_01_000003_create_elementos_configuracion_table', 1),
	(6, '2024_01_01_000004_create_liberaciones_table', 1),
	(7, '2024_01_01_000005_create_solicitudes_cambio_table', 1),
	(8, '2024_01_01_000006_create_auditorias_table', 1),
	(9, '2025_10_30_000001_create_ajustes_cronograma_table', 1),
	(10, '2025_10_30_000002_create_historial_ajustes_tareas_table', 1),
	(11, '2025_10_30_174732_create_impedimentos_table', 1),
	(12, '2025_11_04_000001_create_commits_repositorio_table', 1),
	(13, '2025_11_11_212433_create_jobs_table', 1),
	(14, '2025_11_13_000001_create_sprints_table', 1),
	(15, '2025_11_13_000002_create_daily_scrums_table', 1),
	(16, '2025_11_13_204932_create_notifications_table', 1);

-- Volcando estructura para tabla sgcs.notificaciones
CREATE TABLE IF NOT EXISTS `notificaciones` (
  `id` char(36) NOT NULL,
  `usuario_id` char(36) DEFAULT NULL,
  `tipo` varchar(50) DEFAULT NULL,
  `datos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`datos`)),
  `leida` tinyint(1) NOT NULL DEFAULT 0,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `notificaciones_usuario_id_foreign` (`usuario_id`),
  CONSTRAINT `notificaciones_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla sgcs.notificaciones: ~0 rows (aproximadamente)

-- Volcando estructura para tabla sgcs.notifications
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` char(36) NOT NULL,
  `type` varchar(255) NOT NULL,
  `notifiable_type` varchar(255) NOT NULL,
  `notifiable_id` bigint(20) unsigned NOT NULL,
  `data` text NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla sgcs.notifications: ~0 rows (aproximadamente)

-- Volcando estructura para tabla sgcs.password_reset_tokens
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla sgcs.password_reset_tokens: ~0 rows (aproximadamente)

-- Volcando estructura para tabla sgcs.plantillas_ec
CREATE TABLE IF NOT EXISTS `plantillas_ec` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `metodologia_id` bigint(20) unsigned NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `tipo` enum('DOCUMENTO','CODIGO','SCRIPT_BD','CONFIGURACION','OTRO') NOT NULL DEFAULT 'DOCUMENTO',
  `descripcion` text DEFAULT NULL,
  `orden` int(11) NOT NULL DEFAULT 1,
  `es_recomendado` tinyint(1) NOT NULL DEFAULT 1,
  `tarea_nombre` varchar(255) DEFAULT NULL,
  `tarea_descripcion` text DEFAULT NULL,
  `porcentaje_inicio` decimal(5,2) NOT NULL DEFAULT 0.00,
  `porcentaje_fin` decimal(5,2) NOT NULL DEFAULT 100.00,
  `relaciones` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`relaciones`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `plantillas_ec_metodologia_id_foreign` (`metodologia_id`),
  CONSTRAINT `plantillas_ec_metodologia_id_foreign` FOREIGN KEY (`metodologia_id`) REFERENCES `metodologias` (`id_metodologia`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla sgcs.plantillas_ec: ~14 rows (aproximadamente)
INSERT INTO `plantillas_ec` (`id`, `metodologia_id`, `nombre`, `tipo`, `descripcion`, `orden`, `es_recomendado`, `tarea_nombre`, `tarea_descripcion`, `porcentaje_inicio`, `porcentaje_fin`, `relaciones`, `created_at`, `updated_at`) VALUES
	(1, 1, 'Product Backlog', 'DOCUMENTO', 'Repositorio central de historias de usuario y requisitos del producto', 1, 1, 'Crear historias de usuario iniciales', 'Definir las primeras historias de usuario del proyecto basadas en los requisitos del Product Owner', 0.00, 20.00, NULL, '2025-11-14 05:48:00', '2025-11-14 05:48:00'),
	(2, 1, 'Sprint Backlog', 'DOCUMENTO', 'Planificación y seguimiento de sprints del proyecto', 2, 1, 'Planificar primer sprint', 'Seleccionar historias de usuario del Product Backlog y planificar el primer sprint', 20.00, 40.00, '[{"nombre":"Product Backlog","tipo":"DEPENDE_DE"}]', '2025-11-14 05:48:00', '2025-11-14 05:48:00'),
	(3, 1, 'Repositorio de Código', 'CODIGO', 'Control de versiones del código fuente del proyecto', 3, 1, 'Configurar repositorio Git', 'Inicializar repositorio Git, configurar branches (main, develop) y establecer reglas de commit', 0.00, 10.00, NULL, '2025-11-14 05:48:00', '2025-11-14 05:48:00'),
	(4, 1, 'Documentación Técnica', 'DOCUMENTO', 'Documentación del sistema, APIs y guías de desarrollo', 4, 1, 'Crear README y documentación inicial', 'Documentar configuración del proyecto, estructura de carpetas y guías para desarrolladores', 40.00, 60.00, '[{"nombre":"Repositorio de C\\u00f3digo","tipo":"REFERENCIA"}]', '2025-11-14 05:48:00', '2025-11-14 05:48:00'),
	(5, 1, 'Definition of Done (DoD)', 'DOCUMENTO', 'Criterios de aceptación y definición de "terminado" para el equipo', 5, 0, 'Definir criterios de DoD del equipo', 'Establecer los criterios que debe cumplir una historia de usuario para considerarse terminada', 0.00, 15.00, '[{"nombre":"Product Backlog","tipo":"REFERENCIA"}]', '2025-11-14 05:48:00', '2025-11-14 05:48:00'),
	(6, 1, 'Retrospectivas', 'DOCUMENTO', 'Registro de retrospectivas y acciones de mejora del equipo', 6, 0, 'Preparar template de retrospectivas', 'Crear formato estándar para documentar retrospectivas de cada sprint', 60.00, 80.00, '[{"nombre":"Sprint Backlog","tipo":"REFERENCIA"}]', '2025-11-14 05:48:00', '2025-11-14 05:48:00'),
	(7, 2, 'Documento de Requisitos (SRS)', 'DOCUMENTO', 'Especificación de requisitos del sistema (Software Requirements Specification)', 1, 1, 'Recopilar y documentar requisitos', 'Realizar entrevistas con stakeholders y documentar todos los requisitos funcionales y no funcionales', 0.00, 15.00, NULL, '2025-11-14 05:48:00', '2025-11-14 05:48:00'),
	(8, 2, 'Diseño de Arquitectura', 'DOCUMENTO', 'Diseño técnico y arquitectónico del sistema', 2, 1, 'Diseñar arquitectura del sistema', 'Crear diagramas UML, arquitectura de componentes, diseño de base de datos y especificaciones técnicas', 15.00, 30.00, '[{"nombre":"Documento de Requisitos (SRS)","tipo":"DEPENDE_DE"}]', '2025-11-14 05:48:00', '2025-11-14 05:48:00'),
	(9, 2, 'Código Fuente', 'CODIGO', 'Implementación del código fuente del sistema', 3, 1, 'Configurar estructura del proyecto', 'Inicializar proyecto, configurar dependencias y establecer estructura de carpetas según diseño', 30.00, 60.00, '[{"nombre":"Dise\\u00f1o de Arquitectura","tipo":"DEPENDE_DE"}]', '2025-11-14 05:48:00', '2025-11-14 05:48:00'),
	(10, 2, 'Plan de Pruebas', 'DOCUMENTO', 'Estrategia de testing, casos de prueba y matriz de trazabilidad', 4, 1, 'Elaborar plan de pruebas', 'Definir estrategia de testing, diseñar casos de prueba y crear matriz de trazabilidad de requisitos', 50.00, 70.00, '[{"nombre":"Documento de Requisitos (SRS)","tipo":"REFERENCIA"},{"nombre":"C\\u00f3digo Fuente","tipo":"DEPENDE_DE"}]', '2025-11-14 05:48:00', '2025-11-14 05:48:00'),
	(11, 2, 'Manual de Usuario', 'DOCUMENTO', 'Documentación para usuarios finales del sistema', 5, 1, 'Redactar manual de usuario', 'Crear guías de uso, tutoriales y documentación para usuarios finales del sistema', 70.00, 85.00, '[{"nombre":"C\\u00f3digo Fuente","tipo":"REFERENCIA"}]', '2025-11-14 05:48:00', '2025-11-14 05:48:00'),
	(12, 2, 'Scripts de Base de Datos', 'SCRIPT_BD', 'Scripts SQL de creación y migración de base de datos', 6, 1, 'Crear scripts de base de datos', 'Desarrollar scripts DDL/DML para creación de tablas, procedimientos y carga inicial de datos', 25.00, 35.00, '[{"nombre":"Dise\\u00f1o de Arquitectura","tipo":"DERIVADO_DE"}]', '2025-11-14 05:48:00', '2025-11-14 05:48:00'),
	(13, 2, 'Plan de Despliegue', 'DOCUMENTO', 'Estrategia y procedimientos para despliegue en producción', 7, 0, 'Elaborar plan de despliegue', 'Documentar procedimientos de instalación, configuración y despliegue en ambientes productivos', 85.00, 95.00, '[{"nombre":"C\\u00f3digo Fuente","tipo":"REFERENCIA"},{"nombre":"Scripts de Base de Datos","tipo":"REFERENCIA"}]', '2025-11-14 05:48:00', '2025-11-14 05:48:00'),
	(14, 2, 'Acta de Aceptación', 'DOCUMENTO', 'Documento formal de aceptación del proyecto por el cliente', 8, 0, 'Preparar acta de aceptación', 'Preparar documento formal para firma de aceptación del cliente', 95.00, 100.00, '[{"nombre":"Plan de Pruebas","tipo":"REQUERIDO_POR"},{"nombre":"Manual de Usuario","tipo":"REQUERIDO_POR"}]', '2025-11-14 05:48:00', '2025-11-14 05:48:00');

-- Volcando estructura para tabla sgcs.proyectos
CREATE TABLE IF NOT EXISTS `proyectos` (
  `id` char(36) NOT NULL,
  `codigo` varchar(50) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `id_metodologia` bigint(20) unsigned NOT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `link_repositorio` varchar(255) DEFAULT NULL,
  `creado_por` char(36) NOT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `actualizado_en` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `proyectos_codigo_unique` (`codigo`),
  KEY `proyectos_id_metodologia_foreign` (`id_metodologia`),
  KEY `proyectos_creado_por_foreign` (`creado_por`),
  CONSTRAINT `proyectos_creado_por_foreign` FOREIGN KEY (`creado_por`) REFERENCES `usuarios` (`id`),
  CONSTRAINT `proyectos_id_metodologia_foreign` FOREIGN KEY (`id_metodologia`) REFERENCES `metodologias` (`id_metodologia`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla sgcs.proyectos: ~5 rows (aproximadamente)
INSERT INTO `proyectos` (`id`, `codigo`, `nombre`, `descripcion`, `id_metodologia`, `fecha_inicio`, `fecha_fin`, `link_repositorio`, `creado_por`, `creado_en`, `actualizado_en`) VALUES
	('1970240a-41aa-4e90-bab7-8dea24cd3de5', 'ERP-2024', 'Sistema ERP Corporativo', 'Sistema integral de planificación de recursos empresariales (ERP) con módulos de contabilidad, RRHH, inventario, compras y ventas. Implementado con metodología tradicional en cascada.', 2, '2025-08-14', '2026-08-14', 'https://github.com/sgcs-demo/erp-corporativo', '5d5c77fd-34eb-4fbf-9be6-d9a3014cb337', '2025-11-14 05:48:02', '2025-11-14 05:48:02'),
	('285189fe-2caf-4c8a-ad48-76f2c26eaf1e', 'API-2024', 'API Gateway Empresarial', 'Gateway de APIs para integración de servicios', 1, '2025-10-14', '2026-02-14', 'https://github.com/sgcs-demo/api-2024', '85ad1d9a-d33c-45c5-a652-ae34e08de3b9', '2025-11-14 05:48:03', '2025-11-14 05:48:03'),
	('7e883b37-ef5a-4a43-8524-e7eab0e89eeb', 'MOB-2024', 'App Móvil Bancaria', 'Aplicación móvil para banca digital', 1, '2025-08-14', '2026-03-14', 'https://github.com/sgcs-demo/mob-2024', '962af55f-6ef5-4f08-8813-df0fd1c29519', '2025-11-14 05:48:03', '2025-11-14 05:48:03'),
	('82ad102f-7082-41d2-b68f-e602a5daa665', 'WEB-2024', 'Portal Institucional', 'Sitio web corporativo institucional', 2, '2025-09-14', '2026-05-14', 'https://github.com/sgcs-demo/web-2024', 'b0ae25ef-b19d-412e-a628-5a49d084b82d', '2025-11-14 05:48:03', '2025-11-14 05:48:03'),
	('c63ea06c-a6f9-41ec-abdd-48f1f2821e86', 'ECOM-2024', 'E-Commerce Platform', 'Plataforma de comercio electrónico con gestión de inventario, carrito de compras, pasarela de pagos y dashboard de analytics. Desarrollado con metodología ágil Scrum.', 1, '2025-09-14', '2026-03-14', 'https://github.com/sgcs-demo/ecommerce-platform', '5d5c77fd-34eb-4fbf-9be6-d9a3014cb337', '2025-11-14 05:48:02', '2025-11-14 05:48:02');

-- Volcando estructura para tabla sgcs.relaciones_ec
CREATE TABLE IF NOT EXISTS `relaciones_ec` (
  `id` char(36) NOT NULL,
  `desde_ec` char(36) NOT NULL,
  `hacia_ec` char(36) NOT NULL,
  `tipo_relacion` enum('DEPENDE_DE','DERIVADO_DE','REFERENCIA','REQUERIDO_POR') NOT NULL,
  `nota` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `relaciones_ec_desde_ec_foreign` (`desde_ec`),
  KEY `relaciones_ec_hacia_ec_foreign` (`hacia_ec`),
  CONSTRAINT `relaciones_ec_desde_ec_foreign` FOREIGN KEY (`desde_ec`) REFERENCES `elementos_configuracion` (`id`) ON DELETE CASCADE,
  CONSTRAINT `relaciones_ec_hacia_ec_foreign` FOREIGN KEY (`hacia_ec`) REFERENCES `elementos_configuracion` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla sgcs.relaciones_ec: ~22 rows (aproximadamente)
INSERT INTO `relaciones_ec` (`id`, `desde_ec`, `hacia_ec`, `tipo_relacion`, `nota`) VALUES
	('0c7aeb56-abc0-4535-995c-a1a37fe5c942', '1cfef1ba-65f9-47dc-ad1d-bef147fe82b7', '3135cff2-8ca7-434e-ab27-970c8df4bfb1', 'DERIVADO_DE', 'Los scripts implementan el modelo de datos'),
	('2d632d3a-b2e6-4f8b-b266-4f0ffd02b3a8', 'c7cd79dd-8930-4d61-aac7-2f74b7423bb9', '123b42d1-98bc-45eb-b43e-7627a8f3dac3', 'DEPENDE_DE', 'Módulo requiere el repositorio de código base'),
	('2dd49c05-091c-4860-8112-cc70b046af1e', '4630f9cc-961c-42f8-a615-1aef8dd3b5e1', '1cfef1ba-65f9-47dc-ad1d-bef147fe82b7', 'DEPENDE_DE', 'Módulo requiere la base de datos'),
	('3108656e-8285-4147-bacf-a01182ffb192', '0508cba4-60a0-489c-bf45-06195f756d3e', '5a89597e-a77b-4634-aa83-a53dce364aba', 'DEPENDE_DE', 'Gestión de productos requiere autenticación de usuarios'),
	('38d43fca-c736-4dcb-939f-04400e75ba3d', 'c7cd79dd-8930-4d61-aac7-2f74b7423bb9', '1cfef1ba-65f9-47dc-ad1d-bef147fe82b7', 'DEPENDE_DE', 'Módulo requiere la base de datos'),
	('526926b5-2efa-4c25-8def-7a19812d4729', '1d1645aa-cf51-44b6-98fe-12f550422732', '123b42d1-98bc-45eb-b43e-7627a8f3dac3', 'DEPENDE_DE', 'Módulo requiere el repositorio de código base'),
	('5d82ac33-eabb-49d1-a564-412c15e7d462', '3135cff2-8ca7-434e-ab27-970c8df4bfb1', '52b2de14-ab71-40b3-ae51-263653d356a3', 'DERIVADO_DE', 'El modelo de datos se deriva del diseño arquitectónico'),
	('6967bfa4-7d47-4b0f-a17c-ee0dbc4c9f6b', '825a27e1-82d9-4171-a688-32ac4d1020b9', '1cfef1ba-65f9-47dc-ad1d-bef147fe82b7', 'DEPENDE_DE', 'Módulo requiere la base de datos'),
	('6e666349-ee50-4b83-93fe-5d8ff885b94d', '915c2ee7-7d9b-43ac-97ef-847c5f53da42', '123b42d1-98bc-45eb-b43e-7627a8f3dac3', 'DEPENDE_DE', 'Módulo requiere el repositorio de código base'),
	('79940377-4b1b-45c6-b897-5e4a58741966', '52b2de14-ab71-40b3-ae51-263653d356a3', '4163b058-0660-4949-a69d-8f16e1c42b10', 'DERIVADO_DE', 'La arquitectura se deriva de los requisitos del SRS'),
	('9fe22783-df18-4db8-83b5-3e1900992991', 'd31784cf-c936-464c-af73-14a675657bd0', '0508cba4-60a0-489c-bf45-06195f756d3e', 'DEPENDE_DE', 'El carrito necesita el módulo de productos'),
	('a313b2cf-9bdf-474d-b12f-0fda38d17e9d', '915c2ee7-7d9b-43ac-97ef-847c5f53da42', '1cfef1ba-65f9-47dc-ad1d-bef147fe82b7', 'DEPENDE_DE', 'Módulo requiere la base de datos'),
	('a48f9470-2748-4405-9f7b-5094b7dd62e9', '825a27e1-82d9-4171-a688-32ac4d1020b9', '123b42d1-98bc-45eb-b43e-7627a8f3dac3', 'DEPENDE_DE', 'Módulo requiere el repositorio de código base'),
	('abfc4017-82ec-4f05-a617-01505d0c823f', 'fde3653c-4e37-4a15-bc99-cdca56c47cb3', 'e310fd11-a71a-44db-bd34-d437ae502a49', 'REFERENCIA', 'Los tests validan la API documentada'),
	('c4e8e609-a1d4-4868-87bf-818a44b44a7e', '4630f9cc-961c-42f8-a615-1aef8dd3b5e1', '123b42d1-98bc-45eb-b43e-7627a8f3dac3', 'DEPENDE_DE', 'Módulo requiere el repositorio de código base'),
	('cd50f44c-83e5-4337-a0da-9dd120e75a44', '4c6cef27-aa5c-4242-b900-6802f62d1c85', 'a4558278-1560-4845-8a7d-bad028bab3e9', 'DEPENDE_DE', 'El Sprint Backlog se deriva del Product Backlog'),
	('df4c2a44-d0e9-423a-8b76-9c686e62a178', '5a89597e-a77b-4634-aa83-a53dce364aba', '697be326-9623-4f6a-a526-a0be2d14e3dc', 'DEPENDE_DE', 'El módulo de autenticación requiere la base de datos'),
	('e599ac6e-af3d-4af2-9094-aaa6ddbc04e0', '825a27e1-82d9-4171-a688-32ac4d1020b9', '4630f9cc-961c-42f8-a615-1aef8dd3b5e1', 'REQUERIDO_POR', 'Las ventas requieren consultar el inventario disponible'),
	('e999d71d-1cc6-4b76-8678-0f147ddcf768', '5a89597e-a77b-4634-aa83-a53dce364aba', 'df043ad7-7d2a-4331-b7a7-e191233915f3', 'DEPENDE_DE', 'El módulo de autenticación requiere el repositorio de código'),
	('ea16f464-ddb3-4105-bd98-ff4927ab39e8', '87eaf3b3-5822-41e3-9c76-439e157b1bad', 'd31784cf-c936-464c-af73-14a675657bd0', 'DEPENDE_DE', 'Los pagos procesan items del carrito'),
	('ed2a3640-8db2-4b8e-9bbf-abdd9f515e94', '1d1645aa-cf51-44b6-98fe-12f550422732', '1cfef1ba-65f9-47dc-ad1d-bef147fe82b7', 'DEPENDE_DE', 'Módulo requiere la base de datos'),
	('fc6eabff-7e05-481b-85cd-634d8064e626', 'd4e57bb3-0ed2-465c-9b92-8b792aa5c209', 'ab4bcd6f-e8d3-4006-9fe8-c79f4311ff1d', 'DERIVADO_DE', 'Los casos de prueba implementan el plan maestro de pruebas');

-- Volcando estructura para tabla sgcs.roles
CREATE TABLE IF NOT EXISTS `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `es_para_ccb` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Indica si el rol puede ser miembro del CCB',
  `metodologia_id` bigint(20) unsigned DEFAULT NULL COMMENT 'Metodología específica del rol (null = rol genérico)',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_nombre_unique` (`nombre`),
  KEY `roles_metodologia_id_foreign` (`metodologia_id`),
  CONSTRAINT `roles_metodologia_id_foreign` FOREIGN KEY (`metodologia_id`) REFERENCES `metodologias` (`id_metodologia`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla sgcs.roles: ~20 rows (aproximadamente)
INSERT INTO `roles` (`id`, `nombre`, `descripcion`, `es_para_ccb`, `metodologia_id`, `created_at`, `updated_at`) VALUES
	(1, 'Gestor de Configuración', 'Responsable de la gestión de configuración del software (SCM Manager)', 0, NULL, NULL, NULL),
	(2, 'Administrador CCB', 'Administrador del Comité de Control de Cambios (CCB Administrator)', 0, NULL, NULL, NULL),
	(3, 'Auditor de Configuración', 'Auditor de cumplimiento de procesos de gestión de configuración', 0, NULL, NULL, NULL),
	(4, 'Release Manager', 'Gestor de liberaciones y despliegues a producción', 0, NULL, NULL, NULL),
	(5, 'Product Owner', 'Dueño del producto, define prioridades y requisitos (PO)', 0, 1, NULL, NULL),
	(6, 'Scrum Master', 'Facilitador del proceso Scrum y eliminador de impedimentos', 0, 1, NULL, NULL),
	(7, 'Desarrollador Scrum', 'Miembro del equipo de desarrollo en Scrum', 0, 1, NULL, NULL),
	(8, 'Tester Scrum', 'Responsable de pruebas en equipo Scrum', 0, 1, NULL, NULL),
	(9, 'Líder de Proyecto', 'Líder técnico y gestor del proyecto en metodología Cascada', 0, 2, NULL, NULL),
	(10, 'Arquitecto de Software', 'Evaluador técnico: analiza impacto en la arquitectura y dependencias.', 1, NULL, NULL, NULL),
	(11, 'Analista de Sistemas', 'Analista de requisitos y especificaciones', 0, 2, NULL, NULL),
	(12, 'Desarrollador Senior', 'Desarrollador con experiencia y capacidad de revisión de código', 0, 2, NULL, NULL),
	(13, 'Desarrollador', 'Desarrollador de software con acceso a elementos de configuración', 0, 2, NULL, NULL),
	(14, 'Analista QA', 'Analista de aseguramiento de calidad (Quality Assurance)', 0, 2, NULL, NULL),
	(15, 'Tester', 'Responsable de pruebas y validación de software', 0, 2, NULL, NULL),
	(16, 'Líder del Proyecto (Gestor de Configuración)', 'Presidente del CCB: convoca reuniones, valida decisiones y coordina acciones.', 1, NULL, NULL, NULL),
	(17, 'Desarrollador Senior / Líder Técnico', 'Responsable de implementación: estima esfuerzo y valida la viabilidad técnica.', 1, NULL, NULL, NULL),
	(18, 'Tester / QA', 'Asegurador de calidad: evalúa impacto en pruebas y validez tras implementación.', 1, NULL, NULL, NULL),
	(19, 'Documentador / Analista funcional', 'Encargado de trazabilidad y documentación del cambio.', 1, NULL, NULL, NULL),
	(20, 'Auditor', 'Control y seguimiento: verifica cumplimiento de procesos y políticas.', 1, NULL, NULL, NULL);

-- Volcando estructura para tabla sgcs.sessions
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(255) NOT NULL,
  `usuario_id` char(36) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_usuario_id_index` (`usuario_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla sgcs.sessions: ~0 rows (aproximadamente)

-- Volcando estructura para tabla sgcs.solicitudes_cambio
CREATE TABLE IF NOT EXISTS `solicitudes_cambio` (
  `id` char(36) NOT NULL,
  `proyecto_id` char(36) NOT NULL,
  `titulo` varchar(255) DEFAULT NULL,
  `descripcion_cambio` text DEFAULT NULL,
  `motivo_cambio` text DEFAULT NULL,
  `prioridad` enum('BAJA','MEDIA','ALTA','CRITICA') NOT NULL DEFAULT 'MEDIA',
  `estado` enum('ABIERTA','EN_REVISION','APROBADA','RECHAZADA','IMPLEMENTADA','CERRADA') NOT NULL DEFAULT 'ABIERTA',
  `solicitante_id` char(36) DEFAULT NULL,
  `origen_cambio` varchar(255) DEFAULT NULL COMMENT 'Origen del cambio: Impedimento, Solicitud Cliente, etc.',
  `resumen_impacto` text DEFAULT NULL,
  `aprobado_por` char(36) DEFAULT NULL,
  `aprobado_en` timestamp NULL DEFAULT NULL,
  `rechazado_por` char(36) DEFAULT NULL,
  `rechazado_en` timestamp NULL DEFAULT NULL,
  `motivo_rechazo` text DEFAULT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `actualizado_en` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `solicitudes_cambio_proyecto_id_foreign` (`proyecto_id`),
  KEY `solicitudes_cambio_solicitante_id_foreign` (`solicitante_id`),
  KEY `solicitudes_cambio_aprobado_por_foreign` (`aprobado_por`),
  KEY `solicitudes_cambio_rechazado_por_foreign` (`rechazado_por`),
  CONSTRAINT `solicitudes_cambio_aprobado_por_foreign` FOREIGN KEY (`aprobado_por`) REFERENCES `usuarios` (`id`),
  CONSTRAINT `solicitudes_cambio_proyecto_id_foreign` FOREIGN KEY (`proyecto_id`) REFERENCES `proyectos` (`id`),
  CONSTRAINT `solicitudes_cambio_rechazado_por_foreign` FOREIGN KEY (`rechazado_por`) REFERENCES `usuarios` (`id`),
  CONSTRAINT `solicitudes_cambio_solicitante_id_foreign` FOREIGN KEY (`solicitante_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla sgcs.solicitudes_cambio: ~0 rows (aproximadamente)

-- Volcando estructura para tabla sgcs.sprints
CREATE TABLE IF NOT EXISTS `sprints` (
  `id_sprint` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_proyecto` char(36) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `objetivo` text DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `velocidad_estimada` int(11) DEFAULT NULL COMMENT 'Story points planeados',
  `velocidad_real` int(11) DEFAULT NULL COMMENT 'Story points completados',
  `estado` enum('planificado','activo','completado','cancelado') NOT NULL DEFAULT 'planificado',
  `observaciones` text DEFAULT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `actualizado_en` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_sprint`),
  KEY `sprints_id_proyecto_estado_index` (`id_proyecto`,`estado`),
  KEY `sprints_fecha_inicio_index` (`fecha_inicio`),
  CONSTRAINT `sprints_id_proyecto_foreign` FOREIGN KEY (`id_proyecto`) REFERENCES `proyectos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla sgcs.sprints: ~9 rows (aproximadamente)
INSERT INTO `sprints` (`id_sprint`, `id_proyecto`, `nombre`, `objetivo`, `fecha_inicio`, `fecha_fin`, `velocidad_estimada`, `velocidad_real`, `estado`, `observaciones`, `creado_en`, `actualizado_en`) VALUES
	(1, '285189fe-2caf-4c8a-ad48-76f2c26eaf1e', 'Sprint 1', 'Establecer la arquitectura base y funcionalidades core del proyecto', '2025-09-15', '2025-09-29', 25, 22, 'completado', 'Sprint 1 completado exitosamente', '2025-11-14 05:48:03', '2025-11-14 05:48:03'),
	(2, '285189fe-2caf-4c8a-ad48-76f2c26eaf1e', 'Sprint 2', 'Implementar módulos principales y casos de uso prioritarios', '2025-09-29', '2025-10-13', 30, 28, 'completado', 'Sprint 2 completado exitosamente', '2025-11-14 05:48:03', '2025-11-14 05:48:03'),
	(3, '285189fe-2caf-4c8a-ad48-76f2c26eaf1e', 'Sprint 3', 'Completar integraciones, optimización y preparación para release', '2025-10-13', '2025-10-27', 35, NULL, 'activo', 'Sprint actual en progreso', '2025-11-14 05:48:03', '2025-11-14 05:48:03'),
	(4, '7e883b37-ef5a-4a43-8524-e7eab0e89eeb', 'Sprint 1', 'Establecer la arquitectura base y funcionalidades core del proyecto', '2025-09-15', '2025-09-29', 25, 22, 'completado', 'Sprint 1 completado exitosamente', '2025-11-14 05:48:03', '2025-11-14 05:48:03'),
	(5, '7e883b37-ef5a-4a43-8524-e7eab0e89eeb', 'Sprint 2', 'Implementar módulos principales y casos de uso prioritarios', '2025-09-29', '2025-10-13', 30, 28, 'completado', 'Sprint 2 completado exitosamente', '2025-11-14 05:48:03', '2025-11-14 05:48:03'),
	(6, '7e883b37-ef5a-4a43-8524-e7eab0e89eeb', 'Sprint 3', 'Completar integraciones, optimización y preparación para release', '2025-10-13', '2025-10-27', 35, NULL, 'activo', 'Sprint actual en progreso', '2025-11-14 05:48:03', '2025-11-14 05:48:03'),
	(7, 'c63ea06c-a6f9-41ec-abdd-48f1f2821e86', 'Sprint 1', 'Establecer la arquitectura base y funcionalidades core del proyecto', '2025-09-15', '2025-09-29', 26, 22, 'completado', 'Sprint 1 completado exitosamente', '2025-11-14 05:48:03', '2025-11-14 05:48:03'),
	(8, 'c63ea06c-a6f9-41ec-abdd-48f1f2821e86', 'Sprint 2', 'Implementar módulos principales y casos de uso prioritarios', '2025-09-29', '2025-10-13', 18, 28, 'completado', 'Sprint 2 completado exitosamente', '2025-11-14 05:48:03', '2025-11-14 05:48:03'),
	(9, 'c63ea06c-a6f9-41ec-abdd-48f1f2821e86', 'Sprint 3', 'Completar integraciones, optimización y preparación para release', '2025-10-13', '2025-10-27', 39, NULL, 'activo', 'Sprint actual en progreso', '2025-11-14 05:48:03', '2025-11-14 05:48:03');

-- Volcando estructura para tabla sgcs.tareas_proyecto
CREATE TABLE IF NOT EXISTS `tareas_proyecto` (
  `id_tarea` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `id_proyecto` char(36) NOT NULL,
  `id_fase` bigint(20) unsigned NOT NULL,
  `id_sprint` bigint(20) unsigned DEFAULT NULL COMMENT 'FK a sprints - Solo para Scrum',
  `id_ec` char(36) DEFAULT NULL,
  `responsable` char(36) DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `fecha_inicio_original` date DEFAULT NULL COMMENT 'Fecha original antes de ajustes automáticos',
  `fecha_fin_original` date DEFAULT NULL COMMENT 'Fecha original antes de ajustes automáticos',
  `estado` varchar(50) DEFAULT NULL,
  `prioridad` int(11) NOT NULL DEFAULT 3,
  `story_points` int(11) DEFAULT NULL COMMENT 'Solo para Scrum',
  `horas_estimadas` decimal(8,2) DEFAULT NULL COMMENT 'Más usado en Cascada',
  `entregable` varchar(255) DEFAULT NULL COMMENT 'Específico de Cascada',
  `duracion_minima` int(11) DEFAULT NULL COMMENT 'Duración mínima posible en días',
  `es_ruta_critica` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Indica si la tarea está en la ruta crítica',
  `holgura_dias` int(11) NOT NULL DEFAULT 0 COMMENT 'Días de holgura (slack) - 0 para ruta crítica',
  `puede_paralelizarse` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Indica si la tarea puede ejecutarse en paralelo con otras',
  `dependencias` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'IDs de tareas de las que depende esta tarea' CHECK (json_valid(`dependencias`)),
  `progreso_real` decimal(5,2) NOT NULL DEFAULT 0.00 COMMENT 'Porcentaje de progreso real (0-100)',
  `criterios_aceptacion` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`criterios_aceptacion`)),
  `notas` text DEFAULT NULL,
  `commit_url` text DEFAULT NULL,
  `commit_id` char(36) DEFAULT NULL,
  `creado_por` char(36) DEFAULT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `actualizado_en` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_tarea`),
  KEY `tareas_proyecto_id_proyecto_foreign` (`id_proyecto`),
  KEY `tareas_proyecto_id_fase_foreign` (`id_fase`),
  KEY `tareas_proyecto_responsable_foreign` (`responsable`),
  KEY `tareas_proyecto_creado_por_foreign` (`creado_por`),
  KEY `tareas_proyecto_es_ruta_critica_index` (`es_ruta_critica`),
  KEY `tareas_proyecto_fecha_inicio_fecha_fin_index` (`fecha_inicio`,`fecha_fin`),
  KEY `tareas_proyecto_id_sprint_index` (`id_sprint`),
  KEY `tareas_proyecto_id_ec_foreign` (`id_ec`),
  KEY `tareas_proyecto_commit_id_foreign` (`commit_id`),
  CONSTRAINT `tareas_proyecto_commit_id_foreign` FOREIGN KEY (`commit_id`) REFERENCES `commits_repositorio` (`id`) ON DELETE SET NULL,
  CONSTRAINT `tareas_proyecto_creado_por_foreign` FOREIGN KEY (`creado_por`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL,
  CONSTRAINT `tareas_proyecto_id_ec_foreign` FOREIGN KEY (`id_ec`) REFERENCES `elementos_configuracion` (`id`),
  CONSTRAINT `tareas_proyecto_id_fase_foreign` FOREIGN KEY (`id_fase`) REFERENCES `fases_metodologia` (`id_fase`),
  CONSTRAINT `tareas_proyecto_id_proyecto_foreign` FOREIGN KEY (`id_proyecto`) REFERENCES `proyectos` (`id`),
  CONSTRAINT `tareas_proyecto_id_sprint_foreign` FOREIGN KEY (`id_sprint`) REFERENCES `sprints` (`id_sprint`) ON DELETE SET NULL,
  CONSTRAINT `tareas_proyecto_responsable_foreign` FOREIGN KEY (`responsable`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla sgcs.tareas_proyecto: ~19 rows (aproximadamente)
INSERT INTO `tareas_proyecto` (`id_tarea`, `nombre`, `descripcion`, `id_proyecto`, `id_fase`, `id_sprint`, `id_ec`, `responsable`, `fecha_inicio`, `fecha_fin`, `fecha_inicio_original`, `fecha_fin_original`, `estado`, `prioridad`, `story_points`, `horas_estimadas`, `entregable`, `duracion_minima`, `es_ruta_critica`, `holgura_dias`, `puede_paralelizarse`, `dependencias`, `progreso_real`, `criterios_aceptacion`, `notas`, `commit_url`, `commit_id`, `creado_por`, `creado_en`, `actualizado_en`) VALUES
	(5, 'Recopilación de requisitos funcionales', 'Tarea: Recopilación de requisitos funcionales', '1970240a-41aa-4e90-bab7-8dea24cd3de5', 6, NULL, NULL, NULL, '2025-09-19', '2025-09-29', NULL, NULL, 'completada', 3, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, 0.00, NULL, NULL, NULL, NULL, NULL, '2025-11-14 05:48:03', '2025-11-14 05:48:03'),
	(6, 'Análisis de factibilidad técnica', 'Tarea: Análisis de factibilidad técnica', '1970240a-41aa-4e90-bab7-8dea24cd3de5', 7, NULL, NULL, NULL, '2025-09-28', '2025-10-17', NULL, NULL, 'completada', 3, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, 0.00, NULL, NULL, NULL, NULL, NULL, '2025-11-14 05:48:03', '2025-11-14 05:48:03'),
	(7, 'Diseño de arquitectura del sistema', 'Tarea: Diseño de arquitectura del sistema', '1970240a-41aa-4e90-bab7-8dea24cd3de5', 8, NULL, NULL, NULL, '2025-10-24', '2025-11-16', NULL, NULL, 'completada', 3, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, 0.00, NULL, NULL, NULL, NULL, NULL, '2025-11-14 05:48:03', '2025-11-14 05:48:03'),
	(8, 'Desarrollo módulo de Contabilidad', 'Tarea: Desarrollo módulo de Contabilidad', '1970240a-41aa-4e90-bab7-8dea24cd3de5', 9, NULL, NULL, NULL, '2025-11-07', '2025-11-22', NULL, NULL, 'en_progreso', 3, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, 0.00, NULL, NULL, NULL, NULL, NULL, '2025-11-14 05:48:03', '2025-11-14 05:48:03'),
	(9, 'Elaboración de plan de pruebas', 'Tarea: Elaboración de plan de pruebas', '1970240a-41aa-4e90-bab7-8dea24cd3de5', 10, NULL, NULL, NULL, '2025-09-25', '2025-10-10', NULL, NULL, 'completada', 3, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, 0.00, NULL, NULL, NULL, NULL, NULL, '2025-11-14 05:48:03', '2025-11-14 05:48:03'),
	(10, 'Preparación de ambiente de producción', 'Tarea: Preparación de ambiente de producción', '1970240a-41aa-4e90-bab7-8dea24cd3de5', 11, NULL, NULL, NULL, '2025-10-25', '2025-11-11', NULL, NULL, 'pendiente', 3, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, 0.00, NULL, NULL, NULL, NULL, NULL, '2025-11-14 05:48:03', '2025-11-14 05:48:03'),
	(11, 'Planificación de soporte post-lanzamiento', 'Tarea: Planificación de soporte post-lanzamiento', '1970240a-41aa-4e90-bab7-8dea24cd3de5', 12, NULL, NULL, NULL, '2025-10-12', '2025-10-29', NULL, NULL, 'pendiente', 3, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, 0.00, NULL, NULL, NULL, NULL, NULL, '2025-11-14 05:48:03', '2025-11-14 05:48:03'),
	(12, 'US-001: Implementar registro de usuarios', 'Como usuario nuevo, quiero registrarme en la plataforma para poder crear una cuenta.', 'c63ea06c-a6f9-41ec-abdd-48f1f2821e86', 5, 7, '105cd5b5-6255-4067-bd0a-30366b3ec45e', '9db28213-895e-4590-b696-f59cf29f0832', '2025-09-25', '2025-10-08', NULL, NULL, 'Done', 9, 5, NULL, NULL, NULL, 0, 0, 0, NULL, 0.00, NULL, NULL, NULL, NULL, '5d5c77fd-34eb-4fbf-9be6-d9a3014cb337', '2025-11-14 05:48:03', '2025-11-14 05:48:03'),
	(13, 'US-002: Implementar login con JWT', 'Como usuario registrado, quiero iniciar sesión con mi email y contraseña.', 'c63ea06c-a6f9-41ec-abdd-48f1f2821e86', 5, 7, '105cd5b5-6255-4067-bd0a-30366b3ec45e', '848b794e-0a54-4dc5-b2fc-7b5618d54140', '2025-09-25', '2025-10-08', NULL, NULL, 'Done', 9, 8, NULL, NULL, NULL, 0, 0, 0, NULL, 0.00, NULL, NULL, NULL, NULL, '5d5c77fd-34eb-4fbf-9be6-d9a3014cb337', '2025-11-14 05:48:03', '2025-11-14 05:48:03'),
	(14, 'US-003: Crear CRUD de productos', 'Como administrador, quiero gestionar productos (crear, leer, actualizar, eliminar).', 'c63ea06c-a6f9-41ec-abdd-48f1f2821e86', 5, 7, 'e7e9f9ec-8792-4f1a-8731-3a9dc7a061d8', 'df805d5f-6f5f-430b-b196-5faec7ca72a9', '2025-09-25', '2025-10-08', NULL, NULL, 'Done', 8, 13, NULL, NULL, NULL, 0, 0, 0, NULL, 0.00, NULL, NULL, NULL, NULL, '5d5c77fd-34eb-4fbf-9be6-d9a3014cb337', '2025-11-14 05:48:03', '2025-11-14 05:48:03'),
	(15, 'US-004: Implementar carrito de compras', 'Como cliente, quiero agregar productos a mi carrito para poder comprarlos.', 'c63ea06c-a6f9-41ec-abdd-48f1f2821e86', 5, 8, '3ea4f5f9-7732-4376-9a46-0bc72168b224', '9db28213-895e-4590-b696-f59cf29f0832', '2025-10-09', '2025-10-22', NULL, NULL, 'Done', 8, 13, NULL, NULL, NULL, 0, 0, 0, NULL, 0.00, NULL, NULL, NULL, NULL, '5d5c77fd-34eb-4fbf-9be6-d9a3014cb337', '2025-11-14 05:48:03', '2025-11-14 05:48:03'),
	(16, 'US-005: Calcular totales del carrito', 'Como cliente, quiero ver el total de mi carrito incluyendo impuestos y envío.', 'c63ea06c-a6f9-41ec-abdd-48f1f2821e86', 5, 8, '3ea4f5f9-7732-4376-9a46-0bc72168b224', '848b794e-0a54-4dc5-b2fc-7b5618d54140', '2025-10-09', '2025-10-22', NULL, NULL, 'Done', 7, 5, NULL, NULL, NULL, 0, 0, 0, NULL, 0.00, NULL, NULL, NULL, NULL, '5d5c77fd-34eb-4fbf-9be6-d9a3014cb337', '2025-11-14 05:48:03', '2025-11-14 05:48:03'),
	(17, 'US-006: Integrar pasarela de pagos Stripe', 'Como cliente, quiero pagar con tarjeta de crédito usando Stripe.', 'c63ea06c-a6f9-41ec-abdd-48f1f2821e86', 4, 9, '7f105513-1722-43a1-9c78-72015ebc1f87', 'df805d5f-6f5f-430b-b196-5faec7ca72a9', '2025-11-04', '2025-11-18', NULL, NULL, 'In Review', 9, 13, NULL, NULL, NULL, 0, 0, 0, NULL, 0.00, NULL, NULL, NULL, NULL, '5d5c77fd-34eb-4fbf-9be6-d9a3014cb337', '2025-11-14 05:48:03', '2025-11-14 05:48:03'),
	(18, 'US-007: Crear dashboard de analytics', 'Como administrador, quiero ver reportes de ventas y métricas del negocio.', 'c63ea06c-a6f9-41ec-abdd-48f1f2821e86', 3, 9, '4f53c600-b521-4525-94ee-e77b7dcac4af', '9db28213-895e-4590-b696-f59cf29f0832', '2025-11-04', '2025-11-18', NULL, NULL, 'In Progress', 6, 8, NULL, NULL, NULL, 0, 0, 0, NULL, 0.00, NULL, NULL, NULL, NULL, '5d5c77fd-34eb-4fbf-9be6-d9a3014cb337', '2025-11-14 05:48:03', '2025-11-14 05:48:03'),
	(19, 'US-008: Implementar gestión de órdenes', 'Como administrador, quiero ver y gestionar todas las órdenes de compra.', 'c63ea06c-a6f9-41ec-abdd-48f1f2821e86', 3, 9, '867e13ea-d618-4780-a9bc-feb5e0688bdf', '848b794e-0a54-4dc5-b2fc-7b5618d54140', '2025-11-04', '2025-11-18', NULL, NULL, 'In Progress', 7, 13, NULL, NULL, NULL, 0, 0, 0, NULL, 0.00, NULL, NULL, NULL, NULL, '5d5c77fd-34eb-4fbf-9be6-d9a3014cb337', '2025-11-14 05:48:03', '2025-11-14 05:48:03'),
	(20, 'US-009: Implementar filtros de productos', 'Como cliente, quiero filtrar productos por categoría, precio y disponibilidad.', 'c63ea06c-a6f9-41ec-abdd-48f1f2821e86', 3, 9, 'e7e9f9ec-8792-4f1a-8731-3a9dc7a061d8', 'df805d5f-6f5f-430b-b196-5faec7ca72a9', '2025-11-04', '2025-11-18', NULL, NULL, 'In Progress', 5, 5, NULL, NULL, NULL, 0, 0, 0, NULL, 0.00, NULL, NULL, NULL, NULL, '5d5c77fd-34eb-4fbf-9be6-d9a3014cb337', '2025-11-14 05:48:03', '2025-11-14 05:48:03'),
	(21, 'US-010: Implementar wishlist', 'Como cliente, quiero guardar productos favoritos para comprarlos después.', 'c63ea06c-a6f9-41ec-abdd-48f1f2821e86', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'To Do', 4, 8, NULL, NULL, NULL, 0, 0, 0, NULL, 0.00, NULL, NULL, NULL, NULL, '5d5c77fd-34eb-4fbf-9be6-d9a3014cb337', '2025-11-14 05:48:03', '2025-11-14 05:48:03'),
	(22, 'US-011: Sistema de reviews y ratings', 'Como cliente, quiero calificar y comentar productos comprados.', 'c63ea06c-a6f9-41ec-abdd-48f1f2821e86', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'To Do', 5, 13, NULL, NULL, NULL, 0, 0, 0, NULL, 0.00, NULL, NULL, NULL, NULL, '5d5c77fd-34eb-4fbf-9be6-d9a3014cb337', '2025-11-14 05:48:03', '2025-11-14 05:48:03'),
	(23, 'US-012: Notificaciones por email', 'Como cliente, quiero recibir emails de confirmación de pedido y envío.', 'c63ea06c-a6f9-41ec-abdd-48f1f2821e86', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'To Do', 6, 5, NULL, NULL, NULL, 0, 0, 0, NULL, 0.00, NULL, NULL, NULL, NULL, '5d5c77fd-34eb-4fbf-9be6-d9a3014cb337', '2025-11-14 05:48:03', '2025-11-14 05:48:03');

-- Volcando estructura para tabla sgcs.usuarios
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` char(36) NOT NULL,
  `correo` varchar(255) NOT NULL,
  `correo_verificado_en` timestamp NULL DEFAULT NULL,
  `nombre_completo` varchar(255) NOT NULL,
  `contrasena_hash` varchar(255) NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `remember_token` varchar(100) DEFAULT NULL,
  `google2fa_secret` varchar(255) DEFAULT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `actualizado_en` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `usuarios_correo_unique` (`correo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla sgcs.usuarios: ~13 rows (aproximadamente)
INSERT INTO `usuarios` (`id`, `correo`, `correo_verificado_en`, `nombre_completo`, `contrasena_hash`, `activo`, `remember_token`, `google2fa_secret`, `creado_en`, `actualizado_en`) VALUES
	('5d5c77fd-34eb-4fbf-9be6-d9a3014cb337', 'admin@sgcs.com', '2025-11-14 05:48:00', 'Administrador SGCS', '$2y$12$fmoUhnPi7OYqIGVY6MTOguR.gJ0o38nQ/eGNHhpErmSgAO19bM9Z2', 1, NULL, NULL, '2025-11-14 05:48:00', '2025-11-14 05:48:00'),
	('848b794e-0a54-4dc5-b2fc-7b5618d54140', 'dev2@sgcs.com', '2025-11-14 05:48:01', 'Carmen Ruiz - Developer', '$2y$12$c3k0cBwYj9fV864N/txQNOKMm5zJj.3K07GYB9YFFDPnJjNQFYSZ2', 1, NULL, NULL, '2025-11-14 05:48:01', '2025-11-14 05:48:01'),
	('85ad1d9a-d33c-45c5-a652-ae34e08de3b9', 'po@sgcs.com', '2025-11-14 05:48:01', 'María González - Product Owner', '$2y$12$awkjY4rZu4fTKc2sR3HhTuai5Z3t59oCmzDgtY3YZ9MG8L/HvrfdO', 1, NULL, NULL, '2025-11-14 05:48:01', '2025-11-14 05:48:01'),
	('949be9b3-b065-4e69-899a-6e3f2fac8fc9', 'tester@sgcs.com', '2025-11-14 05:48:02', 'Ricardo Pérez - Tester', '$2y$12$OsEh7mLdln9SUJHI.L9fdOeZeO9Vv.xvHCO3ly/VRE.S1FoFDxx6i', 1, NULL, NULL, '2025-11-14 05:48:02', '2025-11-14 05:48:02'),
	('962af55f-6ef5-4f08-8813-df0fd1c29519', 'sm@sgcs.com', '2025-11-14 05:48:01', 'Roberto Castillo - Scrum Master', '$2y$12$Jjdb1W3OikDj7/olwweePOmWNp5vuly2e6dC5yYeXzlLSijXj/.5S', 1, NULL, NULL, '2025-11-14 05:48:01', '2025-11-14 05:48:01'),
	('9db28213-895e-4590-b696-f59cf29f0832', 'dev1@sgcs.com', '2025-11-14 05:48:01', 'Luis Hernández - Dev Senior', '$2y$12$LtdnWuzBZKkOkOPOXohV.e9Cjj78oWcBwGk4OmWBPmjCt13RNjQGy', 1, NULL, NULL, '2025-11-14 05:48:01', '2025-11-14 05:48:01'),
	('b0ae25ef-b19d-412e-a628-5a49d084b82d', 'pm@sgcs.com', '2025-11-14 05:48:02', 'Fernando Sánchez - Project Manager', '$2y$12$hgwwp0mGVzRY115cxl.ShO/a8ck4tk1XSFXt7nXtlLlUIsSx6WOaq', 1, NULL, NULL, '2025-11-14 05:48:02', '2025-11-14 05:48:02'),
	('b1daa777-d9c5-4831-9b65-5ced9f65b73f', 'qa@sgcs.com', '2025-11-14 05:48:02', 'Patricia Vega - QA Lead', '$2y$12$3qxlYyseXbO9VDPOCEzuNuctr/OauVeWifz6ywwm6XMLsC/W/Z1r6', 1, NULL, NULL, '2025-11-14 05:48:02', '2025-11-14 05:48:02'),
	('bb4c897e-6d7f-449c-b81a-7e5feceb9ca3', 'analyst@sgcs.com', '2025-11-14 05:48:02', 'Laura Martínez - Analista', '$2y$12$bxYQjtcOl9LP5X2EDyDpROyanwTYUhZcf95PlkzH4ueu3pirpqeKm', 1, NULL, NULL, '2025-11-14 05:48:02', '2025-11-14 05:48:02'),
	('cc256b81-4706-49cd-a8c7-f29394c32ebe', 'scm@sgcs.com', '2025-11-14 05:48:00', 'Carlos Méndez - SCM Manager', '$2y$12$VbkfFm/8xTA2Lzn0iIr02.gDYFHmVIBmr8Di9W82m/JtLbAb3Jmd.', 1, NULL, NULL, '2025-11-14 05:48:00', '2025-11-14 05:48:00'),
	('dc284e83-5666-45a4-b342-b3cff7ba1eea', 'ccb@sgcs.com', '2025-11-14 05:48:00', 'Ana López - CCB Admin', '$2y$12$31H2BjLvpr1m.FdJpgjFuODWI2XbMfejQjjLhGqhSngfe/xaawP4S', 1, NULL, NULL, '2025-11-14 05:48:00', '2025-11-14 05:48:00'),
	('df805d5f-6f5f-430b-b196-5faec7ca72a9', 'dev3@sgcs.com', '2025-11-14 05:48:01', 'Diego Morales - Developer', '$2y$12$lDPjwBq0d0Qo94qFXu7AMeNpzmX007w/TFrzNEfPpcE2ZQ3VcpF/u', 1, NULL, NULL, '2025-11-14 05:48:01', '2025-11-14 05:48:01'),
	('e0a3c802-dc32-4397-a9dd-1320face8c3a', 'arch@sgcs.com', '2025-11-14 05:48:02', 'Alberto Jiménez - Arquitecto', '$2y$12$QE3OmUtquejEmla9Qq/Wx.kj0FGFBk5oMt2mRJlAVHiCY4URUmw/e', 1, NULL, NULL, '2025-11-14 05:48:02', '2025-11-14 05:48:02');

-- Volcando estructura para tabla sgcs.usuarios_roles
CREATE TABLE IF NOT EXISTS `usuarios_roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `usuario_id` char(36) NOT NULL,
  `rol_id` bigint(20) unsigned NOT NULL,
  `proyecto_id` char(36) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `usuarios_roles_usuario_id_foreign` (`usuario_id`),
  KEY `usuarios_roles_rol_id_foreign` (`rol_id`),
  KEY `usuarios_roles_proyecto_id_foreign` (`proyecto_id`),
  CONSTRAINT `usuarios_roles_proyecto_id_foreign` FOREIGN KEY (`proyecto_id`) REFERENCES `proyectos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `usuarios_roles_rol_id_foreign` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`),
  CONSTRAINT `usuarios_roles_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla sgcs.usuarios_roles: ~16 rows (aproximadamente)
INSERT INTO `usuarios_roles` (`id`, `usuario_id`, `rol_id`, `proyecto_id`) VALUES
	(1, '5d5c77fd-34eb-4fbf-9be6-d9a3014cb337', 5, 'c63ea06c-a6f9-41ec-abdd-48f1f2821e86'),
	(2, '962af55f-6ef5-4f08-8813-df0fd1c29519', 6, 'c63ea06c-a6f9-41ec-abdd-48f1f2821e86'),
	(3, '85ad1d9a-d33c-45c5-a652-ae34e08de3b9', 5, 'c63ea06c-a6f9-41ec-abdd-48f1f2821e86'),
	(4, '9db28213-895e-4590-b696-f59cf29f0832', 12, 'c63ea06c-a6f9-41ec-abdd-48f1f2821e86'),
	(5, '848b794e-0a54-4dc5-b2fc-7b5618d54140', 13, 'c63ea06c-a6f9-41ec-abdd-48f1f2821e86'),
	(6, 'df805d5f-6f5f-430b-b196-5faec7ca72a9', 13, 'c63ea06c-a6f9-41ec-abdd-48f1f2821e86'),
	(7, 'b1daa777-d9c5-4831-9b65-5ced9f65b73f', 14, 'c63ea06c-a6f9-41ec-abdd-48f1f2821e86'),
	(8, '5d5c77fd-34eb-4fbf-9be6-d9a3014cb337', 9, '1970240a-41aa-4e90-bab7-8dea24cd3de5'),
	(9, 'b0ae25ef-b19d-412e-a628-5a49d084b82d', 9, '1970240a-41aa-4e90-bab7-8dea24cd3de5'),
	(10, 'e0a3c802-dc32-4397-a9dd-1320face8c3a', 10, '1970240a-41aa-4e90-bab7-8dea24cd3de5'),
	(11, 'bb4c897e-6d7f-449c-b81a-7e5feceb9ca3', 5, '1970240a-41aa-4e90-bab7-8dea24cd3de5'),
	(12, '9db28213-895e-4590-b696-f59cf29f0832', 12, '1970240a-41aa-4e90-bab7-8dea24cd3de5'),
	(13, '848b794e-0a54-4dc5-b2fc-7b5618d54140', 13, '1970240a-41aa-4e90-bab7-8dea24cd3de5'),
	(14, 'df805d5f-6f5f-430b-b196-5faec7ca72a9', 13, '1970240a-41aa-4e90-bab7-8dea24cd3de5'),
	(15, 'b1daa777-d9c5-4831-9b65-5ced9f65b73f', 14, '1970240a-41aa-4e90-bab7-8dea24cd3de5'),
	(16, '949be9b3-b065-4e69-899a-6e3f2fac8fc9', 15, '1970240a-41aa-4e90-bab7-8dea24cd3de5');

-- Volcando estructura para tabla sgcs.versiones_ec
CREATE TABLE IF NOT EXISTS `versiones_ec` (
  `id` char(36) NOT NULL,
  `ec_id` char(36) NOT NULL,
  `version` varchar(50) NOT NULL,
  `registro_cambios` text DEFAULT NULL,
  `commit_id` char(36) DEFAULT NULL,
  `metadatos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadatos`)),
  `estado` enum('PENDIENTE','BORRADOR','EN_REVISION','APROBADO','LIBERADO','OBSOLETO') NOT NULL DEFAULT 'BORRADOR',
  `creado_por` char(36) DEFAULT NULL,
  `aprobado_por` char(36) DEFAULT NULL,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `aprobado_en` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `versiones_ec_ec_id_foreign` (`ec_id`),
  KEY `versiones_ec_creado_por_foreign` (`creado_por`),
  KEY `versiones_ec_aprobado_por_foreign` (`aprobado_por`),
  CONSTRAINT `versiones_ec_aprobado_por_foreign` FOREIGN KEY (`aprobado_por`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL,
  CONSTRAINT `versiones_ec_creado_por_foreign` FOREIGN KEY (`creado_por`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL,
  CONSTRAINT `versiones_ec_ec_id_foreign` FOREIGN KEY (`ec_id`) REFERENCES `elementos_configuracion` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla sgcs.versiones_ec: ~3 rows (aproximadamente)
INSERT INTO `versiones_ec` (`id`, `ec_id`, `version`, `registro_cambios`, `commit_id`, `metadatos`, `estado`, `creado_por`, `aprobado_por`, `creado_en`, `aprobado_en`) VALUES
	('11909cca-e359-41ef-8ac0-2630fdca7f8b', 'df043ad7-7d2a-4331-b7a7-e191233915f3', '0.1.0', 'Configuración inicial del proyecto: Laravel 10, Vue 3, Tailwind CSS', NULL, NULL, 'APROBADO', '85ad1d9a-d33c-45c5-a652-ae34e08de3b9', '85ad1d9a-d33c-45c5-a652-ae34e08de3b9', '2025-11-14 05:48:02', '2025-11-14 05:48:02'),
	('5770eb48-1299-4509-8475-39823f6a2b48', 'a4558278-1560-4845-8a7d-bad028bab3e9', '1.0.0', 'Versión inicial con 45 historias de usuario priorizadas', NULL, NULL, 'APROBADO', '85ad1d9a-d33c-45c5-a652-ae34e08de3b9', '85ad1d9a-d33c-45c5-a652-ae34e08de3b9', '2025-11-14 05:48:02', '2025-11-14 05:48:02'),
	('adc5ed88-4227-48b3-b67f-1c8295a2e65a', '4163b058-0660-4949-a69d-8f16e1c42b10', '2.1.0', 'Revisión aprobada con 125 requisitos funcionales y 38 no funcionales', NULL, NULL, 'APROBADO', 'b0ae25ef-b19d-412e-a628-5a49d084b82d', 'b0ae25ef-b19d-412e-a628-5a49d084b82d', '2025-11-14 05:48:03', '2025-09-14 05:48:03');

-- Volcando estructura para tabla sgcs.votos_ccb
CREATE TABLE IF NOT EXISTS `votos_ccb` (
  `id` char(36) NOT NULL,
  `ccb_id` char(36) NOT NULL,
  `solicitud_cambio_id` char(36) NOT NULL,
  `usuario_id` char(36) NOT NULL,
  `voto` enum('APROBAR','RECHAZAR','ABSTENERSE') NOT NULL,
  `comentario` text DEFAULT NULL,
  `votado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `votos_ccb_ccb_id_foreign` (`ccb_id`),
  KEY `votos_ccb_solicitud_cambio_id_foreign` (`solicitud_cambio_id`),
  KEY `votos_ccb_usuario_id_foreign` (`usuario_id`),
  CONSTRAINT `votos_ccb_ccb_id_foreign` FOREIGN KEY (`ccb_id`) REFERENCES `comite_cambios` (`id`),
  CONSTRAINT `votos_ccb_solicitud_cambio_id_foreign` FOREIGN KEY (`solicitud_cambio_id`) REFERENCES `solicitudes_cambio` (`id`),
  CONSTRAINT `votos_ccb_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla sgcs.votos_ccb: ~0 rows (aproximadamente)

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
