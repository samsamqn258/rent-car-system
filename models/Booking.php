<?php
class Booking
{
    private $conn;
    private $table_name = "bookings";

    // Booking properties
    public $id;
    public $car_id;
    public $user_id;
    public $start_date;
    public $end_date;
    public $total_price;
    public $booking_status;
    public $payment_status;
    public $created_at;
    public $updated_at;

    // Additional properties for joins
    public $car_brand;
    public $car_model;
    public $car_image;
    public $customer_name;
    public $customer_phone;
    public $owner_id;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Create new booking
    public function create()
    {
        $query = "INSERT INTO " . $this->table_name . "
                SET
                    car_id = :car_id,
                    user_id = :user_id,
                    start_date = :start_date,
                    end_date = :end_date,
                    total_price = :total_price,
                    booking_status = 'pending',
                    payment_status = 'pending',
                    created_at = NOW(),
                    updated_at = NOW()";

        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->car_id = htmlspecialchars(strip_tags($this->car_id));
        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->start_date = htmlspecialchars(strip_tags($this->start_date));
        $this->end_date = htmlspecialchars(strip_tags($this->end_date));
        $this->total_price = htmlspecialchars(strip_tags($this->total_price));

        // Bind values
        $stmt->bindParam(":car_id", $this->car_id);
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":start_date", $this->start_date);
        $stmt->bindParam(":end_date", $this->end_date);
        $stmt->bindParam(":total_price", $this->total_price);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }

        return false;
    }

    // Read booking details by ID
    public function readOne()
    {
        $query = "SELECT b.*, c.brand as car_brand, c.model as car_model, c.owner_id,
                    u.fullname as customer_name, u.phone as customer_phone,
                    (SELECT image_path FROM car_images WHERE car_id = c.id AND is_primary = 1 LIMIT 1) as car_image
                FROM " . $this->table_name . " b
                JOIN cars c ON b.car_id = c.id
                JOIN users u ON b.user_id = u.id
                WHERE b.id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->id = $row['id'];
            $this->car_id = $row['car_id'];
            $this->user_id = $row['user_id'];
            $this->start_date = $row['start_date'];
            $this->end_date = $row['end_date'];
            $this->total_price = $row['total_price'];
            $this->booking_status = $row['booking_status'];
            $this->payment_status = $row['payment_status'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            $this->car_brand = $row['car_brand'];
            $this->car_model = $row['car_model'];
            $this->car_image = $row['car_image'];
            $this->customer_name = $row['customer_name'];
            $this->customer_phone = $row['customer_phone']; // Gán giá trị số điện thoại
            $this->owner_id = $row['owner_id'];

            return true;
        }

        return false;
    }

    // Read bookings by user ID
    public function readByUser()
    {
        $query = "SELECT b.*, c.brand as car_brand, c.model as car_model,
                    (SELECT image_path FROM car_images WHERE car_id = c.id AND is_primary = 1 LIMIT 1) as car_image
                FROM " . $this->table_name . " b
                JOIN cars c ON b.car_id = c.id
                WHERE b.user_id = :user_id
                ORDER BY b.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->execute();

        return $stmt;
    }

    // Read bookings by car owner ID
    public function readByOwner()
    {
        $query = "SELECT b.*, c.brand as car_brand, c.model as car_model,
                u.fullname as customer_name, u.phone as customer_phone,
                (SELECT image_path FROM car_images WHERE car_id = c.id AND is_primary = 1 LIMIT 1) as car_image
            FROM " . $this->table_name . " b
            JOIN cars c ON b.car_id = c.id
            JOIN users u ON b.user_id = u.id
            WHERE c.owner_id = :owner_id
            ORDER BY b.created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':owner_id', $this->owner_id);
        $stmt->execute();

        return $stmt;
    }

    // Update booking status
    public function updateStatus()
    {
        $query = "UPDATE " . $this->table_name . "
                SET
                    booking_status = :booking_status,
                    updated_at = NOW()
                WHERE
                    id = :id";

        $stmt = $this->conn->prepare($query);

        $this->booking_status = htmlspecialchars(strip_tags($this->booking_status));

        $stmt->bindParam(':booking_status', $this->booking_status);
        $stmt->bindParam(':id', $this->id);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Update payment status
    public function updatePaymentStatus()
    {
        $query = "UPDATE " . $this->table_name . "
                SET
                    payment_status = :payment_status,
                    updated_at = NOW()
                WHERE
                    id = :id";

        $stmt = $this->conn->prepare($query);

        $this->payment_status = htmlspecialchars(strip_tags($this->payment_status));

        $stmt->bindParam(':payment_status', $this->payment_status);
        $stmt->bindParam(':id', $this->id);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    // Check if booking can be reviewed (booking is completed and not yet reviewed)
    public function canBeReviewed()
    {
        $query = "SELECT COUNT(*) as count FROM reviews WHERE booking_id = :booking_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':booking_id', $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Can be reviewed if booking is completed and no review exists
        return $this->booking_status == 'completed' && $row['count'] == 0;
    }

    // Calculate statistics for admin/owner dashboard
    public function getStatistics($owner_id = null, $period = null)
    {
        $query = "SELECT 
                    COUNT(*) as total_bookings,
                    SUM(total_price) as total_revenue,
                    COUNT(CASE WHEN booking_status = 'completed' THEN 1 END) as completed_bookings,
                    COUNT(CASE WHEN booking_status = 'canceled' THEN 1 END) as canceled_bookings
                FROM " . $this->table_name . " b";

        // For owner, only count their cars
        if ($owner_id) {
            $query .= " JOIN cars c ON b.car_id = c.id WHERE c.owner_id = :owner_id";
        }

        // For period filtering
        if ($period) {
            if ($owner_id) {
                $query .= " AND ";
            } else {
                $query .= " WHERE ";
            }

            if ($period == 'day') {
                $query .= "DATE(b.created_at) = CURDATE()";
            } else if ($period == 'week') {
                $query .= "YEARWEEK(b.created_at, 1) = YEARWEEK(CURDATE(), 1)";
            } else if ($period == 'month') {
                $query .= "MONTH(b.created_at) = MONTH(CURDATE()) AND YEAR(b.created_at) = YEAR(CURDATE())";
            } else if ($period == 'year') {
                $query .= "YEAR(b.created_at) = YEAR(CURDATE())";
            }
        }

        $stmt = $this->conn->prepare($query);

        if ($owner_id) {
            $stmt->bindParam(':owner_id', $owner_id);
        }

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Get monthly statistics for charts
    public function getMonthlyStats($owner_id = null, $year = null)
    {
        if (!$year) {
            $year = date('Y');
        }

        $query = "SELECT 
                    MONTH(b.created_at) as month,
                    COUNT(*) as bookings,
                    SUM(total_price) as revenue
                FROM " . $this->table_name . " b";

        if ($owner_id) {
            $query .= " JOIN cars c ON b.car_id = c.id WHERE c.owner_id = :owner_id AND ";
        } else {
            $query .= " WHERE ";
        }

        $query .= "YEAR(b.created_at) = :year AND b.booking_status IN ('confirmed', 'completed')
                GROUP BY MONTH(b.created_at)
                ORDER BY MONTH(b.created_at)";

        $stmt = $this->conn->prepare($query);

        if ($owner_id) {
            $stmt->bindParam(':owner_id', $owner_id);
        }

        $stmt->bindParam(':year', $year);
        $stmt->execute();

        return $stmt;
    }
}