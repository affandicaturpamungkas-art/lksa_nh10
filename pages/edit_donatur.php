<?php
session_start();
include '../config/database.php';
include '../includes/header.php';

// Verifikasi otorisasi: Hanya Pimpinan, Kepala LKSA, dan Pegawai yang bisa mengakses halaman ini.
if ($_SESSION['jabatan'] != 'Pimpinan' && $_SESSION['jabatan'] != 'Kepala LKSA' && $_SESSION['jabatan'] != 'Pegawai') {
    die("Akses ditolak.");
}

$id_donatur = $_GET['id'] ?? '';
if (empty($id_donatur)) {
    die("ID donatur tidak ditemukan.");
}

// Ambil data donatur dari database
$sql = "SELECT * FROM Donatur WHERE ID_donatur = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $id_donatur);
$stmt->execute();
$result = $stmt->get_result();
$data_donatur = $result->fetch_assoc();

if (!$data_donatur) {
    die("Data donatur tidak ditemukan.");
}
?>
<div class="content">
    <div class="form-container">
        <h1>Edit Data Donatur</h1>
        <form action="proses_edit_donatur.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id_donatur" value="<?php echo htmlspecialchars($data_donatur['ID_donatur']); ?>">
            
            <div class="form-section">
                <h2>Informasi Donatur</h2>
                <div class="form-group">
                    <label>Nama Donatur:</label>
                    <input type="text" name="nama_donatur" value="<?php echo htmlspecialchars($data_donatur['Nama_Donatur']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Nomor WhatsApp:</label>
                    <input type="text" name="no_wa" value="<?php echo htmlspecialchars($data_donatur['NO_WA']); ?>">
                </div>
                <div class="form-group">
                    <label>Email:</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($data_donatur['Email']); ?>">
                </div>
                <div class="form-group">
                    <label>Alamat Lengkap:</label>
                    <textarea name="alamat_lengkap" rows="4" cols="50"><?php echo htmlspecialchars($data_donatur['Alamat_Lengkap']); ?></textarea>
                </div>
                <div class="form-group">
                    <label>Status Donasi:</label>
                    <select name="status_donasi">
                        <option value="Rutin" <?php echo ($data_donatur['Status'] == 'Rutin') ? 'selected' : ''; ?>>Rutin</option>
                        <option value="Insidental" <?php echo ($data_donatur['Status'] == 'Insidental') ? 'selected' : ''; ?>>Insidental</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Foto:</label>
                    <?php if ($data_donatur['Foto']) { ?>
                        <img src="../assets/img/<?php echo htmlspecialchars($data_donatur['Foto']); ?>" alt="Foto Donatur" style="width: 100px; height: 100px; object-fit: cover;">
                    <?php } ?>
                    <input type="file" name="foto" accept="image/*">
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-success">Simpan Perubahan</button>
                <a href="donatur.php" class="btn btn-cancel">Batal</a>
            </div>
        </form>
    </div>
</div>

<?php
include '../includes/footer.php';
$conn->close();
?>