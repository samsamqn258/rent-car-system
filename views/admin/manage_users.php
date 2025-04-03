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
                        <a class="nav-link active" href="<?php echo BASE_URL; ?>/admin/users">
                            <i class="fas fa-users me-2"></i> Quản lý người dùng
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>/admin/cars">
                            <i class="fas fa-car me-2"></i> Quản lý xe
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>/admin/promotions">
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
                            <i class="fas fa-chart-bar me-2"></i> Quản lý hợp đồng
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Main content -->
        <div class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div
                class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Quản lý người dùng</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="btn-group me-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="exportToCSV">
                            <i class="fas fa-download me-1"></i> Xuất CSV
                        </button>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="actionsDropdown"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-cog me-1"></i> Hành động
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="actionsDropdown">
                            <li><a class="dropdown-item" href="#" id="emailSelectedUsers">Gửi email cho người dùng đã chọn</a></li>
                            <li><a class="dropdown-item" href="#" id="blockSelectedUsers">Khóa người dùng đã chọn</a></li>
                            <li><a class="dropdown-item" href="#" id="unblockSelectedUsers">Mở khóa người dùng đã chọn</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- User Filter -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-2 mb-md-0">
                            <select class="form-select" id="roleFilter">
                                <option value="all">Tất cả vai trò</option>
                                <option value="admin">Admin</option>
                                <option value="owner">Chủ xe</option>
                                <option value="regular">Người dùng thường</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-2 mb-md-0">
                            <select class="form-select" id="statusFilter">
                                <option value="all">Tất cả trạng thái</option>
                                <option value="active">Hoạt động</option>
                                <option value="blocked">Đã khóa</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-2 mb-md-0">
                            <div class="input-group">
                                <input type="text" class="form-control" id="searchInput" placeholder="Tìm kiếm theo tên, email...">
                                <button class="btn btn-outline-secondary" type="button">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-primary w-100" id="resetFilters">
                                <i class="fas fa-sync-alt me-1"></i> Đặt lại
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3 mb-3">
                    <div class="card bg-primary text-white h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title">Tổng người dùng</h6>
                                    <h2 class="mb-0"><?php echo count($users); ?></h2>
                                </div>
                                <i class="fas fa-users fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card bg-success text-white h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title">Người dùng hoạt động</h6>
                                    <h2 class="mb-0"><?php
                                                        $active_count = 0;
                                                        foreach ($users as $user) {
                                                            if ($user['status'] == 'active') $active_count++;
                                                        }
                                                        echo $active_count;
                                                        ?></h2>
                                </div>
                                <i class="fas fa-user-check fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card bg-info text-white h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title">Chủ xe</h6>
                                    <h2 class="mb-0"><?php
                                                        $owner_count = 0;
                                                        foreach ($users as $user) {
                                                            if ($user['role'] == 'owner') $owner_count++;
                                                        }
                                                        echo $owner_count;
                                                        ?></h2>
                                </div>
                                <i class="fas fa-car fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card bg-warning text-dark h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title">Người dùng bị khóa</h6>
                                    <h2 class="mb-0"><?php
                                                        $blocked_count = 0;
                                                        foreach ($users as $user) {
                                                            if ($user['status'] == 'blocked') $blocked_count++;
                                                        }
                                                        echo $blocked_count;
                                                        ?></h2>
                                </div>
                                <i class="fas fa-user-lock fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Users Table -->
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="usersTable">
                            <thead>
                                <tr>
                                    <th>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="selectAllUsers">
                                        </div>
                                    </th>
                                    <th>ID</th>
                                    <th>Tên đăng nhập</th>
                                    <th>Tên đầy đủ</th>
                                    <th>Email</th>
                                    <th>Số điện thoại</th>
                                    <th>Vai trò</th>
                                    <th>Trạng thái</th>
                                    <th>Ngày đăng ký</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                    <tr data-role="<?php echo $user['role']; ?>" data-status="<?php echo $user['status']; ?>"
                                        data-search="<?php echo strtolower($user['username'] . ' ' . $user['fullname'] . ' ' . $user['email']); ?>">
                                        <td>
                                            <div class="form-check">
                                                <input class="form-check-input user-checkbox" type="checkbox" value="<?php echo $user['id']; ?>">
                                            </div>
                                        </td>
                                        <td><?php echo $user['id']; ?></td>
                                        <td><?php echo $user['username']; ?></td>
                                        <td><?php echo $user['fullname']; ?></td>
                                        <td><?php echo $user['email']; ?></td>
                                        <td><?php echo $user['phone']; ?></td>
                                        <td>
                                            <?php
                                            switch ($user['role']) {
                                                case 'admin':
                                                    echo '<span class="badge bg-danger">Admin</span>';
                                                    break;
                                                case 'owner':
                                                    echo '<span class="badge bg-primary">Chủ xe</span>';
                                                    break;
                                                case 'regular':
                                                    echo '<span class="badge bg-secondary">Người dùng</span>';
                                                    break;
                                                default:
                                                    echo '<span class="badge bg-secondary">Không xác định</span>';
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php if ($user['status'] == 'active'): ?>
                                                <span class="badge bg-success">Hoạt động</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Đã khóa</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo date('d/m/Y', strtotime($user['created_at'])); ?></td>
                                        <td>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal"
                                                    data-bs-target="#userDetailModal<?php echo $user['id']; ?>">
                                                    <i class="fas fa-eye"></i>
                                                </button>

                                                <?php if ($user['id'] != $_SESSION['user_id']): // Prevent admin from blocking themselves 
                                                ?>
                                                    <?php if ($user['status'] == 'active'): ?>
                                                        <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                                            data-bs-target="#blockModal<?php echo $user['id']; ?>">
                                                            <i class="fas fa-ban"></i>
                                                        </button>
                                                    <?php else: ?>
                                                        <a href="<?php echo BASE_URL; ?>/admin/unblock_user/<?php echo $user['id']; ?>"
                                                            class="btn btn-sm btn-success"
                                                            onclick="return confirm('Bạn có chắc chắn muốn mở khóa người dùng này?');">
                                                            <i class="fas fa-unlock"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                <?php endif; ?>

                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-sm btn-secondary dropdown-toggle" data-bs-toggle="dropdown"
                                                        aria-expanded="false">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        <li><a class="dropdown-item" href="mailto:<?php echo $user['email']; ?>"><i
                                                                    class="fas fa-envelope me-2"></i> Gửi email</a></li>
                                                        <li><a class="dropdown-item"
                                                                href="<?php echo BASE_URL; ?>/admin/edit_user/<?php echo $user['id']; ?>"><i
                                                                    class="fas fa-edit me-2"></i> Chỉnh sửa</a></li>
                                                        <li>
                                                            <hr class="dropdown-divider">
                                                        </li>
                                                        <li><a class="dropdown-item <?php echo ($user['role'] == 'owner') ? '' : 'disabled'; ?>"
                                                                href="<?php echo BASE_URL; ?>/admin/view_owner_cars/<?php echo $user['id']; ?>"><i
                                                                    class="fas fa-car me-2"></i> Xem xe</a></li>
                                                        <li><a class="dropdown-item"
                                                                href="<?php echo BASE_URL; ?>/admin/user_activity/<?php echo $user['id']; ?>"><i
                                                                    class="fas fa-history me-2"></i> Lịch sử hoạt động</a></li>
                                                    </ul>
                                                </div>
                                            </div>

                                            <!-- User Detail Modal -->
                                            <div class="modal fade" id="userDetailModal<?php echo $user['id']; ?>" tabindex="-1"
                                                aria-labelledby="userDetailModalLabel<?php echo $user['id']; ?>" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="userDetailModalLabel<?php echo $user['id']; ?>">Thông tin người
                                                                dùng</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="text-center mb-3">
                                                                <i class="fas fa-user-circle fa-5x text-primary"></i>
                                                                <h4 class="mt-2"><?php echo $user['fullname']; ?></h4>
                                                                <div>
                                                                    <?php
                                                                    switch ($user['role']) {
                                                                        case 'admin':
                                                                            echo '<span class="badge bg-danger">Admin</span>';
                                                                            break;
                                                                        case 'owner':
                                                                            echo '<span class="badge bg-primary">Chủ xe</span>';
                                                                            break;
                                                                        case 'regular':
                                                                            echo '<span class="badge bg-secondary">Người dùng</span>';
                                                                            break;
                                                                    }
                                                                    ?>

                                                                    <?php if ($user['status'] == 'active'): ?>
                                                                        <span class="badge bg-success">Hoạt động</span>
                                                                    <?php else: ?>
                                                                        <span class="badge bg-danger">Đã khóa</span>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>

                                                            <div class="row">
                                                                <div class="col-md-6 mb-3">
                                                                    <label class="form-label fw-bold">ID:</label>
                                                                    <p><?php echo $user['id']; ?></p>
                                                                </div>
                                                                <div class="col-md-6 mb-3">
                                                                    <label class="form-label fw-bold">Tên đăng nhập:</label>
                                                                    <p><?php echo $user['username']; ?></p>
                                                                </div>
                                                                <div class="col-md-6 mb-3">
                                                                    <label class="form-label fw-bold">Email:</label>
                                                                    <p><?php echo $user['email']; ?></p>
                                                                </div>
                                                                <div class="col-md-6 mb-3">
                                                                    <label class="form-label fw-bold">Số điện thoại:</label>
                                                                    <p><?php echo $user['phone']; ?></p>
                                                                </div>
                                                                <div class="col-md-12 mb-3">
                                                                    <label class="form-label fw-bold">Địa chỉ:</label>
                                                                    <p><?php echo $user['address']; ?></p>
                                                                </div>
                                                                <div class="col-md-6 mb-3">
                                                                    <label class="form-label fw-bold">Ngày đăng ký:</label>
                                                                    <p><?php echo date('d/m/Y H:i', strtotime($user['created_at'])); ?></p>
                                                                </div>
                                                                <div class="col-md-6 mb-3">
                                                                    <label class="form-label fw-bold">Cập nhật lần cuối:</label>
                                                                    <p><?php echo date('d/m/Y H:i', strtotime($user['updated_at'])); ?></p>
                                                                </div>
                                                            </div>

                                                            <?php if ($user['role'] == 'owner'): ?>
                                                                <div class="border-top pt-3 mt-3">
                                                                    <h6>Thông tin chủ xe</h6>
                                                                    <div class="row">
                                                                        <div class="col-md-6 mb-3">
                                                                            <label class="form-label fw-bold">Số lượng xe:</label>
                                                                            <p>
                                                                                <?php
                                                                                // In a real app, you would query the database for this information
                                                                                echo '<span class="badge bg-primary">5 xe</span>';
                                                                                ?>
                                                                            </p>
                                                                        </div>
                                                                        <div class="col-md-6 mb-3">
                                                                            <label class="form-label fw-bold">Đánh giá trung bình:</label>
                                                                            <p>
                                                                                <i class="fas fa-star text-warning"></i>
                                                                                <i class="fas fa-star text-warning"></i>
                                                                                <i class="fas fa-star text-warning"></i>
                                                                                <i class="fas fa-star text-warning"></i>
                                                                                <i class="fas fa-star-half-alt text-warning"></i>
                                                                                (4.5/5)
                                                                            </p>
                                                                        </div>
                                                                    </div>
                                                                    <div>
                                                                        <a href="<?php echo BASE_URL; ?>/admin/view_owner_cars/<?php echo $user['id']; ?>"
                                                                            class="btn btn-sm btn-outline-primary">
                                                                            <i class="fas fa-car me-1"></i> Xem danh sách xe
                                                                        </a>
                                                                        <a href="<?php echo BASE_URL; ?>/admin/owner_revenue/<?php echo $user['id']; ?>"
                                                                            class="btn btn-sm btn-outline-success">
                                                                            <i class="fas fa-chart-line me-1"></i> Xem doanh thu
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                                            <a href="<?php echo BASE_URL; ?>/admin/edit_user/<?php echo $user['id']; ?>"
                                                                class="btn btn-primary">Chỉnh sửa</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Block User Modal -->
                                            <?php if ($user['id'] != $_SESSION['user_id'] && $user['status'] == 'active'): ?>
                                                <div class="modal fade" id="blockModal<?php echo $user['id']; ?>" tabindex="-1"
                                                    aria-labelledby="blockModalLabel<?php echo $user['id']; ?>" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="blockModalLabel<?php echo $user['id']; ?>">Khóa người dùng</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p>Bạn có chắc chắn muốn khóa người dùng <strong><?php echo $user['fullname']; ?></strong>?
                                                                </p>
                                                                <p class="text-danger">Khi khóa, người dùng sẽ không thể đăng nhập vào hệ thống.</p>

                                                                <div class="mb-3">
                                                                    <label for="blockReason<?php echo $user['id']; ?>" class="form-label">Lý do khóa:</label>
                                                                    <textarea class="form-control" id="blockReason<?php echo $user['id']; ?>" rows="3"
                                                                        placeholder="Nhập lý do khóa người dùng..."></textarea>
                                                                </div>

                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox" id="notifyUser<?php echo $user['id']; ?>"
                                                                        checked>
                                                                    <label class="form-check-label" for="notifyUser<?php echo $user['id']; ?>">
                                                                        Gửi email thông báo đến người dùng
                                                                    </label>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                                                <a href="<?php echo BASE_URL; ?>/admin/block_user/<?php echo $user['id']; ?>"
                                                                    class="btn btn-danger">Khóa người dùng</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center mb-0">
                            <li class="page-item disabled">
                                <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Trước</a>
                            </li>
                            <li class="page-item active"><a class="page-link" href="#">1</a></li>
                            <li class="page-item"><a class="page-link" href="#">2</a></li>
                            <li class="page-item"><a class="page-link" href="#">3</a></li>
                            <li class="page-item">
                                <a class="page-link" href="#">Sau</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Batch Email Modal -->
<div class="modal fade" id="batchEmailModal" tabindex="-1" aria-labelledby="batchEmailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="batchEmailModalLabel">Gửi email cho người dùng đã chọn</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="selectedUsersInfo" class="mb-3">
                    <p>Bạn đã chọn <span id="selectedUserCount">0</span> người dùng:</p>
                    <div id="selectedUsersList" class="mb-3"></div>
                </div>

                <div class="mb-3">
                    <label for="emailSubject" class="form-label">Tiêu đề:</label>
                    <input type="text" class="form-control" id="emailSubject" placeholder="Nhập tiêu đề email">
                </div>

                <div class="mb-3">
                    <label for="emailContent" class="form-label">Nội dung:</label>
                    <textarea class="form-control" id="emailContent" rows="10" placeholder="Nhập nội dung email..."></textarea>
                </div>

                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="includeTemplate" checked>
                    <label class="form-check-label" for="includeTemplate">
                        Sử dụng mẫu email
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-primary" id="sendBatchEmail">Gửi email</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Filtering functionality
        const roleFilter = document.getElementById('roleFilter');
        const statusFilter = document.getElementById('statusFilter');
        const searchInput = document.getElementById('searchInput');
        const resetButton = document.getElementById('resetFilters');
        const rows = document.querySelectorAll('#usersTable tbody tr');

        function applyFilters() {
            const roleValue = roleFilter.value;
            const statusValue = statusFilter.value;
            const searchValue = searchInput.value.toLowerCase();

            rows.forEach(row => {
                const role = row.dataset.role;
                const status = row.dataset.status;
                const searchText = row.dataset.search;

                const matchesRole = roleValue === 'all' || role === roleValue;
                const matchesStatus = statusValue === 'all' || status === statusValue;
                const matchesSearch = searchValue === '' || searchText.includes(searchValue);

                if (matchesRole && matchesStatus && matchesSearch) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });

            updateSelectedUserCount();
        }

        roleFilter.addEventListener('change', applyFilters);
        statusFilter.addEventListener('change', applyFilters);
        searchInput.addEventListener('input', applyFilters);

        resetButton.addEventListener('click', function() {
            roleFilter.value = 'all';
            statusFilter.value = 'all';
            searchInput.value = '';
            applyFilters();
        });

        // Export to CSV functionality
        document.getElementById('exportToCSV').addEventListener('click', function() {
            const table = document.getElementById('usersTable');
            const rows = table.querySelectorAll('tr');

            let csv = [];
            for (let i = 0; i < rows.length; i++) {
                const row = [],
                    cols = rows[i].querySelectorAll('td, th');

                for (let j = 0; j < cols.length; j++) {
                    // Skip the checkbox and action columns
                    if (j === 0 || j === cols.length - 1) continue;

                    // Get text content, clean it up
                    let data = cols[j].innerText.replace(/(\r\n|\n|\r)/gm, '').trim();

                    // Escape double quotes and wrap with quotes
                    data = data.replace(/"/g, '""');
                    row.push('"' + data + '"');
                }

                csv.push(row.join(','));
            }

            const csvString = csv.join('\n');
            const a = document.createElement('a');
            a.href = 'data:text/csv;charset=utf-8,' + encodeURIComponent(csvString);
            a.target = '_blank';
            a.download = 'users_data.csv';
            a.click();
        });

        // Select all users functionality
        const selectAllCheckbox = document.getElementById('selectAllUsers');
        const userCheckboxes = document.querySelectorAll('.user-checkbox');

        selectAllCheckbox.addEventListener('change', function() {
            const isChecked = this.checked;

            userCheckboxes.forEach(checkbox => {
                const row = checkbox.closest('tr');
                if (row.style.display !== 'none') { // Only select visible rows
                    checkbox.checked = isChecked;
                }
            });

            updateSelectedUserCount();
        });

        userCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateSelectedUserCount);
        });

        // Batch actions
        document.getElementById('emailSelectedUsers').addEventListener('click', function() {
            const selectedUsers = getSelectedUsers();
            if (selectedUsers.length === 0) {
                alert('Vui lòng chọn ít nhất một người dùng.');
                return;
            }

            // Populate selected users list
            const selectedUsersList = document.getElementById('selectedUsersList');
            selectedUsersList.innerHTML = '';

            selectedUsers.forEach(userId => {
                const row = document.querySelector(`.user-checkbox[value="${userId}"]`).closest('tr');
                const username = row.cells[2].textContent;
                const fullname = row.cells[3].textContent;
                const email = row.cells[4].textContent;

                const userItem = document.createElement('div');
                userItem.className = 'badge bg-light text-dark p-2 me-2 mb-2';
                userItem.innerHTML = `${fullname} <small>(${email})</small>`;
                selectedUsersList.appendChild(userItem);
            });

            // Show modal
            const batchEmailModal = new bootstrap.Modal(document.getElementById('batchEmailModal'));
            batchEmailModal.show();
        });

        document.getElementById('blockSelectedUsers').addEventListener('click', function() {
            const selectedUsers = getSelectedUsers();
            if (selectedUsers.length === 0) {
                alert('Vui lòng chọn ít nhất một người dùng.');
                return;
            }

            if (confirm(`Bạn có chắc chắn muốn khóa ${selectedUsers.length} người dùng đã chọn?`)) {
                // In a real app, you would send a request to the server
                alert('Chức năng này sẽ được triển khai sau.');
            }
        });

        document.getElementById('unblockSelectedUsers').addEventListener('click', function() {
            const selectedUsers = getSelectedUsers();
            if (selectedUsers.length === 0) {
                alert('Vui lòng chọn ít nhất một người dùng.');
                return;
            }

            if (confirm(`Bạn có chắc chắn muốn mở khóa ${selectedUsers.length} người dùng đã chọn?`)) {
                // In a real app, you would send a request to the server
                alert('Chức năng này sẽ được triển khai sau.');
            }
        });

        document.getElementById('sendBatchEmail').addEventListener('click', function() {
            const subject = document.getElementById('emailSubject').value;
            const content = document.getElementById('emailContent').value;

            if (!subject || !content) {
                alert('Vui lòng nhập đầy đủ tiêu đề và nội dung email.');
                return;
            }

            // In a real app, you would send a request to the server
            alert('Email của bạn đã được gửi đi. Chức năng này sẽ được triển khai đầy đủ sau.');

            const batchEmailModal = bootstrap.Modal.getInstance(document.getElementById('batchEmailModal'));
            batchEmailModal.hide();
        });

        // Helper functions
        function getSelectedUsers() {
            const selectedUsers = [];
            userCheckboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    selectedUsers.push(checkbox.value);
                }
            });
            return selectedUsers;
        }

        function updateSelectedUserCount() {
            const selectedUsers = getSelectedUsers();
            const selectedUserCount = document.getElementById('selectedUserCount');
            if (selectedUserCount) {
                selectedUserCount.textContent = selectedUsers.length;
            }
        }
    });
</script>

<?php include 'views/shared/footer.php'; ?>