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
                    <a href="<?php echo BASE_URL; ?>/user/profile" class="list-group-item list-group-item-action">
                        <i class="fas fa-user me-2"></i> Thông tin cá nhân
                    </a>
                    <a href="<?php echo BASE_URL; ?>/user/bookings" class="list-group-item list-group-item-action active">
                        <i class="fas fa-history me-2"></i> Lịch sử thuê xe
                    </a>
                    <a href="<?php echo BASE_URL; ?>/user/change_password" class="list-group-item list-group-item-action">
                        <i class="fas fa-key me-2"></i> Đổi mật khẩu
                    </a>
                    <?php if ($_SESSION['user_role'] == 'owner'): ?>
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

        <!-- Main Content -->
        <div class="col-md-9">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">Lịch sử thuê xe</h3>
                </div>
                <div class="card-body">
                    <!-- Filter options -->
                    <div class="row mb-3">
                        <div class="col-md-4 mb-2 mb-md-0">
                            <select class="form-select" id="statusFilter">
                                <option value="all">Tất cả trạng thái</option>
                                <option value="pending">Chờ xác nhận</option>
                                <option value="confirmed">Đã xác nhận</option>
                                <option value="completed">Hoàn thành</option>
                                <option value="canceled">Đã hủy</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-2 mb-md-0">
                            <select class="form-select" id="timeFilter">
                                <option value="all">Tất cả thời gian</option>
                                <option value="upcoming">Sắp đến</option>
                                <option value="ongoing">Đang diễn ra</option>
                                <option value="past">Đã qua</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group">
                                <input type="text" class="form-control" id="searchBooking" placeholder="Tìm kiếm...">
                                <button class="btn btn-outline-secondary" type="button">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Bookings list -->
                    <?php if (empty($bookings)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-car-side fa-4x text-muted mb-3"></i>
                            <h4>Bạn chưa có lịch sử thuê xe</h4>
                            <p class="text-muted">Hãy thuê một chiếc xe và bắt đầu hành trình của bạn!</p>
                            <a href="<?php echo BASE_URL; ?>/cars/search" class="btn btn-primary mt-2">
                                <i class="fas fa-search me-2"></i> Tìm xe ngay
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Xe</th>
                                        <th>Thời gian</th>
                                        <th>Tổng tiền</th>
                                        <th>Trạng thái</th>
                                        <th>Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $current_date = date('Y-m-d');
                                    foreach ($bookings as $booking):
                                        // Determine booking time status
                                        $time_status = 'past';
                                        if ($booking['start_date'] > $current_date) {
                                            $time_status = 'upcoming';
                                        } else if ($booking['end_date'] >= $current_date) {
                                            $time_status = 'ongoing';
                                        }
                                    ?>
                                        <tr class="booking-row" data-status="<?php echo $booking['booking_status']; ?>"
                                            data-time="<?php echo $time_status; ?>"
                                            data-search="<?php echo strtolower($booking['car_brand'] . ' ' . $booking['car_model']); ?>">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="<?php echo BASE_URL . '/' . $booking['car_image']; ?>"
                                                        alt="<?php echo $booking['car_brand'] . ' ' . $booking['car_model']; ?>" class="me-2 rounded"
                                                        style="width: 60px; height: 40px; object-fit: cover;">
                                                    <div>
                                                        <div class="fw-bold"><?php echo $booking['car_brand'] . ' ' . $booking['car_model']; ?></div>
                                                        <small class="text-muted">Đặt ngày:
                                                            <?php echo date('d/m/Y', strtotime($booking['created_at'])); ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div>Từ: <?php echo date('d/m/Y', strtotime($booking['start_date'])); ?></div>
                                                <div>Đến: <?php echo date('d/m/Y', strtotime($booking['end_date'])); ?></div>
                                                <div class="small text-muted">
                                                    <?php
                                                    $days = (strtotime($booking['end_date']) - strtotime($booking['start_date'])) / (60 * 60 * 24) + 1;
                                                    echo $days . ' ngày';
                                                    ?>
                                                </div>
                                            </td>
                                            <td><?php echo number_format($booking['total_price'], 0, ',', '.'); ?> VND</td>
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

                                                echo '<div class="mt-1">';
                                                if ($booking['payment_status'] == 'paid') {
                                                    echo '<span class="badge bg-success">Đã thanh toán</span>';
                                                } else {
                                                    echo '<span class="badge bg-warning text-dark">Chưa thanh toán</span>';
                                                }
                                                echo '</div>';
                                                ?>
                                            </td>
                                            <td>
                                                <div class="btn-group-vertical w-100">
                                                    <a href="<?php echo BASE_URL; ?>/booking/details/<?php echo $booking['id']; ?>"
                                                        class="btn btn-sm btn-info mb-1">
                                                        <i class="fas fa-eye me-1"></i> Chi tiết
                                                    </a>

                                                    <?php if ($booking['payment_status'] == 'pending'): ?>
                                                        <a href="<?php echo BASE_URL; ?>/booking/payment/<?php echo $booking['id']; ?>"
                                                            class="btn btn-sm btn-primary mb-1">
                                                            <i class="fas fa-credit-card me-1"></i> Thanh toán
                                                        </a>
                                                    <?php endif; ?>

                                                    <?php if ($booking['booking_status'] == 'pending' && $booking['start_date'] > date('Y-m-d')): ?>
                                                        <a href="<?php echo BASE_URL; ?>/booking/cancel/<?php echo $booking['id']; ?>"
                                                            class="btn btn-sm btn-danger mb-1"
                                                            onclick="return confirm('Bạn có chắc chắn muốn hủy đơn đặt xe này?');">
                                                            <i class="fas fa-times me-1"></i> Hủy đặt
                                                        </a>
                                                    <?php endif; ?>

                                                    <?php if ($booking['can_be_reviewed']): ?>
                                                        <a href="<?php echo BASE_URL; ?>/review/create/<?php echo $booking['id']; ?>"
                                                            class="btn btn-sm btn-warning mb-1">
                                                            <i class="fas fa-star me-1"></i> Đánh giá
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Filtering functionality
        const statusFilter = document.getElementById('statusFilter');
        const timeFilter = document.getElementById('timeFilter');
        const searchInput = document.getElementById('searchBooking');
        const rows = document.querySelectorAll('.booking-row');

        function applyFilters() {
            const statusValue = statusFilter.value;
            const timeValue = timeFilter.value;
            const searchValue = searchInput.value.toLowerCase();

            rows.forEach(row => {
                const status = row.dataset.status;
                const time = row.dataset.time;
                const searchText = row.dataset.search;

                const matchesStatus = statusValue === 'all' || status === statusValue;
                const matchesTime = timeValue === 'all' || time === timeValue;
                const matchesSearch = searchValue === '' || searchText.includes(searchValue);

                if (matchesStatus && matchesTime && matchesSearch) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        statusFilter.addEventListener('change', applyFilters);
        timeFilter.addEventListener('change', applyFilters);
        searchInput.addEventListener('input', applyFilters);
    });
</script>

<?php include 'views/shared/footer.php'; ?>