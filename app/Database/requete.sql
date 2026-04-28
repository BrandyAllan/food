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

CREATE TABLE foods (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    emoji VARCHAR(20),
    img VARCHAR(255),
    cat VARCHAR(100),
    time VARCHAR(50),
    cal VARCHAR(50),
    rating DECIMAL(2,1),
    description TEXT
);

INSERT INTO foods (name, emoji, img, cat, time, cal, rating, description) VALUES
('Ramen Tonkotsu', '🍜', 'ramen.jpg', 'Japonais', '45 min', '620 kcal', '4.8', 'Bouillon de porc riche, nouilles fraiches, oeuf mollet et chashu'),

('Pizza Margherita', '🍕', 'pizza.jpg', 'Italien', '30 min', '540 kcal', '4.7', 'Tomate San Marzano, mozzarella di bufala, basilic frais'),

('Tacos al Pastor', '🌮', 'tacos.jpg', 'Mexicain', '20 min', '480 kcal', '4.6', 'Porc marine aux epices, ananas, coriandre et salsa verde'),

('Pad Thai', '🍝', 'padthai.jpg', 'Thailandais', '25 min', '550 kcal', '4.5', 'Nouilles de riz sautees, crevettes, cacahuetes et citron vert'),

('Burger Smash', '🍔', 'burger.jpg', 'Americain', '15 min', '750 kcal', '4.9', 'Double galette beurree, cheddar fondu, pickles maison'),

('Sushi Omakase', '🍣', 'sushi.jpg', 'Japonais', '60 min', '420 kcal', '5.0', 'Selection du chef thon saumon oursin et bar de ligne'),

('Shakshuka', '🍳', 'shakshuka.jpg', 'Oriental', '20 min', '390 kcal', '4.4', 'Oeufs poches dans une sauce tomate epicee aux poivrons'),

('Crepe Suzette', '🥞', 'crepes.jpg', 'Francais', '15 min', '310 kcal', '4.6', 'Crepes au beurre agrumes flambees au grand marnier'),

('Biryani agneau', '🍚', 'biryani.jpg', 'Indien', '90 min', '680 kcal', '4.8', 'Riz basmati parfume agneau tendre safran et raita'),

('Poke Bowl Saumon', '🥗', 'pokebowl.jpg', 'Hawaien', '10 min', '490 kcal', '4.7', 'Riz sushi saumon frais avocat edamame et sauce ponzu'),

('Couscous Royal', '🍲', 'couscous.jpg', 'Maghrebin', '75 min', '720 kcal', '4.9', 'Semoule fine merguez poulet legumes et bouillon parfume'),

('Tiramisu', '🍮', 'tiramisu.jpg', 'Dessert', '20 min', '380 kcal', '4.8', 'Mascarpone aerien biscuits imbibes espresso et cacao');

CREATE TABLE food_swipes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  food_id INT NOT NULL,
  action ENUM('seen', 'like', 'super', 'skip') NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,

  UNIQUE KEY unique_user_food (user_id, food_id)
);