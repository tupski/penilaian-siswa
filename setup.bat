@echo off
echo ========================================
echo Setup Aplikasi Penilaian Siswa SMART
echo ========================================
echo.

echo [1/6] Menghapus file migration lama...
del /Q database\migrations\*.php 2>nul

echo [2/6] Membuat migration baru...
php artisan make:migration 01_create_users_table
php artisan make:migration 02_create_alternatifs_table
php artisan make:migration 03_create_kriterias_table
php artisan make:migration 04_create_sub_kriterias_table
php artisan make:migration 05_create_penilaians_table

echo [3/6] Menghapus database lama...
mysql -u root -e "DROP DATABASE IF EXISTS db_penilaian_siswa"
mysql -u root -e "CREATE DATABASE db_penilaian_siswa CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"

echo [4/6] Menjalankan migration...
php artisan migrate --force

echo [5/6] Menjalankan seeder...
php artisan db:seed --force

echo [6/6] Membuat akun admin...
php artisan tinker --execute="App\Models\User::create(['name'=>'Administrator','email'=>'admin@admin.com','password'=>bcrypt('admin123'),'role'=>'admin'])"

echo.
echo ========================================
echo Setup Selesai!
echo ========================================
echo Login dengan:
echo Email: admin@admin.com
echo Password: admin123
echo ========================================
pause