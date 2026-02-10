USE tomtroc;

INSERT INTO users (pseudo, email, password_hash, avatar_path) VALUES
('Alexlecture', 'alex@mail.com', '$2y$10$kE3uP0G2YPXvCPLfB7W5ye0B4xCWBfR5bGZpE6bq83fwtH1jJm1a2', NULL),
('Nathalire', 'nathalie@mail.com', '$2y$10$kE3uP0G2YPXvCPLfB7W5ye0B4xCWBfR5bGZpE6bq83fwtH1jJm1a2', NULL),
('Sas634', 'sas@mail.com', '$2y$10$kE3uP0G2YPXvCPLfB7W5ye0B4xCWBfR5bGZpE6bq83fwtH1jJm1a2', NULL);

-- Mot de passe pour tous: "password123" (hash ci-dessus)

INSERT INTO books (user_id, title, author, description, status, photo_path) VALUES
(1, 'The Kinfolk Table', 'Nathan Williams', 'Un livre captivant autour de la cuisine et de la convivialité.', 'available', NULL),
(2, 'Esther', 'Alabaster', 'Roman intimiste.', 'available', NULL),
(1, 'Wabi Sabi', 'Beth Kempton', 'L’art de la simplicité.', 'unavailable', NULL),
(3, 'Milk & honey', 'Rupi Kaur', 'Poésie contemporaine.', 'available', NULL);

-- Conversation exemple
INSERT INTO conversations (user_one_id, user_two_id) VALUES (1,2);

INSERT INTO messages (conversation_id, sender_id, body) VALUES
(1, 1, 'Bonjour !'),
(1, 2, 'Salut, tu es intéressé(e) par quel livre ?');
