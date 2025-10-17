<?php
session_start();
include '../config/database.php';

// Verifikasi sesi pemilik kotak amal
if (!isset($_SESSION['is_pemilik_kotak_amal'])) {
    header("Location: ../login/login.php");
    exit;
}

$id_kotak_amal = $_SESSION['id_kotak_amal'];
$nama_pemilik = $_SESSION['nama_pemilik'];

$total_uang_diambil = 0;
$result_history = null;

// Query untuk mendapatkan total uang yang diambil
$sql_total = "SELECT SUM(JmlUang) AS total FROM Dana_KotakAmal WHERE ID_KotakAmal = ?";
$stmt_total = $conn->prepare($sql_total);
if ($stmt_total) {
    $stmt_total->bind_param("s", $id_kotak_amal);
    $stmt_total->execute();
    $result_total = $stmt_total->get_result();
    $total_uang_diambil = $result_total->fetch_assoc()['total'] ?? 0;
    $stmt_total->close();
}

// Query untuk mendapatkan riwayat pengambilan
$sql_history = "SELECT dka.*, u.Nama_User 
                FROM Dana_KotakAmal dka
                LEFT JOIN User u ON dka.Id_user = u.Id_user
                WHERE dka.ID_KotakAmal = ?
                ORDER BY dka.Tgl_Ambil desc";
$stmt_history = $conn->prepare($sql_history);
if ($stmt_history) {
    $stmt_history->bind_param("s", $id_kotak_amal);
    $stmt_history->execute();
    $result_history = $stmt_history->get_result();
    $stmt_history->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pemilik Kotak Amal</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Lato:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background-image: url('../assets/img/bg.png');
        }
        .container {
            max-width: 900px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 style="text-align: left;">Ringkasan Kotak Amal</h1>
            <div style="display: flex; align-items: center; gap: 10px;">
                <span style="font-weight: 500; color: #555;">Halo, <?php echo htmlspecialchars($nama_pemilik); ?>!</span> |
                <a href="../login/logout.php" class="btn btn-danger">Logout</a>
            </div>
        </div>
        <div class="content">
            <p style="text-align: left;">Selamat datang di dashboard kotak amal Anda. Di sini Anda dapat melihat riwayat dan total dana yang telah terkumpul.</p>
            
            <div class="stats-grid" style="grid-template-columns: 1fr;">
                <div class="stats-card card-kotak-amal">
                    <i class="fas fa-box"></i>
                    <h3>Total Uang Terkumpul</h3>
                    <span class="value">Rp <?php echo number_format($total_uang_diambil); ?></span>
                </div>
            </div>

            <h2>Riwayat Pengambilan</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID Kwitansi</th>
                        <th>Jumlah Uang</th>
                        <th>Tanggal Ambil</th>
                        <th>Petugas</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result_history && $result_history->num_rows > 0) { ?>
                        <?php while ($row = $result_history->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo $row['ID_Kwitansi_KA']; ?></td>
                                <td>Rp <?php echo number_format($row['JmlUang']); ?></td>
                                <td><?php echo $row['Tgl_Ambil']; ?></td>
                                <td><?php echo htmlspecialchars($row['Nama_User'] ?? 'Admin'); ?></td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="4" class="no-data">Belum ada data pengambilan yang tercatat.</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>