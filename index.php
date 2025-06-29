<?php
session_start();

if (!isset($_SESSION['stack'])) {
    $_SESSION['stack'] = [];
    $_SESSION['top'] = -1;
}

function isEmpty() {
    return $_SESSION['top'] == -1;
}

function isFull() {
    $maxStack = 10;
    return $_SESSION['top'] == $maxStack - 1;
}

function push($data) {
    if (isFull()) {
        echo "<p style='color:red;'>Stack penuh.</p>";
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
        $showDropdown = true;
    }
}

if (isset($_POST['hapus'])) {
    $index = $_POST['index'];
    pop($index);
    $showDropdown = true;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Perpustakaan Digital</title>
  <link rel="stylesheet" href="css/style.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
</head>

<body>
  <div class="sidebar">
    <a href="#"><img src="css/home.svg" alt="Home"></a>
    <a href="member.html"><img src="css/user.svg" alt="User"></a>
    <!-- Rencananya ini tombol user buat info anggota kelompok -->
  </div>
  <div class="main">
    <div id="search-area">
      <form method="POST" class="search-form">
        <input type="text" name="judul" id="search-input" class="search-bar" placeholder="Search for books...">
        <button type="submit" name="cari" class="search-button">Search</button>
      </form>

      <?php if (!isEmpty()): ?>
        <div class="riwayat-list" id="riwayat-dropdown">
          <?php for ($i = $_SESSION['top']; $i >= 0; $i--): ?>
            <div class="riwayat-item">
              <?= htmlspecialchars($_SESSION['stack'][$i]) ?>
              <form method="POST" style="margin: 0;">
                <input type="hidden" name="index" value="<?= $i ?>">
                <button type="submit" name="hapus" class="hapus-btn">Hapus</button>
              </form>
            </div>
          <?php endfor; ?>
        </div>
      <?php endif; ?>
    </div>
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
