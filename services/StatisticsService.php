<?php
require_once 'models/Booking.php';
require_once 'models/Car.php';
require_once 'models/User.php';
require_once 'models/Review.php';
require_once 'models/Payment.php';

class StatisticsService {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    /**
     * Get general statistics for system
     * 
     * @param string $period Time period (day, week, month, year, all)
     * @param int $owner_id Owner ID for filtered stats (null for all)
     * @return array Statistics data
     */
    public function getGeneralStatistics($period = 'all', $owner_id = null) {
        // Initialize statistics array
        $stats = [
            'total_users' => 0,
            'total_cars' => 0,
            'total_bookings' => 0,
            'completed_bookings' => 0,
            'canceled_bookings' => 0,
            'total_revenue' => 0,
            'avg_booking_value' => 0,
            'avg_rating' => 0
        ];
        
        // Where clause for period filtering
        $period_clause = $this->getPeriodWhereClause($period, 'created_at');
        
        // Get total users (only for admin)
        if (!$owner_id) {
            $query = "SELECT COUNT(*) as count FROM users WHERE role != 'admin'";
            if ($period_clause) {
                $query .= " AND " . $period_clause;
            }
            
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $stats['total_users'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        }
        
        // Get total cars
        $query = "SELECT COUNT(*) as count FROM cars";
        $where_clauses = [];
        
        if ($owner_id) {
            $where_clauses[] = "owner_id = :owner_id";
        }
        
        if ($period_clause) {
            $where_clauses[] = $period_clause;
        }
        
        if (!empty($where_clauses)) {
            $query .= " WHERE " . implode(" AND ", $where_clauses);
        }
        
        $stmt = $this->db->prepare($query);
        
        if ($owner_id) {
            $stmt->bindParam(':owner_id', $owner_id);
        }
        
        $stmt->execute();
        $stats['total_cars'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        // Get booking statistics
        $booking = new Booking($this->db);
        
        // Add owner_id to statistics query if needed
        $booking_stats = $booking->getStatistics($owner_id, $period);
        
        $stats['total_bookings'] = $booking_stats['total_bookings'] ?? 0;
        $stats['completed_bookings'] = $booking_stats['completed_bookings'] ?? 0;
        $stats['canceled_bookings'] = $booking_stats['canceled_bookings'] ?? 0;
        $stats['total_revenue'] = $booking_stats['total_revenue'] ?? 0;
        
        // Calculate average booking value
        if ($stats['total_bookings'] > 0) {
            $stats['avg_booking_value'] = $stats['total_revenue'] / $stats['total_bookings'];
        }
        
        // Get average rating
        $query = "SELECT AVG(r.rating) as avg_rating FROM reviews r";
        $join_clause = "";
        $where_clauses = [];
        
        if ($owner_id) {
            $join_clause = " JOIN cars c ON r.car_id = c.id";
            $where_clauses[] = "c.owner_id = :owner_id";
        }
        
        if ($period_clause) {
            $where_clauses[] = str_replace("created_at", "r.created_at", $period_clause);
        }
        
        if (!empty($join_clause)) {
            $query .= $join_clause;
        }
        
        if (!empty($where_clauses)) {
            $query .= " WHERE " . implode(" AND ", $where_clauses);
        }
        
        $stmt = $this->db->prepare($query);
        
        if ($owner_id) {
            $stmt->bindParam(':owner_id', $owner_id);
        }
        
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats['avg_rating'] = $row['avg_rating'] ? round($row['avg_rating'], 1) : 0;
        
        return $stats;
    }
    
    /**
     * Get monthly statistics for charts
     * 
     * @param int $year Year to get stats for
     * @param int $owner_id Owner ID for filtered stats (null for all)
     * @return array Monthly statistics
     */
    public function getMonthlyStatistics($year = null, $owner_id = null) {
        if (!$year) {
            $year = date('Y');
        }
        
        // Get monthly data
        $booking = new Booking($this->db);
        $monthly_stats = $booking->getMonthlyStats($owner_id, $year);
        
        // Initialize data arrays for all 12 months
        $months = [];
        $bookings = [];
        $revenue = [];
        
        for ($i = 1; $i <= 12; $i++) {
            $months[$i] = date('F', mktime(0, 0, 0, $i, 1));
            $bookings[$i] = 0;
            $revenue[$i] = 0;
        }
        
        // Fill with actual data
        while ($row = $monthly_stats->fetch(PDO::FETCH_ASSOC)) {
            $month = $row['month'];
            $bookings[$month] = (int)$row['bookings'];
            $revenue[$month] = (float)$row['revenue'];
        }
        
        return [
            'months' => array_values($months),
            'bookings' => array_values($bookings),
            'revenue' => array_values($revenue)
        ];
    }
    
    /**
     * Get car revenue statistics
     * 
     * @param string $period Time period (day, week, month, year, all)
     * @param int $year Year for statistics
     * @param int $owner_id Owner ID for filtered stats (null for all)
     * @return array Car revenue statistics
     */
    public function getCarRevenueStatistics($period = 'month', $year = null, $owner_id = null) {
        if (!$year) {
            $year = date('Y');
        }
        
        // Period where clause
        $period_clause = $this->getPeriodWhereClause($period, 'b.created_at', $year);
        
        // Base query
        $query = "SELECT c.id, c.brand, c.model, c.car_type, c.seats,
                    COUNT(b.id) as bookings,
                    SUM(DATEDIFF(b.end_date, b.start_date) + 1) as total_days,
                    SUM(b.total_price) as revenue,
                    (SELECT image_path FROM car_images WHERE car_id = c.id AND is_primary = 1 LIMIT 1) as car_image,
                    u.fullname as owner_name
                FROM cars c
                LEFT JOIN bookings b ON c.id = b.car_id AND b.booking_status IN ('confirmed', 'completed')
                LEFT JOIN users u ON c.owner_id = u.id";
        
        // Where clauses
        $where_clauses = [];
        
        if ($owner_id) {
            $where_clauses[] = "c.owner_id = :owner_id";
        }
        
        if ($period_clause) {
            $where_clauses[] = $period_clause;
        }
        
        if (!empty($where_clauses)) {
            $query .= " WHERE " . implode(" AND ", $where_clauses);
        }
        
        // Group by and order
        $query .= " GROUP BY c.id ORDER BY revenue DESC";
        
        $stmt = $this->db->prepare($query);
        
        if ($owner_id) {
            $stmt->bindParam(':owner_id', $owner_id);
        }
        
        $stmt->execute();
        
        $car_revenue = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Only include cars with revenue
            if ($row['revenue'] > 0) {
                $car_revenue[] = $row;
            }
        }
        
        return $car_revenue;
    }
    
    /**
     * Get user booking statistics (admin only)
     * 
     * @param string $period Time period (day, week, month, year, all)
     * @param int $limit Number of users to return
     * @return array User booking statistics
     */
    public function getUserBookingStatistics($period = 'month', $limit = 10) {
        // Period where clause
        $period_clause = $this->getPeriodWhereClause($period, 'b.created_at');
        
        // Base query
        $query = "SELECT u.id, u.username, u.fullname, u.email,
                    COUNT(b.id) as bookings,
                    SUM(b.total_price) as total_spent
                FROM users u
                LEFT JOIN bookings b ON u.id = b.user_id AND b.booking_status IN ('confirmed', 'completed')";
        
        // Where clauses
        $where_clauses = ["u.role = 'regular'"];
        
        if ($period_clause) {
            $where_clauses[] = $period_clause;
        }
        
        $query .= " WHERE " . implode(" AND ", $where_clauses);
        
        // Group by and order
        $query .= " GROUP BY u.id ORDER BY bookings DESC, total_spent DESC LIMIT :limit";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        $user_stats = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $user_stats[] = $row;
        }
        
        return $user_stats;
    }
    
