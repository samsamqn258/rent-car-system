<?php include 'views/shared/header.php'; ?>

<div class="container mt-4">
  <div class="row">
    <div class="col-md-8">
      <div class="card">
        <div class="card-header bg-primary text-white">
          <h3 class="mb-0">Chi tiết đơn thuê xe #<?php echo $booking['id']; ?></h3>
        </div>
        <div class="card-body">
          <div class="row mb-4">
            <div class="col-md-6">
              <h5 class="card-title">Thông tin xe</h5>
              <div class="d-flex mb-3">
                <div class="me-3">
                  <img src="<?php echo BASE_URL . '/' . $booking['car_image']; ?>"
                    alt="<?php echo $booking['car_brand'] . ' ' . $booking['car_model']; ?>" class="rounded"
                    style="width: 100px; height: 70px; object-fit: cover;">
                </div>
                <div>
                  <h6><?php echo $booking['car_brand'] . ' ' . $booking['car_model']; ?></h6>
                  <a href="<?php echo BASE_URL; ?>/cars/details/<?php echo $booking['car_id']; ?>"
                    class="btn btn-sm btn-outline-primary mt-1">
                    <i class="fas fa-external-link-alt me-1"></i> Xem xe
                  </a>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <h5 class="card-title">
                <?php if ($booking['user_id'] == $_SESSION['user_id']): ?>
                Thông tin chủ xe
                <?php else: ?>
                Thông tin khách hàng
                <?php endif; ?>
              </h5>
              <p>
                <i class="fas fa-user me-2 text-primary"></i>
                <?php echo $booking['customer_name']; ?>

              </p>
              <?php if ($_SESSION['user_role'] == 'admin' || $_SESSION['user_id'] == $booking['owner_id']): ?>
              <p>
                <i class="fas fa-phone me-2 text-primary"></i>
                <?php echo $booking['customer_phone']; ?>
              </p>
              <?php endif; ?>
            </div>
          </div>

          <div class="row mb-4">
            <div class="col-12">
              <div class="booking-timeline">
                <div class="d-flex justify-content-between position-relative timeline-header">
                  <div class="timeline-point active">
                    <div class="timeline-icon">
                      <i class="fas fa-clipboard-check"></i>
                    </div>
                    <div class="timeline-label">Đặt xe</div>
                  </div>
                  <div
                    class="timeline-point <?php echo in_array($booking['booking_status'], ['confirmed', 'completed', 'canceled']) ? 'active' : ''; ?>">
                    <div class="timeline-icon">
                      <i class="fas fa-credit-card"></i>
                    </div>
                    <div class="timeline-label">Thanh toán</div>
                  </div>
                  <div
                    class="timeline-point <?php echo in_array($booking['booking_status'], ['confirmed', 'completed']) ? 'active' : ''; ?>">
                    <div class="timeline-icon">
                      <i class="fas fa-car"></i>
                    </div>
                    <div class="timeline-label">Xác nhận</div>
                  </div>
                  <div class="timeline-point <?php echo $booking['booking_status'] == 'completed' ? 'active' : ''; ?>">
                    <div class="timeline-icon">
                      <i class="fas fa-flag-checkered"></i>
                    </div>
                    <div class="timeline-label">Hoàn thành</div>
                  </div>
                  <div class="timeline-line"></div>
                </div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <h5 class="card-title">Thông tin đặt xe</h5>
              <table class="table table-borderless">
                <tr>
                  <th style="width: 40%">Mã đặt xe:</th>
                  <td>#<?php echo $booking['id']; ?></td>
                </tr>
                <tr>
                  <th>Ngày bắt đầu:</th>
                  <td><?php echo date('d/m/Y', strtotime($booking['start_date'])); ?></td>
                </tr>
                <tr>
                  <th>Ngày kết thúc:</th>
                  <td><?php echo date('d/m/Y', strtotime($booking['end_date'])); ?></td>
                </tr>
                <tr>
                  <th>Số ngày thuê:</th>
                  <td>
                    <?php echo (strtotime($booking['end_date']) - strtotime($booking['start_date'])) / (60 * 60 * 24) + 1; ?>
                    ngày</td>
                </tr>
                <tr>
                  <th>Ngày đặt:</th>
                  <td><?php echo date('d/m/Y H:i', strtotime($booking['created_at'])); ?></td>
                </tr>
              </table>
            </div>
            <div class="col-md-6">
              <h5 class="card-title">Thông tin thanh toán</h5>
              <table class="table table-borderless">
                <tr>
                  <th style="width: 40%">Tổng tiền:</th>
                  <td class="text-primary fw-bold"><?php echo number_format($booking['total_price'], 0, ',', '.'); ?>
                    VND</td>
                </tr>
                <tr>
                  <th>Phương thức:</th>
                  <td>MoMo</td>
                </tr>
                <tr>
                  <th>Trạng thái thanh toán:</th>
                  <td>
                    <?php if ($booking['payment_status'] == 'paid'): ?>
                    <span class="badge bg-success">Đã thanh toán</span>
                    <?php elseif ($booking['payment_status'] == 'pending'): ?>
                    <span class="badge bg-warning text-dark">Chờ thanh toán</span>
                    <?php else: ?>
                    <span class="badge bg-danger">Hoàn trả</span>
                    <?php endif; ?>
                  </td>
                </tr>
                <tr>
                  <th>Trạng thái đặt xe:</th>
                  <td>
                    <?php
                                        switch ($booking['booking_status']) {
                                            case 'pending':
                                                echo '<span class="badge bg-warning text-dark">Chờ xác nhận</span>';
                                                break;
                                            case 'confirmed':
                                                echo '<span class="badge bg-primary">Đã xác nhận</span>';
                                                break;
                                            case 'completed':
                                                echo '<span class="badge bg-success">Hoàn thành</span>';
                                                break;
                                            case 'canceled':
                                                echo '<span class="badge bg-danger">Đã hủy</span>';
                                                break;
                                            default:
                                                echo '<span class="badge bg-secondary">Không xác định</span>';
                                        }
                                        ?>
                  </td>
                </tr>
              </table>
            </div>
          </div>
        </div>
        <div class="card-footer">
          <div class="d-flex justify-content-between">
            <a href="<?php echo BASE_URL; ?>/user/bookings" class="btn btn-secondary">
              <i class="fas fa-arrow-left me-1"></i> Quay lại
            </a>

            <!-- Action buttons based on user role and booking status -->
            <div>
              <?php if ($_SESSION['user_id'] == $booking['user_id']): ?>
              <!-- Customer actions -->
              <?php if ($booking['payment_status'] == 'pending'): ?>
              <a href="<?php echo BASE_URL; ?>/booking/payment/<?php echo $booking['id']; ?>" class="btn btn-primary">
                <i class="fas fa-credit-card me-1"></i> Thanh toán
              </a>
              <?php endif; ?>

              <?php if ($booking['booking_status'] == 'pending' && strtotime($booking['start_date']) > time()): ?>
              <a href="<?php echo BASE_URL; ?>/booking/cancel/<?php echo $booking['id']; ?>" class="btn btn-danger"
                onclick="return confirm('Bạn có chắc chắn muốn hủy đơn đặt xe này?');">
                <i class="fas fa-times me-1"></i> Hủy đặt xe
              </a>
              <?php endif; ?>

              <?php if ($booking['can_be_reviewed']): ?>
              <a href="<?php echo BASE_URL; ?>/review/create/<?php echo $booking['id']; ?>" class="btn btn-warning">
                <i class="fas fa-star me-1"></i> Đánh giá
              </a>
              <?php endif; ?>
              <?php elseif ($_SESSION['user_id'] == $booking['owner_id']): ?>
              <!-- Car owner actions -->
              <?php if ($booking['booking_status'] == 'pending' && $booking['payment_status'] == 'paid'): ?>
              <form action="<?php echo BASE_URL; ?>/booking/update_status/<?php echo $booking['id']; ?>" method="post"
                class="d-inline">
                <input type="hidden" name="status" value="confirmed">
                <button type="submit" class="btn btn-success">
                  <i class="fas fa-check me-1"></i> Xác nhận
                </button>
              </form>
              <?php endif; ?>

              <?php if ($booking['booking_status'] == 'confirmed'): ?>
              <form action="<?php echo BASE_URL; ?>/booking/update_status/<?php echo $booking['id']; ?>" method="post"
                class="d-inline">
                <input type="hidden" name="status" value="completed">
                <button type="submit" class="btn btn-success">
                  <i class="fas fa-flag-checkered me-1"></i> Hoàn thành
                </button>
              </form>
              <?php endif; ?>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>

      <!-- Booking Notes or Messages (if needed) -->
      <?php if (!empty($booking['notes'])): ?>
      <div class="card mt-4">
        <div class="card-header bg-info text-white">
          <h5 class="mb-0">Ghi chú</h5>
        </div>
        <div class="card-body">
          <p><?php echo nl2br($booking['notes']); ?></p>
        </div>
      </div>
      <?php endif; ?>
    </div>

    <div class="col-md-4">

      <!-- Important Information -->
      <div class="card">
        <div class="card-header bg-warning text-dark">
          <h5 class="mb-0">Thông tin quan trọng</h5>
        </div>
        <div class="card-body">
          <ul class="list-group list-group-flush">
            <li class="list-group-item d-flex">
              <i class="fas fa-id-card text-primary me-3 mt-1"></i>
              <div>
                <strong>Giấy tờ cần thiết</strong>
                <p class="mb-0">CMND/CCCD, bằng lái xe B1/B2</p>
              </div>
            </li>
            <li class="list-group-item d-flex">
              <i class="fas fa-gas-pump text-primary me-3 mt-1"></i>
              <div>
                <strong>Nhiên liệu</strong>
                <p class="mb-0">Khách hàng tự trả phí nhiên liệu</p>
              </div>
            </li>
            <li class="list-group-item d-flex">
              <i class="fas fa-clock text-primary me-3 mt-1"></i>
              <div>
                <strong>Thời gian nhận xe</strong>
                <p class="mb-0">Từ 8:00 sáng ngày <?php echo date('d/m/Y', strtotime($booking['start_date'])); ?></p>
              </div>
            </li>
            <li class="list-group-item d-flex">
              <i class="fas fa-clock text-primary me-3 mt-1"></i>
              <div>
                <strong>Thời gian trả xe</strong>
                <p class="mb-0">Trước 20:00 tối ngày <?php echo date('d/m/Y', strtotime($booking['end_date'])); ?></p>
              </div>
            </li>
            <li class="list-group-item d-flex">
              <i class="fas fa-phone text-primary me-3 mt-1"></i>
              <div>
                <strong>Hỗ trợ</strong>
                <p class="mb-0">Hotline: 0123 456 789</p>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
.booking-timeline {
  padding: 20px 0;
}

.timeline-header {
  padding: 0 20px;
}

.timeline-point {
  text-align: center;
  z-index: 1;
}

.timeline-icon {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  background-color: #e9ecef;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 10px;
  font-size: 20px;
  color: #6c757d;
}

.timeline-point.active .timeline-icon {
  background-color: #0d6efd;
  color: white;
}

.timeline-label {
  font-size: 14px;
  color: #6c757d;
}

.timeline-point.active .timeline-label {
  color: #0d6efd;
  font-weight: bold;
}

.timeline-line {
  position: absolute;
  top: 25px;
  left: 0;
  right: 0;
  height: 3px;
  background-color: #e9ecef;
  z-index: 0;
}
</style>

<?php include 'views/shared/footer.php'; ?>