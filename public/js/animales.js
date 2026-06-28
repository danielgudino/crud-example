/*
 * animales.js
 * -----------
 * Aquí vive TODO el comportamiento de la tabla (JavaScript).
 * Así no mezclamos JS dentro del HTML ni del PHP.
 *
 * DataTables se encarga solo de: paginación, buscador y ordenar columnas.
 * Nosotros solo le decimos de dónde sacar los datos y cómo pintar los botones.
 */

$(document).ready(function () {

    // Si en esta página no existe la tabla, no hacemos nada.
    if (!document.getElementById('tablaAnimales')) {
        return;
    }

    // Creamos el DataTable.
    var tabla = $('#tablaAnimales').DataTable({

        // De dónde saca los datos (nuestro controlador devuelve JSON).
        ajax: {
            url: 'index.php?action=datos',
            dataSrc: 'data'
        },

        // Traducción al español (archivo local, sin internet).
        language: {
            url: 'vendor/datatables/i18n/es-ES.json'
        },

        // Distribución de la tabla: arriba los botones (B) y el buscador (f),
        // luego el menú de "Mostrar N" (l), la tabla (tr), y abajo info (i) + paginación (p).
        dom: "<'row mb-2'<'col-md-6'B><'col-md-6'f>>" +
             "<'row mb-2'<'col-md-6'l>>" +
             "<'row'<'col-12'tr>>" +
             "<'row mt-2'<'col-md-5'i><'col-md-7'p>>",

        // Botones para generar reportes.
        // exportOptions: columns ':not(:last-child)' = NO exportamos la columna "Acciones".
        buttons: [
            {
                extend: 'copyHtml5',
                text: '<i class="bi bi-clipboard"></i> Copiar',
                className: 'btn btn-sm btn-secondary',
                exportOptions: { columns: ':not(:last-child)' }
            },
            {
                extend: 'csvHtml5',
                text: '<i class="bi bi-filetype-csv"></i> CSV',
                title: 'animales',
                className: 'btn btn-sm btn-secondary',
                exportOptions: { columns: ':not(:last-child)' }
            },
            {
                extend: 'excelHtml5',
                text: '<i class="bi bi-file-earmark-excel"></i> Excel',
                title: 'animales',
                className: 'btn btn-sm btn-success',
                exportOptions: { columns: ':not(:last-child)' }
            },
            {
                extend: 'pdfHtml5',
                text: '<i class="bi bi-file-earmark-pdf"></i> PDF',
                title: 'Reporte de animales',
                orientation: 'landscape', // horizontal: caben mejor las columnas
                pageSize: 'A4',
                className: 'btn btn-sm btn-danger',
                exportOptions: { columns: ':not(:last-child)' }
            },
            {
                extend: 'print',
                text: '<i class="bi bi-printer"></i> Imprimir',
                title: 'Reporte de animales',
                className: 'btn btn-sm btn-primary',
                exportOptions: { columns: ':not(:last-child)' }
            }
        ],

        // Qué columna muestra cada campo del JSON.
        columns: [
            { data: 'id' },
            { data: 'nombre' },
            { data: 'especie' },
            { data: 'raza' },
            { data: 'edad' },
            { data: 'peso' },
            { data: 'genero' },
            { data: 'color' },
            { data: 'fecha_ingreso' },
            { data: 'estado_salud' },

            // Columna "Estado": pintamos una etiqueta de color.
            {
                data: 'activo',
                render: function (valor) {
                    return valor == 1
                        ? '<span class="badge bg-success">Activo</span>'
                        : '<span class="badge bg-secondary">Inactivo</span>';
                }
            },

            // Columna "Acciones": consultar, editar, activar/desactivar y eliminar.
            {
                data: null,
                orderable: false,
                searchable: false,
                className: 'text-center',
                render: function (fila) {

                    // El botón de activar/desactivar depende del estado actual.
                    var botonEstado = (fila.activo == 1)
                        ? '<a class="btn btn-sm btn-warning" title="Desactivar" ' +
                          'href="index.php?action=estado&id=' + fila.id + '&valor=0">' +
                          '<i class="bi bi-toggle-off"></i></a>'
                        : '<a class="btn btn-sm btn-success" title="Activar" ' +
                          'href="index.php?action=estado&id=' + fila.id + '&valor=1">' +
                          '<i class="bi bi-toggle-on"></i></a>';

                    return '<div class="btn-group">' +
                        // Consultar (solo ver)
                        '<a class="btn btn-sm btn-info" title="Consultar" ' +
                        'href="index.php?action=ver&id=' + fila.id + '">' +
                        '<i class="bi bi-eye"></i></a>' +

                        // Editar
                        '<a class="btn btn-sm btn-primary" title="Editar" ' +
                        'href="index.php?action=editar&id=' + fila.id + '">' +
                        '<i class="bi bi-pencil"></i></a>' +

                        // Activar / Desactivar (estado en base de datos)
                        botonEstado +

                        // Eliminar físico (pide confirmación antes)
                        '<a class="btn btn-sm btn-danger" title="Eliminar" ' +
                        'href="index.php?action=eliminar&id=' + fila.id + '" ' +
                        'onclick="return confirm(\'¿Eliminar este registro de forma permanente?\')">' +
                        '<i class="bi bi-trash"></i></a>' +
                        '</div>';
                }
            }
        ]
    });

    // Cuando cambia el droplist de estado, recargamos la tabla con el filtro.
    $('#filtroEstado').on('change', function () {
        var estado = this.value; // 'todos', '1' o '0'
        tabla.ajax.url('index.php?action=datos&estado=' + estado).load();
    });
});
