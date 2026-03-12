<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$new_password = $_POST['new_password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

// Validate
if (strlen($new_password) < 6) {
    $_SESSION['password_message'] = 'Password must be at least 6 characters.';
    $_SESSION['password_message_type'] = 'error';
    header('Location: account.php#passwordCard');
    exit();
}

if ($new_password !== $confirm_password) {
    $_SESSION['password_message'] = 'Passwords do not match.';
    $_SESSION['password_message_type'] = 'error';
    header('Location: account.php#passwordCard');
    exit();
}

// Hash password and update
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
$stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
$stmt->bind_param("si", $hashed_password, $user_id);

if ($stmt->execute()) {
    $_SESSION['password_message'] = 'Password updated successfully.';
    $_SESSION['password_message_type'] = 'success';
} else {
    $_SESSION['password_message'] = 'Error updating password.';
    $_SESSION['password_message_type'] = 'error';
}

header('Location: account.php#passwordCard');
exit();
?>

<!-- LOGOUT -->
<?php
session_start();
session_destroy();
header('Location: login.php');
exit();
?>

<!-- DATABASE -->
CREATE DATABASE addictech_db;
USE addictech_db;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    phone VARCHAR(20),
    address TEXT,
    country VARCHAR(50) DEFAULT 'Philippines',
    language VARCHAR(50) DEFAULT 'English',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    payment_method VARCHAR(50) NOT NULL,
    status ENUM('pending', 'completed', 'cancelled') DEFAULT 'pending',
    total DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);