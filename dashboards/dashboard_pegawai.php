<?php
include 'includes/header.php';
include 'config/database.php';

$id_user = $_SESSION['id_user'];
$total_sumbangan_pegawai = $conn->query("SELECT SUM(Zakat_Profesi + Zakat_Maal + Infaq + Sedekah + Fidyah) AS total FROM Sumbangan WHERE ID_user = '$id_user'")->fetch_assoc()['total'];
?>
<div class="content">
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