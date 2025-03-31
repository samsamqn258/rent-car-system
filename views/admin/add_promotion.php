<?php include 'views/shared/header.php'; ?>

<div class="container-fluid mt-4">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
            <div class="position-sticky pt-3">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>/admin/dashboard">
                            <i class="fas fa-tachometer-alt me-2"></i> Bảng điều khiển
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>/admin/users">
                            <i class="fas fa-users me-2"></i> Quản lý người dùng
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>/admin/cars">
                            <i class="fas fa-car me-2"></i> Quản lý xe
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="<?php echo BASE_URL; ?>/admin/promotions">
                            <i class="fas fa-tags me-2"></i> Quản lý khuyến mãi
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>/admin/statistics">
                            <i class="fas fa-chart-bar me-2"></i> Thống kê doanh thu
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Main content -->
        <div class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Thêm mã khuyến mãi mới</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="<?php echo BASE_URL; ?>/admin/promotions" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Quay lại
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <form action="<?php echo BASE_URL; ?>/admin/promotions/add" method="post">
                                <div class="mb-3">
                                    <label for="code" class="form-label">Mã khuyến mãi</label>
                                    <input type="text" class="form-control" id="code" name="code" required value="<?php echo isset($_SESSION['form_data']['code']) ? $_SESSION['form_data']['code'] : ''; ?>">
                                    <div class="form-text">Nhập mã khuyến mãi (ví dụ: SUMMER2023, NEWYEAR20)</div>
                                </div>

                                <div class="mb-3">
                                    <label for="discount_percentage" class="form-label">Phần trăm giảm giá</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="discount_percentage" name="discount_percentage" min="1" max="100" required value="<?php echo isset($_SESSION['form_data']['discount_percentage']) ? $_SESSION['form_data']['discount_percentage'] : ''; ?>">
                                        <span class="input-group-text">%</span>
                                    </div>
                                    <div class="form-text">Nhập phần trăm giảm giá (1-100)</div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="start_date" class="form-label">Ngày bắt đầu</label>
                                        <input type="datetime-local" class="form-control" id="start_date" name="start_date" required value="<?php echo isset($_SESSION['form_data']['start_date']) ? $_SESSION['form_data']['start_date'] : date('Y-m-d\TH:i'); ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="end_date" class="form-label">Ngày kết thúc</label>
                                        <input type="datetime-local" class="form-control" id="end_date" name="end_date" required value="<?php echo isset($_SESSION['form_data']['end_date']) ? $_SESSION['form_data']['end_date'] : date('Y-m-d\TH:i', strtotime('+30 days')); ?>">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Trạng thái</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="status" id="statusActive" value="active" checked>
                                        <label class="form-check-label" for="statusActive">
                                            Hoạt động
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="status" id="statusInactive" value="inactive" <?php echo (isset($_SESSION['form_data']['status']) && $_SESSION['form_data']['status'] == 'inactive') ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="statusInactive">
                                            Không hoạt động
                                        </label>
                                    </div>
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i> Lưu mã khuyến mãi
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h5 class="card-title mb-0">Hướng dẫn</h5>
                        </div>
                        <div class="card-body">
                            <h6>Tạo mã khuyến mãi hiệu quả</h6>
                            <ul class="list-group list-group-flush mb-3">
                                <li class="list-group-item">
                                    <i class="fas fa-check text-success me-2"></i> Sử dụng mã ngắn gọn, dễ nhớ
                                </li>
                                <li class="list-group-item">
                                    <i class="fas fa-check text-success me-2"></i> Đặt thời gian khuyến mãi hợp lý
                                </li>
                                <li class="list-group-item">
                                    <i class="fas fa-check text-success me-2"></i> Mức giảm giá phù hợp với chiến dịch
                                </li>
                                <li class="list-group-item">
                                    <i class="fas fa-check text-success me-2"></i> Tạo cảm giác khẩn cấp với thời hạn rõ ràng
                                </li>
                            </ul>

                            <h6>Mẹo tạo mã khuyến mãi</h6>
                            <div class="alert alert-primary">
                                <i class="fas fa-lightbulb me-2"></i> Sử dụng các dịp lễ, sự kiện trong đặt tên mã khuyến mãi.
                            </div>
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i> Mã khuyến mãi sẽ áp dụng cho tất cả người dùng nếu đang hoạt động.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validate end date is after start date
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    
    const validateDates = function() {
        if (startDateInput.value && endDateInput.value) {
            const startDate = new Date(startDateInput.value);
            const endDate = new Date(endDateInput.value);
            
            if (endDate <= startDate) {
                endDateInput.setCustomValidity('Ngày kết thúc phải sau ngày bắt đầu');
            } else {
                endDateInput.setCustomValidity('');
            }
        }
    };
    
    startDateInput.addEventListener('change', validateDates);
    endDateInput.addEventListener('change', validateDates);
    
    // Generate random promotion code
    document.getElementById('code').addEventListener('focus', function() {
        if (!this.value) {
            const prefixes = ['SUMMER', 'FALL', 'WINTER', 'SPRING', 'HOLIDAY', 'SPECIAL', 'SAVE', 'NEW'];
            const randomPrefix = prefixes[Math.floor(Math.random() * prefixes.length)];
            const randomNum = Math.floor(Math.random() * 50) + 10;
            this.value = randomPrefix + randomNum;
        }
    });
});
</script>

<?php
// Clear form data from session
if (isset($_SESSION['form_data'])) {
    unset($_SESSION['form_data']);
}
?>

<?php include 'views/shared/footer.php'; ?>