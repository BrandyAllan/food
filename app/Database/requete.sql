CREATE DATABASE food_design;
USE food_design;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100),
  email VARCHAR(150) UNIQUE,
  password VARCHAR(255)
);

INSERT INTO users (name, email, password) VALUES
('Brandy Allan', 'brandy@email.com', 'azerty');

UPDATE users SET password = '$2y$10$bb7u/ItLEiRTIisCZp2YYOdIy45vSt83q5TWr8Qg.5.QGVVgNvaw6' WHERE email = 'brandy@email.com';