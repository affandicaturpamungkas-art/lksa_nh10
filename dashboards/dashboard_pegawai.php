<?php
include 'includes/header.php';
include 'config/database.php';

$id_user = $_SESSION['id_user'];
$total_sumbangan_pegawai = $conn->query("SELECT SUM(Zakat_Profesi + Zakat_Maal + Infaq + Sedekah + Fidyah) AS total FROM Sumbangan WHERE ID_user = '$id_user'")->fetch_assoc()['total'];

// LOGIC BARU UNTUK SIDEBAR
$user_info_sql = "SELECT Nama_User, Foto FROM User WHERE Id_user = '$id_user'";
$user_info = $conn->query($user_info_sql)->fetch_assoc();
$nama_user = $user_info['Nama_User'] ?? 'Pengguna';
$foto_user = $user_info['Foto'] ?? '';
$foto_path = $foto_user ? $base_url . 'assets/img/' . $foto_user : $base_url . 'assets/img/yayasan.png'; // Use Yayasan logo as default if none

// Total donatur yang didaftarkan oleh pegawai ini
$total_donatur_didaftarkan = $conn->query("SELECT COUNT(*) AS total FROM Donatur WHERE ID_user = '$id_user'")->fetch_assoc()['total'] ?? 0;
?>
<div class="content">
    <div class="sidebar-wrapper">
        <img src="<?php echo htmlspecialchars($foto_path); ?>" alt="Foto Profil" class="profile-img">
        
        <p class="welcome-text-sidebar">Selamat Datang,<br>
        <strong><?php echo htmlspecialchars($nama_user); ?> (<?php echo $_SESSION['jabatan']; ?>)</strong></p>

        <a href="<?php echo $base_url; ?>pages/edit_pengguna.php?id=<?php echo htmlspecialchars($id_user); ?>" class="btn btn-primary"><i class="fas fa-edit"></i> Edit Profil</a>
        <a href="<?php echo $base_url; ?>login/logout.php" class="btn btn-danger"><i class="fas fa-sign-out-alt"></i> Logout</a>
        
        <hr>

        <h2>Ringkasan Pegawai</h2>
        <div class="sidebar-stats-card card-donatur" style="border-left-color: #2ecc71;">
            <h4>Total Donatur Didaftarkan</h4>
            <p><?php echo number_format($total_donatur_didaftarkan); ?></p>
        </div>

        <div class="sidebar-stats-card card-sumbangan" style="border-left-color: #9b59b6;">
            <h4>Total Sumbangan ZIS Diinput</h4>
            <p>Rp <?php echo number_format($total_sumbangan_pegawai); ?></p>
        </div>
        
    </div>
    <div class="main-content-area">
        <h1 class="dashboard-title">Sistem Informasi ZIS dan Kotak Amal</h1>
        <p class="welcome-text">Selamat Datang, Pegawai</p>
        <p>Fokus Anda adalah mengelola donasi Zakat, Infaq, dan Sedekah.</p>
        <h2>Ringkasan Sumbangan yang Anda Input</h2>
        <div class="stats-grid" style="justify-content: center;">
            <div class="stats-card card-sumbangan">
                <i class="fas fa-sack-dollar"></i>
                <h3>Total Sumbangan</h3>
                <span class="value">Rp <?php echo number_format($total_sumbangan_pegawai); ?></span>
            </div>
        </div>
    </div>
    <?php
include 'includes/footer.php';
$conn->close();
?>