<?php
include 'config/database.php';

$id_user = $_SESSION['id_user'];
$total_sumbangan_pegawai = $conn->query("SELECT SUM(Zakat_Profesi + Zakat_Maal + Infaq + Sedekah + Fidyah) AS total FROM Sumbangan WHERE ID_user = '$id_user'")->fetch_assoc()['total'];

// LOGIC BARU UNTUK SIDEBAR
$user_info_sql = "SELECT Nama_User, Foto FROM User WHERE Id_user = '$id_user'";
$user_info = $conn->query($user_info_sql)->fetch_assoc();
$nama_user = $user_info['Nama_User'] ?? 'Pengguna';
$foto_user = $user_info['Foto'] ?? '';
$base_url = "http://" . $_SERVER['HTTP_HOST'] . "/lksa_nh/"; // Definisikan $base_url
$foto_path = $foto_user ? $base_url . 'assets/img/' . $foto_user : $base_url . 'assets/img/yayasan.png'; // Use Yayasan logo as default if none

// Total donatur yang didaftarkan oleh pegawai ini
$total_donatur_didaftarkan = $conn->query("SELECT COUNT(*) AS total FROM Donatur WHERE ID_user = '$id_user'")->fetch_assoc()['total'] ?? 0;

// Menetapkan variabel $sidebar_stats untuk digunakan di header.php
$sidebar_stats = '
<div class="sidebar-stats-card card-donatur" style="border-left-color: #2ecc71;">
    <h4>Total Donatur Didaftarkan</h4>
    <p>' . number_format($total_donatur_didaftarkan) . '</p>
</div>

<div class="sidebar-stats-card card-sumbangan" style="border-left-color: #9b59b6;">
    <h4>Total Sumbangan ZIS Diinput</h4>
    <p>Rp ' . number_format($total_sumbangan_pegawai) . '</p>
</div>
';

include 'includes/header.php'; // <-- LOKASI BARU
?>
<p>Fokus Anda adalah mengelola donasi Zakat, Infaq, dan Sedekah.</p>
<h2>Ringkasan Sumbangan yang Anda Input</h2>
<div class="stats-grid" style="justify-content: center;">
    <div class="stats-card card-sumbangan">
        <i class="fas fa-sack-dollar"></i>
        <h3>Total Sumbangan</h3>
        <span class="value">Rp <?php echo number_format($total_sumbangan_pegawai); ?></span>
    </div>
</div>
<?php
include 'includes/footer.php';
$conn->close();
?>