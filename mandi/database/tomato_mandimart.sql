-- ============================================
-- TOMATO MANDIMART - COMPLETE DATABASE
-- For: MySQL 5.7+ / MariaDB 10.3+
-- ============================================

CREATE DATABASE IF NOT EXISTS tomato_mandimart CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE tomato_mandimart;

-- ============================================
-- TABLE: users (Farmers, Buyers, Admins)
-- ============================================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(15) UNIQUE NOT NULL,
    email VARCHAR(100),
    password_hash VARCHAR(255) NOT NULL,
    user_type ENUM('farmer', 'buyer', 'admin') DEFAULT 'farmer',
    mandi_name VARCHAR(100),
    location VARCHAR(100),
    rating DECIMAL(2,1) DEFAULT 5.0,
    status ENUM('active', 'pending', 'suspended') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================
-- TABLE: mandis (Market yards)
-- ============================================
CREATE TABLE mandis (
    id INT AUTO_INCREMENT PRIMARY KEY,
    mandi_name VARCHAR(100) NOT NULL,
    location VARCHAR(100) NOT NULL,
    state VARCHAR(50),
    distance_km DECIMAL(5,1),
    latitude DECIMAL(10,8),
    longitude DECIMAL(11,8),
    status ENUM('active', 'inactive') DEFAULT 'active'
) ENGINE=InnoDB;

