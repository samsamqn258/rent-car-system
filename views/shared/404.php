<?php include 'views/shared/header.php'; ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <div class="error-container">
                <div class="error-code">404</div>
                <h2 class="error-title mb-4">Trang không tìm thấy</h2>
                <p class="error-message mb-4">Xin lỗi, chúng tôi không thể tìm thấy trang bạn đang tìm kiếm. Trang này có thể đã bị xóa, tên đã thay đổi hoặc tạm thời không khả dụng.</p>
                
                <div class="error-actions">
                    <a href="<?php echo BASE_URL; ?>" class="btn btn-primary me-3">
                        <i class="fas fa-home me-2"></i> Trang chủ
                    </a>
                    <a href="<?php echo BASE_URL; ?>/cars/search" class="btn btn-outline-primary">
                        <i class="fas fa-search me-2"></i> Tìm xe
                    </a>
                </div>
                
                <div class="error-illustration mt-5">
                    <img src="<?php echo BASE_URL; ?>/public/images/404-car.png" alt="404 Error" class="img-fluid" style="max-height: 300px;">
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-5">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Bạn đang tìm gì?</h4>
                    <p>Dưới đây là một số liên kết hữu ích:</p>
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="list-unstyled">
                                <li class="mb-2"><a href="<?php echo BASE_URL; ?>"><i class="fas fa-home me-2"></i> Trang chủ</a></li>
                                <li class="mb-2"><a href="<?php echo BASE_URL; ?>/cars/search"><i class="fas fa-search me-2"></i> Tìm xe</a></li>
                                <li class="mb-2"><a href="<?php echo BASE_URL; ?>/auth/login"><i class="fas fa-sign-in-alt me-2"></i> Đăng nhập</a></li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-unstyled">
                                <li class="mb-2"><a href="<?php echo BASE_URL; ?>/auth/register"><i class="fas fa-user-plus me-2"></i> Đăng ký</a></li>
                                <li class="mb-2"><a href="<?php echo BASE_URL; ?>/auth/register_owner"><i class="fas fa-car me-2"></i> Đăng ký chủ xe</a></li>
                                <li class="mb-2"><a href="#" onclick="history.back(); return false;"><i class="fas fa-arrow-left me-2"></i> Quay lại trang trước</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.error-container {
    padding: 40px 0;
}

.error-code {
    font-size: 120px;
    font-weight: bold;
    color: #0d6efd;
    line-height: 1;
    margin-bottom: 20px;
    text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1);
}

.error-title {
    font-size: 32px;
    color: #333;
}

.error-message {
    font-size: 18px;
    color: #6c757d;
    max-width: 600px;
    margin: 0 auto;
}

.error-actions {
    margin-top: 30px;
}

.error-illustration img {
    max-width: 100%;
    height: auto;
}
</style>

<?php include 'views/shared/footer.php'; ?>