DROP TABLE IF EXISTS vehicles;
DROP TABLE IF EXISTS vehicle_types;

CREATE TABLE vehicle_types (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL
);

CREATE TABLE vehicles (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    model_name VARCHAR(100) NOT NULL,
    type_id INT UNSIGNED NOT NULL,
    vehicle_type VARCHAR(50) NOT NULL,
    doors TINYINT UNSIGNED NOT NULL,
    transmission ENUM('manual', 'automatic') NOT NULL,
    fuel ENUM('petrol', 'diesel', 'hybrid', 'electric') NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (type_id) REFERENCES vehicle_types(id)
);

INSERT INTO vehicle_types (name) VALUES
    ('Economy'), ('Compact'), ('SUV'), ('Van');

INSERT INTO vehicles
    (model_name, type_id, vehicle_type, doors, transmission, fuel, price)
VALUES
    ('Fiat Panda',      2, 'car', 4, 'manual',    'petrol',   90),
    ('Toyota Yaris',    1, 'car', 5, 'automatic', 'hybrid',   110),
    ('VW Golf',         2, 'car', 5, 'manual',    'diesel',   130),
    ('Nissan Qashqai',  3, 'car', 5, 'automatic', 'petrol',   180),
    ('Tesla Model Y',   3, 'car', 5, 'automatic', 'electric', 250),
    ('Ford Transit',    4, 'van', 4, 'manual',    'diesel',   200);
