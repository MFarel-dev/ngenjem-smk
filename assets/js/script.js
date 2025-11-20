/* ----------------------------------------------------------------
   File: script.js
   Deskripsi: Logika JavaScript Sederhana untuk interaktivitas
   ---------------------------------------------------------------- */

// Gunakan Vanilla JavaScript untuk validasi form atau efek sederhana

document.addEventListener('DOMContentLoaded', function() {
    console.log('Script JS kustom aktif.');

    // Contoh: Fungsi konfirmasi sederhana dengan Bootstrap Modal (jika dibutuhkan)
    function confirmHapus(id, nama) {
        if (confirm(`Apakah Anda yakin ingin menghapus data ${nama}?`)) {
            // Lakukan redirect atau AJAX call ke skrip hapus
            window.location.href = `buku_hapus.php?id=${id}`;
        }
    }

    // Contoh: Validasi form di sisi klien (sebelum dikirim ke PHP)
    const formTambahBuku = document.getElementById('form-tambah-buku');
    if (formTambahBuku) {
        formTambahBuku.addEventListener('submit', function(event) {
            const stok = document.getElementById('jumlah_stok').value;
            if (parseInt(stok) < 0) {
                alert('Stok tidak boleh bernilai negatif!');
                event.preventDefault(); // Mencegah form terkirim
            }
        });
    }

    // Tambahkan logika kustom Anda di sini
});