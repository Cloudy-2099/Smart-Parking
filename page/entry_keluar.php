<?
include "../config/database.php";

// Ambil UID dari parameter GET
if (isset($_GET['uid'])) {
    $uid = $con->real_escape_string($_GET['uid']); // Amankan inputan UID
    // Query cek UID di database
    $sql = "SELECT * FROM rfid_card WHERE token = '$uid'";
    $hasil = $con->query($sql);
    $row = $hasil->fetch_assoc();
    $nama = $row['nama'];
    if ($hasil->num_rows > 0) {
        $sql = "SELECT 
            aktivitas.id,
            aktivitas.token, 
            rfid_card.plat_nomor,
            rfid_card.nama, 
            aktivitas.status, 
            aktivitas.jam_masuk,
            Aktivitas.jam_keluar,
            aktivitas.lokasi,
            aktivitas.nilai
        FROM aktivitas
        INNER JOIN rfid_card ON aktivitas.token = rfid_card.token where aktivitas.token = '$uid' 
        ORDER BY jam_masuk DESC LIMIT 1";
        $result = $con->query($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            date_default_timezone_set('Asia/Jakarta'); // Set zona waktu ke Jakarta
            $jam_keluar = date("Y-m-d H:i:s");
            $status_terakhir = $row['status'];
            $lokasi = $row['lokasi'];
            $plat_nomor = $row['plat_nomor'];
            $jam_keluar_db = $row['jam_keluar'];
            $nilai = $row['nilai'];
            $id_aktivitas = $row['id'];

            if ($status_terakhir == "Masuk" && is_null($jam_keluar_db)) {
                // Proses keluar apapun nilai sebelumnya
                $run = "UPDATE aktivitas 
                SET status = 'Keluar', jam_keluar = '$jam_keluar', nilai = '0' 
                WHERE id = '$id_aktivitas'";

                if ($con->query($run) === TRUE) {
                    $response = [
                        "status" => "Diterima",
                        "uid" => $uid,
                        "jam_keluar" => $jam_keluar,
                        "plat_nomor" => $plat_nomor,
                        "lokasi" => $lokasi,
                        "nilai" => ($nilai === '1') ? '0' : '0'  // Tetap kirim 'nilai' meskipun sudah 0
                    ];
                    echo json_encode($response);
                } else {
                    echo json_encode(["status" => "error", "message" => "Gagal menyimpan data"]);
                }
            } else {
                echo json_encode(["status" => "error", "message" => "User sudah keluar
                .: $uid"]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "tidak terdapat aktivitas masuk pada UID: $uid"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "UID Tidak terdaftar"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "UID tidak dikirim"]);
}

$con->close();
