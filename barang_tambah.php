<?php

require_once 'config.php';

require_login();

$title = 'Tambah Barang';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nama = trim($_POST['nama_barang'] ?? '');
    $modal = (float) ($_POST['harga_modal'] ?? 0);
    $jual = (float) ($_POST['harga_jual'] ?? 0);
    $foto = null;

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
                'INSERT INTO barang(
                    user_id,
                    foto,
                    nama_barang,
                    harga_modal,
                    harga_jual
                ) VALUES(?,?,?,?,?)'
            );

            $stmt->execute([
                $_SESSION['user_id'],
                $foto,
                $nama,
                $modal,
                $jual
            ]);

            header('Location: barang.php?ok=1');

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
            Tambah Barang
        </h1>

        <p class="muted">
            Tambahkan informasi produk yang akan dijual.
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


    <div class="form-note">
        Foto produk sudah didukung.
        Untuk sementara boleh dikosongkan,
        nanti bisa ditambahkan saat finalisasi data barang.
    </div>


    <form
        method="post"
        enctype="multipart/form-data"
    >

        <div class="form-group">

            <label>
                Foto Barang
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
                placeholder="Contoh: Air Mineral 600ml"
                required
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
                    placeholder="0"
                    required
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
                    placeholder="0"
                    required
                >

            </div>

        </div>


        <button class="btn btn-primary">
            Simpan Barang
        </button>

    </form>

</div>

<?php include 'footer.php'; ?>