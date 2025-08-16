<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// Lấy short code từ query parameter
$short_code = isset($_GET['code']) ? trim($_GET['code']) : '';

// Debug: in ra để kiểm tra
error_log("GET code: " . ($_GET['code'] ?? 'N/A'));
error_log("Short code: " . $short_code);

// Nếu không có short code, chuyển về trang chủ
if (empty($short_code)) {
    header('Location: index.php');
    exit();
}

// Tìm URL trong database
$stmt = $pdo->prepare("SELECT * FROM urls WHERE short_code = ? AND is_active = 1");
$stmt->execute([$short_code]);
$url = $stmt->fetch();

if (!$url) {
    // URL không tồn tại
    http_response_code(404);
    ?>
    <!DOCTYPE html>
    <html lang="vi">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Link không tồn tại - URL Shortener</title>
        <link rel="stylesheet" href="assets/css/style.css">
    </head>
    <body>
        <div class="container">
            <div class="error-page">
                <h1>❌ Link không tồn tại</h1>
                <p>Link bạn đang tìm kiếm không tồn tại hoặc đã bị xóa.</p>
                <a href="index.php" class="btn btn-primary">Về trang chủ</a>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit();
}

// Kiểm tra xem có cần nhập mật khẩu không
if ($url['password'] && !isset($_SESSION['url_' . $url['id'] . '_verified'])) {
    $error = '';
    
    // Xử lý form nhập mật khẩu
    if (isset($_POST['submit_password'])) {
        $input_password = $_POST['password'];
        
        if (password_verify($input_password, $url['password'])) {
            // Mật khẩu đúng, lưu vào session
            $_SESSION['url_' . $url['id'] . '_verified'] = true;
            
            // Ghi log click
            logClick($pdo, $url['id']);
            
            // Chuyển hướng đến URL gốc
            header('Location: ' . $url['original_url']);
            exit();
        } else {
            $error = 'Mật khẩu không đúng! Vui lòng thử lại.';
        }
    }
    
    // Hiển thị form nhập mật khẩu
    ?>
    <!DOCTYPE html>
    <html lang="vi">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Nhập mật khẩu - URL Shortener</title>
        <link rel="stylesheet" href="assets/css/style.css">
    </head>
    <body>
        <div class="container">
            <div class="password-form-container">
                <div class="password-form">
                    <h1>🔒 Link được bảo vệ</h1>
                    <p>Link này được bảo vệ bằng mật khẩu. Vui lòng nhập mật khẩu để tiếp tục.</p>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" class="password-form">
                        <div class="form-group">
                            <label for="password">Mật khẩu:</label>
                            <input type="password" id="password" name="password" required autofocus>
                        </div>
                        <button type="submit" name="submit_password" class="btn btn-primary">Tiếp tục</button>
                    </form>
                    
                    <div class="form-footer">
                        <a href="index.php">← Về trang chủ</a>
                    </div>
                </div>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit();
}

// Nếu không có mật khẩu hoặc đã xác thực, chuyển hướng
if (!isset($_SESSION['url_' . $url['id'] . '_verified'])) {
    // Ghi log click
    logClick($pdo, $url['id']);
}

// Chuyển hướng đến URL gốc
header('Location: ' . $url['original_url']);
exit();

// Hàm ghi log click
function logClick($pdo, $url_id) {
    // Cập nhật số lượt click
    $stmt = $pdo->prepare("UPDATE urls SET clicks = clicks + 1 WHERE id = ?");
    $stmt->execute([$url_id]);
    
    // Ghi log chi tiết
    $stmt = $pdo->prepare("INSERT INTO click_logs (url_id, ip_address, user_agent, referer, clicked_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->execute([
        $url_id,
        getClientIP(),
        $_SERVER['HTTP_USER_AGENT'] ?? '',
        $_SERVER['HTTP_REFERER'] ?? ''
    ]);
}
?>
