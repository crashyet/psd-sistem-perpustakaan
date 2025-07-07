<?php
session_start();

// Class Queue untuk mengelola antrian
class BookQueue {
    private $queue;
    private $maxSize;
    
    public function __construct($maxSize = 100) {
        $this->maxSize = $maxSize;
        if (!isset($_SESSION['book_queue_data'])) {
            $_SESSION['book_queue_data'] = [];
        }
        $this->queue = &$_SESSION['book_queue_data'];
    }
    
    // Fungsi enqueue - menambah item ke antrian
    public function enqueue($bookId, $userId = 'current_user') {
        if ($this->isFull()) {
            return false;
        }
        
        $queueItem = [
            'id' => uniqid(),
            'book_id' => $bookId,
            'user_id' => $userId,
            'timestamp' => time(),
            'status' => 'waiting'
        ];
        
        // Cek apakah buku sudah ada dalam antrian user
        foreach ($this->queue as $item) {
            if ($item['book_id'] == $bookId && $item['user_id'] == $userId) {
                return false; // Sudah ada dalam antrian
            }
        }
        
        $this->queue[] = $queueItem;
        return true;
    }
    
    // Fungsi dequeue - mengeluarkan item pertama dari antrian
    public function dequeue() {
        if ($this->isEmpty()) {
            return false;
        }
        
        $item = array_shift($this->queue);
        return $item;
    }
    
    // Fungsi dequeue berdasarkan ID
    public function dequeueById($queueId) {
        foreach ($this->queue as $key => $item) {
            if ($item['id'] == $queueId) {
                unset($this->queue[$key]);
                $this->queue = array_values($this->queue); // Reindex array
                return $item;
            }
        }
        return false;
    }
    
    // Fungsi isEmpty - mengecek apakah antrian kosong
    public function isEmpty() {
        return count($this->queue) == 0;
    }
    
    // Fungsi isFull - mengecek apakah antrian penuh
    public function isFull() {
        return count($this->queue) >= $this->maxSize;
    }
    
    // Fungsi clear - mengosongkan antrian
    public function clear() {
        $this->queue = [];
        return true;
    }
    
    // Fungsi cetak - menampilkan isi antrian
    public function cetak() {
        return $this->queue;
    }
    
    // Fungsi untuk mendapatkan ukuran antrian
    public function size() {
        return count($this->queue);
    }
    
    // Fungsi untuk memproses antrian berdasarkan book ID
    public function processQueueByBookId($bookId) {
        foreach ($this->queue as $key => $item) {
            if ($item['book_id'] == $bookId && $item['status'] == 'waiting') {
                $this->queue[$key]['status'] = 'ready';
                $this->queue[$key]['ready_time'] = time();
                
                // Update status buku menjadi tersedia di file JSON
                $this->updateBookAvailability($bookId, true);
                
                return $this->queue[$key];
            }
        }
        return false;
    }
    
