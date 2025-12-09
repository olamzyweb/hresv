CREATE DATABASE IF NOT EXISTS jhotel CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE jhotel;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  phone VARCHAR(50),
  role ENUM('guest','admin') DEFAULT 'guest',
  is_email_verified TINYINT(1) DEFAULT 0,
  email_verification_token VARCHAR(100),
  password_reset_token VARCHAR(100),
  token_expires_at DATETIME NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL
);

CREATE TABLE IF NOT EXISTS room_types (
  id INT AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(50) UNIQUE,
  title VARCHAR(150) NOT NULL,
  description TEXT,
  capacity INT NOT NULL DEFAULT 1,
  base_price DECIMAL(10,2) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS rooms (
  id INT AUTO_INCREMENT PRIMARY KEY,
  room_number VARCHAR(50) NOT NULL UNIQUE,
  room_type_id INT NOT NULL,
  status ENUM('available','maintenance','occupied') DEFAULT 'available',
  floor VARCHAR(50),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (room_type_id) REFERENCES room_types(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS room_images (
  id INT AUTO_INCREMENT PRIMARY KEY,
  room_type_id INT NOT NULL,
  filename VARCHAR(255) NOT NULL,
  ordinal INT DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (room_type_id) REFERENCES room_types(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS bookings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  booking_ref VARCHAR(30) NOT NULL UNIQUE,
  user_id INT NULL,
  guest_name VARCHAR(150) NOT NULL,
  guest_email VARCHAR(255) NOT NULL,
  guest_phone VARCHAR(50),
  room_id INT NOT NULL,
  room_type_id INT NOT NULL,
  checkin_date DATE NOT NULL,
  checkout_date DATE NOT NULL,
  nights INT NOT NULL,
  total_amount DECIMAL(10,2) NOT NULL,
  status ENUM('pending','confirmed','checked_in','checked_out','cancelled') DEFAULT 'pending',
  payment_status ENUM('unpaid','paid','refunded') DEFAULT 'unpaid',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL,
  FOREIGN KEY (room_id) REFERENCES rooms(id),
  FOREIGN KEY (room_type_id) REFERENCES room_types(id),
  FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS coupons (
  id INT AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(50) NOT NULL UNIQUE,
  type ENUM('percentage','fixed') NOT NULL,
  value DECIMAL(10,2) NOT NULL,
  min_amount DECIMAL(10,2) DEFAULT 0,
  start_date DATE DEFAULT NULL,
  end_date DATE DEFAULT NULL,
  usage_limit INT DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS payments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  booking_id INT NOT NULL,
  provider VARCHAR(100),
  provider_payment_id VARCHAR(255),
  amount DECIMAL(10,2) NOT NULL,
  currency VARCHAR(10) DEFAULT 'USD',
  status ENUM('pending','success','failed','refunded') DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (booking_id) REFERENCES bookings(id)
);

CREATE TABLE IF NOT EXISTS audit_logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  admin_user_id INT,
  action VARCHAR(255),
  meta JSON,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (admin_user_id) REFERENCES users(id)
);

