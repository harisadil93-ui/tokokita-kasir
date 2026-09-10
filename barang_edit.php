<?php

require_once 'config.php';

require_login();

$title = 'Edit Barang';
$error = '';

$id = (int) ($_GET['id'] ?? 0);

$stmt = $pdo->prepare(
    'SELECT * FROM barang WHERE id=? AND user_id=?'
);

$stmt->execute([
    $id,
    $_SESSION['user_id']
]);

$item = $stmt->fetch();

if (!$item) {
    http_response_code(404);
    exit('Barang tidak ditemukan.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nama = trim($_POST['nama_barang'] ?? '');
    $modal = (float) ($_POST['harga_modal'] ?? 0);
    $jual = (float) ($_POST['harga_jual'] ?? 0);
    $foto = $item['foto'];

    if ($nama === '' || $modal < 0 || $jual < 0) {

        $error = 'Data barang tidak valid.';

    } else {

        if (
            isset($_FILES['foto']) &&
            $_FILES['foto']['error'] === UPLOAD_ERR_OK
        ) {

            $allowed = [
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/webp' => 'webp'
            ];

            $mime = mime_content_type(
                $_FILES['foto']['tmp_name']
            );

            if (!isset($allowed[$mime])) {

                $error = 'Foto harus JPG, PNG, atau WEBP.';

            } else {

                if (
                    $foto &&
                    is_file(__DIR__ . '/uploads/' . $foto)
                ) {
                    @unlink(
                        __DIR__ . '/uploads/' . $foto
                    );
                }

                $foto =
                    bin2hex(random_bytes(8)) .
                    '.' .
                    $allowed[$mime];

                move_uploaded_file(
                    $_FILES['foto']['tmp_name'],
                    __DIR__ . '/uploads/' . $foto
                );
            }
        }

        if ($error === '') {

            $stmt = $pdo->prepare(
                'UPDATE barang
                 SET foto=?,
                     nama_barang=?,
                     harga_modal=?,
                     harga_jual=?
                 WHERE id=? AND user_id=?'
            );

            $stmt->execute([
                $foto,
                $nama,
                $modal,
                $jual,
                $id,
                $_SESSION['user_id']
            ]);

            header(
                'Location: barang.php?ok=1'
            );

            exit;
        }
    }
}

include 'header.php';

?>

<div class="page-title">

    <div>

        <div class="eyebrow">
            Inventori
        </div>

        <h1>
            Edit Barang
        </h1>

        <p class="muted">
            Perbarui informasi produk tanpa
            mengubah data lainnya.
        </p>

    </div>

    <a
        class="btn btn-outline"
        href="barang.php"
    >
        ← Kembali
    </a>

</div>


<div class="card form-card">

    <?php if ($error): ?>

        <div class="alert alert-danger">
            <?= e($error) ?>
        </div>

    <?php endif; ?>


    <?php if ($item['foto']): ?>

        <div style="margin-bottom:18px">

            <label>
                Foto Saat Ini
            </label>

            <img
                class="product-img"
                style="width:90px;height:90px"
                src="uploads/<?= e($item['foto']) ?>"
                alt="<?= e($item['nama_barang']) ?>"
            >

        </div>

    <?php endif; ?>


    <form
        method="post"
        enctype="multipart/form-data"
    >

        <div class="form-group">

            <label>
                Ganti Foto (opsional)
            </label>

            <input
                type="file"
                name="foto"
                accept="image/jpeg,image/png,image/webp"
            >

        </div>


        <div class="form-group">

            <label>
                Nama Barang
            </label>

            <input
                name="nama_barang"
                required
                value="<?= e($item['nama_barang']) ?>"
            >

        </div>


        <div class="grid grid-2">

            <div class="form-group">

                <label>
                    Harga Modal
                </label>

                <input
                    type="number"
                    name="harga_modal"
                    min="0"
                    step="1"
                    required
                    value="<?= (float) $item['harga_modal'] ?>"
                >

            </div>


            <div class="form-group">

                <label>
                    Harga Jual
                </label>

                <input
                    type="number"
                    name="harga_jual"
                    min="0"
                    step="1"
                    required
                    value="<?= (float) $item['harga_jual'] ?>"
                >

            </div>

        </div>


        <button class="btn btn-primary">
            Simpan Perubahan
        </button>

    </form>

</div>

<?php include 'footer.php'; ?>