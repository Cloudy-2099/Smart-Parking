<?php
header('Content-Type: application/json'); // Pastikan response dalam format JSON
include '../config/database.php'; // Sesuaikan dengan lokasi file konfigurasi database

// Koneksi ke database
if ($con->connect_error) {
    die(json_encode(["error" => "Koneksi database gagal"]));
}

// Query untuk mendapatkan status parkiran
$sql = "SELECT 
            aktivitas.token, 
            rfid_card.plat_nomor,
            rfid_card.nama, 
            aktivitas.status, 
            aktivitas.jam_masuk,
            aktivitas.lokasi,
            aktivitas.nilai
        FROM aktivitas
        INNER JOIN rfid_card ON aktivitas.token = rfid_card.token
        WHERE aktivitas.nilai = 1";  // Hanya ambil data dengan nilai = 1

$result = $con->query($sql);

$parkir_status = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $lokasi = trim($row['lokasi']);
        $status = trim($row['status']);
        $uid = trim($row['token']);
        $plat = trim($row['plat_nomor']);
        $nilai = trim($row['nilai']);
        if (!empty($lokasi) && $lokasi !== "" && $nilai !== "0") { // Hanya tambahkan jika lokasi tidak kosong
            $parkir_status[$lokasi] = [
                "status" => trim($row['status']), // Bersihkan spasi
                "uid" => trim($row['token']), // Bersihkan spasi
                "plat" => trim($row['plat_nomor']), // Bersihkan spasi
            ];
        }
        if (empty($uid)) {
            $status = "Ditolak";
        }
    }
}

// Tutup koneksi
$con->close();

// Kirim data dalam format JSON
echo json_encode($parkir_status);
