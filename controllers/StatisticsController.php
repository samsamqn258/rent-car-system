<?php
require_once 'models/Booking.php';
require_once 'models/Car.php';
require_once 'models/User.php';
require_once 'models/Payment.php';

class StatisticsController {
    private $db;
    private $booking;
    private $car;
    private $user;
    private $payment;

    public function __construct($db) {
        $this->db = $db;
        $this->booking = new Booking($db);
        $this->car = new Car($db);
        $this->user = new User($db);
        $this->payment = new Payment($db);
        
        // Check if user is logged in with appropriate role
        if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] != 'admin' && $_SESSION['user_role'] != 'owner')) {
            $_SESSION['error'] = "Bạn không có quyền truy cập trang này.";
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }
    }

    // Display general statistics dashboard
    public function dashboard() {
        // Determine what data to show based on user role
        if ($_SESSION['user_role'] == 'admin') {
            $this->adminDashboard();
        } else {
            $this->ownerDashboard();
        }
    }

    // Admin dashboard statistics
    private function adminDashboard() {
        // Get period and year from request
        $period = isset($_GET['period']) ? $_GET['period'] : 'month';
        $year = isset($_GET['year']) ? $_GET['year'] : date('Y');
        
        // Get statistics for the period
        $statistics = $this->getOverallStatistics($period);
        
        // Get monthly statistics for charts
        $monthly_stats = $this->getMonthlyStatistics($year);
        
        // Get top performing cars
        $top_cars = $this->getTopPerformingCars(5);
        
        // Get top customers
        $top_customers = $this->getTopCustomers(5);
        
        // Get recent bookings
        $recent_bookings = $this->getRecentBookings(5);
        
        include 'views/admin/statistics_dashboard.php';
    }

    // Owner dashboard statistics
    private function ownerDashboard() {
        // Get period and year from request
        $period = isset($_GET['period']) ? $_GET['period'] : 'month';
        $year = isset($_GET['year']) ? $_GET['year'] : date('Y');
        
        // Get statistics for the period
        $statistics = $this->getOwnerStatistics($_SESSION['user_id'], $period);
        
        // Get monthly statistics for charts
        $monthly_stats = $this->getOwnerMonthlyStatistics($_SESSION['user_id'], $year);
        
        // Get top performing cars for this owner
        $top_cars = $this->getOwnerTopCars($_SESSION['user_id'], 5);
        
        // Get recent bookings for this owner
        $recent_bookings = $this->getOwnerRecentBookings($_SESSION['user_id'], 5);
        
        include 'views/owner/statistics_dashboard.php';
    }

    // Revenue reports page
    public function revenueReports() {
        // Get period and year from request
        $period = isset($_GET['period']) ? $_GET['period'] : 'year';
        $year = isset($_GET['year']) ? $_GET['year'] : date('Y');
        $month = isset($_GET['month']) ? $_GET['month'] : date('m');
        
        if ($_SESSION['user_role'] == 'admin') {
            // Get revenue data
            $revenue_data = $this->getRevenueData($period, $year, $month);
            
            // Get revenue by car type
            $revenue_by_car_type = $this->getRevenueByCarType($period, $year);
            
            // Get revenue by month
            $revenue_by_month = $this->getRevenueByMonth($year);
            
            include 'views/admin/revenue_reports.php';
        } else {
            // Get owner revenue data
            $revenue_data = $this->getOwnerRevenueData($_SESSION['user_id'], $period, $year, $month);
            
            // Get owner revenue by car
            $revenue_by_car = $this->getOwnerRevenueByCar($_SESSION['user_id'], $period, $year);
            
            // Get owner revenue by month
            $revenue_by_month = $this->getOwnerRevenueByMonth($_SESSION['user_id'], $year);
            
            include 'views/owner/revenue_reports.php';
        }
    }

    // Booking statistics page
    public function bookingStatistics() {
        // Get period and year from request
        $period = isset($_GET['period']) ? $_GET['period'] : 'year';
        $year = isset($_GET['year']) ? $_GET['year'] : date('Y');
        
        if ($_SESSION['user_role'] == 'admin') {
            // Get booking stats
            // $booking_stats = $this->getBookingStats($period, $year);
            
            // Get booking completion rate
            // $completion_rate = $this->getBookingCompletionRate($period, $year);
            
            // Get booking by status
            // $booking_by_status = $this->getBookingByStatus($period, $year);
            
            include 'views/admin/booking_statistics.php';
        } else {
            // Get owner booking stats
            // $booking_stats = $this->getOwnerBookingStats($_SESSION['user_id'], $period, $year);
            
            // Get owner booking completion rate
            // $completion_rate = $this->getOwnerBookingCompletionRate($_SESSION['user_id'], $period, $year);
            
            // Get owner booking by status
            // $booking_by_status = $this->getOwnerBookingByStatus($_SESSION['user_id'], $period, $year);
            
            include 'views/owner/booking_statistics.php';
        }
    }

    // Car performance statistics
    public function carPerformance() {
        // Get period and year from request
        $period = isset($_GET['period']) ? $_GET['period'] : 'year';
        $year = isset($_GET['year']) ? $_GET['year'] : date('Y');
        
        if ($_SESSION['user_role'] == 'admin') {
            // Get cars with performance metrics
            // $car_performance = $this->getCarPerformanceMetrics($period, $year);
            
            include 'views/admin/car_performance.php';
        } else {
            // Get owner's cars with performance metrics
            // $car_performance = $this->getOwnerCarPerformanceMetrics($_SESSION['user_id'], $period, $year);
            
            include 'views/owner/car_performance.php';
        }
    }

    // Customer activity statistics (admin only)
    public function customerActivity() {
        // Check if user is admin
        if ($_SESSION['user_role'] != 'admin') {
            $_SESSION['error'] = "Bạn không có quyền truy cập trang này.";
            header('Location: ' . BASE_URL);
            exit;
        }
        
        // Get period and year from request
        $period = isset($_GET['period']) ? $_GET['period'] : 'year';
        $year = isset($_GET['year']) ? $_GET['year'] : date('Y');
        
        // // Get user activity stats
        // $user_activity = $this->getUserActivityStats($period, $year);
        
        // // Get new user registrations
        // $new_registrations = $this->getNewRegistrations($period, $year);
        
        // // Get user conversion rate (regular to owner)
        // $conversion_rate = $this->getUserConversionRate($period, $year);
        
        include 'views/admin/customer_activity.php';
    }

    // Export statistics to CSV
    public function exportCsv() {
        $type = isset($_GET['type']) ? $_GET['type'] : 'revenue';
        $period = isset($_GET['period']) ? $_GET['period'] : 'year';
        $year = isset($_GET['year']) ? $_GET['year'] : date('Y');
        
        // Generate CSV content based on type
        switch ($type) {
            case 'revenue':
                $this->exportRevenueCsv($period, $year);
                break;
            case 'bookings':
                $this->exportBookingsCsv($period, $year);
                break;
            case 'cars':
                $this->exportCarsCsv($period, $year);
                break;
            case 'users':
                $this->exportUsersCsv($period, $year);
                break;
            default:
                $_SESSION['error'] = "Loại báo cáo không hợp lệ.";
                header('Location: ' . BASE_URL . '/statistics/dashboard');
                exit;
        }
    }

    // Export cars data to CSV
    private function exportCarsCsv($period, $year) {
        $filename = "cars_report_" . $period . "_" . $year . ".csv";
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // Add CSV header row
        fputcsv($output, ['Car ID', 'Brand', 'Model', 'Year', 'Car Type', 'Seats', 'Total Bookings', 'Total Revenue']);
        
        $query = "SELECT 
                    c.id, c.brand, c.model, c.year, c.car_type, c.seats,
                    COUNT(b.id) as booking_count,
                    SUM(b.total_price) as total_revenue
                  FROM cars c
                  LEFT JOIN bookings b ON c.id = b.car_id
                  WHERE YEAR(b.created_at) = :year
                  GROUP BY c.id
                  ORDER BY total_revenue DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':year', $year);
        $stmt->execute();
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            fputcsv($output, $row);
        }
        
        fclose($output);
        exit;
    }

    // PRIVATE METHODS FOR DATA RETRIEVAL

    // Get overall statistics for admin
    private function getOverallStatistics($period = 'month') {
        $where_clause = $this->getWhereClauseByPeriod($period);
        
        $query = "SELECT 
                    COUNT(b.id) as total_bookings,
                    SUM(b.total_price) as total_revenue,
                    COUNT(CASE WHEN b.booking_status = 'completed' THEN 1 END) as completed_bookings,
                    COUNT(CASE WHEN b.booking_status = 'canceled' THEN 1 END) as canceled_bookings,
                    COUNT(DISTINCT b.user_id) as active_users,
                    COUNT(DISTINCT c.id) as active_cars
                FROM bookings b
                LEFT JOIN cars c ON b.car_id = c.id
                " . $where_clause;
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Add user and car counts
        $query_users = "SELECT COUNT(*) as count FROM users";
        $stmt_users = $this->db->prepare($query_users);
        $stmt_users->execute();
        $result_users = $stmt_users->fetch(PDO::FETCH_ASSOC);
        
        $query_cars = "SELECT COUNT(*) as count FROM cars";
        $stmt_cars = $this->db->prepare($query_cars);
        $stmt_cars->execute();
        $result_cars = $stmt_cars->fetch(PDO::FETCH_ASSOC);
        
        $result['total_users'] = $result_users['count'];
        $result['total_cars'] = $result_cars['count'];
        
        return $result;
    }

    // Get owner statistics
    private function getOwnerStatistics($owner_id, $period = 'month') {
        $where_clause = $this->getWhereClauseByPeriod($period);
        
        if (!empty($where_clause)) {
            $where_clause .= " AND c.owner_id = :owner_id";
        } else {
            $where_clause = " WHERE c.owner_id = :owner_id";
        }
        
        $query = "SELECT 
                    COUNT(b.id) as total_bookings,
                    SUM(b.total_price) as total_revenue,
                    COUNT(CASE WHEN b.booking_status = 'completed' THEN 1 END) as completed_bookings,
                    COUNT(CASE WHEN b.booking_status = 'canceled' THEN 1 END) as canceled_bookings,
                    COUNT(DISTINCT b.user_id) as unique_customers,
                    COUNT(DISTINCT c.id) as active_cars
                FROM bookings b
                LEFT JOIN cars c ON b.car_id = c.id
                " . $where_clause;
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':owner_id', $owner_id);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Add car count
        $query_cars = "SELECT COUNT(*) as count FROM cars WHERE owner_id = :owner_id";
        $stmt_cars = $this->db->prepare($query_cars);
        $stmt_cars->bindParam(':owner_id', $owner_id);
        $stmt_cars->execute();
        $result_cars = $stmt_cars->fetch(PDO::FETCH_ASSOC);
        
        $result['total_cars'] = $result_cars['count'];
        
        return $result;
    }

    // Get monthly statistics for charts
    private function getMonthlyStatistics($year) {
        $query = "SELECT 
                    MONTH(created_at) as month,
                    COUNT(*) as bookings,
                    SUM(total_price) as revenue
                FROM bookings
                WHERE YEAR(created_at) = :year
                GROUP BY MONTH(created_at)
                ORDER BY MONTH(created_at)";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':year', $year);
        $stmt->execute();
        
        $monthly_data = [
            'months' => [],
            'bookings' => [],
            'revenue' => []
        ];
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $month_name = date('F', mktime(0, 0, 0, $row['month'], 1));
            $monthly_data['months'][] = $month_name;
            $monthly_data['bookings'][] = $row['bookings'];
            $monthly_data['revenue'][] = $row['revenue'];
        }
        
        return $monthly_data;
    }

    // Get monthly statistics for owner
    private function getOwnerMonthlyStatistics($owner_id, $year) {
        $query = "SELECT 
                    MONTH(b.created_at) as month,
                    COUNT(*) as bookings,
                    SUM(b.total_price) as revenue
                FROM bookings b
                JOIN cars c ON b.car_id = c.id
                WHERE YEAR(b.created_at) = :year
                    AND c.owner_id = :owner_id
                GROUP BY MONTH(b.created_at)
                ORDER BY MONTH(b.created_at)";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':year', $year);
        $stmt->bindParam(':owner_id', $owner_id);
        $stmt->execute();
        
        $monthly_data = [
            'months' => [],
            'bookings' => [],
            'revenue' => []
        ];
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $month_name = date('F', mktime(0, 0, 0, $row['month'], 1));
            $monthly_data['months'][] = $month_name;
            $monthly_data['bookings'][] = $row['bookings'];
            $monthly_data['revenue'][] = $row['revenue'];
        }
        
        return $monthly_data;
    }

    // Get top performing cars
    private function getTopPerformingCars($limit = 5) {
        $query = "SELECT 
                    c.id, c.brand, c.model, c.year, c.car_type, c.seats,
                    COUNT(b.id) as booking_count,
                    SUM(b.total_price) as total_revenue,
                    (SELECT image_path FROM car_images WHERE car_id = c.id AND is_primary = 1 LIMIT 1) as car_image,
                    u.fullname as owner_name
                FROM cars c
                JOIN bookings b ON c.id = b.car_id
                JOIN users u ON c.owner_id = u.id
                WHERE b.booking_status IN ('confirmed', 'completed')
                GROUP BY c.id
                ORDER BY total_revenue DESC
                LIMIT :limit";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get top performing cars for owner
    private function getOwnerTopCars($owner_id, $limit = 5) {
        $query = "SELECT 
                    c.id, c.brand, c.model, c.year, c.car_type, c.seats,
                    COUNT(b.id) as booking_count,
                    SUM(b.total_price) as total_revenue,
                    (SELECT image_path FROM car_images WHERE car_id = c.id AND is_primary = 1 LIMIT 1) as car_image
                FROM cars c
                JOIN bookings b ON c.id = b.car_id
                WHERE c.owner_id = :owner_id
                    AND b.booking_status IN ('confirmed', 'completed')
                GROUP BY c.id
                ORDER BY total_revenue DESC
                LIMIT :limit";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':owner_id', $owner_id);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get top customers by revenue
    private function getTopCustomers($limit = 5) {
        $query = "SELECT 
                    u.id, u.fullname, u.email, u.phone,
                    COUNT(b.id) as booking_count,
                    SUM(b.total_price) as total_spent
                FROM users u
                JOIN bookings b ON u.id = b.user_id
                WHERE b.booking_status IN ('confirmed', 'completed')
                GROUP BY u.id
                ORDER BY total_spent DESC
                LIMIT :limit";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get recent bookings
    private function getRecentBookings($limit = 5) {
        $query = "SELECT 
                    b.id, b.start_date, b.end_date, b.total_price, b.booking_status, b.payment_status,
                    c.brand, c.model,
                    u.fullname as customer_name,
                    (SELECT image_path FROM car_images WHERE car_id = c.id AND is_primary = 1 LIMIT 1) as car_image
                FROM bookings b
                JOIN cars c ON b.car_id = c.id
                JOIN users u ON b.user_id = u.id
                ORDER BY b.created_at DESC
                LIMIT :limit";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get recent bookings for owner
    private function getOwnerRecentBookings($owner_id, $limit = 5) {
        $query = "SELECT 
                    b.id, b.start_date, b.end_date, b.total_price, b.booking_status, b.payment_status,
                    c.brand, c.model,
                    u.fullname as customer_name,
                    (SELECT image_path FROM car_images WHERE car_id = c.id AND is_primary = 1 LIMIT 1) as car_image
                FROM bookings b
                JOIN cars c ON b.car_id = c.id
                JOIN users u ON b.user_id = u.id
                WHERE c.owner_id = :owner_id
                ORDER BY b.created_at DESC
                LIMIT :limit";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':owner_id', $owner_id);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get revenue data
    private function getRevenueData($period = 'year', $year = null, $month = null) {
        if (!$year) {
            $year = date('Y');
        }
        
        if (!$month) {
            $month = date('m');
        }
        
        $where_clause = "";
        
        switch ($period) {
            case 'day':
                $where_clause = "WHERE DATE(created_at) = CURDATE()";
                break;
            case 'week':
                $where_clause = "WHERE YEARWEEK(created_at, 1) = YEARWEEK(CURDATE(), 1)";
                break;
            case 'month':
                $where_clause = "WHERE YEAR(created_at) = :year AND MONTH(created_at) = :month";
                break;
            case 'year':
                $where_clause = "WHERE YEAR(created_at) = :year";
                break;
            default:
                $where_clause = "WHERE YEAR(created_at) = :year";
        }
        
        $query = "SELECT 
                    SUM(total_price) as total_revenue,
                    COUNT(*) as transaction_count,
                    AVG(total_price) as avg_transaction,
                    MIN(total_price) as min_transaction,
                    MAX(total_price) as max_transaction
                FROM bookings
                " . $where_clause;
        
        $stmt = $this->db->prepare($query);
        
        if ($period == 'month' || $period == 'year') {
            $stmt->bindParam(':year', $year);
            
            if ($period == 'month') {
                $stmt->bindParam(':month', $month);
            }
        }
        
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Get revenue by car type
    private function getRevenueByCarType($period = 'year', $year = null) {
        if (!$year) {
            $year = date('Y');
        }
        
        $where_clause = $this->getWhereClauseByPeriod($period, 'b');
        
        $query = "SELECT 
                    c.car_type,
                    COUNT(b.id) as booking_count,
                    SUM(b.total_price) as revenue
                FROM bookings b
                JOIN cars c ON b.car_id = c.id
                " . $where_clause . "
                GROUP BY c.car_type
                ORDER BY revenue DESC";
        
        $stmt = $this->db->prepare($query);
        
        if ($period == 'month' || $period == 'year') {
            $stmt->bindParam(':year', $year);
            
            if ($period == 'month') {
                $month = date('m');
                $stmt->bindParam(':month', $month);
            }
        }
        
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get revenue by month
    private function getRevenueByMonth($year = null) {
        if (!$year) {
            $year = date('Y');
        }
        
        $query = "SELECT 
                    MONTH(created_at) as month,
                    SUM(total_price) as revenue,
                    COUNT(*) as booking_count
                FROM bookings
                WHERE YEAR(created_at) = :year
                GROUP BY MONTH(created_at)
                ORDER BY MONTH(created_at)";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':year', $year);
        $stmt->execute();
        
        $result = [];
        
        for ($i = 1; $i <= 12; $i++) {
            $result[$i] = [
                'month' => date('F', mktime(0, 0, 0, $i, 1)),
                'revenue' => 0,
                'booking_count' => 0
            ];
        }
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $month = $row['month'];
            $result[$month]['revenue'] = $row['revenue'];
            $result[$month]['booking_count'] = $row['booking_count'];
        }
        
        return array_values($result);
    }

    // Get owner revenue data
    private function getOwnerRevenueData($owner_id, $period = 'year', $year = null, $month = null) {
        if (!$year) {
            $year = date('Y');
        }
        
        if (!$month) {
            $month = date('m');
        }
        
        $where_clause = $this->getWhereClauseByPeriod($period, 'b');
        
        if (!empty($where_clause)) {
            $where_clause .= " AND c.owner_id = :owner_id";
        } else {
            $where_clause = " WHERE c.owner_id = :owner_id";
        }
        
        $query = "SELECT 
                    SUM(b.total_price) as total_revenue,
                    COUNT(*) as transaction_count,
                    AVG(b.total_price) as avg_transaction,
                    MIN(b.total_price) as min_transaction,
                    MAX(b.total_price) as max_transaction
                FROM bookings b
                JOIN cars c ON b.car_id = c.id
                " . $where_clause;
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':owner_id', $owner_id);
        
        if ($period == 'month' || $period == 'year') {
            $stmt->bindParam(':year', $year);
            
            if ($period == 'month') {
                $stmt->bindParam(':month', $month);
            }
        }
        
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Get owner revenue by car
    private function getOwnerRevenueByCar($owner_id, $period = 'year', $year = null) {
        if (!$year) {
            $year = date('Y');
        }
        
        $where_clause = $this->getWhereClauseByPeriod($period, 'b');
        
        if (!empty($where_clause)) {
            $where_clause .= " AND c.owner_id = :owner_id";
        } else {
            $where_clause = " WHERE c.owner_id = :owner_id";
        }
        
        $query = "SELECT 
                    c.id, c.brand, c.model, c.year,
                    COUNT(b.id) as booking_count,
                    SUM(b.total_price) as revenue,
                    (SELECT image_path FROM car_images WHERE car_id = c.id AND is_primary = 1 LIMIT 1) as car_image
                FROM bookings b
                JOIN cars c ON b.car_id = c.id
                " . $where_clause . "
                GROUP BY c.id
                ORDER BY revenue DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':owner_id', $owner_id);
        
        if ($period == 'month' || $period == 'year') {
            $stmt->bindParam(':year', $year);
            
            if ($period == 'month') {
                $month = date('m');
                $stmt->bindParam(':month', $month);
            }
        }
        
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get owner revenue by month
    private function getOwnerRevenueByMonth($owner_id, $year = null) {
        if (!$year) {
            $year = date('Y');
        }
        
        $query = "SELECT 
                    MONTH(b.created_at) as month,
                    SUM(b.total_price) as revenue,
                    COUNT(*) as booking_count
                FROM bookings b
                JOIN cars c ON b.car_id = c.id
                WHERE YEAR(b.created_at) = :year
                    AND c.owner_id = :owner_id
                GROUP BY MONTH(b.created_at)
                ORDER BY MONTH(b.created_at)";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':year', $year);
        $stmt->bindParam(':owner_id', $owner_id);
        $stmt->execute();
        
        $result = [];
        
        for ($i = 1; $i <= 12; $i++) {
            $result[$i] = [
                'month' => date('F', mktime(0, 0, 0, $i, 1)),
                'revenue' => 0,
                'booking_count' => 0
            ];
        }
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $month = $row['month'];
            $result[$month]['revenue'] = $row['revenue'];
            $result[$month]['booking_count'] = $row['booking_count'];
        }
        
        return array_values($result);
    }

    // Helper to get WHERE clause based on period
    private function getWhereClauseByPeriod($period, $table_alias = '') {
        $prefix = $table_alias ? $table_alias . '.' : '';
        
        switch ($period) {
            case 'day':
                return " WHERE " . $prefix . "created_at >= CURDATE() AND " . $prefix . "created_at < CURDATE() + INTERVAL 1 DAY";
            case 'week':
                return " WHERE YEARWEEK(" . $prefix . "created_at, 1) = YEARWEEK(CURDATE(), 1)";
            case 'month':
                return " WHERE YEAR(" . $prefix . "created_at) = :year AND MONTH(" . $prefix . "created_at) = :month";
            case 'year':
                return " WHERE YEAR(" . $prefix . "created_at) = :year";
            default:
                return "";
        }
    }

    // Export bookings data to CSV
    private function exportBookingsCsv($period, $year) {
        $filename = "bookings_report_" . $period . "_" . $year . ".csv";
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // Add CSV header row
        fputcsv($output, ['Booking ID', 'Start Date', 'End Date', 'Total Price', 'Booking Status', 'Payment Status']);
        
        $query = "SELECT id, start_date, end_date, total_price, booking_status, payment_status 
                  FROM bookings 
                  WHERE YEAR(created_at) = :year";
        
        if ($period == 'month') {
            $query .= " AND MONTH(created_at) = :month";
        }
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':year', $year);
        
        if ($period == 'month') {
            $month = date('m');
            $stmt->bindParam(':month', $month);
        }
        
        $stmt->execute();
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            fputcsv($output, $row);
        }
        
        fclose($output);
        exit;
    }

    // Export users data to CSV
    private function exportUsersCsv($period, $year) {
        $filename = "users_report_" . $period . "_" . $year . ".csv";

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        // Add CSV header row
        fputcsv($output, ['User ID', 'Full Name', 'Email', 'Phone', 'Role', 'Registration Date']);

        $query = "SELECT id, fullname, email, phone, role, created_at 
                  FROM users 
                  WHERE YEAR(created_at) = :year";

        if ($period == 'month') {
            $query .= " AND MONTH(created_at) = :month";
        }

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':year', $year);

        if ($period == 'month') {
            $month = date('m');
            $stmt->bindParam(':month', $month);
        }

        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            fputcsv($output, $row);
        }

        fclose($output);
        exit;
    }

    // Export revenue data to CSV
    private function exportRevenueCsv($period, $year) {
        $filename = "revenue_report_" . $period . "_" . $year . ".csv";
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // Add CSV header row
        fputcsv($output, ['Thời gian', 'Tổng doanh thu', 'Số giao dịch', 'Giao dịch trung bình', 'Giao dịch nhỏ nhất', 'Giao dịch lớn nhất']);
        
        if ($_SESSION['user_role'] == 'admin') {
            $revenue_data = $this->getRevenueData($period, $year);
        } else {
            $revenue_data = $this->getOwnerRevenueData($_SESSION['user_id'], $period, $year);
        }
        
        // Add revenue data
        fputcsv($output, [
            $period . ' ' . $year,
            $revenue_data['total_revenue'],
            $revenue_data['transaction_count'],
            $revenue_data['avg_transaction'],
            $revenue_data['min_transaction'],
            $revenue_data['max_transaction']
        ]);
        
        // Add monthly breakdown if year period
        if ($period == 'year') {
            fputcsv($output, []); // Empty row for spacing
            fputcsv($output, ['Tháng', 'Doanh thu', 'Số đơn đặt xe']);
            
            if ($_SESSION['user_role'] == 'admin') {
                $monthly_data = $this->getRevenueByMonth($year);
            } else {
                $monthly_data = $this->getOwnerRevenueByMonth($_SESSION['user_id'], $year);
            }
            
            foreach ($monthly_data as $data) {
                fputcsv($output, [
                    $data['month'],
                    $data['revenue'],
                    $data['booking_count']
                ]);
            }
        }
        
        fclose($output);
        exit;
    }
}