<?php

require_once 'config.php';

require_login();

$id = (int) ($_GET['id'] ?? 0);

$stmt = $pdo->prepare(
    'SELECT foto FROM barang WHERE id=? AND user_id=?'
);

$stmt->execute([
    $id,
    $_SESSION['user_id']
]);

$item = $stmt->fetch();

if ($item) {

    $stmt = $pdo->prepare(
        'DELETE FROM barang WHERE id=? AND user_id=?'
    );

    $stmt->execute([
        $id,
        $_SESSION['user_id']
    ]);

    if (
        $item['foto'] &&
        is_file(__DIR__ . '/uploads/' . $item['foto'])
    ) {
        @unlink(
            __DIR__ . '/uploads/' . $item['foto']
        );
    }
}

header('Location: barang.php?ok=1');

exit;