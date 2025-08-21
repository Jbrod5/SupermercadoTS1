<?php
session_start();
if (!isset($_SESSION['id_empleado']) || $_SESSION['id_rol'] != 3) {
    header("Location: ../index.php");
    exit();
}

// Conexión a la DB
$conexion = new mysqli("localhost", "root", "", "Supermercado");
if ($conexion->connect_error) die("Conexion fallida: " . $conexion->connect_error);

$id = $_GET['id'] ?? null;
if (!$id) {
    die("Empleado no especificado.");
}

// Obtener datos del empleado
$stmt = $conexion->prepare("SELECT * FROM empleado WHERE id_empleado = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$empleado = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$empleado) {
    die("Empleado no encontrado.");
}

// Si se envía el formulario -> actualizar
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['actualizar_empleado'])) {
    $nombre = $_POST['nombre'];
    $salario = $_POST['salario'];
    $id_rol = $_POST['id_rol'];
    $telefono = $_POST['telefono'];
    $correo = $_POST['correo'];
    $contrasena = $_POST['contrasena'];
    $estado_activo = isset($_POST['estado_activo']) ? 1 : 0;

    // Fotografía
    $fotografia_ruta = $empleado['fotografia'];
    if (isset($_FILES['fotografia']) && $_FILES['fotografia']['error'] == 0) {
        $destino = '../uploads/Trabajadores/';
        if (!is_dir($destino)) mkdir($destino, 0777, true);
        $fotografia_ruta = $destino . basename($_FILES['fotografia']['name']);
        move_uploaded_file($_FILES['fotografia']['tmp_name'], $fotografia_ruta);
    }

    $stmt = $conexion->prepare("UPDATE empleado 
        SET nombre=?, salario=?, id_rol=?, telefono=?, correo=?, contrasena=?, estado_activo=?, fotografia=? 
        WHERE id_empleado=?");

    $stmt->bind_param("sdisssisi", 
        $nombre, $salario, $id_rol, $telefono, $correo, $contrasena, $estado_activo, $fotografia_ruta, $id
    );

    if ($stmt->execute()) {
        header("Location: admin_dashboard.php"); // volver al dashboard
        exit();
    } else {
        echo "Error al actualizar: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Empleado</title>
    <link rel="stylesheet" href="../estilos.css">
</head>
<body>
    <div class="contenedor">
        <h2>Editar Empleado</h2>
        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="actualizar_empleado" value="1">

            <label>Nombre:</label>
            <input type="text" name="nombre" value="<?= $empleado['nombre'] ?>" required><br>

            <label>Salario:</label>
            <input type="number" step="0.01" name="salario" value="<?= $empleado['salario'] ?>" required><br>

            <label>Rol:</label>
            <select name="id_rol">
                <?php
                $roles = $conexion->query("SELECT id_rol, nombre_rol FROM rol");
                while($r = $roles->fetch_assoc()){
                    $sel = ($r['id_rol'] == $empleado['id_rol']) ? 'selected' : '';
                    echo "<option value='{$r['id_rol']}' $sel>{$r['nombre_rol']}</option>";
                }
                ?>
            </select><br>

            <label>Telefono:</label>
            <input type="number" name="telefono" value="<?= $empleado['telefono'] ?>"><br>

            <label>Correo:</label>
            <input type="email" name="correo" value="<?= $empleado['correo'] ?>"><br>

            <label>Contraseña:</label>
            <input type="text" name="contrasena" value="<?= $empleado['contrasena'] ?>" required><br>

            <label>Activo:</label>
            <input type="checkbox" name="estado_activo" <?= $empleado['estado_activo'] ? 'checked' : '' ?>><br>

            <label>Fotografía:</label>
            <input type="file" name="fotografia" accept="image/*"><br>
            <?php if($empleado['fotografia']): ?>
                <img src="<?= $empleado['fotografia'] ?>" alt="Foto" style="width:80px;height:80px;object-fit:cover;border-radius:5px;">
            <?php endif; ?>
            <br><br>

            <input type="submit" value="Actualizar">
        </form>
    </div>
</body>
</html>
