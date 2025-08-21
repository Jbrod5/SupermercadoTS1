<?php
$conexion = new mysqli("localhost", "root", "", "Supermercado");
if ($conexion->connect_error) die("Error: " . $conexion->connect_error);

// Crear proveedor
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['crear_proveedor'])) {
    $nombre = $_POST['nombre'];
    $telefono = $_POST['telefono'];
    $correo = $_POST['correo'];
    $direccion = $_POST['direccion'];

    $stmt = $conexion->prepare("INSERT INTO proveedor (nombre, telefono, correo, direccion) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nombre, $telefono, $correo, $direccion);
    $stmt->execute();
    $stmt->close();
}

// Obtener proveedores
$proveedores = $conexion->query("SELECT * FROM proveedor");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Proveedores</title>
    <link rel="stylesheet" href="../estilos.css">
</head>
<body>
    <h2>Agregar Proveedor</h2>
    <form method="post" action="">
        <input type="hidden" name="crear_proveedor" value="1">
        <label>Nombre:</label>
        <input type="text" name="nombre" required>
        <label>Teléfono:</label>
        <input type="text" name="telefono">
        <label>Correo:</label>
        <input type="email" name="correo">
        <label>Dirección:</label>
        <input type="text" name="direccion">
        <input type="submit" value="Guardar">
    </form>

    <h2>Lista de Proveedores</h2>
    <table>
        <tr><th>ID</th><th>Nombre</th><th>Teléfono</th><th>Correo</th><th>Dirección</th></tr>
        <?php while($p = $proveedores->fetch_assoc()): ?>
            <tr>
                <td><?= $p['id_proveedor'] ?></td>
                <td><?= $p['nombre'] ?></td>
                <td><?= $p['telefono'] ?></td>
                <td><?= $p['correo'] ?></td>
                <td><?= $p['direccion'] ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
