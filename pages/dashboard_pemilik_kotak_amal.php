<?php
session_start();
include '../config/database.php';

// Verifikasi sesi pemilik kotak amal
if (!isset($_SESSION['is_pemilik_kotak_amal'])) {
    header("Location: ../login/login.php");
    exit;
}

$id_kotak_amal = $_SESSION['id_kotak_amal'];
// Mengambil Nama Pemilik dari DB berdasarkan ID Kotak Amal
$sql_pemilik = "SELECT Nama_Pemilik FROM KotakAmal WHERE ID_KotakAmal = ?";
$stmt_pemilik = $conn->prepare($sql_pemilik);
$stmt_pemilik->bind_param("s", $id_kotak_amal);
$stmt_pemilik->execute();
$result_pemilik = $stmt_pemilik->get_result();
$nama_pemilik = $result_pemilik->fetch_assoc()['Nama_Pemilik'] ?? 'Pemilik Kotak Amal';
$stmt_pemilik->close();

// Setting session ID Pemilik Kotak Amal untuk laporan (ID Kotak Amal berfungsi sebagai ID Pemilik di konteks ini)
$_SESSION['id_pemilik'] = $id_kotak_amal; 


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

// LOGIC BARU UNTUK SIDEBAR
// Ambil foto kotak amal (asumsi KotakAmal table has a Foto column)
$sql_kotak_amal_foto = "SELECT Foto FROM KotakAmal WHERE ID_KotakAmal = ?";
$stmt_foto = $conn->prepare($sql_kotak_amal_foto);
$stmt_foto->bind_param("s", $id_kotak_amal);
$stmt_foto->execute();
$foto_result = $stmt_foto->get_result();
$foto_row = $foto_result->fetch_assoc();
$foto_kotak_amal = $foto_row['Foto'] ?? '';
$stmt_foto->close();

$base_url = "http://" . $_SERVER['HTTP_HOST'] . "/lksa_nh/";
$foto_path = $foto_kotak_amal ? $base_url . 'assets/img/' . $foto_kotak_amal : $base_url . 'assets/img/yayasan.png'; 
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
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* NEW STYLES FOR PEMILIK KOTAK AMAL LAYOUT */
        :root {
            --ka-accent: #F97316; /* Orange (Diperbarui) */
            --ka-secondary-bg: #FEF3C7; /* Light Amber/Yellow */
            --ka-danger: #EF4444; /* Red */
            --text-dark: #1F2937; /* Deep Navy (Diperbarui) */
        }

        body {
            background-image: url('../assets/img/bg.png');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            font-family: 'Open Sans', sans-serif;
        }
        .container {
            max-width: 1400px;
            padding: 20px;
        }
        
        .content { 
            padding: 40px;
            background-color: #fff;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1); /* Bayangan lebih menonjol dan elegan */
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
            border: 5px solid var(--ka-accent);
            margin-bottom: 15px;
            box-shadow: 0 0 15px rgba(0,0,0,0.2); /* Bayangan lebih jelas */
        }
        .welcome-text-sidebar {
            font-size: 1.2em;
            font-weight: 600;
            margin: 10px 0 20px 0;
            color: var(--text-dark); /* Deep Navy */
        }
        
        .sidebar-wrapper .btn { 
            width: 100%;
            margin-top: 10px;
            display: block;
            text-align: center;
            box-sizing: border-box;
            font-family: 'Open Sans', sans-serif;
            font-weight: 600;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }
        
        /* Gaya Tombol Edit dan Logout */
        .sidebar-wrapper .btn-edit-ka {
            background-color: var(--ka-accent);
            color: white;
        }
        .sidebar-wrapper .btn-edit-ka:hover {
            background-color: #C2410C; /* Darker Orange (Diperbarui) */
            transform: translateY(-3px);
            box-shadow: 0 8px 15px rgba(249, 115, 22, 0.4);
        }
        .sidebar-wrapper .btn-report { 
            background-color: #3B82F6; /* Strong Blue (Diperbarui) */
            color: white;
            font-weight: 700;
        }
        .sidebar-wrapper .btn-report:hover {
            background-color: #2563EB; /* Darker Blue (Diperbarui) */
            transform: translateY(-3px);
        }
        .sidebar-wrapper .btn-danger {
            background-color: var(--ka-danger);
            color: white;
        }

        .sidebar-stats-card {
            background-color: var(--ka-secondary-bg);
            padding: 18px; /* Lebih lega */
            border-radius: 10px;
            margin-top: 15px;
            text-align: left;
            border-left: 5px solid var(--ka-accent);
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
            color: var(--ka-accent);
        }
        .sidebar-wrapper hr { 
            margin: 20px 0;
            border: 0;
            border-top: 1px solid #e0e0e0;
        }
        .header {
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            margin-bottom: 0;
        }
        .header h1 {
            color: var(--text-dark); /* Deep Navy */
        }
        .main-content-area h2 {
            font-size: 1.8em;
            color: var(--ka-accent);
            border-bottom: 2px solid #e0e0e0;
            padding-bottom: 10px;
            margin-top: 40px;
            font-family: 'Montserrat', sans-serif;
        }
        .stats-card {
            border-left: 5px solid var(--ka-accent);
            background-color: var(--ka-secondary-bg);
            text-align: left;
            padding: 30px; /* Lebih lega */
            align-items: flex-start;
            border-radius: 15px; /* Lebih membulat */
            box-shadow: 0 4px 15px rgba(0,0,0,0.1); /* Shadow elegan */
        }
        .stats-card i { color: var(--ka-accent); align-self: flex-end; font-size: 3.0em;} /* Ikon lebih besar */
        .stats-card h3 { color: var(--text-dark); font-size: 1.3em;} /* Deep Navy */
        .stats-card .value { color: var(--ka-accent); font-size: 3.5em; font-weight: 800;} /* Angka lebih besar */
        
        table thead th {
            background-color: var(--ka-accent);
            color: white;
            font-weight: 600;
        }
        /* END NEW STYLES */
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 style="text-align: left;">Dashboard Pemilik Kotak Amal</h1>
            <div style="display: flex; align-items: center; gap: 10px;">
                <span style="font-weight: 500; color: #555;">Halo, <?php echo htmlspecialchars($nama_pemilik); ?>!</span>
            </div>
        </div>

        <div class="content">
            <div class="sidebar-wrapper">
                <img src="<?php echo htmlspecialchars($foto_path); ?>" alt="Foto Kotak Amal" class="profile-img">
                
                <p class="welcome-text-sidebar">Selamat Datang,<br>
                <strong><?php echo htmlspecialchars($nama_pemilik); ?> (Pemilik Kotak Amal)</strong></p>

                <a href="<?php echo $base_url; ?>pages/edit_kotak_amal.php?id=<?php echo htmlspecialchars($id_kotak_amal); ?>" class="btn btn-edit-ka">
                    <i class="fas fa-edit"></i> Edit Kotak Amal
                </a> 
                
                <a href="tambah_laporan.php" class="btn btn-report" style="margin-top: 10px;">
                    <i class="fas fa-bullhorn"></i> Laporkan Masalah
                </a>
                
                <a href="../login/logout.php" class="btn btn-danger" style="margin-top: 10px;"><i class="fas fa-sign-out-alt"></i> Logout</a>

                <hr>
                
                <div class="sidebar-stats-card">
                    <h4>Total Uang Diambil</h4>
                    <p>Rp <?php echo number_format($total_uang_diambil); ?></p>
                </div>
            </div>
            <div class="main-content-area">
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
    </div>
</body>
</html>