    /**
     * Get owner performance statistics (admin only)
     * 
     * @param string $period Time period (day, week, month, year, all)
     * @param int $limit Number of owners to return
     * @return array Owner performance statistics
     */
    public function getOwnerPerformanceStatistics($period = 'month', $limit = 10) {
        // Period where clause
        $period_clause = $this->getPeriodWhereClause($period, 'b.created_at');
        
        // Base query
        $query = "SELECT u.id, u.username, u.fullname, u.email,
                    COUNT(DISTINCT c.id) as cars,
                    COUNT(b.id) as bookings,
                    SUM(b.total_price) as revenue,
                    AVG(r.rating) as avg_rating
                FROM users u
                LEFT JOIN cars c ON u.id = c.owner_id
                LEFT JOIN bookings b ON c.id = b.car_id AND b.booking_status IN ('confirmed', 'completed')
                LEFT JOIN reviews r ON b.id = r.booking_id";
        
        // Where clauses
        $where_clauses = ["u.role = 'owner'"];
        
        if ($period_clause) {
            $where_clauses[] = $period_clause;
        }
        
        $query .= " WHERE " . implode(" AND ", $where_clauses);
        
        // Group by and order
        $query .= " GROUP BY u.id ORDER BY revenue DESC, bookings DESC LIMIT :limit";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        $owner_stats = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $row['avg_rating'] = $row['avg_rating'] ? round($row['avg_rating'], 1) : 0;
            $owner_stats[] = $row;
        }
        
