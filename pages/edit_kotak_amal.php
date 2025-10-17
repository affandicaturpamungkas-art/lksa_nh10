<?php
session_start();
include '../config/database.php';

// Ambil data sesi dengan penanganan untuk mencegah Undefined array key warning
$jabatan_user = $_SESSION['jabatan'] ?? '';
$id_ka_session = $_SESSION['id_kotak_amal'] ?? '';
$is_pemilik_ka_logged_in = isset($_SESSION['is_pemilik_kotak_amal']) && $_SESSION['is_pemilik_kotak_amal'] === true;

// Tentukan jenis pengguna
$is_admin_or_employee = in_array($jabatan_user, ['Pimpinan', 'Kepala LKSA', 'Petugas Kotak Amal']);

// --- 1. Authorization and ID Determination ---
if (!$is_admin_or_employee && !$is_pemilik_ka_logged_in) {
    die("Akses ditolak.");
}

$id_kotak_amal_to_edit = '';
if ($is_pemilik_ka_logged_in) {
    // Pemilik Kotak Amal hanya mengedit Kotak Amal mereka
    $id_kotak_amal_to_edit = $id_ka_session; 

    // Keamanan: Jika Pemilik KA mencoba mengedit ID lain via GET, paksa redirect
    if (isset($_GET['id']) && $_GET['id'] !== $id_kotak_amal_to_edit) {
        header("Location: edit_kotak_amal.php?id=" . $id_kotak_amal_to_edit);
        exit;
    }

} else {
    // Admin/Pegawai menggunakan parameter GET
    $id_kotak_amal_to_edit = $_GET['id'] ?? '';
}

if (empty($id_kotak_amal_to_edit)) {
    die("ID Kotak Amal tidak ditemukan.");
}

// Set ID Kotak Amal yang akan digunakan di query dan form
$id_kotak_amal = $id_kotak_amal_to_edit;

// Set sidebar_stats ke string kosong agar tidak ada error jika diakses oleh admin
$sidebar_stats = ''; 

// Memanggil header.php untuk layout admin/karyawan. Non-internal user (Pemilik KA) tidak menggunakan layout ini.
if (!$is_pemilik_ka_logged_in) {
    include '../includes/header.php';
}
// Ambil data kotak amal dari database
$sql = "SELECT * FROM KotakAmal WHERE ID_KotakAmal = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $id_kotak_amal);
$stmt->execute();
$result = $stmt->get_result();
$data_kotak_amal = $result->fetch_assoc();
$stmt->close();

if (!$data_kotak_amal) {
    die("Data kotak amal tidak ditemukan.");
}

