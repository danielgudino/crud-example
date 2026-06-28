<?php
/**
 * View (motor de plantillas muy simple)
 * -------------------------------------
 * Su trabajo es: tomar un archivo .html, reemplazar las variables
 * escritas como {{nombre}} por su valor real, y mostrarlo en pantalla.
 *
 * Así separamos el PHP (lógica) del HTML (diseño).
 * El HTML NO tiene código PHP: solo "huecos" con la forma {{variable}}.
 *
 * Ejemplo:
 *   HTML:  <input value="{{nombre}}">
 *   PHP :  View::render('formulario', ['nombre' => 'Max']);
 *   Sale:  <input value="Max">
 */

class View
{
    public static function render(string $vista, array $datos = []): void
    {
        $rutaVistas = __DIR__ . '/../views/';

        // Toda página = encabezado + contenido + pie.
        $html  = file_get_contents($rutaVistas . 'partials/header.html');
        $html .= file_get_contents($rutaVistas . $vista . '.html');
        $html .= file_get_contents($rutaVistas . 'partials/footer.html');

        // Reemplazamos cada {{clave}} por su valor.
        foreach ($datos as $clave => $valor) {
            $html = str_replace('{{' . $clave . '}}', (string) $valor, $html);
        }

        // Limpiamos cualquier {{...}} que no se haya usado, para que no se vea.
        $html = preg_replace('/\{\{[^}]+\}\}/', '', $html);

        echo $html;
    }
}
