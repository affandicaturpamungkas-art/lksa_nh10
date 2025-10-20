<?php
$base_url = "http://" . $_SERVER['HTTP_HOST'] . "/lksa_nh/";

$current_page = basename($_SERVER['PHP_SELF']);
$current_dir = basename(dirname($_SERVER['PHP_SELF']));
if ($current_dir == 'lksa_nh') {
    $current_page = 'index.php';
}

$dashboard_active = ($current_page == 'index.php' || strpos($current_page, 'dashboard_') !== false) ? 'active' : '';
$lksa_active = ($current_page == 'lksa.php' || $current_page == 'tambah_lksa.php' || $current_page == 'tambah_pimpinan.php') ? 'active' : '';
$users_active = ($current_page == 'users.php' || $current_page == 'tambah_pengguna.php' || $current_page == 'edit_pengguna.php') ? 'active' : '';
$donatur_active = ($current_page == 'donatur.php' || $current_page == 'tambah_donatur.php' || $current_page == 'edit_donatur.php') ? 'active' : '';
$sumbangan_active = ($current_page == 'sumbangan.php' || $current_page == 'tambah_sumbangan.php' || $current_page == 'detail_sumbangan.php') ? 'active' : '';
$verifikasi_active = ($current_page == 'verifikasi-donasi.php' || $current_page == 'edit_sumbangan.php' || $current_page == 'wa-blast-form.php') ? 'active' : '';
$kotak_amal_active = ($current_page == 'kotak-amal.php' || $current_page == 'tambah_kotak_amal.php' || $current_page == 'edit_kotak_amal.php') ? 'active' : '';
$dana_kotak_amal_active = ($current_page == 'dana-kotak-amal.php') ? 'active' : '';
$laporan_active = ($current_page == 'laporan.php' || $current_page == 'tambah_laporan.php') ? 'active' : ''; // Tambahkan Laporan

// --- SIDEBAR LOGIC ---
$show_sidebar = false;
$sidebar_html = '';
$is_internal_user = false;

