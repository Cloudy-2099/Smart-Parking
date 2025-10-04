<?php
session_start();


// Include file database
include 'config/database.php';
$pesan = "You are only one step a way from your new password, recover your password now.";


if (isset($_POST['username'])) {
  $username = $_POST['username'];
  $new_password = $_POST['new_password'];
  $confirm_password = $_POST['confirm_password'];
  $sql = "select * from user where username = '$username' limit 1";
  $result = $con->query($sql);

  if (!mysqli_num_rows($result) > 0) {
    echo "<div class='alert alert-danger'>Username tidak ditemukan</div>";
  } else if ($new_password !== $confirm_password) {
    echo "<div class='alert alert-danger'>Password confirmation does not match!</div>";
  } else if ($new_password == $confirm_password) {
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    // Update password di tabel users
    $sql = "UPDATE user SET password = '$hashed_password' WHERE username = '$username'";
    if ($con->query($sql) === TRUE) {
      $_SESSION['message'] = "Password berhasil direset! Silakan login.";
      header("Location: login.php");
      exit();
    } else {
      echo "<div class='alert alert-danger'>Error updating password!</div>";
    }
  }
}



?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title></title>

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
    <div class="card card-outline card-primary">
      <div class="card-header text-center">
        <div class="card-header text-center">
          <p class="h3"><b>Sistem Parkir</b> Polbis</p>
        </div>
      </div>
      <div class="card-body">
        <p class="login-box-msg"><?php echo $pesan ?></p>
        <form action="" method="post">
          <div class="input-group mb-3">
            <input type="text" class="form-control" placeholder="Username" name="username">
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-user"></span>
              </div>
            </div>
          </div>
          <div class="input-group mb-3">
            <input type="password" class="form-control" placeholder="Password" name="new_password">
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-lock"></span>
              </div>
            </div>
          </div>
          <div class="input-group mb-3">
            <input type="password" class="form-control" placeholder="Confirm Password" name="confirm_password">
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-lock"></span>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-12">
              <button type="submit" class="btn btn-primary btn-block">Change password</button>
            </div>
            <!-- /.col -->
          </div>
        </form>

        <p class="mt-3 mb-1">
          <a href="login.php">Login</a>
        </p>
        <p class="mb-0">
          <a href="register.php" class="text-center">Register a new membership</a>
        </p>
      </div>
      <!-- /.login-card-body -->
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