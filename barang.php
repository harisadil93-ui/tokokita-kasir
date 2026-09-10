<?php

require_once 'config.php';

require_login();

$title = 'Barang';
$userId = (int) $_SESSION['user_id'];

$stmt = $pdo->prepare(
    'SELECT * FROM barang WHERE user_id=? ORDER BY id DESC'
);

$stmt->execute([
    $userId
]);

$items = $stmt->fetchAll();

include 'header.php';

?>

<div class="page-title">

    <div>

        <div class="eyebrow">
            Inventori
        </div>

        <h1>
            Daftar Barang
        </h1>

        <p class="muted">
            Kelola seluruh produk milik akun toko Anda.
        </p>

    </div>

    <a
        class="btn btn-primary"
        href="barang_tambah.php"
    >
        ＋ Tambah Barang
    </a>

</div>


<?php if (isset($_GET['ok'])): ?>

    <div class="alert alert-success">
        Data barang berhasil diproses.
    </div>

<?php endif; ?>


<div class="card table-wrap">

    <div class="table-scroll">

        <table class="table">

            <thead>

                <tr>
                    <th>Foto</th>
                    <th>Nama Barang</th>
                    <th>Harga Modal</th>
                    <th>Harga Jual</th>
                    <th>Aksi</th>
                </tr>

            </thead>

            <tbody>

                <?php if (!$items): ?>

                    <tr>

                        <td
                            class="empty-state"
                            colspan="5"
                        >
                            Belum ada barang.
                            Klik “Tambah Barang” untuk mulai
                            menambahkan produk.
                        </td>

                    </tr>

                <?php endif; ?>


                <?php foreach ($items as $item): ?>

                    <tr>

                        <td>

                            <?php if ($item['foto']): ?>

                                <img
                                    class="product-img"
                                    src="uploads/<?= e($item['foto']) ?>"
                                    alt="<?= e($item['nama_barang']) ?>"
                                >

                            <?php else: ?>

                                <div class="product-placeholder">
                                    Belum ada<br>
                                    foto
                                </div>

                            <?php endif; ?>

                        </td>


                        <td>

                            <span class="product-name">
                                <?= e($item['nama_barang']) ?>
                            </span>

                        </td>


                        <td>

                            <span class="price-cost">
                                <?= rupiah($item['harga_modal']) ?>
                            </span>

                        </td>


                        <td>

                            <span class="price-sale">
                                <?= rupiah($item['harga_jual']) ?>
                            </span>

                        </td>


                        <td>

                            <div class="actions">

                                <a
                                    class="btn btn-sm btn-outline"
                                    href="barang_edit.php?id=<?= $item['id'] ?>"
                                >
                                    ✎ Edit
                                </a>

                                <a
                                    class="btn btn-danger btn-sm"
                                    href="barang_hapus.php?id=<?= $item['id'] ?>"
                                    onclick="return confirm('Hapus barang ini?')"
                                >
                                    Hapus
                                </a>

                            </div>

                        </td>

                    </tr>

                <?php endforeach; ?>

            </tbody>

        </table>

    </div>

</div>

<?php include 'footer.php'; ?>