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

// Lấy thống kê 7 ngày gần nhất
$stmt = $pdo->prepare("
    SELECT 
        DATE(cl.clicked_at) as click_date,
        COUNT(*) as daily_clicks,
        COUNT(DISTINCT cl.url_id) as unique_urls
    FROM click_logs cl
    INNER JOIN urls u ON cl.url_id = u.id
    WHERE u.user_id = ? 
    AND cl.clicked_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
    GROUP BY DATE(cl.clicked_at)
    ORDER BY click_date DESC
");
$stmt->execute([$user_id]);
$daily_stats = $stmt->fetchAll();

// Lấy thống kê top URLs
$stmt = $pdo->prepare("
    SELECT 
        u.title,
        u.original_url,
        u.short_code,
        u.clicks,
        u.created_at
    FROM urls u
    WHERE u.user_id = ?
    ORDER BY u.clicks DESC
    LIMIT 10
");
$stmt->execute([$user_id]);
$top_urls = $stmt->fetchAll();

// Lấy thống kê tổng quan
$stmt = $pdo->prepare("
    SELECT 
        COUNT(*) as total_urls,
        COALESCE(SUM(clicks), 0) as total_clicks,
        COALESCE(AVG(clicks), 0) as avg_clicks,
        COALESCE(MAX(clicks), 0) as max_clicks
    FROM urls 
    WHERE user_id = ?
");
$stmt->execute([$user_id]);
$overview_stats = $stmt->fetch();

// Tạo dữ liệu cho biểu đồ (7 ngày gần nhất)
$chart_data = [];
$chart_labels = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $chart_labels[] = date('d/m', strtotime($date));
    
    $found = false;
    foreach ($daily_stats as $stat) {
        if ($stat['click_date'] == $date) {
            $chart_data[] = $stat['daily_clicks'];
            $found = true;
            break;
        }
    }
    if (!$found) {
        $chart_data[] = 0;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thống kê - URL Shortener</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📊 Thống kê chi tiết</h1>
            <p>Chào mừng, <?php echo htmlspecialchars($username); ?>!</p>
            <div class="header-actions">
                <a href="dashboard.php" class="btn btn-secondary">🔗 Dashboard</a>
                <a href="logout.php" class="btn btn-danger">🚪 Đăng xuất</a>
            </div>
        </div>

        <!-- Thống kê tổng quan -->
        <div class="stats-overview">
            <div class="stat-card">
                <h3>Tổng số link</h3>
                <p class="stat-number"><?php echo $overview_stats['total_urls']; ?></p>
            </div>
            <div class="stat-card">
                <h3>Tổng lượt click</h3>
                <p class="stat-number"><?php echo formatNumber($overview_stats['total_clicks'] ?? 0); ?></p>
            </div>
            <div class="stat-card">
                <h3>Trung bình click/link</h3>
                <p class="stat-number"><?php echo round($overview_stats['avg_clicks'] ?? 0, 1); ?></p>
            </div>
            <div class="stat-card">
                <h3>Link có nhiều click nhất</h3>
                <p class="stat-number"><?php echo formatNumber($overview_stats['max_clicks'] ?? 0); ?></p>
            </div>
        </div>

        <!-- Biểu đồ 7 ngày gần nhất -->
        <div class="chart-section">
            <h2>📈 Lượt click 7 ngày gần nhất</h2>
            <div class="chart-container">
                <canvas id="clicksChart"></canvas>
            </div>
        </div>

        <!-- Top URLs -->
        <div class="top-urls-section">
            <h2>🏆 Top 10 link có nhiều lượt click nhất</h2>
            <?php if (empty($top_urls)): ?>
                <p class="no-data">Chưa có dữ liệu thống kê.</p>
            <?php else: ?>
                <div class="top-urls-list">
                    <?php foreach ($top_urls as $index => $url): ?>
                        <div class="top-url-item">
                            <div class="rank">#<?php echo $index + 1; ?></div>
                            <div class="url-info">
                                <h3><?php echo htmlspecialchars($url['title'] ?: 'Không có tiêu đề'); ?></h3>
                                <p class="original-url"><?php echo htmlspecialchars($url['original_url']); ?></p>
                                                                 <p class="short-url">
                                     <strong>Link rút gọn:</strong> 
                                     <a href="redirect.php?code=<?php echo $url['short_code']; ?>" target="_blank">
                                         <?php echo $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>redirect.php?code=<?php echo $url['short_code']; ?>
                                     </p>
                            </div>
                            <div class="url-stats">
                                <span class="clicks">👆 <?php echo formatNumber($url['clicks'] ?? 0); ?> lượt</span>
                                <span class="date">📅 <?php echo formatDate($url['created_at']); ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Thống kê chi tiết theo ngày -->
        <div class="daily-stats-section">
            <h2>📅 Thống kê chi tiết theo ngày</h2>
            <?php if (empty($daily_stats)): ?>
                <p class="no-data">Chưa có dữ liệu thống kê trong 7 ngày gần nhất.</p>
            <?php else: ?>
                <div class="daily-stats-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Ngày</th>
                                <th>Lượt click</th>
                                <th>Số link được click</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($daily_stats as $stat): ?>
                                <tr>
                                    <td><?php echo date('d/m/Y', strtotime($stat['click_date'])); ?></td>
                                    <td><?php echo formatNumber($stat['daily_clicks'] ?? 0); ?></td>
                                    <td><?php echo $stat['unique_urls'] ?? 0; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Biểu đồ thống kê
        const ctx = document.getElementById('clicksChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($chart_labels); ?>,
                datasets: [{
                    label: 'Lượt click',
                    data: <?php echo json_encode($chart_data); ?>,
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    tension: 0.1,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Lượt click theo ngày'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
