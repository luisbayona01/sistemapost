-- ============================================
-- RESET DE DATOS PARA PRUEBA DE CIERRE
-- Fecha: 14/02/2026
-- ============================================

-- 1. Eliminar ventas de hoy (solo si son de prueba)
-- IMPORTANTE: Verifica que NO haya ventas reales antes de ejecutar
DELETE FROM producto_venta 
WHERE venta_id IN (
    SELECT id FROM ventas 
    WHERE DATE(fecha_hora) = CURDATE()
);

DELETE FROM ventas 
WHERE DATE(fecha_hora) = CURDATE();

-- 2. Eliminar cajas de hoy
DELETE FROM cajas 
WHERE DATE(fecha_hora_apertura) = CURDATE();

-- 3. Eliminar movimientos de hoy
DELETE FROM movimientos 
WHERE DATE(created_at) = CURDATE();

-- ============================================
-- VERIFICACIÓN (ejecuta esto después del reset)
-- ============================================
SELECT 'Ventas hoy' as tabla, COUNT(*) as registros FROM ventas WHERE DATE(fecha_hora) = CURDATE()
UNION ALL
SELECT 'Cajas hoy', COUNT(*) FROM cajas WHERE DATE(fecha_hora_apertura) = CURDATE()
UNION ALL
SELECT 'Movimientos hoy', COUNT(*) FROM movimientos WHERE DATE(created_at) = CURDATE();
