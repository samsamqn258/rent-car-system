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
                    <a href="<?php echo BASE_URL; ?>/owner/cars" class="list-group-item list-group-item-action active">
                        <i class="fas fa-car me-2"></i> Quản lý xe
                    </a>
                    <a href="<?php echo BASE_URL; ?>/owner/bookings" class="list-group-item list-group-item-action">
                        <i class="fas fa-calendar-alt me-2"></i> Quản lý đơn thuê
                    </a>
                    <a href="<?php echo BASE_URL; ?>/owner/revenue?period=week" class="list-group-item list-group-item-action">
                        <i class="fas fa-chart-line me-2"></i> Doanh thu
                    </a>
                    <a href="<?php echo BASE_URL; ?>/owner/contracts" class="list-group-item list-group-item-action">
                        <i class="fas fa-chart-line me-2"></i> Hợp đồng của tôi
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Quản lý xe</h2>
                <a href="<?php echo BASE_URL; ?>/cars/add" class="btn btn-primary">
                    <i class="fas fa-plus-circle me-2"></i> Đăng xe mới
                </a>
            </div>

            <!-- Car Status Filter -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-0">Bộ lọc</h5>
                        </div>
                        <div>
                            <select class="form-select" id="statusFilter">
                                <option value="all">Tất cả trạng thái</option>
                                <option value="available">Có sẵn</option>
                                <option value="rented">Đang cho thuê</option>
                                <option value="pending">Chờ duyệt</option>
                                <option value="rejected">Bị từ chối</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (empty($cars)): ?>
                <div class="alert alert-info">
                    <h4 class="alert-heading">Chưa có xe nào!</h4>
                    <p>Bạn chưa đăng xe nào lên hệ thống. Hãy bắt đầu bằng cách đăng xe đầu tiên của bạn.</p>
                    <hr>
                    <p class="mb-0">Nhấp vào nút "Đăng xe mới" để bắt đầu.</p>
                </div>
            <?php else: ?>
                <!-- Car List -->
                <div class="row" id="carList">
                    <?php foreach ($cars as $car): ?>
                        <div class="col-md-6 mb-4 car-item" data-status="<?php echo $car['status']; ?>">
                            <div class="card h-100">
                                <div class="position-relative">
                                    <img src="<?php echo BASE_URL . '/' . $car['primary_image']; ?>" class="card-img-top"
                                        alt="<?php echo $car['brand'] . ' ' . $car['model']; ?>" style="height: 180px; object-fit: cover;">

                                    <!-- Status Badge -->
                                    <?php
                                    $status_class = '';
                                    $status_text = '';

                                    switch ($car['status']) {
                                        case 'available':
                                            $status_class = 'bg-success';
                                            $status_text = 'Có sẵn';
                                            break;
                                        case 'rented':
                                            $status_class = 'bg-primary';
                                            $status_text = 'Đang cho thuê';
                                            break;
                                        case 'pending':
                                        case 'unapproved':
                                            $status_class = 'bg-warning text-dark';
                                            $status_text = 'Chờ duyệt';
                                            break;
                                        case 'rejected':
                                            $status_class = 'bg-danger';
                                            $status_text = 'Bị từ chối';
                                            break;
                                        default:
                                            $status_class = 'bg-secondary';
                                            $status_text = 'Không xác định';
                                    }
                                    ?>
                                    <span class="position-absolute top-0 end-0 mt-2 me-2 badge <?php echo $status_class; ?>">
                                        <?php echo $status_text; ?>
                                    </span>
                                </div>

                                <div class="card-body">
                                    <h5 class="card-title"><?php echo $car['brand'] . ' ' . $car['model'] . ' (' . $car['year'] . ')'; ?></h5>

                                    <div class="car-specs mb-2">
                                        <span class="badge bg-secondary me-1">
                                            <i class="fas fa-gas-pump me-1"></i>
                                            <?php echo $car['car_type'] == 'electric' ? 'Xe điện' : ($car['car_type'] == 'gasoline' ? 'Xe xăng' : 'Xe dầu'); ?>
                                        </span>
                                        <span class="badge bg-secondary me-1">
                                            <i class="fas fa-user me-1"></i>
                                            <?php echo $car['seats']; ?> chỗ
                                        </span>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div class="car-price text-primary fw-bold">
                                            <?php echo number_format($car['price_per_day'], 0, ',', '.'); ?> VND/ngày
                                        </div>
                                        <div class="car-rating">
                                            <i class="fas fa-star text-warning"></i>
                                            <span><?php echo $car['avg_rating'] ? number_format($car['avg_rating'], 1) : 'N/A'; ?></span>
                                            <small class="text-muted">(<?php echo $car['review_count']; ?>)</small>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">Đăng ngày: <?php echo date('d/m/Y', strtotime($car['created_at'])); ?></small>
                                    </div>
                                </div>

                                <div class="card-footer bg-white">
                                    <div class="d-flex justify-content-between">
                                        <a href="<?php echo BASE_URL; ?>/cars/details/<?php echo $car['id']; ?>"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye me-1"></i> Xem
                                        </a>
                                        <a href="<?php echo BASE_URL; ?>/cars/edit/<?php echo $car['id']; ?>"
                                            class="btn btn-sm btn-outline-secondary">
                                            <i class="fas fa-edit me-1"></i> Sửa
                                        </a>
                                        <?php if ($car['status'] == 'rejected'): ?>
                                            <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal"
                                                data-bs-target="#rejectionReason<?php echo $car['id']; ?>">
                                                <i class="fas fa-info-circle me-1"></i> Lý do
                                            </button>

                                            <!-- Rejection Reason Modal -->
                                            <div class="modal fade" id="rejectionReason<?php echo $car['id']; ?>" tabindex="-1"
                                                aria-labelledby="rejectionReasonLabel<?php echo $car['id']; ?>" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="rejectionReasonLabel<?php echo $car['id']; ?>">Lý do từ chối</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>Admin đã từ chối xe của bạn vì lý do sau:</p>
                                                            <div class="alert alert-danger">
                                                                <?php echo !empty($car['rejection_reason']) ? $car['rejection_reason'] : 'Không đáp ứng yêu cầu của hệ thống.'; ?>
                                                            </div>
                                                            <p>Bạn có thể chỉnh sửa thông tin xe và gửi lại để được duyệt.</p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                                            <a href="<?php echo BASE_URL; ?>/cars/edit/<?php echo $car['id']; ?>"
                                                                class="btn btn-primary">Chỉnh sửa xe</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Status filter functionality
        const statusFilter = document.getElementById('statusFilter');
        const carItems = document.querySelectorAll('.car-item');

        statusFilter.addEventListener('change', function() {
            const selectedStatus = this.value;

            carItems.forEach(item => {
                const carStatus = item.dataset.status;

                if (selectedStatus === 'all' || selectedStatus === carStatus) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });
</script>

<?php include 'views/shared/footer.php'; ?>