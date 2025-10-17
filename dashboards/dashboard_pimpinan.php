<?php
include 'config/database.php';

// Menentukan bulan dan tahun saat ini sebagai default
$selected_month = isset($_POST['bulan']) ? $_POST['bulan'] : date('m');
$selected_year = isset($_POST['tahun']) ? $_POST['tahun'] : date('Y');

// Query untuk total sumbangan berdasarkan bulan dan tahun yang dipilih
$total_sumbangan_bulan_ini = $conn->query("SELECT SUM(Zakat_Profesi + Zakat_Maal + Infaq + Sedekah + Fidyah) AS total FROM Sumbangan WHERE MONTH(Tgl) = '$selected_month' AND YEAR(Tgl) = '$selected_year'")->fetch_assoc()['total'];

// Menghitung total sumbangan tahunan
$total_sumbangan_tahun_ini = $conn->query("SELECT SUM(Zakat_Profesi + Zakat_Maal + Infaq + Sedekah + Fidyah) AS total FROM Sumbangan WHERE YEAR(Tgl) = '$selected_year'")->fetch_assoc()['total'];

// Menghitung total donatur
$total_donatur = $conn->query("SELECT COUNT(*) AS total FROM Donatur")->fetch_assoc()['total'];

// Menghitung total kotak amal
$total_kotak_amal = $conn->query("SELECT COUNT(*) AS total FROM KotakAmal")->fetch_assoc()['total'];

// Menghitung total sumbangan ZIS dari Donatur
$total_sumbangan_donatur = $conn->query("SELECT SUM(Zakat_Profesi + Zakat_Maal + Infaq + Sedekah + Fidyah) AS total FROM Sumbangan")->fetch_assoc()['total'];

// Menghitung total dana yang diambil dari Kotak Amal
$total_dana_kotak_amal = $conn->query("SELECT SUM(JmlUang) AS total FROM Dana_KotakAmal")->fetch_assoc()['total'];

// Menghitung total sumbangan keseluruhan (Donatur + Kotak Amal)
$total_sumbangan = $total_sumbangan_donatur + $total_dana_kotak_amal;

// LOGIC BARU UNTUK SIDEBAR
$id_user = $_SESSION['id_user'] ?? '';
$user_info_sql = "SELECT Nama_User, Foto FROM User WHERE Id_user = '$id_user'";
$user_info = $conn->query($user_info_sql)->fetch_assoc();
$nama_user = $user_info['Nama_User'] ?? 'Pengguna';
$foto_user = $user_info['Foto'] ?? '';
$base_url = "http://" . $_SERVER['HTTP_HOST'] . "/lksa_nh/"; // Definisikan $base_url
$foto_path = $foto_user ? $base_url . 'assets/img/' . $foto_user : $base_url . 'assets/img/yayasan.png'; // Use Yayasan logo as default if none
$sidebar_total_lksa = $conn->query("SELECT COUNT(*) AS total FROM LKSA")->fetch_assoc()['total'];
$sidebar_total_user = $conn->query("SELECT COUNT(*) AS total FROM User")->fetch_assoc()['total'];

// Menetapkan variabel $sidebar_stats untuk digunakan di header.php
$sidebar_stats = '
<div class="sidebar-stats-card card-lksa">
    <h4>Total LKSA Terdaftar</h4>
    <p>' . number_format($sidebar_total_lksa) . '</p>
</div>
<div class="sidebar-stats-card card-user" style="border-left-color: #3498db;">
    <h4>Total Pengguna Sistem</h4>
    <p>' . number_format($sidebar_total_user) . '</p>
</div>
<div class="sidebar-stats-card card-sumbangan" style="border-left-color: #9b59b6;">
    <h4>Total Sumbangan ZIS Global</h4>
    <p>Rp ' . number_format($total_sumbangan_donatur) . '</p>
</div>
<div class="sidebar-stats-card card-kotak-amal" style="border-left-color: #e67e22;">
    <h4>Total Dana Kotak Amal Global</h4>
    <p>Rp ' . number_format($total_dana_kotak_amal) . '</p>
</div>
';

include 'includes/header.php'; // <-- LOKASI BARU
?>
<p>Anda memiliki akses penuh ke seluruh data dan fitur di sistem.</p>
<h2>Ringkasan Statistik Global</h2>
<div class="stats-grid">
    <div class="stats-card card-donatur">
        <i class="fas fa-hand-holding-heart"></i>
        <h3>Jumlah Donatur</h3>
        <span class="value"><?php echo number_format($total_donatur); ?></span>
    </div>
    <div class="stats-card card-sumbangan">
        <i class="fas fa-sack-dollar"></i>
        <h3>Total Sumbangan Donatur</h3>
        <span class="value">Rp <?php echo number_format($total_sumbangan_donatur); ?></span>
    </div>
    <div class="stats-card card-kotak-amal">
        <i class="fas fa-box"></i>
        <h3>Total Kotak Amal</h3>
        <span class="value"><?php echo number_format($total_kotak_amal); ?></span>
    </div>
    <div class="stats-card card-sumbangan-kotak-amal">
        <i class="fas fa-coins"></i>
        <h3>Total Sumbangan Kotak Amal</h3>
        <span class="value">Rp <?php echo number_format($total_dana_kotak_amal); ?></span>
    </div>
    <div class="stats-card card-lksa">
        <i class="fas fa-building"></i>
        <h3>Total Lembaga LKSA</h3>
        <span class="value"><?php echo $sidebar_total_lksa; ?></span>
    </div>
    <div class="stats-card card-user">
        <i class="fas fa-users"></i>
        <h3>Total Pengguna</h3>
        <span class="value"><?php echo $sidebar_total_user; ?></span>
    </div>
</div>

<h2>Sumbangan Berdasarkan Periode</h2>
<form method="POST" action="">
    <div style="display: flex; gap: 10px; margin-bottom: 20px; justify-content: flex-end; align-items: center;">
        <label for="bulan">Pilih Bulan:</label>
        <select name="bulan" id="bulan">
            <?php
            $bulan_indonesia = [
                '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
                '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
                '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
            ];
            foreach ($bulan_indonesia as $num => $name) {
                $selected = ($num == $selected_month) ? 'selected' : '';
                echo "<option value='$num' $selected>$name</option>";
            }
            ?>
        </select>

        <label for="tahun">Pilih Tahun:</label>
        <select name="tahun" id="tahun">
            <?php
            $current_year = date('Y');
            for ($i = $current_year; $i >= $current_year - 5; $i--) {
                $selected = ($i == $selected_year) ? 'selected' : '';
                echo "<option value='$i' $selected>$i</option>";
            }
            ?>
        </select>
        <button type="submit" class="btn btn-primary">Tampilkan</button>
    </div>
</form>

<div class="stats-grid">
    <div class="stats-card card-sumbangan">
        <i class="fas fa-calendar-alt"></i>
        <h3>Sumbangan Bulan Terpilih</h3>
        <span class="value">Rp <?php echo number_format($total_sumbangan_bulan_ini); ?></span>
    </div>
    <div class="stats-card card-sumbangan">
        <i class="fas fa-chart-line"></i>
        <h3>Sumbangan Tahun Terpilih</h3>
        <span class="value">Rp <?php echo number_format($total_sumbangan_tahun_ini); ?></span>
    </div>
    <div class="stats-card card-total">
        <i class="fas fa-donate"></i>
        <h3>Total Sumbangan Keseluruhan</h3>
        <span class="value">Rp <?php echo number_format($total_sumbangan); ?></span>
    </div>
</div>
<?php
include 'includes/footer.php';
$conn->close();
?>