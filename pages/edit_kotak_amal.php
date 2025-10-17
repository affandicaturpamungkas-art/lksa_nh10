<?php
session_start();
include '../config/database.php';
include '../includes/header.php';

// Verifikasi otorisasi: Hanya Pimpinan, Kepala LKSA, dan Petugas Kotak Amal yang bisa mengakses
if ($_SESSION['jabatan'] != 'Pimpinan' && $_SESSION['jabatan'] != 'Kepala LKSA' && $_SESSION['jabatan'] != 'Petugas Kotak Amal') {
    die("Akses ditolak.");
}

$id_kotak_amal = $_GET['id'] ?? '';
if (empty($id_kotak_amal)) {
    die("ID Kotak Amal tidak ditemukan.");
}

// Ambil data kotak amal dari database
$sql = "SELECT * FROM KotakAmal WHERE ID_KotakAmal = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $id_kotak_amal);
$stmt->execute();
$result = $stmt->get_result();
$data_kotak_amal = $result->fetch_assoc();

if (!$data_kotak_amal) {
    die("Data kotak amal tidak ditemukan.");
}

?>
<div class="content">
    <div class="form-container">
        <h1>Edit Kotak Amal</h1>
        <form action="proses_edit_kotak_amal.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id_kotak_amal"
                value="<?php echo htmlspecialchars($data_kotak_amal['ID_KotakAmal']); ?>">
            <input type="hidden" id="latitude" name="latitude"
                value="<?php echo htmlspecialchars($data_kotak_amal['Latitude'] ?? ''); ?>">
            <input type="hidden" id="longitude" name="longitude"
                value="<?php echo htmlspecialchars($data_kotak_amal['Longitude'] ?? ''); ?>">

            <div class="form-section">
                <h2>Informasi Kotak Amal</h2>
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

                <div class="form-group">
                    <label>Pilih Lokasi di Peta:</label>
                    <div id="map" style="height: 400px; width: 100%;"></div>
                    <small>Geser marker atau klik peta untuk menempatkan lokasi baru.</small>
                </div>
            </div>

            <div class="form-section">
                <h2>Informasi Pemilik</h2>
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
            </div>

            <div class="form-section">
                <h2>Informasi Lainnya</h2>
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
                <div class="form-group">
                    <label>Keterangan:</label>
                    <textarea name="keterangan" rows="4"
                        cols="50"><?php echo htmlspecialchars($data_kotak_amal['Ket'] ?? ''); ?></textarea>
                </div>
                <div class="form-group">
                    <label>Foto Saat Ini:</label>
                    <?php if ($data_kotak_amal['Foto']) { ?>
                        <img src="../assets/img/<?php echo htmlspecialchars($data_kotak_amal['Foto']); ?>" alt="Foto Kotak Amal"
                            style="width: 100px; height: 100px; object-fit: cover;">
                    <?php } else { ?>
                        <p>Belum ada foto.</p>
                    <?php } ?>
                </div>
                <div class="form-group">
                    <input type="hidden" name="foto_lama" value="<?php echo htmlspecialchars($data_kotak_amal['Foto']); ?>">
                    <label>Unggah Foto Baru:</label>
                    <input type="file" name="foto" accept="image/*">
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-success">Simpan Perubahan</button>
                <a href="kotak-amal.php" class="btn btn-cancel">Batal</a>
            </div>
        </form>
    </div>
</div>

<script async defer src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&callback=initMap"></script>
<script>
    let map;
    let marker;

    function initMap() {
        const defaultLat = parseFloat(document.getElementById('latitude').value) || -7.5583;
        const defaultLng = parseFloat(document.getElementById('longitude').value) || 110.8252;
        const defaultPos = { lat: defaultLat, lng: defaultLng };

        map = new google.maps.Map(document.getElementById("map"), {
            zoom: 12,
            center: defaultPos,
        });

        marker = new google.maps.Marker({
            position: defaultPos,
            map: map,
            draggable: true,
        });

        // Update koordinat saat marker dipindah
        marker.addListener('dragend', function () {
            const pos = marker.getPosition();
            document.getElementById('latitude').value = pos.lat().toFixed(8);
            document.getElementById('longitude').value = pos.lng().toFixed(8);
        });

        // Klik peta untuk pindahkan marker
        map.addListener('click', function (event) {
            marker.setPosition(event.latLng);
            document.getElementById('latitude').value = event.latLng.lat().toFixed(8);
            document.getElementById('longitude').value = event.latLng.lng().toFixed(8);
        });
    }
</script>

<?php
include '../includes/footer.php';
$conn->close();
?>