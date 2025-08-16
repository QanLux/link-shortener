<?php
// File test đơn giản để kiểm tra kết nối database
require_once 'config/database.php';

echo "<h1>Test Database Connection</h1>";

try {
    // Test kết nối
    echo "<p>✅ Database connection successful!</p>";
    
    // Test query đơn giản
    $stmt = $pdo->query("SELECT 1 as test");
    $result = $stmt->fetch();
    echo "<p>✅ Simple query test successful!</p>";
    
    // Kiểm tra bảng users
    $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
    if ($stmt->rowCount() > 0) {
        echo "<p>✅ Table 'users' exists!</p>";
    } else {
        echo "<p>❌ Table 'users' does not exist!</p>";
    }
    
    // Kiểm tra bảng urls
    $stmt = $pdo->query("SHOW TABLES LIKE 'urls'");
    if ($stmt->rowCount() > 0) {
        echo "<p>✅ Table 'urls' exists!</p>";
    } else {
        echo "<p>❌ Table 'urls' does not exist!</p>";
    }
    
    // Kiểm tra bảng click_logs
    $stmt = $pdo->query("SHOW TABLES LIKE 'click_logs'");
    if ($stmt->rowCount() > 0) {
        echo "<p>✅ Table 'click_logs' exists!</p>";
    } else {
        echo "<p>❌ Table 'click_logs' does not exist!</p>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
}
?>
