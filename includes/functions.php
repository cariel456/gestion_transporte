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
    $sql = "INSERT INTO solicitudes_pedidos_reparaciones (especialidades, grupo_funcion, nivel_urgencia, nombre_completo_conductor, nombre_completo_mantenimiento, numero_solicitud, numero_unidad, observaciones, solicitante, ubicacion, fecha_solicitud) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssiiiiisiis", $data['especialidades'], $data['grupo_funcion'], $data['nivel_urgencia'], $data['nombre_completo_conductor'], $data['nombre_completo_mantenimiento'], $data['numero_solicitud'], $data['numero_unidad'], $data['observaciones'], $data['solicitante'], $data['ubicacion'], $data['fecha_solicitud']);
    return $stmt->execute();
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
    $sql = "UPDATE solicitudes_pedidos_reparaciones SET 
            especialidades = ?, 
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
    $stmt->bind_param("ssiiiiisiii", 
        $data['especialidades'],
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
    return $stmt->execute();
}


function deleteSolicitudPedidoReparacion($id) {
    global $conn;
    $sql = "DELETE FROM solicitudes_pedidos_reparaciones WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    return $stmt->execute();
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
    $sql = "SELECT id, nombre_personal FROM personal";
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
    $sql = "SELECT id, nombre_localidad FROM localidades ORDER BY nombre_localidad";
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

function updateUnidad($id, $codigo_interno, $descripcion, $habilitado, $numero_unidad) {
    global $conn;
    $sql = "UPDATE unidades SET codigo_interno = ?, descripcion = ?, habilitado = ?, numero_unidad = ? WHERE id = ?";
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
    $sql = "SELECT u.*, r.leer, r.crear, r.actualizar, r.eliminar 
            FROM usuarios u 
            JOIN roles_usuarios r ON u.rol_id = r.id 
            WHERE u.nombre_usuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}


//ROLES DE USUARIOS
function getAllRoles() {
    global $conn;
    $sql = "SELECT * FROM roles_usuarios";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function createRole($nombre_rol, $leer, $crear, $actualizar, $eliminar) {
    global $conn;
    $sql = "INSERT INTO roles_usuarios (nombre_rol, leer, crear, actualizar, eliminar) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("siiii", $nombre_rol, $leer, $crear, $actualizar, $eliminar);
    return $stmt->execute();
}

function getRoleById($id) {
    global $conn;
    $sql = "SELECT * FROM roles_usuarios WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function updateRole($id, $nombre_rol, $leer, $crear, $actualizar, $eliminar) {
    global $conn;
    $sql = "UPDATE roles_usuarios SET nombre_rol = ?, leer = ?, crear = ?, actualizar = ?, eliminar = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("siiiis", $nombre_rol, $leer, $crear, $actualizar, $eliminar, $id);
    return $stmt->execute();
}

function deleteRole($id) {
    global $conn;
    $sql = "DELETE FROM roles_usuarios WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    return $stmt->execute();
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
// Obtener todos los horarios interurbanos
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