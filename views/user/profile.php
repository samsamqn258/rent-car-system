<?php include 'views/shared/header.php'; ?>

<div class="container mt-4">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Tài khoản của bạn</h5>
                </div>
                <div class="list-group list-group-flush">
                    <a href="<?php echo BASE_URL; ?>/user/profile" class="list-group-item list-group-item-action active">
                        <i class="fas fa-user me-2"></i> Thông tin cá nhân
                    </a>
                    <a href="<?php echo BASE_URL; ?>/user/bookings" class="list-group-item list-group-item-action">
                        <i class="fas fa-history me-2"></i> Lịch sử thuê xe
                    </a>
                    <a href="<?php echo BASE_URL; ?>/user/change_password" class="list-group-item list-group-item-action">
                        <i class="fas fa-key me-2"></i> Đổi mật khẩu
                    </a>
                    <?php if ($user['role'] == 'owner'): ?>
                        <div class="list-group-item list-group-item-secondary fw-bold">Quản lý chủ xe</div>
                        <a href="<?php echo BASE_URL; ?>/owner/cars" class="list-group-item list-group-item-action">
                            <i class="fas fa-car me-2"></i> Quản lý xe
                        </a>
                        <a href="<?php echo BASE_URL; ?>/owner/bookings" class="list-group-item list-group-item-action">
                            <i class="fas fa-calendar-alt me-2"></i> Quản lý đơn thuê
                        </a>
                        <a href="<?php echo BASE_URL; ?>/owner/revenue?period=week" class="list-group-item list-group-item-action">
                            <i class="fas fa-chart-line me-2"></i> Doanh thu
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <div class="col-md-9">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">Thông tin cá nhân</h3>
                </div>
                <div class="card-body">
                    <form action="<?php echo BASE_URL; ?>/user/profile" method="post">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="username" class="form-label">Tên đăng nhập</label>
                                <input type="text" class="form-control" id="username" value="<?php echo $user['username']; ?>" readonly
                                    disabled>
                                <div class="form-text">Tên đăng nhập không thể thay đổi.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" value="<?php echo $user['email']; ?>" readonly
                                    disabled>
                                <div class="form-text">Email không thể thay đổi.</div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="fullname" class="form-label">Họ và tên</label>
                                <input type="text" class="form-control" id="fullname" name="fullname"
                                    value="<?php echo $user['fullname']; ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Số điện thoại</label>
                                <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo $user['phone']; ?>"
                                    required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Địa chỉ</label>
                            <textarea class="form-control" id="address" name="address" rows="3"
                                required><?php echo $user['address']; ?></textarea>
                        </div>

                        <?php if ($user['role'] !== 'admin'): ?>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="license" class="form-label">Số giấy phép lái xe</label>
                                    <input type="text" class="form-control <?php echo empty($user['license']) ? 'border-danger' : ''; ?>" id="license" name="license" value="<?php echo isset($user['license']) ? $user['license'] : ''; ?>" required>
                                    <?php if (empty($user['license'])): ?>
                                        <div class="text-danger mt-2">Hãy cập nhật giấy phép lái xe</div>
                                    <?php endif; ?>
                                </div>

                            </div>
                        <?php endif; ?>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="role" class="form-label">Vai trò</label>
                                <input type="text" class="form-control" id="role"
                                    value="<?php echo $user['role'] == 'admin' ? 'Quản trị viên' : ($user['role'] == 'owner' ? 'Chủ xe' : 'Người dùng thường'); ?>"
                                    readonly disabled>
                            </div>
                            <div class="col-md-6">
                                <label for="created_at" class="form-label">Ngày đăng ký</label>
                                <input type="text" class="form-control" id="created_at"
                                    value="<?php echo date('d/m/Y', strtotime($user['created_at'])); ?>" readonly disabled>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Cập nhật thông tin</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'views/shared/footer.php'; ?>