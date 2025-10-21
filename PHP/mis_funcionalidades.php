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


$query = "SELECT id, titulo, resumen, estado, ambiente, fecha_creacion FROM solicitudes_funcionalidad WHERE autor_rut = ?";
$stmt = $conexion->prepare($query);
$stmt->bind_param('s', $id_usuario);
$stmt->execute();
$stmt->bind_result($id, $titulo, $resumen, $estado, $ambiente, $fecha_creacion);
    while ($stmt->fetch()) {
        $rows[] = [
            'id' => $id,
            'titulo' => $titulo,
            'ambiente' => $ambiente,
            'resumen' => $resumen,
            'estado' => $estado,
            'fecha_creacion' => $fecha_creacion
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
    <link rel="stylesheet" href="/BD-Tarea2/CSS/style.css">
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
      <a href="main.php" title="Inicio">Inicio</a>
      <a href="#" title="Crear solicitudes">Crear solicitud</a>

      <?php if ($rol === 'ingeniero'): ?>
        <!-- Solo Ingeniero -->
        <a href="#" title="Todas las funcionalidades">Funcionalidades (todas)</a>
        <a href="#" title="Todos los errores">Errores (todas)</a>
        <a href="#" title="Asignadas a mí">Asignadas a mí</a>
      <?php endif; ?>

      <?php if ($rol === 'usuario'): ?>
        <!-- Solo Usuario -->
        <a href="mis_funcionalidades.php" title="Mis funcionalidades">Mis funcionalidades</a>
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
                <th>Ambiente</th>
                <th>Resumen</th>
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
                    <td><?= htmlspecialchars($row['ambiente']) ?></td>
                    <td><?= htmlspecialchars($row['resumen']) ?></td>
                    <td><?= htmlspecialchars($row['estado']) ?></td>
                    <td><?= htmlspecialchars($row['fecha_creacion']) ?></td>
                </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>