-- Ejecuta esta consulta en tu base de datos para ver qué instituciones tienen calificaciones

-- 1. Ver todas las instituciones
SELECT id, nombre, activo FROM instituciones ORDER BY nombre;

-- 2. Ver todas las calificaciones
SELECT * FROM calificaciones WHERE activo = 1 LIMIT 20;

-- 3. Ver el conteo de calificaciones por institución
SELECT 
    i.id,
    i.nombre as institucion,
    COUNT(c.id) as total_calificaciones,
    ROUND(AVG(c.puntaje), 2) as promedio,
    MIN(c.puntaje) as min_puntaje,
    MAX(c.puntaje) as max_puntaje
FROM instituciones i
LEFT JOIN calificaciones c ON c.institucion_id = i.id AND c.activo = 1
GROUP BY i.id, i.nombre
ORDER BY promedio DESC;

-- 4. Ver la estructura de la tabla calificaciones
DESCRIBE calificaciones;

-- 5. Ver si hay calificaciones sin institucion_id
SELECT COUNT(*) as sin_institucion FROM calificaciones WHERE institucion_id IS NULL OR institucion_id = 0;
