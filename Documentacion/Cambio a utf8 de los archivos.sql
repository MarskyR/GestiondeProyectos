-- 1. AÑADIMOS LOS campos nombreAparente8

 
ALTER TABLE `archivo_orden` ADD `nombreAparente8` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL;
ALTER TABLE `archivo_proyecto` ADD `nombreAparente8` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL;
ALTER TABLE `archivo_rend` ADD `nombreAparente8` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL;
ALTER TABLE `archivo_repo` ADD `nombreAparente8` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL;
ALTER TABLE `archivo_req_admin` ADD `nombreAparente8` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL;
ALTER TABLE `archivo_req_emp` ADD `nombreAparente8` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL;
ALTER TABLE `archivo_solicitud` ADD `nombreAparente8` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL;
 
-- 2. CORREMOS EL PROCEDIMIENTO DE /archivos/migrarAutf8


-- 3. Eliminamos el nombreAparente
ALTER TABLE `archivo_orden` DROP COLUMN `nombreAparente`;
ALTER TABLE `archivo_proyecto` DROP COLUMN `nombreAparente`;
ALTER TABLE `archivo_rend` DROP COLUMN `nombreAparente`;
ALTER TABLE `archivo_repo` DROP COLUMN `nombreAparente`;
ALTER TABLE `archivo_req_admin` DROP COLUMN `nombreAparente`;
ALTER TABLE `archivo_req_emp` DROP COLUMN `nombreAparente`;
ALTER TABLE `archivo_solicitud` DROP COLUMN `nombreAparente`;


-- 4. Cambiamos el nombre de la columna 'nombreAparente8' a 'nombreAparente'
ALTER TABLE `archivo_orden` CHANGE `nombreAparente8` `nombreAparente` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL;
ALTER TABLE `archivo_proyecto` CHANGE `nombreAparente8` `nombreAparente` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL;
ALTER TABLE `archivo_rend` CHANGE `nombreAparente8` `nombreAparente` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL;
ALTER TABLE `archivo_repo` CHANGE `nombreAparente8` `nombreAparente` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL;
ALTER TABLE `archivo_req_admin` CHANGE `nombreAparente8` `nombreAparente` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL;
ALTER TABLE `archivo_req_emp` CHANGE `nombreAparente8` `nombreAparente` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL;
ALTER TABLE `archivo_solicitud` CHANGE `nombreAparente8` `nombreAparente` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL;
