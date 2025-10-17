<?php
session_start();
include '../config/database.php';
include '../includes/header.php';

// Authorization check
if ($_SESSION['jabatan'] != 'Pimpinan' && $_SESSION['jabatan'] != 'Kepala LKSA' && $_SESSION['jabatan'] != 'Petugas Kotak Amal') {
    die("Akses ditolak.");
}

$id_lksa = $_SESSION['id_lksa'];

// Kueri SQL yang diperbarui untuk menghindari duplikasi
// Menggunakan LEFT JOIN dan GROUP BY untuk mendapatkan satu baris per kotak amal
$sql = "SELECT ka.*, MAX(dka.ID_Kwitansi_KA) AS is_collected_today
        FROM KotakAmal ka
        LEFT JOIN Dana_KotakAmal dka ON ka.ID_KotakAmal = dka.ID_KotakAmal AND dka.Tgl_Ambil = CURDATE()";

if ($_SESSION['jabatan'] != 'Pimpinan') {
    $sql .= " WHERE ka.Id_lksa = '$id_lksa'";
}

$sql .= " GROUP BY ka.ID_KotakAmal";

$result = $conn->query($sql);
?>
<h1 class="dashboard-title">Manajemen Kotak Amal</h1>
<p>Kelola data kotak amal.</p>
<a href="tambah_kotak_amal.php" class="btn btn-success">Tambah Kotak Amal</a>

<table>
    <thead>
        <tr>
            <th>ID Kotak Amal</th>
            <th>Nama Toko</th>
            <th>Alamat</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row['ID_KotakAmal']; ?></td>
                <td><?php echo $row['Nama_Toko']; ?></td>
                <td><?php echo $row['Alamat_Toko']; ?></td>
                <td>
                    <a href="edit_kotak_amal.php?id=<?php echo $row['ID_KotakAmal']; ?>" class="btn btn-primary">Edit</a>
                    <?php if ($row['is_collected_today']) { ?>
                        <span style="color: green; font-weight: bold;">Sudah Diambil</span>
                    <?php } else { ?>
                        <a href="dana-kotak-amal.php?id_kotak_amal=<?php echo $row['ID_KotakAmal']; ?>" class="btn btn-success">Pengambilan</a>
                    <?php } ?>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>

<?php
include '../includes/footer.php';
$conn->close();
?>