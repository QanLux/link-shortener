# 🔗 URL Shortener - Website rút gọn link

[![PHP Version](https://img.shields.io/badge/PHP-7.0+-blue.svg)](https://php.net)
[![MySQL Version](https://img.shields.io/badge/MySQL-5.0+-green.svg)](https://mysql.com)
[![License](https://img.shields.io/badge/License-MIT-yellow.svg)](LICENSE)

Website rút gọn link được xây dựng bằng **PHP thuần** và **MySQL**, không sử dụng framework hay API bên thứ ba. Hỗ trợ đầy đủ các tính năng cần thiết cho một URL shortener chuyên nghiệp.

## ✨ Tính năng chính

- 🔐 **Đăng ký & Đăng nhập** - Hệ thống xác thực người dùng an toàn
- 📊 **Dashboard** - Quản lý các link rút gọn của bạn
- ➕ **Tạo link rút gọn** - Tạo link mới với tiêu đề và mật khẩu bảo vệ tùy chọn
- ✏️ **Quản lý link** - Chỉnh sửa, xóa và theo dõi số lượt click
- 🔒 **Bảo mật** - Hỗ trợ mật khẩu bảo vệ cho từng link
- 📈 **Thống kê chi tiết** - Biểu đồ và báo cáo 7 ngày gần nhất
- 📝 **Theo dõi click** - Ghi log IP, User Agent, Referer
- 🎨 **Giao diện đẹp** - Thiết kế responsive, dễ sử dụng

## 🛠️ Công nghệ sử dụng

- **Backend:** PHP 7.0+
- **Database:** MySQL 5.0+ / MariaDB 5.0+
- **Frontend:** HTML5, CSS3, JavaScript (Chart.js)
- **Bảo mật:** Password hashing, CSRF protection, Prepared statements
- **Server:** Apache/Nginx

## 📁 Cấu trúc thư mục

```
url-shortener/
├── 📄 index.php              # Trang chủ, đăng ký/đăng nhập
├── 📊 dashboard.php          # Dashboard quản lý link
├── 📈 statistics.php         # Thống kê chi tiết
├── 🔗 redirect.php           # Xử lý redirect link rút gọn
├── 🚪 logout.php             # Đăng xuất
├── ⚙️ config/
│   ├── database.php          # Cấu hình database
│   └── database.example.php  # File mẫu cấu hình
├── 🔧 includes/
│   └── functions.php         # Các hàm tiện ích
├── 🎨 assets/
│   └── css/
│       └── style.css         # CSS styling
├── 🗄️ database.sql           # Cấu trúc database
├── 📋 .htaccess              # Cấu hình Apache
├── 📖 README.md              # Tài liệu này
├── 🚀 SETUP.md               # Hướng dẫn cài đặt
├── 🔧 TROUBLESHOOTING.md     # Khắc phục sự cố
└── 🚫 .gitignore             # Loại trừ file không cần thiết
```

## 🚀 Cài đặt nhanh

### 1. Clone repository
```bash
git clone https://github.com/your-username/url-shortener.git
cd url-shortener
```

### 2. Cấu hình database
```bash
# Copy file mẫu
cp config/database.example.php config/database.php

# Sửa thông tin database trong config/database.php
```

### 3. Import database
```sql
-- Tạo database mới
CREATE DATABASE url_shortener CHARACTER SET utf8 COLLATE utf8_general_ci;

-- Import cấu trúc bảng
mysql -u username -p url_shortener < database.sql
```

### 4. Cấu hình server
- Đảm bảo mod_rewrite được bật (Apache)
- Upload toàn bộ file lên thư mục web

### 5. Kiểm tra
Truy cập website và đăng ký tài khoản đầu tiên.

## 📖 Hướng dẫn sử dụng

### 🆕 Đăng ký tài khoản
1. Truy cập trang chủ
2. Chọn tab "Đăng ký"
3. Điền thông tin và tạo tài khoản

### ➕ Tạo link rút gọn
1. Đăng nhập vào dashboard
2. Nhập URL gốc và tiêu đề
3. Tùy chọn thêm mật khẩu bảo vệ
4. Click "Tạo link rút gọn"

### 📋 Quản lý link
- Xem danh sách tất cả link
- Chỉnh sửa tiêu đề và mật khẩu
- Xóa link không cần thiết
- Theo dõi số lượt click

### 📊 Xem thống kê
- Biểu đồ 7 ngày gần nhất
- Top 10 link có nhiều click
- Thống kê chi tiết theo ngày

## 🔒 Bảo mật

- **🔐 Password Hashing:** Sử dụng `password_hash()` và `password_verify()`
- **🛡️ CSRF Protection:** Token bảo vệ khỏi tấn công CSRF
- **💉 SQL Injection:** Prepared statements cho mọi query
- **❌ XSS Protection:** `htmlspecialchars()` cho output
- **🔑 Session Security:** Session management an toàn

## 🌟 Tính năng nâng cao

- **🔒 Password Protection:** Bảo vệ link bằng mật khẩu
- **📝 Click Tracking:** Ghi log chi tiết mọi lượt click
- **📊 Analytics:** Thống kê và biểu đồ trực quan
- **📱 Responsive Design:** Hoạt động tốt trên mọi thiết bị
- **🔗 Clean URLs:** URL rewriting cho link rút gọn

## 🚀 Demo

- **Demo Online:** [https://your-domain.com](https://your-domain.com)
- **Screenshots:** [Xem ảnh demo](docs/screenshots.md)

## 🤝 Đóng góp

Mọi đóng góp đều được chào đón! Hãy:

1. 🍴 Fork dự án
2. 🌿 Tạo branch mới (`git checkout -b feature/AmazingFeature`)
3. 💾 Commit thay đổi (`git commit -m 'Add some AmazingFeature'`)
4. 🚀 Push lên branch (`git push origin feature/AmazingFeature`)
5. 📝 Tạo Pull Request

### 🐛 Báo cáo lỗi

Nếu bạn tìm thấy lỗi, hãy tạo [Issue](https://github.com/QanLux/link-shortener/issues) với:
- Mô tả lỗi chi tiết
- Bước thực hiện gây lỗi
- Thông tin hệ thống (OS, PHP, MySQL version)

## 📄 Giấy phép

Dự án này được phân phối dưới giấy phép **MIT**. Xem file [LICENSE](LICENSE) để biết thêm chi tiết.

## 📞 Liên hệ & Hỗ trợ

- **👨‍💻 Tác giả:** TranAnhQuan
- **📧 Email:** tranhquan44@gmail.com
- **🐙 GitHub:** [@QanLux](https://github.com/QanLux)
- **💬 Discord:** [Join our community](https://discord.gg/your-server)

## 🙏 Cảm ơn

Cảm ơn bạn đã sử dụng **URL Shortener**! 

⭐ Nếu dự án này hữu ích, hãy cho một **star** trên GitHub!

---

**Made with ❤️ by [QanLux](https://github.com/QanLux)**



