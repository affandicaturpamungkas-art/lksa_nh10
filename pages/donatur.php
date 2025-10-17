<?php
session_start();
include '../config/database.php';

// Authorization check: Semua yang terkait dengan donasi ZIS
// PERBAIKAN: Menggunakan null coalescing operator (??) untuk mencegah Undefined array key warning
$jabatan = $_SESSION['jabatan'] ?? '';
if (!in_array($jabatan, ['Pimpinan', 'Kepala LKSA', 'Pegawai'])) {
    die("Akses ditolak.");
}

$id_lksa = $_SESSION['id_lksa'];

$sql = "SELECT d.*, u.Nama_User FROM Donatur d JOIN User u ON d.ID_user = u.Id_user";
if ($jabatan != 'Pimpinan') {
    $sql .= " WHERE d.ID_LKSA = '$id_lksa'";
}
$result = $conn->query($sql);

// Set sidebar stats ke string kosong agar sidebar tetap tampil
$sidebar_stats = '';

include '../includes/header.php'; // <-- LOKASI BARU
?>
<h1 class="dashboard-title">Manajemen Donatur ZIS</h1>
<p>Kelola data donatur.</p>

<?php if (isset($_GET['status']) && $_GET['status'] == 'success') { ?>
    <div style="background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 10px; margin-bottom: 20px; border-radius: 5px;">
        Data donatur berhasil disimpan!
    </div>
<?php } ?>

<a href="tambah_donatur.php" class="btn btn-success">Tambah Donatur</a>

<table>
    <thead>
        <tr>
            <th>ID Donatur</th>
            <th>Nama Donatur</th>
            <th>No. WA</th>
            <th>Dibuat Oleh</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row['ID_donatur']; ?></td>
                <td><?php echo $row['Nama_Donatur']; ?></td>
                <td><?php echo $row['NO_WA']; ?></td>
                <td><?php echo $row['Nama_User']; ?></td>
                <td>
                    <a href="edit_donatur.php?id=<?php echo $row['ID_donatur']; ?>" class="btn btn-primary">Edit</a>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>

<?php
include '../includes/footer.php';
$conn->close();
?>