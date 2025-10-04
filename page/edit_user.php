<?php
include "config/database.php";

$id = $_GET['id'];
$result = $con->query("SELECT * FROM user WHERE id = '$id'");
$row = mysqli_fetch_assoc($result);
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
    $id = $_POST['id'];
    $username = $_POST['username'];
    $nama = $_POST['nama'];
    $password = $_POST['password'];

    // Jika password diisi, maka update password juga
    $sql = "SELECT * FROM user WHERE username = '$username' AND id != '$id'"; // Check if the username is already taken but exclude the current user
    $result = mysqli_query($con, $sql);

    if (mysqli_num_rows($result) > 0) {
        $_SESSION['message'] = "Akun Sudah ada / terdaftar";
    } else {
        if (!empty($password)) {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $query = "UPDATE user SET username = '$username', nama = '$nama', password = '$passwordHash' WHERE id = '$id'";
            if (mysqli_query($con, $query)) {
                echo "<script>alert('Data berhasil diubah!'); window.location.href='?page=data_user';</script>";
            } else {
                echo "Error: " . mysqli_error($con);
            }
        } else if (empty($password)) {
            $query = "UPDATE user SET username = '$username', nama = '$nama' WHERE id = '$id'";
            if (mysqli_query($con, $query)) {
                echo "<script>alert('Data berhasil diubah!'); window.location.href='?page=data_user';</script>";
            } else {
                echo "Error: " . mysqli_error($con);
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
                    <h1 class="m-0">Pengedit an Data Admin</h1>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <?php if (isset($_SESSION['message'])) {
                alertFailed($_SESSION['message']);
                unset($_SESSION['message']); // Hapus setelah ditampilkan
            } ?>
            <div class="row">
                <div class="col-lg">
                    <form action="" method="post">
                        <div class="form-group">
                            <input type="hidden" name="id" value="<?= $id ?>">
                            <label>Username:</label>
                            <input type="text" class="form-control" name="username" value="<?= htmlspecialchars($row['username']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Nama:</label>
                            <input type="text" class="form-control" name="nama" value="<?= htmlspecialchars($row['nama']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" class="form-control" name="password">
                            <small id="emailHelp" class="form-text text-muted">kosongkan jika tidak ingin diubah</small>
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