<?php
$sesi_username = $_SESSION['username'];
$sql = "select * from user where username != '$sesi_username'";
$result = mysqli_query($con, $sql);


function alertSuccess($message)
{
    echo '<div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <h5><i class="icon fas fa-check"></i> Berhasil!</h5>'
        . $message .
        '</div>';
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Admin</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>


<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Data Admin</h1>
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
                    <table id="example1" class="table table-bordered table-striped">
                        <div class="alert-container">
                            <?php
                            if (isset($_SESSION['message'])) {
                                // Tampilkan pesan sesuai tipe
                                alertSuccess($_SESSION['message']);
                                unset($_SESSION['message']); // Hapus pesan setelah ditampilkan
                            }
                            ?>
                        </div>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Nama</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php while ($row = $result->fetch_assoc()) { ?>
                                <tr>
                                    <td><?= $row['id']; ?></td>
                                    <td><?= $row['username']; ?></td>
                                    <td><?= $row['nama']; ?></td>
                                    <td>
                                        <a href="?page=edit_user&id=<?= $row['id']; ?>">Edit</a>
                                        <a href="?page=delete_user&id=<?= $row['id']; ?>" onclick="return confirm('Hapus data?');">Hapus</a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>

                    </table>
                    <br>

                    <a href="?page=add_user" class="btn btn-danger">
                        <i class="nav-icon fas fa-user-plus"></i>
                        Tambah data admin
                    </a>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
    </div>
</div>