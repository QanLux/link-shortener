# 🔧 Hướng dẫn khắc phục lỗi

## Lỗi thường gặp và cách khắc phục

### 1. Lỗi SQL Syntax Error (1064)

**Lỗi:** `Error (1064): You have an error in your SQL syntax`

**Nguyên nhân:** 
- Lỗi cú pháp SQL trong các query
- Sử dụng hàm `SUM()`, `AVG()`, `MAX()` với giá trị NULL
- Thiếu bảng hoặc cột trong database

**Cách khắc phục:**

#### Bước 1: Kiểm tra database
Chạy file `test.php` để kiểm tra kết nối và cấu trúc database:
```
http://localhost/your-project/test.php
```

#### Bước 2: Import lại database
```sql
-- Xóa database cũ (nếu có)
DROP DATABASE IF EXISTS url_shortener;

-- Tạo database mới
CREATE DATABASE url_shortener CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE url_shortener;

-- Import cấu trúc bảng
SOURCE database.sql;
```

#### Bước 3: Kiểm tra cấu hình
Đảm bảo file `config/database.php` có thông tin đúng:
```php
$host = 'localhost';
$dbname = 'url_shortener';
$username = 'root';  // hoặc username của bạn
$password = '';       // hoặc password của bạn
```

### 2. Lỗi "Table doesn't exist"

**Nguyên nhân:** Database chưa được tạo hoặc import

**Cách khắc phục:**
1. Tạo database mới
2. Import file `database.sql`
3. Kiểm tra quyền truy cập database

### 3. Lỗi "Access denied for user"

**Nguyên nhân:** Sai username/password hoặc user không có quyền

**Cách khắc phục:**
1. Kiểm tra thông tin đăng nhập MySQL
2. Tạo user mới với quyền đầy đủ:
```sql
CREATE USER 'urluser'@'localhost' IDENTIFIED BY 'password';
GRANT ALL PRIVILEGES ON url_shortener.* TO 'urluser'@'localhost';
FLUSH PRIVILEGES;
```

### 4. Lỗi "mod_rewrite not enabled"

**Nguyên nhân:** Apache mod_rewrite chưa được bật

**Cách khắc phục:**
```bash
# Ubuntu/Debian
sudo a2enmod rewrite
sudo systemctl restart apache2

# CentOS/RHEL
sudo yum install mod_rewrite
sudo systemctl restart httpd
```

### 5. Lỗi "Permission denied"

**Nguyên nhân:** Quyền thư mục không đúng

**Cách khắc phục:**
```bash
chmod 755 assets/css/
chmod 644 assets/css/style.css
chmod 644 *.php
chmod 644 config/*.php
chmod 644 includes/*.php
```

### 6. Lỗi "Function not found"

**Nguyên nhân:** PHP extension chưa được bật

**Cách khắc phục:**
1. Bật các extension cần thiết trong `php.ini`:
```ini
extension=pdo_mysql
extension=openssl
```

2. Restart web server

### 7. Lỗi "Session cannot be started"

**Nguyên nhân:** Vấn đề với session

**Cách khắc phục:**
1. Kiểm tra quyền thư mục session
2. Đảm bảo `session_start()` được gọi đầu tiên

### 8. Lỗi "CSRF token mismatch"

**Nguyên nhân:** Session bị mất hoặc token không khớp

**Cách khắc phục:**
1. Xóa cache trình duyệt
2. Đăng nhập lại
3. Kiểm tra session configuration

## Kiểm tra hệ thống

### 1. Yêu cầu hệ thống
- PHP >= 7.4
- MySQL >= 5.7
- Apache/Nginx với mod_rewrite
- Extensions: PDO, OpenSSL

### 2. Kiểm tra PHP
```bash
php -v
php -m | grep -E "(pdo|openssl)"
```

### 3. Kiểm tra MySQL
```bash
mysql --version
mysql -u root -p -e "SHOW DATABASES;"
```

### 4. Kiểm tra Apache
```bash
apache2ctl -M | grep rewrite
# hoặc
httpd -M | grep rewrite
```

## Log files

### 1. PHP Error Log
```bash
# Ubuntu/Debian
tail -f /var/log/apache2/error.log

# CentOS/RHEL
tail -f /var/log/httpd/error_log
```

### 2. MySQL Error Log
```bash
tail -f /var/log/mysql/error.log
```

## Test từng bước

### 1. Test database connection
```
http://localhost/your-project/test.php
```

### 2. Test trang chủ
```
http://localhost/your-project/index.php
```

### 3. Test tạo user
- Đăng ký tài khoản mới
- Kiểm tra database có user mới không

### 4. Test tạo link
- Đăng nhập
- Tạo link rút gọn
- Kiểm tra database có link mới không

## Liên hệ hỗ trợ

Nếu vẫn gặp vấn đề:
1. Chụp màn hình lỗi
2. Copy nội dung log file
3. Mô tả bước thực hiện gây lỗi
4. Thông tin hệ thống (OS, PHP version, MySQL version)
