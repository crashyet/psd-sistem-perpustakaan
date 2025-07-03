<?php
session_start();

// Maksimal untuk antrian
define("MAX", 6);

//antrian awal
if (!isset($_SESSION['Q'])) {
    $_SESSION['Q'] = array(
        'head' => -1,
        'tail' => -1,
        'data' => array()
    );
}


$Q = $_SESSION['Q'];



function isEmpty() {
    global $Q;
    return $Q['tail'] == -1;
}

function isFull() {
    global $Q;
    return $Q['tail'] == MAX - 1;
}

//FUNCTION ENQUEUE

function enqueue($d) {
    global $Q;
    if (!isFull()) {
        if (isEmpty()) {
            $Q['head'] = $Q['tail'] = 0;
        } else {
            $Q['tail']++;
        }
        $Q['data'][$Q['tail']] = $d;
        return "✅ $d berhasil ditambahkan ke antrian.";
    } else {
        return "❌ Maaf, antrian sudah penuh.";
    }
}

function resetQueue() {
    global $Q;
    $Q = array('head' => -1, 'tail' => -1, 'data' => array());
}



$pesan = "";

// Reset 
if (isset($_POST['reset'])) {
    resetQueue();
    $pesan = "Antrian berhasil direset.";
}

//tambah antrian
elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['nama'])) {
    $nama = trim($_POST['nama']);
    $pesan = enqueue($nama);
}

// Simpan kembali ke session
$_SESSION['Q'] = $Q;
?>
