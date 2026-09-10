<?php

require_once 'config.php';

require_login();

$title = 'Penjualan';
$error = '';
$success = '';
$userId = (int) $_SESSION['user_id'];

$stmt = $pdo->prepare(
    'SELECT id,nama_barang,harga_jual
     FROM barang
     WHERE user_id=?
     ORDER BY nama_barang'
);

$stmt->execute([
    $userId
]);

$barang = $stmt->fetchAll();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $ids = $_POST['barang_id'] ?? [];
    $qtys = $_POST['qty'] ?? [];
    $uang = (float) ($_POST['uang_bayar'] ?? 0);

    $cart = [];
    $total = 0;

    foreach ($ids as $i => $bid) {

        $bid = (int) $bid;
        $qty = (int) ($qtys[$i] ?? 0);

        if ($bid <= 0 || $qty <= 0) {
            continue;
        }

        $st = $pdo->prepare(
            'SELECT id,nama_barang,harga_jual
             FROM barang
             WHERE id=? AND user_id=?'
        );

        $st->execute([
            $bid,
            $userId
        ]);

        $b = $st->fetch();

        if ($b) {

            $sub = (float) $b['harga_jual'] * $qty;

            $total += $sub;

            $cart[] = [
                'id' => $b['id'],
                'nama' => $b['nama_barang'],
                'harga' => (float) $b['harga_jual'],
                'qty' => $qty,
                'subtotal' => $sub
            ];
        }
    }


    if (!$cart) {

        $error =
            'Pilih minimal satu barang dengan kuantitas lebih dari 0.';

    } elseif ($uang < $total) {

        $error =
            'Uang pelanggan kurang. Total ' .
            rupiah($total) .
            '.';

    } else {

        try {

            $pdo->beginTransaction();

            $kembalian = $uang - $total;

            $st = $pdo->prepare(
                'INSERT INTO penjualan(
                    user_id,
                    total,
                    uang_bayar,
                    kembalian
                ) VALUES(?,?,?,?)'
            );

            $st->execute([
                $userId,
                $total,
                $uang,
                $kembalian
            ]);

            $pid = (int) $pdo->lastInsertId();

            $d = $pdo->prepare(
                'INSERT INTO penjualan_detail(
                    penjualan_id,
                    barang_id,
                    nama_barang,
                    harga_jual,
                    qty,
                    subtotal
                ) VALUES(?,?,?,?,?,?)'
            );

            foreach ($cart as $c) {

                $d->execute([
                    $pid,
                    $c['id'],
                    $c['nama'],
                    $c['harga'],
                    $c['qty'],
                    $c['subtotal']
                ]);
            }

            $pdo->commit();

            header(
                'Location: penjualan.php?success=1&kembalian=' .
                urlencode((string) $kembalian)
            );

            exit;

        } catch (Throwable $e) {

            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            $error = 'Transaksi gagal disimpan.';
        }
    }
}


include 'header.php';

?>

<div class="page-title">

    <div>

        <div class="eyebrow">
            Point of sale
        </div>

        <h1>
            Penjualan
        </h1>

        <p class="muted">
            Pilih satu atau lebih barang,
            tentukan jumlah, lalu masukkan uang pelanggan.
        </p>

    </div>

</div>


<?php if (isset($_GET['success'])): ?>

    <div class="alert alert-success">

        Transaksi berhasil. Kembalian:

        <strong>
            <?= rupiah((float) ($_GET['kembalian'] ?? 0)) ?>
        </strong>

    </div>

<?php endif; ?>


<?php if ($error): ?>

    <div class="alert alert-danger">
        <?= e($error) ?>
    </div>

<?php endif; ?>


<?php if (!$barang): ?>

    <div class="card">

        Belum ada barang.

        <a
            href="barang_tambah.php"
            style="color:#5b5cf0;font-weight:800"
        >
            Tambah barang dulu
        </a>.

    </div>

