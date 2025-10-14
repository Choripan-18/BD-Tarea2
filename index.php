<?php
session_start();
include 'conexion.php';

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Registro
    if (isset($_POST['registrar'])) {
        $rut = $_POST['rut'];
        $email = $_POST['email'];

        // Verificar si el usuario ya existe
        $sql = "SELECT * FROM usuarios WHERE rut='$rut'";
        $result = $conexion->query($sql);
        if ($result->num_rows > 0) {
            $mensaje = "usuario registrado";
        } else {
            // Insertar nuevo usuario
            $sql = "INSERT INTO usuarios (rut, email) VALUES ('$rut', '$email')";
            if ($conexion->query($sql) === TRUE) {
                $mensaje = "Registro exitoso. Ahora puedes iniciar sesión.";
            } else {
                $mensaje = "Error al registrar: " . $conexion->error;
            }
        }
    }

    // Login
    if (isset($_POST['login'])) {
        $rut = $_POST['rut'];
        $email = $_POST['email'];

        $sql = "SELECT * FROM usuarios WHERE rut='$rut' AND email='$email'";
        $result = $conexion->query($sql);
        if ($result->num_rows > 0) {
            $_SESSION['usuario'] = $rut;
            header("Location: principal.php");
            exit();
        } else {
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
        Email: <input type="email" name="email" required><br>
        <button type="submit" name="login">Entrar</button>
    </form>
    <h2>Registro</h2>
    <form method="post">
        RUT: <input type="text" name="rut" required><br>
        Email: <input type="email" name="email" required><br>
        <button type="submit" name="registrar">Registrar</button>
    </form>
    <p style="color:red;"><?php echo $mensaje; ?></p>
</body>
</html>