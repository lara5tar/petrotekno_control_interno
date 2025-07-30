-- Script para crear la base de datos MySQL
-- Ejecutar este script en MySQL Workbench, phpMyAdmin o cualquier cliente MySQL

CREATE DATABASE IF NOT EXISTS petrotekno 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

-- Verificar que la base de datos fue creada
SHOW DATABASES LIKE 'petrotekno';

-- Usar la base de datos
USE petrotekno;

-- Mostrar las tablas (debería estar vacía inicialmente)
SHOW TABLES;