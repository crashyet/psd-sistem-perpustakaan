<?php
session_start();

$maxStack = 5;

if (!isset($_SESSION['stack'])) {
    $_SESSION['stack'] = [];
    $_SESSION['top'] = -1;
}

function isEmpty() {
    return $_SESSION['top'] == -1;
}

function push($data) {
    global $maxStack;

    if ($_SESSION['top'] == $maxStack - 1) {
        // Geser semua data ke kiri, data paling lama dibuang
        for ($i = 0; $i < $_SESSION['top']; $i++) {
            $_SESSION['stack'][$i] = $_SESSION['stack'][$i + 1];
        }
        $_SESSION['stack'][$_SESSION['top']] = $data;
    } else {
        $_SESSION['top']++;
        $_SESSION['stack'][$_SESSION['top']] = $data;
    }
}

function pop($index) {
    for ($i = $index; $i < $_SESSION['top']; $i++) {
        $_SESSION['stack'][$i] = $_SESSION['stack'][$i + 1];
    }
    unset($_SESSION['stack'][$_SESSION['top']]);
    $_SESSION['top']--;
}

$showDropdown = false;

if (isset($_POST['cari'])) {
    $judul = trim($_POST['judul']);
    if ($judul !== "") {
        push($judul);
    }
}

if (isset($_POST['hapus'])) {
    $index = $_POST['index'];
    pop($index);
    $showDropdown = true;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Undo Pencarian - Stack Geser Otomatis</title>
    <style>
        .search-container {
            position: relative;
            width: 300px;
            margin: 50px auto;
        }
        .search-box {
            display: flex;
        }
        .riwayat-list {
            position: absolute;
            top: 40px;
            width: 100%;
            border: 1px solid #ddd;
            background: #fff;
            max-height: 150px;
            overflow-y: auto;
            z-index: 999;
            display: none;
        }
        .riwayat-item {
            padding: 8px 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .riwayat-item:hover {
            background: #f0f0f0;
        }
        .hapus-btn {
            background: none;
            border: none;
            color: red;
            cursor: pointer;
            font-size: 12px;
        }
    </style>
</head>
<body>

<div class="search-container" id="search-area">
    <form method="post" class="search-box">
        <input type="text" name="judul" id="search-input" placeholder="Cari buku..." autocomplete="off" style="flex: 1; padding: 8px;">
        <button type="submit" name="cari">Cari</button>
    </form>

    <?php if (!isEmpty()): ?>
        <div class="riwayat-list" id="riwayat-dropdown">
            <?php for ($i = $_SESSION['top']; $i >= 0; $i--): ?>
                <div class="riwayat-item">
                    <?= htmlspecialchars($_SESSION['stack'][$i]) ?>
                    <form method="post" style="margin: 0;">
                        <input type="hidden" name="index" value="<?= $i ?>">
                        <button type="submit" name="hapus" class="hapus-btn">Hapus</button>
                    </form>
                </div>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</div>

<script>
    const searchInput = document.getElementById('search-input');
    const riwayatDropdown = document.getElementById('riwayat-dropdown');
    const searchArea = document.getElementById('search-area');

    <?php if ($showDropdown): ?>
        if (riwayatDropdown) riwayatDropdown.style.display = 'block';
        searchInput.focus();
    <?php endif; ?>

    searchInput.addEventListener('focus', () => {
        if (riwayatDropdown) riwayatDropdown.style.display = 'block';
    });

    document.addEventListener('click', function(e) {
        if (!searchArea.contains(e.target)) {
            if (riwayatDropdown) riwayatDropdown.style.display = 'none';
        }
    });
</script>

</body>
</html>
