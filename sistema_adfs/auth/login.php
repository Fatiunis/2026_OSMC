<?php
// Habilitar CORS (IMPORTANTE: sin redirección)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: POST, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");
    http_response_code(204);
    exit;
}

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header('Content-Type: application/json; charset=utf-8');

include(__DIR__ . "/../db.php");


$input = file_get_contents("php://input");
// Guardar entrada cruda
//file_put_contents("debug.txt", "INPUT CRUDO:\n" . $input . "\n\n", FILE_APPEND);

$data = json_decode($input, true);
header('Content-Type: application/json; charset=utf-8');

if (!isset($data['correo']) || !isset($data['password'])) {
    echo json_encode(["success" => false, "message" => "Datos incompletos"]);
    exit;
}

$correo = $data['correo'];
$password = $data['password'];

$sql = "SELECT * FROM usuarios WHERE correo = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $correo);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $usuario = $result->fetch_assoc();

    if (password_verify($password, $usuario['contrasena'])) {
        if ($usuario['estado'] == 1) {
            echo json_encode([
                "success" => true,
                "usuario" => [
                    "id" => $usuario['id'],
                    "correo" => $usuario['correo'],
                    "rol" => $usuario['rol']
                ]
            ]);
        } else {
            echo json_encode(["success" => false, "message" => "Usuario inactivo"]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Contraseña incorrecta"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Usuario no encontrado"]);
}

$conn->close();
?>
