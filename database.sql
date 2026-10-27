-- NOVA Festival - database schema and demo data
-- Import with phpMyAdmin (Import tab) or: mysql -u root < database.sql

CREATE DATABASE IF NOT EXISTS festival CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE festival;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS e_tickets, payments, purchases, event_artists, events, artists, ticket_types, user_roles, roles, users;
SET FOREIGN_KEY_CHECKS = 1;

-- ---------------------------------------------------------------
-- Users & roles
-- ---------------------------------------------------------------

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(30) NULL UNIQUE,
    registered_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE user_roles (
    user_id INT NOT NULL,
    role_id INT NOT NULL,
    assigned_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id, role_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------
-- Events & artists
-- ---------------------------------------------------------------

CREATE TABLE artists (
    id INT AUTO_INCREMENT PRIMARY KEY,
    artist_name VARCHAR(100) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_name VARCHAR(150) NOT NULL,
    event_date DATETIME NOT NULL,
    description TEXT NULL
) ENGINE=InnoDB;

CREATE TABLE event_artists (
    event_id INT NOT NULL,
    artist_id INT NOT NULL,
    PRIMARY KEY (event_id, artist_id),
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (artist_id) REFERENCES artists(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------
-- Tickets, purchases & payments
-- ---------------------------------------------------------------

CREATE TABLE ticket_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ticket_name VARCHAR(100) NOT NULL,
    description TEXT NULL,
    price DECIMAL(10,2) NOT NULL,
    booking_fee DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    availability INT NOT NULL DEFAULT 0
) ENGINE=InnoDB;

CREATE TABLE purchases (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    ticket_type_id INT NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    purchase_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'confirmed', 'refunded', 'cancelled') NOT NULL DEFAULT 'pending',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (ticket_type_id) REFERENCES ticket_types(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    purchase_id INT NOT NULL,
    payment_method ENUM('credit_card', 'debit_card', 'paypal', 'bank_transfer') NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_status ENUM('pending', 'completed', 'failed', 'refunded') NOT NULL DEFAULT 'pending',
    transaction_id VARCHAR(100) NOT NULL UNIQUE,
    payment_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (purchase_id) REFERENCES purchases(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE e_tickets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    purchase_id INT NOT NULL UNIQUE,
    eticket_token VARCHAR(255) NOT NULL UNIQUE,
    status ENUM('active', 'used', 'cancelled') DEFAULT 'active',
    generated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (purchase_id) REFERENCES purchases(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------
-- Demo data
-- ---------------------------------------------------------------

-- role_id 1 = admin, 2 = user (register.php assigns role 2 to new accounts)
INSERT INTO roles (id, role_name) VALUES (1, 'admin'), (2, 'user');

-- Demo accounts:
--   admin@novafestival.com / admin123
--   user@novafestival.com  / user1234
INSERT INTO users (id, name, email, password, phone) VALUES
(1, 'Admin', 'admin@novafestival.com', '$2y$10$CwPwSJEy2KrsKsVVEpw/OuLrMCqDpCxpMlm8Bu0xTtV27e03ix2vW', NULL),
(2, 'Demo User', 'user@novafestival.com', '$2y$10$W3BZ0F/far0cGpRnmuo8BeLrQn9BMpS9VlmnhMHnQHFpkxpWHdK0y', NULL);

INSERT INTO user_roles (user_id, role_id) VALUES (1, 1), (2, 2);

INSERT INTO artists (artist_name) VALUES
('Lana Del Rey'), ('Lola Young'), ('Neon Vibes'), ('Shawn Mendes'),
('Dua Lipa'), ('Electric Soul'), ('BTS');

INSERT INTO events (event_name, event_date, description) VALUES
('Opening Night - Main Stage', '2026-07-31 20:00:00', 'The festival kicks off with the biggest names on the main stage.'),
('Electro Night', '2026-08-01 21:00:00', 'A full night of electronic music on the Electro stage.'),
('Closing Night - Main Stage', '2026-08-02 20:00:00', 'The grand finale of NOVA Festival 2026.');

INSERT INTO event_artists (event_id, artist_id) VALUES
(1, 1), (1, 2), (1, 3),
(2, 3), (2, 6),
(3, 4), (3, 5), (3, 7);

INSERT INTO ticket_types (ticket_name, description, price, booking_fee, availability) VALUES
('Day Pass', 'Access to all stages for one festival day.', 49.00, 3.00, 2000),
('3-Day Pass', 'Full access to all three festival days.', 119.00, 5.00, 1500),
('VIP Day Pass', 'One day with VIP lounge access and a dedicated entrance.', 129.00, 6.00, 300),
('VIP 3-Day Pass', 'All three days with VIP lounge, fast-track entry and private bars.', 299.00, 9.00, 200),
('Camping Pass', 'Three days of festival access plus a camping spot.', 159.00, 6.00, 500),
('Backstage Experience', 'Exclusive backstage tour and meet and greet with the artists.', 499.00, 12.00, 50);
