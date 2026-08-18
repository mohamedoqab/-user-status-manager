CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  age INT NOT NULL,
  status TINYINT(1) NOT NULL DEFAULT 1
);

INSERT INTO users (name, age, status) VALUES
('Mohamed', 22, 1),
('Ahmed', 25, 0);
