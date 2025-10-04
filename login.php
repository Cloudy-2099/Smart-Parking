<?php

session_start();

include "config/database.php";

$pesan = "Masukkan Username dan Password";

function alertSuccess($message)
{
  echo '                <div class="alert alert-success alert-dismissible">
                  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                  <h5><i class="icon fas fa-check"></i> Berhasil!</h5>'
    . $message . '
                </div>';
}

if (isset($_SESSION['username'])) {
  echo "<script> location.href='index.php'</script>";
}

if (isset($_SESSION['message'])) {
  alertSuccess($_SESSION['message']); // Menyimpan pesan ke variabel $pesan
  unset($_SESSION['message']); // Hapus pesan dari session setelah ditampilkan
}

if (isset($_POST['username'])) {
  $username = $_POST['username'];
  $pass = $_POST['pass'];

  $sql = "select * from user where username = '$username' limit 1";
  $result = mysqli_query($con, $sql);

  $data = mysqli_fetch_assoc($result);

  if (!mysqli_num_rows($result) > 0) {
    $pesan = "<b style='color:red'>Username tidak ditemukan</b>";
  } else {
    if (password_verify($pass, $data['password'])) {
      $_SESSION['username'] = $username;
      $_SESSION['nama'] = $data['nama'];
      echo "<script> location.href='index.php'</script>";
    } else {
      $pesan = "<b style='color:red'>Password salah</b>";
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Smart Parking Politeknik Bisnis Digital Indonesia</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <link rel="icon" href="inc/bulat.png">

</head>

<body class="hold-transition login-page">
  <div class="login-box">


    <!-- /.login-logo -->
    <div class="card card-outline card-primary">

      <div class="card-header text-center">
        <p class="h3"><b>Sistem Parkir</b> Polbis</p>
      </div>
      <div class="card-body">
        <p class="login-box-msg"><?php echo $pesan ?></p>
        <form action="" method="post">
          <div class="input-group mb-3">
            <input type="text" class="form-control" name="username" placeholder="username" required>
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-envelope"></span>
              </div>
            </div>
          </div>
          <div class="input-group mb-3">
            <input type="password" class="form-control" name="pass" placeholder="Password" required>
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-lock"></span>
              </div>
            </div>
          </div>

          <!-- /.col -->
          <div class="col-4 mb-2">
            <button type="submit" class="btn btn-primary btn-block">Sign In</button>
          </div>

          <!-- /.col -->
        </form>

        <div>
          <p class="mb-1">
            <a href="forgot_password.php">I forgot my password</a>
          </p>
          <p class="mb-0">
            <a href="register.php" class="text-center">Register a new membership</a>
          </p>
        </div>
        <!-- /.card-body -->

        <!-- /.card -->
      </div>
    </div>
    <!-- /.login-box -->

    <!-- jQuery -->
    <script src="plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="dist/js/adminlte.min.js"></script>
</body>

</html>