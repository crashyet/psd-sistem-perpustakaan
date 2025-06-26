<?php
session_start(); // Aktifkan session untuk menyimpan riwayat pencarian

// Inisialisasi riwayat pencarian jika belum ada
if (!isset($_SESSION['riwayat'])) {
    $_SESSION['riwayat'] = [];
}

// Tangani form pencarian
if (isset($_POST['cari'])) {
    $judul = trim($_POST['judul']);
    if (!empty($judul)) {
        $_SESSION['riwayat'][] = $judul; // Tambahkan ke riwayat
    }
}

// Tangani tombol undo
if (isset($_POST['undo'])) {
    array_pop($_SESSION['riwayat']); // Hapus pencarian terakhir
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Undo Pencarian Buku</title>
</head>
<body>
    <h2>Pencarian Buku</h2>

    <form method="post">
        <input type="text" name="judul" placeholder="Masukkan judul buku..." required>
        <button type="submit" name="cari">Cari</button>
        <button type="submit" name="undo">Undo Pencarian</button>
    </form>

    <h3>Riwayat Pencarian:</h3>
    <ul>
        <?php foreach ($_SESSION['riwayat'] as $item): ?>
            <li><?= htmlspecialchars($item) ?></li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
