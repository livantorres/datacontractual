CREATE DATABASE IF NOT EXISTS datacontractual_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE datacontractual_db;

-- Tabla de Roles
CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_rol VARCHAR(50) NOT NULL,
    descripcion VARCHAR(255) NULL
);

INSERT INTO roles (nombre_rol, descripcion) VALUES 
('SuperAdmin', 'Acceso total al sistema'),
('Contador', 'Gestión de clientes y contratos'),
('Cliente', 'Acceso de solo lectura a sus propios contratos');

-- Tabla de Usuarios
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol_id INT NOT NULL,
    estado ENUM('Activo', 'Inactivo') DEFAULT 'Activo',
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (rol_id) REFERENCES roles(id) ON DELETE RESTRICT
);

-- Tabla de Vigencias (Periodos Fiscales)
CREATE TABLE vigencias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    anio YEAR NOT NULL,
    estado ENUM('Activa', 'Inactiva') DEFAULT 'Activa'
);

INSERT INTO vigencias (anio, estado) VALUES 
(2023, 'Inactiva'),
(2024, 'Activa'),
(2025, 'Inactiva'),
(2026, 'Inactiva');

-- Tabla de Clientes (Instituciones o Independientes)
CREATE TABLE clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo_cliente ENUM('Institucion', 'Independiente') NOT NULL,
    nombre_razon_social VARCHAR(200) NOT NULL,
    nit_rfc VARCHAR(50) NOT NULL UNIQUE,
    direccion VARCHAR(255) NULL,
    email VARCHAR(100) NULL,
    telefono VARCHAR(50) NULL,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de Plantillas de Contrato
CREATE TABLE plantillas_contrato (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(150) NOT NULL,
    contenido_html LONGTEXT NOT NULL,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de Contratos
CREATE TABLE contratos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vigencia_id INT NOT NULL,
    cliente_id INT NOT NULL,
    usuario_id INT NOT NULL, -- Quien generó el contrato
    plantilla_id INT NOT NULL,
    monto DECIMAL(15, 2) NOT NULL DEFAULT 0.00,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NOT NULL,
    estado ENUM('Borrador', 'Generado', 'Firmado', 'Anulado') DEFAULT 'Borrador',
    archivo_pdf_generado VARCHAR(255) NULL, -- Ruta relativa
    archivo_soporte_escaneado VARCHAR(255) NULL, -- Ruta relativa
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (vigencia_id) REFERENCES vigencias(id) ON DELETE RESTRICT,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE RESTRICT,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE RESTRICT,
    FOREIGN KEY (plantilla_id) REFERENCES plantillas_contrato(id) ON DELETE RESTRICT
);

-- Tabla de Bitácora de Auditoría
CREATE TABLE bitacora_auditoria (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    accion VARCHAR(100) NOT NULL,
    modulo VARCHAR(50) NOT NULL,
    detalles TEXT NULL,
    fecha_hora TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ip VARCHAR(45) NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Insertar Usuario Administrador por Defecto (password: admin123)
-- El hash se genera con password_hash('admin123', PASSWORD_BCRYPT)
INSERT INTO usuarios (nombre, email, password, rol_id, estado) VALUES
('Administrador Principal', 'admin@datacontractual.com', '$2y$10$UoWb/Z/z8r51k./xY43X8.lZkOQ9YFp/Jz73T0zO7o6C./sR8Q1G.', 1, 'Activo');
