<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' . APP_NAME : APP_NAME; ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/public/css/styles.css">
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark ">
        <div class="container">
            <a class="navbar-brand" href="<?php echo BASE_URL; ?>">
                <img src="<?php echo BASE_URL; ?>/public/images/logo-full.ea382559.png"
                    style="width: 150px; height: 30px; object-fit: cover;" />
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link text-black" href="<?php echo BASE_URL; ?>">Trang chủ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-black" href="<?php echo BASE_URL; ?>/cars/search">Tìm xe</a>
                    </li>
                    <?php if (isset($_SESSION['user_id']) && $_SESSION['user_role'] == 'owner'): ?>
                        <li class="nav-item dropdown ">
                            <a class="nav-link dropdown-toggle text-black" href="#" id="ownerDropdown" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                Chủ xe
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="ownerDropdown">
                                <li><a class="dropdown-item " href="<?php echo BASE_URL; ?>/cars/add">Đăng xe mới</a></li>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/owner/cars">Quản lý xe</a></li>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/owner/bookings">Quản lý đơn thuê</a></li>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/owner/revenue">Doanh thu</a></li>
                            </ul>
                        </li>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['user_id']) && $_SESSION['user_role'] == 'admin'): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-black" href="#" id="adminDropdown" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                Quản trị
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="adminDropdown">
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/admin/dashboard">Bảng điều khiển</a></li>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/admin/users">Quản lý người dùng</a></li>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/admin/cars">Quản lý xe</a></li>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/admin/promotions">Quản lý khuyến mãi</a></li>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/admin/statistics">Thống kê doanh thu</a></li>
                            </ul>
                        </li>
                    <?php endif; ?>
                </ul>
                <ul class="navbar-nav">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-black" href="#" id="userDropdown" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user-circle me-1"></i>
                                <?php echo $_SESSION['username']; ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/user/profile">Thông tin cá nhân</a></li>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/user/bookings">Lịch sử thuê xe</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item " href="<?php echo BASE_URL; ?>/auth/logout">Đăng xuất</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item ">
                            <a class="nav-link text-black" href="<?php echo BASE_URL; ?>/auth/register_owner">Trở thành chủ xe</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-black" href="<?php echo BASE_URL; ?>/auth/login">Đăng nhập</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-black" href="<?php echo BASE_URL; ?>/auth/register">Đăng ký</a>
                        </li>

                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="container mt-3">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['success']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="container mt-3">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['error']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>