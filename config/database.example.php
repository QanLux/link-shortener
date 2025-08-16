<?php
// File mẫu cấu hình database
// Copy file này thành database.php và sửa thông tin phù hợp

$host = 'localhost';        // Địa chỉ host database
$dbname = 'your_database';  // Tên database của bạn
$username = 'your_username'; // Username database
$password = 'your_password'; // Password database

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Lỗi kết nối database: " . $e->getMessage());
}
?>
