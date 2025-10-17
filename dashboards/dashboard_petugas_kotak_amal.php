<?php
include 'includes/header.php';
include 'config/database.php';

$id_user = $_SESSION['id_user'];
$id_lksa = $_SESSION['id_lksa'];

// Query untuk mendapatkan total uang yang diambil
$sql_uang = "SELECT SUM(JmlUang) AS total FROM Dana_KotakAmal WHERE ID_user = ?";
$stmt_uang = $conn->prepare($sql_uang);
$stmt_uang->bind_param("s", $id_user);
$stmt_uang->execute();
$result_uang = $stmt_uang->get_result();
$total_uang_diambil = $result_uang->fetch_assoc()['total'] ?? 0;
$stmt_uang->close();

// Query untuk mendapatkan total kotak amal yang dikelola
$sql_kotak = "SELECT COUNT(*) AS total FROM KotakAmal WHERE ID_LKSA = ?";
$stmt_kotak = $conn->prepare($sql_kotak);
$stmt_kotak->bind_param("s", $id_lksa);
$stmt_kotak->execute();
$result_kotak = $stmt_kotak->get_result();
$total_kotak_amal_dikelola = $result_kotak->fetch_assoc()['total'] ?? 0;
$stmt_kotak->close();

// Ambil jadwal pengambilan untuk hari ini dengan status pengambilan
$current_day = date('l');
$hari_indonesia = [
    'Sunday' => 'Minggu',
    'Monday' => 'Senin',
    'Tuesday' => 'Selasa',
    'Wednesday' => 'Rabu',
    'Thursday' => 'Kamis',
    'Friday' => 'Jumat',
    'Saturday' => 'Sabtu'
];
$hari_ini = $hari_indonesia[$current_day];

// Kueri SQL yang diperbarui untuk menghindari duplikasi
$sql_jadwal = "SELECT ka.ID_KotakAmal, ka.Nama_Toko, ka.Alamat_Toko, MAX(dka.ID_Kwitansi_KA) AS is_collected_today
               FROM KotakAmal ka
               LEFT JOIN Dana_KotakAmal dka ON ka.ID_KotakAmal = dka.ID_KotakAmal AND dka.Tgl_Ambil = CURDATE()
               WHERE ka.ID_LKSA = ? AND FIND_IN_SET(?, ka.Jadwal_Pengambilan)
               GROUP BY ka.ID_KotakAmal
               ORDER BY ka.Nama_Toko ASC";
$stmt = $conn->prepare($sql_jadwal);
$stmt->bind_param("ss", $id_lksa, $hari_ini);
$stmt->execute();
$result_jadwal = $stmt->get_result();
$stmt->close();
?>
<div class="content">
    <h1 class="dashboard-title">Sistem Informasi ZIS dan Kotak Amal</h1>
    <p class="welcome-text">Selamat Datang, Petugas Kotak Amal</p>
    <p>Anda dapat mengelola data kotak amal dan pengambilan dananya.</p>
    <h2>Ringkasan Kotak Amal</h2>
    <div class="stats-grid">
        <div class="stats-card card-sumbangan">
            <i class="fas fa-money-bill-wave"></i>
            <h3>Total Uang Diambil</h3>
            <span class="value">Rp <?php echo number_format($total_uang_diambil); ?></span>
        </div>
        <div class="stats-card card-kotak-amal">
            <i class="fas fa-box"></i>
            <h3>Kotak Amal Dikelola</h3>
            <span class="value"><?php echo $total_kotak_amal_dikelola; ?></span>
        </div>
    </div>

    <h2>Jadwal Pengambilan Hari Ini (<?php echo $hari_ini; ?>)</h2>
    <?php if ($result_jadwal->num_rows > 0) { ?>
        <table>
            <thead>
                <tr>
                    <th>Nama Toko</th>
                    <th>Alamat Toko</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result_jadwal->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['Nama_Toko']); ?></td>
                        <td><?php echo htmlspecialchars($row['Alamat_Toko']); ?></td>
                        <td>
                            <?php if ($row['is_collected_today']) { ?>
                                <span style="color: green; font-weight: bold;">Sudah Diambil</span>
                            <?php } else { ?>
                                <span style="color: orange; font-weight: bold;">Belum Diambil</span>
                            <?php } ?>
                        </td>
                        <td>
                            <?php if ($row['is_collected_today']) { ?>
                                <?php } else { ?>
                                <a href="pages/dana-kotak-amal.php?id_kotak_amal=<?php echo htmlspecialchars($row['ID_KotakAmal']); ?>" class="btn btn-primary">Ambil</a>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } else { ?>
        <p>Tidak ada jadwal pengambilan untuk hari ini.</p>
    <?php } ?>
</div>
<?php
include 'includes/footer.php';
$conn->close();
?>