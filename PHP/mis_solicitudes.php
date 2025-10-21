<?php

session_start();
include('conexion.php'); 
if (!isset($_SESSION['id_usuario'])) {
    header('Location: index.php');
    exit();
}

$id_usuario = $_SESSION['id_usuario'];


$query = "SELECT id, titulo, descripcion, estado, fecha_publicacion FROM solicitudes_error WHERE autor_rut = ?";
$stmt = $conexion->prepare($query);
$stmt->bind_param('s', $id_usuario);
$stmt->execute();
$stmt->bind_result($id, $titulo, $descripcion, $estado, $fecha_publicacion);
    while ($stmt->fetch()) {
        $rows[] = [
            'id' => $id,
            'titulo' => $titulo,
            'descripcion' => $descripcion,
            'estado' => $estado,
            'fecha_publicacion' => $fecha_publicacion
        ];
    }

$stmt->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Solicitudes</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        header {
            background: #2d89ef;
            color: white;
            padding: 1rem;
            text-align: center;
        }
        .container {
            max-width: 800px;
            margin: 30px auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #f0f0f0;
        }
        tr:hover {
            background-color: #f9f9f9;
        }
        a.btn {
            display: inline-block;
            padding: 8px 12px;
            background: #2d89ef;
            color: white;
            border-radius: 4px;
            text-decoration: none;
            margin-top: 10px;
        }
        a.btn:hover {
            background: #1a5fb4;
        }
    </style>
</head>
<body>
<header>
    <h1>Mis Solicitudes</h1>
</header>
<div class="container">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Título</th>
                <th>Descripción</th>
                <th>Estado</th>
                <th>Fecha</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($rows)): ?>
                <tr>
                    <td colspan="5" style="text-align:center;">No hay solicitudes registradas.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($rows as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['id']) ?></td>
                    <td><?= htmlspecialchars($row['titulo']) ?></td>
                    <td><?= htmlspecialchars($row['descripcion']) ?></td>
                    <td><?= htmlspecialchars($row['estado']) ?></td>
                    <td><?= htmlspecialchars($row['fecha_publicacion']) ?></td>
                </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    <a class="btn" href="usuario_inicio.php">Volver al inicio</a>
</div>
</body>
</html>