<?php else: ?>

    <form
        method="post"
        id="saleForm"
    >

        <div class="sale-layout">

            <div class="card sale-main">

                <h2>
                    Item Penjualan
                </h2>

                <div id="items"></div>

                <button
                    type="button"
                    class="btn btn-outline"
                    id="addItem"
                >
                    ＋ Tambah Item
                </button>

            </div>


            <div class="card checkout-card">

                <div class="eyebrow">
                    Pembayaran
                </div>

                <h2>
                    Ringkasan
                </h2>

                <div class="summary-box">

                    <div class="summary-row total">

                        <span>
                            Total
                        </span>

                        <span id="grandTotal">
                            Rp 0
                        </span>

                    </div>


                    <div class="pay-highlight">

                        <div
                            class="form-group"
                            style="margin:0"
                        >

                            <label>
                                Uang Diberikan Pelanggan
                            </label>

                            <input
                                type="number"
                                min="0"
                                step="1"
                                name="uang_bayar"
                                id="uangBayar"
                                placeholder="0"
                                required
                            >

                        </div>

                    </div>


                    <div class="summary-row">

                        <span>
                            Kembalian
                        </span>

                        <strong id="kembalian">
                            Rp 0
                        </strong>

                    </div>


                    <button
                        class="btn btn-success btn-block"
                        style="margin-top:16px"
                    >
                        ✓ Simpan Transaksi
                    </button>

                </div>

            </div>

        </div>

    </form>


    <script>

        const products =
            <?= json_encode($barang, JSON_UNESCAPED_UNICODE) ?>;

        const items =
            document.getElementById('items');


        function fmt(n) {

            return 'Rp ' +
                Number(n).toLocaleString('id-ID');
        }


        function row() {

            const div =
                document.createElement('div');

            div.className = 'sale-item';

            let opts =
                '<option value="">Pilih barang</option>' +
                products.map(
                    p =>
                        `<option
                            value="${p.id}"
                            data-price="${p.harga_jual}"
                        >
                            ${p.nama_barang} - ${fmt(p.harga_jual)}
                        </option>`
                ).join('');


            div.innerHTML = `
                <div>

                    <label>
                        Barang
                    </label>

                    <select
                        name="barang_id[]"
                        class="product"
                        required
                    >
                        ${opts}
                    </select>

                </div>

                <div>

                    <label>
                        Qty
                    </label>

                    <input
                        type="number"
                        name="qty[]"
                        class="qty"
                        min="1"
                        value="1"
                        required
                    >

                </div>

                <div class="price">

                    <label>
                        Subtotal
                    </label>

                    <input
                        class="subtotal"
                        value="Rp 0"
                        readonly
                    >

                </div>

                <button
                    type="button"
                    class="btn btn-danger remove"
                >
                    ×
                </button>
            `;


            items.appendChild(div);


            div.querySelectorAll('select,input').forEach(
                x => x.addEventListener('input', calc)
            );


            div.querySelector('.remove').onclick = () => {

                div.remove();

                calc();
            };


            calc();
        }


        function calc() {

            let total = 0;

            document
                .querySelectorAll('.sale-item')
                .forEach(r => {

                    const s =
                        r.querySelector('.product');

                    const q =
                        Number(
                            r.querySelector('.qty').value || 0
                        );

                    const p =
                        Number(
                            s.options[s.selectedIndex]
                                ?.dataset.price || 0
                        );

                    const sub = p * q;

                    r.querySelector('.subtotal').value =
                        fmt(sub);

                    total += sub;
                });


            document.getElementById(
                'grandTotal'
            ).textContent = fmt(total);


            const bayar =
                Number(
                    document.getElementById(
                        'uangBayar'
                    ).value || 0
                );


            document.getElementById(
                'kembalian'
            ).textContent =
                fmt(
                    Math.max(
                        0,
                        bayar - total
                    )
                );
        }


        document.getElementById(
            'addItem'
        ).onclick = row;


        document.getElementById(
            'uangBayar'
        ).addEventListener(
            'input',
            calc
        );


        row();

    </script>

<?php endif; ?>


<?php include 'footer.php'; ?>