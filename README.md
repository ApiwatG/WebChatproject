# คู่มือการใช้ GitHub กับ XAMPP

## ส่วนที่ 1: อัปโหลดโปรเจ็คจากเครื่องขึ้น GitHub

### วิธีที่ 1: ใช้ GitHub Desktop (ง่ายที่สุด)

#### ขั้นตอนที่ 1: ติดตั้ง GitHub Desktop
1. ไปที่: https://desktop.github.com/
2. ดาวน์โหลดและติดตั้ง
3. เข้าสู่ระบบด้วย GitHub account

#### ขั้นตอนที่ 2: สร้าง Repository ใหม่
1. เปิด GitHub Desktop
2. คลิก "Add an Existing Repository from your Hard Drive"
3. เลือกโฟลเดอร์โปรเจ็คของคุณ (เช่น `C:\xampp\htdocs\Webproject`)
4. กรอกชื่อ repository
5. คลิก "Publish repository"

### วิธีที่ 2: ใช้ Command Line (Git)

#### ขั้นตอนที่ 1: ติดตั้ง Git
- ดาวน์โหลดจาก: https://git-scm.com/download/win
- ติดตั้งตามขั้นตอน

#### ขั้นตอนที่ 2: สร้าง Repository บน GitHub.com
1. ไปที่ GitHub.com
2. คลิก "New repository" (ปุ่มสีเขียว)
3. ตั้งชื่อ repository
4. คลิก "Create repository"

#### ขั้นตอนที่ 3: ใช้คำสั่ง Git
เปิด Command Prompt ในโฟลเดอร์โปรเจ็ค:
```cmd
cd C:\xampp\htdocs\Webproject

# เริ่มต้น Git
git init

# เพิ่มไฟล์ทั้งหมด
git add .

# สร้าง commit แรก
git commit -m "Initial commit"

# เชื่อมต่อกับ GitHub (แทนที่ username และ repository-name)
git remote add origin https://github.com/username/repository-name.git

# อัปโหลดขึ้น GitHub
git push -u origin main
```

### วิธีที่ 3: ใช้ VS Code

1. เปิดโปรเจ็คใน VS Code
2. ไปที่ Source Control (Ctrl+Shift+G)
3. คลิก "Initialize Repository"
4. Stage ไฟล์ทั้งหมด (+)
5. เขียน commit message แล้วกด Commit
6. คลิก "Publish to GitHub"

---

## ส่วนที่ 2: โคลนโปรเจ็ค GitHub มาใช้บน XAMPP

### วิธีที่ 1: ใช้ Git Command Line

#### ขั้นตอนที่ 1: ติดตั้ง Git (ถ้ายังไม่มี)
- ดาวน์โหลด: https://git-scm.com/download/win
- ติดตั้งตามขั้นตอน

#### ขั้นตอนที่ 2: โคลนโปรเจ็ค
```cmd
cd C:\xampp\htdocs
git clone https://github.com/username/repository-name.git
```

**ตัวอย่าง:**
```cmd
git clone https://github.com/john/my-laravel-project.git
```

### วิธีที่ 2: ใช้ GitHub Desktop

1. เปิด GitHub Desktop
2. คลิก "Clone a repository from the Internet"
3. ใส่ URL ของ repository
4. เลือกโฟลเดอร์ปลายทาง: `C:\xampp\htdocs`
5. คลิก "Clone"

### วิธีที่ 3: ดาวน์โหลดไฟล์ ZIP

1. ไปที่ GitHub repository
2. คลิกปุ่ม "Code" สีเขียว
3. เลือก "Download ZIP"
4. แตกไฟล์ไปยัง `C:\xampp\htdocs`

---

## ส่วนที่ 3: ขั้นตอนสำหรับ Laravel Project

### ก่อนอัปโหลดขึ้น GitHub:

#### สร้างไฟล์ .gitignore (สำคัญ!)
ในโฟลเดอร์ root ของโปรเจ็ค สร้างไฟล์ `.gitignore` ใส่:
```
/node_modules
/public/hot
/public/storage
/storage/*.key
/vendor
.env
.env.backup
.phpunit.result.cache
Homestead.json
Homestead.yaml
npm-debug.log
yarn-error.log
```

