<?php
session_start();
include '../config/database.php';

// Verifikasi sesi donatur
if (!isset($_SESSION['id_donatur'])) {
    header("Location: ../login/login.php");
    exit;
}

$id_donatur = $_SESSION['id_donatur'];
$total_donasi = 0;
$result_history = null;

// Query untuk mendapatkan total donasi
$sql_total = "SELECT SUM(Zakat_Profesi + Zakat_Maal + Infaq + Sedekah + Fidyah) AS total_donasi FROM Sumbangan WHERE ID_donatur = ?";
$stmt_total = $conn->prepare($sql_total);
if ($stmt_total) {
    $stmt_total->bind_param("s", $id_donatur);
    $stmt_total->execute();
    $result_total = $stmt_total->get_result();
    $total_donasi = $result_total->fetch_assoc()['total_donasi'] ?? 0;
    $stmt_total->close();
}

// Query untuk mendapatkan riwayat donasi dan nama user yang menginput
$sql_history = "SELECT s.*, u.Nama_User 
                FROM Sumbangan s 
                LEFT JOIN User u ON s.ID_User = u.Id_user 
                WHERE s.ID_donatur = ?
                ORDER BY s.Tgl desc";
$stmt_history = $conn->prepare($sql_history);
if ($stmt_history) {
    $stmt_history->bind_param("s", $id_donatur);
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
    <title>Dashboard Donatur</title>
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
            <h1 style="text-align: left;">Ringkasan Donasi</h1>
            <div style="display: flex; align-items: center; gap: 10px;">
                <span style="font-weight: 500; color: #555;">Halo, <?php echo htmlspecialchars($_SESSION['nama_donatur']); ?>!</span> |
                <a href="../login/logout.php" class="btn btn-danger">Logout</a>
            </div>
        </div>

        <div class="content">
            <p style="text-align: left;">Selamat datang di dashboard donasi Anda. Di sini Anda dapat melihat riwayat dan total sumbangan yang telah Anda berikan.</p>
            
            <div class="stats-grid" style="grid-template-columns: 1fr;">
                <div class="stats-card card-donatur">
                    <i class="fas fa-hand-holding-usd"></i>
                    <h3>Total Donasi Anda</h3>
                    <span class="value">Rp <?php echo number_format($total_donasi); ?></span>
                </div>
            </div>

            <h2>Riwayat Sumbangan</h2>
            <table>
                <thead>
                    <tr>
                        <th>No. Kwitansi</th>
                        <th>Tanggal</th>
                        <th>Zakat Profesi</th>
                        <th>Zakat Maal</th>
                        <th>Infaq</th>
                        <th>Sedekah</th>
                        <th>Fidyah</th>
                        <th>Dibuat Oleh</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result_history && $result_history->num_rows > 0) { ?>
                        <?php while ($row = $result_history->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo $row['ID_Kwitansi_ZIS']; ?></td>
                                <td><?php echo $row['Tgl']; ?></td>
                                <td>Rp <?php echo number_format($row['Zakat_Profesi']); ?></td>
                                <td>Rp <?php echo number_format($row['Zakat_Maal']); ?></td>
                                <td>Rp <?php echo number_format($row['Infaq']); ?></td>
                                <td>Rp <?php echo number_format($row['Sedekah']); ?></td>
                                <td>Rp <?php echo number_format($row['Fidyah']); ?></td>
                                <td><?php echo htmlspecialchars($row['Nama_User'] ?? 'Admin'); ?></td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="8" class="no-data">Belum ada data sumbangan yang tercatat.</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>