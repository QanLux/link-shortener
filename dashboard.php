<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// Kiểm tra đăng nhập
if (!isLoggedIn()) {
    redirect('index.php');
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$error = '';
$success = '';

// Xử lý tạo link mới
if (isset($_POST['create_url'])) {
    $original_url = trim($_POST['original_url']);
    $title = trim($_POST['title']);
    $password = trim($_POST['password']);
    
    if (empty($original_url)) {
        $error = 'Vui lòng nhập URL gốc!';
    } elseif (!isValidUrl($original_url)) {
        $error = 'URL không hợp lệ!';
    } else {
        // Tạo short code mới
        do {
            $short_code = generateShortCode();
            $stmt = $pdo->prepare("SELECT id FROM urls WHERE short_code = ?");
            $stmt->execute([$short_code]);
        } while ($stmt->rowCount() > 0);
        
        // Lưu URL mới
        $hashed_password = !empty($password) ? password_hash($password, PASSWORD_DEFAULT) : null;
        $stmt = $pdo->prepare("INSERT INTO urls (user_id, original_url, short_code, password, title, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        
        if ($stmt->execute([$user_id, $original_url, $short_code, $hashed_password, $title])) {
            $success = 'Tạo link rút gọn thành công!';
        } else {
            $error = 'Có lỗi xảy ra, vui lòng thử lại!';
        }
    }
}

// Xử lý xóa URL
if (isset($_POST['delete_url']) && verifyCSRFToken($_POST['csrf_token'])) {
    $url_id = (int)$_POST['url_id'];
    $stmt = $pdo->prepare("DELETE FROM urls WHERE id = ? AND user_id = ?");
    
    if ($stmt->execute([$url_id, $user_id])) {
        $success = 'Đã xóa link thành công!';
    } else {
        $error = 'Có lỗi xảy ra khi xóa link!';
    }
}

// Xử lý cập nhật URL
if (isset($_POST['update_url']) && verifyCSRFToken($_POST['csrf_token'])) {
    $url_id = (int)$_POST['url_id'];
    $title = trim($_POST['title']);
    $password = trim($_POST['password']);
    
    $hashed_password = !empty($password) ? password_hash($password, PASSWORD_DEFAULT) : null;
    $stmt = $pdo->prepare("UPDATE urls SET title = ?, password = ?, updated_at = NOW() WHERE id = ? AND user_id = ?");
    
    if ($stmt->execute([$title, $hashed_password, $url_id, $user_id])) {
        $success = 'Cập nhật link thành công!';
    } else {
        $error = 'Có lỗi xảy ra khi cập nhật link!';
    }
}

// Lấy danh sách URLs của user
$stmt = $pdo->prepare("SELECT * FROM urls WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$urls = $stmt->fetchAll();

// Lấy thống kê tổng quan
$stmt = $pdo->prepare("SELECT COUNT(*) as total_urls, COALESCE(SUM(clicks), 0) as total_clicks FROM urls WHERE user_id = ?");
$stmt->execute([$user_id]);
$stats = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - URL Shortener</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔗 Dashboard</h1>
            <p>Chào mừng, <?php echo htmlspecialchars($username); ?>!</p>
            <div class="header-actions">
                <a href="statistics.php" class="btn btn-secondary">📊 Thống kê</a>
                <a href="logout.php" class="btn btn-danger">🚪 Đăng xuất</a>
            </div>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <!-- Thống kê tổng quan -->
        <div class="stats-overview">
            <div class="stat-card">
                <h3>Tổng số link</h3>
                <p class="stat-number"><?php echo $stats['total_urls']; ?></p>
            </div>
            <div class="stat-card">
                <h3>Tổng lượt click</h3>
                <p class="stat-number"><?php echo formatNumber($stats['total_clicks'] ?? 0); ?></p>
            </div>
        </div>

        <!-- Form tạo link mới -->
        <div class="create-url-section">
            <h2>➕ Tạo link rút gọn mới</h2>
            <form method="POST" class="create-url-form">
                <div class="form-row">
                    <div class="form-group">
                        <label for="original_url">URL gốc:</label>
                        <input type="url" id="original_url" name="original_url" placeholder="https://example.com" required>
                    </div>
                    <div class="form-group">
                        <label for="title">Tiêu đề (tùy chọn):</label>
                        <input type="text" id="title" name="title" placeholder="Mô tả link">
                    </div>
                </div>
                <div class="form-group">
                    <label for="password">Mật khẩu bảo vệ (tùy chọn):</label>
                    <input type="password" id="password" name="password" placeholder="Để trống nếu không cần bảo vệ">
                </div>
                <button type="submit" name="create_url" class="btn btn-primary">Tạo link rút gọn</button>
            </form>
        </div>

        <!-- Danh sách URLs -->
        <div class="urls-section">
            <h2>📋 Danh sách link của bạn</h2>
            <?php if (empty($urls)): ?>
                <p class="no-urls">Bạn chưa có link nào. Hãy tạo link đầu tiên!</p>
            <?php else: ?>
                <div class="urls-list">
                    <?php foreach ($urls as $url): ?>
                        <div class="url-item">
                            <div class="url-info">
                                <h3><?php echo htmlspecialchars($url['title'] ?: 'Không có tiêu đề'); ?></h3>
                                <p class="original-url"><?php echo htmlspecialchars($url['original_url']); ?></p>
                                                                 <p class="short-url">
                                     <strong>Link rút gọn:</strong> 
                                     <a href="redirect.php?code=<?php echo $url['short_code']; ?>" target="_blank">
                                         <?php echo $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>redirect.php?code=<?php echo $url['short_code']; ?>
                                     </a>
                                 </p>
                                <p class="url-stats">
                                    <span>📅 <?php echo formatDate($url['created_at']); ?></span>
                                    <span>👆 <?php echo formatNumber($url['clicks'] ?? 0); ?> lượt click</span>
                                    <?php if ($url['password']): ?>
                                        <span>🔒 Có mật khẩu</span>
                                    <?php endif; ?>
                                </p>
                            </div>
                            <div class="url-actions">
                                <button class="btn btn-small btn-secondary" onclick="editUrl(<?php echo $url['id']; ?>, '<?php echo htmlspecialchars($url['title']); ?>')">✏️ Sửa</button>
                                <form method="POST" style="display: inline;" onsubmit="return confirm('Bạn có chắc muốn xóa link này?')">
                                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                    <input type="hidden" name="url_id" value="<?php echo $url['id']; ?>">
                                    <button type="submit" name="delete_url" class="btn btn-small btn-danger">🗑️ Xóa</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal chỉnh sửa URL -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>✏️ Chỉnh sửa link</h2>
            <form method="POST" class="edit-url-form">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                <input type="hidden" name="url_id" id="edit_url_id">
                <div class="form-group">
                    <label for="edit_title">Tiêu đề:</label>
                    <input type="text" id="edit_title" name="title" required>
                </div>
                <div class="form-group">
                    <label for="edit_password">Mật khẩu mới (để trống nếu không thay đổi):</label>
                    <input type="password" id="edit_password" name="password" placeholder="Để trống nếu không thay đổi">
                </div>
                <button type="submit" name="update_url" class="btn btn-primary">Cập nhật</button>
            </form>
        </div>
    </div>

    <script>
        // Xử lý modal chỉnh sửa
        const modal = document.getElementById('editModal');
        const closeBtn = document.getElementsByClassName('close')[0];

        function editUrl(urlId, title) {
            document.getElementById('edit_url_id').value = urlId;
            document.getElementById('edit_title').value = title;
            modal.style.display = 'block';
        }

        closeBtn.onclick = function() {
            modal.style.display = 'none';
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
    </script>
</body>
</html>