-- ============================================
-- TABLE: mandi_prices (Daily tomato prices)
-- ============================================
CREATE TABLE mandi_prices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    mandi_id INT NOT NULL,
    variety VARCHAR(50) NOT NULL,
    min_price INT NOT NULL,
    max_price INT NOT NULL,
    modal_price INT NOT NULL,
    trend ENUM('up', 'down', 'flat') DEFAULT 'flat',
    price_date DATE NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (mandi_id) REFERENCES mandis(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================
-- TABLE: crop_listings (Farmer's tomato listings)
-- ============================================
CREATE TABLE crop_listings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    variety VARCHAR(50) NOT NULL,
    quantity_quintals INT NOT NULL,
    grade ENUM('A', 'B', 'C') DEFAULT 'B',
    expected_price INT NOT NULL,
    location VARCHAR(100),
    description TEXT,
    image_path VARCHAR(255),
    status ENUM('active', 'sold', 'expired') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================
-- TABLE: auctions (Live tomato auctions)
-- ============================================
CREATE TABLE auctions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    variety VARCHAR(50) NOT NULL,
    quantity_quintals INT NOT NULL,
    base_price INT NOT NULL,
    current_price INT NOT NULL,
    ends_at DATETIME NOT NULL,
    grade ENUM('A', 'B', 'C') DEFAULT 'A',
    winner_id INT,
    bids_count INT DEFAULT 0,
    status ENUM('live', 'ended', 'cancelled') DEFAULT 'live',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (winner_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ============================================
-- TABLE: bids (Auction bids)
-- ============================================
CREATE TABLE bids (
    id INT AUTO_INCREMENT PRIMARY KEY,
    auction_id INT NOT NULL,
    buyer_id INT NOT NULL,
    bid_amount INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (auction_id) REFERENCES auctions(id) ON DELETE CASCADE,
    FOREIGN KEY (buyer_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================
-- TABLE: orders (Direct purchases)
-- ============================================
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    listing_id INT,
    auction_id INT,
    buyer_id INT NOT NULL,
    seller_id INT NOT NULL,
    total_amount INT NOT NULL,
    status ENUM('pending', 'completed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (listing_id) REFERENCES crop_listings(id) ON DELETE SET NULL,
    FOREIGN KEY (auction_id) REFERENCES auctions(id) ON DELETE SET NULL,
    FOREIGN KEY (buyer_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (seller_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================
-- TABLE: messages (Buyer-Farmer chat)
-- ============================================
CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    message TEXT NOT NULL,
    is_read TINYINT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================
-- TABLE: chatbot_logs
-- ============================================
CREATE TABLE chatbot_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    question TEXT NOT NULL,
    answer TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ============================================
-- TABLE: admin_logs
-- ============================================
CREATE TABLE admin_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id INT NOT NULL,
    action VARCHAR(255) NOT NULL,
    details TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================
-- SEED DATA
-- ============================================

-- Insert Mandis
INSERT INTO mandis (mandi_name, location, state, distance_km) VALUES
('Azadpur Mandi', 'Delhi', 'Delhi', 2.3),
('Ghazipur Mandi', 'Delhi', 'Delhi', 5.1),
('Keshopur Mandi', 'Delhi', 'Delhi', 8.4),
('Okhla Mandi', 'Delhi', 'Delhi', 9.0),
('Narela Mandi', 'Delhi', 'Delhi', 14.2),
('Karnal Mandi', 'Karnal', 'Haryana', 120.0),
('Ludhiana Mandi', 'Ludhiana', 'Punjab', 310.0),
('Jaipur Mandi', 'Jaipur', 'Rajasthan', 270.0);

-- Insert Users (password: 'password123' for all demo accounts)
INSERT INTO users (full_name, phone, email, password_hash, user_type, mandi_name, location, rating, status) VALUES
('Ramesh Kumar', '9876543210', 'ramesh@tomatomart.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'farmer', 'Azadpur Mandi', 'Delhi', 4.8, 'active'),
('Sunita Devi', '9876543211', 'sunita@tomatomart.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'farmer', 'Ghazipur Mandi', 'Delhi', 4.5, 'active'),
('Gurpreet Singh', '9876543212', 'gurpreet@tomatomart.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'farmer', 'Keshopur Mandi', 'Delhi', 4.9, 'active'),
('Amit Patel', '9876543213', 'amit@tomatomart.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'buyer', 'Azadpur Mandi', 'Delhi', 4.7, 'active'),
('Priya Sharma', '9876543214', 'priya@tomatomart.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'buyer', 'Ghazipur Mandi', 'Delhi', 4.6, 'active'),
('Rajesh Verma', '9876543215', 'rajesh@tomatomart.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'buyer', 'Okhla Mandi', 'Delhi', 4.8, 'active'),
('Admin User', '9999999999', 'admin@tomatomart.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'All Mandis', 'Delhi', 5.0, 'active');

-- Insert Mandi Prices (today's prices)
INSERT INTO mandi_prices (mandi_id, variety, min_price, max_price, modal_price, trend, price_date) VALUES
(1, 'Desi/Local', 1200, 1800, 1500, 'up', CURDATE()),
(1, 'Hybrid TO-1057', 2000, 2800, 2400, 'up', CURDATE()),
(1, 'Cherry Tomato', 3000, 4500, 3750, 'flat', CURDATE()),
(1, 'Roma/Plum', 1500, 2200, 1850, 'flat', CURDATE()),
(2, 'Desi/Local', 1100, 1700, 1400, 'down', CURDATE()),
(2, 'Hybrid TO-1057', 1900, 2600, 2250, 'up', CURDATE()),
(3, 'Desi/Local', 1300, 1900, 1600, 'up', CURDATE()),
(3, 'Cherry Tomato', 3200, 4800, 4000, 'up', CURDATE()),
(4, 'Roma/Plum', 1500, 2200, 1850, 'flat', CURDATE()),
(5, 'Hybrid TO-1057', 2100, 2800, 2450, 'up', CURDATE()),
(6, 'Desi/Local', 1000, 1500, 1250, 'down', CURDATE());

-- Insert Crop Listings
INSERT INTO crop_listings (user_id, variety, quantity_quintals, grade, expected_price, location, description, status) VALUES
(1, 'Desi/Local', 50, 'A', 1500, 'Azadpur', 'Fresh desi tomatoes, hand-picked daily from our farm. Organically grown with drip irrigation. Perfect for curries and salads.', 'active'),
(1, 'Roma/Plum', 80, 'A', 2000, 'Azadpur', 'Premium Roma tomatoes ideal for paste, sauce, and ketchup making. Dense flesh, fewer seeds.', 'active'),
(2, 'Hybrid TO-1057', 120, 'B', 2250, 'Ghazipur', 'High-yield hybrid variety, disease resistant. Good for commercial bulk buyers and processing units.', 'active'),
(2, 'Desi/Local', 40, 'B', 1400, 'Ghazipur', 'Standard quality desi tomatoes. Affordable price for bulk purchase.', 'active'),
(3, 'Cherry Tomato', 30, 'A', 3500, 'Keshopur', 'Premium cherry tomatoes for salads, garnishing, and high-end restaurants. Sweet and juicy.', 'active'),
(3, 'Hybrid TO-1057', 90, 'A', 2500, 'Keshopur', 'Top grade hybrid tomatoes. Uniform size, excellent shelf life. Perfect for retail chains.', 'active');

-- Insert Auctions
INSERT INTO auctions (user_id, variety, quantity_quintals, base_price, current_price, ends_at, grade, status) VALUES
(1, 'Desi/Local', 100, 1400, 1400, DATE_ADD(NOW(), INTERVAL 45 MINUTE), 'A', 'live'),
(2, 'Hybrid TO-1057', 200, 2200, 2200, DATE_ADD(NOW(), INTERVAL 60 MINUTE), 'B', 'live'),
(3, 'Cherry Tomato', 50, 3200, 3200, DATE_ADD(NOW(), INTERVAL 30 MINUTE), 'A', 'live');

-- Insert sample orders
INSERT INTO orders (listing_id, buyer_id, seller_id, total_amount, status) VALUES
(1, 4, 1, 75000, 'completed'),
(2, 5, 1, 160000, 'completed'),
(3, 6, 2, 270000, 'pending');

-- Create indexes for better performance
CREATE INDEX idx_prices_date ON mandi_prices(price_date);
CREATE INDEX idx_prices_variety ON mandi_prices(variety);
CREATE INDEX idx_listings_status ON crop_listings(status);
CREATE INDEX idx_listings_user ON crop_listings(user_id);
CREATE INDEX idx_auctions_status ON auctions(status);
CREATE INDEX idx_messages_sender ON messages(sender_id);
CREATE INDEX idx_messages_receiver ON messages(receiver_id);