#### เตรียมไฟล์ Environment:
```cmd
copy .env.example .env
php artisan key:generate
```

### หลังโคลน Laravel Project มาแล้ว:

#### ขั้นตอนที่ 1: เข้าไปในโฟลเดอร์โปรเจ็ค
```cmd
cd C:\xampp\htdocs\project-name
```

#### ขั้นตอนที่ 2: ติดตั้ง Dependencies
```cmd
composer install
```

#### ขั้นตอนที่ 3: สร้างไฟล์ .env
```cmd
copy .env.example .env
```

#### ขั้นตอนที่ 4: สร้าง Application Key
```cmd
php artisan key:generate
```

#### ขั้นตอนที่ 5: ตั้งค่าฐานข้อมูล
แก้ไขไฟล์ `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=root
DB_PASSWORD=
```

#### ขั้นตอนที่ 6: สร้างฐานข้อมูล
1. เปิด phpMyAdmin: http://localhost/phpmyadmin
2. สร้างฐานข้อมูลใหม่ตามชื่อที่ตั้งใน .env

#### ขั้นตอนที่ 7: รัน Migration (ถ้ามี)
```cmd
php artisan migrate
```

#### ขั้นตอนที่ 8: ติดตั้ง Node.js Dependencies (ถ้ามี)
```cmd
npm install
npm run dev
```

---

## ส่วนที่ 4: การทดสอบและใช้งาน

### เปิด XAMPP
1. เปิด XAMPP Control Panel
2. Start Apache และ MySQL

### ทดสอบเว็บไซต์

**วิธีที่ 1: ใช้ XAMPP**
```
http://localhost/project-name/public
```

**วิธีที่ 2: ใช้ Laravel Development Server**
```cmd
php artisan serve
```
แล้วเปิด: http://localhost:8000

---

## ข้อแนะนำและข้อควรระวัง

### สำคัญ:
- **อย่าลืมรัน `composer install`** หลังโคลนโปรเจ็ค Laravel
- โฟลเดอร์ `vendor` และ `node_modules` มักไม่ได้อัปโหลดขึ้น GitHub
- ตรวจสอบไฟล์ `README.md` ของโปรเจ็คเสมอ

### ข้อควรระวัง:
- ไม่ควรอัปโหลดไฟล์ `.env` ขึ้น GitHub (มีข้อมูลสำคัญ)
- ตรวจสอบ PHP version ให้เข้ากับโปรเจ็ค
- Laravel version ใหม่ต้องใช้ PHP 8.1+ ขึ้นไป

### เคล็ดลับ:
- ใช้ GitHub Desktop สำหรับผู้เริ่มต้น
- เรียนรู้ Git commands สำหรับการใช้งานขั้นสูง
- สร้าง branch แยกสำหรับ feature ใหม่
- เขียน commit message ที่ชัดเจน

---

## คำสั่งที่ใช้บ่อย

### Git Commands:
```cmd
git status                    # ดูสถานะไฟล์
git add .                    # เพิ่มไฟล์ทั้งหมด
git commit -m "message"      # สร้าง commit
git push                     # อัปโหลดขึ้น GitHub
git pull                     # ดาวน์โหลดจาก GitHub
git clone [URL]              # โคลนโปรเจ็ค
```

### Laravel Commands:
```cmd
composer install            # ติดตั้ง dependencies
php artisan key:generate     # สร้าง app key
php artisan migrate         # รัน migration
php artisan serve           # เปิด development server
php artisan cache:clear     # ล้าง cache
```

### Composer Commands:
```cmd
composer install            # ติดตั้งจาก composer.lock
composer update             # อัปเดต dependencies
composer require [package]  # ติดตั้ง package ใหม่
composer dump-autoload      # รีโหลด autoloader
```

---

*อัปเดตล่าสุด: กันยายน 2025*
