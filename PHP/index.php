<?php
session_start();
include 'conexion.php';

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $rut = isset($_POST['rut']) ? trim($_POST['rut']) : '';

    // Registro
    if (isset($_POST['registrar'])) {
        if ($rut === '') {
            $mensaje = "RUT vacío.";
        } else {
            $stmt = $conexion->prepare("SELECT 1 FROM usuarios WHERE rut = ? LIMIT 1");
            $stmt->bind_param('s', $rut);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $mensaje = "usuario registrado";
                $stmt->close();
            } else {
                $stmt->close();
                $ins = $conexion->prepare("INSERT INTO usuarios (rut) VALUES (?)");
                $ins->bind_param('s', $rut);
                if ($ins->execute()) {
                    $mensaje = "Registro exitoso. Ahora puedes iniciar sesión.";
                } else {
                    $mensaje = "Error al registrar: " . htmlspecialchars($conexion->error);
                }
                $ins->close();
            }
        }
    }

    // Login
    if (isset($_POST['login'])) {
        if ($rut === '') {
            $mensaje = "RUT vacío.";
        } else {
            // Buscar en usuarios
            $stmt = $conexion->prepare("SELECT 1 FROM usuarios WHERE rut = ? LIMIT 1");
            $stmt->bind_param('s', $rut);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $_SESSION['id_usuario'] = $rut;
                $_SESSION['tipo_usuario'] = 'usuario';
                $stmt->close();
                header("Location: main.php");
                exit();
            }
            $stmt->close();

            // Buscar en ingenieros 
            $stmt = $conexion->prepare("SELECT 1 FROM ingenieros WHERE rut = ? LIMIT 1");
            $stmt->bind_param('s', $rut);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $_SESSION['id_usuario'] = $rut;
                $_SESSION['tipo_usuario'] = 'ingeniero';
                $stmt->close();
                header("Location: main.php");
                exit();
            }
            $stmt->close();

            $mensaje = "usuario no existente";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login y Registro</title>
</head>
<body>
    <h2>Login</h2>
    <form method="post">
        RUT: <input type="text" name="rut" required><br>
        <button type="submit" name="login">Entrar</button>
    </form>
    <h2>Registro</h2>
    <form method="post">
        RUT: <input type="text" name="rut" required><br>
        <button type="submit" name="registrar">Registrar</button>
    </form>
    <p style="color:red;"><?php echo htmlspecialchars($mensaje); ?></p>
</body>
</html>