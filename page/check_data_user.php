<?php
include __DIR__ . "/../config/database.php";

if (isset($_POST['token'])) {
    $token = mysqli_real_escape_string($con, $_POST['token']);

    // Cek data user di rfid_card terlebih dahulu
    $user = mysqli_query($con, "SELECT token, nama, nim, plat_nomor, saldo FROM rfid_card WHERE token = '$token'");

    if (mysqli_num_rows($user) > 0) {
        $userData = mysqli_fetch_assoc($user);

        // Cek apakah user ini punya aktivitas parkir terbaru
        $aktivitas = mysqli_query($con, "SELECT status, jam_masuk, jam_keluar, lokasi 
                                         FROM aktivitas 
                                         WHERE token = '$token' 
                                         ORDER BY id DESC 
                                         LIMIT 1");

        if (mysqli_num_rows($aktivitas) > 0) {
            $activityData = mysqli_fetch_assoc($aktivitas);
            echo json_encode([
                'status' => 'success',
                'nama' => $userData['nama'],
                'nim' => $userData['nim'],
                'plat_nomor' => $userData['plat_nomor'],
                'saldo' => $userData['saldo'],
                'lokasi' => $activityData['lokasi'],
                'kondisi' => $activityData['status'],
                'jam_masuk' => $activityData['jam_masuk'],
                'jam_keluar' => $activityData['jam_keluar']
            ]);
        } else {
            // User ditemukan tapi belum ada aktivitas parkir
            echo json_encode([
                'status' => 'success',
                'nama' => $userData['nama'],
                'nim' => $userData['nim'],
                'plat_nomor' => $userData['plat_nomor'],
                'saldo' => $userData['saldo'],
                'lokasi' => "Belum melakukan aktivitas parkir",
                'kondisi' => "Belum melakukan aktivitas parkir",
                'jam_masuk' => "Belum melakukan aktivitas parkir",
                'jam_keluar' => "Belum melakukan aktivitas parkir"
            ]);
        }
    } else {
        echo json_encode(['status' => 'not_found']);
    }
    exit;
}
?>


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Pengguna RFID</title>
    <script src="https://unpkg.com/mqtt/dist/mqtt.min.js"></script>
</head>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Pengecekkan Data User</h1>

                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <div class="content">
        <div class="container-fluid">
            <div id="alert-container"></div>

            <div class="row">
                <div class="col-lg-6">
                    <div class="form-group">
                        <label for="token">Token / UID</label>
                        <input type="text" class="form-control" id="token" placeholder="Scan kartu anda" readonly>
                        <small class="form-text text-muted">Silakan scan kartu RFID anda</small>
                    </div>
                    <div class="form-group">
                        <label for="nama">Nama Lengkap</label>
                        <input type="text" class="form-control" id="nama" placeholder="Nama Lengkap" readonly>
                    </div>
                    <div class="form-group">
                        <label for="nim">NIM (Nomor Induk Mahasiswa)</label>
                        <input type="text" class="form-control" id="nim" placeholder="NIM Anda" readonly>
                    </div>
                    <div class="form-group">
                        <label for="plat_nomor">Plat Nomor Kendaraan</label>
                        <input type="text" class="form-control" id="plat_nomor" placeholder="Masukkan Plat Nomor kendaraan" readonly>
                    </div>
                    <div class="form-group">
                        <label for="saldo">Saldo</label>
                        <input type="text" class="form-control" id="saldo" placeholder="Saldo Anda" readonly>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-group">
                        <label for="lokasi">Lokasi Parkir</label>
                        <input type="text" class="form-control" id="lokasi" placeholder="Lokasi Parkir" readonly>
                    </div>
                    <div class="form-group">
                        <label for="status">Status</label>
                        <input type="text" class="form-control" id="status" placeholder="Scan kartu anda" readonly>
                        <small class="form-text text-muted">Silakan scan kartu RFID anda</small>
                    </div>
                    <div class="form-group">
                        <label for="jam_keluar">Jam masuk</label>
                        <input type="text" class="form-control" id="jam_masuk" placeholder="Jam Masuk" readonly>
                    </div>
                    <div class="form-group">
                        <label for="jam_keluar">Jam Keluar</label>
                        <input type="text" class="form-control" id="jam_keluar" placeholder="Jam Keluar" readonly>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    // Koneksi ke MQTT WebSocket
    const client = mqtt.connect('wss://broker.emqx.io:8084/mqtt');

    client.on('connect', function() {
        console.log("Terhubung ke MQTT");
        client.subscribe("rfid/check", function(err) {
            if (!err) {
                console.log("Subscribe ke topik rfid/check berhasil!");
            }
        });
    });

    // Saat menerima pesan MQTT (UID dari ESP32)
    client.on('message', function(topic, message) {
        if (topic === "rfid/check") {
            let uid = message.toString();
            console.log("UID diterima:", uid);
            document.getElementById("token").value = uid;

            // Kirim UID ke PHP untuk cek data tanpa reload
            fetch('/skripsi/Pengcodean/page/check_data_user.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'token=' + uid
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        document.getElementById("nama").value = data.nama;
                        document.getElementById("nim").value = data.nim;
                        document.getElementById("plat_nomor").value = data.plat_nomor;
                        if (data.lokasi !== "") {
                            document.getElementById("lokasi").value = data.lokasi;
                        } else {
                            document.getElementById("lokasi").value = "Pengguna Belum mentap di lokasi parkir";
                        }
                        document.getElementById("status").value = data.kondisi;
                        document.getElementById("jam_masuk").value = data.jam_masuk;
                        if (data.jam_keluar) {
                            document.getElementById("jam_keluar").value = data.jam_keluar;
                        } else {
                            document.getElementById("jam_keluar").value = "Pengguna masih didalam";
                        }
                        document.getElementById("saldo").value = data.saldo;


                        document.getElementById("alert-container").innerHTML =
                            "<div class='alert alert-success'>Data ditemukan!</div>";
                    } else {
                        document.getElementById("nama").value = "";
                        document.getElementById("nim").value = "";
                        document.getElementById("plat_nomor").value = "";
                        document.getElementById("lokasi").value = "";
                        document.getElementById("status").value = "";
                        document.getElementById("jam_masuk").value = "";
                        document.getElementById("jam_keluar").value = "";

                        document.getElementById("alert-container").innerHTML =
                            "<div class='alert alert-danger'>Data tidak ditemukan!</div>";
                    }
                });
        }
    });
</script>