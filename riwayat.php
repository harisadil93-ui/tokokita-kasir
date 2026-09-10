<?php

require_once 'config.php';

require_login();

$title = 'Riwayat Penjualan';
$userId = (int) $_SESSION['user_id'];

$stmt = $pdo->prepare(
    'SELECT * FROM penjualan
     WHERE user_id=?
     ORDER BY created_at DESC,id DESC'
);

$stmt->execute([
    $userId
]);

$sales = $stmt->fetchAll();

$detailStmt = $pdo->prepare(
    'SELECT * FROM penjualan_detail
     WHERE penjualan_id=?
     ORDER BY id'
);

include 'header.php';

?>

<div class="page-title">

    <div>

        <div class="eyebrow">
            Laporan transaksi
        </div>

        <h1>
            Riwayat Penjualan
        </h1>

        <p class="muted">
            Lihat tanggal, waktu, detail barang,
            total pembayaran, dan kembalian setiap transaksi.
        </p>

    </div>

</div>


<?php if (!$sales): ?>

    <div class="card">
        Belum ada transaksi.
    </div>

<?php endif; ?>


<?php foreach ($sales as $sale):

    $detailStmt->execute([
        $sale['id']
    ]);

    $details = $detailStmt->fetchAll();

?>

    <div class="card history-card">

        <div class="history-head">

            <div class="history-id">

                <div class="history-id-icon">
                    🧾
                </div>

                <div>

                    <strong>
                        Transaksi #<?= $sale['id'] ?>
                    </strong>

                    <div class="muted">

                        <?= date(
                            'd-m-Y H:i:s',
                            strtotime($sale['created_at'])
                        ) ?>

                    </div>

                </div>

            </div>


            <div>

                <span class="badge">
                    Total <?= rupiah($sale['total']) ?>
                </span>

            </div>

        </div>


        <div class="table-scroll">

            <table class="table">

                <thead>

                    <tr>
                        <th>Barang</th>
                        <th>Harga</th>
                        <th>Qty</th>
                        <th>Subtotal</th>
                    </tr>

                </thead>

                <tbody>

                    <?php foreach ($details as $d): ?>

                        <tr>

                            <td>
                                <?= e($d['nama_barang']) ?>
                            </td>

                            <td>
                                <?= rupiah($d['harga_jual']) ?>
                            </td>

                            <td>
                                <?= $d['qty'] ?>
                            </td>

                            <td>
                                <?= rupiah($d['subtotal']) ?>
                            </td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>


        <div class="history-summary">

            <div
                class="summary-box"
                style="margin-top:10px"
            >

                <div class="summary-row">

                    <span>
                        Uang Bayar
                    </span>

                    <strong>
                        <?= rupiah($sale['uang_bayar']) ?>
                    </strong>

                </div>


                <div class="summary-row">

                    <span>
                        Kembalian
                    </span>

                    <strong>
                        <?= rupiah($sale['kembalian']) ?>
                    </strong>

                </div>

            </div>

        </div>

    </div>

<?php endforeach; ?>


<?php include 'footer.php'; ?>