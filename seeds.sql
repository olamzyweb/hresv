USE jhotel;

INSERT INTO users (name, email, password_hash, phone, role, is_email_verified)
VALUES ('Admin', 'admin@demo.test', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '000-000-0000', 'admin', 1)
ON DUPLICATE KEY UPDATE email=email;

INSERT INTO room_types (code, title, description, capacity, base_price) VALUES
('STD', 'Standard Room', 'Cozy standard room with queen bed.', 2, 120.00),
('DLX', 'Deluxe Room', 'Spacious deluxe room with balcony.', 3, 180.00),
('STE', 'Suite', 'Suite with living area and city view.', 4, 260.00)
ON DUPLICATE KEY UPDATE title=VALUES(title);

INSERT INTO rooms (room_number, room_type_id, status, floor) VALUES
('101', (SELECT id FROM room_types WHERE code='STD'), 'available', '1'),
('102', (SELECT id FROM room_types WHERE code='STD'), 'available', '1'),
('201', (SELECT id FROM room_types WHERE code='DLX'), 'available', '2'),
('202', (SELECT id FROM room_types WHERE code='DLX'), 'available', '2'),
('301', (SELECT id FROM room_types WHERE code='STE'), 'available', '3')
ON DUPLICATE KEY UPDATE status=VALUES(status);

INSERT INTO coupons (code, type, value, min_amount, start_date, end_date, usage_limit) VALUES
('WELCOME10', 'percentage', 10, 0, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 90 DAY), 500),
('FLAT20', 'fixed', 20, 150, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 90 DAY), 300)
ON DUPLICATE KEY UPDATE value=VALUES(value);

