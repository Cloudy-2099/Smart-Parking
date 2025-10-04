<?php
function alertFailed($message)
{
    echo '<div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <h5><i class="icon fas fa-ban"></i> Gagal!</h5>'
        . $message .
        '</div>';
}
// Jika form dikirim
if (isset($_POST['submit'])) {
    $token = mysqli_real_escape_string($con, $_POST['token']);
    $nama = mysqli_real_escape_string($con, $_POST['name']);
    $nim = mysqli_real_escape_string($con, $_POST['nim']);
    $plat = mysqli_real_escape_string($con, $_POST['plat_nomor']);
    $saldo = mysqli_real_escape_string($con, $_POST['saldo']);

    // Cek apakah UID sudah terdaftar
    $cek_token = mysqli_query($con, "SELECT * FROM rfid_card WHERE token = '$token'");
    if (mysqli_num_rows($cek_token) > 0) {
        $_SESSION['message'] = "Token sudah digunakan oleh pengguna lain.";
    } else {
        // Cek apakah NIM sudah terdaftar
        $cek_nim = mysqli_query($con, "SELECT * FROM rfid_card WHERE nim = '$nim'");
        if (mysqli_num_rows($cek_nim) > 0) {
            $_SESSION['message'] = "Nim sudah digunakan oleh pengguna lain";
        } else {
            // Simpan data ke database jika tidak ada masalah
            $sql = "INSERT INTO rfid_card (token, nama, nim, plat_nomor, saldo) VALUES ('$token', '$nama', '$nim', '$plat', '$saldo')";
            if (mysqli_query($con, $sql)) {
                $_SESSION['message'] = "Akun Berhasil Ditambahkan";
                echo "<script>location.href='?page=rfid_user';</script>";
                exit;
            } else {
                $_SESSION['message'] = "Terdapat Error silahkan coba lagi";
                alertFailed($message);
                unset($_SESSION['message']); // Hapus pesan dari session setelah ditampilkan           
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran RFID</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <script src="https://unpkg.com/mqtt/dist/mqtt.min.js"></script>
</head>

<body>

    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Daftar RFID</h1>
                    </div>
                </div>
            </div>
        </div>

        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg">
                        <?php if (isset($_SESSION['message'])) {
                            alertFailed($_SESSION['message']);
                            unset($_SESSION['message']); // Hapus setelah ditampilkan
                        } ?>

                        <form action="" method="post">
                            <div class="form-group">
                                <label>Token / UID</label>
                                <input type="text" class="form-control" name="token" id="token" placeholder="Scan kartu anda" readonly>
                                <small class="form-text text-muted">Tolong scan kartu anda</small>
                            </div>
                            <div class="form-group">
                                <label>Nama Lengkap</label>
                                <input type="text" class="form-control" name="name" id="name" placeholder="Nama Lengkap" required>
                            </div>
                            <div class="form-group">
                                <label>NIM (Nomor Induk Mahasiswa)</label>
                                <input type="text" class="form-control" name="nim" id="nim" placeholder="NIM Anda" required>
                            </div>
                            <div class="form-group">
                                <label>Plat Nomor Kendaraan</label>
                                <input type="text" class="form-control" name="plat_nomor" id="plat_nomor" placeholder="Masukkan Plat Nomor kendaraan" required>
                            </div>
                            <div class="form-group">
                                <label>Saldo</label>
                                <input type="text" class="form-control" name="saldo" id="saldo" placeholder="Masukkan jumlah deposit anda" required>
                            </div>

                            <button type="submit" class="btn btn-primary mb-3 mr-3" name="submit">Submit</button> <a href="?page=rfid_user" class="btn btn-danger mb-3">Kembali ke Daftar User</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Koneksi MQTT WebSocket
        const client = mqtt.connect('wss://broker.emqx.io:8084/mqtt');

        client.on('connect', function() {
            console.log("Terhubung ke MQTT");
            client.subscribe("rfid/register", function(err) {
                if (!err) {
                    console.log("Subscribe ke topik rfid/register berhasil!");
                }
            });
        });

        // Saat menerima pesan MQTT (UID dari ESP32)
        client.on('message', function(topic, message) {
            if (topic === "rfid/register") {
                let uid = message.toString();
                console.log("UID diterima:", uid);
                document.getElementById("token").value = uid;
            }
        });
    </script>

</body>

</html>