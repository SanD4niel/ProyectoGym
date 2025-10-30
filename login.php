<?php
// Permitir solicitudes de origen cruzado (necesario para la app móvil)
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

include("../conexion.php"); // Subimos un nivel para encontrar la conexión

// Leer los datos JSON enviados desde la app
$data = json_decode(file_get_contents("php://input"));

// Validar que los datos no estén vacíos
if (empty($data->usuario) || empty($data->contraseña)) {
    http_response_code(400); // Bad Request
    echo json_encode(["status" => "error", "message" => "Usuario y contraseña requeridos."]);
    exit();
}

$usuario = $data->usuario;
$contraseña = md5($data->contraseña); // Usamos el mismo md5 que en el panel

// Prevenir Inyección SQL
$stmt = $conn->prepare("SELECT id, nombre FROM miembros WHERE usuario_app = ? AND contraseña_app = ?");
$stmt->bind_param("ss", $usuario, $contraseña);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $miembro = $result->fetch_assoc();
    
    // Éxito: Enviar datos del miembro
    http_response_code(200);
    echo json_encode([
        "status" => "success",
        "miembro_id" => $miembro['id'],
        "nombre" => $miembro['nombre']
    ]);
} else {
    // Error: Credenciales incorrectas
    http_response_code(401); // Unauthorized
    echo json_encode(["status" => "error", "message" => "Credenciales incorrectas."]);
}

$conn->close();
?>