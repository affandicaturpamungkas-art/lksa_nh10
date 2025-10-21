<?php
session_start();
include '../config/database.php';
include '../includes/header.php';

// Verifikasi otorisasi: Hanya Pimpinan dan Kepala LKSA yang bisa mengakses halaman ini.
if ($_SESSION['jabatan'] != 'Pimpinan' && $_SESSION['jabatan'] != 'Kepala LKSA') {
    die("Akses ditolak.");
}

// Persiapan query SQL dasar untuk mengambil data pengguna.
$sql = "SELECT * FROM User";

// Logika untuk menyesuaikan query berdasarkan jabatan dan ID LKSA pengguna yang sedang login.
if ($_SESSION['jabatan'] == 'Pimpinan' && $_SESSION['id_lksa'] == 'Pimpinan_Pusat') {
    // Pimpinan Pusat dapat melihat semua pengguna.
} elseif ($_SESSION['jabatan'] == 'Pimpinan' && $_SESSION['id_lksa'] !== 'Pimpinan_Pusat') {
    // Pimpinan cabang hanya dapat melihat pengguna di LKSA-nya.
    $sql .= " WHERE Id_lksa = '" . $_SESSION['id_lksa'] . "'";
} elseif ($_SESSION['jabatan'] == 'Kepala LKSA') {
    // Kepala LKSA hanya dapat melihat pengguna dengan jabatan di bawahnya di LKSA-nya.
    $sql .= " WHERE Id_lksa = '" . $_SESSION['id_lksa'] . "' AND Jabatan IN ('Pegawai', 'Petugas Kotak Amal')";
}

$result = $conn->query($sql);
?>
<h1 class="dashboard-title">Manajemen Pengguna</h1>
<p>Anda dapat mengelola akun pengguna di sistem.</p>
<a href="tambah_pengguna.php" class="btn btn-success">Tambah Pengguna Baru</a>

<table>
    <thead>
        <tr>
            <th>Nama User</th>
            <th>Jabatan</th>
            <th>Foto</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row['Nama_User']; ?></td>
                <td><?php echo $row['Jabatan']; ?></td>
                <td>
                    <?php if ($row['Foto']) { ?>
                        <img src="../assets/img/<?php echo htmlspecialchars($row['Foto']); ?>" alt="Foto Profil" style="width: 50px; height: 50px; object-fit: cover;">
                    <?php } else { ?>
                        Tidak Ada
                    <?php } ?>
                </td>
                <td>
                    <a href="edit_pengguna.php?id=<?php echo $row['Id_user']; ?>" class="btn btn-primary">Edit</a>
                    <a href="hapus_pengguna.php?id=<?php echo $row['Id_user']; ?>" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?');">Hapus</a>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>

<?php
include '../includes/footer.php';
$conn->close();
?>