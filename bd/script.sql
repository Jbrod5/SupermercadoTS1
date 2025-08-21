-- Utilizar roles que sean valores que apunten a esta tablaaa
CREATE TABLE roles (
    id_rol INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL
);
INSERT INTO roles (nombre) VALUES
('Cajero'),                                 -- Atiende ventas en caja
('Encargado de Inventario'),                -- Gestiona inventario y stock
('Gerente'),                                -- Supervisión general y administración
('Empleado de Estanterias'),                -- Coloca productos en estantes, reposición
('Personal de Limpieza y Mantenimiento'),   -- Mantenimiento de instalaciones
('Seguridad');                              -- Vigilancia del supermercado



-- El empleado deberá iniciar sesion para poder entrar a funciones del sistema 
-- Habran muchos tipos de empleados, es mejor que apunten a otra tabla?
CREATE TABLE empleados (
    id_empleado INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    salario DECIMAL(10,2) NOT NULL,
    id_rol INT NOT NULL,
    telefono VARCHAR(20),
    correo VARCHAR(100) UNIQUE,
    contrasena VARCHAR(255) NOT NULL,
    estado BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (id_rol) REFERENCES roles(id_rol)
);
