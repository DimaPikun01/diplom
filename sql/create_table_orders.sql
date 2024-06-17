-- Создание таблицы "orders" для хранения заказов
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    creation_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(50) DEFAULT 'новый',
    client_first_name VARCHAR(50) NOT NULL,
    client_last_name VARCHAR(50) NOT NULL,
    client_phone VARCHAR(20) NOT NULL,
    manager VARCHAR(50),
    technician VARCHAR(50),
    cost DECIMAL(10, 2),
    reason TEXT,
    imei_sn VARCHAR(50),
    appearance TEXT,
    device_type VARCHAR(100)
);
