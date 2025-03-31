<?php include 'views/shared/header.php'; 

?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3>Đặt xe</h3>
                </div>
                <div class="card-body">
                    <div class="car-summary mb-4">
                        <h4><?php echo $car_details['brand'] . ' ' . $car_details['model'] . ' (' . $car_details['year'] . ')'; ?></h4>
                        <div class="d-flex">
                            <div class="pe-4">
                                <img src="<?php echo BASE_URL . '/' . $car_details['images'][0]['image_path']; ?>" alt="<?php echo $car_details['brand'] . ' ' . $car_details['model']; ?>" style="width: 150px; height: 100px; object-fit: cover;">
                            </div>
                            <div>
                                <p><strong>Giá thuê:</strong> <?php echo number_format($car_details['price_per_day'], 0, ',', '.'); ?> VND / ngày</p>
                                <p><strong>Loại xe:</strong> <?php echo $car_details['car_type'] == 'electric' ? 'Xe điện' : ($car_details['car_type'] == 'gasoline' ? 'Xe xăng' : 'Xe dầu'); ?></p>
                                <p><strong>Số chỗ ngồi:</strong> <?php echo $car_details['seats']; ?></p>
                                <p><strong>Địa chỉ:</strong> <?php echo $car_details['address']; ?></p>
                            </div>
                        </div>
                    </div>

                    <form action="<?php echo BASE_URL . '/booking/create/' . $car_details['id']; ?>" method="post" id="booking-form">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="start_date" class="form-label">Ngày bắt đầu:</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" min="<?php echo date('Y-m-d'); ?>" required
                                    value="<?php echo isset($_SESSION['form_data']['start_date']) ? $_SESSION['form_data']['start_date'] : date('Y-m-d'); ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="end_date" class="form-label">Ngày kết thúc:</label>
                                <input type="date" class="form-control" id="end_date" name="end_date" min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" required
                                    value="<?php echo isset($_SESSION['form_data']['end_date']) ? $_SESSION['form_data']['end_date'] : date('Y-m-d', strtotime('+1 day')); ?>">
                            </div>
                        </div>

                        <div class="alert alert-info" id="pricing-info">
                            <div class="d-flex justify-content-between">
                                <span>Giá thuê xe mỗi ngày:</span>
                                <span><?php echo number_format($car_details['price_per_day'], 0, ',', '.'); ?> VND</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Số ngày thuê:</span>
                                <span id="total-days">1</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between">
                                <strong>Tổng tiền:</strong>
                                <strong id="total-price"><?php echo number_format($car_details['price_per_day'], 0, ',', '.'); ?> VND</strong>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100 mt-3">Tiến hành thanh toán</button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h3>Lưu ý khi thuê xe</h3>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex align-items-center">
                            <i class="fas fa-info-circle text-primary me-3"></i>
                            <div>
                                <strong>Giấy tờ cần thiết</strong>
                                <p class="mb-0">CMND/CCCD, bằng lái xe B1/B2</p>
                            </div>
                        </li>
                        <li class="list-group-item d-flex align-items-center">
                            <i class="fas fa-clock text-primary me-3"></i>
                            <div>
                                <strong>Thời gian thuê</strong>
                                <p class="mb-0">Tính theo ngày (24 giờ)</p>
                            </div>
                        </li>
                        <li class="list-group-item d-flex align-items-center">
                            <i class="fas fa-gas-pump text-primary me-3"></i>
                            <div>
                                <strong>Nhiên liệu</strong>
                                <p class="mb-0">Khách hàng tự trả phí nhiên liệu</p>
                            </div>
                        </li>
                        <li class="list-group-item d-flex align-items-center">
                            <i class="fas fa-money-bill-wave text-primary me-3"></i>
                            <div>
                                <strong>Thanh toán</strong>
                                <p class="mb-0">Thanh toán trước 100% qua MoMo</p>
                            </div>
                        </li>
                        <li class="list-group-item d-flex align-items-center">
                            <i class="fas fa-ban text-primary me-3"></i>
                            <div>
                                <strong>Hủy đặt xe</strong>
                                <p class="mb-0">Có thể hủy trước ngày nhận xe</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Car Owner Info -->
            <div class="card mt-3">
                <div class="card-header bg-success text-white">
                    <h3>Thông tin chủ xe</h3>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <i class="fas fa-user-circle fa-3x text-secondary me-3"></i>
                        <div>
                            <h5 class="mb-0"><?php echo $car_details['owner_name']; ?></h5>
                            <div class="text-muted">Chủ xe</div>
                        </div>
                    </div>
                    <p><i class="fas fa-phone me-2 text-primary"></i> Liên hệ khi cần thiết</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    const totalDaysElement = document.getElementById('total-days');
    const totalPriceElement = document.getElementById('total-price');
    const pricePerDay = <?php echo $car_details['price_per_day']; ?>;
    
    function updateTotalPrice() {
        const startDate = new Date(startDateInput.value);
        const endDate = new Date(endDateInput.value);
        
        if (startDate && endDate && endDate >= startDate) {
            // Calculate days difference (including both start and end dates)
            const timeDiff = endDate - startDate;
            const days = Math.floor(timeDiff / (1000 * 60 * 60 * 24)) + 1;
            
            totalDaysElement.textContent = days;
            const totalPrice = days * pricePerDay;
            totalPriceElement.textContent = new Intl.NumberFormat('vi-VN').format(totalPrice) + ' VND';
        }
    }
    
    // Set minimum end date based on start date
    startDateInput.addEventListener('change', function() {
        const startDate = new Date(startDateInput.value);
        const nextDay = new Date(startDate);
        nextDay.setDate(nextDay.getDate() + 1);
        
        endDateInput.min = nextDay.toISOString().split('T')[0];
        
        // If current end date is before new min, update it
        if (new Date(endDateInput.value) < nextDay) {
            endDateInput.value = nextDay.toISOString().split('T')[0];
        }
        
        updateTotalPrice();
    });
    
    endDateInput.addEventListener('change', updateTotalPrice);
    
    // Initial calculation
    updateTotalPrice();
});
</script>

<?php
// Clear form data from session
if (isset($_SESSION['form_data'])) {
    unset($_SESSION['form_data']);
}
?>

<?php include 'views/shared/footer.php'; ?>