<?php
session_start();
include '../config/database.php';
include '../includes/header.php';

// Verifikasi otorisasi: Pimpinan, Kepala LKSA, dan Petugas Kotak Amal
if ($_SESSION['jabatan'] != 'Pimpinan' && $_SESSION['jabatan'] != 'Kepala LKSA' && $_SESSION['jabatan'] != 'Petugas Kotak Amal') {
    die("Akses ditolak.");
}

$id_user = $_SESSION['id_user'];
$id_lksa = $_SESSION['id_lksa'];

// Ambil daftar kotak amal untuk dropdown
$sql_kotak_amal = "SELECT ID_KotakAmal, Nama_Toko FROM KotakAmal";
if ($_SESSION['jabatan'] != 'Pimpinan') {
    $sql_kotak_amal .= " WHERE Id_lksa = '$id_lksa'";
}
$result_kotak_amal = $conn->query($sql_kotak_amal);

// Ambil riwayat pengambilan dana
$sql_history = "SELECT dka.*, ka.Nama_Toko, u.Nama_User
                FROM Dana_KotakAmal dka
                LEFT JOIN KotakAmal ka ON dka.ID_KotakAmal = ka.ID_KotakAmal
                LEFT JOIN User u ON dka.Id_user = u.Id_user";
if ($_SESSION['jabatan'] != 'Pimpinan') {
    $sql_history .= " WHERE dka.Id_lksa = '$id_lksa'";
}
$result_history = $conn->query($sql_history);

?>
<h1 class="dashboard-title">Pengambilan Kotak Amal</h1>
<p>Catat pengambilan dana dari kotak amal dan lihat riwayatnya.</p>

<div class="form-container">
    <div class="form-section">
        <h2>Catat Pengambilan Baru</h2>
        <form action="proses_dana_kotak_amal.php" method="POST">
            <input type="hidden" name="id_user" value="<?php echo htmlspecialchars($id_user); ?>">

            <div class="form-group">
                <label>Pilih Kotak Amal:</label>
                <select name="id_kotak_amal" required>
                    <option value="">-- Pilih Kotak Amal --</option>
                    <?php while ($row_ka = $result_kotak_amal->fetch_assoc()) { ?>
                        <option value="<?php echo htmlspecialchars($row_ka['ID_KotakAmal']); ?>">
                            <?php echo htmlspecialchars($row_ka['Nama_Toko']); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="form-group">
                <label>Jumlah Uang (Rp):</label>
                <input type="number" name="jumlah_uang" required>
            </div>

            <div class="form-actions" style="justify-content: flex-start;">
                <button type="submit" class="btn btn-success">Simpan Pengambilan</button>
            </div>
        </form>
    </div>
</div>

<h2>Riwayat Pengambilan</h2>
<table>
    <thead>
        <tr>
            <th>ID Kwitansi</th>
            <th>Nama Toko</th>
            <th>Jumlah Uang</th>
            <th>Tanggal Ambil</th>
            <th>Petugas</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row_hist = $result_history->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row_hist['ID_Kwitansi_KA']; ?></td>
                <td><?php echo $row_hist['Nama_Toko']; ?></td>
                <td>Rp <?php echo number_format($row_hist['JmlUang']); ?></td>
                <td><?php echo $row_hist['Tgl_Ambil']; ?></td>
                <td><?php echo $row_hist['Nama_User']; ?></td>
            </tr>
        <?php } ?>
    </tbody>
</table>

<?php
include '../includes/footer.php';
$conn->close();
?>