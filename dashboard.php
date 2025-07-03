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

// List buku
$books = [
    [
        'id' => 1,
        'title' => 'As Long as the Lemon Trees Grow',
        'author' => 'Zoulfa Katouh',
        'genre' => 'Fiksi Historis Perang Romansa',
        'description' => 'Seorang mahasiswi farmasi yang tiba-tiba harus bekerja sebagai dokter darurat di rumah sakit yang penuh korban perang. Di tengah keputusasaan, ia dihantui oleh Khawf (ketakutan), sebuah personifikasi halusinasi yang mendorongnya untuk melarikan diri dari negaranya...',
        'cover' => 'css/cover/lemon.jpg',
        'rating' => 4.5,
        'year' => 2022,
        'pages' => 432,
        'available' => true
    ],
    [
        'id' => 2,
        'title' => 'Ronggeng Dukuh Paruk',
        'author' => 'Ahmad Tohari',
        'genre' => 'Fiksi Sastra Tragedi',
        'description' => 'Dukuh Paruk, sebuah desa terpencil di Jawa, sangat bergantung pada tradisi ronggeng. Srintil, seorang gadis muda yang memiliki bakat menari, diyakini sebagai pewaris tradisi ronggeng dan diangkat menjadi ronggeng. Kehadirannya membawa kebahagiaan dan kebanggaan bagi desa, tetapi juga membawa perubahan dalam kehidupan Srintil.',
        'cover' => 'css/cover/ronggeng.jpg',
        'rating' => 4.8,
        'year' => 1982,
        'pages' => 368,
        'available' => true
    ],
    [
        'id' => 3,
        'title' => 'Laut Bercerita',
        'author' => 'Leila S. Chudori',
        'genre' => 'Fiksi Sastra Drama',
        'description' => 'Kisah seorang aktivis mahasiswa bernama Laut yang diculik dan menghilang secara paksa pada masa Orde Baru. Novel ini menggambarkan perjuangan, pengorbanan, dan keteguhan hati dalam memperjuangkan kebenaran.',
        'cover' => 'css/cover/laut.jpg',
        'rating' => 4.7,
        'year' => 2017,
        'pages' => 394,
        'available' => false
    ],
    [
        'id' => 4,
        'title' => 'Negeri 5 Menara',
        'author' => 'Ahmad Fuadi',
        'genre' => 'Inspirasi Coming-of-age',
        'description' => 'Kisah perjuangan sekelompok santri di Pondok Madura yang memegang prinsip "Man Jadda Wa Jadda" (Siapa yang bersungguh-sungguh akan berhasil) untuk meraih mimpi mereka.',
        'cover' => 'css/cover/5menara_2.jpg',
        'rating' => 4.6,
        'year' => 2009,
        'pages' => 424,
        'available' => true
    ],
    [
        'id' => 5,
        'title' => 'Harry Potter and the Philosopher\'s Stone',
        'author' => 'J.K. Rowling',
        'genre' => 'Fantasi Petualangan',
        'description' => 'Harry Potter menemukan bahwa ia adalah penyihir dan memulai petualangannya di Sekolah Sihir Hogwarts.',
        'cover' => 'css/cover/harry.jpg',
        'rating' => 4.9,
        'year' => 1997,
        'pages' => 223,
        'available' => true
    ],
    [
        'id' => 6,
        'title' => '1984',
        'author' => 'George Orwell',
        'genre' => 'Fiksi Distopia',
        'description' => 'Dunia di bawah pengawasan ketat Big Brother di mana kebebasan individu ditekan dan sejarah dimanipulasi.',
        'cover' => 'css/cover/1984.jpeg',
        'rating' => 4.8,
        'year' => 1949,
        'pages' => 328,
        'available' => false
    ],
    [
        'id' => 7,
        'title' => 'Gadis Kretek',
        'author' => 'Ratih Kumala',
        'genre' => 'Fiksi Historis Romansa',
        'description' => 'Mengisahkan tentang Jeng Yah, seorang perempuan yang terlibat dalam industri kretek di Indonesia.',
        'cover' => 'css/cover/kretek.jpg',
        'rating' => 4.3,
        'year' => 2012,
        'pages' => 256,
        'available' => true
    ],
    [
        'id' => 8,
        'title' => 'Perahu Kertas',
        'author' => 'Dee Lestari',
        'genre' => 'Romansa Drama',
        'description' => 'Kugy dan Keenan, dua orang dengan impian berbeda, saling menemukan cinta dan arti kehidupan.',
        'cover' => 'css/cover/pkertas.jpg',
        'rating' => 4.5,
        'year' => 2009,
        'pages' => 444,
        'available' => true
    ],
    [
        'id' => 9,
        'title' => 'Bumi Manusia',
        'author' => 'Pramoedya Ananta Toer',
        'genre' => 'Fiksi Historis',
        'description' => 'Kisah Minke, pemuda Jawa yang bersekolah di HBS, berkenalan dengan Nyai Ontosoroh, perempuan pribumi yang berhasil menjadi pengusaha sukses di era kolonial.',
        'cover' => 'css/cover/bumi_manusia.jpg',
        'rating' => 4.9,
        'year' => 1980,
        'pages' => 535,
        'available' => true
    ],
    [
        'id' => 10,
        'title' => 'Laskar Pelangi',
        'author' => 'Andrea Hirata',
        'genre' => 'Drama Inspiratif',
        'description' => 'Kisah persahabatan 10 anak dari keluarga miskin di Belitung yang bersekolah di SD Muhammadiyah dengan segala keterbatasannya.',
        'cover' => 'css/cover/laskar.jpg',
        'rating' => 4.8,
        'year' => 2005,
        'pages' => 529,
        'available' => true
    ],
    [
        'id' => 11,
        'title' => 'The Hobbit',
        'author' => 'J.R.R. Tolkien',
        'genre' => 'Fantasi Petualangan',
        'description' => 'Bilbo Baggins ikut dalam perjalanan tak terduga untuk merebut kembali harta karun yang dijaga oleh naga Smaug.',
        'cover' => 'css/cover/hobbit.jpg',
        'rating' => 4.7,
        'year' => 1937,
        'pages' => 310,
        'available' => true
    ],
    [
        'id' => 12,
        'title' => 'Pangeran Kecil',
        'author' => 'Antoine de Saint-Exupéry',
        'genre' => 'Filosofi Anak-anak',
        'description' => 'Seorang pilot yang terdampar di gurun bertemu dengan pangeran kecil dari asteroid lain yang mengajarkannya tentang arti kehidupan.',
        'cover' => 'css/cover/pangeran.jpg',
        'rating' => 4.9,
        'year' => 1943,
        'pages' => 96,
        'available' => true
    ],
    [
        'id' => 13,
        'title' => 'Dilan 1990',
        'author' => 'Pidi Baiq',
        'genre' => 'Romansa Muda',
        'description' => 'Kisah cinta Milea dan Dilan di Bandung tahun 1990-an dengan segala romansa dan keunikan karakter Dilan.',
        'cover' => 'css/cover/dilan.jpg',
        'rating' => 4.4,
        'year' => 2014,
        'pages' => 332,
        'available' => true
    ],
    [
        'id' => 14,
        'title' => 'Ayat-Ayat Cinta',
        'author' => 'Habiburrahman El Shirazy',
        'genre' => 'Romansa Religius',
        'description' => 'Kisah cinta Fahri, mahasiswa Indonesia di Kairo, yang diuji dengan berbagai cobaan dan konflik.',
        'cover' => 'css/cover/ayat.jpg',
        'rating' => 4.5,
        'year' => 2004,
        'pages' => 418,
        'available' => true
    ],
    [
        'id' => 15,
        'title' => 'To Kill a Mockingbird',
        'author' => 'Harper Lee',
        'genre' => 'Drama Hukum',
        'description' => 'Kisah tentang keadilan dan rasisme di Amerika Selatan melalui mata Scout Finch yang masih kecil.',
        'cover' => 'css/cover/mockingbird.jpg',
        'rating' => 4.8,
        'year' => 1960,
        'pages' => 281,
        'available' => true
    ],
    [
        'id' => 16,
        'title' => 'Pride and Prejudice',
        'author' => 'Jane Austen',
        'genre' => 'Romansa Klasik',
        'description' => 'Kisah Elizabeth Bennet dan Fitzwilliam Darcy dalam dunia masyarakat Inggris abad ke-19 yang penuh dengan prasangka dan kelas sosial.',
        'cover' => 'css/cover/pride.jpg',
        'rating' => 4.7,
        'year' => 1813,
        'pages' => 279,
        'available' => true
    ],
    [
        'id' => 17,
        'title' => 'The Da Vinci Code',
        'author' => 'Dan Brown',
        'genre' => 'Misteri Thriller',
        'description' => 'Robert Langdon memecahkan kode rahasia yang bisa mengubah pandangan dunia tentang Yesus Kristus.',
        'cover' => 'css/cover/da vinci.jpg',
        'rating' => 4.2,
        'year' => 2003,
        'pages' => 454,
        'available' => false
    ],
    [
        'id' => 18,
        'title' => 'Hujan',
        'author' => 'Tere Liye',
        'genre' => 'Fiksi Ilmiah',
        'description' => 'Kisah Lail dan Esok di masa depan ketika hujan menjadi bencana yang mematikan dan mengubah peradaban manusia.',
        'cover' => 'css/cover/hujan.jpg',
        'rating' => 4.6,
        'year' => 2016,
        'pages' => 320,
        'available' => true
    ]
];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novel Grove - Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
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
                <a href="#" class="nav-item active">
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
                        <span>5 peminjaman hari ini</span>
                    </div>
                    <div class="activity-item">
                        <div class="activity-dot orange"></div>
                        <span>2 buku terlambat</span>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-content">
                        <div class="stat-info">
                            <p class="stat-title">Total Buku</p>
                            <p class="stat-value">2,847</p>
                        </div>
                        <div class="stat-icon blue">📚</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-content">
                        <div class="stat-info">
                            <p class="stat-title">Buku Dipinjam</p>
                            <p class="stat-value">1,234</p>
                        </div>
                        <div class="stat-icon green">📖</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-content">
                        <div class="stat-info">
                            <p class="stat-title">Anggota Aktif</p>
                            <p class="stat-value">856</p>
                        </div>
                        <div class="stat-icon purple">👥</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-content">
                        <div class="stat-info">
                            <p class="stat-title">Buku Populer</p>
                            <p class="stat-value">127</p>
                        </div>
                        <div class="stat-icon orange">📈</div>
                    </div>
                </div>
            </div>

            <!-- Search and Filters -->
            <div class="search-section">
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
                    <?= isset($_POST['judul']) && $_POST['judul'] ? 'Hasil pencarian "' . htmlspecialchars($_POST['judul']) . '"' : 'Semua Buku' ?>
                    <span class="results-count">(<?= count($books) ?> buku)</span>
                </h2>
            </div>

            <!-- Books Grid -->
            <div class="books-grid" id="books-container">
                <?php foreach ($books as $book): ?>
                    <div class="book-card">
                        <div class="book-cover-container">
                            <img src="<?= $book['cover'] ?>" alt="<?= htmlspecialchars($book['title']) ?>" class="book-cover">
                            <?php if (!$book['available']): ?>
                                <div class="unavailable-overlay">
                                    <span class="unavailable-badge">Tidak Tersedia</span>
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
                            <p class="book-description"><?= htmlspecialchars($book['description']) ?></p>
                            <div class="book-meta">
                                <span><?= $book['year'] ?></span>
                                <span><?= $book['pages'] ?> halaman</span>
                            </div>
                            <button class="borrow-btn <?= !$book['available'] ? 'disabled' : '' ?>" <?= !$book['available'] ? 'disabled' : '' ?>>
                                <?= $book['available'] ? 'Pinjam Buku' : 'Tidak Tersedia' ?>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Navigation Button -->
            <div class="navigation-section">
                <a href="antrian_peminjaman.php" class="queue-button">Lihat Antrian Peminjaman</a>
            </div>
        </main>
    </div>

    <script>
        const searchInput = document.getElementById('search-input');
        const searchHistory = document.getElementById('search-history');
        const searchArea = document.getElementById('search-area');

        <?php if ($showDropdown): ?>
            if (searchHistory) searchHistory.style.display = 'block';
            searchInput.focus();
        <?php endif; ?>

        searchInput.addEventListener('focus', () => {
            if (searchHistory) searchHistory.style.display = 'block';
        });

        document.addEventListener('click', function(e) {
            if (!searchArea.contains(e.target)) {
                if (searchHistory) searchHistory.style.display = 'none';
            }
        });

        function closeHistory() {
            if (searchHistory) searchHistory.style.display = 'none';
        }

        function selectHistory(query) {
            searchInput.value = query;
            if (searchHistory) searchHistory.style.display = 'none';
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
    </script>
</body>
</html>
