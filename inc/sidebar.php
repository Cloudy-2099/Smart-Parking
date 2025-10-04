<?php
$currentPage = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

?>


<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-light-primary elevation-4">
    <!-- Brand Logo -->
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image mt-2">
                <img src="inc/polbis.png" alt="AdminLTE Logo" class="img-circle elevation-2" style="width: 30px; height: 30px;">
            </div>
            <div class="info">
                <a href="#" class="d-block">Politeknik Bisnis <br>Digital Indonesia</a>
            </div>
        </div>
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a class="d-block"><?php echo $_SESSION['username'] ?></a>
            </div>
        </div>

        <!-- SidebarSearch Form -->
        <div class="form-inline">
            <div class="input-group" data-widget="sidebar-search">
                <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
                <div class="input-group-append">
                    <button class="btn btn-sidebar">
                        <i class="fas fa-search fa-fw"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="?page=dashboard" class="nav-link <?php echo ($currentPage == 'dashboard') ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="?page=data_user" class="nav-link <?php echo ($currentPage == 'data_user' || $currentPage == 'add_user' || $currentPage == 'edit_user') ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-user"></i>
                        <p>Data Admin / Petugas</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="?page=rfid_user" class="nav-link <?php echo ($currentPage == 'rfid_user' || $currentPage == 'add_rfid' || $currentPage == 'edit_rfid') ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-user"></i>
                        <p>Data Pengguna</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="?page=activity" class="nav-link <?php echo ($currentPage == 'activity') ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-address-card"></i>
                        <p>Activity Logs</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="?page=activity_slot" class="nav-link <?php echo ($currentPage == 'activity_slot') ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-address-card"></i>
                        <p>Activity Slot Parkir</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="?page=check_data_user" class="nav-link <?php echo ($currentPage == 'check_data_user') ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-search"></i>
                        <p>Pengecekkan Data Pengguna</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="logout.php" class="nav-link">
                        <i class="nav-icon fas fa-sign-out-alt"></i>
                        <p>
                            Logout
                        </p>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>