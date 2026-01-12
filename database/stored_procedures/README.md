# Stored Procedures untuk CRUD tabel mst_pl

File ini berisi stored procedures untuk operasi CRUD pada tabel `mst_pl` (Profile Lulusan).

## Instalasi

Jalankan file SQL di database MySQL/MariaDB Anda:

```bash
mysql -u root -p nama_database < database/stored_procedures/mst_pl_crud.sql
```

Atau melalui phpMyAdmin/HeidiSQL:
1. Buka database Anda
2. Pilih tab "SQL"
3. Copy-paste isi file `mst_pl_crud.sql`
4. Klik "Go" atau "Execute"

## Stored Procedures yang Tersedia

### 1. sp_mst_pl_create
Menambahkan data baru ke tabel mst_pl.

**Parameter:**
- `p_id` (VARCHAR(50)) - ID (required, unique, primary key)
- `p_desk` (VARCHAR(255)) - Deskripsi (required)
- `p_unsur` (VARCHAR(255)) - Unsur (optional)
- `p_keterangan` (TEXT) - Keterangan (optional)
- `p_result_message` (OUT VARCHAR(255)) - Pesan hasil

**Contoh penggunaan di Laravel:**
```php
DB::select('CALL sp_mst_pl_create(?, ?, ?, ?, @result_message)', [
    'PL001',
    'Deskripsi Profile Lulusan 1',
    'Unsur 1',
    'Keterangan 1'
]);

$result = DB::select('SELECT @result_message as message');
```

### 2. sp_mst_pl_read
Membaca semua data dari tabel mst_pl, diurutkan berdasarkan ID ASC.

**Contoh penggunaan di Laravel:**
```php
$data = DB::select('CALL sp_mst_pl_read()');
```

### 3. sp_mst_pl_read_by_id
Membaca data berdasarkan ID.

**Parameter:**
- `p_id` (VARCHAR(50)) - ID data yang akan dibaca

**Contoh penggunaan di Laravel:**
```php
$data = DB::select('CALL sp_mst_pl_read_by_id(?)', ['PL001']);
```

### 4. sp_mst_pl_update
Mengupdate data berdasarkan ID.

**Parameter:**
- `p_id` (VARCHAR(50)) - ID lama (untuk mencari data)
- `p_new_id` (VARCHAR(50)) - ID baru (bisa sama dengan p_id jika tidak diubah)
- `p_desk` (VARCHAR(255)) - Deskripsi baru
- `p_unsur` (VARCHAR(255)) - Unsur baru
- `p_keterangan` (TEXT) - Keterangan baru
- `p_result_message` (OUT VARCHAR(255)) - Pesan hasil

**Contoh penggunaan di Laravel:**
```php
DB::select('CALL sp_mst_pl_update(?, ?, ?, ?, ?, @result_message)', [
    'PL001',  // ID lama
    'PL001',  // ID baru (bisa diubah jika perlu)
    'Deskripsi Updated',
    'Unsur Updated',
    'Keterangan Updated'
]);

$result = DB::select('SELECT @result_message as message');
```

### 5. sp_mst_pl_delete
Menghapus data berdasarkan ID.

**Parameter:**
- `p_id` (VARCHAR(50)) - ID data yang akan dihapus
- `p_result_message` (OUT VARCHAR(255)) - Pesan hasil

**Contoh penggunaan di Laravel:**
```php
DB::select('CALL sp_mst_pl_delete(?, @result_message)', ['PL001']);
$result = DB::select('SELECT @result_message as message');
```

### 6. sp_mst_pl_delete_multiple
Menghapus multiple data berdasarkan array ID.

**Parameter:**
- `p_ids` (TEXT) - Comma-separated IDs (contoh: "PL001,PL002,PL003")
- `p_result_message` (OUT VARCHAR(255)) - Pesan hasil
- `p_deleted_count` (OUT INT) - Jumlah data yang dihapus

**Contoh penggunaan di Laravel:**
```php
$ids = implode(',', ['PL001', 'PL002', 'PL003']);
DB::select('CALL sp_mst_pl_delete_multiple(?, @result_message, @deleted_count)', [$ids]);
$result = DB::select('SELECT @result_message as message, @deleted_count as count');
```

## Catatan

- Semua stored procedures menggunakan transaction untuk memastikan data integrity
- Stored procedures akan melakukan validasi sebelum melakukan operasi
- Pesan hasil akan mengindikasikan apakah operasi berhasil atau gagal
- Untuk operasi CREATE dan UPDATE, akan dilakukan pengecekan duplikasi ID
- ID adalah VARCHAR(50) dan berfungsi sebagai primary key (bukan auto-increment)
