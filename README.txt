KASIR TOKO - PHP + MySQL

Fitur:
- Register: nama pengguna, nama toko, email unik, password hashed dengan password_hash().
- Login menggunakan email + password.
- Barang CRUD dengan foto, nama, harga modal, harga jual.
- Barang terikat user_id, jadi akun lain tidak bisa melihat/mengubah barang milik user lain.
- Penjualan multi-item dan kuantitas.
- Total otomatis dari harga jual.
- Input uang pelanggan + kembalian otomatis.
- Riwayat penjualan: tanggal/jam, total transaksi, barang, qty, subtotal, uang bayar, kembalian.
- Logout menghapus session.
- Responsive desktop & mobile.

Cara menjalankan di XAMPP:
1. Copy folder kasir_toko ke C:\xampp\htdocs\
2. Jalankan Apache dan MySQL.
3. Buka phpMyAdmin.
4. Import file database.sql.
5. Pastikan config.php menggunakan database kasir_toko, user root, password kosong (default XAMPP).
6. Buka http://localhost/kasir_toko/register.php

Catatan:
- Folder uploads harus bisa ditulisi oleh PHP.
- Password tidak memakai MD5 karena password_hash() lebih aman dan memenuhi syarat password terenkripsi/hash.
