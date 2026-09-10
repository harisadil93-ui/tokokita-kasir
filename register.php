<?php

require_once 'config.php';

if (!empty($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nama = trim($_POST['nama_pengguna'] ?? '');
    $toko = trim($_POST['nama_toko'] ?? '');

    $email = strtolower(
        trim($_POST['email'] ?? '')
    );

    $password = $_POST['password'] ?? '';

    if (
        $nama === '' ||
        $toko === '' ||
        $email === '' ||
        $password === ''
    ) {
        $error = 'Semua field wajib diisi.';

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = 'Format email tidak valid.';

    } elseif (strlen($password) < 6) {

        $error = 'Password minimal 6 karakter.';

    } else {

        $stmt = $pdo->prepare(
            'SELECT id FROM users WHERE email = ?'
        );

        $stmt->execute([
            $email
        ]);

        if ($stmt->fetch()) {

            $error = 'Email sudah terdaftar. Gunakan email lain.';

        } else {

            $hash = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            $stmt = $pdo->prepare(
                'INSERT INTO users (
                    nama_pengguna,
                    nama_toko,
                    email,
                    password
                ) VALUES (?, ?, ?, ?)'
            );

            $stmt->execute([
                $nama,
                $toko,
                $email,
                $hash
            ]);

            header(
                'Location: login.php?registered=1'
            );

            exit;
        }
    }
}

?>

<!doctype html>

<html lang="id">

<head>

    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width,initial-scale=1"
    >

    <title>
        Register - TokoKita
    </title>

    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >

</head>

<body>

<div class="auth-page auth-tokokita auth-logo-only">

    <section class="auth-visual">

        <div class="auth-logo-stage">

            <img
                src="assets/images/logo-tokokita.png"
                alt="Logo TokoKita"
                class="tokokita-logo-main"
            >

            <div class="auth-hero">

                <span class="hero-label">
                    MULAI TOKO ANDA
                </span>

                <h2>
                    Buat akun dan mulai kelola penjualan.
                </h2>

                <p>
                    Setiap akun memiliki data barang dan transaksi
                    masing-masing sehingga data toko tetap aman
                    dan terpisah.
                </p>

                <div class="feature-chips">

                    <span class="feature-chip">
                        ✓ Email unik
                    </span>

                    <span class="feature-chip">
                        ✓ Password terenkripsi
                    </span>

                    <span class="feature-chip">
                        ✓ Data per akun
                    </span>

                </div>

            </div>

        </div>


        <div class="auth-foot">
            © 2026 TokoKita • Web POS
        </div>

    </section>


    <section class="auth-wrap">

        <div class="auth-card">

            <img
                src="assets/images/logo-tokokita.png"
                alt="Logo TokoKita"
                class="tokokita-logo-mobile"
            >

            <div class="auth-kicker">
                Buat akun toko
            </div>

            <h1>
                Register
            </h1>

            <p class="muted">
                Lengkapi data berikut untuk membuat akun kasir.
            </p>


            <?php if ($error): ?>

                <div class="alert alert-danger">
                    <?= e($error) ?>
                </div>

            <?php endif; ?>


            <form method="post">

                <div class="grid grid-2">

                    <div class="form-group">

                        <label>
                            Nama Pengguna
                        </label>

                        <input
                            name="nama_pengguna"
                            placeholder="Nama Anda"
                            required
                            value="<?= e($_POST['nama_pengguna'] ?? '') ?>"
                        >

                    </div>


                    <div class="form-group">

                        <label>
                            Nama Toko
                        </label>

                        <input
                            name="nama_toko"
                            placeholder="Nama toko"
                            required
                            value="<?= e($_POST['nama_toko'] ?? '') ?>"
                        >

                    </div>

                </div>


                <div class="form-group">

                    <label>
                        Email
                    </label>

                    <div class="input-icon">

                        <span>
                            ✉
                        </span>

                        <input
                            type="email"
                            name="email"
                            placeholder="nama@email.com"
                            required
                            value="<?= e($_POST['email'] ?? '') ?>"
                        >

                    </div>

                </div>


                <div class="form-group">

                    <label>
                        Password
                    </label>

                    <div class="input-icon">

                        <span>
                            🔒
                        </span>

                        <input
                            type="password"
                            name="password"
                            placeholder="Minimal 6 karakter"
                            required
                            minlength="6"
                        >

                    </div>

                </div>


                <button
                    class="btn btn-primary auth-submit"
                    type="submit"
                >
                    Buat Akun →
                </button>

            </form>


            <div class="auth-divider">

                <span>
                    atau
                </span>

            </div>


            <p class="footer-note">

                Sudah punya akun?

                <a href="login.php">
                    Login di sini
                </a>

            </p>


            <div class="auth-copyright">
                © 2026 TokoKita. Semua Hak Dilindungi.
            </div>

        </div>

    </section>

</div>

</body>

</html>