<?php
session_start();
include '../config/database.php';
include '../includes/header.php';

if ($_SESSION['jabatan'] != 'Pimpinan') {
    die("Akses ditolak.");
}
?>
<div class="content">
    <div class="form-container">
        <h1>Tambah LKSA Baru</h1>
        <p>Isi formulir di bawah ini untuk mendaftarkan LKSA baru dan Kepala LKS-nya.</p>
        <form action="proses_lksa.php" method="POST">
            <div class="form-section">
                <h2>Data LKSA</h2>
                <div class="form-group">
                    <label>Nama LKSA:</label>
                    <input type="text" name="nama_lksa" required>
                </div>
                <div class="form-group">
                    <label>Alamat:</label>
                    <input type="text" name="alamat_lksa" required>
                </div>
                <div class="form-group">
                    <label>Nomor WA:</label>
                    <input type="text" name="nomor_wa_lksa" required>
                </div>
                <div class="form-group">
                    <label>Nama Pimpinan LKSA:</label>
                    <input type="text" name="nama_pimpinan_lksa" required>
                </div>
                <div class="form-group">
                    <label>Email LKSA:</label>
                    <input type="email" name="email_lksa">
                </div>
            </div>

            <div class="form-section">
                <h2>Data Kepala LKSA</h2>
                <p>Pengguna yang dibuat di sini akan menjadi Kepala LKSA untuk LKSA yang baru didaftarkan.</p>
                <div class="form-group">
                    <label>Nama User (Kepala LKSA):</label>
                    <input type="text" name="nama_user_kepala_lksa" required>
                </div>
                <div class="form-group">
                    <label>Password:</label>
                    <input type="password" name="password_kepala_lksa" required>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-success">Daftarkan LKSA & Kepala LKSA</button>
                <a href="lksa.php" class="btn btn-cancel">Batal</a>
            </div>
        </form>
    </div>
</div>

<?php
include '../includes/footer.php';
$conn->close();
?>