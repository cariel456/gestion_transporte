<?php
function searchTurnosDistribucion($nombre, $descripcion, $tipo_servicio, $fecha_inicio, $fecha_fin, $personal) {
    global $db;
    
    $sql = "SELECT DISTINCT td.* FROM turnos_distribucion td
            LEFT JOIN turnos_distribucion_detalle tdd ON td.id = tdd.id_turno_distribucion
            WHERE 1=1";
    $params = array();
    
    if (!empty($nombre)) {
        $sql .= " AND td.nombre LIKE ?";
        $params[] = "%$nombre%";
    }
    
    if (!empty($descripcion)) {
        $sql .= " AND td.descripcion LIKE ?";
        $params[] = "%$descripcion%";
    }
    
    if (!empty($tipo_servicio)) {
        $sql .= " AND td.tipo_servicio = ?";
        $params[] = $tipo_servicio;
    }
    
    if (!empty($fecha_inicio)) {
        $sql .= " AND tdd.fecha >= ?";
        $params[] = $fecha_inicio;
    }
    
    if (!empty($fecha_fin)) {
        $sql .= " AND tdd.fecha <= ?";
        $params[] = $fecha_fin;
    }
    
    if (!empty($personal)) {
        $sql .= " AND tdd.id_personal = ?";
        $params[] = $personal;
    }
    
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getPersonalList() {
    global $db;
    
    $sql = "SELECT id, nombre_personal FROM personal ORDER BY nombre_personal";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}