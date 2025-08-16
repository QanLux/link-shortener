# 🚀 Hướng dẫn cài đặt URL Shortener

## 📋 Yêu cầu hệ thống
- PHP >= 7.0
- MySQL >= 5.0 hoặc MariaDB >= 5.0
- Web server (Apache/Nginx)

## 🔧 Cài đặt theo loại hosting

### **1. Local Development (XAMPP, WAMP, Laragon)**

#### Bước 1: Tạo database
```sql
-- Mở phpMyAdmin hoặc MySQL command line
CREATE DATABASE url_shortener CHARACTER SET utf8 COLLATE utf8_general_ci;
USE url_shortener;
```

#### Bước 2: Import cấu trúc bảng
```sql
-- Copy toàn bộ nội dung file database.sql và paste vào SQL tab
-- Hoặc sử dụng Import function
```

#### Bước 3: Cấu hình database
```php
// Sửa file config/database.php
$host = 'localhost';
$dbname = 'url_shortener';  // Tên database bạn vừa tạo
$username = 'root';
$password = '';
```

### **2. Shared Hosting (cPanel, DirectAdmin)**

#### Bước 1: Tạo database trong cPanel
1. Đăng nhập cPanel
2. Vào **MySQL Databases**
3. Tạo database mới (thường có prefix như `username_`)
4. Tạo user database và gán quyền
5. Ghi nhớ: **Database name**, **Username**, **Password**

#### Bước 2: Import cấu trúc bảng
1. Vào **phpMyAdmin**
2. Chọn database vừa tạo
3. Vào tab **SQL**
4. Copy nội dung file `database.sql` và paste vào
5. Click **Go**

#### Bước 3: Cấu hình database
```php
// Sửa file config/database.php
$host = 'localhost';  // Thường là localhost
$dbname = 'username_urlshortener';  // Tên database từ cPanel
$username = 'username_dbuser';      // Username database
$password = 'your_password';        // Password database
```

### **3. VPS/Dedicated Server**

#### Bước 1: Tạo database
```bash
mysql -u root -p
CREATE DATABASE url_shortener CHARACTER SET utf8 COLLATE utf8_general_ci;
CREATE USER 'urluser'@'localhost' IDENTIFIED BY 'strong_password';
GRANT ALL PRIVILEGES ON url_shortener.* TO 'urluser'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

#### Bước 2: Import cấu trúc
```bash
mysql -u urluser -p url_shortener < database.sql
```

#### Bước 3: Cấu hình database
```php
// Sửa file config/database.php
$host = 'localhost';
$dbname = 'url_shortener';
$username = 'urluser';
$password = 'strong_password';
```

## 🗂️ Cấu trúc thư mục

```
your-project/
├── index.php              # Trang chủ, đăng ký/đăng nhập
├── dashboard.php          # Dashboard quản lý link
├── statistics.php         # Thống kê chi tiết
├── redirect.php           # Xử lý redirect link rút gọn
├── logout.php             # Đăng xuất
├── config/
│   └── database.php       # Cấu hình database
├── includes/
│   └── functions.php      # Các hàm tiện ích
├── assets/
│   └── css/
│       └── style.css      # CSS styling
├── database.sql           # Cấu trúc database
├── test.php               # Test kết nối database
├── debug.php              # Debug thông tin
├── .htaccess              # Cấu hình Apache
├── SETUP.md               # Hướng dẫn này
├── README.md              # Tài liệu tổng quan
└── TROUBLESHOOTING.md     # Khắc phục sự cố
```

## ✅ Kiểm tra cài đặt

### **1. Test database connection**
```
http://your-domain.com/test.php
```
Kết quả mong đợi:
- ✅ Database connection successful!
- ✅ Table 'users' exists!
- ✅ Table 'urls' exists!
- ✅ Table 'click_logs' exists!

### **2. Test trang chủ**
```
http://your-domain.com/index.php
```
- Hiển thị form đăng ký/đăng nhập
- Không có lỗi database

### **3. Test tạo tài khoản**
- Đăng ký tài khoản mới
- Kiểm tra có thể đăng nhập

## 🚨 Lưu ý quan trọng

### **1. Quyền thư mục (Linux/Unix)**
```bash
chmod 755 assets/css/
chmod 644 assets/css/style.css
chmod 644 *.php
chmod 644 config/*.php
chmod 644 includes/*.php
```

### **2. Bảo mật**
- Không để file `config/database.php` có thể truy cập từ web
- Sử dụng password mạnh cho database
- Cập nhật PHP và MySQL thường xuyên

### **3. Tương thích hosting**
- **Shared hosting:** Sử dụng database có sẵn, không tạo mới
- **VPS/Dedicated:** Có thể tạo database tùy ý
- **Local:** Tùy ý tạo database

## 🔍 Khắc phục sự cố

Nếu gặp vấn đề, hãy xem file `TROUBLESHOOTING.md` hoặc chạy `debug.php` để kiểm tra.

## 📞 Hỗ trợ

Nếu vẫn gặp vấn đề:
1. Chạy `test.php` và `debug.php`
2. Kiểm tra error log của hosting
3. Cung cấp thông tin lỗi cụ thể
4. Cho biết loại hosting và phiên bản PHP/MySQL
