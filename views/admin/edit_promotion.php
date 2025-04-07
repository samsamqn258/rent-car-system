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
            <a class="nav-link" href="<?php echo BASE_URL; ?>/admin/statistics?period=week">
              <i class="fas fa-chart-bar me-2"></i> Thống kê doanh thu
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo BASE_URL; ?>/admin/contracts">
              <i class="fas fa-file-contract me-2"></i> Quản lý hợp đồng
            </a>
          </li>
        </ul>
      </div>
    </div>

    <!-- Main content -->
    <div class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
      <div
        class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Chỉnh sửa mã khuyến mãi</h1>
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
              <form action="<?php echo BASE_URL; ?>/admin/promotions/edit/<?php echo $promotion->id; ?>" method="post">
                <div class="mb-3">
                  <label for="code" class="form-label">Mã khuyến mãi</label>
                  <input type="text" class="form-control" id="code" name="code" required
                    value="<?php echo isset($_SESSION['form_data']['code']) ? $_SESSION['form_data']['code'] : $promotion->code; ?>">
                </div>

                <div class="mb-3">
                  <label for="discount_percentage" class="form-label">Phần trăm giảm giá</label>
                  <div class="input-group">
                    <input type="number" class="form-control" id="discount_percentage" name="discount_percentage"
                      min="1" max="100" required
                      value="<?php echo isset($_SESSION['form_data']['discount_percentage']) ? $_SESSION['form_data']['discount_percentage'] : $promotion->discount_percentage; ?>">
                    <span class="input-group-text">%</span>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label for="start_date" class="form-label">Ngày bắt đầu</label>
                    <input type="datetime-local" class="form-control" id="start_date" name="start_date" required
                      value="<?php echo isset($_SESSION['form_data']['start_date']) ? $_SESSION['form_data']['start_date'] : date('Y-m-d\TH:i', strtotime($start_date)); ?>">
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="end_date" class="form-label">Ngày kết thúc</label>
                    <input type="datetime-local" class="form-control" id="end_date" name="end_date" required
                      value="<?php echo isset($_SESSION['form_data']['end_date']) ? $_SESSION['form_data']['end_date'] : date('Y-m-d\TH:i', strtotime($end_date)); ?>">
                  </div>
                </div>

                <div class="mb-3">
                  <label class="form-label">Trạng thái</label>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="status" id="statusActive" value="active"
                      <?php echo (isset($_SESSION['form_data']['status']) ? $_SESSION['form_data']['status'] == 'active' : $promotion->status == 'active') ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="statusActive">
                      Hoạt động
                    </label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="status" id="statusInactive" value="inactive"
                      <?php echo (isset($_SESSION['form_data']['status']) ? $_SESSION['form_data']['status'] == 'inactive' : $promotion->status == 'inactive') ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="statusInactive">
                      Không hoạt động
                    </label>
                  </div>
                </div>

                <div class="d-grid gap-2">
                  <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> Cập nhật mã khuyến mãi
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>

        <div class="col-md-4">
          <div class="card mb-4">
            <div class="card-header bg-info text-white">
              <h5 class="card-title mb-0">Thông tin khuyến mãi</h5>
            </div>
            <div class="card-body">
              <p><strong>ID:</strong> <?php echo $promotion->id; ?></p>
              <p><strong>Ngày tạo:</strong> <?php echo date('d/m/Y H:i', strtotime($promotion->created_at)); ?></p>
              <p><strong>Cập nhật lần cuối:</strong> <?php echo date('d/m/Y H:i', strtotime($promotion->updated_at)); ?>
              </p>

              <?php
                            $now = new DateTime();
                            $start = new DateTime($promotion->start_date);
                            $end = new DateTime($promotion->end_date);

                            if ($now < $start) {
                                echo '<div class="alert alert-info">Khuyến mãi sẽ bắt đầu trong ' . $now->diff($start)->days . ' ngày nữa.</div>';
                            } else if ($now > $end) {
                                echo '<div class="alert alert-danger">Khuyến mãi đã kết thúc ' . $now->diff($end)->days . ' ngày trước.</div>';
                            } else {
                                echo '<div class="alert alert-success">Khuyến mãi đang diễn ra và sẽ kết thúc trong ' . $now->diff($end)->days . ' ngày nữa.</div>';
                            }
                            ?>
            </div>
          </div>

          <div class="card">
            <div class="card-header bg-warning text-dark">
              <h5 class="card-title mb-0">Lưu ý quan trọng</h5>
            </div>
            <div class="card-body">
              <ul class="list-unstyled">
                <li class="mb-2">
                  <i class="fas fa-exclamation-circle text-danger me-2"></i>
                  Khi thay đổi mã khuyến mãi, các booking đã sử dụng mã cũ sẽ không bị ảnh hưởng.
                </li>
                <li class="mb-2">
                  <i class="fas fa-exclamation-circle text-danger me-2"></i>
                  Việc thay đổi trạng thái sẽ có hiệu lực ngay lập tức.
                </li>
                <li>
                  <i class="fas fa-exclamation-circle text-danger me-2"></i>
                  Cập nhật phần trăm giảm giá sẽ ảnh hưởng đến tất cả các đơn hàng sau thời điểm cập nhật.
                </li>
              </ul>
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

  // Initial validation
  validateDates();
});
</script>

<?php
// Clear form data from session
if (isset($_SESSION['form_data'])) {
    unset($_SESSION['form_data']);
}
?>

<?php include 'views/shared/footer.php'; ?>