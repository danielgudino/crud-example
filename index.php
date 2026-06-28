<?php
/**
 * Redireccionador de la raíz.
 *
 * La aplicación vive dentro de la carpeta "public/" (por seguridad y orden).
 * Este archivo solo existe para que, si alguien entra a la raíz del proyecto
 * (por ejemplo http://localhost/crud-example/), lo enviemos directo a public/.
 */

header('Location: public/');
exit;
