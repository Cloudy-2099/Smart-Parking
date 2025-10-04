<?php
include "../config/database.php";

if (isset($_GET['uid'])) {
    $uid = $con->real_escape_string($_GET['uid']);

    // Cari UID dan ambil data user
    $sql_user = "SELECT * FROM rfid_card WHERE token = '$uid'";
    $result_user = $con->query($sql_user);

    if ($result_user->num_rows > 0) {
        $user = $result_user->fetch_assoc();
        $nama = $user['nama'];
        $saldo = $user['saldo'];

        // Set waktu dan tarif masuk
        date_default_timezone_set('Asia/Jakarta');
        $jam_masuk = date("Y-m-d H:i:s");
        $harga_masuk = 3000;

        // Cek saldo mencukupi
        if ($saldo >= $harga_masuk) {
            $saldo_baru = $saldo - $harga_masuk;

            // Insert aktivitas sesuai query milikmu
            $run = "INSERT INTO aktivitas (token, status, jam_masuk, nilai) 
                    VALUES ('$uid', 'Masuk', '$jam_masuk', '0') 
                    ON DUPLICATE KEY UPDATE 
                    status = 'Masuk', jam_masuk = '$jam_masuk'";

            // Update saldo user
            $update_saldo = "UPDATE rfid_card SET saldo = '$saldo_baru' WHERE token = '$uid'";

            if ($con->query($run) === TRUE && $con->query($update_saldo) === TRUE) {
                echo json_encode([
                    "status" => "success",
                    "message" => "Akses diterima, saldo dipotong",
                    "nama" => $nama,
                    "uid" => $uid,
                    "jam_masuk" => $jam_masuk,
                    "harga_masuk" => $harga_masuk,
                    "saldo_tersisa" => $saldo_baru
                ]);
            } else {
                echo json_encode([
                    "status" => "error",
                    "message" => "Gagal menyimpan aktivitas atau update saldo"
                ]);
            }
        } else {
            echo json_encode([
                "status" => "ditolak",
                "message" => "Saldo tidak mencukupi",
                "saldo" => $saldo,
                "minimum" => $harga_masuk
            ]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "UID tidak terdaftar"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "UID tidak dikirim"]);
}

$con->close();
