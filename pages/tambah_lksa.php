<?php
session_start();
include '../config/database.php';
include '../includes/header.php';

if ($_SESSION['jabatan'] != 'Pimpinan' || $_SESSION['id_lksa'] != 'Pimpinan_Pusat') {
    die("Akses ditolak.");
}
?>
<div class="content">
    <div class="form-container">
        <h1>Tambah LKSA Baru (Kantor Cabang)</h1>
        <p>Isi formulir di bawah ini untuk mendaftarkan LKSA (kantor cabang) baru.</p>
        <form action="proses_lksa.php" method="POST" enctype="multipart/form-data">
            <div class="form-section">
                <h2>Data LKSA (Kantor Cabang)</h2>
                <div class="form-group">
                    <label>Nama LKSA:</label>
                    <input type="text" name="nama_lksa" required>
                </div>
                <div class="form-group">
                    <label>Alamat (Untuk ID LKSA):</label>
                    <input type="text" name="alamat_lksa" required>
                </div>
                <div class="form-group">
                    <label>Nomor WA:</label>
                    <input type="text" name="nomor_wa_lksa">
                </div>
                <div class="form-group">
                    <label>Email LKSA:</label>
                    <input type="email" name="email_lksa">
                </div>
                <div class="form-group">
                    <label>Logo LKSA (Opsional, Max 5MB):</label>
                    <input type="file" name="logo" accept="image/*">
                </div>
                </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-success">Daftarkan LKSA</button>
                <a href="lksa.php" class="btn btn-cancel">Batal</a>
            </div>
        </form>
    </div>
</div>

<?php
include '../includes/footer.php';
$conn->close();
?>