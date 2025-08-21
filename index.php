<?php
session_start();

// Configuracion de la base de datos
$servidor = "localhost";
$usuario = "root";      // tu usuario de MySQL
$contrasena_db = "";    // tu contraseña de MySQL
$base_datos = "Supermercado";

// Conectar a la base de datos
$conexion = new mysqli($servidor, $usuario, $contrasena_db, $base_datos);
if ($conexion->connect_error) {
    die("Conexion fallida: " . $conexion->connect_error);
}

// Procesar login
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_empleado = $_POST['id_empleado'];
    $contrasena = $_POST['contrasena'];

    // Preparar consulta para evitar inyeccion SQL
    $stmt = $conexion->prepare("SELECT id_empleado, nombre, id_rol FROM empleado WHERE id_empleado = ? AND contrasena = ? AND estado_activo = TRUE");
    $stmt->bind_param("is", $id_empleado, $contrasena);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        // Login exitoso
        $stmt->bind_result($id, $nombre, $id_rol);
        $stmt->fetch();
        $_SESSION['id_empleado'] = $id;
        $_SESSION['nombre'] = $nombre;
        $_SESSION['id_rol'] = $id_rol;
        header("Location: panel.php"); // Redirigir a pagina principal
        exit();
    } else {
        $error = "ID o contrasena incorrectos, o empleado inactivo.";
    }

    $stmt->close();
}
$conexion->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - Supermercado</title>
    <link rel="stylesheet" href="estilos.css">
</head>

<body>
    <div class="contenedor">
        <h2>Login de Empleado</h2>
        <?php if(isset($error)) echo "<p class='error'>$error</p>"; ?>
        <form method="post" action="">
            <label for="id_empleado">ID Empleado:</label>
            <input type="number" name="id_empleado" required>

            <label for="contrasena">Contrasena:</label>
            <input type="password" name="contrasena" required>

            <input type="submit" value="Ingresar">
        </form>
    </div>
</body>

</html>
