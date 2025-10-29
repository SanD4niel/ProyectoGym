<?php
session_start();
include("conexion.php");

if (SERVER["REQUEST_METHOD"] == "POST") {
    usuario = POST["usuario"];
    contraseña = md5(POST["contraseña"]);

    sql = "SELECT * FROM usuarios WHERE usuario='usuario' AND contraseña='contraseña'";
    result = conn->query(sql);

    if (result->num_rows > 0) {
        SESSION['usuario'] = usuario;
        header("Location: panel.php");
        exit();
    } else {
        $error = "Usuario o contraseña incorrectos";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Login | Iron Fit</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="login-container">
  <h1>🏋️‍♂️ Iron Fit</h1>
  <form method="POST" action="">
    <input type="text" name="usuario" placeholder="Usuario" required>
    <input type="password" name="contrasena" placeholder="Contraseña" required>
    <button type="submit">Ingresar</button>
    <?php if(isset(error)) echo "<p class='error'>error</p>"; ?>
  </form>
</div>
</body>
</html>
