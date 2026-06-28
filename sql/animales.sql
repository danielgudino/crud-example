-- =============================================================
--  Base de datos para el CRUD de ejemplo
--  Usa la MISMA base y tabla del proyecto original (catalogo_animales).
--  Lo único nuevo es la columna "activo" (1 = activo, 0 = inactivo),
--  que necesitamos para el botón Activar/Desactivar.
-- =============================================================

CREATE DATABASE IF NOT EXISTS catalogo_animales;
USE catalogo_animales;

-- Por si la tabla no existe todavía (instalación desde cero).
CREATE TABLE IF NOT EXISTS animales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    especie VARCHAR(100) NOT NULL,
    raza VARCHAR(100),
    edad INT,
    peso DECIMAL(5,2),
    genero VARCHAR(10),
    color VARCHAR(50),
    fecha_ingreso DATE,
    estado_salud VARCHAR(100)
);

-- Agrega la columna "activo" solo si aún no existe (MariaDB lo permite).
ALTER TABLE animales
    ADD COLUMN IF NOT EXISTS activo TINYINT(1) NOT NULL DEFAULT 1;

-- Datos de ejemplo (solo si la tabla está vacía).
INSERT INTO animales (nombre, especie, raza, edad, peso, genero, color, fecha_ingreso, estado_salud, activo)
SELECT * FROM (SELECT
    'Max'   AS nombre, 'Perro' AS especie, 'Labrador' AS raza, 3 AS edad, 28.50 AS peso,
    'Macho' AS genero, 'Dorado' AS color, '2025-01-15' AS fecha_ingreso, 'Saludable' AS estado_salud, 1 AS activo
) AS nuevo
WHERE NOT EXISTS (SELECT 1 FROM animales);

INSERT INTO animales (nombre, especie, raza, edad, peso, genero, color, fecha_ingreso, estado_salud, activo)
SELECT 'Luna', 'Gato', 'Siamés', 2, 4.20, 'Hembra', 'Crema', '2025-03-20', 'Saludable', 1
WHERE (SELECT COUNT(*) FROM animales) = 1;

INSERT INTO animales (nombre, especie, raza, edad, peso, genero, color, fecha_ingreso, estado_salud, activo)
SELECT 'Rocky', 'Perro', 'Bulldog', 5, 22.00, 'Macho', 'Blanco', '2024-11-10', 'En tratamiento', 0
WHERE (SELECT COUNT(*) FROM animales) = 2;
