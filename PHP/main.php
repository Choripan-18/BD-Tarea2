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
  <style>
    :root { --bg:#0f172a; --fg:#fff; --muted:#cbd5e1; --link:#38bdf8; --chip:#334155; }
    *{box-sizing:border-box}
    body{margin:0;font-family:system-ui,Segoe UI,Roboto,Arial,sans-serif;background:#f8fafc;color:#0f172a}
    /* Navbar */
    .nav{position:sticky;top:0;background:var(--bg);color:var(--fg);display:flex;gap:16px;align-items:center;justify-content:space-between;padding:12px 16px}
    .brand{font-weight:700;letter-spacing:.3px}
    .links{display:flex;gap:10px;flex-wrap:wrap}
    .links a{color:var(--fg);text-decoration:none;padding:6px 10px;border-radius:8px}
    .links a:hover{background:rgba(255,255,255,.1)}
    .right{display:flex;align-items:center;gap:12px}
    .chip{font-size:12px;background:var(--chip);color:#e2e8f0;padding:4px 8px;border-radius:999px}
    .logout{border:none;background:#ef4444;color:#fff;padding:8px 12px;border-radius:8px;cursor:pointer}
    .logout:hover{background:#dc2626}
    /* Page */
    .wrap{max-width:1000px;margin:20px auto;padding:0 16px}
    .card{background:#ffffff;border:1px solid #e5e7eb;border-radius:12px;padding:18px}
    .muted{color:#475569}
  </style>
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
