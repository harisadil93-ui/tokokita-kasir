<?php

require_once 'config.php';

require_login();

$title = 'Dashboard';
$userId = (int) $_SESSION['user_id'];

$stmt = $pdo->prepare(
    'SELECT COUNT(*) FROM barang WHERE user_id=?'
);

$stmt->execute([
    $userId
]);

$jumlahBarang = (int) $stmt->fetchColumn();


$stmt = $pdo->prepare(
    'SELECT COUNT(*) FROM penjualan WHERE user_id=?'
);

$stmt->execute([
    $userId
]);

$jumlahTransaksi = (int) $stmt->fetchColumn();


$stmt = $pdo->prepare(
    'SELECT COALESCE(SUM(total),0) FROM penjualan WHERE user_id=?'
);

$stmt->execute([
    $userId
]);

$omzet = (float) $stmt->fetchColumn();


include 'header.php';

?>

<div class="page-title">

    <div>

        <div class="eyebrow">
            Ringkasan toko
        </div>

        <h1>
            Halo, <?= e($_SESSION['nama_pengguna']) ?> 👋
        </h1>

        <p class="muted">
            Pantau aktivitas toko dan akses menu utama
            dari satu tempat.
        </p>

    </div>

    <a
        class="btn btn-primary"
        href="penjualan.php"
    >
        ＋ Transaksi Baru
    </a>

</div>


<div class="grid grid-3">

    <div class="card stat-card">

        <div class="stat-head">

            <div class="stat-icon">
                ▣
            </div>

        </div>

        <div class="stat-label">
            Jumlah Barang
        </div>

        <div class="stat">
            <?= $jumlahBarang ?>
        </div>

    </div>


    <div class="card stat-card">

        <div class="stat-head">

            <div class="stat-icon">
                🧾
            </div>

        </div>

        <div class="stat-label">
            Total Transaksi
        </div>

        <div class="stat">
            <?= $jumlahTransaksi ?>
        </div>

    </div>


    <div class="card stat-card">

        <div class="stat-head">

            <div class="stat-icon">
                ↗
            </div>

        </div>

        <div class="stat-label">
            Total Penjualan
        </div>

        <div class="stat">
            <?= rupiah($omzet) ?>
        </div>

    </div>

</div>


<div class="card quick-card">

    <div class="eyebrow">
        Akses cepat
    </div>

    <h2>
        Menu Utama
    </h2>


    <div class="quick-grid">

        <a
            class="quick-action"
            href="barang_tambah.php"
        >

            <div class="qa-icon">
                ＋
            </div>

            <div>

                <strong>
                    Tambah Barang
                </strong>

                <span>
                    Masukkan produk baru ke toko
                </span>

            </div>

        </a>


        <a
            class="quick-action"
            href="penjualan.php"
        >

            <div class="qa-icon">
                🛒
            </div>

            <div>

                <strong>
                    Mulai Penjualan
                </strong>

                <span>
                    Buat transaksi pelanggan
                </span>

            </div>

        </a>


        <a
            class="quick-action"
            href="riwayat.php"
        >

            <div class="qa-icon">
                ↺
            </div>

            <div>

                <strong>
                    Lihat Riwayat
                </strong>

                <span>
                    Cek transaksi yang sudah selesai
                </span>

            </div>

        </a>

    </div>

</div>

<?php include 'footer.php'; ?>