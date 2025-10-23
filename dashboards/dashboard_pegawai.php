<?php
include 'config/database.php';

$id_user = $_SESSION['id_user'];
$id_lksa = $_SESSION['id_lksa'];

// 1. Query untuk Total Sumbangan yang di-Input oleh Pegawai saat ini (Sudah ada)
$sql_pegawai = "SELECT SUM(Zakat_Profesi + Zakat_Maal + Infaq + Sedekah + Fidyah) AS total FROM Sumbangan WHERE ID_user = '$id_user'";
$total_sumbangan_pegawai = $conn->query($sql_pegawai)->fetch_assoc()['total'] ?? 0;

// 2. Query baru untuk Total Sumbangan KESELURUHAN LKSA (Sumbangan dari semua user di LKSA ini)
$sql_lksa = "SELECT SUM(Zakat_Profesi + Zakat_Maal + Infaq + Sedekah + Fidyah) AS total FROM Sumbangan WHERE Id_lksa = '$id_lksa'";
$total_sumbangan_lksa = $conn->query($sql_lksa)->fetch_assoc()['total'] ?? 0;

// LOGIC BARU UNTUK SIDEBAR
$user_info_sql = "SELECT Nama_User, Foto FROM User WHERE Id_user = '$id_user'";
$user_info = $conn->query($user_info_sql)->fetch_assoc();
$nama_user = $user_info['Nama_User'] ?? 'Pengguna';
$foto_user = $user_info['Foto'] ?? '';
$base_url = "http://" . $_SERVER['HTTP_HOST'] . "/lksa_nh/"; // Definisikan $base_url
$foto_path = $foto_user ? $base_url . 'assets/img/' . $foto_user : $base_url . 'assets/img/yayasan.png'; // Use Yayasan logo as default if none

// Total donatur yang didaftarkan oleh pegawai ini
$total_donatur_didaftarkan = $conn->query("SELECT COUNT(*) AS total FROM Donatur WHERE ID_user = '$id_user'")->fetch_assoc()['total'] ?? 0;

// Menetapkan variabel $sidebar_stats untuk digunakan di header.php (Mempertahankan total input pegawai)
$sidebar_stats = '
<div class="sidebar-stats-card card-donatur" style="border-left-color: #10B981;">
    <h4>Total Donatur Didaftarkan</h4>
    <p>' . number_format($total_donatur_didaftarkan) . '</p>
</div>

<div class="sidebar-stats-card card-sumbangan" style="border-left-color: #7C3AED;">
    <h4>Total Sumbangan ZIS Diinput</h4>
    <p>Rp ' . number_format($total_sumbangan_pegawai) . '</p>
</div>
';

include 'includes/header.php'; // <-- LOKASI BARU
?>
<p>Fokus Anda adalah mengelola donasi Zakat, Infaq, dan Sedekah.</p>

<h2>Ringkasan Donasi ZIS LKSA Anda</h2>
<div class="stats-grid">
    <div class="stats-card card-lksa" style="border-color: #06B6D4;">
        <i class="fas fa-building"></i>
        <div class="stats-card-content">
            <h3>Total Sumbangan Keseluruhan LKSA</h3>
            <span class="value" style="color: #06B6D4;">Rp <?php echo number_format($total_sumbangan_lksa); ?></span>
        </div>
    </div>
</div>

<h2>Ringkasan Sumbangan yang Anda Input</h2>
<div class="stats-grid">
    <div class="stats-card card-sumbangan">
        <i class="fas fa-sack-dollar"></i>
        <div class="stats-card-content">
            <h3>Total Sumbangan Pribadi</h3>
            <span class="value">Rp <?php echo number_format($total_sumbangan_pegawai); ?></span>
        </div>
    </div>
    
    <div class="stats-card card-donatur">
        <i class="fas fa-user-plus"></i>
        <div class="stats-card-content">
            <h3>Total Donatur Didaftarkan</h3>
            <span class="value"> <?php echo number_format($total_donatur_didaftarkan); ?></span>
        </div>
    </div>
</div>

<?php
include 'includes/footer.php';
$conn->close();
?>