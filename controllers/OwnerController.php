<?php
require_once 'models/Car.php';
require_once 'models/Booking.php';
require_once 'services/CarService.php';
require_once 'services/BookingService.php';
require_once 'services/CarOwnerContractService.php';
class OwnerController
{
    private $db;
    private $carService;
    private $bookingService;
    private $contractService;
    public function __construct($db)
    {
        $this->db = $db;
        $this->carService = new CarService($db);
        $this->bookingService = new BookingService($db);
        $this->contractService = new CarOwnerContractService($db);
        // Check if user is logged in and is an owner
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'owner') {
            $_SESSION['error'] = "Bạn không có quyền truy cập trang này.";
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }
    }

    // Manage owner's cars
    public function manageCars()
    {
        // Get owner's cars
        $cars = $this->carService->getCarsByOwner($_SESSION['user_id']);

        include 'views/owner/manage_cars.php';
    }

    // Manage owner's bookings
    public function manageBookings()
    {
        // Get owner's bookings
        $bookings = $this->bookingService->getOwnerBookings($_SESSION['user_id']);

        include 'views/owner/manage_bookings.php';
    }

    // View owner's revenue
    public function viewRevenue()
    {
        // Get period and year from request
        $period = isset($_GET['period']) ? $_GET['period'] : 'month';
        $year = isset($_GET['year']) ? $_GET['year'] : date('Y');

        // Get statistics for the period
        $statistics = $this->bookingService->getBookingStatistics($_SESSION['user_id'], $period);

        // Get monthly statistics for charts
        $monthly_stats = $this->bookingService->getMonthlyBookingStats($_SESSION['user_id'], $year);

        // Get car revenue data
        $car_revenue = $this->getCarRevenue($_SESSION['user_id'], $period, $year);

        include 'views/owner/revenue.php';
    }

    // Get car revenue data for owner
    private function getCarRevenue($owner_id, $period = 'month', $year = null)
    {
        if (!$year) {
            $year = date('Y');
        }

        $where_clause = "WHERE c.owner_id = :owner_id ";

        switch ($period) {
            case 'day':
                $where_clause .= "AND DATE(b.created_at) = CURDATE()";
                break;
            case 'week':
                $where_clause .= "AND YEARWEEK(b.created_at, 1) = YEARWEEK(CURDATE(), 1)";
                break;
            case 'month':
                $where_clause .= "AND MONTH(b.created_at) = MONTH(CURDATE()) AND YEAR(b.created_at) = YEAR(CURDATE())";
                break;
            case 'year':
                $where_clause .= "AND YEAR(b.created_at) = :year";
                break;
            default:
                $where_clause .= "AND MONTH(b.created_at) = MONTH(CURDATE()) AND YEAR(b.created_at) = YEAR(CURDATE())";
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
        $stmt->bindParam(':owner_id', $owner_id);

        if ($period == 'year') {
            $stmt->bindParam(':year', $year);
        }

        $stmt->execute();

        $car_revenue = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $car_revenue[] = $row;
        }

        return $car_revenue;
    }

    public function createContract()
    {
        $ownerId = $_SESSION['user_id'];
        $result = $this->contractService->createDefaultContract($ownerId);

        if ($result) {
            $_SESSION['success'] = "Hợp đồng đã được tạo thành công.";
            header('Location: ' . BASE_URL . '/owner/contracts');
        } else {
            $_SESSION['error'] = "Không thể tạo hợp đồng.";
            header('Location: ' . BASE_URL . '/owner/contracts');
        }


        exit;
    }

    // Lấy danh sách hợp đồng của chủ xe
    public function manageContracts()
    {
        $ownerId = $_SESSION['user_id'];
        $contracts = $this->contractService->getContractsByOwnerId($ownerId);
        include 'views/owner/owner_contracts.php';
    }
}
