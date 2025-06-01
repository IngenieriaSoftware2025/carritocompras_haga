CREATE TABLE clientesCC (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    email VARCHAR(150),
    telefono VARCHAR(20),
    direccion VARCHAR(200),
    nit VARCHAR(20),
    fecha_registro DATETIME YEAR TO SECOND,
    situacion SMALLINT DEFAULT 1
);

CREATE TABLE productosCC (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    descripcion VARCHAR(255),
    precio DECIMAL(10,2) NOT NULL,
    stock_disponible INTEGER NOT NULL DEFAULT 0,
    stock_minimo INTEGER DEFAULT 5,
    fecha_creacion DATETIME YEAR TO SECOND,
    fecha_actualizacion DATETIME YEAR TO SECOND,
    situacion SMALLINT DEFAULT 1
);

CREATE TABLE facturasCC (
    id SERIAL PRIMARY KEY,
    numero_factura VARCHAR(50) NOT NULL UNIQUE,
    id_cliente INTEGER NOT NULL,
    fecha_factura DATETIME YEAR TO SECOND,
    subtotal DECIMAL(10,2) NOT NULL,
    impuestos DECIMAL(10,2),
    descuento DECIMAL(10,2),
    total DECIMAL(10,2) NOT NULL,
    estado VARCHAR(20) DEFAULT 'EMITIDA',
    observaciones LVARCHAR(500),
    fecha_creacion DATETIME YEAR TO SECOND,
    situacion SMALLINT DEFAULT 1,
    FOREIGN KEY (id_cliente) REFERENCES clientesCC(id)
);

CREATE TABLE detalle_facturasCC (
    id SERIAL PRIMARY KEY,
    id_factura INTEGER NOT NULL,
    id_producto INTEGER NOT NULL,
    cantidad INTEGER NOT NULL,
    precio_unitario DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    descuento_linea DECIMAL(10,2),
    total_linea DECIMAL(10,2) NOT NULL,
    situacion SMALLINT DEFAULT 1,
    FOREIGN KEY (id_factura) REFERENCES facturasCC(id),
    FOREIGN KEY (id_producto) REFERENCES productosCC(id)
);


