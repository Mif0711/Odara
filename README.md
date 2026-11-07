# ODARA

**Sistem Informasi Data Operasional** — aplikasi internal untuk mencatat dan mengelola transaksi Shopee dan Charger.

## Ringkasan

ODARA hanya memiliki dua role internal:

- **Admin** — mengelola akun internal, data kamar, dan data barang Charger. Admin **tidak** mengakses transaksi Shopee maupun Charger.
- **Petugas** — mencatat transaksi Shopee dan Charger, serta melihat dan mengelola transaksi miliknya sendiri.

Pelanggan **tidak** memiliki akun. Pada transaksi Shopee, nama pelanggan dan jenis barang diketik manual. Pada transaksi Charger, nama pelanggan diketik manual dan barang dipilih dari daftar yang dikelola Admin.

## Teknologi

- Laravel 12 (PHP 8.2+)
- SQLite (default, database kosong sudah disiapkan di `database/database.sqlite`)
- Blade + CSS/JavaScript tanpa build step

## Menjalankan Aplikasi

```bash
php artisan serve
```

Buka aplikasi. Karena database sengaja dikosongkan, ODARA akan mengarahkan Anda ke halaman **/setup** untuk membuat akun Admin pertama.

Setelah akun Admin dibuat:

1. Buka menu **Data Kamar** dan tambahkan kamar.
2. Buka menu **Data Barang Charger** dan tambahkan barang yang dapat dipilih Petugas.
3. Buka menu **Akun Internal** dan tambahkan akun Petugas.
4. Petugas mulai mencatat transaksi melalui menu **Transaksi Shopee** atau **Transaksi Charger**.

## Database

Database SQLite berada di `database/database.sqlite` dengan struktur tabel sudah dimigrasikan dan **tanpa data awal**.

Jika ingin membangun ulang struktur database:

```bash
php artisan migrate:fresh
```

> Perintah di atas menghapus seluruh data. Halaman `/setup` akan aktif kembali ketika tabel `users` kosong.

Tabel utama:

| Tabel | Fungsi |
| --- | --- |
| `users` | Akun internal Admin dan Petugas |
| `rooms` | Master data kamar |
| `charger_items` | Master data barang untuk transaksi Charger |
| `shopee_transactions` | Transaksi Shopee |
| `charger_transactions` | Transaksi Charger |

Data transaksi lama tidak terhapus ketika kamar atau barang dinonaktifkan karena status hanya berupa flag `is_active` dan relasi kamar/barang menggunakan `nullOnDelete`.

## Status Pengambilan Barang

Transaksi Shopee dan Charger memiliki status **Sudah diambil** / **Belum diambil**:

- status dapat diisi saat membuat atau mengedit transaksi;
- status ditampilkan sebagai badge pada daftar transaksi;
- badge dapat diklik untuk mengubah status dengan cepat;
- status hanya dapat diubah oleh Petugas pemilik transaksi; Admin tidak memiliki akses ke transaksi.

## Transaksi Charger

- Barang dipilih dari master **Data Barang Charger** yang dikelola Admin.
- Nomor PIN dipilih dari daftar angka **1 sampai 30** dan wajib diisi.
- Barang nonaktif tidak muncul pada formulir baru, tetapi tetap tersimpan pada transaksi lama.
- Transaksi Charger tidak menggunakan Harga Barang maupun Jasa.
- Petugas dapat membuat, melihat, mengedit, dan menghapus transaksi Charger miliknya sendiri.
- Admin tidak memiliki akses ke halaman transaksi Charger.

## Transaksi Shopee

- Nama pelanggan dan jenis barang diketik manual oleh Petugas.
- Transaksi Shopee tetap menggunakan Harga Barang dan Jasa.
- Harga Barang dan Jasa menerima format Rupiah seperti `5.000` atau `Rp 5.000` dan dibaca sebagai `5000`.
- Petugas hanya melihat dan mengelola transaksi Shopee miliknya sendiri.
- Admin tidak memiliki akses ke halaman transaksi Shopee.

## Laporan Admin

Admin memiliki dua halaman laporan yang mencakup seluruh aktivitas semua Petugas:

- **Laporan Shopee** — transaksi, harga barang, jasa, grand total, serta status pengambilan.
- **Laporan Charger** — transaksi, total QTY, serta status pengambilan.

Setiap laporan menyediakan:

- pemilih bulan dan tahun berbentuk kalender untuk menentukan periode;
- total transaksi dan total keseluruhan pada bulan terpilih;
- total transaksi dan total keseluruhan pada tahun terpilih;
- rekap 12 bulan dalam satu tahun beserta baris total tahunan;
- rekap per Petugas pada bulan terpilih;
- detail seluruh transaksi Petugas pada bulan terpilih dengan pagination;
- tombol **Export Excel (.xlsx)** untuk mengunduh detail transaksi bulan terpilih sebagai tabel Excel berformat (judul, border, lebar kolom, dan baris TOTAL).

## Filter Tanggal

Halaman Shopee dan Charger memiliki date picker modern dengan:

- pencarian satu tanggal,
- pencarian rentang tanggal,
- navigasi bulan sebelumnya/berikutnya,
- tombol kembali ke hari ini,
- reset filter,
- filter diproses melalui query database (`whereDate`).

## Pengujian

```bash
php artisan test
```

Test mencakup autentikasi, hak akses Admin/Petugas, pembatasan akses seluruh transaksi untuk Admin, CRUD transaksi, status pengambilan barang, laporan Shopee/Charger dengan filter bulan dan tahun, format Rupiah, pengelolaan kamar, pengelolaan barang Charger, pengelolaan akun internal, filter tanggal, dan render seluruh halaman utama.
