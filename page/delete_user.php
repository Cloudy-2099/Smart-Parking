<?php


if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($con, $_GET['id']);

    $sql = "DELETE FROM user WHERE id = '$id'";
    $result = mysqli_query($con, $sql);

    if ($result) {
        $_SESSION['message'] = "Data Berhasil Dihapus";
    } else {
        $_SESSION['message'] = "Terdapat Error, silahkan coba lagi";
    }

    // Redirect kembali ke halaman yang diinginkan
    echo "<script>location.href='?page=data_user'</script>";
}
