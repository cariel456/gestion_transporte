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
                barrio: "PINARES POR TRANSITO PESADO",
                codigo: "8",
                horarios: [
                    { horaSalida: "05:00", lugarSalida: "KM 11 - 2  Y 3 - A PINARES x TP", horaLlegada: "06:00", lugarLlegada: "PINARES - KM 11 TP" },
                    { horaSalida: "07:00", lugarSalida: "KM 11 - 2  Y 3 - A PINARES x TP", horaLlegada: "08:00", lugarLlegada: "PINARES - KM 11 TP" },
                    { horaSalida: "09:00", lugarSalida: "KM 11 - 2  Y 3 - A PINARES x TP", horaLlegada: "10:00", lugarLlegada: "PINARES - KM 11 TP" },
                    { horaSalida: "11:15", lugarSalida: "KM 11 - A PINARES TP", horaLlegada: "12:00", lugarLlegada: "PINARES - KM 11 TP Finaliza" }
                ]
            },
            {
                barrio: "IPRODHA - KM 3",
                codigo: "172",
                horarios: [
                    { horaSalida: "05:15", lugarSalida: "IPRODHA - AL KM 11 - DIRECTO", horaLlegada: "06:15", lugarLlegada: "KM 11 - 2 Y 3  - A IPRODHA" },
                    { horaSalida: "07:00", lugarSalida: "IPRODHA - AL KM 11 - DIRECTO", horaLlegada: "07:45", lugarLlegada: "KM 11 - 2 Y 3  - A IPRODHA" },
                    { horaSalida: "08:30", lugarSalida: "IPRODHA - AL KM 11 - DIRECTO", horaLlegada: "09:15", lugarLlegada: "KM 11 - 2 Y 3  - A IPRODHA" },
                    { horaSalida: "10:00", lugarSalida: "IPRODHA - AL KM 11 - DIRECTO", horaLlegada: "10:45", lugarLlegada: "KM 11 - 2 Y 3  - A IPRODHA" },
                    { horaSalida: "11:30", lugarSalida: "IPRODHA - AL KM 11 - DIRECTO", horaLlegada: "12:15", lugarLlegada: "KM 11 - 2 Y 3  - A IPRODHA" },
                    { horaSalida: "13:00", lugarSalida: "IPRODHA - AL KM 11 - DIRECTO", horaLlegada: "13:45", lugarLlegada: "KM 11 - 2 Y 3  - A IPRODHA" },
                    { horaSalida: "14:30", lugarSalida: "IPRODHA - AL KM 11 - DIRECTO", horaLlegada: "15:15", lugarLlegada: "Finaliza" }
                ]
            },
            {
                barrio: "IPRODHA - KM 3",
                codigo: "171",
                horarios: [
                    { horaSalida: "06:15", lugarSalida: "IPRODHA - San Jose - AL KM 11", horaLlegada: "07:00", lugarLlegada: "KM 11 - 2 Y 3  - A IPRODHA" },
                    { horaSalida: "07:45", lugarSalida: "IPRODHA - 3 Y 2 - AL KM 11", horaLlegada: "08:30", lugarLlegada: "KM 11 - 2 Y 3  - A IPRODHA" },
                    { horaSalida: "09:15", lugarSalida: "IPRODHA - 3 Y 2 - AL KM 11", horaLlegada: "10:00", lugarLlegada: "KM 11 - 2 Y 3  - A IPRODHA" },
                    { horaSalida: "10:45", lugarSalida: "IPRODHA - 3 Y 2 - AL KM 11", horaLlegada: "11:30", lugarLlegada: "KM 11 - 2 Y 3  - A IPRODHA" },
                    { horaSalida: "12:15", lugarSalida: "IPRODHA - 3 Y 2 - AL KM 11", horaLlegada: "13:00", lugarLlegada: "KM 11 - 2 Y 3  - A IPRODHA" },
                    { horaSalida: "13:45", lugarSalida: "IPRODHA - 3 Y 2 - AL KM 11", horaLlegada: "14:30", lugarLlegada: "Finaliza" }
                ]
            },

            {
        barrio: "ROULET",
        codigo: "8",
        horarios: [
            { horaSalida: "05:45", lugarSalida: "KM 11  - ROULET", horaLlegada: "06:10", lugarLlegada: "ROULET x SARMIENTO KM 11" },
            { horaSalida: "07:10", lugarSalida: "KM 11 - x R 12 - ROULET", horaLlegada: "07:45", lugarLlegada: "ROULET x SARMIENTO KM 11" },
            { horaSalida: "08:45", lugarSalida: "KM 11 - x SARMIENTO - ROULET", horaLlegada: "09:30", lugarLlegada: "ROULET x  SARMIENTO KM 14" },
            { horaSalida: "10:30", lugarSalida: "KM 14 Samrau y Josy - x SARMENTO - ROULET", horaLlegada: "11:30", lugarLlegada: "ROULET x  SARMIENTO KM 14" },
            { horaSalida: "12:10", lugarSalida: "KM 11 A LAPACHOS", horaLlegada: "13:00", lugarLlegada: "LAPACHOS KM 11" }
        ]
    },
    {
        barrio: "AVENIDA - 1A",
        codigo: "176",
        horarios: [
            { horaSalida: "05:00", lugarSalida: "Km 11 - 2  Y 3 - ELENA - KM 1", horaLlegada: "06:00", lugarLlegada: "KM 1- Km 11 - DIRECTO" },
            { horaSalida: "07:00", lugarSalida: "Km 11 - 2  Y 3 a KM 1", horaLlegada: "08:00", lugarLlegada: "KM 1- Km 11 - DIRECTO" },
            { horaSalida: "09:00", lugarSalida: "Km 11 - 2  Y 3 a KM 1", horaLlegada: "10:00", lugarLlegada: "KM 1- Km 11 - DIRECTO" },
            { horaSalida: "11:00", lugarSalida: "Km 11 - 2  Y 3 a KM 1", horaLlegada: "12:00", lugarLlegada: "KM 1- Km 11 - DIRECTO" },
            { horaSalida: "13:00", lugarSalida: "KM 11 Finaliza", horaLlegada: "", lugarLlegada: "" }
        ]
    },
    {
        barrio: "AVENIDA - 1B",
        codigo: "178",
        horarios: [
            { horaSalida: "05:15", lugarSalida: "KM 11 - 2 y 3 - CABUREI - KM 1", horaLlegada: "06:15", lugarLlegada: "KM 1- Km 11 - DIRECTO" },
            { horaSalida: "07:15", lugarSalida: "Km 11 - 2  Y 3 a KM 1", horaLlegada: "08:15", lugarLlegada: "KM 1- Km 11 - DIRECTO" },
            { horaSalida: "09:15", lugarSalida: "Km 11 - 2  Y 3 a KM 1", horaLlegada: "10:15", lugarLlegada: "KM 1- Km 11 - DIRECTO" },
            { horaSalida: "11:15", lugarSalida: "Km 11 - 2  Y 3 a KM 1", horaLlegada: "12:15", lugarLlegada: "KM 1- Km 11 - DIRECTO" },
            { horaSalida: "13:15", lugarSalida: "KM 11 Finaliza", horaLlegada: "", lugarLlegada: "" }
        ]
    },
    {
        barrio: "PATICUA - 1C  3 Y 2",
        codigo: "161",
        horarios: [
            { horaSalida: "05:30", lugarSalida: "KM 11 - directo  a PATICUA y Km 1", horaLlegada: "06:30", lugarLlegada: "PATICUA - KM 1 y vuelve 3 y 2 Km 11" },
            { horaSalida: "07:30", lugarSalida: "KM 11 - directo  a PATICUA y Km 1", horaLlegada: "08:30", lugarLlegada: "PATICUA - KM 1 y vuelve 3 y 2 Km 11" },
            { horaSalida: "09:30", lugarSalida: "KM 11 - directo  a PATICUA y Km 1", horaLlegada: "10:30", lugarLlegada: "PATICUA - KM 1 y vuelve 3 y 2 Km 11" },
            { horaSalida: "11:30", lugarSalida: "KM 11 - directo  a PATICUA y Km 1", horaLlegada: "12:30", lugarLlegada: "PATICUA - KM 1 y vuelve 3 y 2 Km 11" },
            { horaSalida: "13:30", lugarSalida: "KM 11 Finaliza", horaLlegada: "", lugarLlegada: "" }
        ]
    },
    {
        barrio: "AVENIDA - 1D",
        codigo: "179",
        horarios: [
            { horaSalida: "05:45", lugarSalida: "KM 11 2 y 3  - KM 1", horaLlegada: "06:45", lugarLlegada: "KM 1 - 3 Y 2 - KM 11" },
            { horaSalida: "07:45", lugarSalida: "KM 11 - KM 1 - DIRECTO", horaLlegada: "08:45", lugarLlegada: "KM 1 - 3 Y 2 - KM 11" },
            { horaSalida: "09:45", lugarSalida: "KM 11 - KM 1 - DIRECTO", horaLlegada: "10:45", lugarLlegada: "KM 1 - 3 Y 2 - KM 11" },
            { horaSalida: "11:45", lugarSalida: "KM 11 - KM 1 - DIRECTO", horaLlegada: "12:45", lugarLlegada: "KM 1 - 3 Y 2 - KM 11" },
            { horaSalida: "13:45", lugarSalida: "KM 11 Finaliza", horaLlegada: "", lugarLlegada: "" }
        ]
    },
    {
        barrio: "AVENIDA - 1E",
        codigo: "175",
        horarios: [
            { horaSalida: "06:00", lugarSalida: "KM 11 - 2 y 3 - ELENA - KM 1", horaLlegada: "07:00", lugarLlegada: "KM 1 - 3 Y 2 - KM 11" },
            { horaSalida: "08:00", lugarSalida: "KM 11 - KM 1 - DIRECTO", horaLlegada: "09:00", lugarLlegada: "KM 1 - 3 Y 2 - KM 11" },
            { horaSalida: "10:00", lugarSalida: "KM 11 - KM 1 - DIRECTO", horaLlegada: "11:00", lugarLlegada: "KM 1 - 3 Y 2 - KM 11" },
            { horaSalida: "12:00", lugarSalida: "KM 11 - KM 1 - DIRECTO", horaLlegada: "13:00", lugarLlegada: "KM 1 - 3 Y 2 - KM 11" },
            { horaSalida: "14:00", lugarSalida: "KM 11 Finaliza", horaLlegada: "", lugarLlegada: "" }
        ]
    },

    {
        barrio: "AVENIDA - 1F",
        codigo: "177",
        horarios: [
            { horaSalida: "06:15", lugarSalida: "KM 11 - 2 y 3  a PATICUA - KM 1", horaLlegada: "07:15", lugarLlegada: "PATICUA - 3 Y 2 - KM 11" },
            { horaSalida: "08:15", lugarSalida: "KM 11 - KM 1 - DIRECTO", horaLlegada: "09:15", lugarLlegada: "KM 1 - 3 Y 2 - AL KM 11" },
            { horaSalida: "10:15", lugarSalida: "KM 11 - KM 1 - DIRECTO", horaLlegada: "11:15", lugarLlegada: "KM 1 - 3 Y 2 - AL KM 11" },
            { horaSalida: "12:15", lugarSalida: "KM 11 - KM 1 - DIRECTO", horaLlegada: "13:15", lugarLlegada: "KM 1 - 3 Y 2 - AL KM 11" },
            { horaSalida: "14:15", lugarSalida: "KM 11 Finaliza", horaLlegada: "", lugarLlegada: "" }
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