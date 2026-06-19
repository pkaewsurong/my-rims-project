# RIMS — Research & Innovation Management System

ระบบบริหารจัดการงานวิจัยและนวัตกรรม (RIMS) พัฒนาด้วย Pure PHP โดยใช้ MySQL (PDO) และ deploy บน Vercel ผ่าน serverless PHP runtime

## Project Structure

```
project-is/
├── api/                  # Vercel serverless entry point
│   └── index.php         #   → forwards requests to public/index.php
├── config/               # Configuration files
│   ├── database.php      #   → PDO connection (MySQL / TiDB Cloud)
│   ├── mail.php          #   → SMTP mail settings
│   ├── mail.local.php    #   → Local mail overrides (gitignored)
│   └── cacert.pem        #   → SSL certificate for remote DB connections
├── includes/             # Shared helpers
│   ├── functions.php     #   → Utility functions (auth, redirect, notifications, email)
│   └── DatabaseSessionHandler.php  #   → Custom session handler for Vercel (serverless)
├── main/                 # Application pages & templates (PHP/HTML)
│   ├── header.php        #   → Shared header / navbar
│   ├── footer.php        #   → Shared footer
│   ├── login.php         #   → Login page
│   ├── register.php      #   → Registration page
│   ├── index.php         #   → Dashboard
│   ├── projects.php      #   → Project listing
│   ├── project_detail.php#   → Project detail view
│   ├── proposals.php     #   → Proposal listing
│   ├── proposal_form.php #   → Create / edit proposal
│   ├── proposal_detail.php #  → Proposal detail view
│   ├── profile.php       #   → User profile
│   ├── admin.php         #   → Admin panel
│   ├── archives.php      #   → Archived items
│   ├── metrics.php       #   → Metrics / analytics
│   ├── css/              #   → Stylesheets
│   ├── js/               #   → JavaScript files
│   └── ajax/             #   → AJAX handlers
│       ├── admin/        #     → Admin AJAX endpoints
│       ├── archives/     #     → Archives AJAX endpoints
│       ├── dashboard/    #     → Dashboard AJAX endpoints
│       ├── metrics/      #     → Metrics AJAX endpoints
│       ├── notifications/#     → Notification AJAX endpoints
│       ├── projects/     #     → Projects AJAX endpoints
│       └── proposals/    #     → Proposals AJAX endpoints
├── public/               # Web root & router
│   ├── index.php         #   → Front controller / router
│   ├── .htaccess         #   → Apache rewrite rules
│   └── uploads/          #   → User-uploaded files
├── vendor/               # Composer dependencies
│   ├── phpmailer/        #   → PHPMailer (SMTP email)
│   └── phpoffice/        #   → PhpWord (Word document generation)
├── composer.json         # PHP dependencies
├── vercel.json           # Vercel deployment configuration
├── project_is.sql        # Database schema (MySQL)
└── .gitignore
```

## Prerequisites

- **PHP** 8.0+
- **MySQL** 5.7+ (หรือ TiDB Cloud สำหรับ production)
- **Apache** (with mod_rewrite) หรือ PHP built-in server
- **Composer** (สำหรับติดตั้ง dependencies)

## Setup & Installation

### 1. Clone the repository

```bash
git clone https://github.com/<your-username>/my-rims-project.git
cd my-rims-project
```

### 2. Install dependencies

```bash
composer install
```

### 3. Setup database

สร้าง database ชื่อ `project_is` แล้ว import schema:

```bash
mysql -u root -p project_is < project_is.sql
```

### 4. Configure environment

Database connection สามารถตั้งค่าผ่าน **environment variables** หรือใช้ค่า default (localhost):

| Variable  | Default     | Description       |
|-----------|-------------|-------------------|
| `DB_HOST` | `127.0.0.1` | Database host     |
| `DB_PORT` | `3306`      | Database port     |
| `DB_NAME` | `project_is` | Database name    |
| `DB_USER` | `root`      | Database username |
| `DB_PASS` | *(empty)*   | Database password |

สำหรับ email (password reset) ให้สร้างไฟล์ `config/mail.local.php` ตาม format ใน `config/mail.php`

### 5. Run locally

**Option A — PHP built-in server:**

```bash
php -S localhost:8080 -t public
```

**Option B — XAMPP / Apache:**

ชี้ Document Root ไปที่โฟลเดอร์ `public/` หรือเข้าถึงผ่าน `http://localhost/project-is/public/`

จากนั้นเปิด `http://localhost:8080/` ในเบราว์เซอร์

## Deployment (Vercel)

โปรเจกต์นี้รองรับ deploy บน [Vercel](https://vercel.com) ด้วย `vercel-php` runtime:

1. ตั้งค่า Environment Variables (`DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASS`) ใน Vercel Dashboard
2. Sessions จะถูกจัดเก็บใน database อัตโนมัติผ่าน `DatabaseSessionHandler` เมื่ออยู่บน Vercel
3. Deploy ด้วย:

```bash
vercel --prod
```

## Technical Notes

- **Routing:** `public/index.php` ทำหน้าที่เป็น front controller — route ทุก request ไปยังไฟล์ใน `main/` หรือ `public/`
- **Sessions:** ใช้ `$_SESSION` มาตรฐาน (local) หรือ database-backed sessions (Vercel)
- **Database:** ใช้ PDO prepared statements ทั้งหมดเพื่อป้องกัน SQL Injection
- **Authentication:** Role-based access control ผ่าน session (`hasRole()`, `requireRole()`)
- **Email:** ส่งผ่าน PHPMailer (Gmail SMTP) พร้อม fallback logging ไปที่ `public/uploads/email_logs.txt`
- **SSL:** รองรับ TLS connection สำหรับ remote database (TiDB Cloud) ด้วย bundled CA certificate
