
<?php
require 'db.php';

$email = 'admin@kanban.local';
$clave_ingresada = 'admin123';

try {
    echo "<h3>🔍 Verificando conexión a base de datos...</h3>";
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch();

    if ($usuario) {
        echo "✅ Usuario encontrado: <strong>{$usuario['nombre']}</strong><br>";
        echo "Hash almacenado: <code>{$usuario['clave']}</code><br>";

        if (password_verify($clave_ingresada, $usuario['clave'])) {
            echo "<p style='color:green;'>✅ La contraseña es correcta. Login OK.</p>";
        } else {
            echo "<p style='color:red;'>❌ La contraseña es INCORRECTA.</p>";
        }

    } else {
        echo "<p style='color:red;'>❌ No se encontró el usuario con email: $email</p>";
    }
} catch (PDOException $e) {
    echo "<p style='color:red;'>❌ Error en la conexión o consulta: " . $e->getMessage() . "</p>";
}
?>