        return $owner_stats;
    }
    
    /**
     * Get booking trends statistics
     * 
     * @param int $days Number of days to analyze
     * @param int $owner_id Owner ID for filtered stats (null for all)
     * @return array Booking trends
     */
    public function getBookingTrends($days = 30, $owner_id = null) {
        // Start date
        $start_date = date('Y-m-d', strtotime("-$days days"));
        
        // Base query
        $query = "SELECT DATE(created_at) as date, COUNT(*) as count
                FROM bookings";
        
        // Where clauses
        $where_clauses = ["DATE(created_at) >= :start_date"];
        
        if ($owner_id) {
            $query .= " JOIN cars c ON bookings.car_id = c.id";
            $where_clauses[] = "c.owner_id = :owner_id";
        }
        
        $query .= " WHERE " . implode(" AND ", $where_clauses);
        
        // Group by and order
        $query .= " GROUP BY DATE(created_at) ORDER BY date ASC";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':start_date', $start_date);
        
        if ($owner_id) {
            $stmt->bindParam(':owner_id', $owner_id);
        }
        
        $stmt->execute();
        
        // Initialize data for all days in range
        $dates = [];
        $bookings = [];
        
        $current = new DateTime($start_date);
        $end = new DateTime();
        
        while ($current <= $end) {
            $date_str = $current->format('Y-m-d');
            $dates[] = $date_str;
            $bookings[$date_str] = 0;
            $current->modify('+1 day');
        }
        
        // Fill with actual data
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $bookings[$row['date']] = (int)$row['count'];
        }
        
        return [
            'dates' => $dates,
            'bookings' => array_values($bookings)
        ];
    }
    
    /**
     * Get top-rated cars
     * 
     * @param int $limit Number of cars to return
     * @param int $owner_id Owner ID for filtered stats (null for all)
     * @return array Top-rated cars
     */
    public function getTopRatedCars($limit = 5, $owner_id = null) {
        // Base query
        $query = "SELECT c.id, c.brand, c.model, c.price_per_day,
                    (SELECT image_path FROM car_images WHERE car_id = c.id AND is_primary = 1 LIMIT 1) as car_image,
                    AVG(r.rating) as avg_rating,
                    COUNT(r.id) as review_count
                FROM cars c
                LEFT JOIN reviews r ON c.id = r.car_id";
        
        // Where clauses
        $where_clauses = ["c.status = 'approved'"];
        
        if ($owner_id) {
            $where_clauses[] = "c.owner_id = :owner_id";
        }
        
        $query .= " WHERE " . implode(" AND ", $where_clauses);
        
        // Group by, having, and order
        $query .= " GROUP BY c.id HAVING review_count > 0 ORDER BY avg_rating DESC, review_count DESC LIMIT :limit";
        
        $stmt = $this->db->prepare($query);
        
        if ($owner_id) {
            $stmt->bindParam(':owner_id', $owner_id);
        }
        
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        $top_cars = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $row['avg_rating'] = round($row['avg_rating'], 1);
            $top_cars[] = $row;
        }
        
        return $top_cars;
    }
    
    /**
     * Get pending approvals count (admin only)
     * 
     * @return array Counts of pending items
     */
    public function getPendingApprovalsCount() {
        // Get pending cars count
        $query = "SELECT COUNT(*) as count FROM cars WHERE status = 'unapproved'";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $pending_cars = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        return [
            'pending_cars' => $pending_cars
        ];
    }
    
    /**
     * Get pending cars for approval (admin only)
     * 
     * @param int $limit Number of cars to return
     * @return array Pending cars
     */
    public function getPendingCars($limit = 5) {
        $query = "SELECT c.*, u.fullname as owner_name 
                FROM cars c
                JOIN users u ON c.owner_id = u.id
                WHERE c.status = 'unapproved'
                ORDER BY c.created_at DESC
                LIMIT :limit";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        $pending_cars = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $pending_cars[] = $row;
        }
        
        return $pending_cars;
    }
    
    /**
     * Get recent users (admin only)
     * 
     * @param int $limit Number of users to return
     * @return array Recent users
     */
    public function getRecentUsers($limit = 5) {
        $query = "SELECT * FROM users 
                ORDER BY created_at DESC 
                LIMIT :limit";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        $recent_users = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $recent_users[] = $row;
        }
        
        return $recent_users;
    }
    
    /**
     * Generate WHERE clause for time period filtering
     * 
     * @param string $period Time period (day, week, month, year, all)
     * @param string $field Date field to filter on
     * @param int $year Year for 'year' period
     * @return string WHERE clause or empty string
     */
    private function getPeriodWhereClause($period, $field, $year = null) {
        if ($period == 'all') {
            return '';
        }
        
        switch ($period) {
            case 'day':
                return "DATE($field) = CURDATE()";
                
            case 'week':
                return "YEARWEEK($field, 1) = YEARWEEK(CURDATE(), 1)";
                
            case 'month':
                return "MONTH($field) = MONTH(CURDATE()) AND YEAR($field) = YEAR(CURDATE())";
                
            case 'year':
                if ($year) {
                    return "YEAR($field) = $year";
                } else {
                    return "YEAR($field) = YEAR(CURDATE())";
                }
                
            default:
                return '';
        }
    }
}