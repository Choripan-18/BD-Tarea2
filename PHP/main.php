<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header('Location: index.php');
    exit();
}


include('conexion.php'); 
$rut = $_SESSION['id_usuario'];
$rol = $_SESSION['tipo_usuario'];
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Inicio</title>
  <link rel="stylesheet" href="/BD-Tarea2/CSS/style.css">
</head>
<body>
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

  <main class="wrap">
    <div class="card">
      <h1>Bienvenido</h1>
      <p class="muted">Usa la barra superior para navegar por las secciones. Aún no se han creado las páginas de cada sección; los enlaces son de ejemplo y se activarán cuando implementemos cada vista.</p>
      <?php if ($rol === 'DESCONOCIDO'): ?>
        <p style="color:#dc2626"><strong>Atención:</strong> tu RUT no fue encontrado en <code>ingenieros</code> ni en <code>usuarios</code>. Verifica los datos de tu base o el proceso de login.</p>
      <?php endif; ?>
    </div>
  </main>
</body>
</html>
