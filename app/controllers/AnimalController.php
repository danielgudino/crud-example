<?php
/**
 * AnimalController (el CONTROLADOR)
 * ---------------------------------
 * Es el "intermediario": recibe la petición del usuario, le pide datos
 * al MODELO y decide qué VISTA mostrar.
 *
 * Cada método público de aquí es una ACCIÓN (una pantalla o una operación):
 *   - index()      -> muestra el listado (DataTable)
 *   - datos()      -> devuelve los animales en JSON (lo consume DataTables)
 *   - crear()      -> muestra el formulario vacío (modo registrar)
 *   - ver()        -> muestra el formulario lleno y bloqueado (modo consultar)
 *   - editar()     -> muestra el formulario lleno y editable (modo editar)
 *   - guardar()    -> inserta y vuelve al listado
 *   - actualizar() -> actualiza y vuelve al listado
 *   - estado()     -> activa/desactiva y vuelve al listado
 *   - eliminar()   -> borra físicamente y vuelve al listado
 */

class AnimalController
{
    private AnimalModel $modelo;

    public function __construct()
    {
        $this->modelo = new AnimalModel();
    }

    /** Pantalla principal: el listado con el DataTable. */
    public function index(): void
    {
        View::render('lista', ['titulo' => 'Listado de Animales']);
    }

    /** Devuelve los datos en JSON para que DataTables los pinte. */
    public function datos(): void
    {
        $estado   = $_GET['estado'] ?? 'todos';
        $animales = $this->modelo->todos($estado);

        header('Content-Type: application/json');
        echo json_encode(['data' => $animales]);
    }

    /** Formulario VACÍO para registrar. */
    public function crear(): void
    {
        $this->mostrarFormulario('registrar', null);
    }

    /** Formulario LLENO y bloqueado para solo consultar. */
    public function ver(): void
    {
        $animal = $this->modelo->buscarPorId((int) ($_GET['id'] ?? 0));
        $this->mostrarFormulario('consultar', $animal);
    }

    /** Formulario LLENO y editable para modificar. */
    public function editar(): void
    {
        $animal = $this->modelo->buscarPorId((int) ($_GET['id'] ?? 0));
        $this->mostrarFormulario('editar', $animal);
    }

    /** Recibe el POST del formulario de registro e inserta. */
    public function guardar(): void
    {
        $this->modelo->crear($_POST);
        $this->volverAlListado();
    }

    /** Recibe el POST del formulario de edición y actualiza. */
    public function actualizar(): void
    {
        $this->modelo->actualizar((int) $_POST['id'], $_POST);
        $this->volverAlListado();
    }

    /** Activa o desactiva (cambia el campo "activo"). */
    public function estado(): void
    {
        $this->modelo->cambiarEstado((int) $_GET['id'], (int) $_GET['valor']);
        $this->volverAlListado();
    }

    /** Borra físicamente el registro. */
    public function eliminar(): void
    {
        $this->modelo->eliminar((int) $_GET['id']);
        $this->volverAlListado();
    }

    // ------------------------------------------------------------------
    // Funciones de apoyo (privadas: solo se usan dentro del controlador)
    // ------------------------------------------------------------------

    /**
     * Una sola función arma el formulario para los TRES modos.
     * Así el mismo HTML (formulario.html) sirve para registrar, consultar y editar.
     */
    private function mostrarFormulario(string $modo, ?array $animal): void
    {
        // Valores por defecto (formulario vacío).
        $a = [
            'id' => '', 'nombre' => '', 'especie' => '', 'raza' => '',
            'edad' => '', 'peso' => '', 'genero' => 'Macho', 'color' => '',
            'fecha_ingreso' => '', 'estado_salud' => '', 'activo' => 1,
        ];

        // Si nos pasaron un animal (consultar/editar), usamos sus valores.
        if ($animal) {
            $a = array_merge($a, $animal);
        }

        // Cada modo cambia el título, la acción del formulario y si está bloqueado.
        $modos = [
            'registrar' => ['titulo' => 'Registrar Animal',  'accion' => 'guardar',    'disabled' => '',          'boton' => 'Guardar',    'ocultar' => ''],
            'consultar' => ['titulo' => 'Consultar Animal',  'accion' => '',           'disabled' => 'disabled',  'boton' => '',           'ocultar' => 'd-none'],
            'editar'    => ['titulo' => 'Editar Animal',     'accion' => 'actualizar', 'disabled' => '',          'boton' => 'Actualizar', 'ocultar' => ''],
        ];
        $cfg = $modos[$modo];

        View::render('formulario', [
            'titulo'        => $cfg['titulo'],
            'titulo_form'   => $cfg['titulo'],
            'accion'        => $cfg['accion'],
            'disabled'      => $cfg['disabled'],
            'texto_boton'   => $cfg['boton'],
            'ocultar_boton' => $cfg['ocultar'],

            // Valores de cada campo (escapados para que no rompan el HTML).
            'id'            => $this->limpiar($a['id']),
            'nombre'        => $this->limpiar($a['nombre']),
            'especie'       => $this->limpiar($a['especie']),
            'raza'          => $this->limpiar($a['raza']),
            'edad'          => $this->limpiar($a['edad']),
            'peso'          => $this->limpiar($a['peso']),
            'color'         => $this->limpiar($a['color']),
            'fecha_ingreso' => $this->limpiar($a['fecha_ingreso']),
            'estado_salud'  => $this->limpiar($a['estado_salud']),

            // Para los <select> marcamos cuál opción va "selected".
            'sel_macho'     => $a['genero'] === 'Macho'  ? 'selected' : '',
            'sel_hembra'    => $a['genero'] === 'Hembra' ? 'selected' : '',
            'sel_activo'    => (int) $a['activo'] === 1   ? 'selected' : '',
            'sel_inactivo'  => (int) $a['activo'] === 0   ? 'selected' : '',
        ]);
    }

    /** Escapa caracteres especiales para mostrarlos seguros en el HTML. */
    private function limpiar($valor): string
    {
        return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
    }

    /** Redirige de vuelta al listado. */
    private function volverAlListado(): void
    {
        header('Location: index.php?action=index');
        exit;
    }
}
