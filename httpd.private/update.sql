CREATE TABLE IF NOT EXISTS logs (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    log_type ENUM('error', 'action', 'info') NOT NULL,
    user_id INT(11) DEFAULT NULL,
    client_id INT(11) DEFAULT NULL,
    action VARCHAR(255) NOT NULL,
    script VARCHAR(255) NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    additional_info TEXT DEFAULT NULL,
    INDEX (user_id),
    INDEX (client_id),
    INDEX (created_at)
);