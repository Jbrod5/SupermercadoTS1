<?php
session_start();
if (!isset($_SESSION['id_empleado']) || $_SESSION['id_rol'] != 2) {
    header("Location: ../index.php");
    exit();
}

// Conexión DB
$conexion = new mysqli("localhost", "root", "", "Supermercado");
if ($conexion->connect_error) die("Conexión fallida: " . $conexion->connect_error);

// Consultas para resumen
$total_proveedores = $conexion->query("SELECT COUNT(*) as total FROM proveedor")->fetch_assoc()['total'];
$total_productos   = $conexion->query("SELECT COUNT(*) as total FROM producto")->fetch_assoc()['total'];
$total_unidades    = $conexion->query("SELECT SUM(cantidad) as total FROM inventario")->fetch_assoc()['total'];

// Productos bajos en stock (menos de 5)
$bajos_stock = $conexion->query("SELECT COUNT(*) as total FROM inventario WHERE cantidad < 5")->fetch_assoc()['total'];

// Proveedores y productos recientes
$proveedores = $conexion->query("SELECT id_proveedor, nombre, telefono, correo FROM proveedor LIMIT 5");
$productos   = $conexion->query("SELECT p.id_producto, p.nombre, pr.nombre AS proveedor, i.cantidad 
                                FROM producto p
                                LEFT JOIN proveedor pr ON p.id_proveedor = pr.id_proveedor
                                LEFT JOIN inventario i ON p.id_producto = i.id_producto
                                LIMIT 5");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Inventario</title>
    <link rel="stylesheet" href="../estilos.css">
    <style>
        .cards { display: flex; gap: 20px; margin-bottom: 30px; }
        .card { background:#4a90e2; color:white; padding:20px; border-radius:8px; flex:1; text-align:center; }
        .alerta { background:#e74c3c !important; }
        .enlaces { margin-bottom:30px; }
        .enlaces a { margin-right:15px; padding:10px 15px; background:#357ab7; color:white; text-decoration:none; border-radius:5px; }
        table { width: 90%; max-width: 900px; border-collapse: collapse; margin-bottom: 30px; }
        th, td { padding:10px; border:1px solid #ccc; text-align:left; }
        th { background:#4a90e2; color:white; }
        tr:nth-child(even){background:#f9f9f9;}
    </style>
</head>
<body>
    <div class="contenedor">
        <h2>Dashboard de Inventario</h2>

        <!-- Cards resumen -->
        <div class="cards">
            <div class="card">Proveedores: <?= $total_proveedores ?></div>
            <div class="card">Productos: <?= $total_productos ?></div>
            <div class="card">Unidades en inventario: <?= $total_unidades ?></div>
            <div class="card alerta">Stock bajo: <?= $bajos_stock ?></div>
        </div>

        <!-- Enlaces rápidos -->
        <div class="enlaces">
            <a href="proveedores.php">Ver Proveedores</a>
            <a href="productos.php">Ver Productos</a>
            <a href="inventario.php">Ver Inventario</a>
        </div>

        <!-- Tabla de proveedores recientes -->
        <h3>Proveedores recientes</h3>
        <table>
            <tr><th>ID</th><th>Nombre</th><th>Teléfono</th><th>Correo</th></tr>
            <?php while($p = $proveedores->fetch_assoc()): ?>
                <tr>
                    <td><?= $p['id_proveedor'] ?></td>
                    <td><?= $p['nombre'] ?></td>
                    <td><?= $p['telefono'] ?></td>
                    <td><?= $p['correo'] ?></td>
                </tr>
            <?php endwhile; ?>
        </table>

        <!-- Tabla de productos recientes -->
        <h3>Productos recientes</h3>
        <table>
            <tr><th>ID</th><th>Nombre</th><th>Proveedor</th><th>Cantidad</th></tr>
            <?php while($pr = $productos->fetch_assoc()): ?>
                <tr>
                    <td><?= $pr['id_producto'] ?></td>
                    <td><?= $pr['nombre'] ?></td>
                    <td><?= $pr['proveedor'] ?></td>
                    <td><?= $pr['cantidad'] ?? 0 ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>
