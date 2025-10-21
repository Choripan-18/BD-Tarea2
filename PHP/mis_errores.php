<?php

session_start();
if (!isset($_SESSION['id_usuario'])) {
    header('Location: index.php');
    exit();
}
include('conexion.php'); 

$id_usuario = $_SESSION['id_usuario'];
$rut = $_SESSION['id_usuario'];
$rol = $_SESSION['tipo_usuario'];


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
  <header class="nav">
    <div class="left" style="display:flex;align-items:center;gap:12px">
      <div class="brand">ZeroPressure</div>
      <span class="chip">RUT: <?php echo htmlspecialchars($rut); ?></span>
      <span class="chip">ROL: <?php echo htmlspecialchars($rol); ?></span>
    </div>

    <nav class="links">
      <!-- Ambos roles -->
      <a href="#" title="Búsqueda">Búsqueda</a>
      <a href="#" title="Crear solicitudes">Crear solicitud</a>

      <?php if ($rol === 'ingeniero'): ?>
        <!-- Solo Ingeniero -->
        <a href="#" title="Todas las funcionalidades">Funcionalidades (todas)</a>
        <a href="#" title="Todos los errores">Errores (todas)</a>
        <a href="#" title="Asignadas a mí">Asignadas a mí</a>
      <?php endif; ?>

      <?php if ($rol === 'usuario'): ?>
        <!-- Solo Usuario -->
        <a href="#" title="Mis funcionalidades">Mis funcionalidades</a>
        <a href="mis_errores.php" title="Mis errores">Mis errores</a>
      <?php endif; ?>
    </nav>

    <div class="right">
      <form method="post" action="logout.php">
        <button class="logout" type="submit" name="logout">Cerrar sesión</button>
      </form>
    </div>
  </header>
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
</div>
</body>
</html>
