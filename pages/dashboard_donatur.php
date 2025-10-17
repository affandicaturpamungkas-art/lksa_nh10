<?php
session_start();
include '../config/database.php';

// Verifikasi sesi donatur
if (!isset($_SESSION['id_donatur'])) {
    header("Location: ../login/login.php");
    exit;
}

$id_donatur = $_SESSION['id_donatur'];
$nama_donatur = $_SESSION['nama_donatur'] ?? 'Donatur';
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

// LOGIC BARU UNTUK SIDEBAR
// Ambil foto donatur (asumsi Donatur table has a Foto column)
$sql_donatur_foto = "SELECT Foto FROM Donatur WHERE ID_donatur = ?";
$stmt_foto = $conn->prepare($sql_donatur_foto);
$stmt_foto->bind_param("s", $id_donatur);
$stmt_foto->execute();
$foto_result = $stmt_foto->get_result();
$foto_row = $foto_result->fetch_assoc();
$foto_donatur = $foto_row['Foto'] ?? '';
$stmt_foto->close();

$base_url = "http://" . $_SERVER['HTTP_HOST'] . "/lksa_nh/";
$foto_path = $foto_donatur ? $base_url . 'assets/img/' . $foto_donatur : $base_url . 'assets/img/yayasan.png'; 
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
        /* NEW STYLES FOR DONATUR LAYOUT */
        body {
            background-image: url('../assets/img/bg.png');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }
        .container {
            max-width: 1400px; /* Increased max-width */
            padding: 20px;
        }
        
        /* Implementasi layout sidebar di .content */
        .content { 
            padding: 40px;
            background-color: #fff;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            margin-top: 20px; 
            display: flex;
            gap: 40px; 
            align-items: flex-start;
        }

        .sidebar-wrapper { 
            width: 280px; 
            flex-shrink: 0;
            padding: 20px 0; 
            text-align: center;
            border-right: 1px solid #e0e0e0;
            padding-right: 40px;
        }
        .main-content-area {
            flex-grow: 1;
            padding: 0;
        }
        .profile-img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 50%;
            border: 5px solid #2ecc71; /* Donatur color */
            margin-bottom: 15px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .welcome-text-sidebar {
            font-size: 1.2em;
            font-weight: 600;
            margin: 10px 0 20px 0;
            color: #2c3e50;
        }
        .sidebar-wrapper .btn { 
            width: 100%;
            margin-top: 10px;
            display: block;
            text-align: center;
            box-sizing: border-box;
        }
        .sidebar-stats-card {
            background-color: #f0f2f5;
            padding: 15px;
            border-radius: 10px;
            margin-top: 15px;
            text-align: left;
            border-left: 5px solid #2ecc71;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .sidebar-stats-card h4 {
            margin: 0 0 5px 0;
            font-size: 0.9em;
            color: #555;
        }
        .sidebar-stats-card p {
            margin: 0;
            font-size: 1.5em;
            font-weight: 700;
            color: #2ecc71;
        }
        .sidebar-wrapper hr { 
            margin: 20px 0;
            border: 0;
            border-top: 1px solid #e0e0e0;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #fff;
            padding: 20px 30px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            margin-bottom: 0;
        }
        .header h1 {
            text-align: left;
            margin: 0;
            font-size: 1.5em;
            font-weight: 700;
            color: #2c3e50;
        }
        /* END NEW STYLES */
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 style="text-align: left;">Dashboard Donatur</h1>
            <div style="display: flex; align-items: center; gap: 10px;">
                <span style="font-weight: 500; color: #555;">Halo, <?php echo htmlspecialchars($nama_donatur); ?>!</span>
                <a href="../login/logout.php" class="btn btn-danger">Logout</a>
            </div>
        </div>

        <div class="content">
            <div class="sidebar-wrapper">
                <img src="<?php echo htmlspecialchars($foto_path); ?>" alt="Foto Profil" class="profile-img">
                
                <p class="welcome-text-sidebar">Selamat Datang,<br>
                <strong><?php echo htmlspecialchars($nama_donatur); ?> (Donatur)</strong></p>

                <a href="#" class="btn btn-primary" disabled><i class="fas fa-edit"></i> Edit Profil</a> 
                <a href="../login/logout.php" class="btn btn-danger"><i class="fas fa-sign-out-alt"></i> Logout</a>
                
                <hr>
                
                <div class="sidebar-stats-card">
                    <h4>Total Donasi Anda</h4>
                    <p>Rp <?php echo number_format($total_donasi); ?></p>
                </div>
            </div>
            <div class="main-content-area">
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
    </div>
</body>
</html>