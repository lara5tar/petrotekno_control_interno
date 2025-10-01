-- Crear una asignación de prueba para demostrar el botón "Liberar Asignación"
INSERT INTO obra_vehiculo (obra_id, vehiculo_id, fecha_asignacion, kilometraje_inicial, estado, created_at, updated_at) 
VALUES (1, 1, NOW(), 50000, 'activo', NOW(), NOW());

-- Verificar que la asignación se creó correctamente
SELECT * FROM obra_vehiculo WHERE obra_id = 1 AND estado = 'activo';