// Persiapan untuk layout Pemilik KA yang minimal
$page_title = $is_pemilik_ka_logged_in ? 'Edit Data Kotak Amal Anda' : 'Edit Kotak Amal';
if ($is_pemilik_ka_logged_in) {
    $base_url = "http://" . $_SERVER['HTTP_HOST'] . "/lksa_nh/";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Kotak Amal</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --ka-accent: #e67e22; /* Orange */
            --ka-secondary-bg: #f9e9d9;
            --text-dark: #2c3e50;
        }
        body {
            background-image: url('../assets/img/bg.png');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            font-family: 'Open Sans', sans-serif;
            color: #34495e;
        }
        .form-container {
            max-width: 900px; 
            margin: 50px auto;
            padding: 40px;
            background-color: #fff;
            border-radius: 20px;
            box-shadow: 0 15px 50px rgba(0,0,0,0.1); 
        }
        .form-section {
            padding: 15px 0; 
            margin-bottom: 30px;
            border-top: 1px solid #f0f0f0;
        }
        .form-section:first-of-type { border-top: none; }
        .form-section h2 {
            border-bottom: 2px solid var(--ka-accent);
            padding-bottom: 10px;
            color: var(--ka-accent);
            font-size: 1.5em;
            margin-bottom: 20px;
            font-weight: 700;
            font-family: 'Montserrat', sans-serif;
        }
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px 30px;
        }
        .form-group label {
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 4px;
            display: block;
            font-size: 0.9em;
        }
        .form-group input, .form-group select, .form-group textarea {
            padding: 12px;
            border: 1px solid #e0e0e0;
            border-radius: 10px; 
            width: 100%;
            box-sizing: border-box;
            background-color: #fafafa;
            font-size: 0.95em;
        }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
            border-color: var(--ka-accent);
            outline: none;
            box-shadow: 0 0 8px rgba(230, 126, 34, 0.4); 
            background-color: #fff;
        }
        .form-actions {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
            margin-top: 40px;
        }
        .btn {
            padding: 12px 30px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
            transition: transform 0.2s, box-shadow 0.2s;
            color: white;
            font-size: 1em;
            display: inline-block;
        }
        .btn-success { background-color: var(--ka-accent); }
        .btn-cancel { background-color: #95a5a6; }
        .btn-success:hover { background-color: #cf6717; transform: translateY(-3px); box-shadow: 0 6px 15px rgba(230, 126, 34, 0.3); }
        .btn-cancel:hover { background-color: #7f8c8d; transform: translateY(-3px); box-shadow: 0 6px 15px rgba(149, 165, 166, 0.3); }
        .foto-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
            border: 2px dashed var(--ka-accent); 
            border-radius: 15px;
            background-color: var(--ka-secondary-bg); 
            margin-top: 20px;
        }
        .foto-preview {
            width: 120px; 
            height: 120px;
            object-fit: cover;
            border-radius: 50%;
            border: 5px solid #fff; 
            box-shadow: 0 0 15px rgba(0,0,0,0.2); 
            margin-bottom: 20px;
        }
        .upload-group { text-align: center; width: 100%; max-width: 400px; }
        .upload-group input[type="file"] { border: none; padding: 10px 0; background-color: transparent; }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #fff;
            padding: 20px 30px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            margin-bottom: 20px;
        }
        .header h1 {
            font-family: 'Montserrat', sans-serif;
            font-size: 1.3em;
            font-weight: 700;
            margin: 0;
        }
        .form-container h1 {
            font-family: 'Montserrat', sans-serif;
            font-size: 2.0em;
            color: var(--text-dark);
            margin-bottom: 30px;
        }
        .content { padding: 20px; width: 100%; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 style="text-align: left;"><i class="fas fa-edit" style="color: var(--ka-accent);"></i> Edit Data Kotak Amal</h1>
            <a href="dashboard_pemilik_kotak_amal.php" class="btn btn-cancel" style="background-color: #95a5a6; color: white;">
                <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
            </a>
        </div>
<?php
} else {
    // Jika login sebagai Admin/Pegawai, gunakan layout default
    echo '<div class="main-content-area" style="flex-grow: 1;">';
    echo '<h1 class="dashboard-title">' . htmlspecialchars($page_title) . ' (Admin View)</h1>';
}
?>

<div class="content" style="padding: 0; background: none; box-shadow: none;">
    <div class="form-container">
        <h1><?php echo htmlspecialchars($page_title); ?></h1>
        <form action="proses_edit_kotak_amal.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id_kotak_amal"
                value="<?php echo htmlspecialchars($data_kotak_amal['ID_KotakAmal']); ?>">
            <input type="hidden" name="foto_lama" value="<?php echo htmlspecialchars($data_kotak_amal['Foto']); ?>">

            <div class="form-section">
                <h2><i class="fas fa-box"></i> Informasi Kotak Amal</h2>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Nama Toko:</label>
                        <input type="text" name="nama_toko"
                            value="<?php echo htmlspecialchars($data_kotak_amal['Nama_Toko']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Alamat Toko:</label>
                        <input type="text" name="alamat_toko"
                            value="<?php echo htmlspecialchars($data_kotak_amal['Alamat_Toko']); ?>" required>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h2><i class="fas fa-map-marker-alt"></i> Lokasi Peta (Koordinat)</h2>
                <p>Saat ini hanya menampilkan input, tidak ada peta interaktif di halaman Edit.</p>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Latitude:</label>
                        <input type="text" name="latitude"
                            value="<?php echo htmlspecialchars($data_kotak_amal['Latitude'] ?? ''); ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label>Longitude:</label>
                        <input type="text" name="longitude"
                            value="<?php echo htmlspecialchars($data_kotak_amal['Longitude'] ?? ''); ?>" readonly>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h2><i class="fas fa-user-tag"></i> Informasi Pemilik</h2>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Nama Pemilik:</label>
                        <input type="text" name="nama_pemilik"
                            value="<?php echo htmlspecialchars($data_kotak_amal['Nama_Pemilik']); ?>">
                    </div>
                    <div class="form-group">
                        <label>Nomor WA Pemilik:</label>
                        <input type="text" name="wa_pemilik"
                            value="<?php echo htmlspecialchars($data_kotak_amal['WA_Pemilik']); ?>">
                    </div>
                    <div class="form-group">
                        <label>Email Pemilik:</label>
                        <input type="email" name="email_pemilik"
                            value="<?php echo htmlspecialchars($data_kotak_amal['Email']); ?>">
                    </div>
                    <div class="form-group">
                        <label>Jadwal Pengambilan:</label>
                        <select name="jadwal_pengambilan">
                            <option value="">-- Pilih Hari --</option>
                            <?php
                            $hari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
                            $jadwal_saat_ini = $data_kotak_amal['Jadwal_Pengambilan'];
                            foreach ($hari as $h) {
                                $selected = ($h == $jadwal_saat_ini) ? 'selected' : '';
                                echo "<option value='{$h}' {$selected}>{$h}</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>Keterangan:</label>
                    <textarea name="keterangan" rows="4"
                        cols="50"><?php echo htmlspecialchars($data_kotak_amal['Ket'] ?? ''); ?></textarea>
                </div>
            </div>

            <div class="form-section">
                <h2><i class="fas fa-image"></i> Foto Kotak Amal</h2>
                <div class="foto-container">
                    <?php if ($data_kotak_amal['Foto']) { ?>
                        <img src="../assets/img/<?php echo htmlspecialchars($data_kotak_amal['Foto']); ?>" alt="Foto Kotak Amal" class="foto-preview">
                    <?php } else { ?>
                        <div class="foto-preview" style="display: flex; justify-content: center; align-items: center; background-color: #f7f7f7;">
                            <i class="fas fa-camera" style="font-size: 50px; color: #ccc;"></i>
                        </div>
                    <?php } ?>
                    
                    <div class="upload-group">
                        <label>Unggah Foto Baru (Max 5MB, JPG/PNG/GIF):</label>
                        <input type="file" name="foto" accept="image/*">
                        <small style="color: #7f8c8d; display: block; margin-top: 5px;">Kosongkan jika tidak ingin mengubah foto.</small>
                    </div>
                </div>
            </div>
            
            <div class="form-actions">
                <a href="<?php echo $is_pemilik_ka_logged_in ? 'dashboard_pemilik_kotak_amal.php' : 'kotak-amal.php'; ?>" class="btn btn-cancel"><i class="fas fa-times-circle"></i> Batal</a>
                <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<?php
if ($is_pemilik_ka_logged_in) {
    echo '</div></body></html>';
} else {
    // Menutup div main-content-area yang dibuka di awal blok else
    echo '</div>'; 
    include '../includes/footer.php';
}
$conn->close();
?>