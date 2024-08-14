<?php
$projectRoot = dirname(__FILE__, 2);
require_once $projectRoot . '/config/db_config.php';

//SECCION ESPECIALIDADES (NO ESTA EN USO)
function createEspecialidad($nombre, $descripcion) {
    global $conn;
    $sql = "INSERT INTO especialidades_talleres (nombre_especialidad, descripcion_especialidad) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $nombre, $descripcion);
    return $stmt->execute();
}

function getAllEspecialidades() {
    global $conn;
    $sql = "SELECT * FROM especialidades_talleres";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getEspecialidadById($id) {
    global $conn;
    $sql = "SELECT * FROM especialidades_talleres WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function updateEspecialidad($id, $nombre, $descripcion) {
    global $conn;
    $sql = "UPDATE especialidades_talleres SET nombre_especialidad = ?, descripcion_especialidad = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $nombre, $descripcion, $id);
    return $stmt->execute();
}

function deleteEspecialidad($id) {
    global $conn;
    $sql = "DELETE FROM especialidades_talleres WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}


//CATEGORIA PERSONAS
function createCategoriaPersona($nombre, $descripcion) {
    global $conn;
    $sql = "INSERT INTO categoria_persona (nombre_categoria, descripcion_categoria) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $nombre, $descripcion);
    return $stmt->execute();
}

function getAllCategoriasPersona() {
    global $conn;
    $sql = "SELECT * FROM categoria_persona";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getCategoriaPersonaById($id) {
    global $conn;
    $sql = "SELECT * FROM categoria_persona WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function updateCategoriaPersona($id, $nombre, $descripcion) {
    global $conn;
    $sql = "UPDATE categoria_persona SET nombre_categoria = ?, descripcion_categoria = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $nombre, $descripcion, $id);
    return $stmt->execute();
}

function deleteCategoriaPersona($id) {
    global $conn;
    $sql = "DELETE FROM categoria_persona WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}


//SECCION SOLICITUDES DE PEDIDOS DE REPARACIONES
function createSolicitudPedidoReparacion($data) {
    global $conn;
    $conn->begin_transaction();
    $estado_inicial = 1; 

    try {
        $sql = "INSERT INTO solicitudes_pedidos_reparaciones (grupo_funcion, nivel_urgencia, nombre_completo_conductor, nombre_completo_mantenimiento, numero_solicitud, numero_unidad, observaciones, solicitante, ubicacion, fecha_solicitud) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiiiiissss", 
            $data['grupo_funcion'],
            $data['nivel_urgencia'],
            $data['nombre_completo_conductor'],
            $data['nombre_completo_mantenimiento'],
            $data['numero_solicitud'],
            $data['numero_unidad'],
            $data['observaciones'],
            $data['solicitante'],
            $data['ubicacion'],
            $data['fecha_solicitud']
        );

        $stmt->execute();
        $solicitud_id = $conn->insert_id;
        
        if (isset($data['especialidades']) && is_array($data['especialidades'])) {
            foreach ($data['especialidades'] as $especialidad_id) {
                $sql = "INSERT INTO solicitud_especialidades (solicitud_id, especialidad_id) VALUES (?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ii", $solicitud_id, $especialidad_id);
                $stmt->execute();
            }
        }

        // Insertar el estado inicial en el historial
        $sql = "INSERT INTO historial_estados_solicitud (solicitud_id, personal_id) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $solicitud_id, $data['solicitante']);
        $stmt->execute();  
        
        $conn->commit();
        return true;

    } catch (Exception $e) {
        $conn->rollback();
        error_log("Error en createSolicitudPedidoReparacion: " . $e->getMessage());
        return false;
    }
}

function getAllSolicitudesPedidosReparaciones() {
    global $conn;
    $sql = "SELECT spr.*, p1.nombre_personal AS conductor, p2.nombre_personal AS mantenimiento, u.codigo_interno AS unidad, p3.nombre_personal AS solicitante, l.nombre_localidad AS ubicacion
            FROM solicitudes_pedidos_reparaciones spr
            LEFT JOIN personal p1 ON spr.nombre_completo_conductor = p1.id
            LEFT JOIN personal p2 ON spr.nombre_completo_mantenimiento = p2.id
            LEFT JOIN unidades u ON spr.numero_unidad = u.id
            LEFT JOIN personal p3 ON spr.solicitante = p3.id
            LEFT JOIN localidades l ON spr.ubicacion = l.id";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getSolicitudPedidoReparacionById($id) {
    global $conn;
    $sql = "SELECT * FROM solicitudes_pedidos_reparaciones WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}


function updateSolicitudPedidoReparacion($id, $data) {
    global $conn;
    $conn->begin_transaction();

    try {
        $sql = "UPDATE solicitudes_pedidos_reparaciones SET 
                grupo_funcion = ?, 
                nivel_urgencia = ?, 
                nombre_completo_conductor = ?, 
                nombre_completo_mantenimiento = ?, 
                numero_solicitud = ?, 
                numero_unidad = ?, 
                observaciones = ?, 
                solicitante = ?,
                ubicacion = ?
                WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiiiiiisii", 
            $data['grupo_funcion'],
            $data['nivel_urgencia'],
            $data['nombre_completo_conductor'],
            $data['nombre_completo_mantenimiento'],
            $data['numero_solicitud'],
            $data['numero_unidad'],
            $data['observaciones'],
            $data['solicitante'],
            $data['ubicacion'],
            $id
        );
        $stmt->execute();
        
        // Eliminar especialidades existentes
        $sql = "DELETE FROM solicitud_especialidades WHERE solicitud_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        
        // Insertar nuevas especialidades
        foreach ($data['especialidades'] as $especialidad_id) {
            $sql = "INSERT INTO solicitud_especialidades (solicitud_id, especialidad_id) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $id, $especialidad_id);
            $stmt->execute();
        }
        
        $conn->commit();
        return true;
    } catch (Exception $e) {
        $conn->rollback();
        return false;
    }
}


function deleteSolicitudPedidoReparacion($id) {
    global $conn;
    $sql = "DELETE FROM solicitudes_pedidos_reparaciones WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

function getEspecialidadesBySolicitudId($solicitud_id) {
    global $conn;
    $sql = "SELECT et.nombre_especialidad 
            FROM solicitud_especialidades se
            JOIN especialidades_talleres et ON se.especialidad_id = et.id
            WHERE se.solicitud_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $solicitud_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return array_column($result->fetch_all(MYSQLI_ASSOC), 'nombre_especialidad');
}

//ESTADO DE SOLICITUDES
function actualizarEstadoSolicitud($solicitud_id, $nuevo_estado_id, $personal_id) {
    global $conn;
    $conn->begin_transaction();

    try {
        // Actualizar el estado actual en la tabla de solicitudes
        $sql = "UPDATE solicitudes_pedidos_reparaciones SET estado_actual_id = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $nuevo_estado_id, $solicitud_id);
        $stmt->execute();

        // Insertar el nuevo estado en el historial
        $sql = "INSERT INTO historial_estados_solicitud (solicitud_id, estado_id, personal_id, fecha_cambio) VALUES (?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iii", $solicitud_id, $nuevo_estado_id, $personal_id);
        $stmt->execute();

        $conn->commit();
        return true;
    } catch (Exception $e) {
        $conn->rollback();
        return false;
    }
}
function getAllEstados() {
    global $conn;
    $sql = "SELECT * FROM estados_solicitud";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getEstadoActual($solicitud_id) {
    global $conn;
    $sql = "SELECT es.id, es.nombre_estado 
            FROM solicitudes_pedidos_reparaciones spr
            JOIN estados_solicitud es ON spr.estado_actual_id = es.id
            WHERE spr.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $solicitud_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}



// Funciones para obtener datos de las tablas relacionadas
function getAllEspecialidadesTalleres() {
    global $conn;
    $sql = "SELECT id, nombre_especialidad FROM especialidades_talleres";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getAllPersonal() {
    global $conn;
    $sql = "SELECT * FROM personal";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getAllUnidades() {
    global $conn;
    $sql = "SELECT id, codigo_interno, descripcion, habilitado, numero_unidad FROM unidades";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getAllLocalidades() {
    global $conn;
    $sql = "SELECT * FROM localidades ORDER BY nombre_localidad";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}


//UNIDADES
function createUnidad($codigo_interno, $descripcion, $numero_unidad) {
    global $conn;
    $habilitado = 1; // Por defecto, la unidad se crea habilitada
    $sql = "INSERT INTO unidades (codigo_interno, descripcion, habilitado, numero_unidad) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssii", $codigo_interno, $descripcion, $habilitado, $numero_unidad);
    return $stmt->execute();
}

function getUnidadById($id) {
    global $conn;
    $sql = "SELECT * FROM unidades WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function updateUnidad($id, $codigo_interno, $descripcion, $numero_unidad) {
    global $conn;
    $sql = "UPDATE unidades SET codigo_interno = ?, descripcion = ?, numero_unidad = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssiii", $codigo_interno, $descripcion, $habilitado, $numero_unidad, $id);
    return $stmt->execute();
}

function deleteUnidad($id) {
    global $conn;
    $sql = "DELETE FROM unidades WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}


//CONTROL DE USUARIOS 
function getUserByUsername($username) {
    global $conn;
    $sql = "SELECT * FROM usuarios WHERE nombre_usuario = ? AND habilitado = 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}


// USUARIOS
function getAllUsers() {
    global $conn;
    $sql = "SELECT * FROM usuarios";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function createUser($nombre_usuario, $descripcion_usuario, $habilitado, $rol_id, $password) {
    global $conn;
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO usuarios (nombre_usuario, descripcion_usuario, habilitado, rol_id, password) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssiis", $nombre_usuario, $descripcion_usuario, $habilitado, $rol_id, $hashed_password);
    return $stmt->execute();
}

function getUserById($id) {
    global $conn;
    $sql = "SELECT * FROM usuarios WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function updateUser($id, $nombre_usuario, $descripcion_usuario, $habilitado, $rol_id, $password = null) {
    global $conn;
    if ($password) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "UPDATE Usuarios SET nombre_usuario = ?, descripcion_usuario = ?, habilitado = ?, rol_id = ?, password = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssiisi", $nombre_usuario, $descripcion_usuario, $habilitado, $rol_id, $hashed_password, $id);
    } else {
        $sql = "UPDATE Usuarios SET nombre_usuario = ?, descripcion_usuario = ?, habilitado = ?, rol_id = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssiii", $nombre_usuario, $descripcion_usuario, $habilitado, $rol_id, $id);
    }
    return $stmt->execute();
}

function deleteUser($id) {
    global $conn;
    $sql = "DELETE FROM usuarios WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

//HORARIOS TERMINALES INTERURBANOS
// Crear un nuevo horario interurbano
function createHorarioInterurbano($terminal_salida, $hora_salida, $terminal_llegada, $hora_llegada) {
    global $conn;
    $sql = "INSERT INTO horarios_interurbanos (terminal_salida, hora_salida, terminal_llegada, hora_llegada, habilitado) VALUES (?, ?, ?, ?, 1)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isis", $terminal_salida, $hora_salida, $terminal_llegada, $hora_llegada);
    return $stmt->execute();
}

// Obtener un horario interurbano por ID
function getHorarioInterurbanoById($id) {
    global $conn;
    $sql = "SELECT * FROM horarios_interurbanos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// Actualizar un horario interurbano
function updateHorarioInterurbano($id, $terminal_salida, $hora_salida, $terminal_llegada, $hora_llegada, $habilitado) {
    global $conn;
    $sql = "UPDATE horarios_interurbanos SET terminal_salida = ?, hora_salida = ?, terminal_llegada = ?, hora_llegada = ?, habilitado = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isisii", $terminal_salida, $hora_salida, $terminal_llegada, $hora_llegada, $habilitado, $id);
    return $stmt->execute();
}

// Eliminar un horario interurbano
function deleteHorarioInterurbano($id) {
    global $conn;
    $sql = "DELETE FROM horarios_interurbanos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

// Obtener todas las terminales
function getAllTerminales() {
    global $conn;
    $sql = "SELECT * FROM terminales";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Obtener todas las líneas
function getAllLineas() {
    global $conn;
    $sql = "SELECT * FROM lineas WHERE habilitado = 1";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getLineasPorTerminal($terminal_id) {
    global $conn;
    $sql = "SELECT DISTINCT l.* FROM lineas l
            JOIN horarios_interurbanos hi ON l.id = hi.linea_id
            WHERE hi.terminal_salida = ? OR hi.terminal_llegada = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $terminal_id, $terminal_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function createHorariosInterurbanos($data) {
    global $conn;
    $sql = "INSERT INTO horarios_interurbanos (linea_id, terminal_salida, terminal_llegada, hora_salida, hora_llegada) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    $conn->begin_transaction();

    try {
        foreach ($data as $horario) {
            $stmt->bind_param("iiiss", $horario['linea_id'], $horario['terminal_salida'], $horario['terminal_llegada'], $horario['hora_salida'], $horario['hora_llegada']);
            $stmt->execute();
        }
        $conn->commit();
        return true;
    } catch (Exception $e) {
        $conn->rollback();
        return false;
    }
}

function getAllHorariosInterurbanos() {
    global $conn;
    $sql = "SELECT hi.*, l.numero AS numero_linea, l.descripcion AS descripcion_linea,
                   t1.nombre_terminal AS terminal_salida_nombre, 
                   t2.nombre_terminal AS terminal_llegada_nombre
            FROM horarios_interurbanos hi
            JOIN lineas l ON hi.linea_id = l.id
            JOIN terminales t1 ON hi.terminal_salida = t1.id
            JOIN terminales t2 ON hi.terminal_llegada = t2.id
            ORDER BY l.numero, hi.terminal_salida, hi.hora_salida";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}


//PERSONAL
function getAllPersonales() {
    global $conn;
    $sql = "SELECT p.*, cp.nombre_categoria FROM personal p 
            LEFT JOIN categoria_persona cp ON p.categoria = cp.id";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function createPersonal($codigo, $categoria, $nombre_personal, $legajo, $tarjeta, $vencimiento_licencia, $habilitado) {
    global $conn;
    $sql = "INSERT INTO personal (codigo, categoria, nombre_personal, legajo, tarjeta, vencimiento_licencia, habilitado) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iissssi", $codigo, $categoria, $nombre_personal, $legajo, $tarjeta, $vencimiento_licencia, $habilitado);
    return $stmt->execute();
}

function getPersonalById($id) {
    global $conn;
    $sql = "SELECT * FROM personal WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function updatePersonal($id, $codigo, $categoria, $nombre_personal, $legajo, $tarjeta, $vencimiento_licencia, $habilitado) {
    global $conn;
    $sql = "UPDATE personal SET codigo = ?, categoria = ?, nombre_personal = ?, legajo = ?, tarjeta = ?, vencimiento_licencia = ?, habilitado = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iissssii", $codigo, $categoria, $nombre_personal, $legajo, $tarjeta, $vencimiento_licencia, $habilitado, $id);
    return $stmt->execute();
}

function deletePersonal($id) {
    global $conn;
    $sql = "DELETE FROM personal WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}


// CRUD para Usuarios
function createUsuario($nombre, $descripcion, $password, $rol_id, $habilitado) {
    global $conn;
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO usuarios (nombre_usuario, descripcion_usuario, password, rol_id, habilitado) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssis", $nombre, $descripcion, $hashed_password, $rol_id, $habilitado);
    return $stmt->execute();
}

function getAllUsuarios() {
    global $conn;
    $sql = "SELECT u.*, r.nombre_rol FROM usuarios u JOIN roles_usuarios r ON u.rol_id = r.id";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getUsuarioById($id) {
    global $conn;
    $sql = "SELECT * FROM usuarios WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function updateUsuario($id, $nombre, $descripcion, $rol_id, $habilitado) {
    global $conn;
    $sql = "UPDATE usuarios SET nombre_usuario = ?, descripcion_usuario = ?, rol_id = ?, habilitado = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssiii", $nombre, $descripcion, $rol_id, $habilitado, $id);
    return $stmt->execute();
}

function deleteUsuario($id) {
    global $conn;
    $sql = "DELETE FROM usuarios WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

// CRUD para Roles de Usuarios
function createRol($nombre, $habilitado, $leer, $crear, $actualizar, $eliminar) {
    global $conn;
    $sql = "INSERT INTO roles_usuarios (nombre_rol, habilitado, leer, crear, actualizar, eliminar) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("siiiii", $nombre, $habilitado, $leer, $crear, $actualizar, $eliminar);
    return $stmt->execute();
}

function getRolById($id) {
    global $conn;
    $sql = "SELECT * FROM roles_usuarios WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function updateRol($id, $nombre, $habilitado, $leer, $crear, $actualizar, $eliminar) {
    global $conn;
    $sql = "UPDATE roles_usuarios SET nombre_rol = ?, habilitado = ?, leer = ?, crear = ?, actualizar = ?, eliminar = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("siiiiii", $nombre, $habilitado, $leer, $crear, $actualizar, $eliminar, $id);
    return $stmt->execute();
}

function deleteRol($id) {
    global $conn;
    $sql = "DELETE FROM roles_usuarios WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

// CRUD para Ventanas
function createVentana($nombre, $descripcion) {
    global $conn;
    $sql = "INSERT INTO ventanas (nombre, descripcion) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $nombre, $descripcion);
    return $stmt->execute();
}

function getAllVentanas() {
    global $conn;
    $sql = "SELECT * FROM ventanas";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getVentanaById($id) {
    global $conn;
    $sql = "SELECT * FROM ventanas WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function updateVentana($id, $nombre, $descripcion) {
    global $conn;
    $sql = "UPDATE ventanas SET nombre = ?, descripcion = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $nombre, $descripcion, $id);
    return $stmt->execute();
}

function deleteVentana($id) {
    global $conn;
    $sql = "DELETE FROM ventanas WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

// CRUD para Roles_Ventanas
function createRolVentana($rol_id, $ventana_id, $leer, $crear, $actualizar, $eliminar) {
    global $conn;
    $sql = "INSERT INTO roles_ventanas (rol_id, ventana_id, leer, crear, actualizar, eliminar) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiiiii", $rol_id, $ventana_id, $leer, $crear, $actualizar, $eliminar);
    return $stmt->execute();
}

function getAllRolesVentanas() {
    global $conn;
    $sql = "SELECT rv.*, r.nombre_rol, v.nombre as nombre_ventana 
            FROM roles_ventanas rv 
            JOIN roles_usuarios r ON rv.rol_id = r.id 
            JOIN ventanas v ON rv.ventana_id = v.id";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getRolVentanaById($id) {
    global $conn;
    $sql = "SELECT * FROM roles_ventanas WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function updateRolVentana($id, $rol_id, $ventana_id, $leer, $crear, $actualizar, $eliminar) {
    global $conn;
    $sql = "UPDATE roles_ventanas SET rol_id = ?, ventana_id = ?, leer = ?, crear = ?, actualizar = ?, eliminar = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiiiiii", $rol_id, $ventana_id, $leer, $crear, $actualizar, $eliminar, $id);
    return $stmt->execute();
}

function deleteRolVentana($id) {
    global $conn;
    $sql = "DELETE FROM roles_ventanas WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}



function getAllNivelesUrgencias() {
    global $conn;
    $sql = "SELECT id, nombre_urgencia FROM niveles_urgencias";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getAllGruposFunciones() {
    global $conn;
    $sql = "SELECT id, nombre_grupo_funcion FROM grupos_funciones";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getGrupoFuncionNombre($id) {
    global $conn;
    $sql = "SELECT nombre_grupo_funcion FROM grupos_funciones WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row ? $row['nombre_grupo_funcion'] : 'N/A';
}

function getNivelUrgenciaNombre($id) {
    global $conn;
    $sql = "SELECT nombre_urgencia FROM niveles_urgencias WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row ? $row['nombre_urgencia'] : 'N/A';
}



// Funciones CRUD para países
function getAllPaises() {
    global $conn;
    $sql = "SELECT * FROM paises";
    $result = $conn->query($sql);
    $paises = array();
    while ($row = $result->fetch_assoc()) {
        $paises[$row['id']] = $row;
    }
    return $paises;
}

function getPaisById($id) {
    global $conn;
    $sql = "SELECT * FROM paises WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function createPais($data) {
    global $conn;
    $sql = "INSERT INTO paises (nombre_pais, descripcion_pais) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $data['nombre_pais'], $data['descripcion_pais']);
    return $stmt->execute();
}

function updatePais($data) {
    global $conn;
    $sql = "UPDATE paises SET nombre_pais = ?, descripcion_pais = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi",  $data['nombre_pais'], $data['descripcion_pais'], $data['id']);
    return $stmt->execute();
}

function deletePais($id) {
    global $conn;
    $sql = "DELETE FROM paises WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

// Funciones CRUD para provincias

function getAllProvincias() {
    global $conn;
    $sql = "SELECT * FROM provincias";
    $result = $conn->query($sql);
    $provincias = array();
    while ($row = $result->fetch_assoc()) {
        $provincias[$row['id']] = $row;
    }
    return $provincias;
}

function getProvinciaById($id) {
    global $conn;
    $sql = "SELECT * FROM provincias WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function createProvincia($data) {
    global $conn;
    $sql = "INSERT INTO provincias (nombre_provincia, descripcion_provincia, pais) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $data['nombre_provincia'],$data['descripcion_provincia'], $data['pais'],);
    return $stmt->execute();
}

function updateProvincia($data) {
    global $conn;
    $sql = "UPDATE provincias SET nombre_provincia = ?, descripcion_provincia = ?, pais = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssii",  $data['nombre_provincia'], $data['descripcion_provincia'],$data['pais'], $data['id']);
    return $stmt->execute();
}

function deleteProvincia($id) {
    global $conn;
    $sql = "DELETE FROM provincias WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

// Funciones CRUD para localidades

function getLocalidadById($id) {
    global $conn;
    $sql = "SELECT * FROM localidades WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function createLocalidad($data) {
    global $conn;
    $sql = "INSERT INTO localidades (nombre_localidad, descripcion_localidad, provincia) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssii",  $data['nombre_localidad'], $data['descripcion_localidad'],$data['provincia'], $data['id']);
    return $stmt->execute();
}

function updateLocalidad($id, $nombre_localidad, $descripcion_localidad, $provincia) {
    global $conn;
    $sql = "UPDATE localidades SET nombre_localidad = ?, descripcion_localidad = ?, provincia = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssii", $nombre_localidad, $descripcion_localidad, $provincia, $id);
    return $stmt->execute();
}

function deleteLocalidad($id) {
    global $conn;
    $sql = "DELETE FROM localidades WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

// Funciones CRUD para niveles de urgencia

function getNivelUrgenciaById($id) {
    global $conn;
    $sql = "SELECT * FROM niveles_urgencias WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function createNivelUrgencia($nombre_urgencia, $descripcion_urgencia) {
    global $conn;
    $sql = "INSERT INTO niveles_urgencias (nombre_urgencia, descripcion_urgencia) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $nombre_urgencia, $descripcion_urgencia);
    return $stmt->execute();
}

function updateNivelUrgencia($id, $nombre_urgencia, $descripcion_urgencia) {
    global $conn;
    $sql = "UPDATE niveles_urgencias SET nombre_urgencia = ?, descripcion_urgencia = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $nombre_urgencia, $descripcion_urgencia, $id);
    return $stmt->execute();
}

function deleteNivelUrgencia($id) {
    global $conn;
    $sql = "DELETE FROM niveles_urgencias WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}


// Crear un nuevo viaje abierto
function createViajeAbierto($chofer1, $chofer2, $unidad, $estado_viaje_abierto, $turno_registro_sistema, $turno_registro_planilla, $chofer_actual, $observaciones) {
    global $conn;
    $sql = "INSERT INTO viajes_abiertos (chofer1, chofer2, unidad, estado_viaje_abierto, turno_registro_sistema, turno_registro_planilla, chofer_actual, observaciones) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiiiiiisi", $chofer1, $chofer2, $unidad, $estado_viaje_abierto, $turno_registro_sistema, $turno_registro_planilla, $chofer_actual, $observaciones);
    return $stmt->execute();
}

// Obtener todos los viajes abiertos
function getAllViajesAbiertos() {
    global $conn;
    $sql = "SELECT va.*, 
            p1.nombre_personal AS nombre_chofer1, 
            p2.nombre_personal AS nombre_chofer2, 
            u.codigo_interno AS codigo_unidad,
            p3.nombre_personal AS nombre_chofer_actual
            FROM viajes_abiertos va
            LEFT JOIN personal p1 ON va.chofer1 = p1.id
            LEFT JOIN personal p2 ON va.chofer2 = p2.id
            LEFT JOIN unidades u ON va.unidad = u.id
            LEFT JOIN personal p3 ON va.chofer_actual = p3.id";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Obtener un viaje abierto por ID
function getViajeAbiertoById($id) {
    global $conn;
    $sql = "SELECT va.*, 
            p1.nombre_personal AS nombre_chofer1, 
            p2.nombre_personal AS nombre_chofer2, 
            u.codigo_interno AS codigo_unidad,
            p3.nombre_personal AS nombre_chofer_actual
            FROM viajes_abiertos va
            LEFT JOIN personal p1 ON va.chofer1 = p1.id
            LEFT JOIN personal p2 ON va.chofer2 = p2.id
            LEFT JOIN unidades u ON va.unidad = u.id
            LEFT JOIN personal p3 ON va.chofer_actual = p3.id
            WHERE va.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// Actualizar un viaje abierto
function updateViajeAbierto($id, $chofer1, $chofer2, $unidad, $estado_viaje_abierto, $turno_registro_sistema, $turno_registro_planilla, $chofer_actual, $observaciones) {
    global $conn;
    $sql = "UPDATE viajes_abiertos SET 
            chofer1 = ?, chofer2 = ?, unidad = ?, estado_viaje_abierto = ?, 
            turno_registro_sistema = ?, turno_registro_planilla = ?, 
            chofer_actual = ?, observaciones = ?
            WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiiiiiiisi", $chofer1, $chofer2, $unidad, $estado_viaje_abierto, $turno_registro_sistema, $turno_registro_planilla, $chofer_actual, $observaciones, $id);
    return $stmt->execute();
}

// Eliminar un viaje abierto
function deleteViajeAbierto($id) {
    global $conn;
    $sql = "DELETE FROM viajes_abiertos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

// Función auxiliar para obtener todos los choferes (personal)
function getAllChoferes() {
    global $conn;
    $sql = "SELECT id, nombre_personal FROM personal WHERE categoria = (SELECT id FROM categoria_persona WHERE nombre_categoria = 'Chofer')";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Función auxiliar para obtener todas las unidades
function getAllUnidadesForViajes() {
    global $conn;
    $sql = "SELECT id, codigo_interno FROM unidades WHERE habilitado = 1";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

//PAREJAS
function getAllParejasChoferes() {
    global $conn;
    $sql = "SELECT pc.id, pc.id_chofer, pc.id_chofer2, pc.pareja, pc.unidad,
                   p1.nombre_personal AS chofer1_nombre, 
                   p2.nombre_personal AS chofer2_nombre,
                   u.codigo_interno AS unidad_codigo
            FROM parejas_choferes pc
            JOIN personal p1 ON pc.id_chofer = p1.id
            JOIN personal p2 ON pc.id_chofer2 = p2.id
            JOIN unidades u ON pc.unidad = u.id";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getParejaChoferesById($id) {
    global $conn;
    $sql = "SELECT * FROM parejas_choferes WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function createParejaChoferes($data) {
    global $conn;
    $sql = "INSERT INTO parejas_choferes (id_chofer, id_chofer2, pareja, unidad) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiii", $data['chofer_id'], $data['chofer2_id'], $data['pareja'], $data['unidad']);
    return $stmt->execute();
}

function updateParejaChoferes($id, $data) {
    global $conn;
    $sql = "UPDATE parejas_choferes SET id_chofer = ?, id_chofer2 = ?, pareja = ?, unidad = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiiii", $data['id_chofer'], $data['id_chofer2'], $data['pareja'], $data['unidad'], $id);
    return $stmt->execute();
}

function deleteParejaChoferes($id) {
    global $conn;
    $sql = "DELETE FROM parejas_choferes WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}


// TURNOS DE PAREJAS DE CHOFERES
function getAllTurnosParejasChoferes() {
    global $conn;
    $sql = "SELECT tpc.*, pc.id_chofer as pc_chofer_id, pc.id_chofer2 as pc_chofer2_id, 
                   p1.nombre_personal AS chofer1_nombre, p2.nombre_personal AS chofer2_nombre,
                   t1.nombre AS turno1_nombre, t2.nombre AS turno2_nombre
            FROM turnos_parejas_choferes tpc
            LEFT JOIN parejas_choferes pc ON tpc.pareja_id = pc.id
            LEFT JOIN personal p1 ON pc.id_chofer = p1.id
            LEFT JOIN personal p2 ON pc.id_chofer2 = p2.id
            LEFT JOIN turnos t1 ON tpc.turno = t1.id
            LEFT JOIN turnos t2 ON tpc.turno2 = t2.id
            ORDER BY tpc.fecha DESC";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getTurnoParejasChoferesById($id) {
    global $conn;
    $sql = "SELECT tpc.*, pc.id_chofer, pc.id_chofer2
            FROM turnos_parejas_choferes tpc
            JOIN parejas_choferes pc ON tpc.pareja_id = pc.id
            WHERE tpc.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function createTurnoParejasChoferes($data) {
    global $conn;
    $sql = "INSERT INTO turnos_parejas_choferes (pareja_id, chofer_id, turno, chofer2_id, turno2, descripcion, fecha) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiiiiss", $data['pareja_id'], $data['chofer_id'], $data['turno'], $data['chofer2_id'], $data['turno2'], $data['descripcion'], $data['fecha']);
    return $stmt->execute();
}

function updateTurnoParejasChoferes($id, $data) {
    global $conn;
    $sql = "UPDATE turnos_parejas_choferes 
            SET pareja_id = ?, chofer_id = ?, turno = ?, chofer2_id = ?, turno2 = ?, descripcion = ?, habilitado = ?, fecha = ? 
            WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiiiiissi", $data['pareja_id'], $data['chofer_id'], $data['turno'], $data['chofer2_id'], $data['turno2'], $data['descripcion'], $data['habilitado'], $data['fecha'], $id);
    return $stmt->execute();
}

function deleteTurnoParejasChoferes($id) {
    global $conn;
    $sql = "DELETE FROM turnos_parejas_choferes WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

function getAllParejas() {
    global $conn;
    $sql = "SELECT pc.id, CONCAT(p1.nombre_personal, ' - ', p2.nombre_personal) AS nombre_pareja
            FROM parejas_choferes pc
            JOIN personal p1 ON pc.id_chofer = p1.id
            JOIN personal p2 ON pc.id_chofer2 = p2.id";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getAllTurnos() {
    global $conn;
    $sql = "SELECT id, nombre FROM turnos";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}