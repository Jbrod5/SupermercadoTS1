DROP DATABASE IF EXISTS Supermercado;
CREATE DATABASE Supermercado;
USE Supermercado;



-- Utilizar roles que sean valores que apunten a esta tablaaa
CREATE TABLE rol (
    id_rol INT AUTO_INCREMENT PRIMARY KEY,
    nombre_rol VARCHAR(50) NOT NULL
);
INSERT INTO rol (nombre_rol) VALUES
('Cajero'),                                 -- Atiende ventas en caja
('Encargado de Inventario'),                -- Gestiona inventario y stock
('Gerente'),                                -- Supervisión general y administración
('Empleado de Estanterias'),                -- Coloca productos en estantes, reposición
('Personal de Limpieza y Mantenimiento'),   -- Mantenimiento de instalaciones
('Seguridad');                              -- Vigilancia del supermercado



-- El empleado deberá iniciar sesion para poder entrar a funciones del sistema 
-- Habran muchos tipos de empleados, es mejor que apunten a otra tabla?
CREATE TABLE empleado (
    id_empleado INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    salario DECIMAL(10,2) NOT NULL,
    id_rol INT NOT NULL,
    telefono BIGINT,
    correo VARCHAR(100) UNIQUE,
    contrasena VARCHAR(255) NOT NULL,
    estado_activo BOOLEAN DEFAULT TRUE, --
    fotografia VARCHAR(255),        -- ruta de la foto 
    FOREIGN KEY (id_rol) REFERENCES rol(id_rol)
);

INSERT INTO empleado (nombre, salario, id_rol, telefono, correo, contrasena, estado_activo, fotografia) -- insertar un gerente para iniciar con el sistema :3
VALUES (
    'Pedro Administrador General',
    8000.00,
    3, -- 3 corresponde a 'Gerente' en la tabla rol
    '12349876',
    'admin@supermercado.com',
    'admin123', -- contrasena
    TRUE,
    NULL
);




-- Proveedores y productos
CREATE TABLE proveedor (
    id_proveedor INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    telefono VARCHAR(20),
    correo VARCHAR(100),
    direccion VARCHAR(255)
);

CREATE TABLE producto (
    id_producto INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion_corta VARCHAR(255),
    precio DECIMAL(10,2) NOT NULL,
    imagen VARCHAR(255),        -- ruta de la imagen del producto
    id_proveedor INT NOT NULL,
    fecha_ingreso DATE DEFAULT CURRENT_DATE,
    FOREIGN KEY (id_proveedor) REFERENCES proveedor(id_proveedor)
);




-- inventario
CREATE TABLE inventario (
    id_inventario INT AUTO_INCREMENT PRIMARY KEY,
    id_producto INT NOT NULL,
    cantidad INT NOT NULL DEFAULT 0,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_producto) REFERENCES producto(id_producto)
);

-- ventas (una tabla aparte puede servir como lista de productos??), metodo de pago separado
CREATE TABLE metodo_pago(
    id_metodo_pago INT AUTO_INCREMENT PRIMARY KEY, 
    nombre_metodo VARCHAR(255)
);
INSERT INTO metodo_pago (nombre_metodo) VALUES
('Efectivo'),                                 
('Tarjeta'),                
('Transferencia');                                

CREATE TABLE cliente (
    nit_cliente BIGINT PRIMARY KEY,       -- NIT del cliente
    nombre VARCHAR(255) NOT NULL,
    telefono BIGINT,
    correo VARCHAR(255),
    direccion VARCHAR(255)
);



CREATE TABLE venta (
    id_venta INT AUTO_INCREMENT PRIMARY KEY,
    nit_cliente BIGINT NULL, -- los int pueden dar problemas con los nit largos? / null para ventas como consumidor final 
    id_empleado INT NOT NULL,

    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    total DECIMAL(10,2) NOT NULL,
    tipo_pago INT NOT NULL DEFAULT 1,
    FOREIGN KEY (id_empleado) REFERENCES empleado(id_empleado),
    FOREIGN KEY (tipo_pago) REFERENCES metodo_pago(id_metodo_pago),
    FOREIGN KEY (nit_cliente) REFERENCES cliente(nit_cliente)
);

CREATE TABLE detalle_venta (
    id_detalle INT AUTO_INCREMENT PRIMARY KEY,
    id_venta INT NOT NULL,
    id_producto INT NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (id_venta) REFERENCES venta(id_venta),
    FOREIGN KEY (id_producto) REFERENCES producto(id_producto)
);
