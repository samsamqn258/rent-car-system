<?php include 'views/shared/header.php'; ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">Đăng nhập</h3>
                </div>
                <div class="card-body">
                    <form action="<?php echo BASE_URL; ?>/auth/login" method="post">
                        <div class="mb-3">
                            <label for="username" class="form-label">Tên đăng nhập hoặc Email</label>
                            <input type="text" class="form-control" id="username" name="username" required autofocus
                                value="<?php echo isset($_SESSION['form_data']['username']) ? $_SESSION['form_data']['username'] : ''; ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Mật khẩu</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label" for="remember">Ghi nhớ đăng nhập</label>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Đăng nhập</button>
                        </div>
                    </form>
                    
                    <div class="mt-3 text-center">
                        <p>Chưa có tài khoản? <a href="<?php echo BASE_URL; ?>/auth/register">Đăng ký ngay</a></p>
                        <p>Bạn muốn trở thành chủ xe? <a href="<?php echo BASE_URL; ?>/auth/register_owner">Đăng ký chủ xe</a></p>
                        <p><a href="<?php echo BASE_URL; ?>/auth/forgot_password">Quên mật khẩu?</a></p>
                    </div>
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-body">
                    <h5 class="card-title">Lợi ích khi đăng nhập</h5>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex align-items-center">
                            <i class="fas fa-search text-primary me-3"></i>
                            <span>Tìm kiếm và thuê xe dễ dàng</span>
                        </li>
                        <li class="list-group-item d-flex align-items-center">
                            <i class="fas fa-history text-primary me-3"></i>
                            <span>Xem lịch sử thuê xe</span>
                        </li>
                        <li class="list-group-item d-flex align-items-center">
                            <i class="fas fa-star text-primary me-3"></i>
                            <span>Đánh giá xe sau khi thuê</span>
                        </li>
                        <li class="list-group-item d-flex align-items-center">
                            <i class="fas fa-bell text-primary me-3"></i>
                            <span>Nhận thông báo về ưu đãi đặc biệt</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Clear form data from session
if (isset($_SESSION['form_data'])) {
    unset($_SESSION['form_data']);
}
?>

<?php include 'views/shared/footer.php'; ?>