CREATE DATABASE IF NOT EXISTS test;
USE test;
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY,
    username VARCHAR(50),
    password VARCHAR(50)
);
INSERT IGNORE INTO users VALUES (1, 'admin', 'admin123');
INSERT IGNORE INTO users VALUES (2, 'test', 'test123');
INSERT IGNORE INTO users VALUES (3, 'root', 'root123');
