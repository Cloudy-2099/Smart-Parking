<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../config/database.php';

$json = file_get_contents('php://input');
if (!$json) {
    echo json_encode(["status" => "error", "message" => "Tidak ada data diterima"]);
    exit;
}

$data = json_decode($json, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(["status" => "error", "message" => "Format JSON salah"]);
    exit;
}

$uid = mysqli_real_escape_string($con, strtoupper($data['uid']));
$lokasi = mysqli_real_escape_string($con, $data['lokasi']);

// Cek apakah UID terdaftar
$sql = "SELECT * FROM rfid_card WHERE token = '$uid'";
$hasil = mysqli_query($con, $sql);

if (mysqli_num_rows($hasil) > 0) {
    // Cek aktivitas masuk yang belum keluar
    $cek_aktivitas = "SELECT 
            aktivitas.token, 
            rfid_card.plat_nomor,
            aktivitas.lokasi,
            aktivitas.nilai
        FROM aktivitas
        INNER JOIN rfid_card ON aktivitas.token = rfid_card.token
        WHERE aktivitas.token = '$uid' AND aktivitas.jam_keluar IS NULL";
    $result = mysqli_query($con, $cek_aktivitas);
    $row = mysqli_fetch_assoc($result);

    if ($row) {
        $plat_nomor = $row['plat_nomor'];
        $lokasi_sekarang = $row['lokasi'];
        $lokasi_sebelumnya = $lokasi_sekarang;
        $nilai = $row['nilai'];
        date_default_timezone_set('Asia/Jakarta'); // Set zona waktu ke Jakarta
        $jam_parkir = date('Y-m-d H:i:s'); // Menyimpan waktu saat ini

        if ($lokasi_sekarang == $lokasi) {
            // Tap di lokasi yang sama 
            $keluar = "UPDATE aktivitas 
            SET nilai = '1' 
            WHERE token = '$uid' AND status = 'Masuk' AND jam_keluar IS NULL AND lokasi = '$lokasi' 
            ORDER BY jam_masuk DESC LIMIT 1";

            if (mysqli_query($con, $keluar)) {
                $insert_slot = "INSERT INTO aktivitas_slot (token, slot, jam_parkir) 
                                VALUES ('$uid', '$lokasi', '$jam_parkir')";
                mysqli_query($con, $insert_slot); // Tidak perlu cek error satu per satu
                echo json_encode([
                    "status" => "success",
                    'uid' => $uid,
                    'plat_nomor' => $plat_nomor,
                    'lokasi' => $lokasi,
                    'nilai' => '1'
                ]);
            } else {
                echo json_encode(["status" => "error", "message" => "Gagal mengubah nilai menjadi kosong"]);
            }
        } else if ($lokasi_sekarang == "") {
            // Tap di lokasi yang sama 
            $keluar = "UPDATE aktivitas 
            SET lokasi = '$lokasi', nilai = '1' 
            WHERE token = '$uid' AND status = 'Masuk' AND jam_keluar IS NULL  
            ORDER BY jam_masuk DESC LIMIT 1";

            if (mysqli_query($con, $keluar)) {
                $insert_slot = "INSERT INTO aktivitas_slot (token, slot, jam_parkir) 
                                VALUES ('$uid', '$lokasi', '$jam_parkir')";
                mysqli_query($con, $insert_slot); // Tidak perlu cek error satu per satu
                echo json_encode([
                    "status" => "success",
                    'uid' => $uid,
                    'plat_nomor' => $plat_nomor,
                    'lokasi' => $lokasi,
                    'nilai' => '1'
                ]);
            } else {
                echo json_encode(["status" => "error", "message" => "Gagal mengubah nilai menjadi kosong"]);
            }
        } else if ($nilai != '1') {
            // Tap di saat belum pernah parkir sama sekali
            $keluar = "UPDATE aktivitas 
            SET lokasi = '$lokasi', nilai = '1' 
                        WHERE token = '$uid' AND status = 'Masuk' AND jam_keluar IS NULL
                        ORDER BY jam_masuk DESC LIMIT 1";

            if (mysqli_query($con, $keluar)) {
                $insert_slot = "INSERT INTO aktivitas_slot (token, slot, jam_parkir) 
                                            VALUES ('$uid', '$lokasi', '$jam_parkir')";
                mysqli_query($con, $insert_slot); // Tidak perlu cek error satu per satu
                echo json_encode([
                    "status" => "success",
                    'uid' => $uid,
                    'plat_nomor' => $plat_nomor,
                    'lokasi' => $lokasi,
                    'nilai' => '1'
                ]);
            } else {
                echo json_encode(["status" => "error", "message" => "Gagal mengubah nilai menjadi kosong"]);
            }
        } else if ($lokasi_sekarang != $lokasi) {
            // Tap di lokasi yang sama 
            $keluar = "UPDATE aktivitas 
            SET lokasi = '$lokasi', nilai = '1' 
            WHERE token = '$uid' AND status = 'Masuk' AND jam_keluar IS NULL AND lokasi = '$lokasi' 
            ORDER BY jam_masuk DESC LIMIT 1";

            if (mysqli_query($con, $keluar)) {
                $insert_slot = "INSERT INTO aktivitas_slot (token, slot, jam_parkir) 
                                VALUES ('$uid', '$lokasi', '$jam_parkir')";
                mysqli_query($con, $insert_slot); // Tidak perlu cek error satu per satu
                echo json_encode([
                    "status" => "success",
                    'uid' => $uid,
                    'plat_nomor' => $plat_nomor,
                    'lokasi' => $lokasi,
                    'nilai' => '1'
                ]);
            } else {
                echo json_encode(["status" => "error", "message" => "Gagal mengubah nilai menjadi kosong"]);
            }
        } else {
            // Pindah ke lokasi baru Cek apakah lokasi baru ditempati orang lain
            $cek_lokasi = "SELECT * FROM aktivitas 
                            WHERE lokasi = '$lokasi' 
                            AND status = 'Masuk' 
                            AND jam_keluar IS NULL 
                            AND token != '$uid'";
            $result_lokasi = mysqli_query($con, $cek_lokasi);

            if (mysqli_num_rows($result_lokasi) > 0) {
                // Set nilai slot orang lain menjadi 0 (kosong)
                while ($row_lokasi = mysqli_fetch_assoc($result_lokasi)) {
                    $token_lama = $row_lokasi['token'];
                    $keluar_lama = "UPDATE aktivitas 
                                    SET nilai = '0' 
                                    WHERE token = '$token_lama' 
                                    AND status = 'Masuk' 
                                    AND jam_keluar IS NULL";
                    mysqli_query($con, $keluar_lama); // Tidak perlu cek error satu per satu
                }
            }

            // Update lokasi baru dan set nilainya jadi 1
            $update_lokasi = "UPDATE aktivitas 
            SET lokasi = '$lokasi', nilai = '1' 
            WHERE token = '$uid' AND status = 'Masuk' AND jam_keluar IS NULL 
            ORDER BY jam_masuk DESC LIMIT 1";

            if (mysqli_query($con, $update_lokasi)) {
                $insert_slot = "INSERT INTO aktivitas_slot (token, slot, jam_parkir) 
                                VALUES ('$uid', '$lokasi', '$jam_parkir')";
                mysqli_query($con, $insert_slot); // Tambahkan eksekusi ini
                echo json_encode([
                    "status" => "success",
                    'uid' => $uid,
                    'plat_nomor' => $plat_nomor,
                    'lokasi' => $lokasi,
                    'nilai' => '1'
                ]);
            } else {
                echo json_encode(["status" => "error", "message" => "Gagal memperbarui lokasi ke slot baru"]);
            }
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Tidak ada aktivitas masuk ditemukan"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Kartu tidak terdaftar"]);
}

mysqli_close($con);
