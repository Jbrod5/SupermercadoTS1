<?php
session_start();
if (!isset($_SESSION['id_empleado']) || $_SESSION['id_rol'] != 2) {
    header("Location: ../index.php");
    exit();
}

$conexion = new mysqli("localhost", "root", "", "Supermercado");
if ($conexion->connect_error) die("Conexion fallida: " . $conexion->connect_error);

// Crear producto
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['crear_producto'])) {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $id_proveedor = $_POST['id_proveedor'];

    // Procesar imagen
    $imagen_ruta = NULL;
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        $destino = '../uploads/Productos';
        if (!is_dir($destino)) mkdir($destino, 0777, true);
        $imagen_ruta = $destino . basename($_FILES['imagen']['name']);
        move_uploaded_file($_FILES['imagen']['tmp_name'], $imagen_ruta);
    }

    $stmt = $conexion->prepare("INSERT INTO producto (nombre, descripcion_corta, precio, imagen, id_proveedor) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdsi", $nombre, $descripcion, $precio, $imagen_ruta, $id_proveedor);
    $stmt->execute();
    $stmt->close();

    // Crear registro en inventario
    $ultimo_id = $conexion->insert_id;
    $conexion->query("INSERT INTO inventario (id_producto, cantidad) VALUES ($ultimo_id, 0)");
}

// Editar producto
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['editar_producto'])) {
    $id_producto = $_POST['id_producto'];
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $id_proveedor = $_POST['id_proveedor'];

    // Procesar imagen nueva
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        $destino = '../uploads/Productos';
        if (!is_dir($destino)) mkdir($destino, 0777, true);
        $imagen_ruta = $destino . basename($_FILES['imagen']['name']);
        move_uploaded_file($_FILES['imagen']['tmp_name'], $imagen_ruta);
        $stmt = $conexion->prepare("UPDATE producto SET nombre=?, descripcion_corta=?, precio=?, id_proveedor=?, imagen=? WHERE id_producto=?");
        $stmt->bind_param("ssdssi", $nombre, $descripcion, $precio, $id_proveedor, $imagen_ruta, $id_producto);
    } else {
        $stmt = $conexion->prepare("UPDATE producto SET nombre=?, descripcion_corta=?, precio=?, id_proveedor=? WHERE id_producto=?");
        $stmt->bind_param("ssdsi", $nombre, $descripcion, $precio, $id_proveedor, $id_producto);
    }
    $stmt->execute();
    $stmt->close();
}

// Obtener proveedores
$proveedores = $conexion->query("SELECT * FROM proveedor");

// Obtener productos
$productos = $conexion->query("SELECT p.*, pr.nombre AS proveedor_nombre FROM producto p JOIN proveedor pr ON p.id_proveedor = pr.id_proveedor");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Productos</title>
    <link rel="stylesheet" href="../estilos.css">
    <style>
        .form-producto { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 30px;}
        .form-producto input[type="submit"], .form-producto input[type="file"] { grid-column: 1 / -1; }
        .editar-form { display:flex; flex-direction: column; gap:5px; }
        .editar-form input[type="submit"] { width: auto; }
    </style>
</head>
<body>
<div class="contenedor">
    <h2>Agregar Producto</h2>
    <form method="post" enctype="multipart/form-data" class="form-producto">
        <input type="hidden" name="crear_producto" value="1">

        <label>Nombre:</label>
        <input type="text" name="nombre" required>

        <label>Precio:</label>
        <input type="number" step="0.01" name="precio" required>

        <label>Descripción corta:</label>
        <input type="text" name="descripcion">

        <label>Proveedor:</label>
        <select name="id_proveedor" required>
            <?php while($p = $proveedores->fetch_assoc()): ?>
                <option value="<?= $p['id_proveedor'] ?>"><?= $p['nombre'] ?></option>
            <?php endwhile; ?>
        </select>

        <label>Imagen:</label>
        <input type="file" name="imagen" accept="image/*">

        <input type="submit" value="Agregar Producto">
    </form>

    <h2>Productos Existentes</h2>
    <table>
        <tr>
            <th>ID</th><th>Nombre</th><th>Precio</th><th>Proveedor</th><th>Imagen</th><th>Editar</th>
        </tr>
        <?php while($prod = $productos->fetch_assoc()): ?>
        <tr>
            <td><?= $prod['id_producto'] ?></td>
            <td><?= $prod['nombre'] ?></td>
            <td><?= $prod['precio'] ?></td>
            <td><?= $prod['proveedor_nombre'] ?></td>
            <td>
                <?php if($prod['imagen']): ?>
                    <img src="<?= $prod['imagen'] ?>" alt="Foto" style="width:50px;height:50px;object-fit:cover;border-radius:5px;">
                <?php else: ?>
                    -
                <?php endif; ?>
            </td>
            <td>
                <form method="post" enctype="multipart/form-data" class="editar-form">
                    <input type="hidden" name="editar_producto" value="1">
                    <input type="hidden" name="id_producto" value="<?= $prod['id_producto'] ?>">
                    <input type="text" name="nombre" value="<?= $prod['nombre'] ?>" required>
                    <input type="number" step="0.01" name="precio" value="<?= $prod['precio'] ?>" required>
                    <input type="text" name="descripcion" value="<?= $prod['descripcion_corta'] ?>">
                    <select name="id_proveedor">
                        <?php
                        $proveedores2 = $conexion->query("SELECT * FROM proveedor");
                        while($p2 = $proveedores2->fetch_assoc()):
                        ?>
                            <option value="<?= $p2['id_proveedor'] ?>" <?= $p2['id_proveedor'] == $prod['id_proveedor'] ? 'selected' : '' ?>>
                                <?= $p2['nombre'] ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                    <input type="file" name="imagen" accept="image/*">
                    <input type="submit" value="Editar">
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>
</body>
</html>
