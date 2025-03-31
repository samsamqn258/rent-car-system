<?php include 'views/shared/header.php'; ?>

<div class="container mt-4">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Quản lý chủ xe</h5>
                </div>
                <div class="list-group list-group-flush">
                    <a href="<?php echo BASE_URL; ?>/cars/add" class="list-group-item list-group-item-action">
                        <i class="fas fa-plus-circle me-2"></i> Đăng xe mới
                    </a>
                    <a href="<?php echo BASE_URL; ?>/owner/cars" class="list-group-item list-group-item-action">
                        <i class="fas fa-car me-2"></i> Quản lý xe
                    </a>
                    <a href="<?php echo BASE_URL; ?>/owner/bookings" class="list-group-item list-group-item-action active">
                        <i class="fas fa-calendar-alt me-2"></i> Quản lý đơn thuê
                    </a>
                    <a href="<?php echo BASE_URL; ?>/owner/revenue" class="list-group-item list-group-item-action">
                        <i class="fas fa-chart-line me-2"></i> Doanh thu
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Quản lý đơn thuê xe</h2>
                <div class="input-group" style="width: 300px;">
                    <input type="text" class="form-control" id="searchBooking" placeholder="Tìm theo tên, xe...">
                    <button class="btn btn-outline-secondary" type="button">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>

            <!-- Filter and Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-2 mb-md-0">
                                    <select class="form-select" id="statusFilter">
                                        <option value="all">Tất cả trạng thái</option>
                                        <option value="pending">Chờ xác nhận</option>
                                        <option value="confirmed">Đã xác nhận</option>
                                        <option value="completed">Hoàn thành</option>
                                        <option value="canceled">Đã hủy</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <select class="form-select" id="timeFilter">
                                        <option value="all">Tất cả thời gian</option>
                                        <option value="upcoming">Sắp đến</option>
                                        <option value="ongoing">Đang diễn ra</option>
                                        <option value="past">Đã qua</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-info text-white">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title mb-0">Tổng đơn thuê</h6>
                                <h3 class="mb-0"><?php echo count($bookings); ?></h3>
                            </div>
                            <i class="fas fa-calendar-check fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (empty($bookings)): ?>
                <div class="alert alert-info">
                    <h4 class="alert-heading">Chưa có đơn thuê nào!</h4>
                    <p>Hiện tại bạn chưa có đơn thuê xe nào. Khi có người thuê xe của bạn, đơn thuê sẽ hiển thị ở đây.</p>
                </div>
            <?php else: ?>
                <!-- Bookings Table -->
                <div class="card">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover booking-table">
                                <thead class="table-light">
                                    <tr>
                                        <th>Mã đơn</th>
                                        <th>Xe</th>
                                        <th>Khách hàng</th>
                                        <th>Thời gian thuê</th>
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
                                        <tr class="booking-row" 
                                            data-status="<?php echo $booking['booking_status']; ?>" 
                                            data-time="<?php echo $time_status; ?>"
                                            data-search="<?php echo strtolower($booking['customer_name'] . ' ' . $booking['car_brand'] . ' ' . $booking['car_model']); ?>">
                                            <td><?php echo $booking['id']; ?></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="<?php echo BASE_URL . '/' . $booking['car_image']; ?>" alt="<?php echo $booking['car_brand'] . ' ' . $booking['car_model']; ?>" class="me-2 rounded" style="width: 40px; height: 30px; object-fit: cover;">
                                                    <div><?php echo $booking['car_brand'] . ' ' . $booking['car_model']; ?></div>
                                                </div>
                                            </td>
                                            <td>
                                                <div><?php echo $booking['customer_name']; ?></div>
                                                <div class="small text-muted"><?php echo $booking['customer_phone']; ?></div>
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
                                                <div class="d-flex flex-column">
                                                    <a href="<?php echo BASE_URL; ?>/booking/details/<?php echo $booking['id']; ?>" class="btn btn-sm btn-info mb-1">
                                                        <i class="fas fa-eye me-1"></i> Chi tiết
                                                    </a>
                                                    
                                                    <?php if ($booking['booking_status'] == 'pending' && $booking['payment_status'] == 'paid'): ?>
                                                        <form action="<?php echo BASE_URL; ?>/booking/update_status/<?php echo $booking['id']; ?>" method="post">
                                                            <input type="hidden" name="status" value="confirmed">
                                                            <button type="submit" class="btn btn-sm btn-success mb-1 w-100">
                                                                <i class="fas fa-check me-1"></i> Xác nhận
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>
                                                    
                                                    <?php if ($booking['booking_status'] == 'confirmed'): ?>
                                                        <form action="<?php echo BASE_URL; ?>/booking/update_status/<?php echo $booking['id']; ?>" method="post">
                                                            <input type="hidden" name="status" value="completed">
                                                            <button type="submit" class="btn btn-sm btn-primary mb-1 w-100">
                                                                <i class="fas fa-flag-checkered me-1"></i> Hoàn thành
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
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