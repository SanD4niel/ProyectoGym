<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include("../conexion.php");

// Esperamos el ID del miembro (ej. /api/status.php?miembro_id=15)
if (empty($_GET['miembro_id'])) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "ID de miembro requerido."]);
    exit();
}

$miembro_id = intval($_GET['miembro_id']);
$fecha_actual = date("Y-m-d");

// Prevenir Inyección SQL
$stmt = $conn->prepare("SELECT nombre, plan, fecha_inicio, fecha_vencimiento, usuario_app FROM miembros WHERE id = ?");
$stmt->bind_param("i", $miembro_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $miembro = $result->fetch_assoc();
    
    // Determinar el estado (Activo o Vencido)
    $estado = ($miembro['fecha_vencimiento'] >= $fecha_actual) ? "Activo" : "Vencido";

    // Generar la cadena de datos para el QR
    // Esta es la información que la app convertirá en un QR.
    // Usar el 'usuario_app' es una excelente idea porque es único.
    $qr_data = $miembro['usuario_app'];

    http_response_code(200);
    echo json_encode([
        "status" => "success",
        "nombre" => $miembro['nombre'],
        "plan" => $miembro['plan'],
        "fecha_inicio" => $miembro['fecha_inicio'],
        "fecha_vencimiento" => $miembro['fecha_vencimiento'],
        "estado" => $estado,
        "qr_data" => $qr_data // La app usará esto para generar el QR
    ]);
} else {
    http_response_code(404); // Not Found
    echo json_encode(["status" => "error", "message" => "Miembro no encontrado."]);
}

$conn->close();
?>