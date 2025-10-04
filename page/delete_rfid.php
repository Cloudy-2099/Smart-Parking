<?php


if (isset($_GET['token'])) {
    $token = mysqli_real_escape_string($con, $_GET['token']);

    $sql = "DELETE FROM rfid_card WHERE token = '$token'";
    $result = mysqli_query($con, $sql);

    if ($result) {
        $_SESSION['message'] = "Data Berhasil Dihapus";
    } else {
        $_SESSION['message'] = "Terdapat Error, silahkan coba lagi";
    }

    // Redirect kembali ke halaman yang diinginkan
    echo "<script>location.href='?page=rfid_user'</script>";
}
