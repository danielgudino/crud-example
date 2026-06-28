<?php
/**
 * index.php (FRONT CONTROLLER / enrutador)
 * ----------------------------------------
 * Es la ÚNICA puerta de entrada de la aplicación.
 * Todas las páginas se piden así:  index.php?action=loQueSea
 *
 * Aquí solo hacemos 3 cosas:
 *   1. Cargar las clases que necesitamos.
 *   2. Leer qué "action" pidió el usuario.
 *   3. Llamar al método del controlador con ese nombre.
 */

// 1. Cargamos las piezas (núcleo, modelo y controlador).
require_once __DIR__ . '/../app/core/Database.php';
require_once __DIR__ . '/../app/core/View.php';
require_once __DIR__ . '/../app/models/AnimalModel.php';
require_once __DIR__ . '/../app/controllers/AnimalController.php';

// 2. ¿Qué acción se pidió? Si no viene ninguna, mostramos el listado.
$action = $_GET['action'] ?? 'index';

// 3. Lista blanca de acciones permitidas (seguridad: nadie llama métodos raros).
$accionesValidas = [
    'index', 'datos', 'crear', 'ver', 'editar',
    'guardar', 'actualizar', 'estado', 'eliminar',
];

$controlador = new AnimalController();

if (in_array($action, $accionesValidas, true)) {
    $controlador->$action();
} else {
    http_response_code(404);
    echo "Acción no encontrada: " . htmlspecialchars($action);
}
