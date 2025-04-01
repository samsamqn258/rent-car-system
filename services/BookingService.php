<?php
require_once 'models/Booking.php';
require_once 'models/Payment.php';
require_once 'services/MoMoPaymentService.php';

class BookingService
{
    private $db;
    private $booking;
    private $momoPaymentService;

    public function __construct($db)
    {
        $this->db = $db;
        $this->booking = new Booking($db);
        $this->momoPaymentService = new MoMoPaymentService($db);
    }

    // Create a new booking
    public function createBooking($booking_data)
    {
        // Set booking properties
        $this->booking->car_id = $booking_data['car_id'];
        $this->booking->user_id = $booking_data['user_id'];
        $this->booking->start_date = $booking_data['start_date'];
        $this->booking->end_date = $booking_data['end_date'];
        $this->booking->total_price = $booking_data['total_price'];

        // Create booking
        if (!$this->booking->create()) {
            return false;
        }

        return $this->booking->id;
    }

    // Get booking details by ID
    public function getBookingDetails($booking_id)
    {
        $this->booking->id = $booking_id;

        if (!$this->booking->readOne()) {
            return false;
        }

        $booking_details = [
            'id' => $this->booking->id,
            'car_id' => $this->booking->car_id,
            'user_id' => $this->booking->user_id,
            'start_date' => $this->booking->start_date,
            'end_date' => $this->booking->end_date,
            'total_price' => $this->booking->total_price,
            'booking_status' => $this->booking->booking_status,
            'payment_status' => $this->booking->payment_status,
            'created_at' => $this->booking->created_at,
            'updated_at' => $this->booking->updated_at,
            'car_brand' => $this->booking->car_brand,
            'car_model' => $this->booking->car_model,
            'car_image' => $this->booking->car_image,
            'customer_name' => $this->booking->customer_name,
            'customer_phone' => $this->booking->customer_phone,
            'owner_id' => $this->booking->owner_id,
            'can_be_reviewed' => $this->booking->canBeReviewed()
        ];

        return $booking_details;
    }

    public function checkUserDriversLicense($userId) {
        // Giả sử bạn đã có một bảng `users` chứa thông tin người dùng
        $user = $this->getUserById($userId);
        
        // Kiểm tra xem người dùng có giấy phép lái xe không
        if (empty($user['license'])) {
            return false; // Người dùng chưa có giấy phép lái xe
        }
        
        return true; // Người dùng đã có giấy phép lái xe
    }

    // Phương thức lấy thông tin người dùng từ cơ sở dữ liệu (Giả sử bạn đã có phương thức này)
    private function getUserById($userId) {
        // Lấy thông tin người dùng từ cơ sở dữ liệu
        // Bạn có thể thay đổi phần này tùy theo cách kết nối DB của bạn
        $query = "SELECT * FROM users WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Get user bookings history
    public function getUserBookings($user_id)
    {
        $this->booking->user_id = $user_id;
        $bookings_stmt = $this->booking->readByUser();
        $bookings = [];

        while ($row = $bookings_stmt->fetch(PDO::FETCH_ASSOC)) {
            $row['can_be_reviewed'] = $this->booking->canBeReviewed();
            $bookings[] = $row;
        }

        return $bookings;
    }

    // Get owner's car bookings
    public function getOwnerBookings($owner_id)
    {
        $this->booking->owner_id = $owner_id;
        $bookings_stmt = $this->booking->readByOwner();
        $bookings = [];

        while ($row = $bookings_stmt->fetch(PDO::FETCH_ASSOC)) {
            $bookings[] = $row;
        }

        return $bookings;
    }

    // Update booking status
    public function updateBookingStatus($booking_id, $status)
    {
        $this->booking->id = $booking_id;
        $this->booking->booking_status = $status;

        return $this->booking->updateStatus();
    }

    // Create MoMo payment for booking
    public function createMoMoPayment($booking_id, $amount, $order_info)
    {
        $momoService = new MoMoPaymentService($this->db);
        return $momoService->createPaymentRequest($booking_id, $amount, $order_info);
    }

    // Process MoMo payment callback
    public function processPaymentCallback($response_data)
    {
        return $this->momoPaymentService->processPaymentCallback($response_data);
    }

    // Get booking statistics for owner or admin
    public function getBookingStatistics($owner_id = null, $period = null)
    {
        return $this->booking->getStatistics($owner_id, $period);
    }

    // Get monthly booking stats for charts
    public function getMonthlyBookingStats($owner_id = null, $year = null)
    {
        $stats_stmt = $this->booking->getMonthlyStats($owner_id, $year);
        $monthly_stats = [
            'months' => [],
            'bookings' => [],
            'revenue' => []
        ];

        while ($row = $stats_stmt->fetch(PDO::FETCH_ASSOC)) {
            $monthName = date('F', mktime(0, 0, 0, $row['month'], 1));
            $monthly_stats['months'][] = $monthName;
            $monthly_stats['bookings'][] = $row['bookings'];
            $monthly_stats['revenue'][] = $row['revenue'];
        }

        return $monthly_stats;
    }

    // Check if a booking belongs to an owner
    public function isOwnerOfBooking($booking_id, $owner_id)
    {
        $this->booking->id = $booking_id;

        if (!$this->booking->readOne()) {
            return false;
        }

        return $this->booking->owner_id == $owner_id;
    }
}