if (isset($_SESSION['loggedin']) && isset($_SESSION['id_user'])) {

    // Check if $conn is defined (it is defined in all page/dashboard files before header.php is included)
    if (isset($conn)) {
        $id_user = $_SESSION['id_user'];
        $user_info_sql = "SELECT Nama_User, Foto, Jabatan FROM User WHERE Id_user = '$id_user'";
        $user_info = $conn->query($user_info_sql)->fetch_assoc();
        $nama_user = $user_info['Nama_User'] ?? 'Pengguna';
        $foto_user = $user_info['Foto'] ?? '';
        $jabatan = $user_info['Jabatan'] ?? '';
        $is_internal_user = true;
        $foto_path = $foto_user ? $base_url . 'assets/img/' . $foto_user : $base_url . 'assets/img/yayasan.png';

        // $sidebar_stats di-set di setiap file dashboard/manajemen
        $sidebar_stats = $sidebar_stats ?? '';

        // Tampilkan sidebar di semua halaman utama (dashboard dan menu manajemen)
        // PERBAIKAN: Sertakan 'index.php' secara eksplisit
        if ($current_page == 'index.php' || $current_dir == 'dashboards' || in_array($current_page, ['lksa.php', 'users.php', 'donatur.php', 'sumbangan.php', 'kotak-amal.php', 'verifikasi-donasi.php', 'dana-kotak-amal.php', 'tambah_pimpinan.php', 'tambah_pengguna.php', 'tambah_donatur.php', 'tambah_kotak_amal.php', 'tambah_sumbangan.php', 'laporan.php', 'tambah_laporan.php'])) {
            // Tambahkan pengecualian agar halaman dashboard_donatur.php dll tidak memakai layout ini
            if ($current_page != 'dashboard_donatur.php' && $current_page != 'dashboard_pemilik_kotak_amal.php') {
                $show_sidebar = true;
            }
        }

        if ($show_sidebar) {
            // Use output buffering to capture the sidebar HTML
            ob_start();
            ?>
            <div class="sidebar-wrapper">
                <img src="<?php echo htmlspecialchars($foto_path); ?>" alt="Foto Profil" class="profile-img">

                <p class="welcome-text-sidebar">Selamat Datang,<br>
                    <strong><?php echo htmlspecialchars($nama_user); ?> (<?php echo htmlspecialchars($jabatan); ?>)</strong>
                </p>

                <a href="<?php echo $base_url; ?>pages/edit_pengguna.php?id=<?php echo htmlspecialchars($id_user); ?>"
                    class="btn btn-primary"><i class="fas fa-edit"></i> Edit Profil</a>
                <a href="<?php echo $base_url; ?>login/logout.php" class="btn btn-danger"><i class="fas fa-sign-out-alt"></i>
                    Logout</a>

                <?php if ($jabatan != 'Pimpinan') { // <-- PERUBAHAN DITAMBAHKAN DI SINI ?>
                <a href="<?php echo $base_url; ?>pages/tambah_laporan.php" class="btn btn-warning"
                    style="margin-top: 20px; background-color: #F97316; color: white;">
                    <i class="fas fa-bullhorn"></i> Lapor ke Atasan
                </a>
                <?php } // <-- PERUBAHAN DITAMBAHKAN DI SINI ?>

                <hr>

                <?php if (!empty($sidebar_stats)) { ?>
                    <h2>Ringkasan <?php echo htmlspecialchars($jabatan); ?></h2>
                    <?php echo $sidebar_stats; ?>
                <?php } ?>

            </div>
            <?php
            $sidebar_html = ob_get_clean();
        }
    }
}
// --- END SIDEBAR LOGIC ---
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Informasi ZIS dan Kotak Amal</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&family=Open+Sans:wght@400;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary-color: #1F2937; /* Dark Navy/Slate (Base/Dark) */
            --secondary-color: #06B6D4; /* Aqua/Cyan (Accent/Highlight) */
            --tertiary-color: #F9FAFB; /* Soft Background (Baru) */
            --text-dark: #1F2937; /* Dark Slate Gray for general text */
            --text-light: #fff;
            --bg-light: #F9FAFB; /* Soft Background (Baru) */
            --border-color: #E5E7EB; /* Light border */
            --form-bg: #FFFFFF;
        }

        body {
            font-family: 'Open Sans', sans-serif;
            background-color: var(--bg-light);
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            color: var(--text-dark);
            min-height: 100vh;
            overflow-x: hidden;
        }

        .container {
            width: 100%;
            max-width: 1200px; /* Diperkecil */
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            padding: 15px 25px; /* Dikecilkan */
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: var(--form-bg);
            border-radius: 15px;
            margin-bottom: 15px; /* Dikecilkan */
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        }

        .header h1 {
            margin: 0;
            font-size: 1.4em; /* Dikecilkan */
            color: var(--primary-color);
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
        }

        .content {
            padding: 30px 40px; /* Dikecilkan untuk simetri */
            background-color: var(--form-bg);
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
            /* NEW: Re-enable original style and add flex for sidebar */
            margin-top: 15px; /* Dikecilkan */
            display: flex;
            gap: 30px; /* Dikecilkan */
            align-items: flex-start;
        }

        .btn {
            padding: 10px 20px; /* Dikecilkan */
            border: none;
            cursor: pointer;
            text-decoration: none;
            border-radius: 8px; /* Dikecilkan */
            font-weight: 600;
            transition: transform 0.2s, box-shadow 0.2s;
            display: inline-block;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.05);
        }

        .btn-primary {
            background: var(--primary-color);
            color: var(--text-light);
        }

        .btn-success {
            background: #10B981; /* Emerald Green */
            color: white;
        }
        
        .btn-warning {
            background: #F97316; /* Orange/Warning */
            color: white;
        }

        .btn-danger {
            background: #EF4444; /* Red */
            color: white;
        }

        .btn-cancel {
            background: #6B7280; /* Gray-500 */
            color: white;
        }

        .btn:hover {
            transform: translateY(-2px); /* Dikecilkan */
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); /* Dikecilkan */
        }
        
        /* Removed unused .summary-card styles */

        .dashboard-title {
            font-size: 2.0em; /* Dikecilkan */
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 10px; /* Dikecilkan */
            font-family: 'Montserrat', sans-serif;
            border-bottom: 2px solid var(--border-color);
            padding-bottom: 8px; /* Dikecilkan */
        }

        .welcome-text {
            font-size: 1.2em; /* Dikecilkan */
            font-weight: 600;
            color: #555;
            margin-top: 0;
            margin-bottom: 20px; /* Dikecilkan */
        }

        .top-nav {
            display: flex;
            /* **PERUBAHAN KRITIS: Mengganti nowrap ke wrap dan menghilangkan overflow-x** */
            flex-wrap: wrap; /* Izinkan item turun ke baris baru */
            overflow-x: hidden; /* Pastikan tidak ada scrollbar horizontal */
            justify-content: center; /* Rata tengah agar terlihat rapi saat wrap */
            /* Rata kiri untuk scrollable menu */
            gap: 8px; /* Dikecilkan */
            background-color: var(--primary-color);
            padding: 8px 15px; /* Dikecilkan */
            border-radius: 10px; /* Dikecilkan */
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); /* Dikecilkan */
            margin-bottom: 15px; /* Dikecilkan */

            /* SCROLLBAR SEKARANG DITAMPILKAN SEBAGAI PENANDA */
        }

        /* Hapus semua aturan penyembunyi scrollbar: -ms-overflow-style, scrollbar-width, dan .top-nav::-webkit-scrollbar */

        .nav-item {
            flex-shrink: 0;
            /* Mencegah item menciut */
            text-decoration: none;
            color: var(--text-light);
            padding: 10px 15px; /* Dikecilkan */
            font-weight: 600;
            border-radius: 8px; /* Dikecilkan */
            transition: background-color 0.3s, transform 0.2s;
            font-size: 0.9em; /* Dikecilkan */
        }

        .nav-item:hover {
            background-color: rgba(255, 255, 255, 0.15); /* Slightly brighter hover */
            transform: translateY(-1px); /* Dikecilkan */
        }

        .nav-item.active {
            background-color: var(--secondary-color); /* Aqua/Cyan */
            color: var(--primary-color);
            font-weight: 700;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px; /* Dikecilkan */
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05); /* Dikecilkan */
            border-radius: 10px; /* Dikecilkan */
            overflow: hidden;
            border: 1px solid var(--border-color);
            font-size: 0.95em; /* Dikecilkan */
        }

        th,
        td {
            text-align: left;
            padding: 12px; /* Dikecilkan */
            border-bottom: 1px solid var(--border-color); 
        }

        thead tr {
            background-color: var(--primary-color); /* Dark header */
            color: var(--text-light);
            font-weight: 600;
            border-bottom: 2px solid var(--secondary-color);
        }
        
        thead th:first-child {
            border-top-left-radius: 10px;
        }
        
        thead th:last-child {
            border-top-right-radius: 10px;
        }

        tbody tr:nth-child(even) {
            background-color: #FDFDFD; 
        }
        
        tbody tr:hover {
            background-color: #F3F4F6; /* Light gray on hover */
        }
        
        /* Ensure the last row does not have a bottom border if it's the only one */
        tbody tr:last-child td {
            border-bottom: none;
        }

        .form-container {
            background-color: var(--form-bg);
            padding: 30px; /* Dikecilkan */
            border-radius: 12px; /* Dikecilkan */
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            max-width: 700px; /* Dikecilkan */
            margin: 0 auto;
        }

        .form-section {
            margin-bottom: 25px; /* Dikecilkan */
        }

        .form-section h2 {
            border-bottom: 2px solid var(--secondary-color); /* Aqua/Cyan under header */
            padding-bottom: 8px; /* Dikecilkan */
            margin-bottom: 15px; /* Dikecilkan */
            color: var(--primary-color);
            font-weight: 700;
            font-family: 'Montserrat', sans-serif;
            font-size: 1.4em;
        }

        .form-group {
            margin-bottom: 15px; /* Dikecilkan */
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px; /* Dikecilkan */
            border: 1px solid var(--border-color);
            border-radius: 8px; /* Dikecilkan */
            box-sizing: border-box;
            font-size: 0.95em; /* Dikecilkan */
            font-family: 'Open Sans', sans-serif;
            transition: border-color 0.3s;
        }

        .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
            border-color: var(--secondary-color); /* Highlight on focus */
            box-shadow: 0 0 0 3px rgba(6, 182, 212, 0.3); /* Adjusted for Aqua/Cyan */
            outline: none;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); /* Dikecilkan */
            gap: 15px; /* Dikecilkan */
        }

        .form-actions {
            display: flex;
            gap: 10px; /* Dikecilkan */
            justify-content: flex-end;
            margin-top: 25px; /* Dikecilkan */
        }
        
        /* Removed .highlight-card style */

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); /* Dikecilkan */
            gap: 20px; /* Dikecilkan */
            margin-bottom: 25px; /* Dikecilkan */
        }
        
        /* --- NEW STYLES FOR STATS CARD ELEGANCE --- */
        .stats-card {
            background-color: #fff;
            border-radius: 12px;
            padding: 20px;
            text-align: left; /* Layout Horizontal */
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s, box-shadow 0.3s;
            border: 1px solid var(--border-color); 
            border-left: 5px solid; /* Use left border for color accent */
            display: flex;
            flex-direction: row; 
            justify-content: space-between;
            align-items: center;
        }

        .stats-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
        }

        .stats-card i {
            font-size: 2.5em; /* Ikon besar */
            margin-bottom: 0;
            flex-shrink: 0;
            padding-right: 15px;
            opacity: 0.8; /* Sedikit transparan */
        }
        
        .stats-card-content {
            flex-grow: 1;
            text-align: right; /* Angka di kanan */
        }

        .stats-card h3 {
            margin: 0 0 5px 0;
            font-size: 0.9em; 
            color: #555; /* Warna redup untuk judul */
            font-weight: 600;
        }

        .stats-card .value {
            font-size: 1.8em; /* Angka besar dan menonjol */
            font-weight: 800;
            margin: 0;
            line-height: 1.1;
        }
        
        /* New large total card style */
        .stats-card-total-large {
            background-color: var(--primary-color); /* Deep Navy background */
            color: var(--text-light);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            text-align: center;
            margin-top: 15px;
            border: none;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .stats-card-total-large h3 {
            color: var(--text-light);
            font-size: 1.4em;
            margin-bottom: 5px;
        }
        .stats-card-total-large i {
            font-size: 3.0em;
            color: var(--secondary-color); /* Aqua/Cyan */
            margin-bottom: 10px;
        }
        .stats-card-total-large .value {
            color: var(--secondary-color); /* Aqua/Cyan highlight untuk total */
            font-size: 3.0em; 
            font-weight: 900;
            margin-top: 5px;
        }
        /* --- END NEW STYLES --- */


        /* NEW CARD COLOR SCHEME */
        /* Aksen: Aqua/Deep Navy/Emerald/Indigo/Orange */
        .card-lksa { border-color: #06B6D4; } .card-lksa .value { color: #06B6D4; } .card-lksa i { color: #06B6D4; }
        .card-user { border-color: #1F2937; } .card-user .value { color: #1F2937; } .card-user i { color: #1F2937; }
        .card-donatur { border-color: #10B981; } .card-donatur .value { color: #10B981; } .card-donatur i { color: #10B981; } /* Emerald Green */
        .card-sumbangan { border-color: #6366F1; } .card-sumbangan .value { color: #6366F1; } .card-sumbangan i { color: #6366F1; } /* Indigo */
        .card-kotak-amal { border-color: #F97316; } .card-kotak-amal .value { color: #F97316; } .card-kotak-amal i { color: #F97316; } /* Orange */
        .card-total { border-color: #EF4444; } .card-total .value { color: #EF4444; } .card-total i { color: #EF4444; }

        /* === NEW SIDEBAR STYLES (Disesuaikan untuk Layout 1 Kolom Utama) === */
        .sidebar-wrapper {
            width: 220px; /* Dikecilkan */
            flex-shrink: 0;
            padding: 15px 0; /* Dikecilkan */
            text-align: center;
            border-right: 1px solid var(--border-color);
            padding-right: 20px; /* Dikecilkan */
        }

        .main-content-area {
            flex-grow: 1;
            /* Konten utama dashboard */
        }

        .profile-img {
            width: 100px; /* Dikecilkan */
            height: 100px; /* Dikecilkan */
            object-fit: cover;
            border-radius: 50%;
            border: 4px solid var(--secondary-color); /* Dikecilkan */
            margin-bottom: 10px; /* Dikecilkan */
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
        }

        .welcome-text-sidebar {
            font-size: 1.0em; /* Dikecilkan */
            font-weight: 600;
            margin: 5px 0 15px 0; /* Dikecilkan */
            color: var(--primary-color);
        }

        .sidebar-stats-card {
            background-color: #F8FBFD; /* Very light background */
            padding: 12px; /* Dikecilkan */
            border-radius: 8px; /* Dikecilkan */
            margin-top: 10px; /* Dikecilkan */
            border-left: 4px solid var(--primary-color); /* Dikecilkan */
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .sidebar-stats-card h4 {
            margin: 0 0 4px 0; /* Dikecilkan */
            font-size: 0.85em; /* Dikecilkan */
            color: #555;
        }

        .sidebar-stats-card p {
            margin: 0;
            font-size: 1.3em; /* Dikecilkan */
            font-weight: 700;
            color: var(--primary-color);
        }

        .sidebar-wrapper .btn {
            width: 100%;
            margin-top: 8px; /* Dikecilkan */
            font-size: 0.9em; /* Dikecilkan */
        }

        .sidebar-wrapper hr {
            margin: 15px 0; /* Dikecilkan */
            border: 0;
            border-top: 1px solid var(--border-color);
        }
        
        /* === MEDIA QUERIES UNTUK RESPONSIVENESS === */
        
        /* Perubahan utama untuk tablet (768px - 1024px) */
        @media (max-width: 1024px) {
            .content {
                gap: 20px; /* Dikecilkan */
                padding: 20px; /* Dikecilkan */
            }
            .sidebar-wrapper {
                width: 180px; /* Dikecilkan */
                padding-right: 15px; /* Dikecilkan */
            }
            
            .main-content-area {
                overflow-x: auto; /* Memungkinkan gulir horizontal untuk konten lebar (misalnya tabel) */
                padding-bottom: 5px; 
            }
        }
        
        /* Perubahan untuk perangkat mobile (di bawah 768px) */
        @media (max-width: 768px) {
            /* Konten utama menjadi satu kolom vertikal */
            .content {
                flex-direction: column;
                padding: 15px; /* Dikecilkan */
                gap: 15px; /* Dikecilkan */
            }

            /* Sidebar mengambil lebar penuh di atas */
            .sidebar-wrapper {
                width: 100%;
                padding-right: 0;
                border-right: none; /* Hapus garis pemisah vertikal */
                border-bottom: 1px solid var(--border-color); /* Tambah garis bawah */
                padding-bottom: 15px; /* Dikecilkan */
                margin-bottom: 15px; /* Dikecilkan */
            }
            
            /* Konten utama mengambil lebar penuh di bawah */
            .main-content-area {
                width: 100%;
                overflow-x: auto; /* Memastikan tabel bisa di-scroll di mobile */
            }

            /* Tombol-tombol di sidebar dibuat lebih lebar */
            .sidebar-wrapper .btn {
                max-width: 100%;
                margin-left: 0;
                margin-right: 0;
            }

            /* Tata letak statistik di sidebar diubah menjadi vertikal penuh */
            .sidebar-stats-card {
                display: block; 
                width: 100%; 
                margin-top: 8px; /* Dikecilkan */
            }
            
            .sidebar-wrapper h2 {
                margin-top: 8px; /* Dikecilkan */
                border-bottom: none;
                padding-bottom: 0;
            }
            
            .top-nav {
                padding: 8px; /* Padding menu navigasi lebih kecil */
            }
        }
        /* END NEW SIDEBAR STYLES */
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <div style="display: flex; align-items: center; gap: 10px;"> <img src="<?php echo $base_url; ?>assets/img/yayasan.png" alt="Logo Yayasan"
                    style="width: 60px; height: auto;"> <h1>Sistem Informasi ZIS dan Kotak Amal</h1>
            </div>
        </div>
        <div class="top-nav">
            <a href="<?php echo $base_url; ?>index.php" class="nav-item <?php echo $dashboard_active; ?>">Dashboard</a>
            <?php if ($_SESSION['jabatan'] == 'Pimpinan' && $_SESSION['id_lksa'] == 'Pimpinan_Pusat') { ?>
                <a href="<?php echo $base_url; ?>pages/lksa.php" class="nav-item <?php echo $lksa_active; ?>">Manajemen
                    LKSA</a>
            <?php } ?>
            <?php if ($_SESSION['jabatan'] == 'Pimpinan' || $_SESSION['jabatan'] == 'Kepala LKSA') { ?>
                <a href="<?php echo $base_url; ?>pages/users.php" class="nav-item <?php echo $users_active; ?>">Manajemen
                    Pengguna</a>
            <?php } ?>
            <?php if ($_SESSION['jabatan'] == 'Pimpinan' || $_SESSION['jabatan'] == 'Kepala LKSA' || $_SESSION['jabatan'] == 'Pegawai') { ?>
                <a href="<?php echo $base_url; ?>pages/donatur.php"
                    class="nav-item <?php echo $donatur_active; ?>">Manajemen Donatur ZIS</a>
                <a href="<?php echo $base_url; ?>pages/sumbangan.php"
                    class="nav-item <?php echo $sumbangan_active; ?>">Manajemen Sumbangan</a>
                <?php if ($_SESSION['jabatan'] == 'Pimpinan' || $_SESSION['jabatan'] == 'Kepala LKSA') { ?>
                    <a href="<?php echo $base_url; ?>pages/verifikasi-donasi.php"
                        class="nav-item <?php echo $verifikasi_active; ?>">Verifikasi Donasi</a>
                <?php } ?>
            <?php } ?>
            <?php if ($_SESSION['jabatan'] == 'Pimpinan' || $_SESSION['jabatan'] == 'Kepala LKSA' || $_SESSION['jabatan'] == 'Petugas Kotak Amal') { ?>
                <a href="<?php echo $base_url; ?>pages/kotak-amal.php"
                    class="nav-item <?php echo $kotak_amal_active; ?>">Manajemen Kotak Amal</a>
                <a href="<?php echo $base_url; ?>pages/dana-kotak-amal.php"
                    class="nav-item <?php echo $dana_kotak_amal_active; ?>">Pengambilan Kotak Amal</a>
            <?php } ?>
            <?php if ($_SESSION['jabatan'] == 'Pimpinan' || $_SESSION['jabatan'] == 'Kepala LKSA') { ?>
                <a href="<?php echo $base_url; ?>pages/laporan.php" class="nav-item <?php echo $laporan_active; ?>">Laporan
                    Pengguna</a>
            <?php } ?>
        </div>

        <?php if ($show_sidebar) { ?>
            <div class="content">
                <?php echo $sidebar_html; ?>
                <div class="main-content-area">
                <?php } else { ?>
                    <div class="content">
                    <?php } ?>