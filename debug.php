<?php
echo "<h1>🔍 Debug Information</h1>";

echo "<h2>Server Information:</h2>";
echo "<p><strong>HTTP_HOST:</strong> " . ($_SERVER['HTTP_HOST'] ?? 'N/A') . "</p>";
echo "<p><strong>REQUEST_URI:</strong> " . ($_SERVER['REQUEST_URI'] ?? 'N/A') . "</p>";
echo "<p><strong>SCRIPT_NAME:</strong> " . ($_SERVER['SCRIPT_NAME'] ?? 'N/A') . "</p>";
echo "<p><strong>PHP_SELF:</strong> " . ($_SERVER['PHP_SELF'] ?? 'N/A') . "</p>";
echo "<p><strong>QUERY_STRING:</strong> " . ($_SERVER['QUERY_STRING'] ?? 'N/A') . "</p>";

echo "<h2>URL Parsing:</h2>";
$request_uri = $_SERVER['REQUEST_URI'] ?? '';
$path = parse_url($request_uri, PHP_URL_PATH);
$short_code = trim($path, '/');

echo "<p><strong>Request URI:</strong> " . $request_uri . "</p>";
echo "<p><strong>Parsed Path:</strong> " . $path . "</p>";
echo "<p><strong>Short Code:</strong> " . $short_code . "</p>";

echo "<h2>Database Test:</h2>";
try {
    require_once 'config/database.php';
    echo "<p>✅ Database connection successful!</p>";
    
    // Test tìm short code
    if (!empty($short_code)) {
        $stmt = $pdo->prepare("SELECT * FROM urls WHERE short_code = ? AND is_active = 1");
        $stmt->execute([$short_code]);
        $url = $stmt->fetch();
        
        if ($url) {
            echo "<p>✅ Found URL: " . htmlspecialchars($url['original_url']) . "</p>";
            echo "<p><strong>Title:</strong> " . htmlspecialchars($url['title'] ?? 'No title') . "</p>";
            echo "<p><strong>Has Password:</strong> " . ($url['password'] ? 'Yes' : 'No') . "</p>";
        } else {
            echo "<p>❌ URL not found for short code: " . htmlspecialchars($short_code) . "</p>";
        }
    }
    
} catch (Exception $e) {
    echo "<p>❌ Database Error: " . $e->getMessage() . "</p>";
}

echo "<h2>Available URLs in Database:</h2>";
try {
    $stmt = $pdo->query("SELECT short_code, title, original_url FROM urls LIMIT 5");
    $urls = $stmt->fetchAll();
    
    if (empty($urls)) {
        echo "<p>No URLs found in database</p>";
    } else {
        echo "<ul>";
        foreach ($urls as $url) {
            echo "<li><strong>" . htmlspecialchars($url['short_code']) . "</strong> - " . htmlspecialchars($url['title'] ?? 'No title') . "</li>";
        }
        echo "</ul>";
    }
} catch (Exception $e) {
    echo "<p>❌ Error fetching URLs: " . $e->getMessage() . "</p>";
}
?>
