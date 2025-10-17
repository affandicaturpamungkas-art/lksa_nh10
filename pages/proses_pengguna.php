<?php
session_start();
include '../config/database.php';

if ($_SESSION['jabatan'] != 'Pimpinan' && $_SESSION['jabatan'] != 'Kepala LKSA') {
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

    $unique_filename = uniqid('user_') . '.' . $file_extension;
    $target_file = $target_dir . $unique_filename;

    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return ['filename' => $unique_filename];
    } else {
        return ['error' => "Maaf, terjadi kesalahan saat mengunggah file Anda."];
    }
}

function generate_user_id($conn, $jabatan, $id_lksa) {
    $parts = explode('_NH_', $id_lksa);
    $daerah = $parts[0];

    $prefix_user = '';
    switch ($jabatan) {
        case 'Kepala LKSA':
            $prefix_user = "KEPALA_LKSA_" . $daerah . "_NH_";
            break;
        case 'Pegawai':
            $prefix_user = "PEGAWAI_" . $daerah . "_NH_";
            break;
        case 'Petugas Kotak Amal':
            $prefix_user = "PETUGAS_KA_" . $daerah . "_NH_";
            break;
        default:
            $prefix_user = "USER_" . $daerah . "_NH_";
            break;
    }

    $count_sql = "SELECT COUNT(*) AS total FROM User WHERE `Id_user` LIKE '{$prefix_user}%'";
    $count_result = $conn->query($count_sql);
    $count_row = $count_result->fetch_assoc();
    $counter = $count_row['total'] + 1;
    $id_user = $prefix_user . str_pad($counter, 3, '0', STR_PAD_LEFT);
    
    return $id_user;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'] ?? '';
    $nama_user = $_POST['nama_user'] ?? '';
    $jabatan = $_POST['jabatan'] ?? '';
    $id_lksa = $_POST['id_lksa'] ?? '';
    $foto_path = null;

    $check_lksa_sql = "SELECT Id_lksa FROM LKSA WHERE Id_lksa = ?";
    $check_lksa_stmt = $conn->prepare($check_lksa_sql);
    $check_lksa_stmt->bind_param("s", $id_lksa);
    $check_lksa_stmt->execute();
    $check_lksa_result = $check_lksa_stmt->get_result();
    
    if ($check_lksa_result->num_rows === 0) {
        die("Error: ID LKSA tidak ditemukan. Silakan masukkan ID LKSA yang valid.");
    }
    $check_lksa_stmt->close();
    
    if (!empty($_FILES['foto']['name'])) {
        $upload_result = handle_upload($_FILES['foto']);
        if (isset($upload_result['error'])) {
            die($upload_result['error']);
        }
        $foto_path = $upload_result['filename'];
    }

    if ($action == 'tambah') {
        $password = $_POST['password'] ?? '';
        $id_user = generate_user_id($conn, $jabatan, $id_lksa);

        $sql = "INSERT INTO User (Id_user, Nama_User, Password, Jabatan, Id_lksa, Foto) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $id_user, $nama_user, $password, $jabatan, $id_lksa, $foto_path);
        
        if ($stmt->execute()) {
            header("Location: users.php");
        } else {
            echo "Error: " . $stmt->error;
        }

    } elseif ($action == 'edit') {
        $id_user = $_POST['id_user'] ?? '';
        $foto_lama = $_POST['foto_lama'] ?? '';
        $sql = "UPDATE User SET Nama_User = ?, Jabatan = ?, Id_lksa = ?, Foto = ? WHERE Id_user = ?";
        
        $final_foto_path = $foto_path ?: $foto_lama;

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $nama_user, $jabatan, $id_lksa, $final_foto_path, $id_user);

        if ($stmt->execute()) {
            header("Location: users.php");
        } else {
            echo "Error: " . $stmt->error;
        }
    }

    $stmt->close();
}

$conn->close();
exit;
?>