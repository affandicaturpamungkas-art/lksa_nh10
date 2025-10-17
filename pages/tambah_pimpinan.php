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
        <h1>Tambah Akun Pimpinan Baru</h1>
        <p>Isi formulir di bawah ini untuk mendaftarkan pimpinan dan LKSA baru.</p>
        <form action="proses_pimpinan.php" method="POST" enctype="multipart/form-data">
            <div class="form-section">
                <h2>Data Pimpinan</h2>
                <div class="form-group">
                    <label>Nama User:</label>
                    <input type="text" name="nama_user" required>
                </div>
                <div class="form-group">
                    <label>Password:</label>
                    <input type="password" name="password" required>
                </div>
                <div class="form-group">
                    <label>Jabatan:</label>
                    <input type="text" name="jabatan" value="Pimpinan" readonly>
                </div>
                <div class="form-group">
                    <label>Foto:</label>
                    <input type="file" name="foto" accept="image/*">
                </div>
            </div>

            <div class="form-section">
                <h2>Data LKSA Pimpinan</h2>
                <p class="form-description">ID LKSA akan dibuat secara otomatis berdasarkan alamat yang Anda masukkan.</p>
                <div class="form-group">
                    <label>Alamat:</label>
                    <input type="text" name="alamat" required>
                </div>
                <div class="form-group">
                    <label>Nomor WA:</label>
                    <input type="text" name="nomor_wa">
                </div>
                <div class="form-group">
                    <label>Email:</label>
                    <input type="email" name="email">
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-success">Simpan</button>
                <a href="users.php" class="btn btn-cancel">Batal</a>
            </div>
        </form>
    </div>
</div>

<?php
include '../includes/footer.php';
$conn->close();
?>