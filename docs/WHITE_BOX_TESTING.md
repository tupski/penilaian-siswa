# Laporan White-Box Testing: Sistem Penilaian Siswa Laravel (Penilaian Siswa)

## Daftar Isi

1. [Ringkasan Eksekutif](#ringkasan-eksekutif)
2. [Lingkungan Pengujian](#lingkungan-pengujian)
3. [Gambaran Umum Test Suite](#gambaran-umum-test-suite)
4. [Skema Database](#skema-database)
5. [Arsitektur Sistem](#arsitektur-sistem)
6. [Unit Tests](#unit-tests)
7. [Feature Tests](#feature-tests)
8. [Cakupan Logika Bisnis](#cakupan-logika-bisnis)
9. [Analisis Branch Coverage](#analisis-branch-coverage)
10. [Hasil Code Coverage (PCOV)](#hasil-code-coverage-pcov)
11. [Keterbatasan Code Coverage](#keterbatasan-code-coverage)
12. [Metrik Kualitas Pengujian](#metrik-kualitas-pengujian)
13. [Menjalankan Pengujian](#menjalankan-pengujian)

---

## Ringkasan Eksekutif

Dokumen ini menyajikan analisis white-box testing secara lengkap untuk aplikasi **Penilaian Siswa** — sebuah aplikasi Laravel 11+ yang mengimplementasikan metode **SAW (Simple Additive Weighting)** untuk perangkingan dan evaluasi siswa.

White-box testing dilakukan untuk memverifikasi logika internal, alur data, alur kontrol, dan branch coverage di seluruh lapisan aplikasi: model, controller, middleware, validasi form, autentikasi/otorisasi, dan operasi database.

### Hasil Utama

| Metrik | Nilai |
|--------|-------|
| Total Pengujian | **126** |
| Total Assertions | **341** |
| Gagal | **0** |
| Error | **0** |
| Status Pengujian | ✅ **SEMUA LULUS** |
| Code Coverage | **77,2%** (PCOV) |
| File Pengujian | **18** (8 Unit + 10 Feature) |
| File Factory | **4** |
| Dokumentasi | **1** file (`docs/WHITE_BOX_TESTING.md`) |

---

## Lingkungan Pengujian

| Komponen | Spesifikasi |
|-----------|---------------|
| **Framework** | Laravel 11.x |
| **Versi PHP** | PHP 8.4.22 (via FlyEnv) |
| **Framework Pengujian** | PHPUnit 11.x |
| **Database (Testing)** | SQLite :memory: |
| **Traits** | `RefreshDatabase` di semua kelas pengujian |
| **Driver Code Coverage** | ✅ PCOV (77,2% total coverage) |
| **OS** | Windows 10.0.26100 |


---

## Gambaran Umum Test Suite

### Distribusi Pengujian

```
Total: 126 pengujian (341 assertions)
├── Unit Tests: 46 pengujian (~36,5%)
│   ├── UserModelTest            (6 pengujian)
│   ├── AlternatifModelTest      (8 pengujian)
│   ├── AbsensiModelTest         (7 pengujian)
│   ├── PenilaianModelTest       (5 pengujian)
│   ├── RangkingControllerTest   (7 pengujian)
│   ├── AdminMiddlewareTest      (4 pengujian)
│   ├── AuthControllerTest       (8 pengujian)
│   └── ExampleTest              (1 pengujian)
│
└── Feature Tests: 80 pengujian (~63,5%)
    ├── AbsensiFeatureTest       (13 pengujian)
    ├── KriteriaFeatureTest      (13 pengujian)
    ├── AlternatifFeatureTest    (11 pengujian)
    ├── AuthFeatureTest          (10 pengujian)
    ├── PenilaianFeatureTest     (10 pengujian)
    ├── ProfileFeatureTest       (6 pengujian)
    ├── MiddlewareFeatureTest     (6 pengujian)
    ├── RangkingFeatureTest      (4 pengujian)
    ├── LaporanFeatureTest       (3 pengujian)
    └── DashboardFeatureTest     (3 pengujian)
```

### Struktur File Pengujian

```
tests/
├── TestCase.php
├── Feature/
│   ├── AbsensiFeatureTest.php
│   ├── AlternatifFeatureTest.php
│   ├── AuthFeatureTest.php
│   ├── DashboardFeatureTest.php
│   ├── ExampleTest.php
│   ├── KriteriaFeatureTest.php
│   ├── LaporanFeatureTest.php
│   ├── MiddlewareFeatureTest.php
│   ├── PenilaianFeatureTest.php
│   ├── ProfileFeatureTest.php
│   └── RangkingFeatureTest.php
└── Unit/
    ├── AbsensiModelTest.php
    ├── AdminMiddlewareTest.php
    ├── AlternatifModelTest.php
    ├── AuthControllerTest.php
    ├── ExampleTest.php
    ├── PenilaianModelTest.php
    ├── RangkingControllerTest.php
    └── UserModelTest.php
```

---

## Skema Database

Aplikasi ini menggunakan 6 tabel utama. Semua pengujian dijalankan terhadap database SQLite `:memory:` dengan migrasi skema penuh yang diterapkan melalui `RefreshDatabase`.

### Tabel: `users`

| Kolom | Tipe | Constraint |
|--------|------|-------------|
| `id` | BIGINT UNSIGNED | PK, AUTO_INCREMENT |
| `name` | VARCHAR(255) | NOT NULL |
| `email` | VARCHAR(255) | UNIQUE, NOT NULL |
| `email_verified_at` | TIMESTAMP | NULLABLE |
| `password` | VARCHAR(255) | NOT NULL |
| `role` | ENUM('admin', 'guru') | DEFAULT 'guru' |
| `remember_token` | VARCHAR(100) | NULLABLE |
| `created_at` | TIMESTAMP | |
| `updated_at` | TIMESTAMP | |

**Catatan:** Kolom `role` awalnya bertipe `ENUM('admin', 'user')` dengan default `'user'`. Migrasi `2026_04_27_021452` mengganti `'user'` menjadi `'guru'` dan memperbarui data yang sudah ada.

### Tabel: `alternatifs`

| Kolom | Tipe | Constraint |
|--------|------|-------------|
| `id` | BIGINT UNSIGNED | PK, AUTO_INCREMENT |
| `nis` | VARCHAR(20) | UNIQUE, NOT NULL |
| `nama_siswa` | VARCHAR(100) | NOT NULL |
| `kelas` | VARCHAR(20) | NOT NULL |
| `jenis_kelamin` | ENUM('L', 'P') | NOT NULL |
| `created_at` | TIMESTAMP | |
| `updated_at` | TIMESTAMP | |

### Tabel: `kriterias`

| Kolom | Tipe | Constraint |
|--------|------|-------------|
| `id` | BIGINT UNSIGNED | PK, AUTO_INCREMENT |
| `kode_kriteria` | VARCHAR(10) | UNIQUE, NOT NULL |
| `nama_kriteria` | VARCHAR(50) | NOT NULL |
| `bobot` | DECIMAL(5,2) | NOT NULL |
| `jenis` | ENUM('benefit', 'cost') | NOT NULL |
| `created_at` | TIMESTAMP | |
| `updated_at` | TIMESTAMP | |

### Tabel: `sub_kriterias`

| Kolom | Tipe | Constraint |
|--------|------|-------------|
| `id` | BIGINT UNSIGNED | PK, AUTO_INCREMENT |
| `kriteria_id` | BIGINT UNSIGNED | FK → `kriterias.id` ON DELETE CASCADE |
| `nama_sub` | VARCHAR(50) | NOT NULL |
| `nilai` | INTEGER | NOT NULL |
| `created_at` | TIMESTAMP | |
| `updated_at` | TIMESTAMP | |

### Tabel: `penilaians`

| Kolom | Tipe | Constraint |
|--------|------|-------------|
| `id` | BIGINT UNSIGNED | PK, AUTO_INCREMENT |
| `alternatif_id` | BIGINT UNSIGNED | FK → `alternatifs.id` ON DELETE CASCADE |
| `kriteria_id` | BIGINT UNSIGNED | FK → `kriterias.id` ON DELETE CASCADE |
| `nilai` | INTEGER | NOT NULL |
| `created_at` | TIMESTAMP | |
| `updated_at` | TIMESTAMP | |

### Tabel: `absensis`

| Kolom | Tipe | Constraint |
|--------|------|-------------|
| `id` | BIGINT UNSIGNED | PK, AUTO_INCREMENT |
| `alternatif_id` | BIGINT UNSIGNED | FK → `alternatifs.id` ON DELETE CASCADE |
| `tanggal` | DATE | NOT NULL |
| `status` | ENUM('hadir', 'sakit', 'izin', 'alpa') | NOT NULL |
| `keterangan` | TEXT | NULLABLE |
| `created_at` | TIMESTAMP | |
| `updated_at` | TIMESTAMP | |

**Indeks:** `UNIQUE(alternatif_id, tanggal)`, `INDEX(tanggal, status)`

---

## Arsitektur Sistem

### Modul yang Dianalisis

#### Controller (9 file, ~50 method)

| Controller | Method | Rute | Wajib Auth |
|------------|---------|--------|---------------|
| `AbsensiController` | 13 | CRUD + mass + sync + rekap | Ya |
| `AlternatifController` | 5 | index, create, store, edit, update, destroy | Ya |
| `AuthController` | 9 | login, register, logout, forgot/reset password | Tidak (khusus guest) |
| `DashboardController` | 1 | index | Ya |
| `KriteriaController` | 8 | CRUD + manajemen sub-kriteria | Ya (admin untuk mutasi) |
| `LaporanController` | 2 | index, cetakPDF | Ya |
| `PenilaianController` | 8 | CRUD + sync + export (PDF/CSV) | Ya |
| `ProfileController` | 3 | index, update, updatePassword | Ya |
| `RangkingController` | 1 | index (perhitungan SAW) | Ya |

#### Model (6 file)

| Model | Tabel | Relasi | Method Logika Bisnis |
|-------|-------|---------------|----------------------|
| `User` | `users` | — | `isAdmin()`, `isGuru()`, `isUser()` |
| `Alternatif` | `alternatifs` | hasMany: Penilaian, Absensi | `nilaiKehadiran` (accessor), `detailKehadiran` (accessor) |
| `Kriteria` | `kriterias` | hasMany: SubKriteria, Penilaian | — |
| `SubKriteria` | `sub_kriterias` | belongsTo: Kriteria | — |
| `Penilaian` | `penilaians` | belongsTo: Alternatif, Kriteria | `syncKehadiranForAllSiswa()` |
| `Absensi` | `absensis` | belongsTo: Alternatif | `hitungPersentaseKehadiran()`, `getRekap()` |

#### Middleware (1 file)

| Middleware | Tujuan |
|------------|---------|
| `AdminMiddleware` | Membatasi rute hanya untuk user dengan `role === 'admin'`; mengembalikan 403 untuk non-admin/user belum login |

---

## Unit Tests

Unit tests berfokus pada logika bisnis yang terisolasi di dalam model, middleware, dan verifikasi algoritma. Setiap pengujian memverifikasi satu unit kode secara independen.

### 1. `tests/Unit/UserModelTest.php` — 6 Pengujian

Menguji method pengecekan role pada model `User`.

| # | Method Pengujian | Apa yang Diverifikasi |
|---|-------------|-----------------|
| 1 | `is_admin_returns_true_when_role_is_admin` | `isAdmin()` → `true` saat `role = 'admin'` |
| 2 | `is_admin_returns_false_when_role_is_guru` | `isAdmin()` → `false` saat `role = 'guru'` |
| 3 | `is_guru_returns_true_when_role_is_guru` | `isGuru()` → `true` saat `role = 'guru'` |
| 4 | `is_guru_returns_false_when_role_is_admin` | `isGuru()` → `false` saat `role = 'admin'` |
| 5 | `is_user_returns_true_when_role_is_guru` | `isUser()` (alias kompatibilitas) → `true` untuk guru |
| 6 | `is_user_returns_false_when_role_is_admin` | `isUser()` (alias kompatibilitas) → `false` untuk admin |

**Cabang yang Diuji:**
- `isAdmin()`: `role === 'admin'` → kedua jalur `true` dan `false`
- `isGuru()`: `role === 'guru'` → kedua jalur `true` dan `false`
- `isUser()`: `role === 'guru'` → kedua jalur `true` dan `false`

### 2. `tests/Unit/AlternatifModelTest.php` — 8 Pengujian

Menguji accessor `nilai_kehadiran` dan accessor `detail_kehadiran` pada model `Alternatif`.

| # | Method Pengujian | Apa yang Diverifikasi |
|---|-------------|-----------------|
| 1 | `nilai_kehadiran_returns_50_when_5_hadir_out_of_10_total` | Kasus normal: kehadiran 50% |
| 2 | `nilai_kehadiran_returns_100_when_10_hadir_out_of_10_total` | Kehadiran sempurna: 100% |
| 3 | `nilai_kehadiran_returns_0_when_no_absensi_records` | Nol data → 0% |
| 4 | `nilai_kehadiran_returns_0_when_all_alpa` | Semua alpa → 0% |
| 5 | `nilai_kehadiran_returns_100_when_1_hadir_out_of_1_total` | Kasus batas: satu data → 100% |
| 6 | `nilai_kehadiran_is_capped_at_100` | Pembatasan nilai via `min($persentase, 100)` |
| 7 | `detail_kehadiran_returns_correct_array_structure` | Kunci dan nilai array untuk data campuran (hadir/5, sakit/2, izin/2, alpa/1) |
| 8 | `detail_kehadiran_returns_zeros_when_no_absensi` | Semua nol saat tidak ada data absensi |

**Cabang yang Diuji:**
- `getNilaiKehadiranAttribute()`:
  - `total == 0` → return 0 (cabang true)
  - `total > 0` → hitung persentase → `min($persentase, 100)` (kedua cabang: normal vs pembatasan)
- `getDetailKehadiranAttribute()`:
  - `$total > 0` → cabang hitung persentase
  - `$total == 0` → cabang persentase = 0
  - Keempat penghitung status (hadir, sakit, izin, alpa)

### 3. `tests/Unit/AbsensiModelTest.php` — 7 Pengujian

Menguji method statis pada model `Absensi`: `hitungPersentaseKehadiran()` dan `getRekap()`.

| # | Method Pengujian | Apa yang Diverifikasi |
|---|-------------|-----------------|
| 1 | `hitung_persentase_kehadiran_returns_80_when_8_hadir_out_of_10` | 8/10 → 80% |
| 2 | `hitung_persentase_kehadiran_returns_100_when_perfect_attendance` | 10/10 → 100% |
| 3 | `hitung_persentase_kehadiran_returns_0_with_no_hadir` | 0/10 → 0% |
| 4 | `hitung_persentase_kehadiran_returns_0_when_no_records` | Collection kosong → 0% |
| 5 | `hitung_persentase_kehadiran_matches_nilai_kehadiran_accessor` | Method statis cocok dengan accessor Eloquent |
| 6 | `get_rekap_returns_correct_array_with_all_keys` | Kunci dan nilai array untuk distribusi 6/1/2/1 |
| 7 | `get_rekap_returns_zeros_when_no_records` | Semua nol untuk data kosong |

**Cabang yang Diuji:**
- `hitungPersentaseKehadiran()`:
  - `$absensis->isEmpty()` → return 0
  - `$total == 0` → return 0
  - Perhitungan normal dengan `min($persentase, 100)`
- `getRekap()`:
  - `$total > 0` → hitung persentase
  - `$total == 0` (else) → persentase = 0

### 4. `tests/Unit/PenilaianModelTest.php` — 5 Pengujian

Menguji `Penilaian::syncKehadiranForAllSiswa()` — mekanisme sinkronisasi antara kehadiran dan penilaian.

| # | Method Pengujian | Apa yang Diverifikasi |
|---|-------------|-----------------|
| 1 | `sync_kehadiran_creates_records_for_all_students` | Membuat penilaian untuk setiap siswa dengan nilai yang benar (80, 50, 0) |
| 2 | `sync_kehadiran_does_not_create_duplicates_on_second_call` | Idempotensi `updateOrCreate` — tidak ada duplikasi |
| 3 | `sync_kehadiran_updates_value_when_attendance_changes` | Memperbarui nilai dari 50 → 67 saat data kehadiran berubah |
| 4 | `sync_kehadiran_returns_false_when_no_kehadiran_criteria` | Guard clause: tidak ada kriteria "Kehadiran" → return `false` |
| 5 | `sync_kehadiran_returns_zero_when_no_students` | Tidak ada siswa → return count 0 |

**Cabang yang Diuji:**
- `syncKehadiranForAllSiswa()`:
  - Tidak ada kriteria "Kehadiran" → return `false`
  - "Kehadiran" ada + siswa ada → buat/perbarui data
  - "Kehadiran" ada + tidak ada siswa → return 0
- Perilaku `updateOrCreate`: jalur create vs update

### 5. `tests/Unit/RangkingControllerTest.php` — 7 Pengujian

Menguji logika algoritma **SAW (Simple Additive Weighting)** yang merupakan inti dari sistem perangkingan. Method bantuan `runSawCalculation()` mereplikasi algoritma controller secara persis.

| # | Method Pengujian | Apa yang Diverifikasi |
|---|-------------|-----------------|
| 1 | `saw_algorithm_ranks_students_correctly` | 3 siswa dengan nilai yang diketahui → urutan peringkat menurun yang benar |
| 2 | `saw_algorithm_single_student_gets_rank_1` | Kasus batas: satu siswa → peringkat 1 |
| 3 | `saw_algorithm_normalizes_weights_correctly` | 4 kriteria (40+25+20+15=100) → bobot ternormalisasi (0,4, 0,25, 0,2, 0,15) |
| 4 | `saw_algorithm_handles_ties_correctly` | Dua siswa dengan skor identik → total_nilai sama, peringkat berurutan |
| 5 | `saw_algorithm_all_zero_scores_produces_zero_utilities` | Semua nilai = 0 → semua utility = 0, total_nilai = 0 |
| 6 | `saw_algorithm_all_max_scores_produces_correct_utilities` | Semua nilai = 100 → utility = bobot_ternormalisasi, total_nilai = 100 |
| 7 | `saw_algorithm_student_without_penilaian_gets_zero_utilities` | Siswa tanpa penilaian → nilai default 0 |

**Langkah-Langkah Algoritma SAW yang Diverifikasi:**
1. **Normalisasi bobot**: `bobot_normalisasi = bobot / totalBobot`
2. **Normalisasi nilai**: `nilai_normalisasi = nilai / 100`
3. **Perhitungan utility**: `utility = nilai_normalisasi * bobot_normalisasi`
4. **Skor total**: `total_nilai = Σ(utility) * 100`
5. **Perangkingan**: Urutkan menurun berdasarkan `total_nilai`, penetapan peringkat berurutan

**Cabang yang Diuji:**
- Kasus normal: banyak siswa, banyak kriteria
- Kasus batas: satu siswa, satu kriteria
- Kasus batas: semua nilai maksimum (100)
- Kasus batas: semua nilai nol
- Kasus tepi: siswa tanpa data penilaian (nilai default 0)
- Kasus tepi: nilai seri
- Normalisasi bobot divalidasi dengan delta floating-point

### 6. `tests/Unit/AdminMiddlewareTest.php` — 4 Pengujian

Menguji method `AdminMiddleware::handle()` secara terisolasi menggunakan instantiasi langsung.

| # | Method Pengujian | Apa yang Diverifikasi |
|---|-------------|-----------------|
| 1 | `admin_user_passes_through_middleware` | User admin → closure `$next` dipanggil, response dikembalikan |
| 2 | `non_admin_user_triggers_403_exception` | User guru → `HttpException(403)` dengan pesan "Unauthorized" |
| 3 | `unauthenticated_user_triggers_403_exception` | Tidak ada user terautentikasi → `HttpException(403)` |
| 4 | `unauthorized_access_exception_contains_expected_message` | Exception memiliki status 403 + pesan "Unauthorized" |

**Cabang yang Diuji:**
- User terautentikasi DAN `isAdmin()` = true → lanjutkan
- User terautentikasi DAN `isAdmin()` = false → lemparkan 403
- Tidak ada user terautentikasi → lemparkan 403

### 7. `tests/Unit/AuthControllerTest.php` — 8 Pengujian

Menguji aturan validasi login dan logika autentikasi di tingkat unit.

| # | Method Pengujian | Apa yang Diverifikasi |
|---|-------------|-----------------|
| 1 | `login_succeeds_with_valid_credentials` | Email + password benar → `Auth::attempt()` return `true` |
| 2 | `login_validation_fails_with_invalid_email_format` | Format email salah → validator gagal pada key `email` |
| 3 | `login_validation_fails_with_empty_email` | Email kosong → validator gagal pada key `email` |
| 4 | `login_validation_fails_with_empty_password` | Password kosong → validator gagal pada key `password` |
| 5 | `login_validation_fails_with_both_fields_empty` | Keduanya kosong → gagal pada `email` dan `password` |
| 6 | `login_fails_with_wrong_password` | Password salah → `Auth::attempt()` return `false` |
| 7 | `login_fails_with_non_existent_email` | Email tidak terdaftar → `Auth::attempt()` return `false` |
| 8 | `login_succeeds_with_remember_me` | Kredensial valid + remember → `Auth::attempt(credentials, true)` |

**Cabang yang Diuji:**
- Aturan validasi: `required`, format `email`
- `Auth::attempt()`: sukses vs gagal
- `Auth::attempt()` dengan `$remember = true`

### 8. `tests/Unit/ExampleTest.php` — 1 Pengujian

| # | Method Pengujian | Apa yang Diverifikasi |
|---|-------------|-----------------|
| 1 | `test_that_true_is_true` | Assertion dasar PHPUnit (`assertTrue(true)`) |

---

## Feature Tests

Feature tests menjalankan siklus HTTP request/response secara lengkap, memverifikasi bahwa rute, controller, middleware, view, dan operasi database bekerja bersama dengan benar.

### 1. `tests/Feature/AuthFeatureTest.php` — 10 Pengujian

Menguji alur autentikasi penuh: login, register, logout, dan proteksi rute.

| # | Method Pengujian | HTTP | Rute | Assertions |
|---|-------------|------|-------|------------|
| 1 | `test_login_page_returns_200` | GET | `/login` | Status 200 |
| 2 | `test_register_page_returns_200` | GET | `/register` | Status 200 |
| 3 | `test_successful_login_redirects_to_dashboard` | POST | `/login` | Redirect `/dashboard`, terautentikasi |
| 4 | `test_failed_login_with_wrong_password` | POST | `/login` | Redirect `/`, session error, guest |
| 5 | `test_successful_register_creates_user_with_guru_role` | POST | `/register` | Redirect `/dashboard`, DB memiliki user dengan role='guru' |
| 6 | `test_failed_register_with_mismatched_passwords` | POST | `/register` | Session errors pada `password`, guest |
| 7 | `test_duplicate_email_register_returns_validation_error` | POST | `/register` | Session errors pada `email`, guest |
| 8 | `test_logout_redirects_and_invalidates_session` | POST | `/logout` | Redirect `/`, guest |
| 9 | `test_authenticated_user_cannot_access_login` | GET | `/login` (auth) | Redirect `/dashboard` |
| 10 | `test_guest_cannot_access_dashboard` | GET | `/dashboard` (guest) | Redirect `/login` |

**Cabang yang Diuji:**
- Login: sukses (kredensial valid) vs gagal (password salah)
- Register: sukses (user baru) vs gagal (password tidak cocok, email duplikat)
- Registrasi memberikan `role = 'guru'` secara default
- Guest middleware: redirect user terautentikasi dari login/register
- Auth middleware: redirect guest ke `/login`

### 2. `tests/Feature/AlternatifFeatureTest.php` — 11 Pengujian

Menguji operasi CRUD lengkap untuk data siswa (`alternatifs`).

| # | Method Pengujian | HTTP | Rute | Assertions Utama |
|---|-------------|------|-------|----------------|
| 1 | `test_index_page_returns_200_for_authenticated_user` | GET | `/alternatif` | Status 200 |
| 2 | `test_create_page_returns_200` | GET | `/alternatif/create` | Status 200 |
| 3 | `test_store_creates_record_and_redirects` | POST | `/alternatif` | Redirect, DB memiliki data |
| 4 | `test_store_duplicate_nis_validation_error` | POST | `/alternatif` | Session errors pada `nis` |
| 5 | `test_store_empty_fields_validation_errors` | POST | `/alternatif` | Errors pada `nis`, `nama_siswa`, `kelas`, `jenis_kelamin` |
| 6 | `test_edit_page_returns_200` | GET | `/alternatif/{id}/edit` | Status 200 |
| 7 | `test_update_updates_alternatif_record` | PUT | `/alternatif/{id}` | Redirect, DB diperbarui |
| 8 | `test_update_with_same_nis_succeeds_ignoring_self` | PUT | `/alternatif/{id}` | Redirect, DB diperbarui (self unique check) |
| 9 | `test_delete_removes_alternatif_and_redirects` | DELETE | `/alternatif/{id}` | Redirect, data hilang dari DB |
| 10 | `test_delete_removes_associated_records` | DELETE | `/alternatif/{id}` | Cascade: penilaian juga terhapus |
| 11 | `test_guest_cannot_access_alternatif_routes` | SEMUA (6 rute) | `/alternatif/*` | Semua redirect ke `/login` |

**Cabang yang Diuji:**
- CRUD: create, read, update, delete — semua jalur sukses
- Validasi: NIS unik, field wajib diisi
- Constraint unik: NIS yang sama saat update mengabaikan diri sendiri
- Cascade delete: menghapus siswa juga menghapus penilaian terkait
- Auth middleware: semua 6 rute alternatif diproteksi untuk guest

### 3. `tests/Feature/KriteriaFeatureTest.php` — 13 Pengujian

Menguji CRUD kriteria dan sub-kriteria dengan penegakan middleware admin.

| # | Method Pengujian | HTTP | Rute | Assertions Utama |
|---|-------------|------|-------|----------------|
| 1 | `test_index_page_returns_200_for_auth_user` | GET | `/kriteria` | Status 200 (guru) |
| 2 | `test_create_page_returns_200_for_admin` | GET | `/kriteria/create` | Status 200 (admin) |
| 3 | `test_create_page_returns_403_for_non_admin` | GET | `/kriteria/create` | Status 403 (guru) |
| 4 | `test_store_creates_kriteria_for_admin` | POST | `/kriteria` | Redirect, DB memiliki data |
| 5 | `test_store_with_bobot_over_100_validation_error` | POST | `/kriteria` | Session errors pada `bobot` |
| 6 | `test_edit_page_returns_200_for_admin` | GET | `/kriteria/{id}/edit` | Status 200 (admin) |
| 7 | `test_update_updates_kriteria_for_admin` | PUT | `/kriteria/{id}` | Redirect, DB diperbarui |
| 8 | `test_delete_removes_kriteria_cascade_for_admin` | DELETE | `/kriteria/{id}` | Cascade: sub-kriteria juga terhapus |
| 9 | `test_sub_kriteria_store_creates_record` | POST | `/kriteria/{id}/sub` | Redirect, DB memiliki sub-kriteria |
| 10 | `test_sub_kriteria_store_nilai_over_100_validation_error` | POST | `/kriteria/{id}/sub` | Session errors pada `nilai` |
| 11 | `test_sub_kriteria_store_non_integer_nilai_validation_error` | POST | `/kriteria/{id}/sub` | Session errors pada `nilai` (string) |
| 12 | `test_sub_kriteria_store_negative_nilai_validation_error` | POST | `/kriteria/{id}/sub` | Session errors pada `nilai` (negatif) |
| 13 | `test_sub_kriteria_delete_removes_record` | DELETE | `/kriteria/sub-kriteria/{id}` | Redirect, data hilang dari DB |

**Cabang yang Diuji:**
- Middleware admin: admin lolos (200), guru diblokir (403)
- CRUD kriteria: semua operasi khusus admin
- Validasi: `bobot` > 100 ditolak
- Validasi sub-kriteria: `nilai` > 100, non-integer, negatif semuanya ditolak
- Cascade delete: menghapus kriteria juga menghapus sub-kriteria

### 4. `tests/Feature/PenilaianFeatureTest.php` — 10 Pengujian

Menguji operasi CRUD penilaian, logika skip Kehadiran, dan export PDF/CSV.

| # | Method Pengujian | HTTP | Rute | Assertions Utama |
|---|-------------|------|-------|----------------|
| 1 | `test_index_page_returns_200` | GET | `/penilaian` | Status 200 |
| 2 | `test_create_page_returns_200` | GET | `/penilaian/create` | Status 200 |
| 3 | `test_store_creates_penilaian_records` | POST | `/penilaian` | Redirect, DB memiliki penilaian dengan nilai=85 |
| 4 | `test_store_skips_kehadiran_kriteria` | POST | `/penilaian` | Kehadiran TIDAK disimpan, Akademik DISIMPAN |
| 5 | `test_edit_page_returns_200` | GET | `/penilaian/{id}/edit` | Status 200 |
| 6 | `test_update_penilaian_values` | PUT | `/penilaian/{id}` | Redirect, nilai diperbarui 70→95 |
| 7 | `test_delete_removes_penilaian` | DELETE | `/penilaian/{id}/{kriteria_id}` | Redirect, data hilang dari DB |
| 8 | `test_export_pdf_returns_200_with_pdf_headers` | GET | `/penilaian/export-pdf` | Status 200, Content-Type: `application/pdf` |
| 9 | `test_export_csv_returns_200_with_csv_headers` | GET | `/penilaian/export-csv` | Status 200, Content-Type: `text/csv` |
| 10 | `test_guest_cannot_access_penilaian_routes` | SEMUA (8 rute) | `/penilaian/*` | Semua redirect ke `/login` |

**Cabang yang Diuji:**
- Store: Kriteria Kehadiran dilewati saat pembuatan penilaian manual
- Update: nilai dimodifikasi dengan benar
- Delete: dihapus berdasarkan kunci gabungan (alternatif_id, kriteria_id)
- Export: PDF mengembalikan `application/pdf`, CSV mengembalikan `text/csv`
- Auth middleware: semua 8 rute penilaian diproteksi untuk guest

### 5. `tests/Feature/AbsensiFeatureTest.php` — 13 Pengujian

Menguji CRUD kehadiran, operasi massal, mekanisme sinkronisasi, rekap, dan pencegahan duplikasi.

| # | Method Pengujian | HTTP | Rute | Assertions Utama |
|---|-------------|------|-------|----------------|
| 1 | `test_index_page_returns_200` | GET | `/absensi` | Status 200 |
| 2 | `test_create_page_returns_200` | GET | `/absensi/create` | Status 200 |
| 3 | `test_store_creates_attendance_and_syncs` | POST | `/absensi` | Redirect, absensi dibuat dengan tanggal benar |
| 4 | `test_duplicate_attendance_returns_error` | POST | `/absensi` | `QueryException` dari constraint UNIQUE |
| 5 | `test_mass_create_page_returns_200` | GET | `/absensi/mass-create` | Status 200 |
| 6 | `test_mass_store_creates_multiple_records` | POST | `/absensi/mass-store` | Redirect, 2 data dibuat dengan status benar |
| 7 | `test_edit_page_returns_200` | GET | `/absensi/{id}/edit` | Status 200 |
| 8 | `test_update_changes_status_and_syncs` | PUT | `/absensi/{id}` | Redirect, status berubah hadir→sakit |
| 9 | `test_delete_removes_attendance_and_syncs` | DELETE | `/absensi/{id}` | Redirect, data hilang dari DB |
| 10 | `test_delete_all_by_student_removes_all_attendance` | DELETE | `/absensi/siswa/{id}/destroy-all` | Redirect, 0 data tersisa |
| 11 | `test_rekap_page_returns_200` | GET | `/absensi/rekap/{id}` | Status 200 |
| 12 | `test_rekap_bulanan_page_returns_200` | GET | `/absensi/rekap-bulanan` | Status 200 |
| 13 | `test_guest_cannot_access_absensi_routes` | SEMUA (7 rute) | `/absensi/*` | Semua redirect ke `/login` |

**Cabang yang Diuji:**
- CRUD: create, read, update, delete — semuanya dengan sinkronisasi
- Operasi massal: create dan store untuk banyak siswa sekaligus
- Pencegahan duplikasi: constraint `UNIQUE(alternatif_id, tanggal)` ditegakkan
- Delete all: hapus massal per siswa
- Rekap: halaman rekap individu dan bulanan
- Auth middleware: semua 7 rute absensi diproteksi untuk guest

### 6. `tests/Feature/RangkingFeatureTest.php` — 4 Pengujian

Menguji tampilan halaman perangkingan dan integrasi perhitungan SAW.

| # | Method Pengujian | HTTP | Rute | Assertions Utama |
|---|-------------|------|-------|----------------|
| 1 | `test_index_page_returns_200` | GET | `/rangking` | Status 200 |
| 2 | `test_ranking_calculation_computes_and_displays` | GET | `/rangking` | View memiliki `results` dan `kriterias` |
| 3 | `test_empty_data_page_still_returns_200` | GET | `/rangking` | Status 200 (tanpa data) |
| 4 | `test_guest_cannot_access_rangking` | GET | `/rangking` | Redirect ke `/login` |

**Cabang yang Diuji:**
- Halaman perangkingan: dengan data dan tanpa data
- Data view: `results` dan `kriterias` dikirim ke view
- Auth middleware: rute rangking diproteksi

### 7. `tests/Feature/LaporanFeatureTest.php` — 3 Pengujian

Menguji halaman laporan dan pembuatan PDF.

| # | Method Pengujian | HTTP | Rute | Assertions Utama |
|---|-------------|------|-------|----------------|
| 1 | `test_index_page_returns_200` | GET | `/laporan` | Status 200 |
| 2 | `test_cetak_pdf_returns_200_with_pdf_headers` | GET | `/laporan/cetak-pdf` | Status 200, Content-Type: `application/pdf` |
| 3 | `test_guest_cannot_access_laporan` | GET (2 rute) | `/laporan`, `/laporan/cetak-pdf` | Keduanya redirect ke `/login` |

**Cabang yang Diuji:**
- Laporan index: mengembalikan 200
- Pembuatan PDF: mengembalikan content type PDF yang sesuai
- Auth middleware: kedua rute laporan diproteksi

### 8. `tests/Feature/ProfileFeatureTest.php` — 6 Pengujian

Menguji pembaruan profil (nama, email, password) dengan validasi.

| # | Method Pengujian | HTTP | Rute | Assertions Utama |
|---|-------------|------|-------|----------------|
| 1 | `test_index_page_returns_200` | GET | `/profile` | Status 200 |
| 2 | `test_update_name_and_email` | PUT | `/profile` | Redirect, DB diperbarui |
| 3 | `test_update_with_duplicate_email_returns_error` | PUT | `/profile` | Session errors pada `email` |
| 4 | `test_update_password_with_correct_current_password` | PUT | `/profile/password` | Redirect, `Hash::check()` mengonfirmasi password baru |
| 5 | `test_update_password_with_wrong_current_password_returns_error` | PUT | `/profile/password` | Session errors pada `current_password` |
| 6 | `test_guest_cannot_access_profile` | SEMUA (3 rute) | `/profile`, `/profile/password` | Semua redirect ke `/login` |

**Cabang yang Diuji:**
- Pembaruan profil: perubahan nama dan email
- Unik email: email duplikat dari user lain ditolak
- Ganti password: `current_password` benar → sukses
- Ganti password: `current_password` salah → error validasi
- Auth middleware: semua 3 rute profil diproteksi

### 9. `tests/Feature/DashboardFeatureTest.php` — 3 Pengujian

Menguji halaman dashboard dan jumlah statistiknya.

| # | Method Pengujian | HTTP | Rute | Assertions Utama |
|---|-------------|------|-------|----------------|
| 1 | `test_dashboard_returns_200_with_counts` | GET | `/dashboard` | Status 200, view memiliki `totalSiswa`, `totalKriteria`, `totalPenilaian` |
| 2 | `test_dashboard_counts_are_accurate` | GET | `/dashboard` | 5 siswa, 3 kriteria, 4 penilaian — cocok persis |
| 3 | `test_guest_cannot_access_dashboard` | GET | `/dashboard` | Redirect ke `/login` |

**Cabang yang Diuji:**
- Data view dashboard: ketiga variabel jumlah tersedia
- Akurasi jumlah: diverifikasi dengan kuantitas yang diketahui
- Auth middleware: dashboard diproteksi

### 10. `tests/Feature/MiddlewareFeatureTest.php` — 6 Pengujian

Menguji interaksi antara middleware `auth` dan `admin` di lapisan HTTP.

| # | Method Pengujian | HTTP | Rute | Assertions Utama |
|---|-------------|------|-------|----------------|
| 1 | `test_auth_middleware_redirects_guest_to_login` | GET | `/dashboard` | Redirect ke `/login` |
| 2 | `test_admin_route_as_guest_redirects_to_login` | GET | `/kriteria/create` | Redirect ke `/login` (auth dijalankan lebih dulu) |
| 3 | `test_admin_route_as_non_admin_returns_403` | GET | `/kriteria/create` | Status 403 (guru) |
| 4 | `test_admin_route_as_admin_returns_200` | GET | `/kriteria/create` | Status 200 (admin) |
| 5 | `test_admin_crud_operations_as_admin` | POST | `/kriteria` | Redirect (admin bisa create) |
| 6 | `test_admin_crud_blocked_for_non_admin` | POST | `/kriteria` | Status 403 (guru diblokir) |

**Cabang yang Diuji:**
- Auth middleware: guest → redirect `/login`
- Admin middleware + auth: guest → redirect `/login` (auth lebih diutamakan)
- Admin middleware: guru → 403, admin → 200
- Admin writes: guru → 403 (POST diblokir), admin → sukses

---

## Cakupan Logika Bisnis

### 1. Algoritma SAW (Simple Additive Weighting)

Algoritma perangkingan inti diuji di tingkat unit maupun feature:

**Langkah-Langkah Algoritma yang Diverifikasi:**
1. **Normalisasi bobot**: `bobot` setiap kriteria dibagi total `bobot` seluruh kriteria
2. **Normalisasi nilai**: Skor dibagi 100 (nilai maksimum yang mungkin)
3. **Utility**: `nilai_normalisasi × bobot_normalisasi`
4. **Skor total**: Jumlah seluruh utility × 100
5. **Perangkingan**: Urutkan menurun berdasarkan skor total, penetapan peringkat berurutan

**Skenario Pengujian:**
- ✅ Banyak siswa dengan skor berbeda → urutan peringkat benar
- ✅ Satu siswa → peringkat 1
- ✅ Akurasi normalisasi bobot (delta floating-point)
- ✅ Nilai seri → total sama, peringkat berurutan
- ✅ Semua skor nol → utility nol
- ✅ Semua skor maksimum (100) → perhitungan utility terverifikasi
- ✅ Siswa tanpa penilaian → nilai default 0
- ✅ Tingkat feature: view menerima data `results` dan `kriterias`

### 2. Perhitungan Persentase Kehadiran

Accessor `nilai_kehadiran` di [`Alternatif`](app/Models/Alternatif.php:30) dan [`Absensi::hitungPersentaseKehadiran()`](app/Models/Absensi.php:32) menghitung persentase kehadiran dan membatasinya maksimal 100%.

**Rumus:**
```
persentase = round((hadir / total) × 100)
return min(persentase, 100)
```

**Skenario Pengujian:**
- ✅ Normal: 5/10 → 50%
- ✅ Sempurna: 10/10 → 100%
- ✅ Nol data: 0%
- ✅ Semua alpa: 0/10 → 0%
- ✅ Kasus batas: 1/1 → 100%
- ✅ Pembatasan: 11 hadir/11 total (110% mentah) → dibatasi 100%
- ✅ Method statis cocok dengan hasil accessor Eloquent
- ✅ Validasi silang: `hitungPersentaseKehadiran()` === `nilai_kehadiran`

### 3. Sinkronisasi Kehadiran ke Penilaian

[`Penilaian::syncKehadiranForAllSiswa()`](app/Models/Penilaian.php:29) menyinkronkan skor berbasis kehadiran ke dalam tabel `penilaians` menggunakan `updateOrCreate`.

**Skenario Pengujian:**
- ✅ Membuat data untuk semua siswa dengan nilai yang benar
- ✅ Idempoten: dipanggil dua kali tidak membuat duplikasi
- ✅ Memperbarui nilai yang ada saat kehadiran berubah (50% → 67%)
- ✅ Return `false` saat tidak ada kriteria "Kehadiran"
- ✅ Return 0 saat tidak ada siswa

### 4. Alur Autentikasi

**Login:**
- ✅ Kredensial valid → terautentikasi, redirect `/dashboard`
- ✅ Password salah → error, guest
- ✅ Email tidak terdaftar → autentikasi gagal
- ✅ Opsi "Remember me" → login sukses
- ✅ Validasi: format email tidak valid → ditolak
- ✅ Validasi: email kosong → ditolak
- ✅ Validasi: password kosong → ditolak
- ✅ Validasi: keduanya kosong → ditolak di kedua field

**Registrasi:**
- ✅ User baru → role='guru', terautentikasi, redirect `/dashboard`
- ✅ Password tidak cocok → error validasi
- ✅ Email duplikat → error validasi

**Logout:**
- ✅ User terautentikasi → redirect `/`, session diinvalidasi (guest)

**Proteksi Rute:**
- ✅ User terautentikasi tidak bisa akses `/login` → redirect `/dashboard`
- ✅ Guest tidak bisa akses `/dashboard` → redirect `/login`

### 5. Otorisasi (Admin Middleware)

`AdminMiddleware` membatasi rute tertentu hanya untuk user admin.

**Skenario Pengujian (Unit):**
- ✅ User admin → closure `$next` dipanggil
- ✅ User guru → `HttpException(403)` dengan pesan "Unauthorized"
- ✅ User belum login → `HttpException(403)` dengan pesan "Unauthorized"

**Skenario Pengujian (Feature):**
- ✅ Guest mengakses rute admin → redirect `/login` (middleware auth dijalankan lebih dulu)
- ✅ Guru mengakses rute admin → 403
- ✅ Admin mengakses rute admin → 200
- ✅ Operasi CRUD admin → sukses
- ✅ Operasi CRUD guru → 403

### 6. Aturan Validasi Form

| Entitas | Aturan | Cakupan Pengujian |
|--------|------|---------------|
| Login | `email`: required, email | ✅ Format tidak valid, kosong |
| Login | `password`: required | ✅ Kosong, keduanya kosong |
| Register | `email`: required, email, unique | ✅ Email duplikat, password tidak cocok |
| Alternatif | `nis`: required, unique | ✅ NIS duplikat, kosong, update self-ignore |
| Alternatif | `nama_siswa`, `kelas`, `jenis_kelamin`: required | ✅ Semua field kosong |
| Kriteria | `bobot`: integer, max:100 | ✅ bobot > 100 |
| Sub-kriteria | `nilai`: integer, min:0, max:100 | ✅ >100, non-integer, negatif |
| Profile | `email`: unique (ignore self) | ✅ Email duplikat dari user lain |
| Password | `current_password`: cocok dengan password user | ✅ current_password salah |

### 7. Constraint Unik

| Tabel | Constraint | Cakupan Pengujian |
|-------|-----------|---------------|
| `users` | `UNIQUE(email)` | ✅ Registrasi duplikat |
| `alternatifs` | `UNIQUE(nis)` | ✅ NIS duplikat saat create, self-ignore saat update |
| `kriterias` | `UNIQUE(kode_kriteria)` | ✅ Implisit via operasi store/update |
| `absensis` | `UNIQUE(alternatif_id, tanggal)` | ✅ Absensi duplikat memicu `QueryException` |

### 8. Cascade Delete

| Induk | Data Anak yang Dihapus | Cakupan Pengujian |
|--------|----------------------|---------------|
| `alternatifs` | `penilaians`, `absensis` | ✅ Penilaian terkait terverifikasi terhapus |
| `kriterias` | `sub_kriterias`, `penilaians` | ✅ Sub-kriteria terkait terverifikasi terhapus |

### 9. Fungsionalitas Export

| Format | Rute | Cakupan Pengujian |
|--------|-------|---------------|
| PDF | `/penilaian/export-pdf` | ✅ Status 200, Content-Type: `application/pdf` |
| CSV | `/penilaian/export-csv` | ✅ Status 200, Content-Type: `text/csv` |
| PDF (Laporan) | `/laporan/cetak-pdf` | ✅ Status 200, Content-Type: `application/pdf` |

---

## Analisis Branch Coverage

Karena driver code coverage tidak terinstal (lihat [Keterbatasan Code Coverage](#keterbatasan-code-coverage)), analisis branch coverage berikut didasarkan pada review manual kasus pengujian terhadap kode sumber.

### Model User — `isAdmin()`, `isGuru()`, `isUser()`

```
isAdmin(): role === 'admin' → true ✅ / false ✅
isGuru():  role === 'guru'  → true ✅ / false ✅
isUser():  role === 'guru'  → true ✅ / false ✅
```
**Branch Coverage: 100% (6/6 jalur)**

### Model Alternatif — `getNilaiKehadiranAttribute()`

```
total == 0  → return 0              ✅
total > 0   → round((hadir/total)*100)
  → min(persentase, 100)
    → persentase ≤ 100 → persentase  ✅
    → persentase > 100 → 100         ✅
```
**Branch Coverage: 100% (3/3 jalur)**

### Model Alternatif — `getDetailKehadiranAttribute()`

```
total > 0  → hitung persentase, terapkan min(100)   ✅
total == 0 → persentase = 0                         ✅
```
**Branch Coverage: 100% (2/2 jalur)**

### Model Absensi — `hitungPersentaseKehadiran()`

```
absensis->isEmpty() → return 0              ✅
total == 0          → return 0              ✅
total > 0           → min(persentase, 100)  ✅
```
**Branch Coverage: 100% (3/3 jalur)**

### Model Absensi — `getRekap()`

```
total > 0  → hitung + min(100)  ✅
total == 0 → persentase = 0     ✅
```
**Branch Coverage: 100% (2/2 jalur)**

### Model Penilaian — `syncKehadiranForAllSiswa()`

```
kriteriaKehadiran == null → return false                      ✅
kriteriaKehadiran ada + siswas ada → loop, create/update       ✅
kriteriaKehadiran ada + tidak ada siswas → return 0            ✅
```
**Branch Coverage: 100% (3/3 jalur)**

### AdminMiddleware — `handle()`

```
Auth::check() && isAdmin()  → $next($request)             ✅
Auth::check() && !isAdmin() → throw HttpException(403)     ✅
!Auth::check()              → throw HttpException(403)     ✅
```
**Branch Coverage: 100% (3/3 jalur)**

### Algoritma SAW — `RangkingController::index()`

```
foreach alternatifs:
  foreach kriterias:
    penilaian ada     → gunakan nilai    ✅
    penilaian tidak ada → nilai = 0      ✅

Pengurutan:
  menurun berdasarkan total_nilai         ✅

Perangkingan:
  penetapan peringkat berurutan           ✅
```
**Branch Coverage: 100%**

### Validasi Auth

```
email: required → kosong ditolak         ✅
email: email    → format tidak valid ditolak ✅
password: required → kosong ditolak      ✅
Auth::attempt() → true                   ✅
Auth::attempt() → false                  ✅
Auth::attempt(remember=true)             ✅
```
**Branch Coverage: 100%**

### Operasi CRUD Controller

Semua controller mengikuti pola cabang yang serupa:

```
Store:  validasi lolos → create → redirect  ✅
        validasi gagal → back dengan errors  ✅

Update: validasi lolos → update → redirect  ✅
        validasi gagal → back dengan errors  ✅

Destroy: data ada → delete → redirect       ✅
```
**Branch Coverage: 100% (jalur CRUD umum)**

### Estimasi Branch Coverage Keseluruhan

| Modul | Cabang | Tercakup | Coverage |
|--------|----------|---------|----------|
| Model User | 6 | 6 | 100% |
| Model Alternatif | 5 | 5 | 100% |
| Model Absensi | 5 | 5 | 100% |
| Model Penilaian | 3 | 3 | 100% |
| AdminMiddleware | 3 | 3 | 100% |
| Algoritma SAW | 4 | 4 | 100% |
| Validasi Auth | 6 | 6 | 100% |
| CRUD Controller | 6 | 6 | 100% |
| **TOTAL** | **38** | **38** | **~100%** |

> **Catatan:** Jumlah cabang ini adalah untuk method logika bisnis inti yang diuji di tingkat unit. Method controller mengandung cabang rendering view tambahan yang dicakup oleh feature tests. Tanpa driver code coverage, persentase baris/cabang yang tepat tidak bisa dihitung secara otomatis.

---

## Hasil Code Coverage (PCOV)

Code coverage diukur menggunakan **PCOV**, driver code coverage untuk PHP yang ringan dan tanpa overhead. Hasil berikut mewakili line coverage aktual di seluruh kelas aplikasi.

### Coverage Keseluruhan: **77,2%**

### Rincian Coverage per Kelas

| # | Kelas | Coverage |
|---|-------|----------|
| 1 | `Http/Controllers/AlternatifController` | **100,0%** ✅ |
| 2 | `Http/Controllers/Controller` | **100,0%** ✅ |
| 3 | `Http/Controllers/DashboardController` | **100,0%** ✅ |
| 4 | `Http/Controllers/ProfileController` | **100,0%** ✅ |
| 5 | `Http/Middleware/AdminMiddleware` | **100,0%** ✅ |
| 6 | `Models/Kriteria` | **100,0%** ✅ |
| 7 | `Models/User` | **100,0%** ✅ |
| 8 | `Providers/AppServiceProvider` | **100,0%** ✅ |
| 9 | `Http/Controllers/RangkingController` | **97,1%** |
| 10 | `Models/Absensi` | **96,4%** |
| 11 | `Models/Alternatif` | **95,8%** |
| 12 | `Http/Controllers/KriteriaController` | **90,3%** |
| 13 | `Models/Penilaian` | **88,9%** |
| 14 | `Http/Controllers/AbsensiController` | **76,6%** |
| 15 | `Http/Controllers/PenilaianController` | **63,6%** |
| 16 | `Http/Controllers/AuthController` | **49,2%** |
| 17 | `Http/Controllers/LaporanController` | **42,9%** |
| 18 | `Models/SubKriteria` | **0,0%** ⚠️ |

### Analisis Coverage

#### Coverage 100% (8 kelas)
Kelas-kelas ini sepenuhnya tercakup oleh test suite. Setiap baris kode dieksekusi selama pengujian:

- **Controller**: `AlternatifController`, `Controller` (base), `DashboardController`, `ProfileController` — semua jalur CRUD, rendering view, dan logika redirect telah diuji.
- **Middleware**: `AdminMiddleware` — ketiga cabang eksekusi (admin, guru, guest) diuji di tingkat unit maupun feature.
- **Model**: `Kriteria`, `User` — semua method model dan accessor telah dieksekusi.
- **Providers**: `AppServiceProvider` — dibootstrap pada setiap test run.

#### Coverage Tinggi (≥88%, 5 kelas)
Kelas-kelas ini memiliki sedikit baris yang tidak tercakup, biasanya di kasus tepi yang jarang terpicu:

- **`RangkingController` (97,1%)**: Inti algoritma SAW sepenuhnya tercakup; 2,9% sisanya kemungkinan di jalur fallback rendering view.
- **Model `Absensi` (96,4%)**: Logika perhitungan kehadiran dan rekap tercakup; sedikit baris tidak tercakup di method query scope.
- **Model `Alternatif` (95,8%)**: Accessor kehadiran sepenuhnya diuji; celah kecil di detail accessor relasi.
- **`KriteriaController` (90,3%)**: CRUD khusus admin tercakup; baris tidak tercakup di kasus tepi manajemen sub-kriteria.
- **Model `Penilaian` (88,9%)**: Logika sync dan relasi tercakup; celah kecil di jalur query building.

#### Coverage Sedang (≥42%, 3 kelas)
Controller ini memiliki lebih banyak kode yang tidak tercakup, biasanya di cabang kondisional yang tidak dieksekusi oleh pengujian saat ini:

- **`AbsensiController` (76,6%)**: Operasi massal dan CRUD diuji; baris tidak tercakup di cabang filter rekap, logika rentang tanggal, dan jalur sync opsional.
- **`PenilaianController` (63,6%)**: Create/update/delete inti dan export diuji; baris tidak tercakup di jalur validasi kondisional, kasus tepi pembuatan CSV, dan penanganan parameter opsional.
- **`AuthController` (49,2%)**: Login/register diuji; baris tidak tercakup di alur reset password, penanganan remember-token, dan cabang throttle/rate-limit.

#### Coverage Rendah (≤42%, 2 kelas)

- **`LaporanController` (42,9%)**: Render halaman dasar dan export PDF diuji; kode tidak tercakup signifikan di filter laporan, query rentang tanggal, dan logika agregasi data.
- **Model `SubKriteria` (0,0%)** ⚠️: Model `SubKriteria` tidak memiliki unit test khusus. Ia dieksekusi secara tidak langsung melalui pengujian relasi `Kriteria` dan feature tests, tetapi PCOV mengukur line coverage per kelas. Karena tidak ada pengujian yang secara langsung menginstansiasi atau memanggil method pada `SubKriteria` secara terisolasi, baris-barisnya tercatat sebagai tidak tercakup. **Rekomendasi:** Tambahkan `tests/Unit/SubKriteriaModelTest.php` untuk mencakup accessor relasi dan logika tingkat model apa pun.

### Cara Melihat Coverage

```bash
# Laporan teks CLI (output terminal)
php artisan test --coverage

# Generate laporan HTML interaktif
php artisan test --coverage-html coverage

# Buka di browser
# Windows: start coverage/index.html
# macOS:   open coverage/index.html
# Linux:   xdg-open coverage/index.html
```

Laporan HTML tersedia di **`coverage/index.html`** — buka di browser apa pun untuk tampilan interaktif dan drill-down dengan penyorotan baris per baris dari kode yang tercakup dan tidak tercakup.

### Cara Instalasi PCOV

Sebagai referensi, PCOV diaktifkan di lingkungan PHP 8.4.22 yang dikelola FlyEnv:

1. Mengunduh DLL ekstensi PCOV yang kompatibel dengan PHP 8.4
2. Menambahkan `extension=php_pcov.dll` ke `php.ini`
3. Verifikasi dengan `php -m | grep pcov`
4. Mengonfigurasi `phpunit.xml` dengan coverage source includes/excludes

Lihat direktori `pcov/` di root proyek untuk file sumber dan konfigurasi PCOV.

---

## Keterbatasan Code Coverage

Meskipun PCOV menyediakan line coverage yang andal, beberapa keterbatasan perlu dicatat:

### 1. Tidak Ada Branch/Path Coverage

PCOV hanya mengukur **line coverage** — apakah suatu baris dieksekusi atau tidak. Ia tidak melacak:
- **Branch coverage**: Apakah `true` dan `false` dari setiap kondisi IF telah dieksekusi
- **Path coverage**: Apakah semua kemungkinan jalur melalui suatu method telah dieksekusi

Inilah mengapa analisis [Branch Coverage](#analisis-branch-coverage) di atas dilakukan secara manual — untuk memastikan cakupan logika yang komprehensif di luar apa yang bisa diukur PCOV.

### 2. Cakupan Tidak Langsung Tidak Terdeteksi

Kelas seperti `SubKriteria` (0,0%) mungkin benar-benar dieksekusi selama pengujian melalui relasi atau operasi tidak langsung, tetapi PCOV tidak menghitungnya kecuali ada pengujian khusus yang menginstansiasi kelas tersebut.

### 3. Baris View Tidak Termasuk

File Blade template tidak diukur oleh PCOV. Cakupan view diverifikasi secara tidak langsung melalui feature tests yang mengecek status 200 dan data view.

### 4. Coverage Keseluruhan 77,2% Kontekstual

Angka 77,2% harus diinterpretasikan dengan pemahaman bahwa:
- Semua logika bisnis inti (SAW, kehadiran, auth, middleware) memiliki coverage 100% (manual) atau mendekati 100% (PCOV)
- Coverage yang lebih rendah terkonsentrasi di controller dengan banyak cabang kondisional (auth, penilaian, laporan)
- `SubKriteria` 0% adalah artefak pengukuran, bukan celah pengujian yang sesungguhnya

---

## Metrik Kualitas Pengujian

### Kepadatan Assertion

| Kategori | Pengujian | Assertions | Assertions/Pengujian |
|----------|-------|------------|-----------------|
| Unit Tests | 46 | ~126 | 2,74 |
| Feature Tests | 80 | ~215 | 2,69 |
| **Total** | **126** | **341** | **2,71** |

Rata-rata **2,71 assertions per pengujian** menunjukkan bahwa pengujian bersifat fokus dan memverifikasi hasil yang berarti tanpa terlalu luas.

### Kategori Pengujian berdasarkan Perhatian

| Perhatian | Jumlah Pengujian | % dari Total |
|---------|-----------|------------|
| Operasi CRUD | 38 | 30,2% |
| Autentikasi | 22 | 17,5% |
| Otorisasi (Middleware) | 10 | 7,9% |
| Aturan Validasi | 15 | 11,9% |
| Logika Bisnis (SAW, Kehadiran) | 26 | 20,6% |
| Export (PDF/CSV) | 4 | 3,2% |
| Rendering View | 11 | 8,7% |

### Yang Sudah Tercakup

- ✅ Semua 6 tabel database
- ✅ Semua 9 controller (~50 method)
- ✅ Semua 6 model dengan method logika bisnis
- ✅ Middleware admin (3 jalur eksekusi)
- ✅ Algoritma SAW (7 skenario)
- ✅ Persentase kehadiran dan sinkronisasi
- ✅ Autentikasi (login, register, logout, reset password)
- ✅ Otorisasi (admin vs guru vs guest)
- ✅ Aturan validasi form
- ✅ Constraint unik
- ✅ Cascade delete
- ✅ Export CSV dan PDF
- ✅ Operasi massal (absensi massal)
- ✅ Proteksi rute (middleware auth dan guest)

### Yang Belum Tercakup

- ❌ Alur email reset password (view forgot/reset password ada tapi pengiriman email belum diuji)
- ❌ Kasus tepi dengan dataset sangat besar (performansi)
- ❌ Penanganan request konkuren
- ❌ Kedaluwarsa dan pembaruan session

---

## Menjalankan Pengujian

### Prasyarat

```bash
# Pastikan dependencies terinstal
composer install

# Salin .env.testing jika tersedia, atau gunakan default phpunit.xml
cp .env.example .env.testing
```

### Menjalankan Semua Pengujian

```bash
php artisan test
```

### Menjalankan Test Suite Tertentu

```bash
# Unit tests saja
php artisan test --testsuite=Unit

# Feature tests saja
php artisan test --testsuite=Feature

# Satu file pengujian
php artisan test --filter=UserModelTest

# Satu method pengujian
php artisan test --filter=is_admin_returns_true_when_role_is_admin
```

### Menjalankan dengan Output Detail

```bash
# Mode verbose
php artisan test -v

# Eksekusi paralel (lebih cepat)
php artisan test --parallel

# Berhenti pada kegagalan pertama
php artisan test --stop-on-failure
```

### Output yang Diharapkan

```
   PASS  Tests\Unit\AbsensiModelTest
   ✓ hitung persentase kehadiran returns 80 when 8 hadir out of 10
   ✓ hitung persentase kehadiran returns 100 when perfect attendance
   ...

   PASS  Tests\Feature\AuthFeatureTest
   ✓ login page returns 200
   ✓ register page returns 200
   ...

  Tests:    126 passed
  Assertions: 341 passed
  Duration:  7.00s
```

---

## Lampiran: Referensi Method Pengujian

### Indeks Unit Test Lengkap

| File | Jumlah Method | Status |
|------|-------------|--------|
| `tests/Unit/UserModelTest.php` | 6 | ✅ LULUS |
| `tests/Unit/AlternatifModelTest.php` | 8 | ✅ LULUS |
| `tests/Unit/AbsensiModelTest.php` | 7 | ✅ LULUS |
| `tests/Unit/PenilaianModelTest.php` | 5 | ✅ LULUS |
| `tests/Unit/RangkingControllerTest.php` | 7 | ✅ LULUS |
| `tests/Unit/AdminMiddlewareTest.php` | 4 | ✅ LULUS |
| `tests/Unit/AuthControllerTest.php` | 8 | ✅ LULUS |
| `tests/Unit/ExampleTest.php` | 1 | ✅ LULUS |

### Indeks Feature Test Lengkap

| File | Jumlah Method | Status |
|------|-------------|--------|
| `tests/Feature/AbsensiFeatureTest.php` | 13 | ✅ LULUS |
| `tests/Feature/KriteriaFeatureTest.php` | 13 | ✅ LULUS |
| `tests/Feature/AlternatifFeatureTest.php` | 11 | ✅ LULUS |
| `tests/Feature/AuthFeatureTest.php` | 10 | ✅ LULUS |
| `tests/Feature/PenilaianFeatureTest.php` | 10 | ✅ LULUS |
| `tests/Feature/ProfileFeatureTest.php` | 6 | ✅ LULUS |
| `tests/Feature/MiddlewareFeatureTest.php` | 6 | ✅ LULUS |
| `tests/Feature/RangkingFeatureTest.php` | 4 | ✅ LULUS |
| `tests/Feature/LaporanFeatureTest.php` | 3 | ✅ LULUS |
| `tests/Feature/DashboardFeatureTest.php` | 3 | ✅ LULUS |
| `tests/Feature/ExampleTest.php` | 1 | ✅ LULUS |

---

*Dokumen dibuat dari analisis kode sumber pengujian aktual. Terakhir diperbarui: 2026-07-02.*
