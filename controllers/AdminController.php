<?php
require_once 'models/User.php';
require_once 'models/Car.php';
require_once 'models/Booking.php';
require_once 'models/Promotion.php';
require_once 'utils/Validator.php';
require_once 'services/CarOwnerContractService.php';
class AdminController
{
    private $db;
    private $validator;
    private $carOwnerContractService;
    public function __construct($db)
    {
        $this->db = $db;
        $this->validator = new Validator();
        $this->carOwnerContractService = new CarOwnerContractService($db);
        // Check if user is admin
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
            $_SESSION['error'] = "Bạn không có quyền truy cập trang này.";
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }
    }

    public function carOwnerContractService()
    {
        $contracts = $this->carOwnerContractService->getContractsWithOwnerName();
        include 'views/admin/contracts.php';
    }

    // Show admin dashboard
    public function dashboard()
    {
        // Get statistics for dashboard
        $statistics = $this->getStatistics();

        // Get pending cars for approval
        $pending_cars = $this->getPendingCars();

        // Get recent users
        $recent_users = $this->getRecentUsers();

        // Get monthly revenue
        $monthly_revenue = $this->getMonthlyRevenue();

        // Pass data to view
        $total_revenue = $statistics['total_revenue'];
        $total_cars = $statistics['total_cars'];
        $total_users = $statistics['total_users'];
        $total_bookings = $statistics['total_bookings'];

        include 'views/admin/dashboard.php';
    }

    // Manage users
    public function manageUsers()
    {
        // Get all users
        $user = new User($this->db);
        $users_stmt = $user->read();
        $users = [];

        while ($row = $users_stmt->fetch(PDO::FETCH_ASSOC)) {
            $users[] = $row;
        }

        include 'views/admin/manage_users.php';
    }

    // Block user
    public function blockUser($user_id)
    {
        $user = new User($this->db);
        $user->id = $user_id;
        $user->status = 'blocked';

        if ($user->updateStatus()) {
            $_SESSION['success'] = "Người dùng đã bị khóa thành công.";
        } else {
            $_SESSION['error'] = "Không thể khóa người dùng.";
        }

        header('Location: ' . BASE_URL . '/admin/users');
        exit;
    }

    // Unblock user
    public function unblockUser($user_id)
    {
        $user = new User($this->db);
        $user->id = $user_id;
        $user->status = 'active';

        if ($user->updateStatus()) {
            $_SESSION['success'] = "Người dùng đã được mở khóa thành công.";
        } else {
            $_SESSION['error'] = "Không thể mở khóa người dùng.";
        }

        header('Location: ' . BASE_URL . '/admin/users');
        exit;
    }

    // Manage cars
    public function manageCars()
    {
        // Get all cars
        $query = "SELECT c.*, u.fullname as owner_name, 
                    (SELECT image_path FROM car_images WHERE car_id = c.id AND is_primary = 1 LIMIT 1) as primary_image
                FROM cars c
                LEFT JOIN users u ON c.owner_id = u.id
                ORDER BY c.created_at DESC";

        $stmt = $this->db->prepare($query);
        $stmt->execute();

        $cars = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $cars[] = $row;
        }

        include 'views/admin/manage_cars.php';
    }

    // Manage promotions
    public function managePromotions()
    {
        // Get all promotions
        $promotion = new Promotion($this->db);
        $promotions_stmt = $promotion->readAll();
        $promotions = [];

        while ($row = $promotions_stmt->fetch(PDO::FETCH_ASSOC)) {
            $promotions[] = $row;
        }

        include 'views/admin/manage_promotions.php';
    }

    // Show form to add promotion
    public function showAddPromotionForm()
    {
        include 'views/admin/add_promotion.php';
    }

    // Add promotion
    public function addPromotion()
    {
        // Validate required fields
        $required_fields = ['code', 'discount_percentage', 'start_date', 'end_date'];
        $validation_errors = $this->validator->validateRequired($_POST, $required_fields);

        if (!empty($validation_errors)) {
            $_SESSION['error'] = "Vui lòng sửa các lỗi sau: " . implode(', ', $validation_errors);
            $_SESSION['form_data'] = $_POST;
            header('Location: ' . BASE_URL . '/admin/promotions/add');
            exit;
        }

        // Validate discount percentage
        if (!is_numeric($_POST['discount_percentage']) || $_POST['discount_percentage'] <= 0 || $_POST['discount_percentage'] > 100) {
            $_SESSION['error'] = "Phần trăm giảm giá phải là số dương và không quá 100%.";
            $_SESSION['form_data'] = $_POST;
            header('Location: ' . BASE_URL . '/admin/promotions/add');
            exit;
        }

        // Validate date range
        if (!$this->validator->validateDateRange($_POST['start_date'], $_POST['end_date'])) {
            $_SESSION['error'] = "Ngày kết thúc phải sau ngày bắt đầu.";
            $_SESSION['form_data'] = $_POST;
            header('Location: ' . BASE_URL . '/admin/promotions/add');
            exit;
        }

        // Create promotion
        $promotion = new Promotion($this->db);
        $promotion->code = $_POST['code'];
        $promotion->discount_percentage = $_POST['discount_percentage'];
        $promotion->start_date = $_POST['start_date'] . ' 00:00:00';
        $promotion->end_date = $_POST['end_date'] . ' 23:59:59';
        $promotion->status = 'active';

        if ($promotion->create()) {
            $_SESSION['success'] = "Thêm mã khuyến mãi thành công.";
            header('Location: ' . BASE_URL . '/admin/promotions');
        } else {
            $_SESSION['error'] = "Không thể thêm mã khuyến mãi.";
            $_SESSION['form_data'] = $_POST;
            header('Location: ' . BASE_URL . '/admin/promotions/add');
        }

        exit;
    }

    // Show form to edit promotion
    public function showEditPromotionForm($promotion_id)
    {
        // Get promotion details
        $promotion = new Promotion($this->db);
        $promotion->id = $promotion_id;

        if (!$promotion->readOne()) {
            $_SESSION['error'] = "Không tìm thấy mã khuyến mãi.";
            header('Location: ' . BASE_URL . '/admin/promotions');
            exit;
        }

        // Format dates for form
        $start_date = date('Y-m-d', strtotime($promotion->start_date));
        $end_date = date('Y-m-d', strtotime($promotion->end_date));

        include 'views/admin/edit_promotion.php';
    }

    // Update promotion
    public function updatePromotion($promotion_id)
    {
        // Validate required fields
        $required_fields = ['code', 'discount_percentage', 'start_date', 'end_date', 'status'];
        $validation_errors = $this->validator->validateRequired($_POST, $required_fields);

        if (!empty($validation_errors)) {
            $_SESSION['error'] = "Vui lòng sửa các lỗi sau: " . implode(', ', $validation_errors);
            $_SESSION['form_data'] = $_POST;
            header('Location: ' . BASE_URL . '/admin/promotions/edit/' . $promotion_id);
            exit;
        }

        // Validate discount percentage
        if (!is_numeric($_POST['discount_percentage']) || $_POST['discount_percentage'] <= 0 || $_POST['discount_percentage'] > 100) {
            $_SESSION['error'] = "Phần trăm giảm giá phải là số dương và không quá 100%.";
            $_SESSION['form_data'] = $_POST;
            header('Location: ' . BASE_URL . '/admin/promotions/edit/' . $promotion_id);
            exit;
        }

        // Validate date range
        if (!$this->validator->validateDateRange($_POST['start_date'], $_POST['end_date'])) {
            $_SESSION['error'] = "Ngày kết thúc phải sau ngày bắt đầu.";
            $_SESSION['form_data'] = $_POST;
            header('Location: ' . BASE_URL . '/admin/promotions/edit/' . $promotion_id);
            exit;
        }

        // Update promotion
        $promotion = new Promotion($this->db);
        $promotion->id = $promotion_id;
        $promotion->code = $_POST['code'];
        $promotion->discount_percentage = $_POST['discount_percentage'];
        $promotion->start_date = $_POST['start_date'] . ' 00:00:00';
        $promotion->end_date = $_POST['end_date'] . ' 23:59:59';
        $promotion->status = $_POST['status'];

        if ($promotion->update()) {
            $_SESSION['success'] = "Cập nhật mã khuyến mãi thành công.";
            header('Location: ' . BASE_URL . '/admin/promotions');
        } else {
            $_SESSION['error'] = "Không thể cập nhật mã khuyến mãi.";
            header('Location: ' . BASE_URL . '/admin/promotions/edit/' . $promotion_id);
        }

        exit;
    }

    // Delete promotion
    public function deletePromotion($promotion_id)
    {
        $promotion = new Promotion($this->db);
        $promotion->id = $promotion_id;

        if ($promotion->delete()) {
            $_SESSION['success'] = "Xóa mã khuyến mãi thành công.";
        } else {
            $_SESSION['error'] = "Không thể xóa mã khuyến mãi.";
        }

        header('Location: ' . BASE_URL . '/admin/promotions');
        exit;
    }

    // View statistics
    public function viewStatistics()
    {
        // Get period and year from request
        $period = isset($_GET['period']) ? $_GET['period'] : 'month';
        $year = isset($_GET['year']) ? $_GET['year'] : date('Y');

        // Get statistics for the period
        $booking = new Booking($this->db);
        $statistics = $booking->getStatistics(null, $period);

        // Get monthly statistics for charts
        $monthly_stats = $booking->getMonthlyStats(null, $year);
        $monthly_data = [];

        while ($row = $monthly_stats->fetch(PDO::FETCH_ASSOC)) {
            $month = $row['month'];
            $monthly_data[$month] = [
                'bookings' => $row['bookings'],
                'revenue' => $row['revenue']
            ];
        }

        // Ensure all months have data
        $monthly_revenue = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthly_revenue[$i] = isset($monthly_data[$i]) ? $monthly_data[$i]['revenue'] : 0;
        }

        // Get car revenue data
        $car_revenue = $this->getCarRevenue($period, $year);

        include 'views/admin/statistics.php';
    }

    // Helper methods

    // Get dashboard statistics
    private function getStatistics()
    {
        // Get total users
        $user_query = "SELECT COUNT(*) as total FROM users";
        $user_stmt = $this->db->prepare($user_query);
        $user_stmt->execute();
        $total_users = $user_stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Get total cars
        $car_query = "SELECT COUNT(*) as total FROM cars";
        $car_stmt = $this->db->prepare($car_query);
        $car_stmt->execute();
        $total_cars = $car_stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Get total bookings and revenue
        $booking_query = "SELECT COUNT(*) as total_bookings, SUM(total_price) as total_revenue 
                        FROM bookings 
                        WHERE booking_status IN ('confirmed', 'completed')";
        $booking_stmt = $this->db->prepare($booking_query);
        $booking_stmt->execute();
        $booking_data = $booking_stmt->fetch(PDO::FETCH_ASSOC);

        return [
            'total_users' => $total_users,
            'total_cars' => $total_cars,
            'total_bookings' => $booking_data['total_bookings'] ?: 0,
            'total_revenue' => $booking_data['total_revenue'] ?: 0
        ];
    }

    // Get pending cars for approval
    private function getPendingCars()
    {
        $query = "SELECT c.*, u.fullname as owner_name 
                FROM cars c
                JOIN users u ON c.owner_id = u.id
                WHERE c.status = 'unapproved'
                ORDER BY c.created_at DESC
                LIMIT 5";

        $stmt = $this->db->prepare($query);
        $stmt->execute();

        $pending_cars = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $pending_cars[] = $row;
        }

        return $pending_cars;
    }

    // Get recent users
    private function getRecentUsers()
    {
        $query = "SELECT * FROM users 
                ORDER BY created_at DESC 
                LIMIT 5";

        $stmt = $this->db->prepare($query);
        $stmt->execute();

        $recent_users = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $recent_users[] = $row;
        }

        return $recent_users;
    }

    // Get monthly revenue
    private function getMonthlyRevenue()
    {
        $query = "SELECT MONTH(created_at) as month, SUM(total_price) as revenue 
                FROM bookings 
                WHERE YEAR(created_at) = YEAR(CURRENT_DATE) 
                    AND booking_status IN ('confirmed', 'completed')
                GROUP BY MONTH(created_at)";

        $stmt = $this->db->prepare($query);
        $stmt->execute();

        $monthly_revenue = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthly_revenue[$i] = 0;
        }

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $monthly_revenue[$row['month']] = $row['revenue'];
        }

        return $monthly_revenue;
    }

    // Get car revenue data
    private function getCarRevenue($period = 'month', $year = null)
    {
        if (!$year) {
            $year = date('Y');
        }

        $where_clause = "";

        switch ($period) {
            case 'day':
                $where_clause = "WHERE DATE(b.created_at) = CURDATE()";
                break;
            case 'week':
                $where_clause = "WHERE YEARWEEK(b.created_at, 1) = YEARWEEK(CURDATE(), 1)";
                break;
            case 'month':
                $where_clause = "WHERE MONTH(b.created_at) = MONTH(CURDATE()) AND YEAR(b.created_at) = YEAR(CURDATE())";
                break;
            case 'year':
                $where_clause = "WHERE YEAR(b.created_at) = :year";
                break;
            default:
                $where_clause = "WHERE MONTH(b.created_at) = MONTH(CURDATE()) AND YEAR(b.created_at) = YEAR(CURDATE())";
        }

        $query = "SELECT c.id, c.brand, c.model, c.car_type, c.seats,
                    COUNT(b.id) as bookings,
                    SUM(DATEDIFF(b.end_date, b.start_date) + 1) as total_days,
                    SUM(b.total_price) as revenue,
                    (SELECT image_path FROM car_images WHERE car_id = c.id AND is_primary = 1 LIMIT 1) as car_image
                FROM cars c
                LEFT JOIN bookings b ON c.id = b.car_id AND b.booking_status IN ('confirmed', 'completed')
                $where_clause
                GROUP BY c.id
                ORDER BY revenue DESC";

        $stmt = $this->db->prepare($query);

        if ($period == 'year') {
            $stmt->bindParam(':year', $year);
        }

        $stmt->execute();

        $car_revenue = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if ($row['revenue'] > 0) { // Only include cars with revenue
                $car_revenue[] = $row;
            }
        }

        return $car_revenue;
    }
}
