-- Create Super Admin Users Table
CREATE TABLE IF NOT EXISTS `super_admin_users` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `email` varchar(100) NOT NULL UNIQUE,
    `password` varchar(255) NOT NULL,
    `name` varchar(100) NOT NULL,
    `status` enum('active', 'inactive') DEFAULT 'active',
    `otp_code` varchar(6) DEFAULT NULL,
    `otp_expires_at` timestamp NULL DEFAULT NULL,
    `last_login` timestamp NULL DEFAULT NULL,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert a default super admin user (you can change this)
-- Password: SuperAdmin@2025 (hashed with password_hash)
INSERT INTO `super_admin_users` (`email`, `password`, `name`, `status`) VALUES 
('admin@supercare.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Super Admin', 'active');

-- Create OTP logs table for tracking
CREATE TABLE IF NOT EXISTS `super_admin_otp_logs` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `email` varchar(100) NOT NULL,
    `otp_code` varchar(6) NOT NULL,
    `ip_address` varchar(45) NOT NULL,
    `attempt_count` int(11) DEFAULT 1,
    `status` enum('pending', 'used', 'expired', 'failed') DEFAULT 'pending',
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `used_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `email` (`email`),
    KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
