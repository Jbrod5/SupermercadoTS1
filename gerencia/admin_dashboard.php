<?php
session_start();
if (!isset($_SESSION['id_empleado']) || $_SESSION['id_rol'] != 3) {
    header("Location: ../index.php");
    exit();
}

// Conexión a la DB
$conexion = new mysqli("localhost", "root", "", "Supermercado");
if ($conexion->connect_error) die("Conexion fallida: " . $conexion->connect_error);

// Crear nuevo empleado
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['crear_empleado'])) {
    $nombre = $_POST['nombre'];
    $salario = $_POST['salario'];
    $id_rol = $_POST['id_rol'];
    $telefono = $_POST['telefono'];
    $correo = $_POST['correo'];
    $contrasena = $_POST['contrasena'];

    // Procesar fotografía
    $fotografia_ruta = NULL;
    if (isset($_FILES['fotografia']) && $_FILES['fotografia']['error'] == 0) {
        $destino = '../uploads/';
        if (!is_dir($destino)) mkdir($destino, 0777, true);
        $fotografia_ruta = $destino . basename($_FILES['fotografia']['name']);
        move_uploaded_file($_FILES['fotografia']['tmp_name'], $fotografia_ruta);
    }

    $stmt = $conexion->prepare("INSERT INTO empleado (nombre, salario, id_rol, telefono, correo, contrasena, estado_activo, fotografia) VALUES (?, ?, ?, ?, ?, ?, TRUE, ?)");
    $stmt->bind_param("sdisiss", $nombre, $salario, $id_rol, $telefono, $correo, $contrasena, $fotografia_ruta);
    $stmt->execute();
    $stmt->close();
}

// Obtener todos los empleados
$result = $conexion->query("SELECT e.id_empleado, e.nombre, e.salario, r.nombre_rol, e.telefono, e.correo, e.estado_activo, e.fotografia 
                            FROM empleado e
                            JOIN rol r ON e.id_rol = r.id_rol");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Administrador</title>
    <link rel="stylesheet" href="../estilos.css">
    <style>
        /* NUEVAS CLASES para formulario de creación */
        .form-crear-empleado {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        .form-crear-empleado label {
            display: block;
        }
        .form-crear-empleado input[type="submit"] {
            grid-column: 1 / -1; /* botón ocupa las dos columnas */
        }
        .form-crear-empleado input[type="file"] {
            grid-column: 1 / -1; /* fotografía ocupa toda la fila */
        }
    </style>
</head>
<body>
    <div class="contenedor">
        <h2>Dashboard Administrador</h2>

        <h3>Crear nuevo empleado</h3>
        <form method="post" action="" enctype="multipart/form-data" class="form-crear-empleado">
            <input type="hidden" name="crear_empleado" value="1">

            <div class="fila">
                <div class="campo">
                    <label>Nombre:</label>
                    <input type="text" name="nombre" required>
                </div>
                <div class="campo">
                    <label>Salario:</label>
                    <input type="number" step="0.01" name="salario" required>
                </div>
            </div>

            <div class="fila">
                <div class="campo">
                    <label>Rol:</label>
                    <select name="id_rol">
                        <?php
                        $roles = $conexion->query("SELECT id_rol, nombre_rol FROM rol");
                        while($r = $roles->fetch_assoc()){
                            echo "<option value='{$r['id_rol']}'>{$r['nombre_rol']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="campo">
                    <label>Telefono:</label>
                    <input type="number" name="telefono">
                </div>
            </div>
            

            <div class="fila">
                <div class="campo">
                    <label>Correo:</label>
                    <input type="email" name="correo">
                </div>
                <div class="campo">
                    <label>Contrasena:</label>
                    <input type="text" name="contrasena" required>
                </div>
            </div>
             <div class="fila">
                <div class="campo">
                    <label>Fotografía:</label>
                    <input type="file" name="fotografia" accept="image/*">
                </div>
            </div>

           

            <input type="submit" value="Crear">
        </form>

        <h3>Empleados existentes</h3>
        <table>
            <tr>
                <th>ID</th><th>Nombre</th><th>Salario</th><th>Rol</th><th>Telefono</th><th>Correo</th><th>Activo</th><th>Fotografía</th>
            </tr>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id_empleado'] ?></td>
                    <td><?= $row['nombre'] ?></td>
                    <td><?= $row['salario'] ?></td>
                    <td><?= $row['nombre_rol'] ?></td>
                    <td><?= $row['telefono'] ?></td>
                    <td><?= $row['correo'] ?></td>
                    <td><?= $row['estado_activo'] ? 'Si' : 'No' ?></td>
                    <td>
                        <?php if($row['fotografia']): ?>
                            <img src="<?= $row['fotografia'] ?>" alt="Foto" style="width:50px;height:50px;object-fit:cover;border-radius:5px;">
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>