    // Fungsi untuk mendapatkan posisi dalam antrian
    public function getQueuePosition($bookId, $userId = 'current_user') {
        $position = 1;
        foreach ($this->queue as $item) {
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
    
    // Fungsi untuk mendapatkan antrian berdasarkan user
    public function getQueueByUser($userId = 'current_user') {
        return array_filter($this->queue, function($item) use ($userId) {
            return $item['user_id'] == $userId;
        });
    }
    
    // Fungsi untuk mendapatkan jumlah antrian berdasarkan book ID dan status
    public function getQueueCountByBookId($bookId, $status = 'waiting') {
        return count(array_filter($this->queue, function($item) use ($bookId, $status) {
            return $item['book_id'] == $bookId && $item['status'] == $status;
        }));
    }
    
    // Fungsi untuk mendapatkan antrian berdasarkan status
    public function getQueueByStatus($status) {
        return array_filter($this->queue, function($item) use ($status) {
            return $item['status'] == $status;
        });
    }
    
    // Fungsi helper untuk update ketersediaan buku
    private function updateBookAvailability($bookId, $available) {
        $jsonFile = '../data/buku.json';
        $books = json_decode(file_get_contents($jsonFile), true);
        
        // Cari dan update buku
        foreach ($books as &$book) {
            if ($book['id'] == $bookId) {
                $book['available'] = $available;
                break;
            }
        }
        
        // Simpan kembali ke file JSON
        file_put_contents($jsonFile, json_encode($books, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        return true;
    }
}

// Inisialisasi Queue
$bookQueue = new BookQueue();

// Load data buku
$books = json_decode(file_get_contents('../data/buku.json'), true);
$message = '';
$messageType = '';

// Proses form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_to_queue'])) {
        $bookId = $_POST['book_id'];
        if ($bookQueue->enqueue($bookId)) {
            $message = 'Buku berhasil ditambahkan ke antrian!';
            $messageType = 'success';
        } else {
            if ($bookQueue->isFull()) {
                $message = 'Antrian sudah penuh!';
                $messageType = 'error';
            } else {
                $message = 'Buku sudah ada dalam antrian Anda!';
                $messageType = 'error';
            }
        }
    }
    
    if (isset($_POST['remove_from_queue'])) {
        $queueId = $_POST['queue_id'];
        if ($bookQueue->dequeueById($queueId)) {
            $message = 'Buku berhasil dihapus dari antrian!';
            $messageType = 'success';
        }
    }
    
    if (isset($_POST['process_queue'])) {
        $bookId = $_POST['book_id'];
        $processed = $bookQueue->processQueueByBookId($bookId);
        if ($processed) {
            $message = 'Antrian berhasil diproses! Buku sekarang tersedia untuk dipinjam.';
            $messageType = 'success';
            // Reload data buku setelah update
            $books = json_decode(file_get_contents('../data/buku.json'), true);
        }
    }
    
    if (isset($_POST['mark_available'])) {
        $bookId = $_POST['book_id'];
        // Update status buku menjadi tersedia
        $bookQueue->updateBookAvailability($bookId, true);
        // Proses antrian otomatis
        $bookQueue->processQueueByBookId($bookId);
        $message = 'Buku telah tersedia dan antrian diproses!';
        $messageType = 'success';
        // Reload data buku setelah update
        $books = json_decode(file_get_contents('../data/buku.json'), true);
    }
    
    // Tambahan: Fungsi untuk menandai buku tidak tersedia (untuk testing)
    if (isset($_POST['mark_unavailable'])) {
        $bookId = $_POST['book_id'];
        $bookQueue->updateBookAvailability($bookId, false);
        $message = 'Buku telah ditandai tidak tersedia!';
        $messageType = 'success';
        // Reload data buku setelah update
        $books = json_decode(file_get_contents('../data/buku.json'), true);
    }
    
    // Fungsi untuk mengosongkan antrian (admin)
    if (isset($_POST['clear_queue'])) {
        $bookQueue->clear();
        $message = 'Semua antrian telah dihapus!';
        $messageType = 'success';
    }
}

// Filter buku yang menunggu tersedia
$unavailableBooks = array_filter($books, function($book) {
    return !$book['available'];
});

// Get current user's queue menggunakan method baru
$userQueue = $bookQueue->getQueueByUser();
$allQueue = $bookQueue->cetak(); // Menggunakan method cetak()
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novel Grove - Antrian Peminjaman</title>
    <link rel="stylesheet" href="../css/queue-style.css">
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
                <a href="dashboard.php" class="nav-item">
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
                <a href="antrian.php" class="nav-item active">
                    <span class="nav-icon">⏳</span>
                    <span class="nav-text">Antrian</span>
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
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Page Header -->
            <div class="page-header">
                <h1>Antrian Peminjaman Buku</h1>
                <p>Kelola antrian untuk buku yang sedang menunggu tersedia</p>
            </div>

            <!-- Message Alert -->
            <?php if ($message): ?>
                <div class="alert alert-<?= $messageType ?>">
                    <span class="alert-icon"><?= $messageType == 'success' ? '✅' : '❌' ?></span>
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-content">
                        <div class="stat-info">
                            <p class="stat-title">Total Antrian</p>
                            <p class="stat-value"><?= $bookQueue->size() ?></p>
                        </div>
                        <div class="stat-icon blue">⏳</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-content">
                        <div class="stat-info">
                            <p class="stat-title">Antrian Saya</p>
                            <p class="stat-value"><?= count($userQueue) ?></p>
                        </div>
                        <div class="stat-icon green">👤</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-content">
                        <div class="stat-info">
                            <p class="stat-title">Siap Dipinjam</p>
                            <p class="stat-value"><?= count($bookQueue->getQueueByStatus('ready')) ?></p>
                        </div>
                        <div class="stat-icon orange">✅</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-content">
                        <div class="stat-info">
                            <p class="stat-title">Buku menunggu tersedia</p>
                            <p class="stat-value"><?= count($unavailableBooks) ?></p>
                        </div>
                        <div class="stat-icon red">❌</div>
                    </div>
                </div>
            </div>

            <!-- Queue Status Info -->
            <div class="section">
                <div class="section-header">
                    <h2>Status Antrian</h2>
                    <div style="display: flex; gap: 1rem;">
                        <span class="section-count">
                            <?= $bookQueue->isEmpty() ? 'Kosong' : $bookQueue->size() . ' item' ?>
                        </span>
                        <?php if ($bookQueue->isFull()): ?>
                            <span class="section-count" style="background: #fef2f2; color: #dc2626;">Penuh</span>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div style="display: flex; gap: 1rem; margin-bottom: 1rem;">
                    <p><strong>Kapasitas:</strong> <?= $bookQueue->size() ?>/<?= 100 ?></p>
                    <p><strong>Status:</strong> <?= $bookQueue->isEmpty() ? 'Kosong' : ($bookQueue->isFull() ? 'Penuh' : 'Tersedia') ?></p>
                </div>
                
                <!-- Admin Controls -->
                <div style="margin-top: 1rem;">
                    <form method="POST" style="display: inline;">
                        <button type="submit" name="clear_queue" class="btn btn-danger btn-sm" 
                                onclick="return confirm('Hapus semua antrian? Tindakan ini tidak dapat dibatalkan!')"
                                <?= $bookQueue->isEmpty() ? 'disabled' : '' ?>>
                            🗑️ Kosongkan Semua Antrian
                        </button>
                    </form>
                </div>
            </div>

            <!-- My Queue Section -->
            <div class="section">
                <div class="section-header">
                    <h2>Antrian Saya</h2>
                    <span class="section-count"><?= count($userQueue) ?> buku</span>
                </div>
                
                <?php if (empty($userQueue)): ?>
                    <div class="empty-state">
                        <div class="empty-icon">📚</div>
                        <h3>Belum Ada Antrian</h3>
                        <p>Anda belum memiliki buku dalam antrian. Tambahkan buku yang menunggu tersedia ke antrian Anda.</p>
                    </div>
                <?php else: ?>
                    <div class="queue-list">
                        <?php foreach ($userQueue as $queueItem): ?>
                            <?php 
                            $book = array_filter($books, function($b) use ($queueItem) {
                                return $b['id'] == $queueItem['book_id'];
                            });
                            $book = reset($book);
                            $position = $bookQueue->getQueuePosition($queueItem['book_id']);
                            ?>
                            <div class="queue-item <?= $queueItem['status'] ?>">
                                <div class="queue-book-info">
                                    <img src="<?= $book['cover'] ?>" alt="<?= htmlspecialchars($book['title']) ?>" class="queue-book-cover">
                                    <div class="queue-book-details">
                                        <h3><?= htmlspecialchars($book['title']) ?></h3>
                                        <p class="author"><?= htmlspecialchars($book['author']) ?></p>
                                        <span class="genre"><?= htmlspecialchars($book['genre']) ?></span>
                                        <?php if ($queueItem['status'] == 'ready'): ?>
                                            <div class="book-status-info">
                                                <span class="available-status">📖 Buku sekarang tersedia untuk dipinjam!</span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="queue-status">
                                    <?php if ($queueItem['status'] == 'waiting'): ?>
                                        <div class="status-badge waiting">
                                            <span class="status-icon">⏳</span>
                                            Posisi #<?= $position ?>
                                        </div>
                                        <p class="queue-time">Ditambahkan: <?= date('d/m/Y H:i', $queueItem['timestamp']) ?></p>
                                    <?php else: ?>
                                        <div class="status-badge ready">
                                            <span class="status-icon">✅</span>
                                            Siap Dipinjam
                                        </div>
                                        <p class="queue-time">Siap sejak: <?= date('d/m/Y H:i', $queueItem['ready_time']) ?></p>
                                    <?php endif; ?>
                                </div>
                                <div class="queue-actions">
                                    <?php if ($queueItem['status'] == 'ready'): ?>
                                        <a href="dashboard.php" class="btn btn-success">
                                            📖 Lihat di Dashboard
                                        </a>
                                    <?php endif; ?>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="queue_id" value="<?= $queueItem['id'] ?>">
                                        <button type="submit" name="remove_from_queue" class="btn btn-danger" onclick="return confirm('Hapus dari antrian?')">
                                            🗑️ Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- All Queue Management (Admin View) -->
            <div class="section">
                <div class="section-header">
                    <h2>Proses Antrian</h2>
                    <span class="section-count"><?= $bookQueue->size() ?> total</span>
                </div>
                
                <?php if ($bookQueue->isEmpty()): ?>
                    <div class="empty-state">
                        <div class="empty-icon">📋</div>
                        <h3>Tidak Ada Antrian</h3>
                        <p>Saat ini tidak ada antrian peminjaman buku.</p>
                    </div>
                <?php else: ?>
                    <div class="admin-queue-list">
                        <?php foreach ($allQueue as $queueItem): ?>
                            <?php 
                            $book = array_filter($books, function($b) use ($queueItem) {
                                return $b['id'] == $queueItem['book_id'];
                            });
                            $book = reset($book);
                            ?>
                            <div class="admin-queue-item <?= $queueItem['status'] ?>">
                                <div class="queue-info-compact">
                                    <img src="<?= $book['cover'] ?>" alt="<?= htmlspecialchars($book['title']) ?>" class="mini-cover">
                                    <div class="compact-details">
                                        <h4><?= htmlspecialchars($book['title']) ?></h4>
                                        <p>User: <?= htmlspecialchars($queueItem['user_id']) ?></p>
                                        <p>Status: <span class="status-text <?= $queueItem['status'] ?>"><?= ucfirst($queueItem['status']) ?></span></p>
                                        <p>Waktu: <?= date('d/m/Y H:i', $queueItem['timestamp']) ?></p>
                                        <?php if ($queueItem['status'] == 'ready'): ?>
                                            <p class="book-availability">📖 Buku tersedia di dashboard</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="admin-actions">
                                    <?php if ($queueItem['status'] == 'waiting'): ?>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="book_id" value="<?= $queueItem['book_id'] ?>">
                                            <button type="submit" name="process_queue" class="btn btn-success btn-sm" onclick="return confirm('Proses antrian ini? Buku akan menjadi tersedia.')">
                                                ✅ Proses
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span class="processed-label">✅ Sudah Diproses</span>
                                    <?php endif; ?>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="queue_id" value="<?= $queueItem['id'] ?>">
                                        <button type="submit" name="remove_from_queue" class="btn btn-danger btn-sm" onclick="return confirm('Hapus antrian ini?')">
                                            🗑️ Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Available Books for Queue -->
            <div class="section">
                <div class="section-header">
                    <h2>Buku menunggu tersedia</h2>
                    <span class="section-count"><?= count($unavailableBooks) ?> buku</span>
                </div>
                
                <?php if (empty($unavailableBooks)): ?>
                    <div class="empty-state">
                        <div class="empty-icon">🎉</div>
                        <h3>Semua Buku Tersedia!</h3>
                        <p>Saat ini semua buku dalam katalog tersedia untuk dipinjam.</p>
                    </div>
                <?php else: ?>
                    <div class="books-grid">
                        <?php foreach ($unavailableBooks as $book): ?>
                            <div class="book-card unavailable">
                                <div class="book-cover-container">
                                    <img src="<?= $book['cover'] ?>" alt="<?= htmlspecialchars($book['title']) ?>" class="book-cover">
                                    <div class="unavailable-overlay">
                                        <span class="unavailable-badge">menunggu tersedia</span>
                                    </div>
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
                                    
                                    <?php
                                    $queueCount = $bookQueue->getQueueCountByBookId($book['id']);
                                    $userPosition = $bookQueue->getQueuePosition($book['id']);
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
                                    
                                    <div class="book-actions">
                                        <?php if ($userPosition == 0): ?>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="book_id" value="<?= $book['id'] ?>">
                                                <button type="submit" name="add_to_queue" class="btn btn-primary"
                                                        <?= $bookQueue->isFull() ? 'disabled title="Antrian penuh"' : '' ?>>
                                                    ➕ Tambah ke Antrian
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <button class="btn btn-disabled" disabled>
                                                ✅ Sudah dalam Antrian
                                            </button>
                                        <?php endif; ?>
                                        
                                        <!-- Admin action untuk testing - bisa dihapus di production -->
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="book_id" value="<?= $book['id'] ?>">
                                            <button type="submit" name="mark_available" class="btn btn-success btn-sm" onclick="return confirm('Tandai buku sebagai tersedia?')">
                                                📖 Tandai Tersedia
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Navigation -->
            <div class="navigation-section">
                <a href="dashboard.php" class="btn btn-secondary">
                    ← Kembali ke Dashboard
                </a>
            </div>
        </main>
    </div>

    <script>
        // Auto refresh untuk update status
        setTimeout(function() {
            // location.reload();
        }, 30000); // Refresh setiap 30 detik

        // Konfirmasi sebelum menghapus
        document.querySelectorAll('.btn-danger').forEach(button => {
            button.addEventListener('click', function(e) {
                if (!confirm('Apakah Anda yakin ingin menghapus item ini dari antrian?')) {
                    e.preventDefault();
                }
            });
        });

        // Hide alert after 5 seconds
        const alert = document.querySelector('.alert');
        if (alert) {
            setTimeout(() => {
                alert.style.opacity = '0';
                setTimeout(() => {
                    alert.remove();
                }, 300);
            }, 5000);
        }
    </script>
</body>
</html>
