<?php
session_start();

// Inisialisasi Stack untuk riwayat pencarian
if (!isset($_SESSION['stack'])) {
    $_SESSION['stack'] = [];
    $_SESSION['top'] = -1;
}

// Inisialisasi Queue untuk antrian buku
if (!isset($_SESSION['book_queue'])) {
    $_SESSION['book_queue'] = [];
}

// Fungsi Stack untuk riwayat pencarian
function isEmpty() {
    return $_SESSION['top'] == -1;
}

function isFull() {
    $maxStack = 10;
    return $_SESSION['top'] == $maxStack - 1;
}

function push($data) {
    $maxStack = 10;
    // Cek duplikat, hapus dulu biar pindah ke paling atas
    $existingIndex = array_search($data, $_SESSION['stack']);
    if ($existingIndex !== false) {
        pop($existingIndex);
    }
    if (isFull()) {
        // Geser semua ke kiri
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

// Fungsi Queue untuk antrian buku
function addToQueue($bookId, $userId = 'current_user') {
    $queueItem = [
        'id' => uniqid(),
        'book_id' => $bookId,
        'user_id' => $userId,
        'timestamp' => time(),
        'status' => 'waiting'
    ];
    
    // Cek apakah buku sudah ada dalam antrian user
    foreach ($_SESSION['book_queue'] as $item) {
        if ($item['book_id'] == $bookId && $item['user_id'] == $userId) {
            return false; // Sudah ada dalam antrian
        }
    }
    
    $_SESSION['book_queue'][] = $queueItem;
    return true;
}

function getQueuePosition($bookId, $userId = 'current_user') {
    $position = 1;
    foreach ($_SESSION['book_queue'] as $item) {
        if ($item['book_id'] == $bookId) {
            if ($item['user_id'] == $userId) {
                return $position;
            }
            if ($item['status'] == 'waiting') {
                $position++;
            }
        }
    }
    return 0;
}

function getQueueCount($bookId) {
    $count = 0;
    foreach ($_SESSION['book_queue'] as $item) {
        if ($item['book_id'] == $bookId && $item['status'] == 'waiting') {
            $count++;
        }
    }
    return $count;
}

// Variabel kontrol
$showDropdown = false;
$hasilFilter = [];
$message = '';
$messageType = '';

// Load data buku
$books = json_decode(file_get_contents('../data/buku.json'), true);

// Proses form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Proses Pencarian
    if (isset($_POST['cari'])) {
        $judul = trim($_POST['judul']);
        if ($judul !== "") {
            push($judul);
            $showDropdown = true;
            foreach ($books as $buku) {
                if (stripos($buku['title'], $judul) !== false) {
                    $hasilFilter[] = $buku;
                }
            }
        }
    }
    
    // Hapus Riwayat Per Item
    if (isset($_POST['hapus'])) {
        $index = $_POST['index'];
        pop($index);
        $showDropdown = true;
    }
    
    // Tambah ke Antrian
    if (isset($_POST['add_to_queue'])) {
        $bookId = $_POST['book_id'];
        if (addToQueue($bookId)) {
            $message = 'Buku berhasil ditambahkan ke antrian!';
            $messageType = 'success';
        } else {
            $message = 'Buku sudah ada dalam antrian Anda!';
            $messageType = 'error';
        }
    }
}

// Statistik untuk dashboard
$totalBooks = count($books);
$availableBooks = count(array_filter($books, function($book) { return $book['available']; }));
$unavailableBooks = $totalBooks - $availableBooks;
$totalQueue = count($_SESSION['book_queue']);
$userQueue = count(array_filter($_SESSION['book_queue'], function($item) { 
    return $item['user_id'] == 'current_user'; 
}));
$readyQueue = count(array_filter($_SESSION['book_queue'], function($item) { 
    return $item['status'] == 'ready'; 
}));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novel Grove - Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-content">
            <div class="header-left">
                <div class="logo">
                    <div class="logo-icon">📚</div>
                    <h1>Novel Grove</h1>
                </div>
            </div>
            <div class="header-right">
                <?php if ($userQueue > 0): ?>
                    <div class="queue-notification">
                        <span class="queue-badge"><?= $userQueue ?></span>
                        <span class="queue-text">Antrian</span>
                    </div>
                <?php endif; ?>
                <button class="notification-btn">🔔</button>
                <div class="user-menu">
                    <div class="user-avatar">LS</div>
                    <span class="user-name">Luhtitisari</span>
                    <span class="dropdown-arrow">▼</span>
                </div>
            </div>
        </div>
    </header>

    <div class="layout">
        <!-- Sidebar -->
        <aside class="sidebar">
            <nav class="sidebar-nav">
                <a href="dashboard.php" class="nav-item active">
                    <span class="nav-icon">🏠</span>
                    <span class="nav-text">Dashboard</span>
                </a>
                <a href="#" class="nav-item">
                    <span class="nav-icon">📚</span>
                    <span class="nav-text">Katalog Buku</span>
                </a>
                <a href="#" class="nav-item">
                    <span class="nav-icon">📖</span>
                    <span class="nav-text">Peminjaman</span>
                </a>
                <a href="antrian.php" class="nav-item">
                    <span class="nav-icon">⏳</span>
                    <span class="nav-text">Antrian</span>
                    <?php if ($userQueue > 0): ?>
                        <span class="nav-badge"><?= $userQueue ?></span>
                    <?php endif; ?>
                </a>
                <a href="#" class="nav-item">
                    <span class="nav-icon">👥</span>
                    <span class="nav-text">Anggota</span>
                </a>
                <a href="#" class="nav-item">
                    <span class="nav-icon">📊</span>
                    <span class="nav-text">Laporan</span>
                </a>
            </nav>
            <div class="recent-activity">
                <h3>Aktivitas Terbaru</h3>
                <div class="activity-list">
                    <div class="activity-item">
                        <div class="activity-dot green"></div>
                        <span>Buku baru ditambahkan</span>
                    </div>
                    <div class="activity-item">
                        <div class="activity-dot blue"></div>
                        <span><?= $availableBooks ?> buku tersedia</span>
                    </div>
                    <div class="activity-item">
                        <div class="activity-dot orange"></div>
                        <span><?= $totalQueue ?> antrian aktif</span>
                    </div>
                    <?php if ($readyQueue > 0): ?>
                    <div class="activity-item">
                        <div class="activity-dot purple"></div>
                        <span><?= $readyQueue ?> buku siap dipinjam</span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Message Alert -->
            <?php if ($message): ?>
                <div class="alert alert-<?= $messageType ?>">
                    <span class="alert-icon"><?= $messageType == 'success' ? '✅' : '❌' ?></span>
                    <?= htmlspecialchars($message) ?>
                    <button class="alert-close" onclick="this.parentElement.remove()">✕</button>
                </div>
            <?php endif; ?>

            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-content">
                        <div class="stat-info">
                            <p class="stat-title">Total Buku</p>
                            <p class="stat-value"><?= $totalBooks ?></p>
                        </div>
                        <div class="stat-icon blue">📚</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-content">
                        <div class="stat-info">
                            <p class="stat-title">Buku Tersedia</p>
                            <p class="stat-value"><?= $availableBooks ?></p>
                        </div>
                        <div class="stat-icon green">📖</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-content">
                        <div class="stat-info">
                            <p class="stat-title">Antrian Saya</p>
                            <p class="stat-value"><?= $userQueue ?></p>
                        </div>
                        <div class="stat-icon purple">⏳</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-content">
                        <div class="stat-info">
                            <p class="stat-title">Siap Dipinjam</p>
                            <p class="stat-value"><?= $readyQueue ?></p>
                        </div>
                        <div class="stat-icon orange">✅</div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="quick-actions">
                <h2>Aksi Cepat</h2>
                <div class="action-buttons">
                    <a href="antrian.php" class="action-btn primary">
                        <span class="action-icon">⏳</span>
                        <div class="action-content">
                            <h3>Lihat Antrian</h3>
                            <p><?= $userQueue ?> buku dalam antrian</p>
                        </div>
                    </a>
                    <a href="#search-section" class="action-btn secondary">
                        <span class="action-icon">🔍</span>
                        <div class="action-content">
                            <h3>Cari Buku</h3>
                            <p>Temukan buku favorit Anda</p>
                        </div>
                    </a>
                    <a href="#" class="action-btn tertiary">
                        <span class="action-icon">📊</span>
                        <div class="action-content">
                            <h3>Laporan</h3>
                            <p>Lihat statistik peminjaman</p>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Search and Filters -->
            <div class="search-section" id="search-section">
                <div class="search-container">
                    <form method="POST" class="search-form" id="search-area">
                        <div class="search-input-container">
                            <span class="search-icon">🔍</span>
                            <input 
                                type="text"
                                name="judul"
                                id="search-input"
                                class="search-input"
                                placeholder="Cari buku, penulis, atau genre..."
                                autocomplete="off"
                                value="<?= isset($_POST['judul']) ? htmlspecialchars($_POST['judul']) : '' ?>"
                            >
                        </div>
                        <button type="submit" name="cari" class="search-btn">Cari</button>
                    </form>
                    
                    <!-- Search History Dropdown -->
                    <?php if (!isEmpty()): ?>
                        <div class="search-history" id="search-history" style="<?= $showDropdown ? 'display: block;' : 'display: none;' ?>">
                            <div class="history-header">
                                <span>Riwayat Pencarian</span>
                                <button type="button" class="close-history" onclick="closeHistory()">✕</button>
                            </div>
                            <?php for ($i = $_SESSION['top']; $i >= 0; $i--): ?>
                                <div class="history-item">
                                    <button type="button" class="history-query" onclick="selectHistory('<?= htmlspecialchars($_SESSION['stack'][$i]) ?>')">
                                        <span class="history-icon">🕒</span>
                                        <?= htmlspecialchars($_SESSION['stack'][$i]) ?>
                                    </button>
                                    <form method="POST" style="margin: 0;">
                                        <input type="hidden" name="index" value="<?= $i ?>">
                                        <button type="submit" name="hapus" class="delete-history">✕</button>
                                    </form>
                                </div>
                            <?php endfor; ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="filters">
                        <select class="filter-select">
                            <option value="all">Semua Genre</option>
                            <option value="fiksi">Fiksi</option>
                            <option value="sastra">Sastra</option>
                            <option value="romansa">Romansa</option>
                            <option value="fantasi">Fantasi</option>
                        </select>
                        <select class="filter-select">
                            <option value="title">Urutkan: Judul</option>
                            <option value="author">Urutkan: Penulis</option>
                            <option value="year">Urutkan: Tahun</option>
                            <option value="rating">Urutkan: Rating</option>
                        </select>
                        <div class="view-toggle">
                            <button class="view-btn active" data-view="grid">⊞</button>
                            <button class="view-btn" data-view="list">☰</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Results Header -->
            <div class="results-header">
                <h2>
                    <?php if (isset($_POST['judul']) && $_POST['judul']): ?>
                        Hasil pencarian "<?= htmlspecialchars($_POST['judul']) ?>"
                        <span class="results-count">(<?= count($hasilFilter) ?> buku)</span>
                    <?php else: ?>
                        Katalog Buku
                        <span class="results-count">(<?= count($books) ?> buku)</span>
                    <?php endif; ?>
                </h2>
            </div>

            <!-- Books Grid -->
            <?php if (!empty($hasilFilter)): ?>
                <!-- Hasil Pencarian -->
                <div class="books-grid" id="books-container">
                    <?php foreach ($hasilFilter as $book): ?>
                        <div class="book-card">
                            <div class="book-cover-container">
                                <img src="<?= $book['cover'] ?>" alt="<?= htmlspecialchars($book['title']) ?>" class="book-cover">
                                <?php if (!$book['available']): ?>
                                    <div class="unavailable-overlay">
                                        <span class="unavailable-badge">Menunggu Tersedia</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="book-info">
                                <div class="book-header">
                                    <h3 class="book-title"><?= htmlspecialchars($book['title']) ?></h3>
                                    <div class="book-rating">
                                        <span class="star">⭐</span>
                                        <span class="rating-value"><?= $book['rating'] ?></span>
                                    </div>
                                </div>
                                <p class="book-author"><?= htmlspecialchars($book['author']) ?></p>
                                <span class="book-genre"><?= htmlspecialchars($book['genre']) ?></span>
                                <p class="book-description"><?= htmlspecialchars(substr($book['description'], 0, 100)) ?>...</p>
                                <div class="book-meta">
                                    <span><?= $book['year'] ?></span>
                                    <span><?= $book['pages'] ?> halaman</span>
                                </div>
                                
                                <?php if (!$book['available']): ?>
                                    <?php 
                                    $queueCount = getQueueCount($book['id']);
                                    $userPosition = getQueuePosition($book['id']);
                                    ?>
                                    <div class="queue-info">
                                        <p class="queue-count">
                                            <span class="queue-icon">👥</span>
                                            <?= $queueCount ?> orang dalam antrian
                                        </p>
                                        <?php if ($userPosition > 0): ?>
                                            <p class="user-position">Posisi Anda: #<?= $userPosition ?></p>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="book-actions">
                                    <?php if ($book['available']): ?>
                                        <button class="borrow-btn available">
                                            📖 Pinjam Buku
                                        </button>
                                    <?php else: ?>
                                        <?php if (getQueuePosition($book['id']) == 0): ?>
                                            <form method="POST" style="display: inline; width: 100%;">
                                                <input type="hidden" name="book_id" value="<?= $book['id'] ?>">
                                                <button type="submit" name="add_to_queue" class="borrow-btn queue">
                                                    ➕ Tambah ke Antrian
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <button class="borrow-btn queued" disabled>
                                                ✅ Sudah dalam Antrian
                                            </button>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <!-- Semua Buku -->
                <div class="books-grid" id="books-container">
                    <?php foreach ($books as $book): ?>
                        <div class="book-card">
                            <div class="book-cover-container">
                                <img src="<?= $book['cover'] ?>" alt="<?= htmlspecialchars($book['title']) ?>" class="book-cover">
                                <?php if (!$book['available']): ?>
                                    <div class="unavailable-overlay">
                                        <span class="unavailable-badge">Menunggu Tersedia</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="book-info">
                                <div class="book-header">
                                    <h3 class="book-title"><?= htmlspecialchars($book['title']) ?></h3>
                                    <div class="book-rating">
                                        <span class="star">⭐</span>
                                        <span class="rating-value"><?= $book['rating'] ?></span>
                                    </div>
                                </div>
                                <p class="book-author"><?= htmlspecialchars($book['author']) ?></p>
                                <span class="book-genre"><?= htmlspecialchars($book['genre']) ?></span>
                                <p class="book-description"><?= htmlspecialchars(substr($book['description'], 0, 100)) ?>...</p>
                                <div class="book-meta">
                                    <span><?= $book['year'] ?></span>
                                    <span><?= $book['pages'] ?> halaman</span>
                                </div>
                                
                                <?php if (!$book['available']): ?>
                                    <?php 
                                    $queueCount = getQueueCount($book['id']);
                                    $userPosition = getQueuePosition($book['id']);
                                    ?>
                                    <div class="queue-info">
                                        <p class="queue-count">
                                            <span class="queue-icon">👥</span>
                                            <?= $queueCount ?> orang dalam antrian
                                        </p>
                                        <?php if ($userPosition > 0): ?>
                                            <p class="user-position">Posisi Anda: #<?= $userPosition ?></p>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="book-actions">
                                    <?php if ($book['available']): ?>
                                        <button class="borrow-btn available">
                                            📖 Pinjam Buku
                                        </button>
                                    <?php else: ?>
                                        <?php if (getQueuePosition($book['id']) == 0): ?>
                                            <form method="POST" style="display: inline; width: 100%;">
                                                <input type="hidden" name="book_id" value="<?= $book['id'] ?>">
                                                <button type="submit" name="add_to_queue" class="borrow-btn queue">
                                                    ➕ Tambah ke Antrian
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <button class="borrow-btn queued" disabled>
                                                ✅ Sudah dalam Antrian
                                            </button>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </main>
    </div>

    <script>
        const searchInput = document.getElementById('search-input');
        const riwayatDropdown = document.getElementById('search-history');
        const searchArea = document.getElementById('search-area');

        // Pastikan dropdown tersembunyi saat halaman dimuat
        if (riwayatDropdown) {
            riwayatDropdown.style.display = 'none';
        }

        // Tampilkan dropdown ketika input difokus
        searchInput.addEventListener('focus', () => {
            if (riwayatDropdown) {
                riwayatDropdown.style.display = 'block';
            }
        });

        // Sembunyikan dropdown ketika klik di luar area search
        document.addEventListener('click', function(e) {
            if (!searchArea.contains(e.target)) {
                if (riwayatDropdown) {
                    riwayatDropdown.style.display = 'none';
                }
            }
        });

        // Function untuk memilih history
        function selectHistory(query) {
            searchInput.value = query;
            document.querySelector('form[method="POST"]').submit();
        }

        // Function untuk menutup history
        function closeHistory() {
            if (riwayatDropdown) {
                riwayatDropdown.style.display = 'none';
            }
        }

        // View toggle functionality
        const viewButtons = document.querySelectorAll('.view-btn');
        const booksContainer = document.getElementById('books-container');
        
        viewButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                viewButtons.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                
                const view = btn.dataset.view;
                if (view === 'list') {
                    booksContainer.classList.add('list-view');
                } else {
                    booksContainer.classList.remove('list-view');
                }
            });
        });

        // Auto hide alert after 5 seconds
        const alert = document.querySelector('.alert');
        if (alert) {
            setTimeout(() => {
                alert.style.opacity = '0';
                setTimeout(() => {
                    alert.remove();
                }, 300);
            }, 5000);
        }

        // Smooth scroll untuk quick actions
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>
</html>
