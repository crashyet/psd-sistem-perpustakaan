# SISTEM PERPUSTAKAAN SEDERHANA
Berisi fitur pencarian buku, hapus pencarian, dan menampilkan antrian buku yang sedang dipinjam.

### Cara Menggunakannya
Cloning repository menggunakan command prompt atau git bash

``` bash
git clone https://github.com/crashyet/psd-sistem-perpustakaan
```

Pindahkan direktori ke web server
* Jika menggunakan Laragon (default path)
  
``` bash
cp -r psd-sistem-perpustakaan C:\laragon\www\psd-sistem-perpustakaan
```

* Jika menggunakan XAMPP (default path)
  
``` bash
cp -r psd-sistem-perpustakaan C:\xampp\htdocs\psd-sistem-perpustakaan
```

Kemudian pindah path ke lokasi direktori, dan buka menggunakan text editor kesayangan anda.

``` bash
cd C:\xampp\htdocs\psd-sistem-perpustakaan
```

``` bash
code .
```

Jangan lupa untuk menjalankan Laragon/XAMPP dan buka di browser tercinta anda.

``` bash
localhost/psd-sistem-perpustakaan
```
---

### Cara Collaboration
1. Buka terminal pada VSCode, tekan <b>ctrl + `</b> pada keyboard.

2. Tambahkan file yang akan di push
```bash
git add .
```

3. Tuliskan pesan Commits
```bash
git commit -m "isi pesan"
```
nb: diusahakan menggunakan semantik commit <a href='https://www.codepolitan.com/blog/panduan-mendalam-tentang-github-semantic-commit/'>Klik disini untuk melihat</a>.<br>

4. Buat branch baru
```bash
git branch -M "nama-branch"
```
nb: untuk nama brach kalian bebas, tetapi kalau bisa menggunakan nama kalian sendiri agar memudahkan.

5. Lakukan push ke github
```bash
git push -u origin nama-branch
```
nb: nama branch disini, nama yg anda bikin tadi.

6. Tunggulah hingga proses selesai, kemudian buka github untuk memastikan apakah file sudah terupdate atau belum.

nb: seringlah melakukan git pull pada saat membuka vscode pertama kali, agar code dalam vscode selalu terupdate sesuai dengan github.
```
git pull
```
