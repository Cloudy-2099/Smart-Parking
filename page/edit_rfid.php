<?php
$id = $_GET['token'];
$result = $con->query("SELECT * FROM rfid_card WHERE token = '$id'");
$row = mysqli_fetch_assoc($result);
function alertFailed($message)
{
    echo '<div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <h5><i class="icon fas fa-ban"></i> Gagal!</h5>'
        . $message .
        '</div>';
}
if (isset($_POST['submit'])) {
    $token = $_POST['token'];
    $nama = $_POST['nama'];
    $nim = $_POST['nim'];
    $plat_nomor = $_POST['plat_nomor'];
    $saldo = $_POST['saldo'];

    // Cek apakah token sudah digunakan oleh pengguna lain
    $cek_token = "SELECT * FROM rfid_card WHERE token = '$token' AND token != '$id'"; // Exclude the current user
    $result_token = mysqli_query($con, $cek_token);

    // Cek apakah NIM sudah digunakan oleh pengguna lain
    $cek_nim = "SELECT * FROM rfid_card WHERE nim = '$nim' AND token != '$id'"; // Exclude the current user
    $result_nim = mysqli_query($con, $cek_nim);

    if (mysqli_num_rows($result_token) > 0) {
        $_SESSION['message'] = "Token sudah digunakan oleh pengguna lain.";
    } else if (mysqli_num_rows($result_nim) > 0) {
        $_SESSION['message'] = "NIM sudah digunakan oleh pengguna lain.";
    } else {
        // Lakukan update jika tidak ada masalah dengan token atau NIM
        $query = "UPDATE rfid_card SET nim = '$nim', nama = '$nama', saldo = $saldo, plat_nomor = '$plat_nomor' WHERE token = '$token'";
        if (mysqli_query($con, $query)) {
            echo "<script>alert('Data berhasil diubah!'); window.location.href='?page=rfid_user';</script>";
        } else {
            echo "Error: " . mysqli_error($con);
        }
    }
}

?>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Ubah data Pengguna</h1>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->

    <div class="content">
        <?php if (isset($_SESSION['message'])) {
            alertFailed($_SESSION['message']);
            unset($_SESSION['message']); // Hapus setelah ditampilkan
        } ?>
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg">
                    <form action="" method="post">
                        <div class="form-group">
                            <label>Token:</label>
                            <input type="text" class="form-control" name="token" value="<?= htmlspecialchars($row['token']) ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label>Nama:</label>
                            <input type="text" class="form-control" name="nama" value="<?= htmlspecialchars($row['nama']) ?>" required>
                        </div>
                        <div class="form-group">
                            <input type="hidden" name="id" value="<?= $id ?>">
                            <label>Nomor Induk Mahasiswa:</label>
                            <input type="text" class="form-control" name="nim" value="<?= htmlspecialchars($row['nim']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Plat Nomor:</label>
                            <input type="text" class="form-control" name="plat_nomor" value="<?= htmlspecialchars($row['plat_nomor']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Saldo:</label>
                            <input type="text" class="form-control" name="saldo" value="<?= htmlspecialchars($row['saldo']) ?>" required>
                        </div>
                        <input type="submit" name="submit" value="submit" class="btn btn-primary mr-3"> <a href="index.php?page=rfid_user" class="btn btn-danger">Kembali ke Daftar Pengguna</a>
                    </form>
                </div>
            </div>

            <!-- /.card-body -->
        </div>
        <!-- /.card -->
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