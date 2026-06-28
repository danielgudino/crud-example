<?php
/**
 * Database
 * --------
 * Se encarga de UNA sola cosa: crear la conexión a MySQL/MariaDB usando PDO.
 *
 * Usamos PDO (en lugar de mysqli) porque:
 *   - Permite "prepared statements" muy fáciles (evita inyección SQL).
 *   - El código queda más corto y legible.
 *
 * Patrón: guardamos la conexión en una variable estática para no abrir
 * una conexión nueva cada vez (se reutiliza la misma).
 */

class Database
{
    private static ?PDO $conexion = null;

    public static function conectar(): PDO
    {
        // Si ya existe una conexión, la devolvemos tal cual.
        if (self::$conexion === null) {
            $cfg = require __DIR__ . '/../../config/config.php';

            $dsn = "mysql:host={$cfg['host']};dbname={$cfg['nombre']};charset=utf8mb4";

            self::$conexion = new PDO($dsn, $cfg['usuario'], $cfg['clave'], [
                // Si hay un error, lanza una excepción (más fácil de depurar).
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                // Los resultados llegan como arreglos asociativos: $fila['nombre'].
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        }

        return self::$conexion;
    }
}
