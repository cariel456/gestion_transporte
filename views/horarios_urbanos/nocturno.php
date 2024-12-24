<?php
include '../../includes/header.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Horarios de Colectivos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.23/jspdf.plugin.autotable.min.js"></script>
    <style>
        .table-responsive {
            max-height: 400px;
            overflow-y: auto;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Horarios de Colectivos</h1>
        
        <div class="row mb-3">
            <div class="col-md-4">
                <select id="barrioFilter" class="form-select">
                    <option value="">Todos los barrios</option>
                    <!-- Las opciones se llenarán dinámicamente con JavaScript -->
                </select>
            </div>
            <div class="col-md-4">
                <button id="exportPDF" class="btn btn-primary">Exportar a PDF</button>
                <a href="index.php" class="btn btn-secondary">VOLVER</a>
            </div>
        </div>

        <div id="rutasContainer">
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
    <script>
        const datos = [
    {
        barrio: "NOCTURNO DE LUNES A VIERNES",
        codigo: "N1",
        horarios: [
            { horaSalida: "22:10", lugarSalida: "KM 11 - SAMIC - PATICUA - KM 1", horaLlegada: "23:00", lugarLlegada: "KM 1 - SAMIC - KM 11" },
            { horaSalida: "00:00", lugarSalida: "KM 11 - SAMIC - KM 1", horaLlegada: "01:00", lugarLlegada: "KM 1 - SAMIC - KM 11" },
            { horaSalida: "02:00", lugarSalida: "KM 11 - ESPERA PARA SALIDA 03HS", horaLlegada: "", lugarLlegada: "" },
            { horaSalida: "03:00", lugarSalida: "KM 11 - SAMIC - KM 1", horaLlegada: "04:00", lugarLlegada: "KM 1 - SAMIC - KM 11" },
            { horaSalida: "05:00", lugarSalida: "FINALIZA SERVICIO", horaLlegada: "", lugarLlegada: "" }
        ]
    },
    {
        barrio: "RONDINES - ESTE Y OESTE - DE LUNES A DOMINGO - TARDE",
        codigo: "R1",
        horarios: [
            { horaSalida: "13:30", lugarSalida: "KM 11 - 3 Y 2 - TERMINAL", horaLlegada: "14:00", lugarLlegada: "TERMINAL - A LOS LAPACHOS" },
            { horaSalida: "14:30", lugarSalida: "LAPACHOS - A PATICUA", horaLlegada: "15:00", lugarLlegada: "PATICUA - KM 1 - A LA TERMINAL" },
            { horaSalida: "15:30", lugarSalida: "TERMINAL - AL SAMIC", horaLlegada: "16:00", lugarLlegada: "SAMIC - A LA TERMINAL" },
            { horaSalida: "16:30", lugarSalida: "TERMINAL - A LOS LAPCHOS", horaLlegada: "17:00", lugarLlegada: "LAPACHOS - A PATICUA" },
            { horaSalida: "17:30", lugarSalida: "PATICUA - KM 1 - A LA TERMINAL", horaLlegada: "18:00", lugarLlegada: "TERMINAL - AL SAMIC" },
            { horaSalida: "18:30", lugarSalida: "SAMIC - A LA TERMINAL", horaLlegada: "19:00", lugarLlegada: "TERMINAL - A LOS LAPACHOS" },
            { horaSalida: "19:30", lugarSalida: "LAPACHOS - A PATICUA", horaLlegada: "20:00", lugarLlegada: "PATICUA - KM 1 - A LA TERMINAL" },
            { horaSalida: "20:30", lugarSalida: "TERMINAL - AL SAMIC", horaLlegada: "21:00", lugarLlegada: "SAMIC - A LA TERMINAL" },
            { horaSalida: "21:30", lugarSalida: "TERMINAL - AL SAMIC", horaLlegada: "22:00", lugarLlegada: "SAMIC - KM 11 - FINALIZA" }
        ]
    }
];
        function llenarBarrios() {
            const barrioFilter = document.getElementById('barrioFilter');
            const barrios = [...new Set(datos.map(d => d.barrio))];
            barrios.forEach(barrio => {
                const option = document.createElement('option');
                option.value = barrio;
                option.textContent = barrio;
                barrioFilter.appendChild(option);
            });
        }

        function mostrarRutas(filtroBarrio = '') {
            const rutasContainer = document.getElementById('rutasContainer');
            rutasContainer.innerHTML = '';

            datos.filter(d => filtroBarrio === '' || d.barrio === filtroBarrio).forEach(ruta => {
                const rutaElement = document.createElement('div');
                rutaElement.className = 'card mb-3';
                rutaElement.innerHTML = `
                    <div class="card-header">
                        <h5 class="mb-0">${ruta.barrio} - Código ${ruta.codigo}</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Hora Salida</th>
                                        <th>Desde</th>
                                        <th>Hora Llegada</th>
                                        <th>Hasta</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${ruta.horarios.map(h => `
                                        <tr>
                                            <td>${h.horaSalida}</td>
                                            <td>${h.lugarSalida}</td>
                                            <td>${h.horaLlegada}</td>
                                            <td>${h.lugarLlegada}</td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    </div>
                `;
                rutasContainer.appendChild(rutaElement);
            });
        }

        function exportarPDF() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();
            const barrioSeleccionado = document.getElementById('barrioFilter').value;
            const rutasFiltradas = datos.filter(d => barrioSeleccionado === '' || d.barrio === barrioSeleccionado);

            rutasFiltradas.forEach((ruta, index) => {
                if (index > 0) {
                    doc.addPage();
                }

                doc.setFontSize(16);
                doc.text(`${ruta.barrio} - Código ${ruta.codigo}`, 14, 15);

                doc.autoTable({
                    head: [['Hora Salida', 'Desde', 'Hora Llegada', 'Hasta']],
                    body: ruta.horarios.map(h => [h.horaSalida, h.lugarSalida, h.horaLlegada, h.lugarLlegada]),
                    startY: 25,
                });
            });

            doc.save('horarios_colectivos.pdf');
        }

        document.addEventListener('DOMContentLoaded', () => {
            llenarBarrios();
            mostrarRutas();

            document.getElementById('barrioFilter').addEventListener('change', (e) => {
                mostrarRutas(e.target.value);
            });

            document.getElementById('exportPDF').addEventListener('click', exportarPDF);
        });
    </script>
</body>
</html>