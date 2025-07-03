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

    // Check if item already exists in history
    $existingIndex = array_search($data, $_SESSION['stack']);
    if ($existingIndex !== false) {
        // Remove existing item to avoid duplicates
        pop($existingIndex);
    }

    if ($_SESSION['top'] >= $maxStack - 1) {
        // Remove the oldest item (bottom of stack)
        array_shift($_SESSION['stack']);
        $_SESSION['top']--;
    }
    
    // Add new item to top
    array_push($_SESSION['stack'], $data);
    $_SESSION['top'] = count($_SESSION['stack']) - 1;
}

function pop($index) {
    if ($index >= 0 && $index <= $_SESSION['top']) {
        array_splice($_SESSION['stack'], $index, 1);
        $_SESSION['top']--;
    }
}

$showDropdown = false;

if (isset($_POST['cari'])) {
    $judul = trim($_POST['judul']);
    if ($judul !== "") {
        push($judul);
        $showDropdown = true;
    }
}

if (isset($_POST['hapus'])) {
    $index = $_POST['index'];
    pop($index);
    $showDropdown = true;
}

$buku = json_decode(file_get_contents("buku.json"), true);

$filter = '';
if (isset($_POST['cari'])) {
    $filter = strtolower($_POST['judul']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Perpustakaan Digital</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
</head>
<body>

<div class="sidebar">
    <a href="index.php"><img src="css/home.svg" alt="Home"></a>
    <a href="#"><img src="css/user.svg" alt="User"></a>
</div>

<div class="main">
    <div id="search-area">
        <form method="POST" class="search-form">
            <input type="text" name="judul" id="search-input" class="search-bar" placeholder="Search for books..." autocomplete="off" value="<?= htmlspecialchars($filter) ?>">
            <button type="submit" name="cari" class="search-button">Search</button>
        </form>

        <?php if (!isEmpty() && $showDropdown): ?>
        <div class="riwayat-list" id="riwayat-dropdown">
            <?php for ($i = $_SESSION['top']; $i >= 0; $i--): ?>
                <div class="riwayat-item">
                    <?= htmlspecialchars($_SESSION['stack'][$i]) ?>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="index" value="<?= $i ?>">
                        <button type="submit" name="hapus" class="hapus-btn">Hapus</button>
                    </form>
                </div>
            <?php endfor; ?>
        </div>
        <?php endif; ?>
    </div>

    <div class="book-list">
        <?php foreach ($buku as $b): ?>
            <?php if ($filter === '' || strpos(strtolower($b['judul']), $filter) !== false): ?>
            <div class="book-item">
                <div class="book-cover" style="background-image: url('<?= $b['cover'] ?>'); background-size: cover;"></div>
                <div class="book-info">
                    <div class="book-title"><?= htmlspecialchars($b['judul']) ?></div>
                    <div class="book-genre"><?= htmlspecialchars($b['genre']) ?></div>
                    <div class="book-description"><?= htmlspecialchars($b['deskripsi']) ?></div>
                    <button class="read-more">See More</button>
                </div>
            </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
</div>

<script>
const searchInput = document.getElementById('search-input');
const riwayatDropdown = document.getElementById('riwayat-dropdown');
const searchArea = document.getElementById('search-area');

searchInput.addEventListener('focus', () => {
    if (riwayatDropdown) riwayatDropdown.style.display = 'block';
});

document.addEventListener('click', function(e) {
    if (riwayatDropdown) {
        if (!searchArea.contains(e.target)) {
            riwayatDropdown.style.display = 'none';
        }
    }
});

// Hide dropdown initially if not triggered by search
document.addEventListener('DOMContentLoaded', function() {
    if (riwayatDropdown) {
        riwayatDropdown.style.display = 'none';
    }
});
</script>

</body>
</html>