<?php
session_start();
include '../config/database.php';
include '../includes/header.php';

// Pastikan hanya Pimpinan yang bisa mengakses halaman ini
if ($_SESSION['jabatan'] != 'Pimpinan') {
    die("Akses ditolak. Anda tidak memiliki izin untuk melihat halaman ini.");
}

// Logika untuk menampilkan data LKSA
$sql = "SELECT * FROM LKSA";
$result = $conn->query($sql);

?>
<div class="content">
    <h1 class="dashboard-title">Manajemen LKSA</h1>
    <p>Halaman ini memungkinkan Anda untuk mengelola semua data LKSA yang terdaftar.</p>
    <table>
        <thead>
            <tr>
                <th>ID LKSA</th>
                <th>Nama Pimpinan</th>
                <th>Alamat</th>
                <th>Tanggal Daftar</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['Id_lksa']; ?></td>
                    <td><?php echo $row['Nama_Pimpinan']; ?></td>
                    <td><?php echo $row['Alamat']; ?></td>
                    <td><?php echo $row['Tanggal_Daftar']; ?></td>
                    <td>
                        <a href="edit_lksa.php?id=<?php echo $row['Id_lksa']; ?>" class="btn btn-primary">Edit</a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php
include '../includes/footer.php';
$conn->close();
?>