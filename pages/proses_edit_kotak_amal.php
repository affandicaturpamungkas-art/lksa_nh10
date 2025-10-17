<?php
session_start();
include '../config/database.php';

// Fungsi untuk mengunggah file foto
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

    $unique_filename = uniqid('kotak_amal_') . '.' . $file_extension;
    $target_file = $target_dir . $unique_filename;

    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return ['filename' => $unique_filename];
    } else {
        return ['error' => "Maaf, terjadi kesalahan saat mengunggah file Anda."];
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Mengambil data dari form
    $id_kotak_amal = $_POST['id_kotak_amal'] ?? '';
    $nama_toko = $_POST['nama_toko'] ?? '';
    $alamat_toko = $_POST['alamat_toko'] ?? '';
    $nama_pemilik = $_POST['nama_pemilik'] ?? '';
    $wa_pemilik = $_POST['wa_pemilik'] ?? '';
    $email_pemilik = $_POST['email_pemilik'] ?? '';
    // Mengambil nilai dari select
    $jadwal_pengambilan = $_POST['jadwal_pengambilan'] ?? ''; 
    $foto_lama = $_POST['foto_lama'] ?? null;
    $foto_path = $foto_lama;

    // Menangani unggahan foto baru
    if (!empty($_FILES['foto']['name'])) {
        $upload_result = handle_upload($_FILES['foto']);
        if (isset($upload_result['error'])) {
            die($upload_result['error']);
        }
        $foto_path = $upload_result['filename'];
        
        // Hapus foto lama jika ada
        if ($foto_lama) {
            $file_path = "C:/xampp/htdocs/lksa_nh/assets/img/" . $foto_lama;
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }
    }

    // Kueri SQL untuk memperbarui data kotak amal
    // Menggunakan nama kolom dengan garis bawah (_)
    $sql = "UPDATE KotakAmal SET Nama_Toko = ?, Alamat_Toko = ?, Nama_Pemilik = ?, WA_Pemilik = ?, Email = ?, Jadwal_Pengambilan = ?, Foto = ? WHERE ID_KotakAmal = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die("Error saat menyiapkan kueri: " . $conn->error);
    }
    
    $stmt->bind_param("ssssssss", $nama_toko, $alamat_toko, $nama_pemilik, $wa_pemilik, $email_pemilik, $jadwal_pengambilan, $foto_path, $id_kotak_amal);

    if ($stmt->execute()) {
        header("Location: kotak-amal.php");
        exit;
    } else {
        die("Error saat memperbarui data kotak amal: " . $stmt->error);
    }
} else {
    header("Location: edit_kotak_amal.php?id=" . $id_kotak_amal);
    exit;
}

$conn->close();
?>