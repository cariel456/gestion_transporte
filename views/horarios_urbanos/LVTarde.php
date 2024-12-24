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
            <!-- El contenido se llenará dinámicamente con JavaScript -->
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
    <script>
        const datos = [
            {
                barrio: "PINARES",
                codigo: "8",
                horarios: [
                    { horaSalida: "06:00", lugarSalida: "PINARES AL KM 11", horaLlegada: "07:20", lugarLlegada: "KM 11 - 2 Y 3 - A PINARES" },
                    { horaSalida: "08:00", lugarSalida: "PINARES AL SAMIC", horaLlegada: "09:00", lugarLlegada: "SAMIC A PINARES" },
                    { horaSalida: "09:45", lugarSalida: "PINARES AL SAMIC", horaLlegada: "10:30", lugarLlegada: "SAMIC A PINARES" },
                    { horaSalida: "11:30", lugarSalida: "PINARES  3 Y 2 AL KM 11", horaLlegada: "12:50", lugarLlegada: "Km 11 - GRUBER X MATIENZO" },
                    { horaSalida: "14:10", lugarSalida: "KM 11", horaLlegada: "", lugarLlegada: "FINALIZA" }
                ]
            },
          
    {
        barrio: "PATICUA - 1G 2 Y 3",
        codigo: "162",
        horarios: [
            { horaSalida: "06:30", lugarSalida: "KM 11 - 2  Y 3 a PATICUA y Km 1", horaLlegada: "07:30", lugarLlegada: "PATICUA -  Km1- y vuelve DIRECTO Km 11" },
            { horaSalida: "08:30", lugarSalida: "KM 11 - 2  Y 3 a PATICUA y Km 1", horaLlegada: "09:30", lugarLlegada: "PATICUA -  Km1- y vuelve DIRECTO Km 11" },
            { horaSalida: "10:30", lugarSalida: "KM 11 - 2  Y 3 a PATICUA y Km 1", horaLlegada: "11:30", lugarLlegada: "PATICUA -  Km1- y vuelve DIRECTO Km 11" },
            { horaSalida: "12:30", lugarSalida: "KM 11 - 2  Y 3 a PATICUA y Km 1", horaLlegada: "13:30", lugarLlegada: "PATICUA - CABURE´I - Km1  DIRECTO Km 11" },
            { horaSalida: "14:30", lugarSalida: "KM 11 Finaliza", horaLlegada: "", lugarLlegada: "" }
        ]
    },
    {
        barrio: "AVENIDA - 1H",
        codigo: "180",
        horarios: [
            { horaSalida: "06:45", lugarSalida: "Km 11 - 2  Y 3 a KM 1", horaLlegada: "07:45", lugarLlegada: "KM 1- Km 11 - DIRECTO" },
            { horaSalida: "08:45", lugarSalida: "Km 11 - 2  Y 3 a KM 1", horaLlegada: "09:45", lugarLlegada: "KM 1- Km 11 - DIRECTO" },
            { horaSalida: "10:45", lugarSalida: "Km 11 - 2  Y 3 a KM 1", horaLlegada: "11:45", lugarLlegada: "KM 1- Km 11 - DIRECTO" },
            { horaSalida: "12:45", lugarSalida: "Km 11 - 2  Y 3 a KM 1", horaLlegada: "13:45", lugarLlegada: "KM 1- Km 11 - DIRECTO" },
            { horaSalida: "14:45", lugarSalida: "KM 11 Finaliza", horaLlegada: "", lugarLlegada: "" }
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