<?php
session_start();
include '../config/database.php';
// include '../includes/header.php'; // Pindahkan ke bawah

// Authorization check: Hanya Pimpinan, Kepala LKSA, dan Petugas Kotak Amal yang bisa mengakses
if ($_SESSION['jabatan'] != 'Pimpinan' && $_SESSION['jabatan'] != 'Kepala LKSA' && $_SESSION['jabatan'] != 'Petugas Kotak Amal') {
    die("Akses ditolak.");
}

// Ambil ID pengguna dan LKSA dari sesi
$id_user = $_SESSION['id_user'];
$id_lksa = $_SESSION['id_lksa'];

$sidebar_stats = ''; // Pastikan sidebar tampil

include '../includes/header.php'; // LOKASI BARU
?>
<div class="form-container">
    <h1>Tambah Kotak Amal Baru</h1>
    <form action="proses_kotak_amal.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id_user" value="<?php echo htmlspecialchars($id_user); ?>">
        <input type="hidden" name="id_lksa" value="<?php echo htmlspecialchars($id_lksa); ?>">

        <div class="form-section">
            <h2>Informasi Toko</h2>
            <div class="form-grid">
                <div class="form-group">
                    <label>Nama Toko:</label>
                    <input type="text" name="nama_toko" required>
                </div>
                <div class="form-group">
                    <label>Alamat Toko:</label>
                    <input type="text" name="alamat_toko" required>
                </div>
            </div>
        </div>

        <div class="form-section">
            <h2>Pilih Lokasi di Peta</h2>
            <div class="form-group">
                <label>Pilih Lokasi dengan Menandai di Peta:</label>
                <div id="map" style="height: 400px; width: 100%;"></div>
                <small>Geser marker atau klik peta untuk menempatkan lokasi.</small>
            </div>
            <div class="form-grid">
                <div class="form-group">
                    <label>Latitude:</label>
                    <input type="text" id="latitude" name="latitude" readonly required>
                </div>
                <div class="form-group">
                    <label>Longitude:</label>
                    <input type="text" id="longitude" name="longitude" readonly required>
                </div>
            </div>
        </div>

        <div class="form-section">
            <h2>Informasi Pemilik</h2>
            <div class="form-grid">
                <div class="form-group">
                    <label>Nama Pemilik:</label>
                    <input type="text" name="nama_pemilik">
                </div>
                <div class="form-group">
                    <label>Nomor WA Pemilik:</label>
                    <input type="text" name="wa_pemilik">
                </div>
            </div>
            <div class="form-group">
                <label>Email Pemilik:</label>
                <input type="email" name="email_pemilik">
            </div>
        </div>
        
        <div class="form-section">
            <h2>Informasi Lainnya</h2>
            <div class="form-grid" style="grid-template-columns: 1fr 1fr;">
                <div class="form-group">
                    <label>Jadwal Pengambilan:</label>
                    <select name="jadwal_pengambilan">
                        <option value="">-- Pilih Hari --</option>
                        <option value="Senin">Senin</option>
                        <option value="Selasa">Selasa</option>
                        <option value="Rabu">Rabu</option>
                        <option value="Kamis">Kamis</option>
                        <option value="Jumat">Jumat</option>
                        <option value="Sabtu">Sabtu</option>
                        <option value="Minggu">Minggu</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Unggah Foto:</label>
                    <input type="file" name="foto" accept="image/*">
                </div>
            </div>
            <div class="form-group">
                <label>Keterangan:</label>
                <textarea name="keterangan" rows="4" cols="50"></textarea>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-success">Simpan Kotak Amal</button>
            <a href="kotak-amal.php" class="btn btn-cancel">Batal</a>
        </div>
    </form>
</div>

<script async defer src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&callback=initMap"></script>
<script>
let map;
let marker;

function initMap() {
    const defaultPos = { lat: -7.5583, lng: 110.8252 }; // Solo sebagai default

    map = new google.maps.Map(document.getElementById("map"), {
        zoom: 12,
        center: defaultPos,
    });

    marker = new google.maps.Marker({
        position: defaultPos,
        map: map,
        draggable: true,
    });

    // Set koordinat awal ke input
    document.getElementById('latitude').value = defaultPos.lat;
    document.getElementById('longitude').value = defaultPos.lng;

    // Update koordinat saat marker dipindah
    marker.addListener('dragend', function() {
        const pos = marker.getPosition();
        document.getElementById('latitude').value = pos.lat().toFixed(8);
        document.getElementById('longitude').value = pos.lng().toFixed(8);
    });

    // Klik peta untuk pindahkan marker
    map.addListener('click', function(event) {
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