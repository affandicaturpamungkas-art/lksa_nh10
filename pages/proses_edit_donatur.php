<?php
session_start();
include '../config/database.php';

// Fungsi untuk mengunggah foto
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

    $unique_filename = uniqid('donatur_') . '.' . $file_extension;
    $target_file = $target_dir . $unique_filename;

    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return ['filename' => $unique_filename];
    } else {
        return ['error' => "Maaf, terjadi kesalahan saat mengunggah file Anda."];
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Mengambil data dari form
    $id_donatur = $_POST['id_donatur'] ?? '';
    $nama_donatur = $_POST['nama_donatur'] ?? '';
    $no_wa = $_POST['no_wa'] ?? '';
    $email = $_POST['email'] ?? '';
    $alamat_lengkap = $_POST['alamat_lengkap'] ?? '';
    $status_donasi = $_POST['status_donasi'] ?? '';
    $foto_path = null;
    $foto_lama = $_POST['foto_lama'] ?? null;
    
    // Tangani unggahan foto baru
    if (!empty($_FILES['foto']['name'])) {
        $upload_result = handle_upload($_FILES['foto']);
        if (isset($upload_result['error'])) {
            die($upload_result['error']);
        }
        $foto_path = $upload_result['filename'];

        // Hapus foto lama jika ada
        if ($foto_lama && file_exists("C:/xampp/htdocs/lksa_nh/assets/img/" . $foto_lama)) {
            unlink("C:/xampp/htdocs/lksa_nh/assets/img/" . $foto_lama);
        }
    } else {
        // Jika tidak ada foto baru, gunakan foto lama
        $foto_path = $foto_lama;
    }

    // Kueri SQL untuk memperbarui data donatur
    $sql = "UPDATE Donatur SET Nama_Donatur = ?, NO_WA = ?, Email = ?, Alamat_Lengkap = ?, Status = ?, Foto = ? WHERE ID_donatur = ?";
    $stmt = $conn->prepare($sql);
    
    // Periksa jika prepare berhasil
    if ($stmt === false) {
        die("Error saat menyiapkan kueri: " . $conn->error);
    }
    
    $stmt->bind_param("sssssss", $nama_donatur, $no_wa, $email, $alamat_lengkap, $status_donasi, $foto_path, $id_donatur);

    if ($stmt->execute()) {
        header("Location: donatur.php?status=success");
        exit;
    } else {
        die("Error saat memperbarui data donatur: " . $stmt->error);
    }
} else {
    header("Location: donatur.php");
    exit;
}

$conn->close();
?>