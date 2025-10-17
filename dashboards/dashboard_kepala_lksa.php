<?php
include 'includes/header.php';
include 'config/database.php';

$id_lksa = $_SESSION['id_lksa'];

$total_user_lksa = $conn->query("SELECT COUNT(*) AS total FROM User WHERE Id_lksa = '$id_lksa'")->fetch_assoc()['total'];
$total_donatur_lksa = $conn->query("SELECT COUNT(*) AS total FROM Donatur WHERE ID_LKSA = '$id_lksa'")->fetch_assoc()['total'];
$total_sumbangan_lksa = $conn->query("SELECT SUM(Zakat_Profesi + Zakat_Maal + Infaq + Sedekah + Fidyah) AS total FROM Sumbangan WHERE ID_LKSA = '$id_lksa'")->fetch_assoc()['total'];
?>
<div class="content">
    <h1 class="dashboard-title">Sistem Informasi ZIS dan Kotak Amal</h1>
    <p class="welcome-text">Selamat Datang, Kepala LKSA</p>
    <p>Anda dapat mengelola data di LKSA Anda, termasuk pengguna dan donatur.</p>
    <h2>Ringkasan Statistik LKSA</h2>
    <div class="stats-grid">
        <div class="stats-card card-sumbangan">
            <i class="fas fa-sack-dollar"></i>
            <h3>Total Sumbangan</h3>
            <span class="value">Rp <?php echo number_format($total_sumbangan_lksa); ?></span>
        </div>
        <div class="stats-card card-user">
            <i class="fas fa-users"></i>
            <h3>Total Pengguna</h3>
            <span class="value"><?php echo $total_user_lksa; ?></span>
        </div>
        <div class="stats-card card-donatur">
            <i class="fas fa-hand-holding-heart"></i>
            <h3>Jumlah Donatur</h3>
            <span class="value"><?php echo $total_donatur_lksa; ?></span>
        </div>
    </div>
</div>
<?php
include 'includes/footer.php';
$conn->close();
?>