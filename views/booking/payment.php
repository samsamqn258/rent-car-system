<?php include 'views/shared/header.php'; ?>

<div class="container mt-4">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card">
        <div class="card-header text-white" style="background-color: #5fcf86;">
          <h3 class="mb-0">Thanh toán đặt xe</h3>
        </div>
        <div class="card-body">
          <div class="booking-summary mb-4">
            <h4>Thông tin đặt xe</h4>
            <div class="row">
              <div class="col-md-6">
                <p><strong>Mã đặt xe:</strong> #<?php echo $booking['id']; ?></p>
                <p><strong>Xe:</strong> <?php echo $booking['car_brand'] . ' ' . $booking['car_model']; ?></p>
                <p><strong>Ngày bắt đầu:</strong> <?php echo date('d/m/Y', strtotime($booking['start_date'])); ?></p>
                <p><strong>Ngày kết thúc:</strong> <?php echo date('d/m/Y', strtotime($booking['end_date'])); ?></p>
              </div>
              <div class="col-md-6">
                <p><strong>Số ngày thuê:</strong>
                  <?php echo (strtotime($booking['end_date']) - strtotime($booking['start_date'])) / (60 * 60 * 24) + 1; ?>
                  ngày</p>
                <p><strong>Tổng tiền:</strong> <?php echo number_format($booking['total_price'], 0, ',', '.'); ?> VND
                </p>
                <p><strong>Trạng thái đặt xe:</strong> <span class="badge bg-warning">Chờ thanh toán</span></p>
                <p><strong>Trạng thái thanh toán:</strong> <span class="badge bg-warning">Chưa thanh toán</span></p>
              </div>
            </div>
          </div>

          <hr>

          <h4 class="mb-3">Chọn phương thức thanh toán</h4>

          <div class="payment-methods">
            <div class="form-check mb-3">
              <input class="form-check-input" type="radio" name="payment_method" id="momo" value="momo" checked>
              <label class="form-check-label d-flex align-items-center" for="momo">
                <img src="<?php echo BASE_URL; ?>/public/images/momo-logo.png" alt="MoMo" width="40" class="me-2">
                <span>Ví điện tử MoMo</span>
              </label>
            </div>
          </div>

          <div class="alert alert-success mt-3">
            <p class="mb-0"><i class="fas fa-info-circle me-2"></i> Bạn sẽ được chuyển đến cổng thanh toán MoMo để hoàn
              tất thanh toán.</p>
          </div>

          <div class="mt-4 text-center">
            <a href="<?php echo BASE_URL; ?>/booking/process_payment/<?php echo $booking['id']; ?>"
              class="btn text-white btn-lg" style="background-color: #5fcf86;">
              <i class="fas fa-credit-card me-2"></i> Thanh toán ngay
            </a>
            <a href="<?php echo BASE_URL; ?>/booking/details/<?php echo $booking['id']; ?>"
              class="btn btn-outline-secondary btn-lg ms-2">
              <i class="fas fa-arrow-left me-2"></i> Quay lại
            </a>
          </div>
        </div>
      </div>

      <div class="card mt-4">
        <div class="card-header text-white" style="background-color: #5fcf86;">
          <h4 class="mb-0">Lưu ý khi thanh toán</h4>
        </div>
        <div class="card-body">
          <ul class="list-group list-group-flush">
            <li class="list-group-item d-flex">
              <i class="fas fa-check-circle text-success me-3 mt-1"></i>
              <div>
                <strong>Bảo mật thanh toán</strong>
                <p class="mb-0">Thông tin thanh toán của bạn được mã hóa và bảo vệ bởi MoMo.</p>
              </div>
            </li>
            <li class="list-group-item d-flex">
              <i class="fas fa-check-circle text-success me-3 mt-1"></i>
              <div>
                <strong>Xác nhận tự động</strong>
                <p class="mb-0">Đơn đặt xe sẽ được xác nhận ngay sau khi thanh toán thành công.</p>
              </div>
            </li>
            <li class="list-group-item d-flex">
              <i class="fas fa-exclamation-circle text-warning me-3 mt-1"></i>
              <div>
                <strong>Chưa thanh toán?</strong>
                <p class="mb-0">Đơn đặt xe chưa thanh toán sẽ tự động hủy sau 30 phút.</p>
              </div>
            </li>
            <li class="list-group-item d-flex">
              <i class="fas fa-phone-alt text-primary me-3 mt-1"></i>
              <div>
                <strong>Hỗ trợ thanh toán</strong>
                <p class="mb-0">Nếu gặp vấn đề khi thanh toán, vui lòng liên hệ với chúng tôi.</p>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include 'views/shared/footer.php'; ?>