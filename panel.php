<?php
session_start();
include("conexion.php");

if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

// Agregar miembro
if (isset($_POST['agregar_miembro'])) {
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $plan = $_POST['plan'];
    $conn->query("INSERT INTO miembros (nombre, correo, plan) VALUES ('$nombre','$correo','$plan')");
}

// Agregar plan
if (isset($_POST['agregar_plan'])) {
    $nombrePlan = $_POST['nombrePlan'];
    $precio = $_POST['precio'];
    $conn->query("INSERT INTO planes (nombre, precio) VALUES ('$nombrePlan','$precio')");
}

// Eliminar miembro
if (isset($_GET['eliminar_miembro'])) {
    $id = $_GET['eliminar_miembro'];
    $conn->query("DELETE FROM miembros WHERE id=$id");
}

$miembros = $conn->query("SELECT * FROM miembros");
$planes = $conn->query("SELECT * FROM planes");
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Panel - Iron Fit</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<header>
  <h1>🏋️‍♂️ Iron Fit - Panel de Control</h1>
  <nav>
    <a href="?sec=dashboard">Dashboard</a>
    <a href="?sec=miembros">Miembros</a>
    <a href="?sec=planes">Planes</a>
    <a href="logout.php" class="logout">Salir</a>
  </nav>
</header>

<main>
<?php
$seccion = $_GET['sec'] ?? 'dashboard';

if ($seccion == 'dashboard'): ?>
  <h2>📊 Dashboard</h2>
  <div class="cards">
    <div class="card"><h3>Total Miembros</h3><p><?= $miembros->num_rows ?></p></div>
    <div class="card"><h3>Planes Activos</h3><p><?= $planes->num_rows ?></p></div>
  </div>

<?php elseif ($seccion == 'miembros'): ?>
  <h2>👥 Miembros</h2>
  <form method="POST">
    <input type="text" name="nombre" placeholder="Nombre" required>
    <input type="email" name="correo" placeholder="Correo" required>
    <select name="plan" required>
      <option value="">Seleccionar plan</option>
      <?php
      $listaPlanes = $conn->query("SELECT * FROM planes");
      while ($p = $listaPlanes->fetch_assoc()):
        echo "<option value='{$p['nombre']}'>{$p['nombre']}</option>";
      endwhile;
      ?>
    </select>
    <button type="submit" name="agregar_miembro">Agregar</button>
  </form>

  <table>
    <thead><tr><th>Nombre</th><th>Correo</th><th>Plan</th><th>Acción</th></tr></thead>
    <tbody>
      <?php while($m = $miembros->fetch_assoc()): ?>
        <tr>
          <td><?= $m['nombre'] ?></td>
          <td><?= $m['correo'] ?></td>
          <td><?= $m['plan'] ?></td>
          <td><a href="?eliminar_miembro=<?= $m['id'] ?>&sec=miembros" class="btn-borrar">❌</a></td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>

<?php elseif ($seccion == 'planes'): ?>
  <h2>💼 Planes</h2>
  <form method="POST">
    <input type="text" name="nombrePlan" placeholder="Nombre del plan" required>
    <input type="number" name="precio" placeholder="Precio" required>
    <button type="submit" name="agregar_plan">Agregar</button>
  </form>

  <ul>
    <?php while($p = $planes->fetch_assoc()): ?>
      <li><?= $p['nombre'] ?> — $<?= $p['precio'] ?></li>
    <?php endwhile; ?>
  </ul>

<?php endif; ?>
</main>

<footer>
  <p>© 2025 Iron Fit - Sistema de Suscripciones</p>
</footer>
</body>
</html>
