<?php
/**
 * AnimalModel (el MODELO)
 * -----------------------
 * Aquí vive TODO lo que tiene que ver con la base de datos para los animales.
 * El controlador nunca escribe SQL: siempre le pide los datos a este modelo.
 *
 * Todas las consultas usan "prepared statements" (los signos ?) para que
 * sea imposible la inyección SQL.
 */

class AnimalModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::conectar();
    }

    /**
     * Devuelve todos los animales.
     * $estado: 'todos' | '1' (activos) | '0' (inactivos)
     */
    public function todos(string $estado = 'todos'): array
    {
        $sql = "SELECT * FROM animales";

        if ($estado === '1') {
            $sql .= " WHERE activo = 1";
        } elseif ($estado === '0') {
            $sql .= " WHERE activo = 0";
        }

        $sql .= " ORDER BY id DESC";

        return $this->db->query($sql)->fetchAll();
    }

    /** Busca un animal por su id (para consultar o editar). */
    public function buscarPorId(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM animales WHERE id = ?");
        $stmt->execute([$id]);

        $animal = $stmt->fetch();
        return $animal ?: null;
    }

    /** Inserta un animal nuevo. */
    public function crear(array $d): void
    {
        $sql = "INSERT INTO animales
                    (nombre, especie, raza, edad, peso, genero, color, fecha_ingreso, estado_salud, activo)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $this->db->prepare($sql)->execute([
            $d['nombre'],
            $d['especie'],
            $d['raza'],
            $d['edad']  !== '' ? $d['edad']  : null,
            $d['peso']  !== '' ? $d['peso']  : null,
            $d['genero'],
            $d['color'],
            $d['fecha_ingreso'] !== '' ? $d['fecha_ingreso'] : null,
            $d['estado_salud'],
            $d['activo'],
        ]);
    }

    /** Actualiza un animal existente. */
    public function actualizar(int $id, array $d): void
    {
        $sql = "UPDATE animales SET
                    nombre = ?, especie = ?, raza = ?, edad = ?, peso = ?,
                    genero = ?, color = ?, fecha_ingreso = ?, estado_salud = ?, activo = ?
                WHERE id = ?";

        $this->db->prepare($sql)->execute([
            $d['nombre'],
            $d['especie'],
            $d['raza'],
            $d['edad']  !== '' ? $d['edad']  : null,
            $d['peso']  !== '' ? $d['peso']  : null,
            $d['genero'],
            $d['color'],
            $d['fecha_ingreso'] !== '' ? $d['fecha_ingreso'] : null,
            $d['estado_salud'],
            $d['activo'],
            $id,
        ]);
    }

    /** Cambia solo el estado activo/inactivo (1 o 0). */
    public function cambiarEstado(int $id, int $activo): void
    {
        $stmt = $this->db->prepare("UPDATE animales SET activo = ? WHERE id = ?");
        $stmt->execute([$activo, $id]);
    }

    /** Elimina físicamente el registro (lo borra de verdad). */
    public function eliminar(int $id): void
    {
        $stmt = $this->db->prepare("DELETE FROM animales WHERE id = ?");
        $stmt->execute([$id]);
    }
}
