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

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Informasi ZIS dan Kotak Amal</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Lato:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary-color: #2c3e50; /* Dark Blue */
            --secondary-color: #e7b10a; /* Gold */
            --tertiary-color: #f8f9fa; /* Very Light Gray */
            --text-dark: #34495e;
            --text-light: #fff;
            --bg-light: #f0f2f5;
            --border-color: #e0e0e0;
            --form-bg: #fff;
        }
        body {
            font-family: 'Lato', sans-serif;
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
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            padding: 20px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: var(--form-bg);
            border-radius: 15px;
            margin-bottom: 20px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        }
        .header h1 {
            margin: 0;
            font-size: 1.5em;
            color: var(--primary-color);
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
        }
        .content {
            padding: 40px;
            background-color: var(--form-bg);
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        }
        .btn {
            padding: 12px 25px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
            transition: transform 0.2s, box-shadow 0.2s;
            display: inline-block;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }
        .btn-primary { background: var(--primary-color); color: var(--text-light); }
        .btn-success { background: #28a745; color: white; }
        .btn-danger { background: #dc3545; color: white; }
        .btn-cancel { background: #6c757d; color: white; }
        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(0,0,0,0.1);
        }
        .summary-card {
            background-color: var(--tertiary-color);
            padding: 40px;
            border-radius: 15px;
            text-align: center;
            flex: 1;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .summary-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }
        .summary-card h3 {
            color: var(--text-dark);
            margin-top: 0;
            font-size: 1.2em;
            font-weight: 600;
            font-family: 'Lato', sans-serif;
        }
        .summary-card h1 {
            color: var(--secondary-color);
            font-size: 3.5em;
            margin: 10px 0 0 0;
            font-weight: 700;
            font-family: 'Poppins', sans-serif;
        }
        .stats-container {
            display: flex;
            justify-content: space-around;
            text-align: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 25px;
        }
        .dashboard-title {
            font-size: 2.8em;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 10px;
            font-family: 'Poppins', sans-serif;
        }
        .welcome-text {
            font-size: 1.6em;
            font-weight: 600;
            color: #555;
            margin-top: 0;
        }
        .top-nav {
            display: flex;
            /* **PERBAIKAN KRITIS UNTUK SATU BARIS DAN SCROLLING** */
            flex-wrap: nowrap; 
            overflow-x: auto; 
            overflow-y: hidden;
            justify-content: flex-start; /* Rata kiri untuk scrollable menu */
            gap: 12px;
            background-color: var(--primary-color);
            padding: 12px 25px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            
            /* SCROLLBAR SEKARANG DITAMPILKAN SEBAGAI PENANDA */
        }
        /* Hapus semua aturan penyembunyi scrollbar: -ms-overflow-style, scrollbar-width, dan .top-nav::-webkit-scrollbar */
        
        .nav-item {
            flex-shrink: 0; /* Mencegah item menciut */
            text-decoration: none;
            color: var(--text-light);
            padding: 15px 20px;
            font-weight: 600;
            border-radius: 10px;
            transition: background-color 0.3s, transform 0.2s;
        }
        .nav-item:hover {
            background-color: rgba(255, 255, 255, 0.1);
            transform: translateY(-2px);
        }
        .nav-item.active {
            background-color: var(--secondary-color);
            color: var(--primary-color);
            font-weight: 700;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            border-radius: 15px;
            overflow: hidden;
        }
        th, td {
            text-align: left;
            padding: 15px;
            border-bottom: 1px solid #e0e0e0;
        }
        thead tr {
            background-color: var(--primary-color);
            color: white;
            font-weight: 600;
        }
        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .form-container {
            background-color: var(--form-bg);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            max-width: 800px;
            margin: 0 auto;
        }
        .form-section {
            margin-bottom: 30px;
        }
        .form-section h2 {
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 10px;
            margin-bottom: 20px;
            color: var(--primary-color);
            font-weight: 700;
            font-family: 'Poppins', sans-serif;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #555;
        }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 14px;
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            box-sizing: border-box;
            font-size: 1em;
            font-family: 'Lato', sans-serif;
        }
        .form-actions {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
            margin-top: 30px;
        }
        .highlight-card {
            background-color: var(--secondary-color);
            color: var(--primary-color);
        }
        .highlight-card h3, .highlight-card h1 {
            color: var(--primary-color);
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }
        .stats-card {
            background-color: #fff;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            transition: transform 0.3s, box-shadow 0.3s;
            border-left: 5px solid;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .stats-card i {
            font-size: 2.5em;
            margin-bottom: 15px;
        }
        .stats-card h3 {
            margin: 0 0 10px 0;
            font-size: 1.1em;
            color: #2c3e50;
        }
        .stats-card .value {
            font-size: 2.5em;
            font-weight: 700;
            margin: 0;
        }
        .card-lksa { border-color: #e7b10a; }
        .card-lksa .value { color: #e7b10a; }
        .card-lksa i { color: #e7b10a; }
        .card-user { border-color: #3498db; }
        .card-user .value { color: #3498db; }
        .card-user i { color: #3498db; }
        .card-donatur { border-color: #2ecc71; }
        .card-donatur .value { color: #2ecc71; }
        .card-donatur i { color: #2ecc71; }
        .card-sumbangan { border-color: #9b59b6; }
        .card-sumbangan .value { color: #9b59b6; }
        .card-sumbangan i { color: #9b59b6; }
        .card-kotak-amal { border-color: #e67e22; }
        .card-kotak-amal .value { color: #e67e22; }
        .card-kotak-amal i { color: #e67e22; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div style="display: flex; align-items: center; gap: 15px;">
                <img src="<?php echo $base_url; ?>assets/img/yayasan.png" alt="Logo Yayasan" style="width: 70px; height: auto;">
                <h1>Sistem Informasi ZIS dan Kotak Amal</h1>
            </div>
            <div>
                <a href="<?php echo $base_url; ?>login/logout.php" class="btn btn-danger">Logout</a>
            </div>
        </div>
        <div class="top-nav">
            <a href="<?php echo $base_url; ?>index.php" class="nav-item <?php echo $dashboard_active; ?>">Dashboard</a>
            <?php if ($_SESSION['jabatan'] == 'Pimpinan' && $_SESSION['id_lksa'] == 'Pimpinan_Pusat') { ?>
                <a href="<?php echo $base_url; ?>pages/lksa.php" class="nav-item <?php echo $lksa_active; ?>">Manajemen LKSA</a>
                <a href="<?php echo $base_url; ?>pages/tambah_pimpinan.php" class="nav-item <?php echo ($current_page == 'tambah_pimpinan.php') ? 'active' : ''; ?>">Tambah Pimpinan Baru</a>
            <?php } ?>
            <?php if ($_SESSION['jabatan'] == 'Pimpinan' || $_SESSION['jabatan'] == 'Kepala LKSA') { ?>
                <a href="<?php echo $base_url; ?>pages/users.php" class="nav-item <?php echo $users_active; ?>">Manajemen Pengguna</a>
            <?php } ?>
            <?php if ($_SESSION['jabatan'] == 'Pimpinan' || $_SESSION['jabatan'] == 'Kepala LKSA' || $_SESSION['jabatan'] == 'Pegawai') { ?>
                <a href="<?php echo $base_url; ?>pages/donatur.php" class="nav-item <?php echo $donatur_active; ?>">Manajemen Donatur ZIS</a>
                <a href="<?php echo $base_url; ?>pages/sumbangan.php" class="nav-item <?php echo $sumbangan_active; ?>">Manajemen Sumbangan</a>
                <?php if ($_SESSION['jabatan'] == 'Pimpinan' || $_SESSION['jabatan'] == 'Kepala LKSA') { ?>
                    <a href="<?php echo $base_url; ?>pages/verifikasi-donasi.php" class="nav-item <?php echo $verifikasi_active; ?>">Verifikasi Donasi</a>
                <?php } ?>
            <?php } ?>
            <?php if ($_SESSION['jabatan'] == 'Pimpinan' || $_SESSION['jabatan'] == 'Kepala LKSA' || $_SESSION['jabatan'] == 'Petugas Kotak Amal') { ?>
                <a href="<?php echo $base_url; ?>pages/kotak-amal.php" class="nav-item <?php echo $kotak_amal_active; ?>">Manajemen Kotak Amal</a>
                <a href="<?php echo $base_url; ?>pages/dana-kotak-amal.php" class="nav-item <?php echo $dana_kotak_amal_active; ?>">Pengambilan Kotak Amal</a>
            <?php } ?>
        </div>