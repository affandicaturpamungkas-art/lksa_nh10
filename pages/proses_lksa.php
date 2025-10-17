<?php
session_start();
include '../config/database.php';

if ($_SESSION['jabatan'] != 'Pimpinan') {
    die("Akses ditolak.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Data dari Form LKSA
    $nama_lksa = $_POST['nama_lksa'] ?? '';
    $alamat_lksa = $_POST['alamat_lksa'] ?? '';
    $nomor_wa_lksa = $_POST['nomor_wa_lksa'] ?? '';
    $nama_pimpinan_lksa = $_POST['nama_pimpinan_lksa'] ?? '';
    $email_lksa = $_POST['email_lksa'] ?? '';

    // Data dari Form Kepala LKSA
    $nama_user_kepala_lksa = $_POST['nama_user_kepala_lksa'] ?? '';
    $password_kepala_lksa = $_POST['password_kepala_lksa'] ?? '';

    // Logika untuk membuat ID LKSA yang unik
    $prefix = preg_replace('/[^a-zA-Z0-9]/', '', str_replace(' ', '_', $nama_lksa));
    $prefix = strtoupper(substr($prefix, 0, 10)); // Batasi panjang prefix
    
    $counter_sql = "SELECT COUNT(*) AS total FROM LKSA WHERE Id_lksa LIKE '{$prefix}_NH_%'";
    $result = $conn->query($counter_sql);
    $row = $result->fetch_assoc();
    $counter = $row['total'] + 1;
    $id_lksa = $prefix . "_NH_" . str_pad($counter, 3, '0', STR_PAD_LEFT);

    // Langkah 1: Masukkan data LKSA baru
    $lksa_sql = "INSERT INTO LKSA (Id_lksa, Alamat, `Nomor WA`, `Nama Pimpinan`, Email, `Tanggal Daftar`) VALUES (?, ?, ?, ?, ?, ?)";
    $lksa_stmt = $conn->prepare($lksa_sql);
    $tgl_daftar = date('Y-m-d');
    $lksa_stmt->bind_param("ssssss", $id_lksa, $alamat_lksa, $nomor_wa_lksa, $nama_pimpinan_lksa, $email_lksa, $tgl_daftar);
    
    if (!$lksa_stmt->execute()) {
        die("Error saat menambahkan LKSA: " . $lksa_stmt->error);
    }
    $lksa_stmt->close();

    // Langkah 2: Masukkan data Kepala LKSA baru
    $user_sql = "INSERT INTO User (Id_lksa, `Nama User`, Jabatan, Password) VALUES (?, ?, 'Kepala LKSA', ?)";
    $user_stmt = $conn->prepare($user_sql);
    $user_stmt->bind_param("sss", $id_lksa, $nama_user_kepala_lksa, $password_kepala_lksa);

    if (!$user_stmt->execute()) {
        die("Error saat menambahkan Kepala LKSA: " . $user_stmt->error);
    }
    $user_stmt->close();
    
    header("Location: lksa.php");
    exit;

}

$conn->close();
?>