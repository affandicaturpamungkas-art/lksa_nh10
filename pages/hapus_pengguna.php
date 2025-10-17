<?php
session_start();
include '../config/database.php';

if ($_SESSION['jabatan'] != 'Pimpinan' && $_SESSION['jabatan'] != 'Kepala LKSA') {
    die("Akses ditolak.");
}

$id_user = $_GET['id'] ?? '';

if ($id_user) {
    // Ambil jabatan dan ID LKSA dari pengguna yang akan dihapus
    $get_user_sql = "SELECT Jabatan, Id_lksa, Foto FROM User WHERE Id_user = ?";
    $get_user_stmt = $conn->prepare($get_user_sql);
    $get_user_stmt->bind_param("s", $id_user);
    $get_user_stmt->execute();
    $user_result = $get_user_stmt->get_result();
    $user_row = $user_result->fetch_assoc();
    $get_user_stmt->close();

    if ($user_row) {
        // Hapus file foto jika ada
        if ($user_row['Foto']) {
            $file_path = "C:/xampp/htdocs/lksa_nh/assets/img/" . $user_row['Foto'];
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }
        
        // Cek apakah pengguna yang dihapus adalah seorang Pimpinan
        if ($user_row['Jabatan'] == 'Pimpinan') {
            $id_lksa_to_delete = $user_row['Id_lksa'];
            // Hapus data LKSA yang terkait
            $delete_lksa_sql = "DELETE FROM LKSA WHERE Id_lksa = ?";
            $delete_lksa_stmt = $conn->prepare($delete_lksa_sql);
            $delete_lksa_stmt->bind_param("s", $id_lksa_to_delete);
            if (!$delete_lksa_stmt->execute()) {
                die("Error saat menghapus data LKSA: " . $delete_lksa_stmt->error);
            }
            $delete_lksa_stmt->close();
        }

        // Hapus data pengguna dari database
        $sql = "DELETE FROM User WHERE Id_user = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $id_user);
        
        if ($stmt->execute()) {
            header("Location: users.php");
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    }
} else {
    header("Location: users.php");
}

$conn->close();
exit;
?>