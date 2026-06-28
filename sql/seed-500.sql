-- =============================================================
--  Datos de prueba: inserta 500 animales aleatorios
--  Sirve para probar la paginación, el buscador y el filtro
--  del DataTable con bastante información.
--
--  Cómo ejecutarlo:
--      sudo mariadb -u root < sql/seed-500.sql
--
--  OJO: cada vez que lo corras agrega 500 registros MÁS.
--  Para borrar SOLO los de prueba mira el final de este archivo.
-- =============================================================

USE catalogo_animales;

-- Generamos los números del 1 al 500 con un CTE recursivo
-- y por cada número creamos un animal con valores al azar.
INSERT INTO animales
    (nombre, especie, raza, edad, peso, genero, color, fecha_ingreso, estado_salud, activo)
WITH RECURSIVE numeros AS (
    SELECT 1 AS n
    UNION ALL
    SELECT n + 1 FROM numeros WHERE n < 500
)
SELECT
    -- Nombre: uno de la lista + el número, para que sea único y fácil de ver
    CONCAT(
        ELT(FLOOR(1 + RAND() * 16),
            'Max','Luna','Rocky','Bella','Toby','Nina','Zeus','Lola',
            'Simba','Kira','Thor','Maya','Coco','Duna','Bruno','Mia'),
        ' #', n
    ),
    -- Especie
    ELT(FLOOR(1 + RAND() * 6),
        'Perro','Gato','Conejo','Ave','Pez','Hámster'),
    -- Raza
    ELT(FLOOR(1 + RAND() * 8),
        'Labrador','Siamés','Bulldog','Mestizo','Persa','Pastor','Angora','Criollo'),
    -- Edad (0 a 14 años)
    FLOOR(RAND() * 15),
    -- Peso (1.00 a 41.00 kg)
    ROUND(1 + RAND() * 40, 2),
    -- Género
    ELT(FLOOR(1 + RAND() * 2), 'Macho', 'Hembra'),
    -- Color
    ELT(FLOOR(1 + RAND() * 8),
        'Negro','Blanco','Dorado','Café','Gris','Crema','Atigrado','Manchado'),
    -- Fecha de ingreso (algún día dentro de los últimos ~3 años)
    DATE_SUB(CURDATE(), INTERVAL FLOOR(RAND() * 1095) DAY),
    -- Estado de salud
    ELT(FLOOR(1 + RAND() * 4),
        'Saludable','En tratamiento','En observación','Recuperándose'),
    -- Estado activo: ~80% activos, ~20% inactivos
    IF(RAND() < 0.8, 1, 0)
FROM numeros;

-- Cuántos registros hay en total ahora:
SELECT COUNT(*) AS total_animales FROM animales;

-- -------------------------------------------------------------
-- ¿Quieres borrar SOLO los registros de prueba?
-- Estos llevan '#' en el nombre, así que puedes hacer:
--
--     DELETE FROM animales WHERE nombre LIKE '%#%';
--
-- (Tus registros originales Max, Luna, Rocky NO tienen '#'.)
-- -------------------------------------------------------------
