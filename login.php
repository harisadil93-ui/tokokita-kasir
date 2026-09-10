<?php

require_once 'config.php';

if (!empty($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = strtolower(
        trim($_POST['email'] ?? '')
    );

    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare(
        'SELECT * FROM users WHERE email = ? LIMIT 1'
    );

    $stmt->execute([
        $email
    ]);

    $user = $stmt->fetch();

    if (
        $user &&
        password_verify($password, $user['password'])
    ) {

        session_regenerate_id(true);

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['nama_pengguna'] = $user['nama_pengguna'];
        $_SESSION['nama_toko'] = $user['nama_toko'];

        header('Location: index.php');
        exit;
    }

    $error = 'Email atau password salah.';
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
        Login - TokoKita
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
                    SISTEM KASIR MODERN
                </span>

                <h2>
                    Kelola toko lebih mudah dalam satu tempat.
                </h2>

                <p>
                    Atur barang, catat transaksi, hitung kembalian,
                    dan lihat riwayat penjualan dengan tampilan
                    yang simpel dan rapi.
                </p>

                <div class="feature-chips">

                    <span class="feature-chip">
                        ✓ Mudah digunakan
                    </span>

                    <span class="feature-chip">
                        ✓ Aman & rapi
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
                Selamat datang
            </div>

            <h1>
                Login Kasir
            </h1>

            <p class="muted">
                Masuk menggunakan email dan password akun toko Anda.
            </p>


            <?php if (isset($_GET['registered'])): ?>

                <div class="alert alert-success">
                    Registrasi berhasil. Silakan login.
                </div>

            <?php endif; ?>


            <?php if ($error): ?>

                <div class="alert alert-danger">
                    <?= e($error) ?>
                </div>

            <?php endif; ?>


            <form method="post">

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
                            placeholder="Masukkan password"
                            required
                        >

                    </div>

                </div>


                <button
                    class="btn btn-primary auth-submit"
                    type="submit"
                >
                    Masuk ke Dashboard →
                </button>

            </form>


            <div class="auth-divider">

                <span>
                    atau
                </span>

            </div>


            <p class="footer-note">

                Belum punya akun?

                <a href="register.php">
                    Buat akun baru
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