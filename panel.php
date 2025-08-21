<?php
session_start();

// Verificar que haya login
if (!isset($_SESSION['id_empleado'])) {
    header("Location: index.php");
    exit();
}

// Redirigir según rol
$id_rol = $_SESSION['id_rol'];

if ($id_rol == 3) {
    header("Location: ./gerencia/admin_dashboard.php");
    exit();
} elseif ($id_rol == 2) {
    header("Location: ./inventario/inventario_dashboard.php");
    exit();
} else {
    echo "Acceso no autorizado para tu rol.";
    exit();
}
?>