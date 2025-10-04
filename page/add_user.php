<?php
include "config/database.php";
$message = "Pendaftaran admin baru";

function alertFailed($message)
{
    echo '<div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <h5><i class="icon fas fa-ban"></i> Gagal!</h5>'
        . $message .
        '</div>';
}

// Jika tombol "Simpan" diklik
if (isset($_POST['submit'])) {

    $username = $_POST['username'];
    $nama = $_POST['nama'];
    $password = $_POST['password'];
    $password2 = $_POST['password2'];

    if ($password != $password2) {
        $_SESSION['message'] = "Password tidak sama";
    } else {
        // Check if username already exists
        $sql = "SELECT * FROM user WHERE username = '$username'";
        $result = mysqli_query($con, $sql);

        if (mysqli_num_rows($result) > 0) {
            $_SESSION['message'] = "Akun Sudah ada / terdaftar";
        } else {
            // Hash password and insert new user into database
            $pass = password_hash($password, PASSWORD_DEFAULT);
            $insert = "insert into user (nama, username, password) values ('$nama', '$username', '$pass')";

            if (mysqli_query($con, $insert)) {
                // Redirect to login page
                $_SESSION['message'] = "Akun Berhasil Ditambahkan";
                echo "<script>location.href='?page=data_user';</script>";
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

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0"><?= $message ?></h1>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
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
                            <label>Username:</label>
                            <input type="text" class="form-control" name="username" required>
                        </div>
                        <div class="form-group">
                            <label>Nama:</label>
                            <input type="text" class="form-control" name="nama" required>
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" class="form-control" name="password">
                        </div>
                        <div class="form-group">
                            <label>Confirm password</label>
                            <input type="password" class="form-control" name="password2">
                        </div>
                        <input type="submit" name="submit" value="submit" class="btn btn-primary mr-3"> <a href="index.php?page=data_user" class="btn btn-danger">Kembali ke Daftar Admin</a>
                    </form>
                </div>
            </div>

            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
</div>