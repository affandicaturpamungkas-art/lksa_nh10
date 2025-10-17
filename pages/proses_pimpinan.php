<?php
session_start();
include '../config/database.php';

if ($_SESSION['jabatan'] != 'Pimpinan') {
    die("Akses ditolak.");
}

function handle_upload($file) {
    $target_dir = "C:/xampp/htdocs/lksa_nh/assets/img/";
    $file_extension = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    $allowed_extensions = array("jpg", "jpeg", "png", "gif");

    if (!in_array($file_extension, $allowed_extensions)) {
        return ['error' => "Maaf, hanya file JPG, JPEG, PNG, & GIF yang diizinkan."];
    }

    if ($file["size"] > 5000000) { // 5MB
        return ['error' => "Maaf, ukuran file terlalu besar."];
    }

    $unique_filename = uniqid('pimpinan_') . '.' . $file_extension;
    $target_file = $target_dir . $unique_filename;

    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return ['filename' => $unique_filename];
    } else {
        return ['error' => "Maaf, terjadi kesalahan saat mengunggah file Anda."];
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_user = $_POST['nama_user'] ?? '';
    $password = $_POST['password'] ?? '';
    $jabatan = 'Pimpinan';
    $foto_path = null;

    $alamat = $_POST['alamat'] ?? '';
    $nomor_wa = $_POST['nomor_wa'] ?? '';
    $email = $_POST['email'] ?? '';
    $tgl_daftar = date('Y-m-d');

    // Membuat ID LKSA yang unik dari alamat
    $prefix = preg_replace('/[^a-zA-Z0-9]/', '', str_replace(' ', '_', $alamat));
    $prefix = strtoupper(substr($prefix, 0, 10)); // Batasi panjang prefix

    $counter_sql = "SELECT COUNT(*) AS total FROM LKSA WHERE Id_lksa LIKE '{$prefix}_NH_%'";
    $result = $conn->query($counter_sql);
    $row = $result->fetch_assoc();
    $counter = $row['total'] + 1;
    $id_lksa = $prefix . "_NH_" . str_pad($counter, 3, '0', STR_PAD_LEFT);
    
    // Masukkan data LKSA baru
    $insert_lksa_sql = "INSERT INTO LKSA (Id_lksa, Alamat, Nomor_WA, Nama_Pimpinan, Email, Tanggal_Daftar) VALUES (?, ?, ?, ?, ?, ?)";
    $insert_lksa_stmt = $conn->prepare($insert_lksa_sql);
    if ($insert_lksa_stmt === false) {
        die("Error preparing LKSA insert: " . $conn->error);
    }
    $insert_lksa_stmt->bind_param("ssssss", $id_lksa, $alamat, $nomor_wa, $nama_user, $email, $tgl_daftar);

    if (!$insert_lksa_stmt->execute()) {
        die("Error saat menambahkan ID LKSA: " . $insert_lksa_stmt->error);
    }
    $insert_lksa_stmt->close();
    
    // Proses unggah foto
    if (!empty($_FILES['foto']['name'])) {
        $upload_result = handle_upload($_FILES['foto']);
        if (isset($upload_result['error'])) {
            die($upload_result['error']);
        }
        $foto_path = $upload_result['filename'];
    }

    // Buat ID User yang unik
    $prefix_user = "PIMPINAN_" . $prefix . "_NH_";
    $count_sql = "SELECT COUNT(*) AS total FROM User WHERE Id_user LIKE '{$prefix_user}%'";
    $count_result = $conn->query($count_sql);
    $count_row = $count_result->fetch_assoc();
    $counter = $count_row['total'] + 1;
    $id_user = $prefix_user . str_pad($counter, 3, '0', STR_PAD_LEFT);

    // Masukkan data Pimpinan ke tabel User
    $sql = "INSERT INTO User (Id_user, Nama_User, Password, Jabatan, Id_lksa, Foto) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Error preparing User insert: " . $conn->error);
    }
    $stmt->bind_param("ssssss", $id_user, $nama_user, $password, $jabatan, $id_lksa, $foto_path);
    
    if ($stmt->execute()) {
        header("Location: users.php");
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
exit;
?>