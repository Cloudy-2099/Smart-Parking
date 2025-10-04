<?php
session_start();
include "config/database.php";


// Pastikan pengguna sudah login
if (!isset($_SESSION['username'])) {
    echo "<script> location.href='login.php'</script>";
}

// Load template halaman utama
include "inc/header.php";
include "inc/navbar.php";
include "inc/sidebar.php";


// Load halaman berdasarkan parameter `page`
if (isset($_GET['page'])) {
    $page = $_GET['page'];
    include "page/" . $page . ".php";
} else {
    include "page/dashboard.php";
}

include "inc/footer.php";
?>
</div>
<!-- ./wrapper -->


</body>

</html>

<!-- REQUIRED SCRIPTS -->
<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- DataTables  & Plugins -->
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="plugins/jszip/jszip.min.js"></script>
<script src="plugins/pdfmake/pdfmake.min.js"></script>
<script src="plugins/pdfmake/vfs_fonts.js"></script>
<script src="plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
<script src="plugins/ion-rangeslider/js/ion.rangeSlider.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes
<script src="dist/js/demo.js"></script> -->
<!-- Page specific script -->
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