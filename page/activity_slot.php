<?php


// Query untuk mengambil data
$sql = "SELECT 
            aktivitas_slot.token, 
            rfid_card.plat_nomor,
            rfid_card.nama, 
            aktivitas_slot.slot,
            aktivitas_slot.jam_parkir
        FROM aktivitas_slot
        INNER JOIN rfid_card ON aktivitas_slot.token = rfid_card.token";

$result = mysqli_query($con, $sql);

// Cek apakah query berhasil
if (!$result) {
    die("Error dalam query: " . mysqli_error($con));
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pengguna</title>
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

<body>

    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Aktivitas User</h1>
                    </div>
                </div>
            </div>
        </div>

        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg">
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Token</th>
                                    <th>Plat Nomor</th>
                                    <th>Lokasi</th>
                                    <th>Jam Parkir</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (mysqli_num_rows($result) > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr>
                                        <td>{$row['token']}</td>
                                        <td>{$row['plat_nomor']}</td>
                                        <td>{$row['slot']}</td>
                                        <td>{$row['jam_parkir']}</td>
                                      </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='5'>Tidak ada data ditemukan</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                        <br>
                    </div>
                    <!-- /.card-body -->
                </div>

            </div>

        </div>
        <!-- /.row -->
    </div><!-- /.container-fluid -->


</body>

</html>

<script>
    $(function() {
        $("#example1").DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            "buttons": [{
                    extend: "copy",
                    title: "Data_Parkir"
                },
                {
                    extend: "csv",
                    title: "Data_Parkir"
                },
                {
                    extend: "excel",
                    title: "Data_Parkir"
                },
                {
                    extend: "pdf",
                    title: "Data_Parkir"
                },
                {
                    extend: "print",
                    title: "Data_Parkir"
                },
                {
                    extend: "colvis"
                }
            ]
        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    });
</script>