<?php
session_start();
if (!isset($_SESSION['id_empleado']) || $_SESSION['id_rol'] != 2) {
    header("Location: ../index.php");
    exit();
}

$conexion = new mysqli("localhost", "root", "", "Supermercado");
if ($conexion->connect_error) die("Conexion fallida: " . $conexion->connect_error);

// Actualizar cantidad de inventario
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['actualizar_inventario'])) {
    $id_producto = $_POST['id_producto'];
    $cantidad = $_POST['cantidad'];
    $stmt = $conexion->prepare("UPDATE inventario SET cantidad = ? WHERE id_producto = ?");
    $stmt->bind_param("ii", $cantidad, $id_producto);
    $stmt->execute();
    $stmt->close();
}

// Obtener inventario
$inventario = $conexion->query("SELECT i.*, p.nombre AS producto_nombre FROM inventario i JOIN producto p ON i.id_producto = p.id_producto");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Inventario</title>
    <link rel="stylesheet" href="../estilos.css">
    <style>
        form.actualizar-inv { display: flex; gap:5px; }
        form.actualizar-inv input[type="number"] { width:70px; }
    </style>
</head>
<body>
<div class="contenedor">
    <h2>Inventario</h2>
    <table>
        <tr>
            <th>ID Producto</th><th>Nombre</th><th>Cantidad</th><th>Última Actualización</th><th>Acción</th>
        </tr>
        <?php while($inv = $inventario->fetch_assoc()): ?>
        <tr>
            <td><?= $inv['id_producto'] ?></td>
            <td><?= $inv['producto_nombre'] ?></td>
            <td><?= $inv['cantidad'] ?></td>
            <td><?= $inv['fecha_actualizacion'] ?></td>
            <td>
                <form method="post" class="actualizar-inv">
                    <input type="hidden" name="actualizar_inventario" value="1">
                    <input type="hidden" name="id_producto" value="<?= $inv['id_producto'] ?>">
                    <input type="number" name="cantidad" value="<?= $inv['cantidad'] ?>" min="0">
                    <input type="submit" value="Actualizar">
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>
</body>
</html>
