<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="<?php echo BASE_URL; ?>">
            <i class="fas fa-car me-2"></i>
            <?php echo APP_NAME; ?>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'home') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>">
                        <i class="fas fa-home me-1"></i> Trang chủ
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'search') ? 'active' : ''; ?>"
                        href="<?php echo BASE_URL; ?>/cars/search">
                        <i class="fas fa-search me-1"></i> Tìm xe
                    </a>
                </li>

                <?php if (isset($_SESSION['user_id']) && $_SESSION['user_role'] == 'owner'): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?php echo (in_array($current_page, ['owner_dashboard', 'owner_cars', 'owner_bookings', 'owner_revenue'])) ? 'active' : ''; ?>"
                            href="#" id="ownerDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-tie me-1"></i> Chủ xe
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="ownerDropdown">
                            <li>
                                <a class="dropdown-item <?php echo ($current_page == 'add_car') ? 'active' : ''; ?>"
                                    href="<?php echo BASE_URL; ?>/cars/add">
                                    <i class="fas fa-plus-circle me-1"></i> Đăng xe mới
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item <?php echo ($current_page == 'owner_cars') ? 'active' : ''; ?>"
                                    href="<?php echo BASE_URL; ?>/owner/cars">
                                    <i class="fas fa-car me-1"></i> Quản lý xe
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item <?php echo ($current_page == 'owner_bookings') ? 'active' : ''; ?>"
                                    href="<?php echo BASE_URL; ?>/owner/bookings">
                                    <i class="fas fa-calendar-alt me-1"></i> Quản lý đơn thuê
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item <?php echo ($current_page == 'owner_revenue') ? 'active' : ''; ?>"
                                    href="<?php echo BASE_URL; ?>/owner/revenue?period=week">
                                    <i class="fas fa-chart-line me-1"></i> Doanh thu
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>

                <?php if (isset($_SESSION['user_id']) && $_SESSION['user_role'] == 'admin'): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?php echo (in_array($current_page, ['admin_dashboard', 'admin_users', 'admin_cars', 'admin_promotions', 'admin_statistics'])) ? 'active' : ''; ?>"
                            href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-shield me-1"></i> Quản trị
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="adminDropdown">
                            <li>
                                <a class="dropdown-item <?php echo ($current_page == 'admin_dashboard') ? 'active' : ''; ?>"
                                    href="<?php echo BASE_URL; ?>/admin/dashboard">
                                    <i class="fas fa-tachometer-alt me-1"></i> Bảng điều khiển
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item <?php echo ($current_page == 'admin_users') ? 'active' : ''; ?>"
                                    href="<?php echo BASE_URL; ?>/admin/users">
                                    <i class="fas fa-users me-1"></i> Quản lý người dùng
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item <?php echo ($current_page == 'admin_cars') ? 'active' : ''; ?>"
                                    href="<?php echo BASE_URL; ?>/admin/cars">
                                    <i class="fas fa-car me-1"></i> Quản lý xe
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item <?php echo ($current_page == 'admin_promotions') ? 'active' : ''; ?>"
                                    href="<?php echo BASE_URL; ?>/admin/promotions">
                                    <i class="fas fa-tags me-1"></i> Quản lý khuyến mãi
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item <?php echo ($current_page == 'admin_statistics') ? 'active' : ''; ?>"
                                    href="<?php echo BASE_URL; ?>/admin/statistics?period=week">
                                    <i class="fas fa-chart-bar me-1"></i> Thống kê doanh thu
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>

                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'about') ? 'active' : ''; ?>"
                        href="<?php echo BASE_URL; ?>/about">
                        <i class="fas fa-info-circle me-1"></i> Giới thiệu
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'contact') ? 'active' : ''; ?>"
                        href="<?php echo BASE_URL; ?>/contact">
                        <i class="fas fa-envelope me-1"></i> Liên hệ
                    </a>
                </li>
            </ul>

            <ul class="navbar-nav">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="fas fa-user-circle me-1"></i>
                            <?php echo $_SESSION['username']; ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li>
                                <a class="dropdown-item <?php echo ($current_page == 'user_profile') ? 'active' : ''; ?>"
                                    href="<?php echo BASE_URL; ?>/user/profile">
                                    <i class="fas fa-user me-1"></i> Thông tin cá nhân
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item <?php echo ($current_page == 'user_bookings') ? 'active' : ''; ?>"
                                    href="<?php echo BASE_URL; ?>/user/bookings">
                                    <i class="fas fa-history me-1"></i> Lịch sử thuê xe
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item <?php echo ($current_page == 'user_change_password') ? 'active' : ''; ?>"
                                    href="<?php echo BASE_URL; ?>/user/change_password">
                                    <i class="fas fa-key me-1"></i> Đổi mật khẩu
                                </a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?php echo BASE_URL; ?>/auth/logout">
                                    <i class="fas fa-sign-out-alt me-1"></i> Đăng xuất
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_page == 'login') ? 'active' : ''; ?>"
                            href="<?php echo BASE_URL; ?>/auth/login">
                            <i class="fas fa-sign-in-alt me-1"></i> Đăng nhập
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?php echo (in_array($current_page, ['register', 'register_owner'])) ? 'active' : ''; ?>"
                            href="#" id="registerDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-plus me-1"></i> Đăng ký
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="registerDropdown">
                            <li>
                                <a class="dropdown-item <?php echo ($current_page == 'register') ? 'active' : ''; ?>"
                                    href="<?php echo BASE_URL; ?>/auth/register">
                                    <i class="fas fa-user me-1"></i> Tài khoản thường
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item <?php echo ($current_page == 'register_owner') ? 'active' : ''; ?>"
                                    href="<?php echo BASE_URL; ?>/auth/register_owner">
                                    <i class="fas fa-car me-1"></i> Tài khoản chủ xe
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>