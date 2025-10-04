<?php

session_start();

include "config/database.php";
$pesan = "Pendaftaran akun admin";
if (isset($_POST['username'])) {
  $nama = $_POST['nama'];
  $un = $_POST['username'];
  $password = $_POST['password'];
  $confirm_password = $_POST['confirm_password'];


  // Check if password matches confirmation
  if ($password !== $confirm_password) {
    $pesan = "<div class='alert alert-danger'>Passwords do not match!</div>";
  } else {
    // Check if username already exists
    $sql = "SELECT * FROM user WHERE username = '$un'";
    $result = mysqli_query($con, $sql);

    if (mysqli_num_rows($result) > 0) {
      $pesan = "<div class='alert alert-danger'>Username already exists!</div>";
    } else {
      // Hash password and insert new user into database
      $pass = password_hash($password, PASSWORD_DEFAULT);
      $insert = "insert into user (nama, username, password) values ('$nama', '$un', '$pass')";
      $result = mysqli_query($con, $insert);

      if ($result) {
        // Redirect to login page
        $_SESSION['message'] = "Registration successful! Please login.";
        header("Location: login.php");
        exit();
      } else {
        $_SESSION['message'] = "Terdapat Error silahkan coba lagi";
        alertFailed($message);
        unset($_SESSION['message']); // Hapus pesan dari session setelah ditampilkan
      }
    }
  }

  function alertFailed($message)
  {
    echo '                <div class="alert alert-danger alert-dismissible">
                  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                  <h5><i class="icon fas fa-check"></i> Gagal!</h5>'
      . $message . '
                </div>';
  }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Registration Page</title>

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

<body class="hold-transition register-page">
  <div class="register-box">
    <div class="card card-outline card-primary">
      <div class="card-header text-center">
      </div>
      <div class="card-body">
        <p class="login-box-msg"><?php echo $pesan ?></p>
        <form action="register.php" method="post">
          <div class="input-group mb-3">
            <input type="text" class="form-control" placeholder="Full name" name="nama">
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-user"></span>
              </div>
            </div>
          </div>
          <div class="input-group mb-3">
            <input type="text" class="form-control" placeholder="Username" name="username">
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-envelope"></span>
              </div>
            </div>
          </div>
          <div class="input-group mb-3">
            <input type="password" class="form-control" placeholder="Password" name="password">
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-lock"></span>
              </div>
            </div>
          </div>
          <div class="input-group mb-3">
            <input type="password" class="form-control" placeholder="Retype password" name="confirm_password">
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-lock"></span>
              </div>
            </div>
          </div>
          <div class="row">
            <!-- /.col -->
            <div class="col-4 mb-2">
              <button type="submit" class="btn btn-primary btn-block">Register</button>
            </div>
            <!-- /.col -->
          </div>
        </form>
        <p class="mb-1">
          <a href="forgot_password.php">I forgot my password</a>
        </p>

        <a href="login.php" class="text-center">I already have a account</a>
      </div>
      <!-- /.form-box -->
    </div><!-- /.card -->
  </div>
  <!-- /.register-box -->

  <!-- jQuery -->
  <script src="plugins/jquery/jquery.min.js"></script>
  <!-- Bootstrap 4 -->
  <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- AdminLTE App -->
  <script src="dist/js/adminlte.min.js"></script>
</body>

</html>