<?php
require_once '../../config/config.php';
require_once ROOT_PATH . '/includes/auth.php';
require_once ROOT_PATH . '/includes/functions.php';
include ROOT_PATH . '/includes/header.php';

requireLogin();

$terminales = getAllTerminales();
$lineas = getAllLineas();

// Función para buscar horarios con filtros
function searchHorariosInterurbanos($filters) {
    global $conn;
    $sql = "SELECT hi.*, l.numero AS numero_linea, l.descripcion AS descripcion_linea,
                   t1.nombre_terminal AS terminal_salida_nombre, 
                   t2.nombre_terminal AS terminal_llegada_nombre
            FROM horarios_interurbanos hi
            JOIN lineas l ON hi.linea_id = l.id
            JOIN terminales t1 ON hi.terminal_salida = t1.id
            JOIN terminales t2 ON hi.terminal_llegada = t2.id
            WHERE 1=1";
    
    $params = [];
    $types = "";

    if (!empty($filters['linea_id'])) {
        $sql .= " AND hi.linea_id = ?";
        $params[] = $filters['linea_id'];
        $types .= "i";
    }
    if (!empty($filters['terminal_salida'])) {
        $sql .= " AND hi.terminal_salida = ?";
        $params[] = $filters['terminal_salida'];
        $types .= "i";
    }
    if (!empty($filters['terminal_llegada'])) {
        $sql .= " AND hi.terminal_llegada = ?";
        $params[] = $filters['terminal_llegada'];
        $types .= "i";
    }
    if (!empty($filters['hora_salida'])) {
        $sql .= " AND hi.hora_salida >= ?";
        $params[] = $filters['hora_salida'];
        $types .= "s";
    }
    if (!empty($filters['hora_llegada'])) {
        $sql .= " AND hi.hora_llegada <= ?";
        $params[] = $filters['hora_llegada'];
        $types .= "s";
    }

    $sql .= " ORDER BY l.numero, hi.hora_salida";

    $stmt = $conn->prepare($sql);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

$results = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $filters = [
        'linea_id' => $_POST['linea_id'] ?? null,
        'terminal_salida' => $_POST['terminal_salida'] ?? null,
        'terminal_llegada' => $_POST['terminal_llegada'] ?? null,
        'hora_salida' => $_POST['hora_salida'] ?? null,
        'hora_llegada' => $_POST['hora_llegada'] ?? null
    ];
    $results = searchHorariosInterurbanos($filters);
}
?>

<div class="container mt-5">
    <h2>Buscar Horarios Interurbanos</h2>

    <form method="POST" class="mb-4">
        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="linea_id" class="form-label">Línea</label>
                <select class="form-control" id="linea_id" name="linea_id">
                    <option value="">Todas las líneas</option>
                    <?php foreach ($lineas as $linea): ?>
                        <option value="<?php echo $linea['id']; ?>"><?php echo $linea['numero'] . ' - ' . $linea['descripcion']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label for="terminal_salida" class="form-label">Terminal de Salida</label>
                <select class="form-control" id="terminal_salida" name="terminal_salida">
                    <option value="">Todas las terminales</option>
                    <?php foreach ($terminales as $terminal): ?>
                        <option value="<?php echo $terminal['id']; ?>"><?php echo $terminal['nombre_terminal']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label for="terminal_llegada" class="form-label">Terminal de Llegada</label>
                <select class="form-control" id="terminal_llegada" name="terminal_llegada">
                    <option value="">Todas las terminales</option>
                    <?php foreach ($terminales as $terminal): ?>
                        <option value="<?php echo $terminal['id']; ?>"><?php echo $terminal['nombre_terminal']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="hora_salida" class="form-label">Hora de Salida (desde)</label>
                <input type="time" class="form-control" id="hora_salida" name="hora_salida">
            </div>
            <div class="col-md-6 mb-3">
                <label for="hora_llegada" class="form-label">Hora de Llegada (hasta)</label>
                <input type="time" class="form-control" id="hora_llegada" name="hora_llegada">
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Buscar</button>
    </form>

    <?php if (!empty($results)): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Línea</th>
                    <th>Terminal Salida</th>
                    <th>Terminal Llegada</th>
                    <th>Hora Salida</th>
                    <th>Hora Llegada</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($results as $horario): ?>
                <tr>
                    <td><?php echo $horario['numero_linea'] . ' - ' . $horario['descripcion_linea']; ?></td>
                    <td><?php echo $horario['terminal_salida_nombre']; ?></td>
                    <td><?php echo $horario['terminal_llegada_nombre']; ?></td>
                    <td><?php echo $horario['hora_salida']; ?></td>
                    <td><?php echo $horario['hora_llegada']; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
        <p>No se encontraron resultados.</p>
    <?php endif; ?>
</div>