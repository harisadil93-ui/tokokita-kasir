<?php

require_once 'config.php';

require_login();

$currentPage = basename($_SERVER['PHP_SELF']);

function nav_active($files)
{
    global $currentPage;

    return in_array(
        $currentPage,
        (array) $files,
        true
    ) ? 'active' : '';
}

?>

<!doctype html>

<html lang="id">

<head>

    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <meta
        name="theme-color"
        content="#111827"
    >

    <title>
        <?= isset($title) ? e($title) . ' - ' : '' ?>Kasir Toko
    </title>

    <link
        rel="stylesheet"
        href="assets/css/style.css"
    >

</head>

<body>

<div class="app-shell">

    <aside class="sidebar">

        <div class="sidebar-brand">

            <div class="brand-mark">
                K
            </div>

            <div class="brand-copy">

                <strong>
                    <?= e($_SESSION['nama_toko'] ?? 'Kasir Toko') ?>
                </strong>

                <span>
                    Point of Sale
                </span>

            </div>

        </div>


        <nav class="sidebar-nav">

            <a
                class="<?= nav_active('index.php') ?>"
                href="index.php"
            >
                <span class="nav-icon">
                    ⌂
                </span>

                <span>
                    Dashboard
                </span>
            </a>


            <a
                class="<?= nav_active([
                    'barang.php',
                    'barang_tambah.php',
                    'barang_edit.php'
                ]) ?>"
                href="barang.php"
            >
                <span class="nav-icon">
                    ▣
                </span>

                <span>
                    Barang
                </span>
            </a>


            <a
                class="<?= nav_active('penjualan.php') ?>"
                href="penjualan.php"
            >
                <span class="nav-icon">
                    🛒
                </span>

                <span>
                    Penjualan
                </span>
            </a>


            <a
                class="<?= nav_active('riwayat.php') ?>"
                href="riwayat.php"
            >
                <span class="nav-icon">
                    ↺
                </span>

                <span>
                    Riwayat
                </span>
            </a>


            <a
                class="logout-link"
                href="logout.php"
            >
                <span class="nav-icon">
                    ↪
                </span>

                <span>
                    Logout
                </span>
            </a>

        </nav>


        <div class="sidebar-bottom">

            <div class="user-mini">

                <strong>
                    <?= e($_SESSION['nama_pengguna'] ?? 'Pengguna') ?>
                </strong>

                <span>
                    Kasir aktif
                </span>

            </div>

        </div>

    </aside>


    <section class="main-area">

        <header class="topbar">

            <div>

                <div class="topbar-title">
                    <?= e($title ?? 'Kasir Toko') ?>
                </div>

                <div class="mobile-brand">
                    <?= e($_SESSION['nama_toko'] ?? 'Kasir Toko') ?>
                </div>

            </div>


            <div class="topbar-right">

                <div class="topbar-pill">
                    ● Sistem aktif
                </div>

            </div>

        </header>


        <main class="page">

            <div class="container"></div>