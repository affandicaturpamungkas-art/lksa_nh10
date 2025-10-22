<?php
include 'config/database.php';

$id_lksa = $_SESSION['id_lksa'];

$total_user_lksa = $conn->query("SELECT COUNT(*) AS total FROM User WHERE Id_lksa = '$id_lksa'")->fetch_assoc()['total'];
$total_donatur_lksa = $conn->query("SELECT COUNT(*) AS total FROM Donatur WHERE ID_LKSA = '$id_lksa'")->fetch_assoc()['total'];
$total_sumbangan_lksa = $conn->query("SELECT SUM(Zakat_Profesi + Zakat_Maal + Infaq + Sedekah + Fidyah) AS total FROM Sumbangan WHERE ID_LKSA = '$id_lksa'")->fetch_assoc()['total'];
$total_dana_kotak_amal_lksa = $conn->query("SELECT SUM(JmlUang) AS total FROM Dana_KotakAmal WHERE Id_lksa = '$id_lksa'")->fetch_assoc()['total'] ?? 0;

// LOGIC BARU UNTUK SIDEBAR
$id_user = $_SESSION['id_user'] ?? '';
$user_info_sql = "SELECT Nama_User, Foto FROM User WHERE Id_user = '$id_user'";
$user_info = $conn->query($user_info_sql)->fetch_assoc();
$nama_user = $user_info['Nama_User'] ?? 'Pengguna';
$foto_user = $user_info['Foto'] ?? '';
$base_url = "http://" . $_SERVER['HTTP_HOST'] . "/lksa_nh/"; // Definisikan $base_url
$foto_path = $foto_user ? $base_url . 'assets/img/' . $foto_user : $base_url . 'assets/img/yayasan.png'; // Use Yayasan logo as default if none
$sidebar_total_pegawai = $conn->query("SELECT COUNT(*) AS total FROM User WHERE Id_lksa = '$id_lksa' AND Jabatan IN ('Pegawai', 'Petugas Kotak Amal')")->fetch_assoc()['total'];
$sidebar_total_donatur_lksa = $conn->query("SELECT COUNT(*) AS total FROM Donatur WHERE ID_LKSA = '$id_lksa'")->fetch_assoc()['total'];

// Menetapkan variabel $sidebar_stats untuk digunakan di header.php
$sidebar_stats = '
<div class="sidebar-stats-card card-user" style="border-left-color: #1E3A8A;">
    <h4>Total Pegawai LKSA</h4>
    <p>' . number_format($sidebar_total_pegawai) . '</p>
</div>
<div class="sidebar-stats-card card-donatur" style="border-left-color: #10B981;">
    <h4>Total Donatur Terdaftar</h4>
    <p>' . number_format($sidebar_total_donatur_lksa) . '</p>
</div>
<div class="sidebar-stats-card card-sumbangan" style="border-left-color: #6366F1;">
    <h4>Total Sumbangan ZIS LKSA</h4>
    <p>Rp ' . number_format($total_sumbangan_lksa) . '</p>
</div>
<div class="sidebar-stats-card card-kotak-amal" style="border-left-color: #F59E0B;">
    <h4>Total Dana Kotak Amal LKSA</h4>
    <p>Rp ' . number_format($total_dana_kotak_amal_lksa) . '</p>
</div>
';

include 'includes/header.php'; // <-- LOKASI BARU
?>
<p>Anda dapat mengelola data di LKSA Anda, termasuk pengguna dan donatur.</p>

<h2 class="dashboard-title">Ringkasan Finansial LKSA</h2>
<div class="stats-grid" style="grid-template-columns: 1fr 1fr;">
    <div class="stats-card card-sumbangan">
        <i class="fas fa-sack-dollar"></i>
        <div class="stats-card-content">
            <h3>Total Sumbangan ZIS</h3>
            <span class="value">Rp <?php echo number_format($total_sumbangan_lksa); ?></span>
        </div>
    </div>
    <div class="stats-card card-kotak-amal">
        <i class="fas fa-box-open"></i>
        <div class="stats-card-content">
            <h3>Total Dana Kotak Amal</h3>
            <span class="value">Rp <?php echo number_format($total_dana_kotak_amal_lksa); ?></span>
        </div>
    </div>
</div>

<h2 class="dashboard-title">Ringkasan Operasional LKSA</h2>
<div class="stats-grid" style="grid-template-columns: 1fr;">
    <div class="stats-card card-donatur">
        <i class="fas fa-hand-holding-heart"></i>
        <div class="stats-card-content">
            <h3>Jumlah Donatur Terdaftar</h3>
            <span class="value"><?php echo number_format($total_donatur_lksa); ?></span>
        </div>
    </div>
</div>

<?php
include 'includes/footer.php';
$conn->close();
?>