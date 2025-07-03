<?php
// demoan peak
$buku = [
    'databuku' => [
        'stack' => [
            ['judul' => 'Buku A', 'pengarang' => 'Pengarang A'],
            ['judul' => 'Buku B', 'pengarang' => 'Pengarang B'],
            ['judul' => 'Buku C', 'pengarang' => 'Pengarang C'],
        ],
        'top' => ['judul' => 'si pintar', 'pengarang' => 'lita']
    ]
];

function peek()
{
    global $buku;
    return $buku['databuku']['top'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Demo Peek Stack Buku</title>
</head>
<body>
    <h2>Form Tambah Buku (Belum Aktif)</h2>
    <form>
        Judul Buku: <input type="text" name="judul"><br>
        Pengarang: <input type="text" name="pengarang"><br>
        <button type="submit">Tambah Buku</button>
    </form>
    <p><i>Form tambah buku hanya tampilan, belum ada fungsi tambah buku. Hanya peak stack yang aktif.</i></p>

    <h3>Data Buku Teratas (Peek Stack)</h3>
    <?php $top = peek(); ?>
    <ul>
        <li>Judul: <?php echo htmlspecialchars($top['judul']); ?></li>
        <li>Pengarang: <?php echo htmlspecialchars($top['pengarang']); ?></li>
    </ul>
</body>
</html>
