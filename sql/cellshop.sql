-- CellShop: base de datos MySQL/MariaDB (XAMPP)
-- Importar desde phpMyAdmin (pestaña "Importar") o: mysql -u root < sql/cellshop.sql
CREATE DATABASE IF NOT EXISTS cellshop CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE cellshop;

DROP TABLE IF EXISTS cotizacion_items;
DROP TABLE IF EXISTS cotizaciones;
DROP TABLE IF EXISTS clientes;
DROP TABLE IF EXISTS celulares;

CREATE TABLE celulares (
  id INT AUTO_INCREMENT PRIMARY KEY,
  marca VARCHAR(60) NOT NULL,
  modelo VARCHAR(80) NOT NULL,
  ram VARCHAR(20) NOT NULL DEFAULT '',
  almacenamiento VARCHAR(20) NOT NULL DEFAULT '',
  pantalla VARCHAR(40) NOT NULL DEFAULT '',
  precio DECIMAL(10,2) NOT NULL,
  stock INT NOT NULL DEFAULT 0,
  imagen VARCHAR(120) NOT NULL DEFAULT ''
) ENGINE=InnoDB;

CREATE TABLE clientes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(120) NOT NULL,
  dni CHAR(8) NOT NULL UNIQUE,
  telefono VARCHAR(20) NOT NULL DEFAULT '',
  email VARCHAR(120) NOT NULL DEFAULT ''
) ENGINE=InnoDB;

CREATE TABLE cotizaciones (
  id INT AUTO_INCREMENT PRIMARY KEY,
  fecha DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  cliente_id INT NULL,
  cliente VARCHAR(120) NOT NULL,
  dni CHAR(8) NOT NULL,
  telefono VARCHAR(20) NOT NULL DEFAULT '',
  email VARCHAR(120) NOT NULL DEFAULT '',
  subtotal DECIMAL(10,2) NOT NULL,
  descuento_pct DECIMAL(5,2) NOT NULL DEFAULT 0,
  descuento DECIMAL(10,2) NOT NULL DEFAULT 0,
  igv DECIMAL(10,2) NOT NULL,
  total DECIMAL(10,2) NOT NULL,
  estado ENUM('Pendiente','Aprobada','Rechazada') NOT NULL DEFAULT 'Pendiente',
  FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE cotizacion_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  cotizacion_id INT NOT NULL,
  celular_id INT NULL,
  producto VARCHAR(150) NOT NULL,
  cantidad INT NOT NULL,
  precio DECIMAL(10,2) NOT NULL,
  total DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (cotizacion_id) REFERENCES cotizaciones(id) ON DELETE CASCADE,
  FOREIGN KEY (celular_id) REFERENCES celulares(id) ON DELETE SET NULL
) ENGINE=InnoDB;

INSERT INTO celulares (marca,modelo,ram,almacenamiento,pantalla,precio,stock,imagen) VALUES
('Samsung','Galaxy A55','8 GB','256 GB','6.6" AMOLED',1499.00,10,'samsung-a55.png'),
('Xiaomi','Redmi 13','6 GB','128 GB','6.79" IPS',599.00,25,'xiaomi-redmi13.png'),
('Apple','iPhone 15','6 GB','128 GB','6.1" OLED',3599.00,6,'iphone15.png'),
('Motorola','Moto G84','8 GB','256 GB','6.5" pOLED',899.00,15,'motorola-g84.png');

INSERT INTO clientes (nombre,dni,telefono,email) VALUES
('María Torres Quispe','45871236','987654321','maria@correo.com'),
('Luis Ramírez Soto','70123456','912345678','luis@correo.com');
