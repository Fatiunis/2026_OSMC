<?php
$conn = new mysqli(
  getenv("DB_HOST"),
  getenv("DB_USER"),
  getenv("DB_PASS"),
  getenv("DB_NAME"),
  getenv("DB_PORT")
);
$conn->set_charset("utf8");

$correo = "test@ejemplo.com";
$passPlano = "123456";
$hash = password_hash($passPlano, PASSWORD_DEFAULT);

$conn->query("DELETE FROM usuarios WHERE correo='" . $conn->real_escape_string($correo) . "'");

$stmt = $conn->prepare("INSERT INTO usuarios (nombre, correo, contrasena, rol, estado) VALUES (?, ?, ?, ?, 1)");
$nombre = "Test User";
$rol = "admin";
$stmt->bind_param("ssss", $nombre, $correo, $hash, $rol);
$stmt->execute();

echo "Seed ok\n";