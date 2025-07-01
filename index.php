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
        //data paling lama dibuang
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
    <a href="index.php"><img src="css/home.svg" alt="Home"></a>
    <a href="#"><img src="css/user.svg" alt="User"></a>
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
    
    <!-- Daftar Buku -->
    <div class="book-list">
      
      <!-- Lemon Tree -->
      <div class="book-item">
        <div class="book-cover" style="background-image: url(css/cover/lemon-tree.jpg); background-size: cover">
        </div>
        <div class="book-info">
          <div class="book-title">As Long as the Lemon Trees Grow</div>
          <div class="book-genre">Fiksi Historis Perang Romansa</div>
          <div class="book-description">
            Seorang mahasiswi farmasi yang tiba-tiba harus bekerja sebagai dokter darurat di rumah sakit yang penuh korban perang. Di tengah keputusasaan, ia dihantui oleh Khawf (ketakutan), sebuah personifikasi halusinasi yang mendorongnya untuk melarikan diri dari negaranya...
          </div>
          <button class="read-more">See More</button>
        </div>
      </div>
      
      <!-- Ronggeng -->
      <div class="book-item">
        <div class="book-cover" style="background-image: url(css/cover/ronggeng.jpg); background-size: cover">
        </div>
        <div class="book-info">
          <div class="book-title">Ronggeng Dukuh Paruk</div>
          <div class="book-genre">Fiksi Sastra Tragedi</div>
          <div class="book-description">
            Mengisahkan kehidupan Srintil, seorang penari ronggeng di pedesaan Jawa yang terjebak antara tradisi, kemiskinan, dan eksploitasi.
          </div>
          <button class="read-more">See More</button>
        </div>
      </div>

      <!-- Laut -->
      <div class="book-item">
        <div class="book-cover" style="background-image: url(css/cover/laut.jpg); background-size: cover">
        </div>
        <div class="book-info">
          <div class="book-title">Laut Bercerita</div>
          <div class="book-genre">Fiksi Sastra Drama</div>
          <div class="book-description">
            Kisah seorang aktivis mahasiswa bernama Laut yang diculik dan menghilang secara paksa pada masa Orde Baru. Novel ini menggambarkan perjuangan, pengorbanan, dan keteguhan hati dalam memperjuangkan kebenaran.
          </div>
          <button class="read-more">See More</button>
        </div>
      </div>

      <!-- 5 Menara -->
      <div class="book-item">
        <div class="book-cover" style="background-image: url(css/cover/5menara_2.jpg); background-size: cover">
        </div>
        <div class="book-info">
          <div class="book-title">Negeri 5 Menara</div>
          <div class="book-genre">Inspirasi Coming-of-age</div>
          <div class="book-description">
            Kisah perjuangan sekelompok santri di Pondok Madura yang memegang prinsip "Man Jadda Wa Jadda" (Siapa yang bersungguh-sungguh akan berhasil) untuk meraih mimpi mereka.
          </div>
          <button class="read-more">See More</button>
        </div>
      </div>
      
      <!-- Gadis Kretek -->
      <div class="book-item">
        <div class="book-cover" style="background-image: url(css/cover/kretek.jpeg); background-size: cover">
        </div>
        <div class="book-info">
          <div class="book-title">Gadis Kretek</div>
          <div class="book-genre">Fiksi Historis Romansa</div>
          <div class="book-description">
            Mengisahkan tentang Jeng Yah, seorang perempuan yang terlibat dalam industri kretek di Indonesia.
          </div>
          <button class="read-more">See More</button>
        </div>
      </div>

      <!-- Perahu Kertas -->
      <div class="book-item">
        <div class="book-cover" style="background-image: url(css/cover/pkertas.jpg); background-size: cover">
        </div>
        <div class="book-info">
          <div class="book-title">Perahu Kertas</div>
          <div class="book-genre">Romansa Drama</div>
          <div class="book-description">
            Kugy dan Keenan, dua orang dengan impian berbeda, saling menemukan cinta dan arti kehidupan.
          </div>
          <button class="read-more">See More</button>
        </div>
      </div>
      
      <!-- Bumi Manusia -->
      <div class="book-item">
        <div class="book-cover" style="background-image: url(css/cover/bumi_manusia.jpg); background-size: cover">
        </div>
        <div class="book-info">
          <div class="book-title">Bumi Manusia</div>
          <div class="book-genre">Fiksi Historis</div>
          <div class="book-description">
            Kisah Minke, pemuda Jawa yang bersekolah di HBS, berkenalan dengan Nyai Ontosoroh, perempuan pribumi yang berhasil menjadi pengusaha sukses di era kolonial.
          </div>
          <button class="read-more">See More</button>
        </div>
      </div>
      
      <!-- Laskar -->
      <div class="book-item">
        <div class="book-cover" style="background-image: url(css/cover/laskar.jpg); background-size: cover">
        </div>
        <div class="book-info">
          <div class="book-title">Laskar Pelangi</div>
          <div class="book-genre">Drama Inspiratif</div>
          <div class="book-description">
            Kisah persahabatan 10 anak dari keluarga miskin di Belitung yang bersekolah di SD Muhammadiyah dengan segala keterbatasannya.
          </div>
          <button class="read-more">See More</button>
        </div>
      </div>
      
      <!-- Harpot -->
      <div class="book-item">
        <div class="book-cover" style="background-image: url(css/cover/harry.jpg); background-size: cover">
        </div>
        <div class="book-info">
          <div class="book-title">Harry Potter and the Philosopher's Stone</div>
          <div class="book-genre">Fantasi Petualangan</div>
          <div class="book-description">
            Harry Potter menemukan bahwa ia adalah penyihir dan memulai petualangannya di Sekolah Sihir Hogwarts.
          </div>
          <button class="read-more">See More</button>
        </div>
      </div>
      
      <!-- The Hobbit -->
      <div class="book-item">
        <div class="book-cover" style="background-image: url(css/cover/hobbit.jpg); background-size: cover">
        </div>
        <div class="book-info">
          <div class="book-title">The Hobbit</div>
          <div class="book-genre">Fantasi Petualangan</div>
          <div class="book-description">
            Bilbo Baggins ikut dalam perjalanan tak terduga untuk merebut kembali harta karun yang dijaga oleh naga Smaug.
          </div>
          <button class="read-more">See More</button>
        </div>
      </div>

      <!-- Pangeran -->
      <div class="book-item">
        <div class="book-cover" style="background-image: url(css/cover/pangeran.jpg); background-size: cover">
        </div>
        <div class="book-info">
          <div class="book-title">Pangeran Kecil</div>
          <div class="book-genre">Filosofi Anak-anak</div>
          <div class="book-description">
            Seorang pilot yang terdampar di gurun bertemu dengan pangeran kecil dari asteroid lain yang mengajarkannya tentang arti kehidupan.
          </div>
          <button class="read-more">See More</button>
        </div>
      </div>
      
      <!-- Dilan -->
      <div class="book-item">
        <div class="book-cover" style="background-image: url(css/cover/dilan.jpg); background-size: cover">
        </div>
        <div class="book-info">
          <div class="book-title">Dilan 1990</div>
          <div class="book-genre">Romansa Muda</div>
          <div class="book-description">
            Kisah cinta Milea dan Dilan di Bandung tahun 1990-an dengan segala romansa dan keunikan karakter Dilan.
          </div>
          <button class="read-more">See More</button>
        </div>
      </div>
      
      <!-- Ayat2 Cinta -->
      <div class="book-item">
        <div class="book-cover" style="background-image: url(css/cover/ayat.jpg); background-size: cover">
        </div>
        <div class="book-info">
          <div class="book-title">Ayat-Ayat Cinta</div>
          <div class="book-genre">Romansa Religius</div>
          <div class="book-description">
            Kisah cinta Fahri, mahasiswa Indonesia di Kairo, yang diuji dengan berbagai cobaan dan konflik.
          </div>
          <button class="read-more">See More</button>
        </div>
      </div>
      
      <!-- 1984 -->
      <div class="book-item">
        <div class="book-cover" style="background-image: url(css/cover/1984.jpeg); background-size: cover">
        </div>
        <div class="book-info">
          <div class="book-title">1984</div>
          <div class="book-genre">Fiksi Distopia</div>
          <div class="book-description">
            Dunia di bawah pengawasan ketat Big Brother di mana kebebasan individu ditekan dan sejarah dimanipulasi.
          </div>
          <button class="read-more">See More</button>
        </div>
      </div>
      
      <!-- Mockingbird -->
      <div class="book-item">
        <div class="book-cover" style="background-image: url(css/cover/mockingbird.jpg); background-size: cover">
        </div>
        <div class="book-info">
          <div class="book-title">To Kill a Mockingbird</div>
          <div class="book-genre">Drama Hukum</div>
          <div class="book-description">
            Kisah tentang keadilan dan rasisme di Amerika Selatan melalui mata Scout Finch yang masih kecil.
          </div>
          <button class="read-more">See More</button>
        </div>
      </div>
      
      <!-- Pride -->
      <div class="book-item">
        <div class="book-cover" style="background-image: url(css/cover/pride.jpeg); background-size: cover">
        </div>
        <div class="book-info">
          <div class="book-title">Pride and Prejudice</div>
          <div class="book-genre">Romansa Klasik</div>
          <div class="book-description">
            Kisah Elizabeth Bennet dan Fitzwilliam Darcy dalam dunia masyarakat Inggris abad ke-19 yang penuh dengan prasangka dan kelas sosial.
          </div>
          <button class="read-more">See More</button>
        </div>
      </div>
      
      <!-- Da Vinci -->
      <div class="book-item">
        <div class="book-cover" style="background-image: url(css/cover/da\ vinci.jpg); background-size: cover">
        </div>
        <div class="book-info">
          <div class="book-title">The Da Vinci Code</div>
          <div class="book-genre">Misteri Thriller</div>
          <div class="book-description">
            Robert Langdon memecahkan kode rahasia yang bisa mengubah pandangan dunia tentang Yesus Kristus.
          </div>
          <button class="read-more">See More</button>
        </div>
      </div>
      
      <!-- Hujan -->
      <div class="book-item">
        <div class="book-cover" style="background-image: url(css/cover/hujan.jpeg); background-size: cover">
        </div>
        <div class="book-info">
          <div class="book-title">Hujan</div>
          <div class="book-genre">Fiksi Ilmiah</div>
          <div class="book-description">
            Kisah Lail dan Esok di masa depan ketika hujan menjadi bencana yang mematikan dan mengubah peradaban manusia.
          </div>
          <button class="read-more">See More</button>
        </div>
      </div>
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