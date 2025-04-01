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
                        <a class="nav-link" href="<?php echo BASE_URL; ?>/admin/bookings">
                            <i class="fas fa-car me-2"></i> Quản lý đơn thuê
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
                </ul>
            </div>
        </div>

        <!-- Main content -->
        <div class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Quản lý khuyến mãi</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="<?php echo BASE_URL; ?>/admin/promotions/add" class="btn btn-primary me-2">
                        <i class="fas fa-plus-circle me-1"></i> Thêm mã khuyến mãi
                    </a>
                    <button type="button" class="btn btn-outline-secondary" id="exportToCSV">
                        <i class="fas fa-download me-1"></i> Xuất CSV
                    </button>
                </div>
            </div>

            <!-- Promotion Filter -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-2 mb-md-0">
                            <select class="form-select" id="statusFilter">
                                <option value="all">Tất cả trạng thái</option>
                                <option value="active">Đang hoạt động</option>
                                <option value="inactive">Không hoạt động</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-2 mb-md-0">
                            <select class="form-select" id="timeFilter">
                                <option value="all">Tất cả thời gian</option>
                                <option value="current">Đang diễn ra</option>
                                <option value="upcoming">Sắp tới</option>
                                <option value="expired">Đã hết hạn</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-2 mb-md-0">
                            <input type="text" class="form-control" id="searchInput" placeholder="Tìm kiếm mã khuyến mãi...">
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-primary w-100" id="resetFilters">
                                <i class="fas fa-sync-alt me-1"></i> Đặt lại
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Promotions Table -->
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="promotionsTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Mã khuyến mãi</th>
                                    <th>Giảm giá</th>
                                    <th>Thời gian</th>
                                    <th>Trạng thái</th>
                                    <th>Ngày tạo</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($promotions)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center">Không có mã khuyến mãi nào.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php
                                    $current_date = date('Y-m-d H:i:s');
                                    foreach ($promotions as $promotion):
                                        // Determine promotion time status
                                        $time_status = 'expired';
                                        if ($promotion['start_date'] > $current_date) {
                                            $time_status = 'upcoming';
                                        } else if ($promotion['end_date'] > $current_date) {
                                            $time_status = 'current';
                                        }
                                    ?>
                                        <tr data-status="<?php echo $promotion['status']; ?>" data-time="<?php echo $time_status; ?>" data-search="<?php echo strtolower($promotion['code']); ?>">
                                            <td><?php echo $promotion['id']; ?></td>
                                            <td>
                                                <span class="badge bg-dark"><?php echo $promotion['code']; ?></span>
                                            </td>
                                            <td><?php echo $promotion['discount_percentage']; ?>%</td>
                                            <td>
                                                <div>Từ: <?php echo date('d/m/Y H:i', strtotime($promotion['start_date'])); ?></div>
                                                <div>Đến: <?php echo date('d/m/Y H:i', strtotime($promotion['end_date'])); ?></div>
                                            </td>
                                            <td>
                                                <?php if ($promotion['status'] == 'active'): ?>
                                                    <span class="badge bg-success">Hoạt động</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Không hoạt động</span>
                                                <?php endif; ?>

                                                <?php if ($time_status == 'current'): ?>
                                                    <span class="badge bg-primary">Đang diễn ra</span>
                                                <?php elseif ($time_status == 'upcoming'): ?>
                                                    <span class="badge bg-info text-dark">Sắp tới</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Đã hết hạn</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo date('d/m/Y', strtotime($promotion['created_at'])); ?></td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="<?php echo BASE_URL; ?>/admin/promotions/edit/<?php echo $promotion['id']; ?>" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $promotion['id']; ?>">
                                                        <i class="fas fa-trash"></i>
                                                    </button>

                                                    <!-- Delete Modal -->
                                                    <div class="modal fade" id="deleteModal<?php echo $promotion['id']; ?>" tabindex="-1" aria-labelledby="deleteModalLabel<?php echo $promotion['id']; ?>" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="deleteModalLabel<?php echo $promotion['id']; ?>">Xóa mã khuyến mãi</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <p>Bạn có chắc chắn muốn xóa mã khuyến mãi <strong><?php echo $promotion['code']; ?></strong>?</p>
                                                                    <p class="text-danger">Hành động này không thể hoàn tác.</p>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                                                    <a href="<?php echo BASE_URL; ?>/admin/promotions/delete/<?php echo $promotion['id']; ?>" class="btn btn-danger">Xóa</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // 1. KHỞI TẠO FILTERS VÀ SEARCH
        const statusFilter = document.getElementById('statusFilter');
        const timeFilter = document.getElementById('timeFilter');
        const searchInput = document.getElementById('searchInput');
        const resetButton = document.getElementById('resetFilters');
        const rows = document.querySelectorAll('#promotionsTable tbody tr');

        // 2. CHỨC NĂNG MODAL - SỬA LỖI NHẤP NHÁY
        // Biến kiểm soát để tránh nhấp nháy khi các sự kiện được gọi nhiều lần
        let isProcessingModalAction = false;

        // Vô hiệu hóa tất cả chuyển động CSS
        const disableAnimationsStyle = document.createElement('style');
        disableAnimationsStyle.textContent = `
            .modal, .modal-backdrop, .modal-dialog, .fade, .collapse {
                transition: none !important;
                animation: none !important;
                -webkit-animation: none !important;
            }
            
            .btn:active, .btn:focus, .form-control:active, .form-control:focus {
                animation: none !important;
                transition: none !important;
            }
            
            /* Ngăn chặn hiệu ứng ripple và các hiệu ứng animation khác */
            * {
                animation-duration: 0s !important;
                transition-duration: 0s !important;
            }
        `;
        document.head.appendChild(disableAnimationsStyle);

        // Xử lý tất cả các nút mở modal
        document.querySelectorAll('[data-bs-toggle="modal"]').forEach(button => {
            const targetModalId = button.getAttribute('data-bs-target');

            // Gỡ bỏ các thuộc tính Bootstrap mặc định
            button.removeAttribute('data-bs-toggle');
            button.removeAttribute('data-bs-target');

            // Thêm sự kiện click mới
            button.addEventListener('click', function(e) {
                // Ngăn chặn hành vi mặc định và ngăn sự kiện lan truyền
                e.preventDefault();
                e.stopPropagation();

                // Ngăn chặn xử lý nếu đang trong quá trình xử lý modal khác
                if (isProcessingModalAction) return;
                isProcessingModalAction = true;

                // Đóng tất cả modal đang mở
                document.querySelectorAll('.modal.show').forEach(openModal => {
                    const modalInstance = bootstrap.Modal.getInstance(openModal);
                    if (modalInstance) modalInstance.hide();
                });

                // Đợi một chút trước khi mở modal mới để tránh xung đột
                setTimeout(() => {
                    try {
                        const modalElement = document.querySelector(targetModalId);
                        if (modalElement) {
                            // Xóa instance cũ
                            const oldInstance = bootstrap.Modal.getInstance(modalElement);
                            if (oldInstance) {
                                oldInstance.dispose();
                            }

                            // Tạo mới và hiển thị modal
                            const modal = new bootstrap.Modal(modalElement, {
                                backdrop: 'static',
                                keyboard: false
                            });

                            // Thêm sự kiện để đặt lại trạng thái khi modal đóng
                            modalElement.addEventListener('hidden.bs.modal', function onHidden() {
                                isProcessingModalAction = false;
                                modalElement.removeEventListener('hidden.bs.modal', onHidden);
                            }, {
                                once: true
                            });

                            modal.show();
                        }
                    } catch (error) {
                        console.error("Lỗi khi mở modal:", error);
                        isProcessingModalAction = false;
                    }
                }, 100);
            });
        });

        // 3. SỬA LỖI NHẤP NHÁY CHO CÁC FORM KHÁC
        // Xử lý tất cả các form trên trang
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(e) {
                // Ngăn chặn nộp form nhanh chóng nhiều lần
                const submitButton = form.querySelector('[type="submit"]');
                if (submitButton && submitButton.disabled) {
                    e.preventDefault();
                    e.stopPropagation();
                    return;
                }

                if (submitButton) {
                    submitButton.disabled = true;

                    // Đặt lại nút sau một khoảng thời gian nếu form không được gửi thành công
                    setTimeout(() => {
                        submitButton.disabled = false;
                    }, 2000);
                }
            });
        });

        // Xử lý tất cả các nút có thể gây nhấp nháy (dropdown, collapse, v.v.)
        document.querySelectorAll('[data-bs-toggle]').forEach(element => {
            const toggleType = element.getAttribute('data-bs-toggle');
            if (toggleType === 'dropdown' || toggleType === 'collapse') {
                // Lưu trữ thông tin ban đầu
                const targetSelector = element.getAttribute('data-bs-target') ||
                    element.getAttribute('href');
                const originalToggleType = toggleType;

                // Loại bỏ thuộc tính Bootstrap
                element.removeAttribute('data-bs-toggle');
                element.removeAttribute('data-bs-target');
                if (toggleType === 'collapse') element.removeAttribute('aria-expanded');

                // Thêm sự kiện click tùy chỉnh
                element.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    if (originalToggleType === 'dropdown') {
                        const dropdown = document.querySelector(targetSelector);
                        if (dropdown) {
                            const instance = bootstrap.Dropdown.getInstance(element);
                            if (instance) {
                                instance.dispose();
                            }
                            new bootstrap.Dropdown(element).toggle();
                        }
                    } else if (originalToggleType === 'collapse') {
                        const collapse = document.querySelector(targetSelector);
                        if (collapse) {
                            const instance = bootstrap.Collapse.getInstance(collapse);
                            if (instance) {
                                instance.toggle();
                            } else {
                                new bootstrap.Collapse(collapse).toggle();
                            }
                        }
                    }
                });
            }
        });

        // 4. CHỨC NĂNG LỌC DỮ LIỆU
        function applyFilters() {
            const statusValue = statusFilter.value;
            const timeValue = timeFilter.value;
            const searchValue = searchInput.value.toLowerCase();

            rows.forEach(row => {
                if (!row.hasAttribute('data-status')) return; // Bỏ qua các dòng không phải khuyến mãi

                const status = row.dataset.status;
                const time = row.dataset.time;
                const searchText = row.dataset.search;

                const matchesStatus = statusValue === 'all' || status === statusValue;
                const matchesTime = timeValue === 'all' || time === timeValue;
                const matchesSearch = searchValue === '' || searchText.includes(searchValue);

                row.style.display = (matchesStatus && matchesTime && matchesSearch) ? '' : 'none';
            });
        }

        // Lắng nghe sự kiện thay đổi bộ lọc
        statusFilter.addEventListener('change', applyFilters);
        timeFilter.addEventListener('change', applyFilters);
        searchInput.addEventListener('input', applyFilters);

        // Đặt lại bộ lọc
        resetButton.addEventListener('click', function() {
            statusFilter.value = 'all';
            timeFilter.value = 'all';
            searchInput.value = '';
            applyFilters();
        });

        // 5. XUẤT DỮ LIỆU RA CSV
        document.getElementById('exportToCSV').addEventListener('click', function() {
            const table = document.getElementById('promotionsTable');
            const rows = table.querySelectorAll('tr');
            let csv = [];

            rows.forEach(row => {
                const rowData = [];
                const cols = row.querySelectorAll('td, th');

                cols.forEach((col, index) => {
                    // Bỏ qua cột hành động
                    if (index === cols.length - 1) return;

                    let data = col.innerText.replace(/(\r\n|\n|\r)/gm, '').trim();
                    data = data.replace(/"/g, '""'); // Đổi dấu " thành ""
                    rowData.push(`"${data}"`);
                });

                if (rowData.length) csv.push(rowData.join(','));
            });

            const csvString = csv.join('\n');
            const a = document.createElement('a');
            a.href = 'data:text/csv;charset=utf-8,' + encodeURIComponent(csvString);
            a.target = '_blank';
            a.download = 'promotions_data_' + new Date().toISOString().split('T')[0] + '.csv';
            a.click();
        });

        // Khởi tạo bộ lọc khi trang tải xong
        applyFilters();
    });
</script>


<?php include 'views/shared/footer.php'; ?>